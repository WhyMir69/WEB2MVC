<?php
$title = 'Student Details';
$page = 'students';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Student Details</h1>
    <div>
        <a href="/WEB2Finals/dashboard/students/edit?id=<?= $student['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="/WEB2Finals/dashboard/students" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Students
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?= htmlspecialchars($student['id']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($student['email'] ?? 'N/A') ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($student['date_of_birth'] ?? 'N/A') ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($student['address'] ?? 'N/A') ?></p>
                <p><strong>Created:</strong> <?= htmlspecialchars($student['created_at']) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Enrolled Courses Section -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Enrolled Courses</h5>
    </div>
    <div class="card-body">
        <?php if (empty($enrolledCourses)): ?>
            <p class="text-muted">This student is not enrolled in any courses.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolledCourses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                <td><?= htmlspecialchars($course['title']) ?></td>
                                <td><?= htmlspecialchars($course['credits']) ?></td>
                                <td>
                                    <form action="/WEB2Finals/dashboard/students/unenroll" method="POST" 
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

<!-- Available Courses for Enrollment -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Available Courses for Enrollment</h5>
    </div>
    <div class="card-body">
        <?php
        // Filter out courses the student is already enrolled in
        $enrolledCourseIds = array_column($enrolledCourses, 'id');
        $availableCourses = array_filter($allCourses, function($course) use ($enrolledCourseIds) {
            return !in_array($course['id'], $enrolledCourseIds);
        });
        
        if (empty($availableCourses)): 
        ?>
            <p class="text-muted">No available courses for enrollment.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Title</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableCourses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_code']) ?></td>
                                <td><?= htmlspecialchars($course['title']) ?></td>
                                <td><?= htmlspecialchars($course['credits']) ?></td>
                                <td>
                                    <form action="/WEB2Finals/dashboard/students/enroll" method="POST">
                                        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-plus-circle"></i> Enroll
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
