<?php
session_start();
require 'db.php';

// SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$user_name = $_SESSION['name'];
$user_id   = (int)$_SESSION['user_id'];
$message   = '';

function ok($text) { return "<div class='alert alert-success'>$text</div>"; }
function err($text) { return "<div class='alert alert-error'>$text</div>"; }

// Load the user's profile
$profile = ['Name' => '', 'Email' => '', 'Phone' => '', 'Address' => ''];
$stmt = $conn->prepare("SELECT Name, Email, Phone, Address FROM Users WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($p_name, $p_email, $p_phone, $p_address);
$stmt->fetch();
$stmt->close();
$profile = ['Name' => $p_name, 'Email' => $p_email, 'Phone' => $p_phone, 'Address' => $p_address];

// Load the user's bank account (if assigned)
$account = null;
$balance = 0.00;
$account_num = "Pending Account Assignment";
$account_type = "---";
$account_status = "---";

$stmt = $conn->prepare("SELECT * FROM Accounts WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $account = $res->fetch_assoc();
    $balance       = (float)$account['Current_Balance'];
    $account_num   = $account['Account_ID'];
    $account_type  = $account['Account_Type'];
    $account_status = $account['Status'];
}
$stmt->close();

$view = isset($_GET['view']) ? $_GET['view'] : 'overview';

// ============================================================
// FORM HANDLING
// ============================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. TRANSFER FUNDS
    if (isset($_POST['transfer_funds'])) {
        if (!$account) {
            $message = err("You don't have a bank account yet. Please contact the bank admin to assign one.");
        } elseif ($account_status !== 'Active') {
            $message = err("Your account is inactive. Please contact support.");
        } else {
            $receiver = trim($_POST['receiver_account']);
            $amount   = (float)$_POST['amount'];

            $errors = [];
            if (empty($receiver)) $errors[] = "Receiver account number is required.";
            if ($receiver === $account_num) $errors[] = "You cannot transfer funds to your own account.";
            if ($receiver === 'SYS-00000') $errors[] = "Transfers to the system account are not allowed.";
            if ($amount <= 0) $errors[] = "Amount must be greater than 0.";
            if ($amount > $balance) $errors[] = "Insufficient balance for this transfer.";

            if (count($errors) > 0) {
                $message = err(implode("<br>", $errors));
            } else {
<<<<<<< HEAD
=======
                // Verify receiver exists and is active
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
                $chk = $conn->prepare("SELECT Account_ID FROM Accounts WHERE Account_ID = ? AND Status = 'Active'");
                $chk->bind_param("s", $receiver);
                $chk->execute();
                $chk->store_result();
                if ($chk->num_rows == 0) {
                    $message = err("Receiver account not found or is inactive. Please check the account number.");
                } else {
<<<<<<< HEAD
                    $deduct = $conn->prepare("UPDATE Accounts SET Current_Balance = Current_Balance - ? WHERE Account_ID = ? AND Current_Balance >= ? AND Status = 'Active'");
                    $deduct->bind_param("dsd", $amount, $account_num, $amount);

                    $credit = $conn->prepare("UPDATE Accounts SET Current_Balance = Current_Balance + ? WHERE Account_ID = ?");
                    $credit->bind_param("ds", $amount, $receiver);

=======
                    // Atomic debit (only succeeds with enough balance on an active account)
                    $deduct = $conn->prepare("UPDATE Accounts SET Current_Balance = Current_Balance - ? WHERE Account_ID = ? AND Current_Balance >= ? AND Status = 'Active'");
                    $deduct->bind_param("dsd", $amount, $account_num, $amount);

                    // Credit the receiver
                    $credit = $conn->prepare("UPDATE Accounts SET Current_Balance = Current_Balance + ? WHERE Account_ID = ?");
                    $credit->bind_param("ds", $amount, $receiver);

                    // Log the transaction
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
                    $log = $conn->prepare("INSERT INTO Transactions (Sender_Account, Receiver_Account, Amount, Transaction_Type, Status) VALUES (?, ?, ?, 'Transfer', 'Completed')");
                    $log->bind_param("ssd", $account_num, $receiver, $amount);

                    $conn->begin_transaction();
                    try {
                        $deduct->execute();
<<<<<<< HEAD
                        if ($deduct->affected_rows != 1) throw new Exception("Insufficient balance or your account is inactive.");
                        $credit->execute();
                        if ($credit->errno) throw new Exception("Failed to credit the receiver account.");
                        $log->execute();
                        if ($log->errno) throw new Exception("Failed to record the transaction.");
=======
                        if ($deduct->affected_rows != 1) {
                            throw new Exception("Insufficient balance or your account is inactive.");
                        }
                        $credit->execute();
                        if ($credit->errno) {
                            throw new Exception("Failed to credit the receiver account.");
                        }
                        $log->execute();
                        if ($log->errno) {
                            throw new Exception("Failed to record the transaction.");
                        }
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
                        $conn->commit();
                        $balance -= $amount;
                        $message = ok("৳" . number_format($amount, 2) . " transferred to <strong>$receiver</strong> successfully.");
                    } catch (Exception $e) {
                        $conn->rollback();
                        $message = err("Transfer failed: " . $e->getMessage());
                    }
                }
                $chk->close();
            }
        }
    }

    // 2. UPDATE PROFILE
    if (isset($_POST['update_profile'])) {
        $name    = trim($_POST['profile_name']);
        $email   = trim($_POST['profile_email']);
        $phone   = trim($_POST['profile_phone']);
        $address = trim($_POST['profile_address']);

        $errors = [];
        if (strlen($name) < 3) $errors[] = "Full Name must be at least 3 characters long.";
<<<<<<< HEAD
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
        if (!preg_match("/^[0-9]{11}$/", $phone)) $errors[] = "Contact number must be exactly 11 digits.";
=======
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format. Please include an '@' and a valid domain.";
        if (!preg_match("/^[0-9]{11}$/", $phone)) $errors[] = "Contact number must be exactly 11 digits (e.g., 01712345678).";
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
        if (empty($address)) $errors[] = "Address cannot be empty.";

        if (count($errors) > 0) {
            $message = err(implode("<br>", $errors));
        } else {
            $dup = $conn->prepare("SELECT User_ID FROM Users WHERE Email = ? AND User_ID != ?");
            $dup->bind_param("si", $email, $user_id);
            $dup->execute();
            $dup->store_result();
            if ($dup->num_rows > 0) {
                $message = err("That email is already registered to another account.");
            } else {
                $upd = $conn->prepare("UPDATE Users SET Name = ?, Email = ?, Phone = ?, Address = ? WHERE User_ID = ?");
                $upd->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
                $upd->execute();
                if ($upd->errno == 0) {
                    $_SESSION['name'] = $name;
                    $user_name = $name;
                    $message = ok("Profile updated successfully.");
                } else {
                    $message = err("Database error while updating your profile.");
                }
            }
            $dup->close();
        }
    }

    // 3. CHANGE PASSWORD
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];
        $confirm = $_POST['new_password_confirm'];

        $pw = $conn->prepare("SELECT Password_Hash FROM Users WHERE User_ID = ?");
        $pw->bind_param("i", $user_id);
        $pw->execute();
        $pw->bind_result($hash);
        $pw->fetch();
        $pw->close();

        $errors = [];
        if (!password_verify($current, $hash)) $errors[] = "Current password is incorrect.";
        if (strlen($new) < 8) $errors[] = "New password must be at least 8 characters long.";
<<<<<<< HEAD
        if (!preg_match('/[A-Z]/', $new)) $errors[] = "Must contain at least one uppercase letter.";
        if (!preg_match('/[^a-zA-Z0-9]/', $new)) $errors[] = "Must contain at least one special character.";
=======
        if (!preg_match('/[A-Z]/', $new)) $errors[] = "New password must contain at least one uppercase letter.";
        if (!preg_match('/[^a-zA-Z0-9]/', $new)) $errors[] = "New password must contain at least one special character (e.g., @, #, $, %).";
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
        if ($new !== $confirm) $errors[] = "New passwords do not match.";

        if (count($errors) > 0) {
            $message = err(implode("<br>", $errors));
        } else {
            $new_hash = password_hash($new, PASSWORD_BCRYPT);
            $upd = $conn->prepare("UPDATE Users SET Password_Hash = ? WHERE User_ID = ?");
            $upd->bind_param("si", $new_hash, $user_id);
            $upd->execute();
<<<<<<< HEAD
            $message = $upd->errno == 0 ? ok("Password changed successfully.") : err("Database error.");
        }
    }

    // 4. APPLY FOR CARD (NEW)
    if (isset($_POST['apply_card'])) {
        if (!$account || $account_status !== 'Active') {
            $message = err("You need an active account to apply for a card.");
        } else {
            $type = $_POST['card_type'];
            if (in_array($type, ['Debit', 'Credit'])) {
                // Check if they already have an active or pending card of this type
                $chk = $conn->prepare("SELECT Request_ID FROM Cards WHERE User_ID = ? AND Card_Type = ? AND Status != 'Blocked'");
                $chk->bind_param("is", $user_id, $type);
                $chk->execute();
                $chk->store_result();
                if ($chk->num_rows > 0) {
                    $message = err("You already have an active or pending $type card.");
                } else {
                    $ins = $conn->prepare("INSERT INTO Cards (User_ID, Account_ID, Card_Type) VALUES (?, ?, ?)");
                    $ins->bind_param("iss", $user_id, $account_num, $type);
                    if ($ins->execute()) {
                        $message = ok("Your $type card application has been submitted and is pending approval.");
                    } else {
                        $message = err("Database error: " . $conn->error);
                    }
                }
                $chk->close();
            } else {
                $message = err("Invalid card type selected.");
            }
=======
            $message = $upd->errno == 0 ? ok("Password changed successfully.") : err("Database error while changing your password.");
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
        }
    }
}

<<<<<<< HEAD
// Recent transactions for overview
=======
// Recent transactions for the overview (only when an account exists)
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
$recent = [];
if ($account) {
    $esc = $conn->real_escape_string($account_num);
    $r = $conn->query("SELECT * FROM Transactions WHERE Sender_Account = '$esc' OR Receiver_Account = '$esc' ORDER BY Timestamp DESC LIMIT 8");
    while ($row = $r->fetch_assoc()) { $recent[] = $row; }
<<<<<<< HEAD
}

// Get User's Cards
$my_cards = [];
if ($account) {
    $r = $conn->query("SELECT * FROM Cards WHERE User_ID = $user_id ORDER BY Applied_On DESC");
    while ($row = $r->fetch_assoc()) { $my_cards[] = $row; }
=======
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
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
        :root { --primary: #0a2540; --secondary: #24b47e; --bg: #f4f7f6; --text: #333; --danger: #e25950; }

        html, body { overflow-x: hidden; width: 100%; }
        body { background-color: var(--bg); color: var(--text); display: flex; flex-direction: column; min-height: 100vh; }

        .sidebar { width: 100%; background-color: var(--primary); color: white; padding: 20px; display: flex; flex-direction: column; }
        .sidebar h2 { margin-bottom: 20px; font-size: 1.5rem; text-align: center; border-bottom: 1px solid #ffffff40; padding-bottom: 10px; color: var(--secondary); }
        .nav-links { list-style: none; flex-grow: 1; display: flex; flex-direction: column; gap: 8px; }
        .nav-links li { background-color: #ffffff10; border-radius: 5px; transition: 0.3s; }
        .nav-links li.active { border-left: 4px solid var(--secondary); background-color: rgba(255,255,255,0.05); }
        .nav-links li a { color: white; text-decoration: none; display: block; padding: 12px; font-weight: bold; }
        .nav-links li:hover { background-color: #ffffff20; }

        .main { width: 100%; padding: 20px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #ccc; padding-bottom: 10px; display: flex; flex-direction: column; gap: 10px; }

        .balance-card { background: linear-gradient(135deg, var(--primary), #1a6db5); color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .balance-card h3 { font-weight: 400; margin-bottom: 8px; opacity: 0.9; }
        .balance-card h1 { font-size: 2.4rem; letter-spacing: 1px; }
        .balance-meta { margin-top: 10px; font-size: 0.9rem; opacity: 0.9; display: flex; flex-wrap: wrap; gap: 15px; }

        .panel { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .panel h2 { margin-bottom: 20px; color: var(--primary); }

        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .alert-success { color: var(--secondary); background: #e8f8f2; }
        .alert-error { color: var(--danger); background: #fceceb; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #555; }
<<<<<<< HEAD
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; transition: border 0.3s; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--secondary); }
=======
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; transition: border 0.3s; }
        .form-group input:focus { outline: none; border-color: var(--secondary); }
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1

        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #12304f; }
        .btn-secondary { background: var(--secondary); color: white; }
        .btn-secondary:hover { background: #1e996a; }

        .badge { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; display: inline-block; }
        .badge-blue { color: var(--primary); background: #eef2f5; }
        .badge-green { color: var(--secondary); background: #e8f8f2; }
        .badge-red { color: white; background: var(--danger); }
        .badge-gray { color: #666; background: #eee; }

        .table-responsive { width: 100%; overflow-x: auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; min-width: 650px; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background-color: var(--primary); color: white; }
        tr:hover { background-color: #f9f9f9; }

        .settings-grid { display: grid; grid-template-columns: 1fr; gap: 25px; }
        .settings-grid .panel { margin-bottom: 0; }

        .muted { color: #999; }
        .notice { background: #fff8e1; border: 1px solid #f0d264; color: #7a6a00; padding: 14px; border-radius: 6px; margin-bottom: 20px; }

        @media (min-width: 768px) {
            body { flex-direction: row; }
            .sidebar { width: 250px; min-height: 100vh; }
            .main { flex: 1; padding: 40px; }
            .header { flex-direction: row; justify-content: space-between; align-items: flex-end; }
            .settings-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Bank Ashkona</h2>
        <ul class="nav-links">
            <li><a href="home.php">🏠 Home</a></li>
            <li class="<?= $view == 'overview' ? 'active' : '' ?>"><a href="dashboard.php?view=overview">Dashboard</a></li>
<<<<<<< HEAD
            <li class="<?= $view == 'cards' ? 'active' : '' ?>"><a href="dashboard.php?view=cards">My Cards</a></li>
=======
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
            <li class="<?= $view == 'transfer' ? 'active' : '' ?>"><a href="dashboard.php?view=transfer">Transfer Funds</a></li>
            <li class="<?= $view == 'history' ? 'active' : '' ?>"><a href="dashboard.php?view=history">Transaction History</a></li>
            <li class="<?= $view == 'settings' ? 'active' : '' ?>"><a href="dashboard.php?view=settings">Settings</a></li>
            <li style="margin-top: auto; background-color: var(--danger);"><a href="logout.php">Secure Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <div>
                <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <p>Account: <strong><?php echo htmlspecialchars($account_num); ?></strong></p>
            </div>
            <h3 style="color: var(--primary); text-transform: uppercase;"><?php echo htmlspecialchars($view); ?></h3>
        </div>

        <?= $message ?>

        <?php if (!$account): ?>
            <div class="notice">
<<<<<<< HEAD
                <strong>No bank account assigned yet.</strong> Your funds transfer and card services will be available once the bank admin assigns an account to you.
=======
                <strong>No bank account assigned yet.</strong> Your funds transfer and transaction history will be available once the bank admin assigns an account to you.
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
            </div>
        <?php endif; ?>

        <?php
        // ==========================================
        // VIEW: OVERVIEW
        // ==========================================
        if ($view == 'overview'): ?>
            <div class="balance-card">
                <h3>Available Balance</h3>
                <h1>৳ <?php echo number_format($balance, 2); ?></h1>
                <?php if ($account): ?>
                <div class="balance-meta">
                    <span>Type: <strong><?php echo $account_type; ?></strong></span>
                    <span>Status:
                        <?php if ($account_status == 'Active'): ?>
                            <strong style="color: #69f0ae;">Active</strong>
                        <?php else: ?>
                            <strong style="color: #ff8a80;">Inactive</strong>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <h2>Recent Transactions</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>Date &amp; Time</th><th>Type</th><th>Counterparty</th><th>Direction</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent) > 0): ?>
                                <?php foreach ($recent as $tx): ?>
                                <tr>
                                    <td style="color: #666; font-size: 0.9rem;"><?php echo date('M d, Y h:i A', strtotime($tx['Timestamp'])); ?></td>
                                    <td>
                                        <?php if ($tx['Transaction_Type'] == 'Deposit'): ?>📥 Deposit
                                        <?php elseif ($tx['Transaction_Type'] == 'Transfer'): ?>🔄 Transfer
                                        <?php else: ?>📤 Withdrawal<?php endif; ?>
                                    </td>
                                    <td><?php echo ($tx['Sender_Account'] == $account_num) ? $tx['Receiver_Account'] : $tx['Sender_Account']; ?></td>
                                    <td>
                                        <?php if ($tx['Sender_Account'] == $account_num): ?>
                                            <span class="badge badge-red">Sent</span>
                                        <?php else: ?>
                                            <span class="badge badge-green">Received</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: bold;">
                                        <?php echo ($tx['Sender_Account'] == $account_num) ? '- ' : '+ '; ?>৳ <?php echo number_format($tx['Amount'], 2); ?>
                                    </td>
                                    <td><span class="badge badge-green"><?php echo $tx['Status']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="muted" style="text-align: center;">No transactions yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php
        // ==========================================
<<<<<<< HEAD
        // VIEW: MY CARDS (NEW)
        // ==========================================
        elseif ($view == 'cards'): ?>
            <div class="settings-grid">
                <!-- Apply for Card Form -->
                <div class="panel">
                    <h2>Apply for a New Card</h2>
                    <?php if (!$account || $account_status !== 'Active'): ?>
                        <p class="muted">You need an active bank account to request a card.</p>
                    <?php else: ?>
                        <form method="POST" action="dashboard.php?view=cards">
                            <div class="form-group">
                                <label>Select Linked Account</label>
                                <input type="text" value="<?php echo htmlspecialchars($account_num); ?> (Balance: ৳<?php echo number_format($balance, 2); ?>)" readonly style="background: #f0f0f0;">
                            </div>
                            <div class="form-group">
                                <label>Select Card Type</label>
                                <select name="card_type" required>
                                    <option value="">-- Choose Option --</option>
                                    <option value="Debit">Visa Debit Card</option>
                                    <option value="Credit">Mastercard Credit Card</option>
                                </select>
                            </div>
                            <button type="submit" name="apply_card" class="btn btn-secondary">Submit Application</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- View Existing Cards -->
                <div class="panel">
                    <h2>Your Digital Cards</h2>
                    <?php if (count($my_cards) > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($my_cards as $card): ?>
                                <div style="border: 1px solid #eee; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                                    <div>
                                        <h3 style="margin-bottom: 5px; color: var(--primary);"><?php echo $card['Card_Type']; ?> Card</h3>
                                        <p style="font-family: monospace; font-size: 1.1rem; color: #555; letter-spacing: 2px;">
                                            <?php 
                                            if ($card['Status'] == 'Active' && !empty($card['Card_Number'])) {
                                                echo htmlspecialchars($card['Card_Number']);
                                            } else {
                                                echo "****-****-****-****";
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <?php if ($card['Status'] == 'Active'): ?>
                                            <span class="badge badge-green">Active</span>
                                        <?php elseif ($card['Status'] == 'Pending'): ?>
                                            <span class="badge badge-blue">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-red">Blocked</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="muted">You do not have any cards linked to your account.</p>
                    <?php endif; ?>
                </div>
            </div>

        <?php
        // ==========================================
=======
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
        // VIEW: TRANSFER FUNDS
        // ==========================================
        elseif ($view == 'transfer'): ?>
            <div class="panel" style="max-width: 560px;">
                <h2>Transfer Funds</h2>
                <?php if (!$account): ?>
                    <p class="muted">You need an assigned bank account before you can transfer funds.</p>
                <?php elseif ($account_status !== 'Active'): ?>
                    <p class="muted">Your account is currently inactive. Please contact support to reactivate it.</p>
                <?php else: ?>
                    <p style="margin-bottom: 20px; color: #555;">
                        Available balance: <strong>৳ <?php echo number_format($balance, 2); ?></strong>
                    </p>
                    <form method="POST" action="dashboard.php?view=transfer">
                        <div class="form-group">
                            <label>Receiver Account Number</label>
                            <input type="text" name="receiver_account" required placeholder="e.g. AC-10002" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Amount (৳)</label>
                            <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0.00">
                        </div>
                        <button type="submit" name="transfer_funds" class="btn btn-secondary">Transfer Now</button>
                    </form>
                <?php endif; ?>
            </div>

        <?php
        // ==========================================
        // VIEW: TRANSACTION HISTORY
        // ==========================================
        elseif ($view == 'history'): ?>
            <?php if ($account): ?>
                <?php
                $esc = $conn->real_escape_string($account_num);
                $h = $conn->query("SELECT * FROM Transactions WHERE Sender_Account = '$esc' OR Receiver_Account = '$esc' ORDER BY Timestamp DESC");
                ?>
                <h3 style="color: var(--primary); margin-bottom: 5px;">Full Transaction History</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Date &amp; Time</th><th>Type</th><th>Counterparty</th><th>Direction</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($h->num_rows > 0): ?>
                                <?php while ($tx = $h->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $tx['Transaction_ID']; ?></td>
                                    <td style="color: #666; font-size: 0.9rem;"><?php echo date('M d, Y h:i A', strtotime($tx['Timestamp'])); ?></td>
                                    <td>
                                        <?php if ($tx['Transaction_Type'] == 'Deposit'): ?>📥 Deposit
                                        <?php elseif ($tx['Transaction_Type'] == 'Transfer'): ?>🔄 Transfer
                                        <?php else: ?>📤 Withdrawal<?php endif; ?>
                                    </td>
                                    <td><?php echo ($tx['Sender_Account'] == $account_num) ? $tx['Receiver_Account'] : $tx['Sender_Account']; ?></td>
                                    <td>
                                        <?php if ($tx['Sender_Account'] == $account_num): ?>
                                            <span class="badge badge-red">Sent</span>
                                        <?php else: ?>
                                            <span class="badge badge-green">Received</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: bold;">
                                        <?php echo ($tx['Sender_Account'] == $account_num) ? '- ' : '+ '; ?>৳ <?php echo number_format($tx['Amount'], 2); ?>
                                    </td>
                                    <td><span class="badge badge-green"><?php echo $tx['Status']; ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="muted" style="text-align: center;">No transactions found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="panel"><p class="muted">No transactions available until an account is assigned to you.</p></div>
            <?php endif; ?>

        <?php
        // ==========================================
        // VIEW: SETTINGS
        // ==========================================
        elseif ($view == 'settings'): ?>
            <div class="settings-grid">
                <div class="panel">
                    <h2>Personal Information</h2>
                    <form method="POST" action="dashboard.php?view=settings">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="profile_name" required minlength="3" value="<?php echo htmlspecialchars($profile['Name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="profile_email" required value="<?php echo htmlspecialchars($profile['Email']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
<<<<<<< HEAD
                            <input type="tel" name="profile_phone" required pattern="[0-9]{11}" title="Enter exactly 11 digits" value="<?php echo htmlspecialchars($profile['Phone']); ?>">
=======
                            <input type="tel" name="profile_phone" required pattern="[0-9]{11}" title="Enter exactly 11 digits (e.g. 01712345678)" value="<?php echo htmlspecialchars($profile['Phone']); ?>">
>>>>>>> a9ac621674ab526db2529f1835712d9e04e38ef1
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="profile_address" required value="<?php echo htmlspecialchars($profile['Address']); ?>">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <div class="panel">
                    <h2>Change Password</h2>
                    <form method="POST" action="dashboard.php?view=settings">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required autocomplete="current-password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required minlength="8" placeholder="Min 8 chars, 1 uppercase, 1 symbol">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="new_password_confirm" required minlength="8">
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
