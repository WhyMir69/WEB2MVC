<?php
$title = 'Page Not Found';
ob_start();
?>

<div class="text-center mt-5">
    <h1 class="display-1">404</h1>
    <p class="lead">Page Not Found</p>
    <p>The page you're looking for doesn't exist or has been moved.</p>
    <a href="/WEB2Finals/dashboard/dashboard" class="btn btn-primary">Back to Dashboard</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
