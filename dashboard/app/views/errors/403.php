<?php
$title = 'Access Denied';
$page = 'error';
ob_start();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-body text-center">
                    <h1 class="display-1 text-danger">403</h1>
                    <h2 class="mb-4">Access Denied</h2>
                    <p class="lead">You don't have permission to access this resource.</p>
                    <div class="mt-4">
                        <a href="/WEB2Finals/dashboard/dashboard" class="btn btn-primary">
                            <i class="bi bi-house-door"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
