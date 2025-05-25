<?php
$title = 'Edit Course';
$page = 'courses';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Course</h1>
    <a href="/WEB2Finals/dashboard/courses" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="/WEB2Finals/dashboard/courses/update" method="post">
            <input type="hidden" name="id" value="<?= $course['id'] ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="course_code" name="course_code" value="<?= htmlspecialchars($course['course_code']) ?>" required>
                    <small class="form-text text-muted">Example: CS101, MATH201, etc.</small>
                </div>
                <div class="col-md-6">
                    <label for="credits" class="form-label">Credits <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="credits" name="credits" min="1" max="6" value="<?= htmlspecialchars($course['credits']) ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/WEB2Finals/dashboard/courses" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
