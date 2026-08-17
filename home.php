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
        html { scroll-behavior: smooth; }
        body { background-color: var(--light); color: var(--dark); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        /* Navigation Bar */
        header { background-color: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; flex-wrap: wrap; gap: 10px; }
        .logo { font-size: 1.8rem; font-weight: bold; color: var(--primary); cursor: pointer; }
        .logo span { color: var(--secondary); }
        .nav-links { list-style: none; display: flex; gap: 30px; flex-wrap: wrap; }
        .nav-links li a { font-weight: 500; transition: color 0.3s; }
        .nav-links li a:hover, .nav-links li a.active { color: var(--secondary); }
        .auth-buttons { display: flex; gap: 15px; flex-wrap: wrap; }
        .btn { padding: 10px 24px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; border: none; font-size: 0.95rem; }
        .btn-outline { background: transparent; border: 2px solid var(--secondary); color: var(--secondary); }
        .btn-outline:hover { background: var(--secondary); color: white; }
        .btn-solid { background: var(--secondary); color: white; border: 2px solid var(--secondary); }
        .btn-solid:hover { background: #004c99; border-color: #004c99; }
        .btn-accent { background: var(--accent); color: white; border: 2px solid var(--accent); }
        .btn-accent:hover { background: #1e996a; border-color: #1e996a; }

        /* Hero Section */
        .hero { position: relative; color: white; padding: 120px 50px; text-align: center; background: linear-gradient(135deg, rgba(10,37,64,0.88), rgba(26,74,123,0.82)), url('images/bank-building.jpg') center/cover no-repeat; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; text-shadow: 0 2px 12px rgba(0,0,0,0.35); }
        .hero p { font-size: 1.2rem; max-width: 620px; margin: 0 auto 40px auto; opacity: 0.95; }
        .hero .btn-group { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        .hero .btn { font-size: 1.05rem; padding: 14px 34px; }
        .btn-white { background: white; color: var(--primary); }
        .btn-white:hover { background: #e8eef5; }

        /* Stats Bar */
        .stats { background: var(--primary); color: white; display: flex; justify-content: space-around; flex-wrap: wrap; padding: 40px 50px; text-align: center; gap: 25px; }
        .stat h3 { font-size: 2.2rem; color: var(--accent); }
        .stat p { opacity: 0.8; }

        /* Section Headings */
        .section { padding: 80px 50px; max-width: 1200px; margin: 0 auto; }
        .section-title { font-size: 2.5rem; margin-bottom: 15px; color: var(--primary); text-align: center; }
        .section-sub { text-align: center; max-width: 650px; margin: 0 auto 50px auto; color: #666; }

        /* Services Cards (Banking Images) */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.06); transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 16px 32px rgba(0,0,0,0.12); }
        .card img { width: 100%; height: 200px; object-fit: cover; }
        .card-body { padding: 25px; }
        .card-body h3 { font-size: 1.3rem; margin-bottom: 10px; color: var(--primary); }
        .card-body p { color: #666; margin-bottom: 15px; }

        /* About Section */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; }
        .about-grid img { border-radius: 12px; box-shadow: 0 14px 30px rgba(0,0,0,0.15); height: 400px; width: 100%; object-fit: cover; }
        .about-text h2 { font-size: 2.3rem; color: var(--primary); margin-bottom: 20px; }
        .about-text p { color: #555; margin-bottom: 18px; }
        .check-list { list-style: none; margin-top: 10px; }
        .check-list li { padding: 6px 0; font-weight: 500; }
        .check-list li::before { content: "✔  "; color: var(--accent); font-weight: bold; }

        /* Team Section */
        .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 30px; }
        .team-card { background: white; border-radius: 10px; overflow: hidden; text-align: center; box-shadow: 0 8px 18px rgba(0,0,0,0.06); transition: transform 0.3s; }
        .team-card:hover { transform: translateY(-8px); }
        .team-card img { width: 100%; height: 260px; object-fit: cover; object-position: top; }
        .team-card h3 { margin-top: 18px; color: var(--primary); font-size: 1.15rem; }
        .team-card p.role { color: var(--secondary); font-weight: bold; margin-bottom: 6px; }
        .team-card p.desc { color: #777; font-size: 0.85rem; padding: 0 15px 20px; }

        /* Contact Section */
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: start; }
        .contact-info h2 { font-size: 2.2rem; color: var(--primary); margin-bottom: 20px; }
        .contact-info p { color: #555; margin-bottom: 14px; }
        .contact-form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .contact-form input, .contact-form textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; font-family: inherit; }
        .contact-form input:focus, .contact-form textarea:focus { outline: none; border-color: var(--secondary); }

        /* Footer */
        footer { background-color: var(--primary); color: white; text-align: center; padding: 25px; margin-top: auto; }
        footer a { color: var(--accent); }

        /* Responsive */
        @media (max-width: 768px) {
            header { padding: 15px 20px; justify-content: center; text-align: center; }
            .hero { padding: 80px 20px; }
            .hero h1 { font-size: 2.3rem; }
            .section { padding: 50px 20px; }
            .about-grid, .contact-grid { grid-template-columns: 1fr; }
            .about-grid img { height: 280px; }
            .stats { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo" onclick="window.location.href='home.php'">Bank <span>Ashkona</span></div>
        <ul class="nav-links">
            <li><a href="home.php" class="active">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="auth-buttons">
            <button class="btn" style="background: transparent; color: var(--primary); border: none; text-decoration: underline;" onclick="window.location.href='auth.php'">Admin Login</button>
            <button class="btn btn-outline" onclick="window.location.href='auth.php'">Customer Login</button>
            <button class="btn btn-solid" onclick="window.location.href='auth.php'">Open Account</button>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <h1>Secure. Fast. Reliable.</h1>
        <p>Experience next-generation digital banking. Manage your finances, transfer funds instantly, and track your history with our robust, ACID-compliant platform.</p>
        <div class="btn-group">
            <button class="btn btn-solid" onclick="window.location.href='auth.php'">Open Your Account</button>
            <button class="btn btn-white" onclick="window.location.href='#services'">Explore Services</button>
        </div>
    </section>

    <!-- STATS -->
    <section class="stats">
        <div class="stat"><h3>50+</h3><p>Branches Nationwide</p></div>
        <div class="stat"><h3>120k+</h3><p>Happy Customers</p></div>
        <div class="stat"><h3>24/7</h3><p>Customer Support</p></div>
        <div class="stat"><h3>৳ 10M+</h3><p>Transactions Daily</p></div>
    </section>

    <!-- SERVICES -->
    <section class="section" id="services">
        <h2 class="section-title">Our Banking Services</h2>
        <p class="section-sub">From everyday savings to secure digital payments, we offer complete financial solutions tailored for you.</p>
        <div class="grid">
            <div class="card">
                <img src="images/savings.jpg" alt="Savings Accounts">
                <div class="card-body">
                    <h3>Savings & Deposit Accounts</h3>
                    <p>Grow your wealth with competitive interest rates and flexible savings plans designed for your future.</p>
                    <button class="btn btn-outline" onclick="window.location.href='auth.php'">Open Savings Account</button>
                </div>
            </div>
            <div class="card">
                <img src="images/card-payment.jpg" alt="Digital Card Payments">
                <div class="card-body">
                    <h3>Digital Cards & Payments</h3>
                    <p>Pay securely with debit and credit cards, QR payments, and seamless online transactions anywhere in the world.</p>
                    <button class="btn btn-outline" onclick="window.location.href='auth.php'">Apply for a Card</button>
                </div>
            </div>
            <div class="card">
                <img src="images/accounting.jpg" alt="Loans and Financial Advisory">
                <div class="card-body">
                    <h3>Loans & Financial Advisory</h3>
                    <p>Get personal, home, and business loans with fast approvals and expert guidance from our financial advisors.</p>
                    <button class="btn btn-outline" onclick="window.location.href='auth.php'">Talk to an Advisor</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="section" id="about">
        <div class="about-grid">
            <img src="images/bank-building.jpg" alt="Bank Ashkona Headquarters">
            <div class="about-text">
                <h2>Trusted Banking, Since Day One</h2>
                <p>Bank Ashkona is a modern financial institution built on security, transparency, and innovation. We combine cutting-edge technology with personalized service to give you a banking experience you can rely on.</p>
                <ul class="check-list">
                    <li>Bank-grade 256-bit encryption on every transaction</li>
                    <li>Two-step identity verification for total peace of mind</li>
                    <li>Real-time account management and instant fund transfers</li>
                    <li>Dedicated relationship managers for premium customers</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- TEAM -->
    <section class="section" id="team">
        <h2 class="section-title">Meet Our Banking Team</h2>
        <p class="section-sub">Our experienced professionals are here to help you reach your financial goals with confidence.</p>
        <div class="team-grid">
            <div class="team-card">
                <img src="images/manager.jpg" alt="Managing Director">
                <h3>Meraj Islam</h3>
                <p class="role">Managing Director</p>
                <p class="desc">Leading our vision of secure, customer-first banking.</p>
            </div>
            <div class="team-card">
                <img src="images/staff-2.jpg" alt="Head of Customer Relations">
                <h3>Nusrat Jahan</h3>
                <p class="role">Head of Customer Relations</p>
                <p class="desc">Ensuring every customer feels valued and supported.</p>
            </div>
            <div class="team-card">
                <img src="images/staff-3.jpg" alt="Chief Financial Officer">
                <h3>Tanvir Ahmed</h3>
                <p class="role">Chief Financial Officer</p>
                <p class="desc">Managing finances with precision and accountability.</p>
            </div>
            <div class="team-card">
                <img src="images/staff-4.jpg" alt="Digital Banking Specialist">
                <h3>Sadia Islam</h3>
                <p class="role">Digital Banking Specialist</p>
                <p class="desc">Powering seamless and secure digital experiences.</p>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="section" id="contact">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Contact Our Support Team</h2>
                <p><strong>Head Office:</strong> House 12, Road 5, Block C, Ashkona, Dhaka 1230</p>
                <p><strong>Phone:</strong> +880 1700-000000</p>
                <p><strong>Email:</strong> support@bankashkona.com</p>
                <p><strong>Working Hours:</strong> Sunday - Thursday, 9:00 AM - 5:00 PM</p>
            </div>
            <form class="contact-form" onsubmit="event.preventDefault(); alert('Thank you! Our team will reach out to you shortly.');">
                <input type="text" placeholder="Your Full Name" required>
                <input type="email" placeholder="Your Email Address" required>
                <textarea rows="5" placeholder="How can we help you?" required></textarea>
                <button type="submit" class="btn btn-solid" style="width: 100%;">Send Message</button>
            </form>
        </div>
    </section>

    <footer>
        <p>© 2026 Bank Ashkona. All rights reserved. | <a href="home.php">Home</a> | <a href="#contact">Contact</a></p>
    </footer>

</body>
</html>