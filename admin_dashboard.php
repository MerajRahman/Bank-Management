<?php
session_start();
require 'db.php';

// STRICT SECURITY CHECK: Only allow Admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: logout.php"); // Kick unauthorized users out
    exit();
}

$admin_name = $_SESSION['name'];
$message = "";

// --- NEW: ACCOUNT ASSIGNMENT LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_account'])) {
    $target_user_id = (int)$_POST['target_user_id'];
    
    // Safety check: Does this user already have an account?
    $check_sql = "SELECT * FROM Accounts WHERE User_ID = $target_user_id";
    $check_res = $conn->query($check_sql);
    
    if ($check_res->num_rows > 0) {
        $message = "<div style='color: #e25950; background: #fceceb; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;'>Error: User already has an active account.</div>";
    } else {
        // Generate a random 5-digit Account ID
        $new_account_id = "AC-" . rand(10000, 99999);
        
        // Insert the new account into the database with a 500 Taka Welcome Bonus
        $insert_sql = "INSERT INTO Accounts (Account_ID, User_ID, Current_Balance, Account_Type) 
                       VALUES ('$new_account_id', $target_user_id, 500.00, 'Savings')";
        
        if ($conn->query($insert_sql) === TRUE) {
            $message = "<div style='color: #24b47e; background: #e8f8f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;'>Success! Account <strong>$new_account_id</strong> assigned with a ৳500.00 welcome bonus.</div>";
        } else {
            $message = "<div style='color: #e25950; background: #fceceb; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;'>Database Error: " . $conn->error . "</div>";
        }
    }
}

// Fetch all registered customers
$customers = $conn->query("SELECT * FROM Users WHERE Role = 'Customer' ORDER BY Created_At DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bank Ashkona</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        :root { --primary: #0a2540; --secondary: #24b47e; --bg: #f4f7f6; --text: #333; }
        body { background-color: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; background-color: var(--primary); color: white; padding: 20px; }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; text-align: center; border-bottom: 1px solid #ffffff40; padding-bottom: 10px; color: var(--secondary); }
        .nav-links { list-style: none; }
        .nav-links li { padding: 15px; margin-bottom: 10px; background-color: #ffffff10; border-radius: 5px; cursor: pointer; }
        .nav-links li.active { border-left: 4px solid var(--secondary); background-color: rgba(255,255,255,0.05); }
        .nav-links li a { color: white; text-decoration: none; display: block; font-weight: bold; }
        
        .main { flex: 1; padding: 40px; max-width: 1200px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #ccc; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: var(--primary); color: white; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Console</h2>
        <ul class="nav-links">
            <li class="active">Overview</li>
            <li>Manage Accounts</li>
            <li>System Logs</li>
            <li style="background-color: #e25950; margin-top: 20px;"><a href="logout.php">Secure Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h2>Authorized Access: <?php echo htmlspecialchars($admin_name); ?></h2>
            <p>System Status: Active</p>
        </div>

        <?= $message ?>

        <h3>Registered Customers</h3>
        <br>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Account Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $customers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['User_ID']; ?></td>
                    <td><?php echo htmlspecialchars($row['Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Email']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($row['Created_At'])); ?></td>
                    <td>
                        <?php 
                            // Check if this specific user has an account
                            $uid = $row['User_ID'];
                            $acc_check = $conn->query("SELECT Account_ID FROM Accounts WHERE User_ID = $uid");
                            
                            if($acc_check->num_rows > 0): 
                                $acc_data = $acc_check->fetch_assoc();
                        ?>
                            <span style="color: var(--primary); font-weight: bold; padding: 5px 10px; background: #eef2f5; border-radius: 4px;">
                                <?php echo $acc_data['Account_ID']; ?>
                            </span>
                        <?php else: ?>
                            <form method="POST" action="admin_dashboard.php" style="margin: 0;">
                                <input type="hidden" name="target_user_id" value="<?php echo $row['User_ID']; ?>">
                                <button type="submit" name="assign_account" style="padding: 6px 12px; background: var(--secondary); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: 0.3s;">Assign Account</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>