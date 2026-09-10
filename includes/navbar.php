<?php
if(!isset($active)) $active = '';

$access = array();
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

if($user_type == "Participant")
  $access = array('Project Dashboard', 'Parameters','Projects');
if($user_type == "SuperAdmin")
  $access = array('Dashboard', 'Admin Users', 'Participants', 'Trainers', 'Parameters','Projects', 'Masters');
if($user_type == "Admin")
  $access = array('Dashboard', 'Participants', 'Trainers', 'Parameters','Projects', 'Masters');
if($user_type == "Trainer")
  $access = array('Dashboard', 'Participants', 'Parameters','Projects');
?>
<header class="admin-topbar">
  <button type="button" class="admin-topbar__toggle" id="adminMenuToggle" aria-label="Toggle Navigation">
    <span class="material-symbols-outlined" id="adminMenuIcon">menu</span>
  </button>
  <div class="admin-topbar__brand">
    <span class="admin-topbar__title"><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span></span>
  </div>
  <div class="admin-topbar__user">
    <span class="material-symbols-outlined">account_circle</span>
    <span class="admin-topbar__username"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
  </div>
</header>

<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

<nav class="navbar-md3" id="sideNav">
  <div class="navbar-md3__brand">
    <div class="navbar-md3__brand-title">
      <span class="navbar-md3__brand-name"><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span></span>
    </div>
    <button type="button" class="admin-sidebar-close" id="adminSidebarClose" aria-label="Close menu">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <div class="navbar-md3__section">

<?php if(in_array("Dashboard", $access) || in_array("Project Dashboard", $access)) { ?>
    <a href="index.php" class="nav-rail-item <?php echo $active=='dashboard'?'active':''; ?>">
      <span class="material-symbols-outlined">dashboard</span>
      <span><?php echo in_array("Project Dashboard", $access) ? 'Project Dashboard' : 'Dashboard'; ?></span>
    </a>
<?php } ?>

<?php if(in_array("Admin Users", $access)) { ?>
    <a href="admin_user_list.php" class="nav-rail-item <?php echo $active=='admin_users'?'active':''; ?>">
      <span class="material-symbols-outlined">admin_panel_settings</span>
      <span>Admin Users</span>
    </a>
<?php } ?>

<?php if(in_array("Trainers", $access)) { ?>
    <a href="trainer_list.php" class="nav-rail-item <?php echo $active=='trainers'?'active':''; ?>">
      <span class="material-symbols-outlined">school</span>
      <span>Trainers</span>
    </a>
<?php } ?>

<?php if(in_array("Participants", $access)) { ?>
    <a href="participant_list.php" class="nav-rail-item <?php echo $active=='participants'?'active':''; ?>">
      <span class="material-symbols-outlined">group</span>
      <span>Participants</span>
    </a>
<?php } ?>

<?php if(in_array("Parameters", $access)) { ?>
    <a href="parameter_list.php" class="nav-rail-item <?php echo $active=='parameters'?'active':''; ?>">
      <span class="material-symbols-outlined">tune</span>
      <span>Parameters</span>
    </a>
<?php } ?>

<?php if(in_array("Projects", $access)) { ?>
    <a href="ongoing_project_list.php" class="nav-rail-item <?php echo $active=='projects'?'active':''; ?>">
      <span class="material-symbols-outlined">folder_open</span>
      <span>Projects</span>
    </a>
<?php } ?>

<?php if(in_array("Masters", $access)) { ?>
    <div class="nav-rail-item nav-rail-item--parent" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
      <span class="material-symbols-outlined">settings</span>
      <span>Masters</span>
      <span class="material-symbols-outlined nav-rail-item__chevron">expand_more</span>
    </div>
    <div class="nav-rail-submenu">
      <a href="well_list.php" class="nav-rail-submenu__item <?php echo $active=='wells'?'active':''; ?>">
        <span class="material-symbols-outlined" style="font-size:18px;">water_well</span>
        <span>Wells</span>
      </a>
      <a href="field_list.php" class="nav-rail-submenu__item <?php echo $active=='fields'?'active':''; ?>">
        <span class="material-symbols-outlined" style="font-size:18px;">terrain</span>
        <span>Fields</span>
      </a>
      <a href="block_list.php" class="nav-rail-submenu__item <?php echo $active=='blocks'?'active':''; ?>">
        <span class="material-symbols-outlined" style="font-size:18px;">layers</span>
        <span>Blocks</span>
      </a>
      <a href="production_facility_list.php" class="nav-rail-submenu__item <?php echo $active=='pfacilities'?'active':''; ?>">
        <span class="material-symbols-outlined" style="font-size:18px;">factory</span>
        <span>Prod. Facilities</span>
      </a>
      <a href="country_list.php" class="nav-rail-submenu__item <?php echo $active=='countries'?'active':''; ?>">
        <span class="material-symbols-outlined" style="font-size:18px;">public</span>
        <span>Countries</span>
      </a>
    </div>
<?php } ?>

  </div>

  <div class="navbar-md3__footer">
    <div class="nav-user-chip">
      <span class="material-symbols-outlined">account_circle</span>
      <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
    </div>
    <a href="logout.php" class="nav-rail-item">
      <span class="material-symbols-outlined">logout</span>
      <span>Logout</span>
    </a>
  </div>
</nav>

<script>
(function() {
  function initAdminNav() {
    var toggleBtn = document.getElementById('adminMenuToggle');
    var closeBtn = document.getElementById('adminSidebarClose');
    var sideNav = document.getElementById('sideNav');
    var overlay = document.getElementById('adminSidebarOverlay');
    var icon = document.getElementById('adminMenuIcon');

    if (!sideNav) return;

    function openMenu() {
      sideNav.classList.add('navbar-md3--open');
      if (overlay) overlay.classList.add('admin-sidebar-overlay--active');
      document.documentElement.classList.add('admin-menu-open');
      document.body.classList.add('admin-menu-open');
      if (icon) icon.textContent = 'close';
    }

    function closeMenu() {
      sideNav.classList.remove('navbar-md3--open');
      if (overlay) overlay.classList.remove('admin-sidebar-overlay--active');
      document.documentElement.classList.remove('admin-menu-open');
      document.body.classList.remove('admin-menu-open');
      if (icon) icon.textContent = 'menu';
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (sideNav.classList.contains('navbar-md3--open')) {
          closeMenu();
        } else {
          openMenu();
        }
      });
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeMenu);
    }

    if (overlay) {
      overlay.addEventListener('click', closeMenu);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && sideNav.classList.contains('navbar-md3--open')) {
        closeMenu();
      }
    });

    var navItems = sideNav.querySelectorAll('a.nav-rail-item, a.nav-rail-submenu__item');
    navItems.forEach(function(item) {
      item.addEventListener('click', function() {
        if (window.innerWidth <= 991) {
          closeMenu();
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminNav);
  } else {
    initAdminNav();
  }
})();
</script>

<div class="main-content-wrapper" id="mainContent">
