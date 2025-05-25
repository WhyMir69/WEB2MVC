<?php
$title = 'Server Error';
ob_start();
?>

<div class="text-center mt-5">
    <h1 class="display-1">500</h1>
    <p class="lead">Server Error</p>
    <p>Something went wrong on our end. Please try again later.</p>
    <a href="/WEB2Finals/dashboard/dashboard" class="btn btn-primary">Back to Dashboard</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
