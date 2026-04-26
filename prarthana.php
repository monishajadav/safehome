<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Prarthana sajeevkumar — Portfolio</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg: #430b0b;
      --surface: #141414;
      --card: #1a1a1a;
      --gold: #d5d2ca;
      --gold-light: #e8c97a;
      --text: #f0ece0;
      --muted: #888;
      --border: rgba(227, 204, 142, 0.18);
      --radius: 12px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-weight: 300;
      overflow-x: hidden;
    }

    /* Grain overlay */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 9999;
      opacity: 0.4;
    }

    /* NAV */
    nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.4rem 4rem;
      background: rgba(13,13,13,0.85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
    }

    .nav-logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem;
      color: var(--gold);
      letter-spacing: 0.05em;
    }

    .nav-links { display: flex; gap: 2.5rem; list-style: none; }
    .nav-links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 0.82rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      transition: color 0.3s;
    }
    .nav-links a:hover { color: var(--gold); }

    /* HERO */
    .hero {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      padding: 8rem 4rem 4rem;
      gap: 4rem;
      position: relative;
      overflow: hidden;
    }

    .hero::after {
      content: '';
      position: absolute;
      right: -10%;
      top: 50%;
      transform: translateY(-50%);
      width: 55vw;
      height: 55vw;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(201,168,76,0.06) 0%, transparent 65%);
      pointer-events: none;
    }

    .hero-tag {
      display: inline-block;
      background: var(--border);
      border: 1px solid var(--border);
      color: var(--gold);
      font-size: 0.72rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      padding: 0.4rem 1rem;
      border-radius: 100px;
      margin-bottom: 1.6rem;
      animation: fadeUp 0.8s ease both;
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.8rem, 5vw, 4.8rem);
      line-height: 1.1;
      animation: fadeUp 0.8s 0.1s ease both;
    }

    .hero h1 em {
      font-style: italic;
      color: var(--gold);
    }

    .hero-subtitle {
      margin-top: 1.4rem;
      color: var(--muted);
      font-size: 1rem;
      line-height: 1.7;
      max-width: 440px;
      animation: fadeUp 0.8s 0.2s ease both;
    }

    .hero-btns {
      margin-top: 2.4rem;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      animation: fadeUp 0.8s 0.3s ease both;
    }

    .btn-primary {
      background: var(--gold);
      color: #0d0d0d;
      font-weight: 500;
      padding: 0.85rem 2rem;
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      font-size: 0.88rem;
      letter-spacing: 0.04em;
      text-decoration: none;
      transition: background 0.25s, transform 0.2s;
      display: inline-block;
    }
    .btn-primary:hover { background: var(--gold-light); transform: translateY(-2px); }

    .btn-outline {
      background: transparent;
      color: var(--gold);
      border: 1px solid var(--gold);
      padding: 0.85rem 2rem;
      border-radius: var(--radius);
      cursor: pointer;
      font-size: 0.88rem;
      letter-spacing: 0.04em;
      text-decoration: none;
      transition: all 0.25s;
      display: inline-block;
    }
    .btn-outline:hover { background: var(--gold); color: #0d0d0d; transform: translateY(-2px); }

    .hero-visual {
      display: flex;
      justify-content: center;
      align-items: center;
      animation: fadeIn 1.2s 0.4s ease both;
    }

    .avatar-ring {
      width: 300px;
      height: 300px;
      border-radius: 50%;
      border: 1.5px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .avatar-ring::before {
      content: '';
      position: absolute;
      inset: -14px;
      border-radius: 50%;
      border: 1px dashed rgba(201,168,76,0.25);
      animation: spin 20s linear infinite;
    }

    .avatar-inner {
      width: 260px;
      height: 260px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1e1a12, #2a2014);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Playfair Display', serif;
      font-size: 5rem;
      color: var(--gold);
      letter-spacing: -0.02em;
    }

    .hero-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      margin-top: 3rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      animation: fadeUp 0.8s 0.4s ease both;
    }

    .stat {
      padding: 1.2rem;
      background: var(--card);
      text-align: center;
    }

    .stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: var(--gold);
    }
    .stat-label { font-size: 0.72rem; color: var(--muted); margin-top: 0.2rem; letter-spacing: 0.1em; text-transform: uppercase; }

    /* SECTIONS */
    section { padding: 6rem 4rem; }
    .section-label {
      font-size: 0.7rem;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 0.6rem;
    }
    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2rem, 3.5vw, 3rem);
      line-height: 1.15;
      margin-bottom: 3rem;
    }
    .section-title em { font-style: italic; color: var(--gold); }

    /* ABOUT */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      align-items: start;
    }

    .about-text p {
      color: var(--muted);
      line-height: 1.85;
      margin-bottom: 1.2rem;
      font-size: 0.97rem;
    }

    .about-info {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      margin-top: 1.6rem;
    }

    .info-item {
      display: flex;
      justify-content: space-between;
      padding: 0.7rem 0;
      border-bottom: 1px solid var(--border);
      font-size: 0.88rem;
    }
    .info-item .label { color: var(--muted); }
    .info-item .value { color: var(--text); font-weight: 500; }

    /* SKILLS */
    .skills-section { background: var(--surface); }
    .skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }

    .skill-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.6rem;
      transition: border-color 0.3s, transform 0.3s;
    }
    .skill-card:hover { border-color: var(--gold); transform: translateY(-4px); }

    .skill-category {
      font-size: 0.7rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 1rem;
    }

    .skill-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .tag {
      background: rgba(201,168,76,0.08);
      border: 1px solid rgba(201,168,76,0.2);
      color: var(--text);
      font-size: 0.78rem;
      padding: 0.35rem 0.8rem;
      border-radius: 100px;
      letter-spacing: 0.04em;
    }

    /* PROJECTS */
    .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; }

    .project-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      transition: border-color 0.3s, transform 0.3s;
      display: flex;
      flex-direction: column;
    }
    .project-card:hover { border-color: var(--gold); transform: translateY(-6px); }

    .project-banner {
      height: 140px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.5rem;
      position: relative;
      overflow: hidden;
    }

    .project-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      opacity: 0.15;
    }

    .banner-ngo { background: linear-gradient(135deg, #1a2a1a, #0d1a0d); }
    .banner-ngo::before { background: radial-gradient(circle, #4caf50, transparent); }

    .banner-food { background: linear-gradient(135deg, #2a1a10, #1a0d05); }
    .banner-food::before { background: radial-gradient(circle, #ff7043, transparent); }

    .project-body { padding: 1.6rem; flex: 1; display: flex; flex-direction: column; }

    .project-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem;
      margin-bottom: 0.6rem;
    }

    .project-desc {
      color: var(--muted);
      font-size: 0.88rem;
      line-height: 1.7;
      flex: 1;
      margin-bottom: 1.2rem;
    }

    .project-stack { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-bottom: 1.2rem; }
    .stack-pill {
      background: rgba(201,168,76,0.1);
      color: var(--gold);
      font-size: 0.72rem;
      padding: 0.28rem 0.7rem;
      border-radius: 100px;
      border: 1px solid rgba(201,168,76,0.25);
      letter-spacing: 0.05em;
    }

    .project-status {
      font-size: 0.72rem;
      color: var(--gold);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }
    .project-status::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--gold);
      display: inline-block;
      animation: pulse 1.5s ease infinite;
    }

    /* EXPERIENCE */
    .experience-section { background: var(--surface); }
    .exp-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 2rem;
      max-width: 760px;
      position: relative;
      overflow: hidden;
    }
    .exp-card::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, var(--gold), transparent);
    }
    .exp-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.2rem; }
    .exp-company { font-family: 'Playfair Display', serif; font-size: 1.25rem; }
    .exp-role { color: var(--muted); font-size: 0.88rem; margin-top: 0.3rem; }
    .exp-date { color: var(--gold); font-size: 0.8rem; letter-spacing: 0.08em; background: rgba(201,168,76,0.08); padding: 0.35rem 0.8rem; border-radius: 100px; border: 1px solid var(--border); white-space: nowrap; }
    .exp-bullets { list-style: none; display: flex; flex-direction: column; gap: 0.7rem; }
    .exp-bullets li { color: var(--muted); font-size: 0.9rem; line-height: 1.65; padding-left: 1.2rem; position: relative; }
    .exp-bullets li::before { content: '—'; position: absolute; left: 0; color: var(--gold); }

    /* EDUCATION & CERTS */
    .edu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .edu-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.6rem;
      transition: border-color 0.3s;
    }
    .edu-card:hover { border-color: var(--gold); }
    .edu-degree { font-family: 'Playfair Display', serif; font-size: 1.05rem; margin-bottom: 0.4rem; }
    .edu-inst { color: var(--muted); font-size: 0.88rem; }
    .edu-year { color: var(--gold); font-size: 0.78rem; letter-spacing: 0.1em; margin-top: 0.8rem; }

    .cert-list { display: flex; flex-direction: column; gap: 0.8rem; margin-top: 2rem; }
    .cert-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1rem 1.4rem;
      transition: border-color 0.3s;
    }
    .cert-item:hover { border-color: var(--gold); }
    .cert-icon { font-size: 1.4rem; }
    .cert-name { font-size: 0.9rem; font-weight: 500; }
    .cert-org { font-size: 0.78rem; color: var(--muted); margin-top: 0.15rem; }

    /* CONTACT */
    .contact-section { background: var(--surface); text-align: center; }
    .contact-links { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-top: 2.5rem; }
    .contact-link {
      background: var(--card);
      border: 1px solid var(--border);
      color: var(--text);
      text-decoration: none;
      padding: 0.9rem 1.8rem;
      border-radius: var(--radius);
      font-size: 0.88rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      transition: all 0.3s;
      letter-spacing: 0.04em;
    }
    .contact-link:hover { background: var(--gold); color: #0d0d0d; border-color: var(--gold); transform: translateY(-3px); }

    .contact-form {
      max-width: 520px;
      margin: 3rem auto 0;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      text-align: left;
    }
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-group label { font-size: 0.78rem; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; }
    .form-group input, .form-group textarea {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--text);
      padding: 0.85rem 1rem;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      transition: border-color 0.3s;
      resize: none;
    }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--gold); }
    .form-group textarea { height: 120px; }

    /* FOOTER */
    footer {
      text-align: center;
      padding: 2rem 4rem;
      border-top: 1px solid var(--border);
      color: var(--muted);
      font-size: 0.82rem;
    }

    /* ANIMATIONS */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.4; }
    }

    /* RESPONSIVE */
    @media (max-width: 860px) {
      nav { padding: 1.2rem 2rem; }
      .nav-links { gap: 1.4rem; }
      .hero { grid-template-columns: 1fr; padding: 7rem 2rem 4rem; text-align: center; }
      .hero-visual { order: -1; }
      .hero-subtitle { margin: 1rem auto 0; }
      .hero-btns { justify-content: center; }
      .hero-stats { max-width: 400px; margin: 2rem auto 0; }
      section { padding: 4rem 2rem; }
      .about-grid { grid-template-columns: 1fr; }
      .edu-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
      .nav-links { display: none; }
    }
  </style>
</head>
<body>

  <!-- NAV -->
  <nav>
    <div class="nav-logo">Prarthana sajeevkumar</div>
    <ul class="nav-links">
      <li><a href="#about">About</a></li>
      <li><a href="#skills">Skills</a></li>
      <li><a href="#projects">Projects</a></li>
      <li><a href="#experience">Experience</a></li>
      <li><a href="#education">Education</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
  </nav>

  <!-- HERO -->
  <section class="hero" id="home">
    <div class="hero-content">
      <div class="hero-tag">✦ Available for Opportunities</div>
      <h1>prarthana<br/><em>sajeevkumar</em></h1>
      <p class="hero-subtitle">
        BCA Final Year Student · cyber security · building skills(programming and coding) .
      </p>
      <div class="hero-btns">
        <a href="#projects" class="btn-primary">View Projects</a>
      </div>
      <div class="hero-stats">
        <div class="stat">
          <div class="stat-num">2+</div>
          <div class="stat-label">Projects</div>
        </div>
        <div class="stat">
          <div class="stat-num">2</div>
          <div class="stat-label">Certifications</div>
        </div>
        <div class="stat">
          <div class="stat-num">2</div>
          <div class="stat-label">Internship</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="avatar-ring">
        <div class="avatar-inner">PS</div>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about">
    <div class="section-label">Who I Am</div>
    <h2 class="section-title">About <em>Me</em></h2>
    <div class="about-grid">
      <div class="about-text">
        <p>
          I'm a final-year BCA student from Alvas Degree College, Moodubidri, currently focuing on developing practical skill,through project.and have good communication skill. 
        <p>
          I have done my intership in jai hr mangament and consultency. Currently, I'm doing documentation  in full-stack NGO platform called <strong style="color:var(--gold)">Safe & Home Foundation</strong> — one of my most ambitious projects yet.
        </p>
        <p>
          i always try to gain knowledge about various coding Languages,and also praticipate in team work.
        </p>
      </div>
      <div>
        <div class="about-info">
          <div class="info-item"><span class="label">Name</span><span class="value">Prarthana sajeevkumar</span></div>
          <div class="info-item"><span class="label">Degree</span><span class="value">BCA — Bachelor of Computer Applications</span></div>
          <div class="info-item"><span class="label">College</span><span class="value">Alvas Degree College, Moodubidri</span></div>
          <div class="info-item"><span class="label">Batch</span><span class="value">2023 – 2026</span></div>
          <div class="info-item"><span class="label">Location</span><span class="value">Andheri-east,mumbai</span></div>
          <div class="info-item"><span class="label">Email</span><span class="value">prarthana359@gmail.com</span></div>
          <div class="info-item"><span class="label">Phone</span><span class="value">+91 9082800656</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- SKILLS -->
  <section id="skills" class="skills-section">
    <div class="section-label">What I Work With</div>
    <h2 class="section-title">My <em>Skills</em></h2>
    <div class="skills-grid">
      <div class="skill-card">
        <div class="skill-category">⚙️ Programming Languages</div>
        <div class="skill-tags">
          <span class="tag">HTML</span>
          <span class="tag">CSS</span>
        </div>
      </div>
      <div class="skill-card">
        <div class="skill-category">🌐 Web Technologies</div>
        <div class="skill-tags">
          <span class="tag">HTML5</span>
          <span class="tag">CSS3</span>
        </div>
      </div>
      </div>
        </div>
      </div>
      <div class="skill-card">
        <div class="skill-category">🤝 Soft Skills</div>
        <div class="skill-tags">
          <span class="tag">Team Work</span>
          <span class="tag">Communication</span>
          <span class="tag">Presentation</span>
          <span class="tag">Time Management</span>
          <span class="tag">Adaptability</span>
        </div>
      </div>
    </div>
  </section>

  <!-- PROJECTS -->
  <section id="projects">
    <div class="section-label">What I've Built</div>
    <h2 class="section-title">My <em>Projects</em></h2>
    <div class="projects-grid">

      <div class="project-card">
        <div class="project-banner banner-ngo">🏠</div>
        <div class="project-body">
          <div class="project-title">Safe & Home Foundation</div>
          <p class="project-desc">
            A full-stack NGO website supporting elderly people, orphans, volunteers, interns, and donors. Features user registration, PHP-MySQL backend, responsive design with Bootstrap, and form validation.
          </p>
          <div class="project-stack">
            <span class="stack-pill">HTML</span>
            <span class="stack-pill">CSS</span>
            <span class="stack-pill">JavaScript</span>
            <span class="stack-pill">PHP</span>
            <span class="stack-pill">MySQL</span>
            <span class="stack-pill">Bootstrap</span>
          </div>
          <div class="project-status">In Progress — Final Year Project</div>
        </div>
      </div>
    </div>
  </section>

  <!-- EXPERIENCE -->
 <section id="experience" class="experience-section">
  <div class="section-label">Work History</div>
  <h2 class="section-title">My <em>Experience</em></h2>

  <!-- Card 1 -->
  <div class="exp-card">
    <div class="exp-header">
      <div>
        <div class="exp-company">JAI HR Management consultency Pvt. Ltd.</div>
        <div class="exp-role">DIGITAL MARKETING INTERN</div>
      </div>
      <div class="exp-date">July 2025 – Aug 2025</div>
    </div>
    <ul class="exp-bullets">
      <li>Assisted in developing and maintaining Digital marketing platfrom.</li>
      <li>Collaborated with a team for various digital apps about there website and helped in uploading there details in various website</details>.</li>
    </ul>
  </div> 

  <!-- Card 2 -->
  <div class="exp-card" style="margin-top: 1.5rem;">
    <div class="exp-header">
      <div>
        <div class="exp-company">substance infotech</div>
        <div class="exp-role">worked as digital mangament intern </div>
      </div>
      <div class="exp-date">Jan 2026 – Feb 2026</div>
    </div>
    <ul class="exp-bullets">
      <li>nsbbs</li>
      <li></li>
    </ul>
  </div> 

</section>

  <!-- EDUCATION & CERTS -->
  <section id="education">
    <div class="section-label">Academic Background</div>
    <h2 class="section-title">Education & <em>Certifications</em></h2>
    <div class="edu-grid">
      <div class="edu-card">
        <div class="edu-degree">Bachelor of Computer Applications (BCA)</div>
        <div class="edu-inst">Alvas Degree College, Moodubidri</div>
        <div class="edu-year">2023 – 2026</div>
      </div>
      <div class="edu-card">
        <div class="edu-degree">Commerce (PUC)</div>
        <div class="edu-inst">marol education academy high school and junior college</div>
        <div class="edu-year">2020 – 2022</div>
      </div>
    </div>

    <div class="cert-list">
      <div class="cert-item">
        <div class="cert-icon">🏆</div>
        <div>
          <div class="cert-name">digital mangament</div>
          <div class="cert-org">jai hr managment</div>
        </div>
      </div>
      <div class="cert-item">
        <div class="cert-icon">🎖️</div>
        <div>
          <div class="cert-name">digital Internship Certificate</div>
          <div class="cert-org">substance infotech Pvt. Ltd.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact" class="contact-section">
    <div class="section-label">Let's Connect</div>
    <h2 class="section-title">Get In <em>Touch</em></h2>
    <p style="color:var(--muted); max-width:480px; margin:0 auto; font-size:0.95rem; line-height:1.7;">
      Open to internship roles, freelance projects, and full-time opportunities. Feel free to reach out!
    </p>

    <div class="contact-links">
      <a href="mailto:prarthana359@gmail.com" class="contact-link">
        ✉️ prarthana359@gmail.com
      </a>
      <a href="tel:+919082800656" class="contact-link">
        📞 +91 9082800656
      </a>
      </a>
      <a href=www.linkedin.com/in/prarthana359
 target="_blank" class="contact-link">
        💼 LinkedIn
      </a>
    </div>

  <!-- FOOTER -->
  <footer>
    <p>© 2025 prarthana sajeevkumar· Crafted with ❤️ using HTML, CSS & JavaScript</p>
  </footer>

  <script>
    // Scroll fade-in animation
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.skill-card, .project-card, .edu-card, .cert-item, .exp-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(24px)';
      el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(el);
    });

    // Active nav highlight
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-links a');
    window.addEventListener('scroll', () => {
      let current = '';
      sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 80) current = s.getAttribute('id');
      });
      navLinks.forEach(a => {
        a.style.color = a.getAttribute('href') === '#' + current ? 'var(--gold)' : '';
      });
    });
  </script>
</body>
</html>