<?php
$pageExtraHead = <<<'STYLE'
<style>
  :root {
    --bg: #07111f;
    --surface: #0f1d35;
    --surface-soft: #162446;
    --surface-alt: #1f2f53;
    --text: #e9eef8;
    --muted: #9fb3d9;
    --accent: #5f8cff;
    --accent-soft: rgba(95,140,255,0.18);
    --accent-strong: #89a7ff;
    --shadow: 0 20px 60px rgba(0,0,0,0.30);
  }

  body {
    background: radial-gradient(circle at top, rgba(95,140,255,0.16), transparent 28%),
                linear-gradient(180deg, #07111f 0%, #091829 100%);
    color: var(--text);
  }

  .landing {
    min-height: calc(100vh - 100px);
    padding: 3rem 1.5rem 4rem;
    display: grid;
    gap: 3rem;
  }

  .hero {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(320px, 1fr);
    align-items: center;
    gap: 2.5rem;
    padding: 3rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 28px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(18px);
  }

  .hero-copy {
    max-width: 620px;
  }

  .hero-copy .eyebrow {
    display: inline-flex;
    padding: 0.65rem 1rem;
    border-radius: 999px;
    background: var(--accent-soft);
    color: var(--accent-strong);
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
  }

  .hero-copy h1 {
    font-size: clamp(2.8rem, 4vw, 4.4rem);
    line-height: 1.02;
    margin: 0;
    letter-spacing: -0.04em;
  }

  .hero-copy p {
    margin: 1.7rem 0 2.5rem;
    max-width: 540px;
    color: var(--muted);
    line-height: 1.85;
    font-size: 1.05rem;
  }

  .cta-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .btn-primary,
  .btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 150px;
    padding: 1rem 1.35rem;
    border-radius: 999px;
    border: 1px solid transparent;
    font-weight: 600;
    transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    text-decoration: none;
  }

  .btn-primary {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 14px 30px rgba(95,140,255,0.28);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    background: var(--accent-strong);
  }

  .btn-secondary {
    background: rgba(255,255,255,0.06);
    color: var(--text);
    border-color: rgba(255,255,255,0.12);
  }

  .btn-secondary:hover {
    background: rgba(255,255,255,0.12);
  }

  .header {
    padding: 1rem 0;
  }

  .nav {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
  }

  .logo h2 {
    margin: 0;
    font-size: 1.75rem;
    letter-spacing: -0.03em;
    text-align: center;
  }

  .hero-visual {
    display: grid;
    place-items: center;
    border-radius: 28px;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(95,140,255,0.16), rgba(6,12,28,0.85));
    border: 1px solid rgba(255,255,255,0.08);
  }

  .hero-visual img {
    width: 100%;
    max-width: 520px;
    height: auto;
    display: block;
    object-fit: contain;
  }

  .feature-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.25rem;
  }

  .feature-card {
    padding: 1.8rem;
    border-radius: 22px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 24px 40px rgba(0,0,0,0.18);
  }

  .feature-card h3 {
    margin: 0 0 0.85rem;
    font-size: 1.15rem;
  }

  .feature-card p {
    margin: 0;
    color: var(--muted);
    line-height: 1.8;
  }

  @media (max-width: 960px) {
    .hero {
      grid-template-columns: 1fr;
      padding: 2rem;
    }

    .feature-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .landing {
      padding: 2rem 1rem 3rem;
    }

    .hero {
      padding: 1.75rem;
    }

    .hero-copy h1 {
      font-size: 2.7rem;
    }
  }
</style>
STYLE;
include "includes/header.php";
?>

<main class="landing">
  <section class="hero">
    <div class="hero-copy">
      <span class="eyebrow">Expense tracker</span>
      <h1>Smart spending starts here.</h1>
      <p>Manage incomes, track expenses, and keep your budget on target with a clean dashboard designed for modern finance habits.</p>
      <div class="cta-group">
        <a href="includes/index.php" class="btn-primary">Sign In</a>
        <a href="includes/signup.php" class="btn-secondary">Sign Up</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="images/ex1.png" alt="Expense tracker preview">
    </div>
  </section>

  <section class="feature-grid">
    <div class="feature-card">
      <h3>Instant expense summary</h3>
      <p>See daily, weekly, and monthly spending at a glance so you never miss a budget target.</p>
    </div>
    <div class="feature-card">
      <h3>Secure account access</h3>
      <p>Sign in securely and keep your financial data private while enjoying fast login flows.</p>
    </div>
    <div class="feature-card">
      <h3>Easy setup</h3>
      <p>Get started in seconds with a simple onboarding path for both new and returning users.</p>
    </div>
  </section>
</main>

<?php include "includes/footer.php"; ?>