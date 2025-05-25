<?php
$title = 'Edit Student';
$page = 'students';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Edit Student</h1>
    <a href="/WEB2Finals/dashboard/students" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Students
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="/WEB2Finals/dashboard/students/update" method="post">
            <input type="hidden" name="id" value="<?= $student['id'] ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($student['address'] ?? '') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($student['date_of_birth'] ?? '') ?>">
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/WEB2Finals/dashboard/students" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>
