<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>C.D.U — Sua Cidade, Seu Cuidado</title>
  <script>document.documentElement.classList.toggle('dark', localStorage.getItem('cdu-theme') === 'dark');</script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body.cdu-home {
      margin: 0;
      min-width: 320px;
      background: var(--cdu-bg) !important;
      color: var(--cdu-text) !important;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    body.cdu-home * { box-sizing: border-box; }
    body.cdu-home a { text-decoration: none; }
    body.cdu-home a:focus-visible { outline: 3px solid var(--cdu-orange); outline-offset: 3px; }
    body.cdu-home .container {
      width: min(1120px, calc(100% - 48px));
      margin: 0 auto;
    }

    body.cdu-home header {
      position: relative;
      z-index: 2;
      width: 100%;
      padding: 22px 0;
      border-bottom: 1px solid var(--cdu-border);
      background: color-mix(in srgb, var(--cdu-surface) 94%, transparent);
    }

    body.cdu-home header nav {
      width: min(1120px, calc(100% - 48px));
      margin: 0 auto;
    }

    body.cdu-home nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 28px;
    }

    body.cdu-home .logo { display: flex; align-items: center; flex: 0 0 auto; }
    body.cdu-home .logo-img {
      display: block;
      width: 142px;
      max-height: 48px;
      object-fit: contain;
      /* Mantém as cores originais da marca presentes no arquivo da logo. */
      filter: none;
    }
    body.cdu-home .logo-img-dark { display: none; }
    html.dark body.cdu-home .logo-img-light { display: none; }
    html.dark body.cdu-home .logo-img-dark { display: block; }

    body.cdu-home .nav-links {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 34px;
      margin-left: auto;
    }

    body.cdu-home .nav-links a {
      color: var(--cdu-muted) !important;
      font-size: 14px;
      font-weight: 550;
      transition: color .2s ease;
    }
    body.cdu-home .nav-links a:hover { color: var(--cdu-orange) !important; }

    body.cdu-home .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 0 0 auto;
    }
    body.cdu-home .nav-links .cdu-theme-toggle { display: none; }

    body.cdu-home .btn-login,
    body.cdu-home .btn-cadastrar {
      display: inline-flex;
      min-height: 42px;
      align-items: center;
      justify-content: center;
      padding: 0 19px;
      border: 1px solid var(--cdu-green);
      border-radius: 9px;
      font-size: 14px;
      font-weight: 650;
      transition: background .2s ease, color .2s ease, transform .2s ease;
    }
    body.cdu-home .btn-login {
      color: var(--cdu-green) !important;
      background: var(--cdu-surface) !important;
    }
    body.cdu-home .btn-cadastrar {
      color: #fff !important;
      background: var(--cdu-orange) !important;
      border-color: var(--cdu-orange);
    }
    body.cdu-home .btn-login:hover,
    body.cdu-home .btn-cadastrar:hover { transform: translateY(-1px); }
    body.cdu-home .btn-login:hover { background: var(--cdu-green-soft) !important; }
    body.cdu-home .btn-cadastrar:hover { background: var(--cdu-orange-dark) !important; border-color: var(--cdu-orange-dark); }

    body.cdu-home .hero { padding: 76px 0 54px; }
    body.cdu-home .hero-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
      align-items: center;
      gap: 64px;
    }
    body.cdu-home .hero h1 {
      margin: 0 0 20px;
      color: var(--cdu-text) !important;
      font-size: clamp(42px, 5.2vw, 64px);
      line-height: 1.08;
      letter-spacing: -2.8px;
      font-weight: 800;
    }
    body.cdu-home .accent { color: var(--cdu-orange) !important; }
    body.cdu-home .hero p {
      max-width: 510px;
      margin: 0;
      color: var(--cdu-muted) !important;
      font-size: 17px;
      line-height: 1.8;
    }
    body.cdu-home .hero-buttons {
      display: flex;
      align-items: center;
      gap: 22px;
      margin-top: 30px;
    }
    body.cdu-home .btn-primary {
      display: inline-flex;
      min-height: 48px;
      align-items: center;
      justify-content: center;
      padding: 0 23px;
      border-radius: 9px;
      background: var(--cdu-green) !important;
      color: #fff !important;
      font-size: 14px;
      font-weight: 700;
      box-shadow: 0 8px 18px rgba(47, 125, 85, .18);
      transition: transform .2s ease, background .2s ease;
    }
    body.cdu-home .btn-primary:hover { background: var(--cdu-green-dark) !important; transform: translateY(-2px); }
    body.cdu-home .link-secondary {
      color: var(--cdu-green-dark) !important;
      font-size: 14px;
      font-weight: 650;
    }
    body.cdu-home .link-secondary:hover { color: var(--cdu-orange) !important; }

    body.cdu-home .map-card {
      overflow: hidden;
      padding: 12px;
      border: 1px solid var(--cdu-border);
      border-radius: 18px;
      background: var(--cdu-surface-muted);
      box-shadow: var(--cdu-shadow);
    }
    body.cdu-home .map-img { display: block; width: 100%; height: auto; border-radius: 11px; }

    body.cdu-home .stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
      margin-top: 64px;
      padding: 25px 12px;
      border: 1px solid var(--cdu-border);
      border-radius: 14px;
      background: var(--cdu-surface);
      box-shadow: 0 8px 26px rgba(23, 32, 51, .04);
    }
    body.cdu-home .stat { text-align: center; }
    body.cdu-home .stat + .stat { border-left: 1px solid var(--cdu-border); }
    body.cdu-home .num { color: var(--cdu-green) !important; font-size: 30px; font-weight: 800; letter-spacing: -1px; }
    body.cdu-home .label { margin-top: 4px; color: var(--cdu-muted) !important; font-size: 13px; }

    body.cdu-home #como-funciona { padding: 58px 0 82px; }
    body.cdu-home .section-head { margin-bottom: 34px; text-align: center; }
    body.cdu-home .section-head h2 {
      margin: 0 0 9px;
      color: var(--cdu-text) !important;
      font-size: clamp(27px, 3vw, 36px);
      line-height: 1.2;
      letter-spacing: -1px;
      font-weight: 780;
    }
    body.cdu-home .section-head p { margin: 0; color: var(--cdu-muted) !important; font-size: 15px; }
    body.cdu-home .steps {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 18px;
    }
    body.cdu-home .step {
      padding: 25px 20px;
      border: 1px solid var(--cdu-border);
      border-radius: 13px;
      background: var(--cdu-surface);
      transition: transform .2s ease, box-shadow .2s ease;
    }
    body.cdu-home .step:hover { transform: translateY(-3px); box-shadow: var(--cdu-shadow); }
    body.cdu-home .icon-box {
      display: grid;
      width: 46px;
      height: 46px;
      place-items: center;
      border-radius: 12px;
      background: var(--cdu-orange-soft);
      color: var(--cdu-orange-dark) !important;
    }
    body.cdu-home .icon-box svg { width: 23px; height: 23px; }
    body.cdu-home .step h3 { margin: 18px 0 8px; color: var(--cdu-text) !important; font-size: 16px; font-weight: 700; }
    body.cdu-home .step p { margin: 0; color: var(--cdu-muted) !important; font-size: 13px; line-height: 1.7; }
    body.cdu-home .divider { margin: 64px 0; border: 0; border-top: 1px solid var(--cdu-border); }

    body.cdu-home .testimonials {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }
    body.cdu-home .t-card {
      position: relative;
      padding: 25px;
      border: 1px solid var(--cdu-border);
      border-radius: 13px;
      background: var(--cdu-surface);
    }
    body.cdu-home .quote-mark { color: var(--cdu-orange); font-size: 38px; line-height: 1; font-weight: 800; }
    body.cdu-home .quote { min-height: 92px; margin: 10px 0 22px; color: var(--cdu-muted) !important; font-size: 14px; line-height: 1.75; }
    body.cdu-home .t-person { display: flex; align-items: center; gap: 11px; }
    body.cdu-home .avatar {
      display: grid;
      width: 39px;
      height: 39px;
      flex: 0 0 39px;
      place-items: center;
      border-radius: 50%;
      background: var(--cdu-green-soft);
      color: var(--cdu-green) !important;
      font-size: 12px;
      font-weight: 750;
    }
    body.cdu-home .name { color: var(--cdu-text) !important; font-size: 13px; font-weight: 700; }
    body.cdu-home .place { margin-top: 3px; color: var(--cdu-muted) !important; font-size: 12px; }

    body.cdu-home .about-project { padding-bottom: 82px; }
    body.cdu-home .about-project-card {
      display: grid;
      grid-template-columns: minmax(0, 1.2fr) minmax(250px, .8fr);
      gap: 28px;
      padding: 28px;
      border: 1px solid var(--cdu-border);
      border-radius: 16px;
      background: var(--cdu-surface);
      box-shadow: var(--cdu-shadow);
    }
    body.cdu-home .about-project-intro { display: flex; align-items: flex-start; gap: 16px; }
    body.cdu-home .about-project-icon { display: grid; width: 52px; height: 52px; flex: 0 0 52px; place-items: center; border-radius: 12px; background: var(--cdu-green-soft); }
    body.cdu-home .about-project-icon img { display: block; width: 38px; height: 38px; object-fit: contain; }
    body.cdu-home .about-project-icon .about-project-logo-dark { display: none; }
    html.dark body.cdu-home .about-project-icon .about-project-logo-light { display: none; }
    html.dark body.cdu-home .about-project-icon .about-project-logo-dark { display: block; }
    body.cdu-home .about-project-card h3 { margin: 0 0 8px; color: var(--cdu-text); font-size: 17px; }
    body.cdu-home .about-project-card p { margin: 0; color: var(--cdu-muted); font-size: 14px; line-height: 1.75; }
    body.cdu-home .about-project-team { border-left: 1px solid var(--cdu-border); padding-left: 28px; }
    body.cdu-home .about-project-team ul { display: grid; gap: 8px; margin: 0; padding: 0; color: var(--cdu-muted); font-size: 14px; list-style: none; }
    body.cdu-home .about-project-team li::before { margin-right: 8px; color: var(--cdu-green); content: '•'; }

    @media (max-width: 900px) {
      body.cdu-home header { padding: 16px 0; }
      body.cdu-home header nav { position: relative; flex-wrap: nowrap; gap: 10px; }
      body.cdu-home .logo-img { width: 112px; }
      body.cdu-home .nav-links { display: none !important; position: absolute !important; top: calc(100% + 10px) !important; left: 0 !important; right: 0 !important; z-index: 5 !important; flex-direction: column !important; align-items: stretch !important; justify-content: flex-start !important; gap: 3px !important; width: auto !important; margin: 0 !important; padding: 8px !important; border: 1px solid var(--cdu-border) !important; border-radius: 14px !important; background: var(--cdu-surface) !important; box-shadow: var(--cdu-shadow) !important; }
      body.cdu-home .nav-links.is-open { display: flex !important; }
      body.cdu-home .nav-links a { display: flex !important; align-items: center !important; justify-content: flex-start !important; min-height: 44px !important; padding: 0 14px !important; border-radius: 8px !important; color: var(--cdu-text) !important; font-size: 14px !important; }
      body.cdu-home .nav-links a:hover { background: var(--cdu-green-soft) !important; color: var(--cdu-text) !important; }
      body.cdu-home .nav-actions { margin-left: auto; gap: 7px; }
      body.cdu-home .nav-actions .cdu-theme-toggle { display: none; }
      body.cdu-home .nav-links .cdu-theme-toggle { display: inline-flex; width: 100%; min-height: 44px; justify-content: flex-start; margin-top: 4px; padding: 0 14px; border: 0; border-top: 1px solid var(--cdu-border); border-radius: 0; background: transparent; color: var(--cdu-text); text-align: left; }
      body.cdu-home .nav-toggle { display: inline-flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; gap: 5px !important; width: 42px !important; height: 42px !important; flex: 0 0 42px !important; border: 1px solid var(--cdu-border) !important; border-radius: 10px !important; background: var(--cdu-surface) !important; cursor: pointer !important; }
      body.cdu-home .nav-toggle span { display: block !important; width: 21px !important; height: 2px !important; border-radius: 2px !important; background: var(--cdu-text) !important; transition: transform .2s ease, opacity .2s ease; }
      body.cdu-home .nav-toggle[aria-expanded="true"] span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
      body.cdu-home .nav-toggle[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
      body.cdu-home .nav-toggle[aria-expanded="true"] span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
      body.cdu-home .btn-login, body.cdu-home .btn-cadastrar { min-height: 38px; padding: 0 12px; font-size: 12px; }
    }

    @media (min-width: 901px) {
      body.cdu-home .nav-toggle { display: none; }
    }

    @media (max-width: 820px) {
      body.cdu-home .hero-grid { grid-template-columns: 1fr; gap: 36px; }
      body.cdu-home .hero { padding-top: 52px; }
      body.cdu-home .steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      body.cdu-home .testimonials { grid-template-columns: 1fr; }
      body.cdu-home .quote { min-height: 0; }
      body.cdu-home .about-project-card { grid-template-columns: 1fr; }
      body.cdu-home .about-project-team { border-top: 1px solid var(--cdu-border); border-left: 0; padding-top: 24px; padding-left: 0; }
    }

    @media (max-width: 560px) {
      body.cdu-home .container { width: min(100% - 32px, 1120px); }
      body.cdu-home .hero h1 { font-size: 43px; letter-spacing: -1.8px; }
      body.cdu-home .hero p { font-size: 15px; }
      body.cdu-home .stats { gap: 8px; margin-top: 38px; padding: 19px 7px; }
      body.cdu-home .num { font-size: 24px; }
      body.cdu-home .label { font-size: 11px; }
      body.cdu-home .steps { grid-template-columns: 1fr; }
      body.cdu-home .divider { margin: 45px 0; }
    }
  </style>
</head>

<body class="cdu-home">

  <header>
      <nav>
        <div class="logo">
          <img
            src="{{ Vite::asset('resources/images/logo.svg') }}"
            alt="Logo" class="logo-img logo-img-light">
          <img
            src="{{ Vite::asset('resources/images/logo_invert.svg') }}"
            alt="Logo" class="logo-img logo-img-dark">
        </div>
        <div class="nav-links" id="home-nav-menu" data-nav-menu>
          <a href="#inicio">Início</a>
          <a href="#como-funciona">Como Funciona</a>
          <a href="#sobre">Sobre</a>
          <x-theme-toggle />
        </div>
        <div class="nav-actions">
          <x-theme-toggle />
          <a href="{{ route('login') }}" class="btn-login">Entrar</a>
          <a href="{{ route('register') }}" class="btn-cadastrar">Cadastrar</a>
        </div>
        <button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="home-nav-menu" aria-label="Abrir menu"><span></span><span></span><span></span></button>
      </nav>
  </header>

  <div class="container">
    <!-- HERO -->
    <section class="hero" id="inicio">
      <div class="hero-grid">
        <div>
          <h1>Sua Cidade,<br><span class="accent">Seu Cuidado.</span></h1>
          <p>Ajude a tornar sua cidade melhor. Registre problemas urbanos do seu bairro e acompanhe a resolução.</p>
          <div class="hero-buttons">
            <a href="{{ route('login') }}" class="btn-primary">Denunciar</a>
            <a href="#como-funciona" class="link-secondary">Saiba Mais</a>
          </div>
        </div>

        <div class="map-card">
          <img
            src="{{ Vite::asset('resources/images/map-homepage.png') }}"
            alt="Mapa de ocorrências" class="map-img">
        </div>
      </div>

      <div class="stats">
        <div class="stat">
          <div class="num">1393</div>
          <div class="label">Denúncias</div>
        </div>
        <div class="stat">
          <div class="num">89%</div>
          <div class="label">Resolvidas</div>
        </div>
        <div class="stat">
          <div class="num">42</div>
          <div class="label">Bairros</div>
        </div>
      </div>
    </section>

    <!-- COMO FUNCIONA -->
    <section id="como-funciona">
      <div class="section-head">
        <h2>Como funciona</h2>
        <p>Simples, rápido e transparente.</p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M4 8h3l2-2h6l2 2h3v11H4z" />
              <circle cx="12" cy="13" r="3.5" />
            </svg>
          </div>
          <h3>1. Registre</h3>
          <p>Tire uma foto e descreva o problema encontrado.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="10.5" cy="10.5" r="6.5" />
              <line x1="20" y1="20" x2="15.2" y2="15.2" />
            </svg>
          </div>
          <h3>2. Análise</h3>
          <p>A equipe avalia e encaminha pro órgão responsável.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 21s7-6.3 7-11.5A7 7 0 105 9.5C5 14.7 12 21 12 21z" />
              <circle cx="12" cy="9.5" r="2.3" />
            </svg>
          </div>
          <h3>3. Acompanhe</h3>
          <p>Veja o status atualizado em tempo real.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="4,12.5 9,17.5 20,6.5" />
            </svg>
          </div>
          <h3>4. Resolva</h3>
          <p>Receba um aviso assim que for resolvido.</p>
        </div>
      </div>

      <hr class="divider">

      <div class="section-head">
        <h2>O que dizem os moradores</h2>
        <p>Quem já usou pra melhorar o próprio bairro.</p>
      </div>

      <div class="testimonials">
        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">Consegui registrar um buraco na rua em menos de um minuto e acompanhei toda a resolução pelo
            próprio site.</p>
          <div class="t-person">
            <div class="avatar">MS</div>
            <div>
              <div class="name">Marina Silva</div>
              <div class="place">Jardim das Flores</div>
            </div>
          </div>
        </div>

        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">A poda da árvore da minha rua foi resolvida em uma semana depois que registrei por aqui.</p>
          <div class="t-person">
            <div class="avatar">CA</div>
            <div>
              <div class="name">Carlos Andrade</div>
              <div class="place">Vila Nova</div>
            </div>
          </div>
        </div>

        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">Finalmente um jeito fácil de cobrar melhorias no bairro sem precisar de burocracia.</p>
          <div class="t-person">
            <div class="avatar">RC</div>
            <div>
              <div class="name">Renata Costa</div>
              <div class="place">Síndica, Cond. Bela Vista</div>
            </div>
          </div>
        </div>
      </div>

      <hr class="divider">

      <section id="sobre" class="about-project" aria-labelledby="about-project-title">
        <div class="section-head">
          <h2 id="about-project-title">Sobre o projeto</h2>
          <p>Uma iniciativa para aproximar a comunidade da transformação da cidade.</p>
        </div>
        <div class="about-project-card">
          <div class="about-project-intro">
            <span class="about-project-icon" aria-hidden="true">
              <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="" class="about-project-logo-light">
              <img src="{{ Vite::asset('resources/images/logo_invert.svg') }}" alt="" class="about-project-logo-dark">
            </span>
            <div>
              <h3>C.D.U — Centro de Denúncias Urbanas</h3>
              <p>O Centro de Denúncias Urbanas conecta cidadãos e poder público para identificar, acompanhar e resolver problemas urbanos de forma simples, transparente e colaborativa.</p>
            </div>
          </div>
          <div class="about-project-team">
            <h3>Desenvolvedores</h3>
            <ul>
              <li>Daniel Sobral</li>
              <li>Deyvid Borges</li>
              <li>Hudson Holanda</li>
              <li>Mateus Lima</li>
            </ul>
          </div>
        </div>
      </section>
    </section>
  </div>

  <x-back-to-top />

</body>

</html>
