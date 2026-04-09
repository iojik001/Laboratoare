<?php
// ══════════════════════════════════════════════════
//  Lucrarea de Laborator Nr. 4 — CGI
//  Prelucrarea datelor + salvare în fișier
// ══════════════════════════════════════════════════

header('Content-Type: text/html; charset=UTF-8');

// Fișierul unde se salvează datele
$dataFile = 'quiz_results.txt';

// Sanitizare input
function sanitize($val) {
    return htmlspecialchars(trim(strip_tags($val)), ENT_QUOTES, 'UTF-8');
}

// Validare server-side
function validate($data) {
    $errors = [];
    if (empty($data['name']) || strlen($data['name']) < 2 || strlen($data['name']) > 50)
        $errors[] = 'Numele este obligatoriu (2–50 caractere).';
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email-ul introdus nu este valid.';
    if (empty($data['age']) || !is_numeric($data['age']) || $data['age'] < 5 || $data['age'] > 120)
        $errors[] = 'Vârsta trebuie să fie între 5 și 120.';
    if (empty($data['character']))
        $errors[] = 'Selectați un personaj preferat.';
    return $errors;
}

// Salvare în fișier
function saveToFile($data, $filename) {
    $timestamp = date('Y-m-d H:i:s');
    $line = implode(' | ', [
        $timestamp,
        'Nume: '        . $data['name'],
        'Email: '       . $data['email'],
        'Varsta: '      . $data['age'],
        'Personaj: '    . $data['character'],
        'Film: '        . $data['film'],
        'Comentariu: '  . $data['comment']
    ]) . PHP_EOL;
    file_put_contents($filename, $line, FILE_APPEND | LOCK_EX);
}

// Procesare POST
$result   = null;
$errors   = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name'      => sanitize($_POST['name']      ?? ''),
        'email'     => sanitize($_POST['email']     ?? ''),
        'age'       => sanitize($_POST['age']       ?? ''),
        'character' => sanitize($_POST['character'] ?? ''),
        'film'      => sanitize($_POST['film']      ?? ''),
        'comment'   => sanitize($_POST['comment']   ?? ''),
    ];

    $errors = validate($formData);

    if (empty($errors)) {
        saveToFile($formData, $dataFile);
        $result = $formData;
    }
}

// Citire ultimele 10 intrari din fisier
$recentEntries = [];
if (file_exists($dataFile)) {
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentEntries = array_slice(array_reverse($lines), 0, 10);
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shrek — Formular (Lab 4)</title>
  <link rel="stylesheet" href="../CSS/style.css">
  <style>
    #form-wrapper {
      max-width: 680px;
      margin: 0 auto;
      padding: 10px 0 60px;
      position: relative;
      z-index: 1;
    }
    .lab-badge {
      display: inline-block;
      background: rgba(140,198,63,.18);
      border: 1px solid rgba(140,198,63,.4);
      border-radius: 99px;
      padding: 5px 18px;
      color: #8cc63f;
      font-size: .78rem;
      letter-spacing: .15em;
      text-transform: uppercase;
      margin-bottom: 14px;
    }
    #contact-form {
      background: linear-gradient(160deg, rgba(30,60,10,.92), rgba(14,34,4,.97));
      border: 1px solid rgba(140,198,63,.28);
      border-radius: 18px;
      padding: 38px 40px;
      margin-bottom: 36px;
    }
    .form-title {
      font-size: 1.5rem;
      color: #c8f060;
      letter-spacing: .14em;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .form-subtitle {
      color: #7aaa48;
      font-size: .91rem;
      margin-bottom: 30px;
    }
    .field-group { margin-bottom: 22px; }
    .field-group label {
      display: block;
      color: #8cc63f;
      font-size: .87rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .required { color: #c8f060; margin-left: 3px; }
    .field-group input[type="text"],
    .field-group input[type="email"],
    .field-group input[type="number"],
    .field-group select,
    .field-group textarea {
      width: 100%;
      background: rgba(0,0,0,.45);
      border: 2px solid rgba(140,198,63,.22);
      border-radius: 10px;
      color: #e8f5d0;
      font-family: 'Georgia', serif;
      font-size: .97rem;
      padding: 13px 16px;
      outline: none;
      transition: border-color .28s, background .28s, box-shadow .28s;
    }
    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
      border-color: #8cc63f;
      background: rgba(0,0,0,.6);
      box-shadow: 0 0 0 3px rgba(140,198,63,.12);
    }
    .field-group select option { background: #1b2f0e; color: #e8f5d0; }
    .field-group textarea { min-height: 100px; resize: vertical; }
    .radio-group { display: flex; flex-direction: column; gap: 10px; }
    .radio-option {
      display: flex; align-items: center; gap: 12px; cursor: pointer;
      padding: 12px 16px; border-radius: 10px;
      border: 2px solid rgba(140,198,63,.16);
      background: rgba(0,0,0,.3);
      transition: border-color .25s, background .25s;
    }
    .radio-option:hover { border-color: rgba(140,198,63,.45); background: rgba(140,198,63,.1); }
    .radio-option input[type="radio"] { accent-color: #8cc63f; width: 18px; height: 18px; }
    .radio-option span { color: #c8ddb0; font-size: .95rem; }
    .radio-option.checked-opt { border-color: #8cc63f; background: rgba(140,198,63,.16); }
    .radio-option.checked-opt span { color: #c8f060; }
    .field-error {
      color: #ff9090; font-size: .82rem; margin-top: 6px; display: none;
    }
    .field-error.visible { display: block; }
    input.invalid, select.invalid, textarea.invalid {
      border-color: rgba(220,80,80,.6) !important;
    }
    .error-box {
      background: rgba(180,40,40,.2);
      border: 1px solid rgba(220,80,80,.4);
      border-radius: 10px;
      padding: 16px 20px;
      margin-bottom: 24px;
    }
    .error-box p { color: #ff9090; font-size: .92rem; margin-bottom: 4px; }
    #btn-submit-form {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #2d5a0e, #4a8a1a);
      border: 2px solid #8cc63f;
      border-radius: 10px;
      color: #c8f060;
      font-family: 'Georgia', serif;
      font-size: 1.05rem;
      font-weight: bold;
      letter-spacing: .1em;
      text-transform: uppercase;
      cursor: pointer;
      transition: background .3s, transform .22s, box-shadow .3s;
      margin-top: 10px;
    }
    #btn-submit-form:hover {
      background: linear-gradient(135deg, #4a8a1a, #6aaa2a);
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(140,198,63,.28);
    }
    /* Succes */
    #success-card {
      background: linear-gradient(160deg, rgba(20,50,5,.97), rgba(10,30,2,.99));
      border: 2px solid rgba(140,198,63,.55);
      border-radius: 18px;
      padding: 44px 40px;
      text-align: center;
      box-shadow: 0 12px 48px rgba(140,198,63,.2);
      animation: fadeSlideIn .5s ease;
    }
    @keyframes fadeSlideIn {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    #success-card .checkmark { font-size: 3.5rem; display: block; margin-bottom: 16px; }
    #success-card h2 { font-size: 1.6rem; color: #c8f060; letter-spacing: .15em; margin-bottom: 8px; }
    #success-card p { color: #b0cc90; font-size: .98rem; line-height: 1.75; margin-bottom: 12px; }
    .result-table { width: 100%; border-collapse: collapse; margin: 26px 0; text-align: left; }
    .result-table td { padding: 10px 14px; border-bottom: 1px solid rgba(140,198,63,.12); font-size: .93rem; }
    .result-table td:first-child {
      color: #8cc63f; font-weight: bold; width: 38%;
      text-transform: uppercase; letter-spacing: .08em; font-size: .83rem;
    }
    .result-table td:last-child { color: #c8ddb0; }
    .result-table tr:last-child td { border-bottom: none; }
    /* Log */
    #log-section {
      background: rgba(0,0,0,.3);
      border: 1px solid rgba(140,198,63,.2);
      border-radius: 14px;
      padding: 28px 32px;
      margin-top: 36px;
    }
    #log-section h3 { font-size: 1rem; color: #8cc63f; text-transform: uppercase; letter-spacing: .14em; margin-bottom: 16px; }
    .log-entry { font-size: .8rem; color: #7a9a60; padding: 8px 0; border-bottom: 1px solid rgba(140,198,63,.08); word-break: break-all; }
    .log-entry:last-child { border-bottom: none; }
    .hint { color: #6a9040; font-size: .82rem; margin-top: 5px; }
    .char-counter { text-align: right; font-size: .78rem; color: #6a9040; margin-top: 4px; }
    @keyframes shake {
      0%,100%{transform:translateX(0)} 20%{transform:translateX(-9px)}
      40%{transform:translateX(9px)} 60%{transform:translateX(-5px)} 80%{transform:translateX(5px)}
    }
    @keyframes rippleAnim { to { transform:scale(4); opacity:0; } }
  </style>
</head>
<body>

<div id="header">
  <h1>SHREK QUIZ</h1>
  <p>Formular de participare</p>
</div>

<div id="nav">
  <ul>
    <li><a href="../index.html">Acasă</a></li>
    <li><a href="../HTML/shrek1.html">Film 1</a></li>
    <li><a href="../HTML/shrek2.html">Film 2</a></li>
    <li><a href="../HTML/shrek3.html">Film 3</a></li>
    <li><a href="../HTML/shrek4.html">Film 4</a></li>
    <li><a href="../HTML/Quiz.html">Quiz</a></li>
    <li><a href="quiz.php" class="active">Formular</a></li>
    <li><a href="../HTML/trivia.html">Trivia</a></li>
  </ul>
</div>

<div id="main">
<div id="form-wrapper">

  <div style="text-align:center; margin-bottom:34px; padding:28px 32px; background:rgba(0,0,0,.32); border-radius:16px; border:1px solid rgba(140,198,63,.22);">
    <span class="lab-badge">Lucrarea de Laborator Nr. 4 — CGI</span>
    <h2 class="form-title" style="margin-top:10px;">Înregistrare Quiz Shrek</h2>
    <p style="color:#b0cc90; line-height:1.7; font-size:.97rem;">
      Completează formularul. Datele sunt validate și salvate pe server în <code style="color:#8cc63f">quiz_results.txt</code>.
    </p>
  </div>

  <?php if ($result !== null): ?>
  <!-- ══ SUCCES ══ -->
  <div id="success-card">
    <span class="checkmark">🧅</span>
    <h2>Mulțumim, <?= htmlspecialchars($result['name']) ?>!</h2>
    <p>Datele tale au fost salvate cu succes în <strong style="color:#8cc63f">quiz_results.txt</strong>.</p>
    <table class="result-table">
      <tr><td>Nume</td><td><?= htmlspecialchars($result['name']) ?></td></tr>
      <tr><td>Email</td><td><?= htmlspecialchars($result['email']) ?></td></tr>
      <tr><td>Vârstă</td><td><?= htmlspecialchars($result['age']) ?> ani</td></tr>
      <tr><td>Personaj</td><td><?= htmlspecialchars($result['character']) ?></td></tr>
      <tr><td>Film favorit</td><td><?= htmlspecialchars($result['film'] ?: '—') ?></td></tr>
      <?php if (!empty($result['comment'])): ?>
      <tr><td>Comentariu</td><td><?= htmlspecialchars($result['comment']) ?></td></tr>
      <?php endif; ?>
    </table>
    <a href="quiz.php" class="btn-link" style="display:inline-block;">← Completează din nou</a>
    &nbsp;
    <a href="../HTML/Quiz.html" class="btn-link" style="display:inline-block;">🧅 Fă Quizul</a>
  </div>

  <?php else: ?>
  <!-- ══ FORMULAR ══ -->
  <div id="contact-form">
    <h2 class="form-title">Date personale</h2>
    <p class="form-subtitle">Câmpurile marcate cu <span style="color:#c8f060">*</span> sunt obligatorii.</p>

    <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $err): ?>
        <p>⚠ <?= htmlspecialchars($err) ?></p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="quiz.php" id="shrek-form" novalidate>

      <!-- NUME -->
      <div class="field-group">
        <label for="name">Nume complet <span class="required">*</span></label>
        <input type="text" id="name" name="name" placeholder="Ex: Ion Popescu"
          maxlength="50" value="<?= htmlspecialchars($formData['name'] ?? '') ?>">
        <div class="field-error" id="err-name">Introduceți un nume valid (2–50 caractere).</div>
      </div>

      <!-- EMAIL -->
      <div class="field-group">
        <label for="email">Adresă de email <span class="required">*</span></label>
        <input type="email" id="email" name="email" placeholder="exemplu@email.com"
          value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        <div class="field-error" id="err-email">Introduceți o adresă de email validă.</div>
      </div>

      <!-- VÂRSTA -->
      <div class="field-group">
        <label for="age">Vârstă <span class="required">*</span></label>
        <input type="number" id="age" name="age" placeholder="Ex: 22" min="5" max="120"
          value="<?= htmlspecialchars($formData['age'] ?? '') ?>">
        <div class="field-error" id="err-age">Vârsta trebuie să fie între 5 și 120.</div>
      </div>

      <!-- PERSONAJ -->
      <div class="field-group">
        <label>Personajul tău preferat <span class="required">*</span></label>
        <div class="radio-group">
          <?php
          $chars = [
            'Shrek'           => '🧅 Shrek — ogorul verde și autentic',
            'Măgarul'         => '🫏 Măgarul — optimistul irezistibil',
            'Fiona'           => '👸 Fiona — puternică și independentă',
            'Pisica în Cizme' => '🐱 Pisica în Cizme — fermecătorul aventurier',
            'Lord Farquaad'   => '👑 Lord Farquaad — ambițiosul perfecționist',
          ];
          foreach ($chars as $val => $label):
            $checked = (($formData['character'] ?? '') === $val) ? 'checked' : '';
            $cls     = $checked ? ' checked-opt' : '';
          ?>
          <label class="radio-option<?= $cls ?>">
            <input type="radio" name="character" value="<?= htmlspecialchars($val) ?>" <?= $checked ?>>
            <span><?= htmlspecialchars($label) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="field-error" id="err-character">Selectați un personaj preferat.</div>
      </div>

      <!-- FILM -->
      <div class="field-group">
        <label for="film">Film favorit</label>
        <select id="film" name="film">
          <option value="">— Selectează un film —</option>
          <?php
          $films = ['Shrek (2001)', 'Shrek 2 (2004)', 'Shrek al Treilea (2007)', 'Shrek Forever After (2010)'];
          foreach ($films as $f):
            $sel = (($formData['film'] ?? '') === $f) ? 'selected' : '';
          ?>
          <option value="<?= htmlspecialchars($f) ?>" <?= $sel ?>><?= htmlspecialchars($f) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="hint">Opțional.</p>
      </div>

      <!-- COMENTARIU -->
      <div class="field-group">
        <label for="comment">Comentariu</label>
        <textarea id="comment" name="comment" maxlength="300"
          placeholder="Scrie un mesaj sau o amintire din film..."><?= htmlspecialchars($formData['comment'] ?? '') ?></textarea>
        <div class="char-counter"><span id="char-count">0</span> / 300</div>
        <p class="hint">Opțional — maxim 300 de caractere.</p>
      </div>

      <button type="submit" id="btn-submit-form">🐉 Trimite datele</button>

    </form>
  </div>
  <?php endif; ?>

  <!-- ══ LOG FISIER ══ -->
  <?php if (!empty($recentEntries)): ?>
  <div id="log-section">
    <h3>📋 Ultimele înregistrări salvate în quiz_results.txt</h3>
    <?php foreach ($recentEntries as $entry): ?>
      <div class="log-entry"><?= htmlspecialchars($entry) ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div id="page-nav" style="margin-top:36px;">
    <a href="../index.html" class="btn-link">← Pagina principală</a>
    <a href="../HTML/Quiz.html" class="btn-link">🧅 Quizul interactiv</a>
  </div>

</div>
</div>

<div id="footer">
  <p>Realizat: Cozlovschi Ana &nbsp;|&nbsp; Lucrarea de Laborator Nr. 4 — CGI</p>
</div>

<script>
(function () {
  'use strict';

  // Particles
  const canvas = document.createElement('canvas');
  canvas.id = 'bg-particles';
  Object.assign(canvas.style, { position:'fixed', top:'0', left:'0', width:'100%', height:'100%', pointerEvents:'none', zIndex:'0', opacity:'.4' });
  document.body.prepend(canvas);
  const ctx = canvas.getContext('2d');
  function resizeC() { canvas.width = innerWidth; canvas.height = innerHeight; }
  resizeC(); window.addEventListener('resize', resizeC);
  const pts = Array.from({length:40}, () => ({
    x:Math.random()*innerWidth, y:Math.random()*innerHeight,
    r:Math.random()*2+0.6, vx:(Math.random()-.5)*.3, vy:(Math.random()-.5)*.3,
    a:Math.random(), da:(Math.random()-.5)*.012,
    hue:Math.random()>.55?'#8cc63f':'#c8f060'
  }));
  (function loop() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    pts.forEach(p => {
      p.x+=p.vx; p.y+=p.vy; p.a+=p.da;
      if(p.a<=0||p.a>=1)p.da*=-1;
      if(p.x<0)p.x=canvas.width; if(p.x>canvas.width)p.x=0;
      if(p.y<0)p.y=canvas.height; if(p.y>canvas.height)p.y=0;
      ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle=p.hue; ctx.globalAlpha=p.a*.6; ctx.fill();
    });
    ctx.globalAlpha=1; requestAnimationFrame(loop);
  })();

  // Typing effect
  const sub = document.querySelector('#header p');
  if (sub) {
    const original = sub.textContent.trim();
    sub.textContent = '';
    sub.style.borderRight = '2px solid #8cc63f';
    let i = 0;
    const t = setInterval(() => {
      sub.textContent += original[i++];
      if (i >= original.length) { clearInterval(t); setTimeout(()=>sub.style.borderRight='none', 1800); }
    }, 70);
  }

  // ══ VALIDARE JAVASCRIPT (Sarcina Suplimentară) ══
  function showErr(fieldId, errorId, show) {
    const field = document.getElementById(fieldId);
    const err   = document.getElementById(errorId);
    if (!field || !err) return !show;
    field.classList.toggle('invalid', show);
    err.classList.toggle('visible', show);
    return !show;
  }

  function validateName() {
    const val = document.getElementById('name').value.trim();
    return showErr('name', 'err-name', val.length < 2 || val.length > 50);
  }
  function validateEmail() {
    const val = document.getElementById('email').value.trim();
    return showErr('email', 'err-email', !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val));
  }
  function validateAge() {
    const val = parseInt(document.getElementById('age').value, 10);
    return showErr('age', 'err-age', isNaN(val) || val < 5 || val > 120);
  }
  function validateCharacter() {
    const ok = Array.from(document.querySelectorAll('input[name="character"]')).some(r => r.checked);
    document.getElementById('err-character').classList.toggle('visible', !ok);
    return ok;
  }

  // Live validation pe blur
  const nameEl = document.getElementById('name');
  const emailEl = document.getElementById('email');
  const ageEl = document.getElementById('age');

  if (nameEl)  nameEl.addEventListener('blur', validateName);
  if (emailEl) emailEl.addEventListener('blur', validateEmail);
  if (ageEl)   ageEl.addEventListener('blur', validateAge);

  // Radio highlight
  document.querySelectorAll('input[name="character"]').forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.radio-option').forEach(o => o.classList.remove('checked-opt'));
      this.closest('.radio-option').classList.add('checked-opt');
      validateCharacter();
    });
  });

  // ══ CONTOR CARACTERE — fix pentru 0/300 ══
  const commentEl = document.getElementById('comment');
  const charCountEl = document.getElementById('char-count');

  if (commentEl && charCountEl) {
    // Seteaza valoarea initiala (in caz ca e prepopulat de PHP)
    charCountEl.textContent = commentEl.value.length;

    commentEl.addEventListener('input', function() {
      charCountEl.textContent = this.value.length;
      charCountEl.style.color = this.value.length > 260 ? '#ff9090' : '#6a9040';
    });
  }

  // Submit cu validare
  const form = document.getElementById('shrek-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      const n  = validateName();
      const em = validateEmail();
      const ag = validateAge();
      const ch = validateCharacter();

      if (!n || !em || !ag || !ch) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-form');
        btn.style.animation = 'none';
        void btn.offsetHeight;
        btn.style.animation = 'shake .4s ease';
        const firstInvalid = form.querySelector('.invalid, .field-error.visible');
        if (firstInvalid) firstInvalid.scrollIntoView({ behavior:'smooth', block:'center' });
      }
    });
  }

  // Ripple
  document.querySelectorAll('#nav ul li a, .btn-link').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const rect = this.getBoundingClientRect();
      const ripple = document.createElement('span');
      Object.assign(ripple.style, {
        position:'absolute', borderRadius:'50%', background:'rgba(140,198,63,.35)',
        transform:'scale(0)', animation:'rippleAnim .5s linear',
        width:'60px', height:'60px',
        left:(e.clientX-rect.left-30)+'px', top:(e.clientY-rect.top-30)+'px',
        pointerEvents:'none'
      });
      this.style.position='relative'; this.style.overflow='hidden';
      this.appendChild(ripple); setTimeout(()=>ripple.remove(), 550);
    });
  });

})();
</script>

</body>
</html>
