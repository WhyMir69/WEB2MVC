<?php
var_dump($user); 
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Password (hashed):</strong> <?= htmlspecialchars($user['password']) ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>

    <form method="POST" action="/logout">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
