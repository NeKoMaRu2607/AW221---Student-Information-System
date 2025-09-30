document.addEventListener('DOMContentLoaded', function () {
    // Form elements
    const signupForm = document.querySelector('.form.signup');
    const loginForm = document.querySelector('.form.login');
    const forgotForm = document.querySelector('.form.forgot');
    const signupLink = document.querySelector('.signup-link');
    const loginLinks = document.querySelectorAll('.login-link');
    const forgotLink = document.querySelector('.forgot-pass');
    const pwShowHide = document.querySelectorAll('.eye-icon');

    // Password Show/Hide Logic
    pwShowHide.forEach(eyeIcon => {
        eyeIcon.addEventListener("click", () => {
            let pwFields = eyeIcon.parentElement.parentElement.querySelectorAll(".password");
            pwFields.forEach(password => {
                if (password.type === "password") {
                    password.type = "text";
                    eyeIcon.classList.replace("bx-hide", "bx-show");
                } else {
                    password.type = "password";
                    eyeIcon.classList.replace("bx-show", "bx-hide");
                }
            });
        });
    });

    // Show Signup Form
    if (signupLink) {
        signupLink.addEventListener('click', e => {
            e.preventDefault(); // Prevent default link behavior
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            forgotForm.style.display = 'none';
        });
    }

    // Show Login Form (multiple links)
    if (loginLinks.length > 0) {
        loginLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault(); // Prevent default link behavior
                signupForm.style.display = 'none';
                loginForm.style.display = 'block';
                forgotForm.style.display = 'none';
            });
        });
    }

    // Show Forgot Password Form
    if (forgotLink) {
        forgotLink.addEventListener('click', e => {
            e.preventDefault(); // Prevent default link behavior
            loginForm.style.display = 'none';
            signupForm.style.display = 'none';
            forgotForm.style.display = 'block';
        });
    }
});