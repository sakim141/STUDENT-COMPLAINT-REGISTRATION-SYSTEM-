<?php
session_start();
require_once "connection.php";

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: admin_login.php");
    exit;
}

// Admin name
$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Dashboard counts
$totalstudent = $conn->query("SELECT COUNT(*) as total FROM student")->fetch_assoc()['total'];
$pendingComplaints = $conn->query("SELECT COUNT(*) as total FROM complaints WHERE state='Pending'")->fetch_assoc()['total'];
$resolvedComplaints = $conn->query("SELECT COUNT(*) as total FROM complaints WHERE state='Resolved'")->fetch_assoc()['total'];
$totalComplaints = $conn->query("SELECT COUNT(*) as total FROM complaints")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<style>
body { display:flex; min-height:100vh; font-family:Arial,sans-serif; margin:0; background:#f4f4f9; }
.sidebar { width:250px; background:#2c3e50; color:#ecf0f1; display:flex; flex-direction:column; }
.sidebar h2 { text-align:center; padding:20px 0; border-bottom:1px solid #34495e; }
.sidebar a { padding:15px 20px; color:#ecf0f1; text-decoration:none; transition:0.3s; }
.sidebar a:hover { background:#34495e; }
.main-content { flex:1; padding:20px; }
.header { background:#fff; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.1); margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
.header h1 { font-size:24px; color:#2c3e50; }
.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
.card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; }
.card h3 { margin-bottom:10px; color:#2c3e50; }
.card p { font-size:18px; color:#16a085; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="all_complaints.php">All Complaints</a>
    <a href="admin_dashboard.php?action=logout">Logout</a>
</div>

<div class="main-content">
    <div class="header">
        <h1>Dashboard</h1>
        <span><?php echo htmlspecialchars($adminName); ?></span>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Student</h3>
            <p><?php echo $totalstudent; ?></p>
        </div>
        <div class="card">
            <h3>Total Complaints</h3>
            <p><?php echo $totalComplaints; ?></p>
        </div>
        <div class="card">
            <h3>Pending Complaints</h3>
            <p><?php echo $pendingComplaints; ?></p>
        </div>
        <div class="card">
            <h3>Resolved Complaints</h3>
            <p><?php echo $resolvedComplaints; ?></p>
        </div>
    </div>
</div>

</body>
</html>
