<?php
// Simple direct admin user fix
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dashboard_db';

// Connect to MySQL
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fresh password hash for 'admin123'
$hash = '$2y$10$YCCH.qeqDxFf5.eOToEle.Mbxu6Nm9.Tj/j.MtiCy1EraZZpzsIem';

// Remove any existing admin users
mysqli_query($conn, "DELETE FROM users WHERE username='admin'");

// Check if the role column exists
$result = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
$roleColumnExists = mysqli_num_rows($result) > 0;

// Insert a fresh admin user
if ($roleColumnExists) {
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@example.com', '$hash', 'admin')";
} else {
    $sql = "INSERT INTO users (username, email, password) VALUES ('admin', 'admin@example.com', '$hash')";
}

if (mysqli_query($conn, $sql)) {
    echo "<h2>Admin user created successfully!</h2>";
    echo "<p>Username: admin</p>";
    echo "<p>Password: admin123</p>";
    echo "<p><a href='/WEB2Finals/dashboard/login'>Go to Login Page</a></p>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Get all users for debugging
echo "<h3>Current Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT id, username, email, password FROM users");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password Hash</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['password'] . "</td>";
    echo "</tr>";
}

echo "</table>";

// Add a test function to verify passwords
echo "<h3>Password Verification Test:</h3>";
$testPass = 'admin123';
$verifyResult = password_verify($testPass, $hash);
echo "Verifying '$testPass' against hash: " . ($verifyResult ? "SUCCESS" : "FAILED");

mysqli_close($conn);
?>
