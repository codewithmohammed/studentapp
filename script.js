document.addEventListener("DOMContentLoaded", function() {

    // --- Get Modal Elements ---
    const modal = document.getElementById("auth-modal");
    const loginBtn = document.getElementById("login-btn");
    const closeBtn = document.querySelector(".close-btn");

    // --- Get Form Elements ---
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");

    // --- Get Toggle Links ---
    const showSignupBtn = document.getElementById("show-signup");
    const showLoginBtn = document.getElementById("show-login");

    // --- Modal Open/Close Functionality ---
    loginBtn.onclick = function() {
        modal.style.display = "block";
        loginForm.style.display = "block";
        signupForm.style.display = "none";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    closeBtn.onclick = closeModal;

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    // --- Form Toggle Functionality ---
    showSignupBtn.onclick = function(e) {
        e.preventDefault();
        loginForm.style.display = "none";
        signupForm.style.display = "block";
    }

    showLoginBtn.onclick = function(e) {
        e.preventDefault();
        loginForm.style.display = "block";
        signupForm.style.display = "none";
    }

    // ===============================================
    // --- NEW SERVER-SIDE SUBMISSION LOGIC ---
    // ===============================================

    // --- Handle LOGIN form submission ---
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault(); // Stop the form from refreshing the page

        // Create FormData object from the form
        const formData = new FormData(loginForm);

        // Send data to login.php
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Get the JSON response from PHP
        .then(data => {
            console.log(data); // Log the response for debugging
            alert(data.message); // Show the server's message

            if (data.status === 'success') {
             window.location.href = 'dashboard.php'; // Redirect to dashboard on successful login
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // --- Handle SIGN UP form submission ---
    signupForm.addEventListener("submit", function(event) {
        event.preventDefault(); 

        const formData = new FormData(signupForm);

        // Send data to signup.php
        fetch('signup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            alert(data.message);

            if (data.status === 'success') {
                // If sign up is successful, switch to the login form
                loginForm.style.display = "block";
                signupForm.style.display = "none";
                signupForm.reset(); // Clear the sign-up form
                loginForm.reset(); // Clear the login form
                
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});