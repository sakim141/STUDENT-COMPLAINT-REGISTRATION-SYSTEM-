<?php
session_start();

$openPopup = '';
if (isset($_SESSION['login_errors'])) {
  $openPopup = 'login-popup';
}
if (isset($_SESSION['register_errors']) || isset($_SESSION['login_success'])) {
  $openPopup = 'register-popup';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STUDENT COMPLAIN REGISTRATION SYSTEM</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
  <h2>STUDENT COMPLAIN REGISTRATION SYSTEM</h2>

  <nav>
    <a href="#">HOME</a>
    <a href="#">SERVICE</a>
    <a href="#">CONTACT</a>
    <a href="#">ABOUT</a>
  </nav>

  <div class="sign-in-up">
    <button onclick="popup('login-popup')">LOGIN</button>
    <button onclick="popup('register-popup')">REGISTER</button>
  </div>
</header>

<section class="home">
  <h1>Welcome to Student Complaint Registration System</h1>
  <p>Submit complaints online and track them easily.</p>
</section>

<!-- LOGIN -->
<div class="popup-container" id="login-popup">
  <div class="popup">
    <form method="POST" action="login_registration.php">
      <h2>
        <span>STUDENT LOGIN</span>
        <button type="button" onclick="popup('login-popup')">X</button>
      </h2>

      <?php if (isset($_SESSION['login_errors'])): ?>
        <div class="error">
          <?= htmlspecialchars(implode("<br>", $_SESSION['login_errors'])) ?>
        </div>
        <?php unset($_SESSION['login_errors']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
          <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <input type="text" name="login_username" placeholder="Username" required>
      <input type="password" name="login_password" placeholder="Password" required>

      <button type="submit" class="login-btn" name="login">LOGIN</button>
    </form>
  </div>
</div>

<!-- REGISTER -->
<div class="popup-container" id="register-popup">
  <div class="popup register">
    <form method="POST" action="login_registration.php">
      <h2>
        <span>STUDENT REGISTER</span>
        <button type="button" onclick="popup('register-popup')">X</button>
      </h2>

      <?php if (isset($_SESSION['register_errors'])): ?>
        <div class="error">
          <?= htmlspecialchars(implode("<br>", $_SESSION['register_errors'])) ?>
        </div>
        <?php unset($_SESSION['register_errors']); ?>
      <?php endif; ?>

      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <input type="text" name="department" placeholder="Department" required>
      <input type="text" name="course" placeholder="Course" required>

      <button type="submit" class="register-btn" name="register">REGISTER</button>
    </form>
  </div>
</div>
<footer>
  <div class="footer-container">
  <p>&copy; 2026 Student Complaint Registration System</p>
  <div class ="social-icons">
    <a href="#"><img src="facebook.png" alt="facebook"></a>
    <a href="#"><img src="twitter.png" alt="twitter"></a>
        <a href="#"><img src="instragram.png" alt="instragram"></a>
    
  </div>
</footer>

<script>
function popup(id) {
  document.querySelectorAll('.popup-container')
    .forEach(p => p.style.display = 'none');
  document.getElementById(id).style.display = 'flex';
}

<?php if ($openPopup): ?>
popup("<?= $openPopup ?>");
<?php endif; ?>
</script>

</body>
</html>
