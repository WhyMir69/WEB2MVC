<?php
$title = 'Course Details';
$page = 'courses';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Course Details</h1>
    <div>
        <a href="/WEB2Finals/dashboard/courses/edit?id=<?= $course['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="/WEB2Finals/dashboard/courses" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Courses
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
            <?= htmlspecialchars($course['course_code']) ?> - <?= htmlspecialchars($course['title']) ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Course Code:</strong> <?= htmlspecialchars($course['course_code']) ?></p>
                <p><strong>Title:</strong> <?= htmlspecialchars($course['title']) ?></p>
                <p><strong>Credits:</strong> <?= htmlspecialchars($course['credits']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Created:</strong> <?= htmlspecialchars($course['created_at']) ?></p>
            </div>
        </div>
        
        <div class="mt-3">
            <h5>Description</h5>
            <p><?= nl2br(htmlspecialchars($course['description'] ?? 'No description available.')) ?></p>
        </div>
    </div>
</div>

<!-- Enrolled Students Section -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Enrolled Students</h5>
    </div>
    <div class="card-body">
        <?php if (empty($enrolledStudents)): ?>
            <p class="text-muted">No students are currently enrolled in this course.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolledStudents as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['id']) ?></td>
                                <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= htmlspecialchars($student['email'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="/WEB2Finals/dashboard/students/show?id=<?= $student['id'] ?>" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="/WEB2Finals/dashboard/students/unenroll" method="POST" class="d-inline" 
                                        onsubmit="return confirm('Are you sure you want to unenroll this student from this course?')">
                                        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-circle"></i> Unenroll
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
