<?php
session_start();
require_once "../backend/connection.php";

// Clear previous errors
$_SESSION['register_errors'] = [];
$_SESSION['login_errors'] = [];
unset($_SESSION['success']);

// Debug mode: set to true while testing login
$debug = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       REGISTER
    ========================= */
    if (isset($_POST['register'])) {

        $reg_errors = [];

        $fullname = trim($_POST['fullname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $department = trim($_POST['department'] ?? '');
        $course = trim($_POST['course'] ?? '');

        if (!preg_match("/^[A-Za-z ]{3,50}$/", $fullname))
            $reg_errors[] = "Full name must be 3–50 letters only.";

        if (!preg_match("/^[A-Za-z0-9_]{6,16}$/", $username))
            $reg_errors[] = "Username must be 6–16 characters.";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $reg_errors[] = "Invalid email address.";

        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password))
            $reg_errors[] = "Password must be at least 8 characters.";

        if ($password !== $confirm_password)
            $reg_errors[] = "Passwords do not match.";

        if (empty($department)) $reg_errors[] = "Department is required.";
        if (empty($course)) $reg_errors[] = "Course is required.";

        if (empty($reg_errors)) {
            $check = $conn->prepare("SELECT student_id FROM student WHERE email=? OR username=?");
            $check->bind_param("ss", $email, $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $reg_errors[] = "Email or username already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO student(fullname, username, email, password, department, course)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("ssssss", $fullname, $username, $email, $hashed, $department, $course);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "✅ Registration successful! Please login.";
                } else {
                    $reg_errors[] = "Database error during registration.";
                }
            }
        }

        $_SESSION['register_errors'] = $reg_errors;
        header("Location: ../backend/index.php");
        exit();
    }

    /* =========================
       LOGIN
    ========================= */
    if (isset($_POST['login'])) {

        $login_errors = [];

        $login_username = trim($_POST['login_username'] ?? '');
        $login_password = $_POST['login_password'] ?? '';

        if (empty($login_username))
            $login_errors[] = "Username is required.";

        if (empty($login_password))
            $login_errors[] = "Password is required.";

        if (empty($login_errors)) {
            $stmt = $conn->prepare("SELECT student_id, username, password FROM student WHERE username=?");
            $stmt->bind_param("s", $login_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $login_errors[] = "Username not found in database.";
            } else {
                $user = $result->fetch_assoc();

                if (password_verify($login_password, $user['password'])) {
                    // Correct login
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $login_errors[] = "Password does not match for this username.";
                }

                // Debug output
                if ($debug) {
                    echo "<pre>DEBUG INFO:\n";
                    echo "Form username: " . htmlspecialchars($login_username) . "\n";
                    echo "DB username: " . htmlspecialchars($user['username']) . "\n";
                    echo "DB hashed password: " . $user['password'] . "\n";
                    echo "Password verify result: " . (password_verify($login_password, $user['password']) ? "true" : "false") . "\n";
                    echo "</pre>";
                    exit();
                }
            }
        }

        $_SESSION['login_errors'] = $login_errors;
        header("Location: ../backend/index.php");
        exit();
    }
}
