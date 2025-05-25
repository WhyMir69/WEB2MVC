<?php
$title = 'Courses';
$page = 'courses';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Courses</h1>
    <a href="/WEB2Finals/dashboard/courses/create" class="btn btn-primary">
        <i class="bi bi-plus"></i> Add New Course
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="/WEB2Finals/dashboard/courses" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by code, title or description..." 
                           name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="credits" class="form-select">
                    <option value="">All Credits</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>" <?= ($credits == $i) ? 'selected' : '' ?>>
                            <?= $i ?> Credit<?= ($i > 1) ? 's' : '' ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if (!empty($search) || !empty($credits)): ?>
                    <a href="/WEB2Finals/dashboard/courses" class="btn btn-outline-secondary mt-2 w-100">
                        <i class="bi bi-x-circle"></i> Clear Filters
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
                        <th>Course Code</th>
                        <th>Title</th>
                        <th>Credits</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No courses found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                <td><?= htmlspecialchars($course['title']) ?></td>
                                <td><?= htmlspecialchars($course['credits']) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/WEB2Finals/dashboard/courses/show?id=<?= $course['id'] ?>" class="btn btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>                                        <a href="/WEB2Finals/dashboard/courses/edit?id=<?= $course['id'] ?>" class="btn btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/WEB2Finals/dashboard/courses/delete" method="POST" class="d-inline" 
                                           onsubmit="return confirm('Are you sure you want to delete this course?')">
                                            <input type="hidden" name="id" value="<?= $course['id'] ?>">
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
