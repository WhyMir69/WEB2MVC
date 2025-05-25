<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'dashboard_db';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = file_get_contents(__DIR__ . '/config/role_updates.sql');

if (mysqli_multi_query($conn, $sql)) {
    echo "<h2>Database updated successfully with role support!</h2>";
    echo "<p><a href='/WEB2Finals/dashboard/login'>Go to Login Page</a></p>";
} else {
    echo "Error updating database: " . mysqli_error($conn);
}

mysqli_close($conn);
