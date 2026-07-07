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
            $message = "<div class='alert success'>Account created successfully! You can now login.</div>";
        } else {
            $message = "<div class='alert error'>Error: Email might already be registered.</div>";
        }
    } 
    
    // --- LOGIN LOGIC (Intercepts and sends to verify.php) ---
    elseif (isset($_POST['login'])) {
        $email = $conn->real_escape_string($_POST['login_email']);
        $password = $_POST['login_password'];

        $sql = "SELECT * FROM Users WHERE Email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify the encrypted password
            if (password_verify($password, $user['Password_Hash'])) {
                
                // 1. Generate 6-digit OTP
                $otp = rand(100000, 999999);
                
                // 2. Store user data in a TEMPORARY session
                $_SESSION['temp_user'] = $user;
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_expires'] = time() + 300; // 5 minutes
                
                // 3. Route to the new Verification File
                header("Location: verify.php");
                exit();
                
            } else {
                $message = "<div class='alert error'>Incorrect password.</div>";
            }
        } else {
            $message = "<div class='alert error'>User not found.</div>";
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
        /* MODERN GLASSMORPHISM UI */
        :root {
            --primary: #0a2540;
            --secondary: #24b47e;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-light: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0a2540 0%, #1a3c60 100%);
            color: var(--text-light);
            padding: 20px;
        }

        .auth-container {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .logo { text-align: center; font-size: 1.8rem; font-weight: bold; color: var(--text-light); margin-bottom: 30px; cursor: pointer; text-transform: uppercase; letter-spacing: 2px;}
        h2 { text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 1.4rem; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: rgba(255, 255, 255, 0.8); }
        .form-group input { width: 100%; padding: 12px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: white; font-size: 1rem; transition: all 0.3s ease; }
        .form-group input:focus { outline: none; border-color: var(--secondary); background: rgba(255, 255, 255, 0.1); }
        .form-group input::placeholder { color: rgba(255,255,255,0.4); }
        
        .btn { width: 100%; padding: 14px; background: var(--secondary); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: transform 0.2s, background 0.3s; margin-top: 10px; }
        .btn:hover { background: #1e996a; transform: translateY(-2px); }
        
        .toggle-text { text-align: center; margin-top: 20px; font-size: 0.9rem; color: rgba(255, 255, 255, 0.7); }
        .toggle-link { color: var(--secondary); cursor: pointer; font-weight: bold; text-decoration: none; transition: 0.3s; }
        .toggle-link:hover { color: #69f0ae; }
        
        #register-form { display: none; }
        
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; font-weight: bold; }
        .error { background: rgba(226, 89, 80, 0.2); border: 1px solid #e25950; color: #ff8a80; }
        .success { background: rgba(36, 180, 126, 0.2); border: 1px solid var(--secondary); color: #69f0ae; }
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