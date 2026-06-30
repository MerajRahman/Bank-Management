<?php
session_start();
require 'db.php'; // Connect to MySQL

$message = '';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- REGISTRATION LOGIC ---
    if (isset($_POST['register'])) {
        $name = $conn->real_escape_string($_POST['reg_name']);
        $email = $conn->real_escape_string($_POST['reg_email']);
        // Encrypt the password
        $password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT); 

        $sql = "INSERT INTO Users (Name, Email, Password_Hash, Role) VALUES ('$name', '$email', '$password', 'Customer')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div style='color: #24b47e; background: #e8f8f2; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;'>Account created successfully! You can now login.</div>";
        } else {
            $message = "<div style='color: #e25950; background: #fceceb; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;'>Error: Email might already be registered.</div>";
        }
    } 
    
    // --- LOGIN LOGIC ---
    elseif (isset($_POST['login'])) {
        $email = $conn->real_escape_string($_POST['login_email']);
        $password = $_POST['login_password'];

        $sql = "SELECT * FROM Users WHERE Email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the encrypted password
            if (password_verify($password, $user['Password_Hash'])) {
                // Success! Set session variables
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['name'] = $user['Name'];
                $_SESSION['role'] = $user['Role'];
                
                // --- NEW ROUTING LOGIC ---
                if ($_SESSION['role'] === 'Admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
                
            } else {
                $message = "<div style='color: #e25950; text-align: center; margin-bottom: 15px;'>Incorrect password.</div>";
            }
        } else {
            $message = "<div style='color: #e25950; text-align: center; margin-bottom: 15px;'>User not found.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Access | Bank Ashkona</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        :root { --primary: #0a2540; --secondary: #0066cc; --text: #333; }
        body { background: linear-gradient(135deg, var(--primary), #1a4a7b); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .auth-container { background: white; width: 100%; max-width: 400px; padding: 40px; border-radius: 10px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .logo { text-align: center; font-size: 1.8rem; font-weight: bold; color: var(--primary); margin-bottom: 30px; cursor: pointer; }
        h2 { text-align: center; margin-bottom: 20px; color: var(--text); font-size: 1.4rem; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-size: 0.9rem; font-weight: 600; color: #555; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        .form-group input:focus { border-color: var(--secondary); outline: none; }
        .btn { width: 100%; padding: 12px; background: var(--secondary); color: white; border: none; border-radius: 5px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn:hover { background: #004c99; }
        .toggle-text { text-align: center; margin-top: 20px; font-size: 0.9rem; color: #666; }
        .toggle-link { color: var(--secondary); cursor: pointer; font-weight: bold; text-decoration: none; }
        .toggle-link:hover { text-decoration: underline; }
        #register-form { display: none; }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="logo" onclick="window.location.href='home.php'">Bank Ashkona</div>
        
        <?= $message ?>

        <div id="login-form">
            <h2>System Login</h2>
            <form action="auth.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="login_email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="login_password" required placeholder="Enter your password">
                </div>
                <button type="submit" name="login" class="btn">Secure Login</button>
            </form>
            <p class="toggle-text">Don't have an account? <span class="toggle-link" onclick="toggleForms()">Open Account</span></p>
        </div>

        <div id="register-form">
            <h2>Open New Account</h2>
            <form action="auth.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="reg_name" required placeholder="As per NID">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="reg_email" required placeholder="For account recovery">
                </div>
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="reg_password" required placeholder="Minimum 8 characters">
                </div>
                <button type="submit" name="register" class="btn">Create Account</button>
            </form>
            <p class="toggle-text">Already a customer? <span class="toggle-link" onclick="toggleForms()">Login Here</span></p>
        </div>

    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>