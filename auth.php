<?php
session_start();
require 'db.php'; // Connect to MySQL

$message = '';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ==========================================
    // 1. REGISTRATION LOGIC & VALIDATION
    // ==========================================
    if (isset($_POST['register'])) {
        
        // Sanitize inputs
        $name = trim($conn->real_escape_string($_POST['reg_name']));
        $email = trim($conn->real_escape_string($_POST['reg_email']));
        $address = trim($conn->real_escape_string($_POST['reg_address']));
        $contact = trim($conn->real_escape_string($_POST['reg_contact']));
        
        $raw_password = $_POST['reg_password'];
        $confirm_password = $_POST['reg_password_confirm'];
        
        $errors = [];

        // VALIDATION: Name Check
        if (strlen($name) < 3) {
            $errors[] = "Full Name must be at least 3 characters long.";
        }

        // VALIDATION: Strict Email Check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format. Please include an '@' and a valid domain.";
        }
        
        // VALIDATION: Contact Number Check (Strictly 11 digits)
        if (!preg_match("/^[0-9]{11}$/", $contact)) {
            $errors[] = "Contact number must be exactly 11 digits (e.g., 01712345678).";
        }
        
        // VALIDATION: Address Check
        if (empty($address)) {
            $errors[] = "Address cannot be empty.";
        }

        // ==========================================
        // STRONG PASSWORD VALIDATION
        // ==========================================
        
        // 1. Check if passwords match
        if ($raw_password !== $confirm_password) {
            $errors[] = "Passwords do not match. Please try again.";
        }
        
        // 2. Minimum Length
        if (strlen($raw_password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        
        // 3. Must contain at least one uppercase letter (A-Z)
        if (!preg_match('/[A-Z]/', $raw_password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        
        // 4. Must contain at least one special character
        if (!preg_match('/[^a-zA-Z0-9]/', $raw_password)) {
            $errors[] = "Password must contain at least one special character (e.g., @, #, $, %).";
        }

        // --- Check for Errors Before Database Entry ---
        if (count($errors) > 0) {
            $error_string = implode("<br>", $errors);
            $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-left bg-danger/20 border border-danger text-[#ff8a80]'>$error_string</div>";
        } else {
            // NO ERRORS: Proceed to Database
            $password_hash = password_hash($raw_password, PASSWORD_BCRYPT); 
            
            $check = $conn->query("SELECT * FROM Users WHERE Email='$email'");
            if ($check->num_rows > 0) {
                $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-danger/20 border border-danger text-[#ff8a80]'>Error: Email is already registered.</div>";
            } else {
                $sql = "INSERT INTO Users (Name, Email, Phone, Address, Password_Hash, Role) VALUES ('$name', '$email', '$contact', '$address', '$password_hash', 'Customer')";
                if ($conn->query($sql) === TRUE) {
                    $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-secondary/20 border border-secondary text-[#69f0ae]'>Account created successfully! You can now login.</div>";
                } else {
                    $message = "<div class='p-3 mb-5 rounded-lg text-sm font-bold text-center bg-danger/20 border border-danger text-[#ff8a80]'>Database Error.</div>";
                }
            }
        }
    } 
    
    // ==========================================
    // 2. LOGIN LOGIC
    // ==========================================
    elseif (isset($_POST['login'])) {
        $email = trim($conn->real_escape_string($_POST['login_email']));
        $password = $_POST['login_password'];

        $sql = "SELECT * FROM Users WHERE Email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['Password_Hash'])) {
                $_SESSION['temp_user'] = $user;
                $_SESSION['otp'] = rand(100000, 999999);
                $_SESSION['otp_expires'] = time() + 300; 
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#0a2540', secondary: '#24b47e', danger: '#e25950' } } } }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary to-[#1a3c60] text-white p-5 font-sans">

    <div class="w-full max-w-lg p-8 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-[0_8px_32px_rgba(0,0,0,0.3)] text-center my-8">
        
        <div class="text-2xl font-bold text-white mb-6 cursor-pointer uppercase tracking-widest" onclick="window.location.href='home.php'">Bank Ashkona</div>
        
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
                <button type="submit" name="login" class="w-full p-3.5 mt-2 bg-secondary hover:bg-[#1e996a] text-white font-bold rounded-lg transition-transform hover:-translate-y-0.5">Secure Login</button>
            </form>
            <p class="text-center mt-5 text-sm text-white/70">Don't have an account? <span class="text-secondary font-bold cursor-pointer transition-colors hover:text-[#69f0ae]" onclick="toggleForms()">Open Account</span></p>
        </div>

        <!-- REGISTRATION FORM -->
        <div id="register-form" class="hidden">
            <h2 class="mb-5 font-semibold text-xl">Open New Account</h2>
            <form action="auth.php" method="POST">
                
                <div class="mb-4 text-left">
                    <label class="block mb-1 text-sm text-white/80">Full Name</label>
                    <input type="text" name="reg_name" required minlength="3" placeholder="As per NID" 
                           class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4 text-left">
                        <label class="block mb-1 text-sm text-white/80">Email Address</label>
                        <input type="email" name="reg_email" required placeholder="name@example.com" 
                               class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                    </div>

                    <div class="mb-4 text-left">
                        <label class="block mb-1 text-sm text-white/80">Contact Number</label>
                        <!-- Changed pattern to strictly 11 digits -->
                        <input type="tel" name="reg_contact" required pattern="[0-9]{11}" title="Enter exactly 11 digits (e.g. 01712345678)" placeholder="e.g. 01712345678" 
                               class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                    </div>
                </div>

                <div class="mb-4 text-left">
                    <label class="block mb-1 text-sm text-white/80">Address</label>
                    <input type="text" name="reg_address" required placeholder="House, Street, City" 
                           class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4 text-left">
                        <label class="block mb-1 text-sm text-white/80">Create Password</label>
                        <input type="password" name="reg_password" required minlength="8" 
                               pattern="(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}" 
                               title="Must contain at least 8 characters, one uppercase letter, and one special character."
                               placeholder="Min 8 chars, 1 uppercase, 1 symbol" 
                               class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                    </div>

                    <div class="mb-4 text-left">
                        <label class="block mb-1 text-sm text-white/80">Verify Password</label>
                        <input type="password" name="reg_password_confirm" required minlength="8" placeholder="Type password again" 
                               class="w-full p-2.5 bg-white/5 border border-white/20 rounded-lg text-white text-base transition-all focus:outline-none focus:border-secondary focus:bg-white/10 placeholder-white/40">
                    </div>
                </div>

                <button type="submit" name="register" class="w-full p-3 mt-2 bg-secondary hover:bg-[#1e996a] text-white font-bold rounded-lg transition-transform hover:-translate-y-0.5">Create Account</button>
            </form>
            <p class="text-center mt-4 text-sm text-white/70">Already a customer? <span class="text-secondary font-bold cursor-pointer transition-colors hover:text-[#69f0ae]" onclick="toggleForms()">Login Here</span></p>
        </div>

    </div>

    <script>
        window.onload = function() {
            var msg = `<?= strip_tags($message) ?>`;
            if (msg.includes("characters long") || msg.includes("Invalid email") || msg.includes("already registered") || 
                msg.includes("exactly 11 digits") || msg.includes("Address") || msg.includes("Database Error") || 
                msg.includes("do not match") || msg.includes("uppercase letter") || msg.includes("special character")) {
                document.getElementById('login-form').classList.add('hidden');
                document.getElementById('register-form').classList.remove('hidden');
            }
        };

        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            loginForm.classList.toggle('hidden');
            registerForm.classList.toggle('hidden');
        }
    </script>
</body>
</html>