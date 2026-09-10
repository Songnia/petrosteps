<?php
session_start();

if (empty($_SESSION['landing_csrf'])) {
    $_SESSION['landing_csrf'] = bin2hex(random_bytes(24));
}

$form_status = '';
$form_message = '';
$form_values = array(
    'name' => '',
    'organisation' => '',
    'email' => '',
    'country' => '',
    'need' => 'Demonstration',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['landing_action']) && $_POST['landing_action'] === 'request_demo') {
    foreach ($form_values as $key => $default) {
        $form_values[$key] = isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }

    $csrf_is_valid = isset($_POST['csrf_token']) && hash_equals($_SESSION['landing_csrf'], (string)$_POST['csrf_token']);
    $honeypot_is_empty = empty($_POST['company_website']);
    $email_is_valid = filter_var($form_values['email'], FILTER_VALIDATE_EMAIL);

    if (!$csrf_is_valid || !$honeypot_is_empty) {
        $form_status = 'error';
        $form_message = 'La demande ne peut pas être envoyée. Rechargez la page et réessayez.';
    } elseif ($form_values['name'] === '' || $form_values['organisation'] === '' || !$email_is_valid) {
        $form_status = 'error';
        $form_message = 'Renseignez votre nom, votre organisation et une adresse e-mail valide.';
    } else {
        $storage_dir = __DIR__.'/storage';
        $storage_file = $storage_dir.'/demo-requests.csv';
        if (!is_dir($storage_dir)) {
            mkdir($storage_dir, 0750, true);
        }

        $safe_csv = function ($value) {
            $value = preg_replace('/[\r\n]+/', ' ', (string)$value);
            if (preg_match('/^[=+\-@]/', $value)) {
                $value = "'".$value;
            }
            return $value;
        };

        $is_new_file = !file_exists($storage_file) || filesize($storage_file) === 0;
        $handle = fopen($storage_file, 'a');
        $saved = false;
        if ($handle && flock($handle, LOCK_EX)) {
            if ($is_new_file) {
                fputcsv($handle, array('date', 'name', 'organisation', 'email', 'country', 'need'));
            }
            $saved = fputcsv($handle, array(
                date('c'),
                $safe_csv($form_values['name']),
                $safe_csv($form_values['organisation']),
                $safe_csv($form_values['email']),
                $safe_csv($form_values['country']),
                $safe_csv($form_values['need']),
            )) !== false;
            flock($handle, LOCK_UN);
        }
        if ($handle) {
            fclose($handle);
        }

        if ($saved) {
            $form_status = 'success';
            $form_message = 'Votre demande est enregistrée. Notre équipe vous contactera bientôt.';
            foreach ($form_values as $key => $value) {
                $form_values[$key] = $key === 'need' ? 'Demonstration' : '';
            }
            $_SESSION['landing_csrf'] = bin2hex(random_bytes(24));
        } else {
            $form_status = 'error';
            $form_message = 'La demande ne peut pas être enregistrée maintenant. Réessayez plus tard.';
        }
    }
}

function landing_escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Petrosteps est un simulateur pédagogique du cycle de vie complet d'un projet pétrolier.">
  <title>Petrosteps | Simulateur pédagogique pétrolier</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/landing.css?v=<?php echo filemtime(__DIR__.'/css/landing.css'); ?>">
</head>
<body>
  <a class="skip-link" href="#main-content">Aller au contenu</a>

  <header class="site-header" data-header>
    <div class="shell site-header__inner">
      <a class="wordmark" href="#top" aria-label="Petrosteps, accueil">Petro<span>$</span>teps</a>
      <nav class="desktop-nav" aria-label="Navigation principale">
        <a href="#parcours">Le parcours</a>
        <a href="#apprentissage">Apprentissage</a>
        <a href="#formateur">Pour le formateur</a>
      </nav>
      <div class="header-actions">
        <a class="text-link" href="login.php">Se connecter</a>
        <a class="button button--small" href="#demo">Demander une démo</a>
      </div>
      <button class="menu-button" type="button" aria-label="Ouvrir le menu" aria-expanded="false" data-menu-button>
        <span></span><span></span>
      </button>
    </div>
    <nav class="mobile-nav" aria-label="Navigation mobile" data-mobile-nav>
      <a href="#parcours">Le parcours</a>
      <a href="#apprentissage">Apprentissage</a>
      <a href="#formateur">Pour le formateur</a>
      <a href="login.php">Se connecter</a>
      <a href="#demo">Demander une démo</a>
    </nav>
  </header>

  <main id="main-content">
    <section class="hero" id="top">
      <div class="shell">
        <div class="lifecycle-ribbon" aria-label="Les huit étapes du projet">
          <span>Licence</span><span>Survey</span><span>Exploration</span><span>Appraisal</span>
          <span>Development</span><span>Production</span><span>Recovery</span><span>Abandonment</span>
        </div>
        <div class="hero__grid">
          <div class="hero__copy reveal">
            <p class="eyebrow">Simulateur de projet pétrolier</p>
            <h1>Pilotez un projet pétrolier.</h1>
            <p class="hero__lead">Petrosteps guide l'apprenant de la licence à l'abandon. Chaque décision change les coûts, la production et les revenus.</p>
            <div class="hero__actions">
              <a class="button" href="#demo">Demander une démonstration</a>
              <a class="button button--ghost" href="#parcours">Voir le parcours</a>
            </div>
          </div>
          <div class="hero__visual reveal" data-delay="1">
            <div class="hero-image-frame">
              <img src="img/avatars/brand-image.png" alt="Illustration d'un champ pétrolier suivi avec Petrosteps" width="944" height="1136">
            </div>
            <div class="hero-metric hero-metric--top">
              <span>Projet</span><strong>8 étapes</strong>
            </div>
            <div class="hero-metric hero-metric--bottom">
              <span>Décisions suivies</span><strong>Coûts + Production</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="problem section-pad">
      <div class="shell narrow reveal">
        <h2>Un projet pétrolier ne s'apprend pas étape par étape sur un tableau.</h2>
        <p class="section-intro">Il faut voir comment une décision technique agit sur tout le projet.</p>
      </div>
      <div class="shell problem-grid">
        <article class="problem-item reveal">
          <strong>01</strong><h3>Relier les étapes</h3><p>L'apprenant comprend ce qui vient avant et après chaque décision.</p>
        </article>
        <article class="problem-item reveal" data-delay="1">
          <strong>02</strong><h3>Voir les conséquences</h3><p>Les coûts, le débit et les revenus évoluent en temps réel.</p>
        </article>
        <article class="problem-item reveal" data-delay="2">
          <strong>03</strong><h3>Expliquer ses choix</h3><p>Le rapport final transforme la simulation en cas d'étude.</p>
        </article>
      </div>
    </section>

    <section class="platform section-pad" id="apprentissage">
      <div class="shell">
        <div class="section-heading reveal">
          <p class="eyebrow">Une seule plateforme</p>
          <h2>Apprendre avec des données claires.</h2>
          <p class="section-intro">L'apprenant agit. Petrosteps calcule. Le formateur explique.</p>
        </div>
        <figure class="platform-shot reveal">
          <a href="img/Une.seule.plateforme.png" target="_blank" rel="noopener" aria-label="Afficher l'interface Petrosteps en taille réelle">
            <img src="img/Une.seule.plateforme.png" alt="Interface Petrosteps montrant le graphe, les indicateurs du projet et les huit étapes" width="1897" height="952" loading="lazy">
          </a>
          <figcaption>Une vue complète du projet, de ses résultats et de son avancement.</figcaption>
        </figure>
      </div>
    </section>

    <section class="journey section-pad" id="parcours">
      <div class="shell">
        <div class="section-heading reveal">
          <h2>Huit étapes. Un seul projet.</h2>
          <p class="section-intro">Chaque étape pose une question simple et montre son impact.</p>
        </div>
        <div class="journey-list">
          <article class="journey-step reveal"><span>01</span><div><h3>Licence</h3><p>Quel bloc faut-il choisir ?</p></div><b>Choisir</b></article>
          <article class="journey-step reveal"><span>02</span><div><h3>Survey</h3><p>Quelles données faut-il acquérir ?</p></div><b>Observer</b></article>
          <article class="journey-step reveal"><span>03</span><div><h3>Exploration</h3><p>Où et comment forer ?</p></div><b>Tester</b></article>
          <article class="journey-step reveal"><span>04</span><div><h3>Appraisal</h3><p>La découverte est-elle suffisante ?</p></div><b>Évaluer</b></article>
          <article class="journey-step reveal"><span>05</span><div><h3>Development</h3><p>Comment préparer la production ?</p></div><b>Construire</b></article>
          <article class="journey-step reveal"><span>06</span><div><h3>Production</h3><p>Comment évoluent le débit et les revenus ?</p></div><b>Produire</b></article>
          <article class="journey-step reveal"><span>07</span><div><h3>Recovery</h3><p>Faut-il investir pour relancer le débit ?</p></div><b>Décider</b></article>
          <article class="journey-step reveal"><span>08</span><div><h3>Abandonment</h3><p>Comment fermer le projet proprement ?</p></div><b>Clôturer</b></article>
        </div>
      </div>
    </section>

    <section class="decision section-pad">
      <div class="shell decision__grid">
        <div class="decision__copy reveal">
          <h2>De la géologie au résultat financier.</h2>
          <p>Petrosteps relie les données du sous-sol, les opérations et les chiffres du projet.</p>
          <a class="button button--ghost" href="#demo">Voir une démonstration</a>
        </div>
        <div class="decision__media reveal" data-delay="1">
          <img src="img/Picture3.jpg" alt="Modèle géologique utilisé pendant l'exploration" width="749" height="314" loading="lazy">
          <div class="formula"><span>Volume récupérable × prix du pétrole</span><strong>= Revenu projeté</strong></div>
        </div>
      </div>
    </section>

    <section class="trainer section-pad" id="formateur">
      <div class="shell trainer__grid">
        <div class="trainer__image reveal">
          <img src="img/Picture5.jpg" alt="Installation de forage sur un champ pétrolier" width="800" height="467" loading="lazy">
        </div>
        <div class="trainer__copy reveal" data-delay="1">
          <h2>Suivre, questionner, expliquer.</h2>
          <div class="trainer-points">
            <p><b>Créer les participants.</b><span>Chaque apprenant reçoit son propre projet.</span></p>
            <p><b>Suivre la progression.</b><span>Le formateur voit les étapes déjà réalisées.</span></p>
            <p><b>Faire le débriefing.</b><span>Les données et le rapport soutiennent la discussion.</span></p>
          </div>
        </div>
      </div>
    </section>

    <section class="outcomes section-pad">
      <div class="shell">
        <div class="section-heading reveal">
          <h2>Un projet que l'apprenant peut défendre.</h2>
        </div>
        <div class="outcome-layout">
          <div class="report-sheet reveal">
            <div class="report-sheet__head"><span class="wordmark wordmark--small">Petro<span>$</span>teps</span><b>PROJECT REPORT</b></div>
            <h3>Executive summary</h3>
            <div class="report-numbers"><span><small>Production</small><b>20 years</b></span><span><small>Revenue</small><b>$2.2 B</b></span><span><small>Cash flow</small><b>$1.4 B</b></span></div>
            <div class="report-line"></div><div class="report-line report-line--short"></div>
          </div>
          <div class="outcome-copy reveal" data-delay="1">
            <p>Le rapport réunit les étapes, les coûts, la production et le cash flow.</p>
            <p>Il sert à évaluer les acquis et à discuter les choix.</p>
            <div class="audiences"><span>Universités</span><span>Centres de formation</span><span>Entreprises</span><span>Consultants</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="demo section-pad" id="demo">
      <div class="shell demo__grid">
        <div class="demo__copy reveal">
          <p class="eyebrow">Demander une démonstration</p>
          <h2>Voyez Petrosteps avec votre équipe.</h2>
          <p>Présentez-nous votre besoin. Nous vous montrerons le parcours adapté à votre formation.</p>
        </div>
        <form class="demo-form reveal" method="post" action="landing.php#demo" data-delay="1">
          <?php if ($form_message !== ''): ?>
            <div class="form-message form-message--<?php echo landing_escape($form_status); ?>" role="alert"><?php echo landing_escape($form_message); ?></div>
          <?php endif; ?>
          <div class="form-row">
            <label>Nom et prénom<input type="text" name="name" autocomplete="name" required value="<?php echo landing_escape($form_values['name']); ?>"></label>
            <label>Organisation<input type="text" name="organisation" autocomplete="organization" required value="<?php echo landing_escape($form_values['organisation']); ?>"></label>
          </div>
          <label>Adresse e-mail<input type="email" name="email" autocomplete="email" required value="<?php echo landing_escape($form_values['email']); ?>"></label>
          <div class="form-row">
            <label>Pays<input type="text" name="country" autocomplete="country-name" value="<?php echo landing_escape($form_values['country']); ?>"></label>
            <label>Votre besoin<select name="need"><option<?php echo $form_values['need'] === 'Demonstration' ? ' selected' : ''; ?>>Demonstration</option><option<?php echo $form_values['need'] === 'Formation' ? ' selected' : ''; ?>>Formation</option><option<?php echo $form_values['need'] === 'Partenariat' ? ' selected' : ''; ?>>Partenariat</option></select></label>
          </div>
          <label class="honeypot" aria-hidden="true">Site web<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
          <input type="hidden" name="csrf_token" value="<?php echo landing_escape($_SESSION['landing_csrf']); ?>">
          <input type="hidden" name="landing_action" value="request_demo">
          <button class="button" type="submit">Envoyer ma demande</button>
          <p class="form-note">Aucun engagement. Votre demande reste confidentielle.</p>
        </form>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="shell site-footer__top"><span class="wordmark wordmark--footer">Petro<span>$</span>teps</span><p>Le cycle pétrolier devient une expérience d'apprentissage.</p></div>
    <div class="shell site-footer__bottom"><span>© <?php echo date('Y'); ?> Consoltia Inc.</span><a href="login.php">Accéder à la plateforme</a></div>
  </footer>

  <script src="js/landing.js?v=<?php echo filemtime(__DIR__.'/js/landing.js'); ?>" defer></script>
</body>
</html>
