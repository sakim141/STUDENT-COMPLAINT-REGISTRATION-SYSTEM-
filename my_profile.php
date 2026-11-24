<?php
session_start();
require_once "connection.php";

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: /backend/index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT fullname, email, department, course FROM student WHERE student_id=?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$student_id) die("User not found!");

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname   = trim($_POST['fullname']);
    $email      = trim($_POST['email']);
    $department = trim($_POST['department']);
    $course     = trim($_POST['course']);

    $stmt = $conn->prepare("UPDATE student SET fullname=?, email=?, department=?, course=? WHERE student_id=?");
    $stmt->bind_param("ssssi", $fullname, $email, $department, $course, $student_id);

    if($stmt->execute()){
        $success = "Profile updated successfully!";
        $user['fullname'] = $fullname;
        $user['email'] = $email;
        $user['department'] = $department;
        $user['course'] = $course;
    } else {
        $error = "Database update failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Profile - Complaint System</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
body{display:flex;background:#7a7a7a;min-height:100vh;}

/* Sidebar */
.sidebar{
    width:220px;
    background:#2f2f2f;
    color:#fff;
    padding:20px;
    display:flex;
    flex-direction:column;
}
.sidebar .profile-box{
    border:2px solid #cfc27a;
    padding:15px;
    text-align:center;
    margin-bottom:30px;
    font-weight:bold;
}
.sidebar a{
    color:#fff;
    text-decoration:none;
    padding:10px 15px;
    margin-bottom:10px;
    border-radius:5px;
    transition:0.3s;
}
.sidebar a:hover{
    background:#4a90e2;
}

/* Main content */
.main{
    flex:1;
    padding:30px;
}
.header{
    background:#8a8a8a;
    padding:15px;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    letter-spacing:1px;
    margin-bottom:30px;
}
.section-title{
    border:1px solid #cfc27a;
    padding:10px;
    margin-bottom:15px;
    width:60%;
    background:transparent;
}
.card{
    border:1px solid #cfc27a;
    padding:20px;
    width:60%;
    background:#bfbfbf;
    margin-bottom:40px;
    line-height:1.8;
}
.card input{
    width:100%;
    padding:6px;
    margin:4px 0;
    border:1px solid #999;
    border-radius:5px;
}
.save-btn{
    background:#4a90e2;
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    cursor:pointer;
    transition:background 0.3s;
    margin-top:10px;
}
.save-btn:hover{background:#357ABD;}
.complaint-btn{
    background:#4CAF50;
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    cursor:pointer;
    transition:background 0.3s;
    margin-top:10px;
    margin-left:10px;
}
.complaint-btn:hover{background:#388E3C;}
.success{color:green;margin-bottom:10px;}
.error{color:red;margin-bottom:10px;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="profile-box">
        <?= htmlspecialchars($student['fullname']) ?><br>ID: <?= $student_id ?>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="my_profile.php">Profile</a>
   
   
</div>

<!-- Main Content -->
<div class="main">
    <div class="header">STUDENT PERSONAL DETAILS</div>

    <?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
    <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

    <form method="POST">
        <div class="card">
            <p><strong>Full Name:</strong> <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required></p>
            <p><strong>Email:</strong> <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></p>
            <p><strong>Department:</strong> <input type="text" name="department" value="<?= htmlspecialchars($user['department']) ?>"></p>
            <p><strong>Course:</strong> <input type="text" name="course" value="<?= htmlspecialchars($user['course']) ?>"></p>
        </div>
        <button class="save-btn" type="submit">Save Changes</button>
    
    </form>
</div>

</body>
</html>
