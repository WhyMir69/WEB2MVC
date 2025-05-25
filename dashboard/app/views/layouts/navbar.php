<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/WEB2Finals/dashboard/dashboard">Student-Course Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>" href="/WEB2Finals/dashboard/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'students' ? 'active' : '' ?>" href="/WEB2Finals/dashboard/students">Students</a>
                </li>                <li class="nav-item">
                    <a class="nav-link <?= $page === 'courses' ? 'active' : '' ?>" href="/WEB2Finals/dashboard/courses">Courses</a>
                </li>
                <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'users' ? 'active' : '' ?>" href="/WEB2Finals/dashboard/users">User Management</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> 
                        <?= htmlspecialchars($user['username']) ?> 
                        <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($user['role'] ?? 'staff')) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/WEB2Finals/dashboard/profile">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/WEB2Finals/dashboard/logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
