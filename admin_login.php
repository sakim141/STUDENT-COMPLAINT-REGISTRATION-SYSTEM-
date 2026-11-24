<?php
session_start();
require_once "connection.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT id, fullname, password 
         FROM admins 
         WHERE username = ? AND status = 1"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();
        echo '<pre>';


   if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['fullname'];

            header("Location: admin_dashboard.php");
            exit;

        } else {
            echo "❌ Wrong password";
        }

    } else {
        echo "❌ Admin not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Roboto', sans-serif; }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #a0c4ff;
        }
        .login-card {
            background: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 350px;
            color: #fff;
            text-align: center;
        }
        .login-card h2 {
            margin-bottom: 30px;
            font-weight: 400;
        }
        .login-card input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            font-size: 16px;
        }
        .login-card input::placeholder { color: #ddd; }
        .login-card button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            background: #4a69bd;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-card button:hover { background: #3b5a9b; }
        .login-card .error { margin-top: 15px; color: #ff6b6b; }
        .login-card .lost-password { font-size: 12px; color: #ccc; text-decoration: none; float: right; margin-top: 5px; }
        .input-icon {
            position: relative;
        }
        .input-icon input {
            padding-left: 40px;
        }
        .input-icon i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
        }
    </style>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="login-card">
    <h2>ADMIN LOGIN</h2>
    <form method="POST">
        <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <a href="#" class="lost-password">Lost Password?</a>
        <button type="submit" name="login">Login</button>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
    </form>
</div>

</body>
</html>
