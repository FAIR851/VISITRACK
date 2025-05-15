// Show register form when clicking "Register" link
document.getElementById("showRegisterForm").addEventListener("click", function(event) {
    event.preventDefault();
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("registerForm").style.display = "block";
});

// Show login form when clicking "Login" link
document.getElementById("showLoginForm").addEventListener("click", function(event) {
    event.preventDefault();
    document.getElementById("registerForm").style.display = "none";
    document.getElementById("loginForm").style.display = "block";
});
//for eye cyclone shit
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const inputId = icon.getAttribute('data-target');
      const passwordInput = document.getElementById(inputId);
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';

      icon.classList.toggle('bx-show');
      icon.classList.toggle('bx-hide');
    });
  });