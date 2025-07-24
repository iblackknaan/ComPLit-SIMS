<?php
require_once 'admin_header.php';
?>

<!-- Main Content -->
<main class="container mt-4">
    <div class="welcome-section">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4">Welcome, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>!</h1>
                <p class="lead">Use the navigation bar to manage users and other administrative tasks.</p>
            </div>
        </div>
    </div>
    
    <?php include 'admin_dashboard_cards.php'; ?>

    
</main>

<?php include 'footer.php'; ?>