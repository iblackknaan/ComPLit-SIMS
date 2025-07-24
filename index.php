<?php
session_start([
    'cookie_secure' => true,  // Enable when using HTTPS
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_samesite' => 'Strict'
]);
// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">
    <style>
        body {
            background: linear-gradient(-45deg, #f8f9fa, #e9ecef, #dee2e6, #ced4da);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100vh;
        }
        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .container { max-width: 400px; margin-top: 10vh; }
        .card { padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); }
        .password-container { position: relative; }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 5;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card bg-white">
        <div class="card-body">
            <h3 class="card-title text-center mb-4 text-primary">
                <i class="bi bi-book me-2"></i>School Login
            </h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars(urldecode($_GET['error'])) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="login.php" method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group password-container">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                        <span class="toggle-password" id="togglePassword"><i class="bi bi-eye-slash"></i></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="usertype" class="form-label">User Type</label>
                    <select class="form-select" id="usertype" name="usertype" required>
                        <option value="" disabled selected>Select user type</option>
                        <option value="admin">Administrator</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <span id="loginText">Login</span>
                        <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>

            <div class="mt-3 text-center">
                <a href="#forgot-password" class="text-decoration-none">Forgot password?</a>
            </div>

            <div id="alertBox" class="mt-3"></div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginText = document.getElementById('loginText');
    const loginSpinner = document.getElementById('loginSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const alertBox = document.getElementById('alertBox');

    // Toggle password visibility
    togglePassword.addEventListener('click', () => {
        const icon = togglePassword.querySelector('i');
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('bi-eye', isHidden);
        icon.classList.toggle('bi-eye-slash', !isHidden);
    });

    // Handle form submission
    form.addEventListener('submit', e => {
        e.preventDefault();
        if (!form.username.value.trim() || !form.password.value || !form.usertype.value) {
            showAlert('Please fill in all fields', 'danger');
            return;
        }

        loginText.classList.add('d-none');
        loginSpinner.classList.remove('d-none');
        loginBtn.disabled = true;

        setTimeout(() => form.submit(), 1000); // Simulate delay
    });

    function showAlert(message, type) {
        alertBox.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
});
</script>
</body>
</html>
