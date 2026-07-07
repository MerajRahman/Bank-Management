<?php
session_start();
require 'db.php';

// SECURITY CHECK: If there is no active session, kick them out to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Grab the user's data from the secure session
$user_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// Check if the user has an active bank account
$balance = 0.00;
$account_num = "Pending Account Assignment";

$sql = "SELECT * FROM Accounts WHERE User_ID = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $account = $result->fetch_assoc();
    $balance = $account['Current_Balance'];
    $account_num = $account['Account_ID'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | Bank Ashkona</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        :root { --primary: #0a2540; --secondary: #0066cc; --bg: #f4f7f6; --text: #333; }
        body { background-color: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; background-color: var(--primary); color: white; padding: 20px; }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; text-align: center; border-bottom: 1px solid #ffffff40; padding-bottom: 10px; }
        .nav-links { list-style: none; }
        .nav-links li { padding: 15px; margin-bottom: 10px; background-color: #ffffff10; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .nav-links li:hover, .nav-links li.active { background-color: var(--secondary); }
        .nav-links li a { color: white; text-decoration: none; display: block; }
        
        .main { flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        .balance-card { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 30px; width: fit-content; }
        .balance-card h3 { font-weight: 400; margin-bottom: 10px; opacity: 0.9; }
        .balance-card h1 { font-size: 2.5rem; letter-spacing: 1px; }
        
        .transfer-section { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); max-width: 500px; }
        .transfer-section h2 { margin-bottom: 20px; color: var(--primary); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Bank Ashkona</h2>
        <ul class="nav-links">
            <li class="active">Dashboard</li>
            <li>Transfer Funds</li>
            <li>Transaction History</li>
            <li>Settings</li>
            <li style="background-color: #e25950;"><a href="logout.php">Secure Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p>Account: <strong><?php echo htmlspecialchars($account_num); ?></strong></p>
        </div>

        <div class="balance-card">
            <h3>Available Balance</h3>
            <h1>৳ <?php echo number_format($balance, 2); ?></h1>
        </div>

        <div class="transfer-section">
            <h2>System Status</h2>
            <p>Your secure session is active. Database connection established.</p>
        </div>
    </div>

</body>
</html>