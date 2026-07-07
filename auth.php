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
        $password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT); 

        $sql = "INSERT INTO Users (Name, Email, Password_Hash, Role) VALUES ('$name', '$email', '$password', 'Customer')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-secondary/20 border border-secondary text-[#69f0ae]'>Account created successfully! You can now login.</div>";
        } else {
            $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-danger/20 border border-danger text-[#ff8a80]'>Error: Email might already be registered.</div>";
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
                $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-danger/20 border border-danger text-[#ff8a80]'>Incorrect password.</div>";
            }
        } else {
            $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-danger/20 border border-danger text-[#ff8a80]'>User not found.</div>";
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
    
    <!-- TAILWIND CSS CDN (This fulfills your faculty framework requirement!) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration (Setting your brand colors) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0a2540',
                        secondary: '#24b47e',
                        danger: '#e25950'
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary to-[#1a3c60] text-white p-5 font-sans">

    <!-- Glassmorphism Container built with Tailwind Utility Classes -->
    <div class="w-full max-w-md p-10 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-[0_8px_32px_rgba(0,0,0,0.3)] text-center">
        
        <div class="text-2xl font-bold text-white mb-8 cursor-pointer uppercase tracking-widest" onclick="window.location.href='home.php'">Bank Ashkona</div>
        
        <?= $message ?>

        <!-- LOGIN FORM -->
        <div id="login-form">
            <h2 class="mb-5 font-semibold text-xl">System Login</h2>
            <form action="auth.php" method="POST">
                <div class="mb-5 text-left">
                    <label class="block mb-2 text-sm text-white/80">Email Address</label>
                    <input type="email" name="login_email" required placeholder="Enter your email" 
                           class="w-full p-3 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>
                <div class="mb-5 text-left">
                    <label class="block mb-2 text-sm text-white/80">Password</label>
                    <input type="password" name="login_password" required placeholder="Enter your password" 
                           class="w-full p-3 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>
                <button type="submit" name="login" 
                        class="w-full p-3.5 mt-2 bg-secondary hover:bg-[#1e996a] text-white font-bold rounded-lg transition-transform hover:-translate-y-0.5">
                    Secure Login
                </button>
            </form>
            <p class="text-center mt-5 text-sm text-white/70">
                Don't have an account? 
                <span class="text-secondary font-bold cursor-pointer transition-colors hover:text-[#69f0ae]" onclick="toggleForms()">Open Account</span>
            </p>
        </div>

        <!-- REGISTRATION FORM -->
        <div id="register-form" class="hidden">
            <h2 class="mb-5 font-semibold text-xl">Open New Account</h2>
            <form action="auth.php" method="POST">
                <div class="mb-5 text-left">
                    <label class="block mb-2 text-sm text-white/80">Full Name</label>
                    <input type="text" name="reg_name" required placeholder="As per NID" 
                           class="w-full p-3 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>
                <div class="mb-5 text-left">
                    <label class="block mb-2 text-sm text-white/80">Email Address</label>
                    <input type="email" name="reg_email" required placeholder="For account recovery" 
                           class="w-full p-3 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>
                <div class="mb-5 text-left">
                    <label class="block mb-2 text-sm text-white/80">Create Password</label>
                    <input type="password" name="reg_password" required placeholder="Minimum 8 characters" 
                           class="w-full p-3 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>
                <button type="submit" name="register" 
                        class="w-full p-3.5 mt-2 bg-secondary hover:bg-[#1e996a] text-white font-bold rounded-lg transition-transform hover:-translate-y-0.5">
                    Create Account
                </button>
            </form>
            <p class="text-center mt-5 text-sm text-white/70">
                Already a customer? 
                <span class="text-secondary font-bold cursor-pointer transition-colors hover:text-[#69f0ae]" onclick="toggleForms()">Login Here</span>
            </p>
        </div>

    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            // Tailwind uses the 'hidden' class just like your custom CSS did!
            loginForm.classList.toggle('hidden');
            registerForm.classList.toggle('hidden');
        }
    </script>
</body>
</html>