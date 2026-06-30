<?php
session_start();
require 'db.php';

// STRICT SECURITY CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: logout.php");
    exit();
}

$admin_name = $_SESSION['name'];
$message = "";

// --- ROUTING ENGINE ---
$view = isset($_GET['view']) ? $_GET['view'] : 'overview';

// --- ACTION HANDLING ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Assign Account Action
    if (isset($_POST['assign_account'])) {
        $target_user_id = (int)$_POST['target_user_id'];
        $check_sql = "SELECT * FROM Accounts WHERE User_ID = $target_user_id";
        
        if ($conn->query($check_sql)->num_rows > 0) {
            $message = "<div class='alert alert-error'>Error: User already has an account.</div>";
        } else {
            $new_account_id = "AC-" . rand(10000, 99999);
            $insert_sql = "INSERT INTO Accounts (Account_ID, User_ID, Current_Balance, Account_Type) VALUES ('$new_account_id', $target_user_id, 500.00, 'Savings')";
            if ($conn->query($insert_sql) === TRUE) {
                $message = "<div class='alert alert-success'>Account <strong>$new_account_id</strong> assigned with a ৳500.00 bonus.</div>";
            }
        }
    }

    // 2. Deposit Action
    if (isset($_POST['deposit_funds'])) {
        $target_account = $conn->real_escape_string($_POST['target_account']);
        $amount = (float)$_POST['deposit_amount'];
        
        if ($amount > 0) {
            $update_sql = "UPDATE Accounts SET Current_Balance = Current_Balance + $amount WHERE Account_ID = '$target_account'";
            if ($conn->query($update_sql) === TRUE) {
                $log_sql = "INSERT INTO Transactions (Sender_Account, Receiver_Account, Amount, Transaction_Type, Status) 
                            VALUES ('SYS-00000', '$target_account', $amount, 'Deposit', 'Completed')";
                $conn->query($log_sql);
                $message = "<div class='alert alert-success'>Successfully deposited ৳" . number_format($amount, 2) . " into $target_account.</div>";
            }
        }
    }

    // 3. NEW: Toggle Account Status Action (The Kill Switch)
    if (isset($_POST['toggle_status'])) {
        $target_account = $conn->real_escape_string($_POST['target_account']);
        $new_status = $conn->real_escape_string($_POST['new_status']);
        
        // Security check to prevent locking the master system account
        if ($target_account !== 'SYS-00000') {
            $update_sql = "UPDATE Accounts SET Status = '$new_status' WHERE Account_ID = '$target_account'";
            if ($conn->query($update_sql) === TRUE) {
                $status_color = $new_status == 'Active' ? 'success' : 'error';
                $message = "<div class='alert alert-$status_color'>Account <strong>$target_account</strong> has been marked as $new_status.</div>";
            } else {
                $message = "<div class='alert alert-error'>Database Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bank Ashkona</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        :root { --primary: #0a2540; --secondary: #24b47e; --bg: #f4f7f6; --text: #333; --danger: #e25950; }
        body { background-color: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 250px; background-color: var(--primary); color: white; padding: 20px; display: flex; flex-direction: column; }
        .sidebar h2 { margin-bottom: 30px; font-size: 1.5rem; text-align: center; border-bottom: 1px solid #ffffff40; padding-bottom: 10px; color: var(--secondary); }
        .nav-links { list-style: none; flex-grow: 1; }
        .nav-links li { margin-bottom: 10px; background-color: #ffffff10; border-radius: 5px; transition: 0.3s; }
        .nav-links li.active { border-left: 4px solid var(--secondary); background-color: rgba(255,255,255,0.05); }
        .nav-links li a { color: white; text-decoration: none; display: block; padding: 15px; font-weight: bold; }
        .nav-links li:hover { background-color: #ffffff20; }
        
        .main { flex: 1; padding: 40px; max-width: 1400px; overflow-y: auto; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #ccc; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: flex-end; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 15px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background-color: var(--primary); color: white; }
        tr:hover { background-color: #f9f9f9; }
        
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .alert-success { color: var(--secondary); background: #e8f8f2; }
        .alert-error { color: var(--danger); background: #fceceb; }
        
        .badge { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.9rem; display: inline-block; }
        .badge-blue { color: var(--primary); background: #eef2f5; }
        .badge-green { color: var(--secondary); background: #e8f8f2; }
        .badge-red { color: white; background: var(--danger); }
        
        .action-form { display: flex; gap: 10px; align-items: center; margin: 0; }
        .action-form input[type="number"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100px; }
        .btn-small { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; transition: 0.2s; }
        .btn-primary { background: var(--primary); }
        .btn-secondary { background: var(--secondary); }
        .btn-danger { background: var(--danger); }
        .btn-danger:hover { opacity: 0.8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Console</h2>
        <ul class="nav-links">
            <li class="<?= $view == 'overview' ? 'active' : '' ?>"><a href="?view=overview">Overview</a></li>
            <li class="<?= $view == 'manage' ? 'active' : '' ?>"><a href="?view=manage">Manage Accounts</a></li>
            <li class="<?= $view == 'logs' ? 'active' : '' ?>"><a href="?view=logs">System Logs</a></li>
            <li style="margin-top: 40px; background-color: var(--danger);"><a href="logout.php">Secure Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <div>
                <h2>Authorized Access: <?php echo htmlspecialchars($admin_name); ?></h2>
                <p>System Status: Active</p>
            </div>
            <h3 style="color: var(--primary); text-transform: uppercase;"><?php echo $view; ?></h3>
        </div>

        <?= $message ?>

        <?php 
        // ==========================================
        // VIEW: OVERVIEW
        // ==========================================
        if ($view == 'overview'): 
            $sql = "SELECT Users.User_ID, Users.Name, Users.Email, Accounts.Account_ID, Accounts.Current_Balance 
                    FROM Users LEFT JOIN Accounts ON Users.User_ID = Accounts.User_ID 
                    WHERE Users.Role = 'Customer' ORDER BY Users.Created_At DESC";
            $customers = $conn->query($sql);
        ?>
            <h3>User & Account Assignment</h3>
            <table>
                <thead><tr><th>User ID</th><th>Name</th><th>Account Number</th><th>Balance</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($row = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['User_ID']; ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <?php if(!empty($row['Account_ID'])): ?>
                            <td><span class="badge badge-blue"><?php echo $row['Account_ID']; ?></span></td>
                            <td style="font-weight: bold;">৳ <?php echo number_format($row['Current_Balance'], 2); ?></td>
                            <td>
                                <form method="POST" action="admin_dashboard.php?view=overview" class="action-form">
                                    <input type="hidden" name="target_account" value="<?php echo $row['Account_ID']; ?>">
                                    <input type="number" name="deposit_amount" min="1" step="0.01" placeholder="Amount" required>
                                    <button type="submit" name="deposit_funds" class="btn-small btn-primary">Deposit</button>
                                </form>
                            </td>
                        <?php else: ?>
                            <td style="color: #999;">Pending</td><td style="color: #999;">---</td>
                            <td>
                                <form method="POST" action="admin_dashboard.php?view=overview" class="action-form">
                                    <input type="hidden" name="target_user_id" value="<?php echo $row['User_ID']; ?>">
                                    <button type="submit" name="assign_account" class="btn-small btn-secondary">Assign Account</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php 
        // ==========================================
        // VIEW: MANAGE ACCOUNTS 
        // ==========================================
        elseif ($view == 'manage'): 
            // UPDATED QUERY: Now selects the new Status column
            $sql = "SELECT a.Account_ID, a.Account_Type, a.Current_Balance, a.Status, u.Name 
                    FROM Accounts a JOIN Users u ON a.User_ID = u.User_ID 
                    WHERE a.Account_ID != 'SYS-00000' ORDER BY a.Account_ID ASC";
            $accounts = $conn->query($sql);
        ?>
            <h3>Active Bank Accounts Directory</h3>
            <table>
                <thead><tr><th>Account Number</th><th>Owner Name</th><th>Account Type</th><th>Current Balance</th><th>Status</th><th>Access Control</th></tr></thead>
                <tbody>
                    <?php while($row = $accounts->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge badge-blue"><?php echo $row['Account_ID']; ?></span></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo $row['Account_Type']; ?></td>
                        <td style="font-weight: bold; color: var(--secondary);">৳ <?php echo number_format($row['Current_Balance'], 2); ?></td>
                        
                        <td>
                            <?php if($row['Status'] == 'Active'): ?>
                                <span class="badge badge-green">Active</span>
                            <?php else: ?>
                                <span class="badge badge-red">Inactive</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <form method="POST" action="admin_dashboard.php?view=manage" style="margin: 0;">
                                <input type="hidden" name="target_account" value="<?php echo $row['Account_ID']; ?>">
                                <?php if($row['Status'] == 'Active'): ?>
                                    <input type="hidden" name="new_status" value="Inactive">
                                    <button type="submit" name="toggle_status" class="btn-small btn-danger">Deactivate</button>
                                <?php else: ?>
                                    <input type="hidden" name="new_status" value="Active">
                                    <button type="submit" name="toggle_status" class="btn-small btn-secondary">Reactivate</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php 
        // ==========================================
        // VIEW: SYSTEM LOGS
        // ==========================================
        elseif ($view == 'logs'): 
            $sql = "SELECT * FROM Transactions ORDER BY Timestamp DESC LIMIT 100";
            $logs = $conn->query($sql);
        ?>
            <h3>Global Transaction Audit Log</h3>
            <table>
                <thead><tr><th>Txn ID</th><th>Date & Time</th><th>Sender</th><th>Receiver</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if($logs->num_rows > 0): ?>
                        <?php while($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['Transaction_ID']; ?></td>
                            <td style="color: #666; font-size: 0.9rem;"><?php echo date('M d, Y h:i A', strtotime($row['Timestamp'])); ?></td>
                            <td><?php echo $row['Sender_Account']; ?></td>
                            <td><?php echo $row['Receiver_Account']; ?></td>
                            <td>
                                <?php if($row['Transaction_Type'] == 'Deposit') echo "📥 Deposit";
                                      elseif($row['Transaction_Type'] == 'Transfer') echo "🔄 Transfer";
                                      else echo "📤 Withdrawal"; ?>
                            </td>
                            <td style="font-weight: bold;">৳ <?php echo number_format($row['Amount'], 2); ?></td>
                            <td><span class="badge badge-green"><?php echo $row['Status']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: #999;">No transactions found in the system.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</body>
</html>