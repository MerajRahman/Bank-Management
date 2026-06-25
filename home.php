<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Ashkona | Secure Banking Management</title>
    <style>
        /* CSS Reset & Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        :root { --primary: #0a2540; --secondary: #0066cc; --accent: #24b47e; --light: #f6f9fc; --dark: #32325d; }
        
        body { background-color: var(--light); color: var(--dark); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }

        /* Navigation Bar */
        header { background-color: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 1.8rem; font-weight: bold; color: var(--primary); }
        .nav-links { list-style: none; display: flex; gap: 30px; }
        .nav-links li a { font-weight: 500; transition: color 0.3s; }
        .nav-links li a:hover { color: var(--secondary); }
        .auth-buttons { display: flex; gap: 15px; }
        .btn { padding: 10px 24px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; border: none; }
        .btn-outline { background: transparent; border: 2px solid var(--secondary); color: var(--secondary); }
        .btn-outline:hover { background: var(--secondary); color: white; }
        .btn-solid { background: var(--secondary); color: white; border: 2px solid var(--secondary); }
        .btn-solid:hover { background: #004c99; border-color: #004c99; }

        /* Hero Section */
        .hero { background: linear-gradient(135deg, var(--primary), #1a4a7b); color: white; padding: 100px 50px; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto 40px auto; opacity: 0.9; }

        /* Access Portals (User Roles) */
        .portals-section { padding: 80px 50px; max-width: 1200px; margin: 0 auto; text-align: center; }
        .portals-section h2 { font-size: 2.5rem; margin-bottom: 50px; color: var(--primary); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        
        .card { background: white; padding: 40px 30px; border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: transform 0.3s; border-top: 5px solid transparent; }
        .card:hover { transform: translateY(-10px); }
        .card.customer { border-top-color: var(--secondary); }
        .card.admin { border-top-color: var(--primary); }
        .card.guest { border-top-color: var(--accent); }
        
        .card h3 { font-size: 1.5rem; margin-bottom: 15px; }
        .card p { color: #666; margin-bottom: 25px; min-height: 50px; }
        .card .btn { width: 100%; display: inline-block; padding: 12px; }

        /* Footer */
        footer { background-color: var(--primary); color: white; text-align: center; padding: 20px; margin-top: auto; }
    </style>
</head>
<body>

    <header>
        <div class="logo">Bank Ashkona</div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <<div class="auth-buttons">
            <button class="btn" style="background: transparent; color: var(--primary); border: none; text-decoration: underline;" onclick="window.location.href='auth.php'">Admin Login</button>
            
            <button class="btn btn-outline" onclick="window.location.href='auth.php'">Customer Login</button>
            <button class="btn btn-solid" onclick="window.location.href='auth.php'">Open Account</button>
        </div>
    </header>

    <section class="hero">
        <h1>Secure. Fast. Reliable.</h1>
        <p>Experience next-generation digital banking. Manage your finances, transfer funds instantly, and track your history with our robust, ACID-compliant platform.</p>
        <button