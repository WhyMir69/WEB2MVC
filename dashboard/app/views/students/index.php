<?php
$title = 'Students';
$page = 'students';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Students</h1>
    <a href="/WEB2Finals/dashboard/students/create" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add New Student
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="/WEB2Finals/dashboard/students" method="GET" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by name, email or phone..." 
                           name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <?php if (!empty($search)): ?>
                    <a href="/WEB2Finals/dashboard/students" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear Search
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No students found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['id']) ?></td>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= htmlspecialchars($student['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></td>                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/WEB2Finals/dashboard/students/show?id=<?= $student['id'] ?>" class="btn btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/WEB2Finals/dashboard/students/edit?id=<?= $student['id'] ?>" class="btn btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/WEB2Finals/dashboard/students/delete" method="POST" class="d-inline" 
                                           onsubmit="return confirm('Are you sure you want to delete this student?')">
                                            <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
