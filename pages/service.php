<?php
require_once __DIR__ . '/../includes/config.php';
$slug = $_GET['slug'] ?? '';
$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM service_pages WHERE slug=? AND is_active=1");
$stmt->execute([$slug]);
$page = $stmt->fetch();
if (!$page) { http_response_code(404); require __DIR__ . '/404.php'; exit(); }

$cities   = getActiveCities(24);
$prodCats = getMainCategories();
$page_title    = $page['meta_title'];
$meta_desc     = $page['meta_desc'];
$canonical_url = BASE_URL . '/' . $page['slug'];
$breadcrumbs   = [['label'=>'Beranda','url'=>'/'],['label'=>$page['title']]];
require_once __DIR__ . '/../includes/header.php';
?>

<style>
/* ── FONTS ───────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap');

/* ── VARIABLES ───────────────────────────────── */
:root {
  --rose:    #fdf0f3;
  --rose-mid:#f9d0da;
  --rose-6:  #e11d48;
  --cream:   #fdf6ee;
  --sage:    #f0f4ed;
  --gold:    #c9a84c;
  --gold-lt: #f5e9c8;
  --burg:    #1c0c12;
  --burg2:   #3d1520;
  --ink:     #2a1018;
}

/* ── HERO ────────────────────────────────────── */
.svc-hero {
  position: relative;
  min-height: 420px;
  display: flex;
  align-items: center;
  overflow: hidden;
  background: linear-gradient(135deg, #fff8f8 0%, #fdf6ee 40%, #f0f4ed 100%);
}

/* Decorative garden blobs */
.svc-hero::before {
  content:'';
  position:absolute;
  top:-80px; right:-80px;
  width:420px; height:420px;
  background: radial-gradient(circle, rgba(253,208,218,0.55) 0%, transparent 70%);
  border-radius:50%;
  pointer-events:none;
}
.svc-hero::after {
  content:'';
  position:absolute;
  bottom:-60px; left:-60px;
  width:320px; height:320px;
  background: radial-gradient(circle, rgba(197,210,180,0.45) 0%, transparent 70%);
  border-radius:50%;
  pointer-events:none;
}

/* Trellis pattern */
.svc-hero-trellis {
  position:absolute;
  inset:0;
  background-image:
    linear-gradient(rgba(201,168,76,0.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,168,76,0.07) 1px, transparent 1px);
  background-size: 40px 40px;
  pointer-events:none;
}

/* Gold sage blob top-left */
.svc-hero-blob1 {
  position:absolute;
  top:20px; left:20px;
  width:200px; height:200px;
  background: radial-gradient(circle, rgba(201,168,76,0.12) 0%, transparent 70%);
  border-radius:50%;
  pointer-events:none;
}

.svc-hero-inner {
  position:relative;
  z-index:2;
  width:100%;
  max-width:1200px;
  margin:0 auto;
  padding: 4rem 2rem;
  display:grid;
  grid-template-columns: 1fr auto;
  gap: 2rem;
  align-items:center;
}
@media(max-width:768px){
  .svc-hero-inner { grid-template-columns:1fr; }
  .svc-hero-floralblock { display:none; }
}

/* Breadcrumb */
.svc-breadcrumb {
  display:flex;
  align-items:center;
  gap:0.5rem;
  margin-bottom:1rem;
  font-family:'Jost',sans-serif;
  font-size:0.78rem;
  color:#9ca3af;
}
.svc-breadcrumb a { color:#9ca3af; text-decoration:none; transition:color .2s; }
.svc-breadcrumb a:hover { color: var(--gold); }
.svc-breadcrumb span { color: var(--gold); }

/* Eyebrow */
.svc-eyebrow {
  display:inline-flex;
  align-items:center;
  gap:0.5rem;
  font-family:'Jost',sans-serif;
  font-size:0.72rem;
  font-weight:600;
  letter-spacing:0.14em;
  text-transform:uppercase;
  color: var(--gold);
  background: var(--gold-lt);
  border: 1px solid rgba(201,168,76,0.3);
  padding:0.3rem 0.8rem;
  border-radius:999px;
  margin-bottom:1.1rem;
}
.svc-eyebrow::before {
  content:'';
  width:6px; height:6px;
  background: var(--gold);
  border-radius:50%;
  display:inline-block;
}

.svc-hero h1 {
  font-family:'Cormorant Garamond',serif;
  font-size: clamp(2rem, 4vw, 3.2rem);
  font-weight:600;
  color: var(--ink);
  line-height:1.15;
  margin-bottom:1rem;
}
.svc-hero h1 em {
  font-style:italic;
  color: var(--rose-6);
}

.svc-hero-desc {
  font-family:'Jost',sans-serif;
  font-size:1rem;
  color:#6b7280;
  line-height:1.7;
  max-width:520px;
  margin-bottom:1.8rem;
}

/* CTA buttons */
.svc-cta-row {
  display:flex;
  gap:0.8rem;
  flex-wrap:wrap;
}
.svc-btn-primary {
  display:inline-flex;
  align-items:center;
  gap:0.5rem;
  background:#16a34a;
  color:white;
  font-family:'Jost',sans-serif;
  font-weight:600;
  font-size:0.9rem;
  padding:0.75rem 1.6rem;
  border-radius:999px;
  text-decoration:none;
  transition: transform .2s, box-shadow .2s;
}
.svc-btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(22,163,74,0.3); }
.svc-btn-secondary {
  display:inline-flex;
  align-items:center;
  gap:0.5rem;
  background:white;
  color: var(--ink);
  border:1.5px solid var(--rose-mid);
  font-family:'Jost',sans-serif;
  font-weight:500;
  font-size:0.9rem;
  padding:0.75rem 1.4rem;
  border-radius:999px;
  text-decoration:none;
  transition: border-color .2s, background .2s;
}
.svc-btn-secondary:hover { border-color: var(--rose-6); background: var(--rose); }

/* Trust pills */
.svc-trust-row {
  display:flex;
  gap:0.6rem;
  flex-wrap:wrap;
  margin-top:1.5rem;
}
.svc-trust-pill {
  display:inline-flex;
  align-items:center;
  gap:0.4rem;
  font-family:'Jost',sans-serif;
  font-size:0.75rem;
  font-weight:500;
  color:#4b5563;
  background:white;
  border:1px solid rgba(201,168,76,0.25);
  padding:0.3rem 0.75rem;
  border-radius:999px;
  box-shadow:0 1px 4px rgba(0,0,0,0.05);
}
.svc-trust-pill svg { color: var(--gold); }

/* Floral decoration block */
.svc-hero-floralblock {
  position:relative;
  width:220px;
  height:280px;
  flex-shrink:0;
}
.svc-hero-floralblock svg {
  position:absolute;
}

/* Wave divider */
.svc-hero-wave {
  position:absolute;
  bottom:0; left:0; right:0;
  line-height:0;
  z-index:1;
}
.svc-hero-wave svg { display:block; width:100%; }

/* ── TRUST CARDS ─────────────────────────────── */
.svc-trust-section {
  background: var(--cream);
  padding: 3.5rem 1.5rem;
  position:relative;
  overflow:hidden;
}
.svc-trust-section::before {
  content:'';
  position:absolute;
  inset:0;
  background-image: radial-gradient(circle, rgba(201,168,76,0.06) 1px, transparent 1px);
  background-size: 24px 24px;
  pointer-events:none;
}
.svc-trust-grid {
  max-width:1100px;
  margin:0 auto;
  display:grid;
  grid-template-columns: repeat(2,1fr);
  gap:1rem;
}
@media(min-width:640px){ .svc-trust-grid { grid-template-columns: repeat(4,1fr); } }

.svc-trust-card {
  background:white;
  border:1px solid rgba(201,168,76,0.2);
  border-radius:16px;
  padding:1.4rem 1rem;
  text-align:center;
  transition: transform .25s, box-shadow .25s, border-color .25s;
}
.svc-trust-card:hover {
  transform:translateY(-4px);
  box-shadow:0 12px 32px rgba(201,168,76,0.15);
  border-color: var(--gold);
}
.svc-trust-icon {
  width:52px; height:52px;
  background: linear-gradient(135deg, var(--rose) 0%, var(--gold-lt) 100%);
  border-radius:14px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 0.9rem;
}
.svc-trust-icon svg { color: var(--gold); }
.svc-trust-card h3 {
  font-family:'Jost',sans-serif;
  font-weight:600;
  font-size:0.88rem;
  color: var(--ink);
  margin-bottom:0.3rem;
}
.svc-trust-card p {
  font-family:'Jost',sans-serif;
  font-size:0.75rem;
  color:#9ca3af;
  line-height:1.5;
}

/* ── MAIN LAYOUT ─────────────────────────────── */
.svc-main {
  max-width:1200px;
  margin:0 auto;
 padding: 2rem 1rem;      
  display:grid;
 grid-template-columns: 1fr;
  gap:2.5rem;
  align-items:start;
}
@media(max-width:900px){
  .svc-main { grid-template-columns:1fr; }
  .svc-sidebar { 
     order: 2;
    position: static;          /* ← hapus sticky di mobile */
    top: auto;
   }
}

/* ── ARTICLE CONTENT ─────────────────────────── */
/* .svc-article {} */

/* Section label */
.svc-section-label {
  display:flex;
  align-items:center;
  gap:0.75rem;
  font-family:'Jost',sans-serif;
  font-size:0.72rem;
  font-weight:700;
  letter-spacing:0.15em;
  text-transform:uppercase;
  color: var(--gold);
  margin-bottom:1.5rem;
}
.svc-section-label::after {
  content:'';
  flex:1;
  height:1px;
  background: linear-gradient(90deg, rgba(201,168,76,0.4), transparent);
}

/* Article typography — editorial */
.svc-prose {
  font-family:'Jost',sans-serif;
}
.svc-prose h2 {
  font-family:'Cormorant Garamond',serif;
  font-size:1.75rem;
  font-weight:600;
  color: var(--ink);
  margin: 2.2rem 0 0.8rem;
  padding-bottom:0.5rem;
  border-bottom:1px solid rgba(201,168,76,0.2);
  position:relative;
}
.svc-prose h2::before {
  content:'';
  position:absolute;
  bottom:-1px; left:0;
  width:48px; height:2px;
  background: var(--gold);
  border-radius:999px;
}
.svc-prose h3 {
  font-family:'Cormorant Garamond',serif;
  font-size:1.25rem;
  font-weight:600;
  font-style:italic;
  color:#374151;
  margin: 1.5rem 0 0.5rem;
}
.svc-prose h3 a {
  color:inherit;
  text-decoration:none;
  transition: color .2s;
}
.svc-prose h3 a:hover { color: var(--rose-6); }
.svc-prose p {
  font-size:0.95rem;
  color:#4b5563;
  line-height:1.85;
  margin-bottom:1rem;
}
.svc-prose ul {
  list-style:none;
  padding:0;
  margin:1rem 0 1.2rem;
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap:0.5rem;
}
.svc-prose ul li {
  display:flex;
  align-items:center;
  gap:0.5rem;
  font-size:0.88rem;
  color:#374151;
  background:white;
  border:1px solid rgba(201,168,76,0.2);
  border-radius:10px;
  padding:0.5rem 0.75rem;
}
.svc-prose ul li::before {
  content:'✦';
  color: var(--gold);
  font-size:0.65rem;
  flex-shrink:0;
}
.svc-prose a {
  color: var(--rose-6);
  text-decoration:none;
  border-bottom:1px solid rgba(225,29,72,0.25);
  transition: border-color .2s;
}
.svc-prose a:hover { border-color: var(--rose-6); }

/* ── HOW TO ORDER — STEPS ────────────────────── */
.svc-steps {
  margin: 2.5rem 0;
}
.svc-steps-title {
  font-family:'Cormorant Garamond',serif;
  font-size:1.5rem;
  font-weight:600;
  color: var(--ink);
  margin-bottom:1.5rem;
}
.svc-steps-grid {
  display:grid;
  grid-template-columns: repeat(2,1fr);
  gap:1rem;
}
@media(min-width:640px){ .svc-steps-grid { grid-template-columns: repeat(4,1fr); } }

.svc-step-card {
  background:white;
  border:1px solid rgba(201,168,76,0.18);
  border-radius:16px;
  padding:1.4rem 1rem;
  text-align:center;
  position:relative;
  overflow:hidden;
  transition: transform .25s, box-shadow .25s;
}
.svc-step-card:hover {
  transform:translateY(-3px);
  box-shadow:0 8px 24px rgba(225,29,72,0.12);
}
.svc-step-num {
  position:absolute;
  top:0.5rem; right:0.8rem;
  font-family:'Cormorant Garamond',serif;
  font-size:3rem;
  font-weight:600;
  color:rgba(201,168,76,0.12);
  line-height:1;
  user-select:none;
}
.svc-step-icon {
  width:48px; height:48px;
  background: linear-gradient(135deg, var(--rose) 0%, var(--gold-lt) 100%);
  border-radius:14px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 0.8rem;
}
.svc-step-icon svg { color: var(--rose-6); }
.svc-step-card h4 {
  font-family:'Jost',sans-serif;
  font-weight:600;
  font-size:0.85rem;
  color: var(--ink);
  margin-bottom:0.3rem;
}
.svc-step-card p {
  font-family:'Jost',sans-serif;
  font-size:0.75rem;
  color:#9ca3af;
  line-height:1.5;
  margin:0;
}

/* connector arrow */
.svc-steps-grid .svc-step-card:not(:last-child)::after {
  display:none;
}

/* ── CATEGORY GRID ───────────────────────────── */
.svc-cats {
  margin: 2.5rem 0;
}
.svc-cats-grid {
  display:grid;
  grid-template-columns: repeat(2,1fr);
  gap:0.75rem;
}
@media(min-width:480px){ .svc-cats-grid { grid-template-columns: repeat(3,1fr); } }

.svc-cat-card {
  background:white;
  border:1px solid rgba(201,168,76,0.2);
  border-radius:14px;
  padding:1rem 0.9rem;
  display:flex;
  align-items:center;
  gap:0.75rem;
  text-decoration:none;
  transition: transform .2s, box-shadow .2s, border-color .2s, background .2s;
  /* group: true; */
}
.svc-cat-card:hover {
  transform:translateX(3px);
  box-shadow:0 4px 16px rgba(201,168,76,0.15);
  border-color: var(--gold);
  background: var(--gold-lt);
}
.svc-cat-dot {
  width:8px; height:8px;
  background: var(--gold);
  border-radius:50%;
  flex-shrink:0;
  transition: transform .2s;
}
.svc-cat-card:hover .svc-cat-dot { transform:scale(1.5); }
.svc-cat-name {
  font-family:'Jost',sans-serif;
  font-weight:500;
  font-size:0.85rem;
  color:#374151;
  line-height:1.3;
  flex:1;
}
.svc-cat-card:hover .svc-cat-name { color: var(--ink); }
.svc-cat-arrow {
  color:#d1d5db;
  transition: color .2s, transform .2s;
  flex-shrink:0;
}
.svc-cat-card:hover .svc-cat-arrow { color: var(--gold); transform:translateX(3px); }

/* ── SIDEBAR ─────────────────────────────────── */
.svc-sidebar {
  position:sticky;
  top:90px;
}

/* WA card */
.svc-wa-card {
  background: linear-gradient(135deg, #1c1c1c 0%, var(--burg2) 100%);
  border-radius:20px;
    padding: 1.4rem 1.2rem;
  text-align:center;
  margin-bottom:1.5rem;
  position:relative;
  overflow:hidden;
}
.svc-wa-card::before {
  content:'';
  position:absolute;
  top:-40px; right:-40px;
  width:140px; height:140px;
  background: radial-gradient(circle, rgba(201,168,76,0.2) 0%, transparent 70%);
  border-radius:50%;
}
.svc-wa-card-icon {
  width:56px; height:56px;
  background: rgba(255,255,255,0.1);
  border-radius:14px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0 auto 1rem;
  border:1px solid rgba(255,255,255,0.15);
}
.svc-wa-card h3 {
  font-family:'Cormorant Garamond',serif;
  font-size:1.3rem;
  font-weight:600;
  color:white;
  margin-bottom:0.4rem;
}
.svc-wa-card p {
  font-family:'Jost',sans-serif;
  font-size:0.8rem;
  color:rgba(255,255,255,0.6);
  margin-bottom:1.3rem;
  line-height:1.6;
}
.svc-wa-btn {
  display:block;
  background:#25D366;
  color:white;
  font-family:'Jost',sans-serif;
  font-weight:600;
  font-size:0.9rem;
  padding:0.85rem 1.5rem;
  border-radius:12px;
  text-decoration:none;
  transition: transform .2s, box-shadow .2s;
  position:relative;
  z-index:1;
}
.svc-wa-btn:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(37,211,102,0.35); }

/* City chips card */
.svc-city-card {
  max-height: 200px;
   overflow-y: auto;
  background:white;
  border:1px solid rgba(201,168,76,0.2);
  border-radius:20px;
  padding:1.4rem;
  margin-bottom:1.2rem;
}
.svc-city-card-title {
  font-family:'Jost',sans-serif;
  font-size:0.72rem;
  font-weight:700;
  letter-spacing:0.12em;
  text-transform:uppercase;
  color: var(--gold);
  margin-bottom:1rem;
  display:flex;
  align-items:center;
  gap:0.5rem;
}
.svc-city-card-title::before {
  content:'';
  width:16px; height:2px;
  background: var(--gold);
  border-radius:999px;
}
.svc-city-chips {
  display:flex;
  flex-wrap:wrap;
  gap:0.4rem;
}
.svc-city-chip {
  display:inline-flex;
  align-items:center;
  gap:0.3rem;
  font-family:'Jost',sans-serif;
  font-size:0.75rem;
  color:#4b5563;
  background: var(--rose);
  border:1px solid rgba(225,29,72,0.12);
  padding:0.3rem 0.65rem;
  border-radius:999px;
  text-decoration:none;
  transition: background .2s, border-color .2s, color .2s;
}
.svc-city-chip:hover {
  background: var(--rose-mid);
  border-color: rgba(225,29,72,0.3);
  color: var(--rose-6);
}
.svc-city-chip svg { flex-shrink:0; opacity:0.5; }

/* Quote pull */
.svc-pullquote {
  border-left:3px solid var(--gold);
  background: linear-gradient(90deg, var(--gold-lt) 0%, transparent 100%);
  padding:1.2rem 1.5rem;
  border-radius:0 12px 12px 0;
  margin:1.8rem 0;
}
.svc-pullquote p {
  font-family:'Cormorant Garamond',serif;
  font-size:1.15rem;
  font-style:italic;
  color: var(--ink);
  line-height:1.7;
  margin:0;
}

/* ══════════════════════════════════════
   CTA FINAL — BRIGHT GARDEN FLORAL
   ══════════════════════════════════════ */

.svc-cta-final {
  position: relative;
  padding: 6rem 1.5rem 5.5rem;
  overflow: hidden;
  text-align: center;
  background: #faf5f0;
  isolation: isolate;
}

/* Layer 1 — foto panjang.jpg */
.svc-cta-final-bgimg {
  position: absolute;
  inset: 0;
  background-image: url('<?= BASE_URL ?>/assets/images/panjang.jpg');
  background-size: cover;
  background-position: center 30%;
  background-attachment: fixed;  /* ← ini yang bikin "keseret" saat scroll */
  opacity: 0.50;                 /* ← naikkan sesuai selera, 0.25–0.35 bagus */
  z-index: 1;
}
@media (max-width: 768px) {
  .svc-cta-final-bgimg {
    background-attachment: scroll; /* fallback mobile */
  }
}
/* Layer 2 — gradient warm cream overlay */
.svc-cta-final-overlay {
  position: absolute;
  inset: 0;
  z-index: 2;
  background:
    radial-gradient(ellipse 80% 60% at 50% 0%,   rgba(255,235,230,0.72) 0%, transparent 70%),
    radial-gradient(ellipse 60% 50% at 20% 100%,  rgba(220,240,210,0.55) 0%, transparent 65%),
    radial-gradient(ellipse 60% 50% at 80% 100%,  rgba(255,220,230,0.45) 0%, transparent 65%),
   linear-gradient(180deg, rgba(253,249,244,0.45) 0%, rgba(252,245,240,0.55) 50%, rgba(250,245,240,0.65) 100%);
}

/* Layer 3 — orbs lembut */
.svc-cta-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(70px);
  pointer-events: none;
  z-index: 3;
}

/* Wave atas — transisi dari konten ke CTA */
.svc-cta-final-wave-top {
  position: absolute;
  top: 0; left: 0; right: 0;
  line-height: 0;
  z-index: 4;
}
.svc-cta-final-wave-top svg { display: block; width: 100%; }

/* Konten utama */
.svc-cta-inner {
  position: relative;
  z-index: 10;
  max-width: 640px;
  margin: 0 auto;
}

/* Eyebrow badge */
.svc-cta-final .svc-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #8a5a4a;
  background: rgba(255,255,255,0.80);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(192,72,90,0.22);
  padding: 0.35rem 1rem;
  border-radius: 999px;
  margin-bottom: 1.4rem;
  box-shadow: 0 2px 12px rgba(192,72,90,0.08);
}
.svc-cta-final .svc-eyebrow svg { color: #c0485a; }

/* Judul */
.svc-cta-inner h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.2rem, 4.5vw, 3.2rem);
  font-weight: 600;
  color: #2e1a16;
  line-height: 1.15;
  margin-bottom: 1rem;
  letter-spacing: -0.01em;
}
.svc-cta-inner h2 em {
  font-style: italic;
  color: #c0485a;
  position: relative;
}
/* Underline garis bawah judul em */
.svc-cta-inner h2 em::after {
  content: '';
  position: absolute;
  bottom: -2px; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #c0485a, #e89090, transparent);
  border-radius: 999px;
  opacity: 0.6;
}

/* Paragraf */
.svc-cta-inner p {
  font-family: 'Jost', sans-serif;
  font-size: 1rem;
  color: rgba(46,26,22,0.62);
  line-height: 1.75;
  max-width: 520px;
  margin: 0 auto 2.2rem;
}

/* Baris tombol */
.svc-cta-btns {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

/* Tombol WA */
.svc-cta-wa {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  color: white;
  font-family: 'Jost', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.9rem 2rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow:
    0 6px 20px rgba(22,163,74,0.30),
    0 2px 6px rgba(22,163,74,0.20);
  transition: transform .22s, box-shadow .22s;
  white-space: nowrap;
}
.svc-cta-wa:hover {
  transform: translateY(-3px);
  box-shadow:
    0 12px 32px rgba(22,163,74,0.38),
    0 4px 12px rgba(22,163,74,0.20);
  color: white;
}
.svc-cta-wa svg { flex-shrink: 0; }

/* Tombol Explore / Katalog */
.svc-cta-explore {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  background: rgba(255,255,255,0.75);
  backdrop-filter: blur(12px);
  color: #3a2420;
  border: 1.5px solid rgba(192,72,90,0.28);
  font-family: 'Jost', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.9rem 1.8rem;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(192,72,90,0.08);
  transition: background .22s, border-color .22s, color .22s, transform .22s, box-shadow .22s;
  white-space: nowrap;
}
.svc-cta-explore:hover {
  background: rgba(255,255,255,0.95);
  border-color: rgba(192,72,90,0.55);
  color: #c0485a;
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(192,72,90,0.14);
}
.svc-cta-explore svg { flex-shrink: 0; color: currentColor; }

/* Trust mini row bawah tombol */
.svc-cta-trust {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
  margin-top: 2rem;
}
.svc-cta-trust-item {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: 'Jost', sans-serif;
  font-size: 0.75rem;
  font-weight: 500;
  color: rgba(46,26,22,0.50);
}
.svc-cta-trust-item svg { color: #c9a84c; flex-shrink: 0; }

/* Dekorasi SVG bunga melayang */
.svc-cta-flower {
  position: absolute;
  pointer-events: none;
  z-index: 5;
  opacity: 0;
  animation: svc-cta-floatin 1s ease forwards;
}
@keyframes svc-cta-floatin {
  to { opacity: 1; }
}
@keyframes svc-cta-sway {
  0%,100% { transform: translateY(0) rotate(0deg); }
  33%      { transform: translateY(-8px) rotate(4deg); }
  66%      { transform: translateY(5px) rotate(-3deg); }
}
@keyframes svc-cta-sway2 {
  0%,100% { transform: translateY(0) rotate(0deg) scaleX(1); }
  50%      { transform: translateY(-12px) rotate(-5deg) scaleX(1.05); }
}

/* Wave bawah */
.svc-cta-final-wave-bot {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  line-height: 0;
  z-index: 4;
}
.svc-cta-final-wave-bot svg { display: block; width: 100%; }

/* Floating floral SVGs */
@keyframes svc-float { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-10px) rotate(3deg)} }
.svc-float { animation: svc-float 5s ease-in-out infinite; }
.svc-float2 { animation: svc-float 7s ease-in-out infinite reverse; }
.svc-float3 { animation: svc-float 6s ease-in-out infinite 1s; }

/* Animate on scroll */
.svc-reveal {
  opacity:0;
  transform:translateY(24px);
  transition: opacity .6s ease, transform .6s ease;
}
.svc-reveal.svc-visible {
  opacity:1;
  transform:translateY(0);
}
</style>

<!-- ═══════════ HERO ═══════════ -->
<section class="svc-hero">
  <div class="svc-hero-trellis"></div>
  <div class="svc-hero-blob1"></div>

  <!-- SVG bunga pojok kanan atas -->
  <svg style="position:absolute;top:12px;right:200px;opacity:.22;pointer-events:none" width="90" height="90" viewBox="0 0 90 90" fill="none">
    <ellipse cx="45" cy="20" rx="10" ry="18" fill="#f9a8d4" class="svc-float"/>
    <ellipse cx="65" cy="35" rx="10" ry="18" fill="#fda4af" transform="rotate(72 65 35)" class="svc-float2"/>
    <ellipse cx="60" cy="60" rx="10" ry="18" fill="#f9a8d4" transform="rotate(144 60 60)" class="svc-float"/>
    <ellipse cx="30" cy="65" rx="10" ry="18" fill="#fecdd3" transform="rotate(216 30 65)" class="svc-float3"/>
    <ellipse cx="22" cy="38" rx="10" ry="18" fill="#fda4af" transform="rotate(288 22 38)" class="svc-float2"/>
    <circle cx="45" cy="45" r="10" fill="#c9a84c"/>
  </svg>

  <!-- SVG daun pojok kiri bawah -->
  <svg style="position:absolute;bottom:50px;left:40px;opacity:.18;pointer-events:none" width="80" height="100" viewBox="0 0 80 100" fill="none">
    <path d="M40 95 Q10 60 20 20 Q40 0 60 20 Q70 60 40 95Z" fill="#86a870" class="svc-float2"/>
    <path d="M40 95 Q40 50 40 20" stroke="#5a7a45" stroke-width="1.5" fill="none"/>
  </svg>

  <div class="svc-hero-inner">
    <div>
      <!-- Breadcrumb -->
      <nav class="svc-breadcrumb">
        <a href="<?= BASE_URL ?>">Beranda</a>
        <span>›</span>
        <span><?= clean($page['title']) ?></span>
      </nav>

      <div class="svc-eyebrow">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 1 0 9 9"/><path d="m15 7 3-3-3-3"/></svg>
        Chika Florist · Layanan
      </div>

      <h1><?= clean($page['h1_text'] ?: $page['title']) ?></h1>

      <p class="svc-hero-desc">
        Rangkaian bunga segar untuk setiap momen spesial — dipesan kapan saja, dikirim tepat waktu ke seluruh Indonesia.
      </p>

      <div class="svc-cta-row">
        <a href="<?= waLink() ?>" target="_blank" class="svc-btn-primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M5.339 15.626C4.094 13.636 3.5 11.272 3.5 9.005 3.5 4.58 7.08 1 11.5 1s8 3.58 8 8.005c0 4.424-3.58 8.004-8 8.004a7.925 7.925 0 0 1-3.945-1.043L2 17.5l1.339-1.874z" fill-rule="evenodd" clip-rule="evenodd"/></svg>
          Pesan via WhatsApp
        </a>
        <a href="<?= BASE_URL ?>/produk" class="svc-btn-secondary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          Lihat Katalog
        </a>
      </div>

      <!-- Trust pills -->
      <div class="svc-trust-row">
        <span class="svc-trust-pill">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
          Buka 24 Jam
        </span>
        <span class="svc-trust-pill">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          Same Day Delivery
        </span>
        <span class="svc-trust-pill">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Bunga Segar
        </span>
        <span class="svc-trust-pill">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          Florist Profesional
        </span>
      </div>
    </div>

    <!-- Floral illustration block -->
    <div class="svc-hero-floralblock">
      <svg style="top:0;left:20px" width="180" height="240" viewBox="0 0 180 240" fill="none" class="svc-float">
        <!-- Stem -->
        <path d="M90 220 Q85 160 88 120 Q90 80 92 40" stroke="#7aaa5e" stroke-width="2.5" fill="none"/>
        <!-- Leaves -->
        <path d="M88 140 Q60 125 50 105 Q70 115 88 120Z" fill="#86a870" opacity=".8"/>
        <path d="M90 160 Q118 145 128 125 Q108 135 90 140Z" fill="#7aaa5e" opacity=".8"/>
        <!-- Big flower -->
        <ellipse cx="90" cy="38" rx="13" ry="22" fill="#fda4af"/>
        <ellipse cx="90" cy="38" rx="13" ry="22" fill="#fda4af" transform="rotate(45 90 38)"/>
        <ellipse cx="90" cy="38" rx="13" ry="22" fill="#f9a8d4" transform="rotate(90 90 38)"/>
        <ellipse cx="90" cy="38" rx="13" ry="22" fill="#fecdd3" transform="rotate(135 90 38)"/>
        <circle cx="90" cy="38" r="13" fill="#c9a84c"/>
        <circle cx="90" cy="38" r="7" fill="#f5e9c8"/>
      </svg>

      <svg style="top:60px;right:0" width="90" height="120" viewBox="0 0 90 120" fill="none" class="svc-float2">
        <path d="M45 115 Q42 80 44 50 Q45 30 46 10" stroke="#7aaa5e" stroke-width="2" fill="none"/>
        <path d="M44 70 Q22 60 16 45 Q30 52 44 55Z" fill="#86a870" opacity=".7"/>
        <ellipse cx="45" cy="10" rx="8" ry="14" fill="#fecdd3"/>
        <ellipse cx="45" cy="10" rx="8" ry="14" fill="#fecdd3" transform="rotate(60 45 10)"/>
        <ellipse cx="45" cy="10" rx="8" ry="14" fill="#fda4af" transform="rotate(120 45 10)"/>
        <circle cx="45" cy="10" r="8" fill="#c9a84c"/>
      </svg>

      <!-- Small rose buds -->
      <svg style="bottom:20px;left:0" width="70" height="80" viewBox="0 0 70 80" fill="none" class="svc-float3">
        <path d="M35 75 Q33 50 34 30 Q35 15 36 5" stroke="#7aaa5e" stroke-width="1.8" fill="none"/>
        <ellipse cx="35" cy="5" rx="7" ry="12" fill="#fda4af"/>
        <ellipse cx="35" cy="5" rx="7" ry="12" fill="#fecdd3" transform="rotate(72 35 5)"/>
        <ellipse cx="35" cy="5" rx="7" ry="12" fill="#fda4af" transform="rotate(144 35 5)"/>
        <circle cx="35" cy="5" r="7" fill="#c9a84c"/>
      </svg>
    </div>
  </div>

  <!-- Wave divider -->
  <div class="svc-hero-wave">
    <svg viewBox="0 0 1440 60" preserveAspectRatio="none" height="60">
      <path d="M0,30 C360,70 1080,0 1440,30 L1440,60 L0,60 Z" fill="#fdf6ee"/>
    </svg>
  </div>
</section>

<!-- ═══════════ TRUST CARDS ═══════════ -->
<section class="svc-trust-section">
  <div class="svc-trust-grid svc-reveal">
    <div class="svc-trust-card">
      <div class="svc-trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
      </div>
      <h3>Layanan 24 Jam</h3>
      <p>Admin siap merespon kapan saja, siang maupun malam</p>
    </div>
    <div class="svc-trust-card">
      <div class="svc-trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
      <h3>Same Day Delivery</h3>
      <p>Bunga tiba di hari yang sama, tepat waktu sesuai kesepakatan</p>
    </div>
    <div class="svc-trust-card">
      <div class="svc-trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <h3>Bunga 100% Segar</h3>
      <p>Dipilih langsung dari sumber terpercaya setiap hari</p>
    </div>
    <div class="svc-trust-card">
      <div class="svc-trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      </div>
      <h3>Florist Profesional</h3>
      <p>Tim berpengalaman dengan desain elegan dan presisi</p>
    </div>
  </div>
</section>

<!-- ═══════════ MAIN CONTENT ═══════════ -->
<div class="svc-main">

  <!-- ARTICLE -->
  <article class="svc-article">

    <?php if ($page['content']): ?>
    <!-- Dynamic content dari DB -->
    <div class="svc-section-label">Tentang Layanan</div>
    <div class="svc-prose svc-reveal">
      <?= $page['content'] ?>
    </div>

    <?php else: ?>
    <!-- Default editorial content -->
    <div class="svc-section-label svc-reveal">Tentang Layanan</div>

    <div class="svc-prose svc-reveal">
      <p>Chika Florist hadir sebagai toko bunga online 24 jam yang melayani pemesanan dan pengiriman berbagai jenis rangkaian bunga ke seluruh wilayah Indonesia. Dengan pengalaman panjang di industri florist, kami memahami bahwa setiap momen penting layak mendapatkan bunga terbaik.</p>

      <div class="svc-pullquote">
        <p>"Tidak semua momen spesial bisa direncanakan jauh hari — itulah kenapa kami selalu siap, 24 jam tanpa henti."</p>
      </div>

      <h2>Layanan Florist Online 24 Jam Nonstop</h2>
      <p>Tidak semua momen penting dapat direncanakan sebelumnya. Oleh karena itu, Chika Florist menyediakan layanan toko bunga online 24 jam yang memungkinkan Anda melakukan pemesanan kapan saja — tengah malam, dini hari, atau pagi buta sekalipun.</p>
      <ul>
        <li>Pemesanan bunga 24 jam nonstop</li>
        <li>Admin responsif melalui WhatsApp</li>
        <li>Proses cepat &amp; praktis</li>
        <li>Pengiriman same day delivery</li>
        <li>Jangkauan pengiriman seluruh Indonesia</li>
      </ul>

      <h2>Jenis Bunga yang Bisa Dipesan</h2>
      <?php foreach ($prodCats as $cat): ?>
      <h3><a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>"><?= clean($cat['name']) ?></a></h3>
      <p><?= clean($cat['description'] ?? 'Koleksi unggulan Chika Florist yang dapat dikustomisasi sesuai kebutuhan Anda.') ?></p>
      <?php endforeach; ?>

      <h2>Kenapa Memilih Chika Florist?</h2>
      <ul>
        <li>Layanan florist online 24 jam nonstop</li>
        <li>Pengiriman cepat &amp; tepat waktu</li>
        <li>Bunga fresh dan berkualitas premium</li>
        <li>Harga kompetitif dan transparan</li>
        <li>Tim florist profesional berpengalaman</li>
        <li>Jangkauan seluruh Indonesia</li>
      </ul>
    </div>
    <?php endif; ?>

    <!-- ── CARA PESAN ── -->
    <div class="svc-steps svc-reveal">
      <div class="svc-section-label">Cara Memesan</div>
      <div class="svc-steps-grid">
        <div class="svc-step-card">
          <span class="svc-step-num">1</span>
          <div class="svc-step-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="m9 12 2 2 4-4"/></svg>
          </div>
          <h4>Pilih Produk</h4>
          <p>Jelajahi katalog dan temukan bunga yang sesuai momen</p>
        </div>
        <div class="svc-step-card">
          <span class="svc-step-num">2</span>
          <div class="svc-step-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <h4>Chat WhatsApp</h4>
          <p>Hubungi admin dan sampaikan detail pesanan Anda</p>
        </div>
        <div class="svc-step-card">
          <span class="svc-step-num">3</span>
          <div class="svc-step-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h4>Konfirmasi & Bayar</h4>
          <p>Setujui desain dan nominal, pesanan langsung diproses</p>
        </div>
        <div class="svc-step-card">
          <span class="svc-step-num">4</span>
          <div class="svc-step-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </div>
          <h4>Terima Bunga</h4>
          <p>Bunga tiba segar dan indah tepat di waktu yang disepakati</p>
        </div>
      </div>
    </div>

    <!-- ── KATEGORI PRODUK ── -->
    <?php if (!empty($prodCats)): ?>
    <div class="svc-cats svc-reveal">
      <div class="svc-section-label">Koleksi Bunga</div>
      <div class="svc-cats-grid">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="svc-cat-card">
          <span class="svc-cat-dot"></span>
          <span class="svc-cat-name"><?= clean($cat['name']) ?></span>
          <svg class="svc-cat-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </article>

  <!-- SIDEBAR -->
  <aside class="svc-sidebar">
    <!-- WA dark card -->
    <div class="svc-wa-card svc-reveal">
      <div class="svc-wa-card-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M5.339 15.626C4.094 13.636 3.5 11.272 3.5 9.005 3.5 4.58 7.08 1 11.5 1s8 3.58 8 8.005c0 4.424-3.58 8.004-8 8.004a7.925 7.925 0 0 1-3.945-1.043L2 17.5l1.339-1.874z" fill-rule="evenodd" clip-rule="evenodd"/></svg>
      </div>
      <h3>Siap Membantu Anda</h3>
      <p>Admin kami online 24 jam, siap merespon pertanyaan dan membantu memilih bunga terbaik untuk momen Anda.</p>
      <a href="<?= waLink() ?>" target="_blank" class="svc-wa-btn">
        💬 Chat Sekarang
      </a>
    </div>

    <!-- Catalog link card -->
    <div class="svc-city-card svc-reveal" style="margin-bottom:1.2rem;">
      <div class="svc-city-card-title">Katalog Produk</div>
      <div class="svc-city-chips">
        <?php foreach ($prodCats as $cat): ?>
        <a href="<?= BASE_URL ?>/<?= $cat['slug'] ?>" class="svc-city-chip">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"/></svg>
          <?= clean($cat['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- City chips -->
    <?php if (!empty($cities)): ?>
    <div class="svc-city-card svc-reveal">
      <div class="svc-city-card-title">Tersedia di Kota</div>
      <div class="svc-city-chips">
        <?php foreach ($cities as $city): ?>
        <a href="<?= BASE_URL ?>/toko-bunga-<?= $city['slug'] ?>" class="svc-city-chip">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <?= clean($page['title']) ?> <?= clean($city['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </aside>

</div>

<!-- ═══════════ FINAL CTA ═══════════ -->
<section class="svc-cta-final">

  <!-- Layer foto + overlay -->
  <div class="svc-cta-final-bgimg"></div>
  <div class="svc-cta-final-overlay"></div>

  <!-- Orbs warm -->
  <div class="svc-cta-orb" style="width:500px;height:500px;background:rgba(255,180,180,0.16);top:-160px;left:50%;transform:translateX(-50%);"></div>
  <div class="svc-cta-orb" style="width:300px;height:300px;background:rgba(150,210,150,0.13);bottom:-80px;right:5%;"></div>
  <div class="svc-cta-orb" style="width:240px;height:240px;background:rgba(255,200,160,0.14);bottom:-60px;left:4%;"></div>
  <div class="svc-cta-orb" style="width:180px;height:180px;background:rgba(255,190,210,0.20);top:10%;left:8%;"></div>
  <div class="svc-cta-orb" style="width:160px;height:160px;background:rgba(180,220,170,0.18);top:15%;right:7%;"></div>

  <!-- Wave atas (transisi dari putih) -->
  <div class="svc-cta-final-wave-top">
    <svg viewBox="0 0 1440 50" preserveAspectRatio="none" height="50">
      <path d="M0,0 C480,50 960,0 1440,40 L1440,0 L0,0 Z" fill="white" opacity="0.6"/>
      <path d="M0,20 C360,55 1080,5 1440,35 L1440,0 L0,0 Z" fill="white" opacity="0.4"/>
    </svg>
  </div>

  <!-- Dekorasi bunga kiri bawah -->
  <svg class="svc-cta-flower" style="bottom:20px;left:3%;width:160px;animation-delay:.3s;animation: svc-cta-floatin .8s .3s ease forwards, svc-cta-sway 7s 1.1s ease-in-out infinite;" viewBox="0 0 160 200" fill="none">
    <path d="M80 195 Q77 140 79 100 Q80 60 81 20" stroke="#8aaa6a" stroke-width="2.5" fill="none"/>
    <path d="M79 120 Q50 108 40 88 Q58 98 79 102Z" fill="#9aba7a" opacity=".75"/>
    <path d="M80 145 Q108 133 118 113 Q100 123 80 128Z" fill="#7aaa5e" opacity=".75"/>
    <!-- Bunga besar -->
    <ellipse cx="80" cy="20" rx="12" ry="20" fill="#fda4af"/>
    <ellipse cx="80" cy="20" rx="12" ry="20" fill="#fba8b8" transform="rotate(45 80 20)"/>
    <ellipse cx="80" cy="20" rx="12" ry="20" fill="#f9d0da" transform="rotate(90 80 20)"/>
    <ellipse cx="80" cy="20" rx="12" ry="20" fill="#fecdd3" transform="rotate(135 80 20)"/>
    <circle cx="80" cy="20" r="12" fill="#c9a84c"/>
    <circle cx="80" cy="20" r="6" fill="#f5e9c8"/>
  </svg>

  <!-- Dekorasi bunga kanan atas -->
  <svg class="svc-cta-flower" style="top:18px;right:4%;width:120px;animation-delay:.5s;animation: svc-cta-floatin .8s .5s ease forwards, svc-cta-sway2 9s 1.3s ease-in-out infinite;" viewBox="0 0 120 160" fill="none">
    <path d="M60 155 Q58 110 59 75 Q60 45 61 15" stroke="#8aaa6a" stroke-width="2" fill="none"/>
    <path d="M59 90 Q38 80 30 64 Q46 72 59 75Z" fill="#9aba7a" opacity=".70"/>
    <ellipse cx="60" cy="15" rx="9" ry="16" fill="#fecdd3"/>
    <ellipse cx="60" cy="15" rx="9" ry="16" fill="#fda4af" transform="rotate(60 60 15)"/>
    <ellipse cx="60" cy="15" rx="9" ry="16" fill="#f9a8d4" transform="rotate(120 60 15)"/>
    <circle cx="60" cy="15" r="9" fill="#c9a84c"/>
    <circle cx="60" cy="15" r="5" fill="#f5e9c8"/>
  </svg>

  <!-- Kuntum kecil kiri atas -->
  <svg class="svc-cta-flower" style="top:25px;left:12%;width:80px;opacity:0;animation: svc-cta-floatin .8s .7s ease forwards, svc-cta-sway 8s 1.5s ease-in-out infinite;" viewBox="0 0 80 110" fill="none">
    <path d="M40 105 Q38 72 39 48 Q40 28 41 8" stroke="#8aaa6a" stroke-width="1.8" fill="none"/>
    <ellipse cx="40" cy="8" rx="7" ry="13" fill="#fda4af"/>
    <ellipse cx="40" cy="8" rx="7" ry="13" fill="#fecdd3" transform="rotate(72 40 8)"/>
    <ellipse cx="40" cy="8" rx="7" ry="13" fill="#f9a8d4" transform="rotate(144 40 8)"/>
    <circle cx="40" cy="8" r="7" fill="#c9a84c"/>
  </svg>

  <!-- Kuntum kecil kanan bawah -->
  <svg class="svc-cta-flower" style="bottom:30px;right:12%;width:70px;opacity:0;animation: svc-cta-floatin .8s .6s ease forwards, svc-cta-sway2 6s 1.4s ease-in-out infinite;" viewBox="0 0 70 100" fill="none">
    <path d="M35 95 Q33 65 34 40 Q35 22 36 5" stroke="#8aaa6a" stroke-width="1.8" fill="none"/>
    <path d="M34 55 Q18 47 13 34 Q25 41 34 43Z" fill="#9aba7a" opacity=".65"/>
    <ellipse cx="35" cy="5" rx="7" ry="13" fill="#fecdd3"/>
    <ellipse cx="35" cy="5" rx="7" ry="13" fill="#fda4af" transform="rotate(60 35 5)"/>
    <ellipse cx="35" cy="5" rx="7" ry="13" fill="#f9d0da" transform="rotate(120 35 5)"/>
    <circle cx="35" cy="5" r="7" fill="#c9a84c"/>
  </svg>

  <!-- KONTEN UTAMA -->
  <div class="svc-cta-inner svc-reveal">

    <!-- Eyebrow -->
    <div class="svc-eyebrow">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      Pesan Sekarang
    </div>

    <!-- Ornamen floral SVG di atas judul -->
    <svg width="80" height="28" viewBox="0 0 80 28" fill="none" style="margin:0 auto 0.8rem;display:block;opacity:.55">
      <circle cx="40" cy="14" r="4" fill="#c9a84c"/>
      <circle cx="20" cy="14" r="2.5" fill="#c0485a" opacity=".6"/>
      <circle cx="60" cy="14" r="2.5" fill="#c0485a" opacity=".6"/>
      <line x1="0" y1="14" x2="14" y2="14" stroke="#c9a84c" stroke-width="1.2" opacity=".4"/>
      <line x1="66" y1="14" x2="80" y2="14" stroke="#c9a84c" stroke-width="1.2" opacity=".4"/>
      <path d="M28 14 Q34 7 40 14 Q34 21 28 14Z" fill="#fda4af" opacity=".7"/>
      <path d="M52 14 Q46 7 40 14 Q46 21 52 14Z" fill="#fda4af" opacity=".7"/>
    </svg>

    <h2>Kirim Bunga <em>Kapan Saja</em></h2>

    <p>Tidak perlu menunggu toko buka. Kami siap melayani pesanan Anda 24 jam nonstop dengan pengiriman same day ke seluruh Indonesia.</p>

    <!-- Tombol -->
    <div class="svc-cta-btns">
      <a href="<?= waLink() ?>" target="_blank" class="svc-cta-wa">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M5.339 15.626C4.094 13.636 3.5 11.272 3.5 9.005 3.5 4.58 7.08 1 11.5 1s8 3.58 8 8.005c0 4.424-3.58 8.004-8 8.004a7.925 7.925 0 0 1-3.945-1.043L2 17.5l1.339-1.874z"/></svg>
        Pesan via WhatsApp
      </a>
      <a href="<?= BASE_URL ?>/produk" class="svc-cta-explore">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
        Lihat Semua Produk
      </a>
    </div>

    <!-- Trust mini row -->
    <div class="svc-cta-trust">
      <span class="svc-cta-trust-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        Buka 24 Jam
      </span>
      <span class="svc-cta-trust-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Same Day Delivery
      </span>
      <span class="svc-cta-trust-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Bunga 100% Segar
      </span>
      <span class="svc-cta-trust-item">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        Florist Profesional
      </span>
    </div>

  </div><!-- /.svc-cta-inner -->

  <!-- Wave bawah (transisi ke footer) -->
  <div class="svc-cta-final-wave-bot">
    <svg viewBox="0 0 1440 50" preserveAspectRatio="none" height="50">
      <path d="M0,20 C360,55 1080,0 1440,35 L1440,50 L0,50 Z" fill="rgba(245,238,230,0.5)"/>
      <path d="M0,30 C480,55 960,10 1440,40 L1440,50 L0,50 Z" fill="rgba(245,238,230,0.3)"/>
    </svg>
  </div>

</section>

<script>
// ── Bintang di CTA ──────────────────────────────
(function(){
  const c = document.getElementById('svcStars');
  if (!c) return;
  for (let i = 0; i < 60; i++) {
    const s = document.createElement('div');
    s.className = 'svc-cta-star';
    s.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*4}s;--o:${0.3+Math.random()*0.6};animation-delay:${Math.random()*4}s`;
    c.appendChild(s);
  }
})();

// ── Scroll reveal ───────────────────────────────
(function(){
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('svc-visible'), i * 80);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  document.querySelectorAll('.svc-reveal').forEach(el => obs.observe(el));
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>