<?php
$title = 'Dashboard';
$page = 'dashboard';
ob_start();
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <h1 class="card-title">
                    Welcome, <?= htmlspecialchars($user['username']) ?>!
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'instructor' ? 'success' : 'secondary') ?>">
                        <?= htmlspecialchars(ucfirst($user['role'] ?? 'staff')) ?>
                    </span>
                </h1>
                <p class="card-text">Welcome to the Student-Course Management Dashboard. Use the navigation links above to manage students and courses.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <h2 class="card-title"><i class="bi bi-people"></i> Students</h2>
                <p class="card-text">Manage student records including personal information and course enrollments.</p>
                <a href="/WEB2Finals/dashboard/students" class="btn btn-primary">View Students</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <h2 class="card-title"><i class="bi bi-book"></i> Courses</h2>
                <p class="card-text">Manage course listings including course codes, descriptions, and credit hours.</p>
                <a href="/WEB2Finals/dashboard/courses" class="btn btn-primary">View Courses</a>
            </div>
        </div>    </div>
    
    <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
    <div class="col-md-12 mb-4">
        <div class="card shadow border-danger">
            <div class="card-header bg-danger text-white">
                <h2 class="card-title mb-0"><i class="bi bi-shield-lock"></i> Admin Tools</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="bi bi-person-badge"></i> User Management</h4>
                        <p>Manage system users and their roles</p>
                        <a href="/WEB2Finals/dashboard/users" class="btn btn-outline-danger">Manage Users</a>
                    </div>
                    <div class="col-md-6">
                        <h4><i class="bi bi-graph-up"></i> System Reports</h4>
                        <p>View detailed system analytics and reports</p>
                        <a href="/WEB2Finals/dashboard/reports" class="btn btn-outline-danger">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
