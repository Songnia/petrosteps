</div> <!-- /.main-content-wrapper -->

<footer class="md3-footer">
  <div class="md3-footer__inner">
    <span><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span> &copy; Ver 2.0</span>
    <span>Consoltia Inc 2015</span>
  </div>
</footer>

<!-- MD3 DIALOG -->
<div id="md3-dialog-overlay" class="md3-dialog-overlay" style="display:none;"></div>
<div id="md3-dialog" class="md3-dialog" style="display:none;">
  <div class="md3-dialog__content">
    <div class="md3-dialog__icon">
      <span class="material-symbols-outlined" id="md3-dialog-icon">info</span>
    </div>
    <p class="md3-dialog__message" id="md3-dialog-message"></p>
    <div class="md3-dialog__actions" id="md3-dialog-actions"></div>
  </div>
</div>

<script>
var md3_callback = null;

function md3_alert(msg, icon) {
  icon = icon || 'info';
  document.getElementById('md3-dialog-message').textContent = msg;
  document.getElementById('md3-dialog-icon').textContent = icon;
  document.getElementById('md3-dialog-actions').innerHTML =
    '<button onclick="md3_closeDialog();" class="md3-btn md3-btn--filled">OK</button>';
  document.getElementById('md3-dialog').className = 'md3-dialog md3-dialog--alert';
  document.getElementById('md3-dialog-overlay').style.display = '';
  document.getElementById('md3-dialog').style.display = '';
}

function md3_confirm(msg, cb, icon) {
  icon = icon || 'help';
  md3_callback = cb;
  document.getElementById('md3-dialog-message').textContent = msg;
  document.getElementById('md3-dialog-icon').textContent = icon;
  document.getElementById('md3-dialog-actions').innerHTML =
    '<button onclick="md3_cancelDialog();" class="md3-btn md3-btn--outline">Cancel</button>' +
    '<button onclick="md3_execConfirm();" class="md3-btn md3-btn--filled">Confirm</button>';
  document.getElementById('md3-dialog').className = 'md3-dialog md3-dialog--confirm';
  document.getElementById('md3-dialog-overlay').style.display = '';
  document.getElementById('md3-dialog').style.display = '';
}

function md3_closeDialog() {
  document.getElementById('md3-dialog-overlay').style.display = 'none';
  document.getElementById('md3-dialog').style.display = 'none';
  md3_callback = null;
}

function md3_cancelDialog() {
  md3_callback = null;
  md3_closeDialog();
}

function md3_execConfirm() {
  var cb = md3_callback;
  md3_callback = null;
  md3_closeDialog();
  if (typeof cb === 'function') cb();
}

function toggleActionMenu(trigger) {
  var dropdown = trigger.nextElementSibling;
  var isOpen = dropdown.classList.contains('md3-action-dropdown--open');
  closeAllActionMenus();
  if (!isOpen) {
    dropdown.classList.add('md3-action-dropdown--open');
    var rect = dropdown.getBoundingClientRect();
    if (rect.bottom > window.innerHeight - 50) {
      dropdown.classList.add('md3-action-dropdown--up');
    }
  }
}

function closeAllActionMenus() {
  document.querySelectorAll('.md3-action-dropdown--open').forEach(function(m) {
    m.classList.remove('md3-action-dropdown--open', 'md3-action-dropdown--up');
  });
}

document.addEventListener('click', function(e) {
  if (!e.target.closest('.md3-action-menu')) {
    closeAllActionMenus();
  }
});
</script>

<?php
	unset($_SESSION['message']);
	unset($_SESSION['message_type']);
?>
</body>
</html>
