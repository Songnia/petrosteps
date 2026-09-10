(function () {
  function togglePasswordVisibility(event) {
    var button = event.currentTarget;
    var input = document.getElementById(button.getAttribute('data-password-target'));
    var icon = button.querySelector('.material-symbols-outlined');

    if (!input) return;

    var isVisible = input.type === 'text';
    input.type = isVisible ? 'password' : 'text';
    button.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
    button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
    if (icon) icon.textContent = isVisible ? 'visibility' : 'visibility_off';
  }

  var buttons = document.querySelectorAll('.md3-password-toggle');
  for (var index = 0; index < buttons.length; index += 1) {
    buttons[index].addEventListener('click', togglePasswordVisibility);
  }
}());
