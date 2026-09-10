<?php
require_once __DIR__.'/project_step_helpers.php';

$participant_user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Participant';
$participant_current_file = basename(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH));
$participant_has_project = isset($_SESSION['project']['project_id']) && (int) $_SESSION['project']['project_id'] > 0;
$participant_step_labels = array(
	1 => 'LICENSE',
	2 => 'SURVEY',
	3 => 'EXPLORATION',
	4 => 'APPRAISAL',
	5 => 'DEVELOPMENT',
	6 => 'PRODUCTION',
	7 => '2ND RECOVERY',
	8 => 'ABANDON',
);
$participant_current_step = $participant_has_project ? project_get_current_step($_SESSION['project']) : 0;
$participant_current_step_label = ($participant_current_step && isset($participant_step_labels[$participant_current_step]))
	? $participant_step_labels[$participant_current_step]
	: 'DASHBOARD';
$participant_project_name = ($participant_has_project && !empty($_SESSION['project']['Project_Name']))
	? $_SESSION['project']['Project_Name']
	: 'Participant workspace';
$participant_nav_title = $participant_has_project ? $participant_project_name : 'My Projects';
$participant_nav_subtitle = $participant_has_project
	? 'Step '.$participant_current_step.' - '.$participant_current_step_label
	: 'Petrosteps simulation';
$participant_project_year = ($participant_has_project && isset($_SESSION['project']['Project_year']))
	? (int) $_SESSION['project']['Project_year']
	: null;
$participant_production_year = ($participant_has_project && isset($_SESSION['project']['Production_year']))
	? (int) $_SESSION['project']['Production_year']
	: null;
$participant_user_email = !empty($_SESSION['email'])
	? $_SESSION['email']
	: strtolower(preg_replace('/[^a-z0-9]+/i', '', $participant_user_name)).'@petrosteps.app';
$participant_user_initial = function_exists('mb_substr')
	? mb_strtoupper(mb_substr($participant_user_name, 0, 1))
	: strtoupper(substr($participant_user_name, 0, 1));
$participant_is_projects_page = in_array($participant_current_file, array('index.php', 'index.php', 'project_add.php'), true);
$participant_current_step_url = $participant_has_project
	? project_get_step_url($participant_current_step)
	: 'index.php';
$participant_top_shortcut_href = $participant_has_project ? 'report.php' : 'index.php';
$participant_top_shortcut_label = $participant_has_project ? 'Open report' : 'Open dashboard';
$participant_top_shortcut_icon = $participant_has_project ? 'description' : 'dashboard';
$participant_top_shortcut_target = $participant_has_project ? '_blank' : '';
$participant_nav_items = array(
	array(
		'label' => 'My Projects',
		'icon' => 'folder_open',
		'href' => 'index.php',
		'active' => $participant_is_projects_page,
	),
);

if ($participant_has_project) {
	$participant_nav_items[] = array(
		'label' => 'Current Step',
		'icon' => 'lan',
		'href' => $participant_current_step_url,
		'active' => !$participant_is_projects_page,
	);
	$participant_nav_items[] = array(
		'label' => 'Report',
		'icon' => 'description',
		'href' => 'report.php',
		'active' => false,
		'target' => '_blank',
	);
}
?>

<header class="participant-topbar" role="banner">
  <div class="participant-topbar__left">
    <button
      type="button"
      class="participant-menu-toggle"
      data-participant-menu-toggle
      data-menu-icon-open="menu"
      data-menu-icon-close="close"
      aria-controls="participantSidebarShell"
      aria-expanded="false"
      aria-label="Open menu"
    >
      <span class="material-symbols-outlined">menu</span>
    </button>

    <div class="participant-topbar__heading">
      <span class="participant-topbar__eyebrow"><?php echo htmlspecialchars($participant_nav_subtitle); ?></span>
      <span class="participant-topbar__title"><?php echo htmlspecialchars($participant_nav_title); ?></span>
    </div>
  </div>

  <div class="participant-topbar__right">
    <?php if($participant_has_project): ?>
      <div class="participant-topbar__metrics">
        <span class="participant-top-pill participant-top-pill--accent">Step <?php echo $participant_current_step; ?></span>
        <?php if($participant_project_year !== null && $participant_project_year > 0): ?>
          <span class="participant-top-pill participant-top-pill--optional">Project year <?php echo $participant_project_year; ?></span>
        <?php endif; ?>
        <?php if($participant_production_year !== null && $participant_production_year > 0): ?>
          <span class="participant-top-pill participant-top-pill--optional">Production <?php echo $participant_production_year; ?></span>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <a
      href="<?php echo htmlspecialchars($participant_top_shortcut_href); ?>"
      class="participant-topbar__shortcut"
      aria-label="<?php echo htmlspecialchars($participant_top_shortcut_label); ?>"
      <?php if(!empty($participant_top_shortcut_target)): ?>target="<?php echo htmlspecialchars($participant_top_shortcut_target); ?>"<?php endif; ?>
    >
      <span class="material-symbols-outlined"><?php echo htmlspecialchars($participant_top_shortcut_icon); ?></span>
    </a>

    <div class="participant-user-chip">
      <div class="participant-user-chip__text">
        <strong><?php echo htmlspecialchars($participant_user_name); ?></strong>
        <span><?php echo htmlspecialchars($participant_user_email); ?></span>
      </div>
      <span class="participant-user-chip__avatar"><?php echo htmlspecialchars($participant_user_initial); ?></span>
    </div>
  </div>
</header>

<button
  type="button"
  class="participant-sidebar-overlay"
  data-participant-menu-overlay
  aria-label="Close navigation menu"
></button>

<div class="participant-sidebar-shell" id="participantSidebarShell">
  <aside class="participant-sidebar-rail" aria-label="Quick actions">
    <button
      type="button"
      class="participant-sidebar-rail__brand"
      data-participant-shell-expand
      aria-controls="participantSidebarShell"
      aria-expanded="false"
      aria-label="Open sidebar"
    >
      <span class="material-symbols-outlined participant-shell-toggle-icon">chevron_left</span>
    </button>

    <div class="participant-sidebar-rail__items">
      <?php foreach($participant_nav_items as $participant_nav_item): ?>
        <a
          href="<?php echo htmlspecialchars($participant_nav_item['href']); ?>"
          class="participant-rail-item<?php echo !empty($participant_nav_item['active']) ? ' active' : ''; ?>"
          title="<?php echo htmlspecialchars($participant_nav_item['label']); ?>"
          <?php if(!empty($participant_nav_item['target'])): ?>target="<?php echo htmlspecialchars($participant_nav_item['target']); ?>"<?php endif; ?>
        >
          <span class="material-symbols-outlined"><?php echo htmlspecialchars($participant_nav_item['icon']); ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="participant-sidebar-rail__footer">
      <a href="logout.php" class="participant-rail-item participant-rail-item--danger" title="Logout">
        <span class="material-symbols-outlined">logout</span>
      </a>
      <span class="participant-sidebar-rail__avatar"><?php echo htmlspecialchars($participant_user_initial); ?></span>
    </div>
  </aside>

  <aside class="user-sidebar">
    <div class="user-sidebar__brand">
      <div class="user-sidebar__brand-copy">
        <button
          type="button"
          class="user-sidebar__brand-mark"
          data-participant-shell-collapse
          aria-controls="participantSidebarShell"
          aria-expanded="true"
          aria-label="Collapse sidebar"
        >
          <span class="material-symbols-outlined participant-shell-toggle-icon">chevron_left</span>
        </button>
        <div>
          <span class="user-sidebar__brand-kicker">Participant shell</span>
          <span class="user-sidebar__brand-name"><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span></span>
        </div>
      </div>
      <button
        type="button"
        class="participant-sidebar-close"
        data-participant-menu-toggle
        data-menu-icon-open="chevron_left"
        data-menu-icon-close="close"
        aria-controls="participantSidebarShell"
        aria-expanded="false"
        aria-label="Close menu"
      >
        <span class="material-symbols-outlined">chevron_left</span>
      </button>
    </div>

    <!--<div class="user-sidebar__hero">
      <span class="user-sidebar__hero-eyebrow">Workspace</span>
      <h2><?php echo htmlspecialchars($participant_nav_title); ?></h2>
      <p><?php echo htmlspecialchars($participant_nav_subtitle); ?></p>
      <a href="<?php echo htmlspecialchars($participant_current_step_url); ?>" class="user-sidebar__cta">
        <span class="material-symbols-outlined"><?php echo $participant_has_project ? 'play_arrow' : 'dashboard'; ?></span>
        <?php echo $participant_has_project ? 'Open current step' : 'Open dashboard'; ?>
      </a>
    </div>-->

    <nav class="user-sidebar__nav" aria-label="Participant navigation">
      <?php foreach($participant_nav_items as $participant_nav_item): ?>
        <a
          href="<?php echo htmlspecialchars($participant_nav_item['href']); ?>"
          class="user-nav-item<?php echo !empty($participant_nav_item['active']) ? ' active' : ''; ?>"
          <?php if(!empty($participant_nav_item['target'])): ?>target="<?php echo htmlspecialchars($participant_nav_item['target']); ?>"<?php endif; ?>
        >
          <span class="material-symbols-outlined"><?php echo htmlspecialchars($participant_nav_item['icon']); ?></span>
          <span><?php echo htmlspecialchars($participant_nav_item['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <?php if($participant_has_project): ?>
      <div class="user-sidebar__meta-card">
        <div class="user-sidebar__meta-card-header">
          <span>Project status</span>
          <span class="participant-top-pill participant-top-pill--accent">Step <?php echo $participant_current_step; ?></span>
        </div>
        <div class="user-sidebar__meta-grid">
          <div class="user-sidebar__meta-cell">
            <span class="user-sidebar__meta-label">Project year</span>
            <strong><?php echo $participant_project_year !== null ? $participant_project_year : 0; ?></strong>
          </div>
          <div class="user-sidebar__meta-cell">
            <span class="user-sidebar__meta-label">Production</span>
            <strong><?php echo $participant_production_year !== null ? $participant_production_year : 0; ?></strong>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="user-sidebar__meta-card">
        <div class="user-sidebar__meta-card-header">
          <span>Project status</span>
        </div>
        <p class="user-sidebar__empty-copy">Create or reopen a project to see the current step, years, and report shortcuts here.</p>
      </div>
    <?php endif; ?>

    <div class="user-sidebar__footer">
      <div class="user-avatar-chip">
        <span class="user-avatar-chip__avatar"><?php echo htmlspecialchars($participant_user_initial); ?></span>
        <div class="user-avatar-chip__text">
          <strong><?php echo htmlspecialchars($participant_user_name); ?></strong>
          <span><?php echo htmlspecialchars($participant_user_email); ?></span>
        </div>
      </div>
      <a href="logout.php" class="user-nav-item user-nav-item--ghost">
        <span class="material-symbols-outlined">logout</span>
        <span>Logout</span>
      </a>
    </div>
  </aside>
</div>

<script>
(function () {
  var body = document.body;
  var sidebar = document.getElementById("participantSidebarShell");
  var overlay = document.querySelector("[data-participant-menu-overlay]");
  var toggles = document.querySelectorAll("[data-participant-menu-toggle]");
  var collapseToggle = document.querySelector("[data-participant-shell-collapse]");
  var expandToggles = document.querySelectorAll("[data-participant-shell-expand]");
  var desktopSidebarCollapsed = false;

  if (!body || !sidebar || !overlay || !toggles.length) {
    return;
  }

  body.classList.add("participant-shell");

  function isDesktopSidebarMode() {
    return window.innerWidth > 991;
  }

  function syncDesktopSidebarState() {
    var isCollapsed = isDesktopSidebarMode() && desktopSidebarCollapsed;

    body.classList.toggle("participant-shell-collapsed", isCollapsed);

    if (collapseToggle) {
      collapseToggle.setAttribute("aria-expanded", isCollapsed ? "false" : "true");
      collapseToggle.setAttribute("aria-label", isCollapsed ? "Open sidebar" : "Collapse sidebar");
    }

    expandToggles.forEach(function (toggle) {
      toggle.setAttribute("aria-expanded", isCollapsed ? "false" : "true");
      toggle.setAttribute("aria-label", isCollapsed ? "Open sidebar" : "Sidebar open");
    });
  }

  function setMenuState(isOpen) {
    body.classList.toggle("participant-menu-open", isOpen);

    toggles.forEach(function (toggle) {
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
      toggle.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");

      var icon = toggle.querySelector(".material-symbols-outlined");
      var openIcon = toggle.getAttribute("data-menu-icon-open") || "menu";
      var closeIcon = toggle.getAttribute("data-menu-icon-close") || "close";

      if (icon) {
        icon.textContent = isOpen ? closeIcon : openIcon;
      }
    });
  }

  toggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      setMenuState(!body.classList.contains("participant-menu-open"));
    });
  });

  overlay.addEventListener("click", function () {
    setMenuState(false);
  });

  if (collapseToggle) {
    collapseToggle.addEventListener("click", function () {
      if (!isDesktopSidebarMode()) {
        return;
      }

      desktopSidebarCollapsed = true;
      syncDesktopSidebarState();
    });
  }

  expandToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      if (!isDesktopSidebarMode()) {
        return;
      }

      desktopSidebarCollapsed = false;
      syncDesktopSidebarState();
    });
  });

  sidebar.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
      if (window.innerWidth <= 991) {
        setMenuState(false);
      }
    });
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      setMenuState(false);
    }
  });

  window.addEventListener("resize", function () {
    if (window.innerWidth > 991) {
      setMenuState(false);
    }

    syncDesktopSidebarState();
  });

  syncDesktopSidebarState();
  setMenuState(false);
})();
</script>
