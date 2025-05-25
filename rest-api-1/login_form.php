<?php
$router->addRoute('GET', '/login_form', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/login_form.html';
    exit; 
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>API Login Test</title>
    <script>
        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
              try {
                const response = await fetch('http://localhost/WEB2Finals/rest-api-1/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const result = await response.json();
                document.getElementById('result').textContent = JSON.stringify(result, null, 2);
                
                // Store the token if login was successful
                if (result.token) {
                    localStorage.setItem('jwt_token', result.token);
                }
            } catch (error) {
                document.getElementById('result').textContent = 'Error: ' + error.message;
            }
        }
    </script>
</head>
<body>
    <h1>API Login Test</h1>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" value="john.doe@example.com">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" value="password123">
    </div>
    <button onclick="login()">Login</button>
    
    <h2>Result:</h2>
    <pre id="result"></pre>
</body>
</html>