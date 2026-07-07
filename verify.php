<?php
session_start();
require 'db.php';

// --- SECURITY FIREWALL ---
// If the user hasn't successfully entered a password, kick them to auth.php
if (!isset($_SESSION['temp_user'])) {
    header("Location: auth.php");
    exit();
}

// Development Mode: Display OTP on screen for testing purposes
$message = "<div class='alert info'>SECURITY CODE: <strong style='font-size: 1.2rem; letter-spacing: 2px;'>" . $_SESSION['otp'] . "</strong></div>";

// --- VERIFICATION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_btn'])) {
    $entered_otp = $_POST['otp_code'];
    
    // Check if OTP expired (5 minutes)
    if (time() > $_SESSION['otp_expires']) {
        session_unset();
        session_destroy();
        header("Location: auth.php");
        exit();
    } 
    
    // Check if OTP is correct
    elseif ($entered_otp == $_SESSION['otp']) {
        // Verification Passed! Migrate temporary session to permanent session
        $user = $_SESSION['temp_user'];
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['name'] = $user['Name'];
        $_SESSION['role'] = $user['Role'];
        
        // Destroy temporary data
        unset($_SESSION['temp_user']);
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expires']);
        
        // Route to the correct dashboard
        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } 
    
    // Invalid OTP
    else {
        $message = "<div class='alert error'>Invalid verification code. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Verification | Bank Ashkona</title>
    <style>
        /* SHARED UI ARCHITECTURE */
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
            text-align: center;
        }

        .logo { font-size: 1.8rem; font-weight: bold; color: var(--text-light); margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;}
        h2 { margin-bottom: 10px; font-weight: 600; font-size: 1.4rem; }
        p.subtitle { margin-bottom: 25px; font-size: 0.9rem; color: rgba(255, 255, 255, 0.7); }
        
        .form-group { margin-bottom: 20px; }
        
        /* Specialized OTP Input Field */
        .form-group input { 
            width: 100%; 
            padding: 15px; 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid var(--glass-border); 
            border-radius: 8px; 
            color: white; 
            transition: all 0.3s ease; 
            text-align: center;
            font-size: 2rem;
            letter-spacing: 15px;
            font-weight: bold;
        }
        .form-group input:focus { outline: none; border-color: var(--secondary); background: rgba(255, 255, 255, 0.1); box-shadow: 0 0 10px rgba(36, 180, 126, 0.3); }
        .form-group input::placeholder { color: rgba(255,255,255,0.2); letter-spacing: normal; font-size: 1rem; font-weight: normal; }
        
        .btn { width: 100%; padding: 14px; background: var(--secondary); color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: transform 0.2s, background 0.3s; margin-top: 10px; }
        .btn:hover { background: #1e996a; transform: translateY(-2px); }
        
        .toggle-link { display: inline-block; margin-top: 20px; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .toggle-link:hover { color: #e25950; text-decoration: underline; }
        
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 25px; font-size: 0.9rem; font-weight: bold; }
        .error { background: rgba(226, 89, 80, 0.2); border: 1px solid #e25950; color: #ff8a80; }
        .info { background: rgba(255, 255, 255, 0.1); border: 1px solid #fff; color: #fff; }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="logo">Bank Ashkona</div>
        
        <h2>2-Step Verification</h2>
        <p class="subtitle">Enter the 6-digit security code generated for this session to verify your identity.</p>
        
        <?= $message ?>

        <form action="verify.php" method="POST">
            <div class="form-group">
                <input type="text" name="otp_code" maxlength="6" required autocomplete="off" autofocus>
            </div>
            <button type="submit" name="verify_btn" class="btn">Confirm Identity</button>
        </form>
        
        <a href="logout.php" class="toggle-link">Cancel Login</a>
    </div>

</body>
</html>