<?php
$title = 'System Reports';
$page = 'reports';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-graph-up"></i> System Reports</h1>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-4"><?= $studentCount ?></h2>
                        <h5>Total Students</h5>
                    </div>
                    <div>
                        <i class="bi bi-people display-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-primary-dark">
                <a href="/WEB2Finals/dashboard/students" class="text-white">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-4"><?= $courseCount ?></h2>
                        <h5>Total Courses</h5>
                    </div>
                    <div>
                        <i class="bi bi-book display-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-success-dark">
                <a href="/WEB2Finals/dashboard/courses" class="text-white">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-4"><?= $userCount ?></h2>
                        <h5>System Users</h5>
                    </div>
                    <div>
                        <i class="bi bi-person-badge display-1 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-info-dark">
                <a href="/WEB2Finals/dashboard/users" class="text-white">View Details <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h4 class="mb-0">Recent Students</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentStudents)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No students found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentStudents as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['email'] ?? 'N/A') ?></td>
                                        <td><?= (new DateTime($student['created_at']))->format('M d, Y') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="/WEB2Finals/dashboard/students" class="btn btn-sm btn-outline-primary">View All Students</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h4 class="mb-0">Recent Courses</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentCourses)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No courses found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentCourses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['course_code']) ?></td>
                                        <td><?= htmlspecialchars($course['title']) ?></td>
                                        <td><?= (new DateTime($course['created_at']))->format('M d, Y') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="/WEB2Finals/dashboard/courses" class="btn btn-sm btn-outline-primary">View All Courses</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
