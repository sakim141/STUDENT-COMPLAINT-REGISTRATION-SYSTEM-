<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard - Complaint System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
 * {
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family: 'Poppins', sans-serif;
}

body {
  display:flex;
  background-color:#f4f5f7;
}

/* ===== SIDEBAR ===== */
.sidebar {
  width:250px;
  background:#2f3e53;
  color:#fff;
  min-height:100vh;
  padding:20px;
  display:flex;
  flex-direction:column;
  gap:20px;
  font-family: 'Poppins', sans-serif;
}

.sidebar h2 {
  text-align:center;
  margin-bottom:30px;
  font-size:20px;
  font-weight:600;
}

.sidebar .user {
  text-align:center;
  margin-bottom:30px;
}

.sidebar .user img {
  width:60px;
  height:60px;
  border-radius:50%;
  margin-bottom:10px;
}

.sidebar ul {
  list-style:none;
  display:flex;
  flex-direction:column;
  gap:15px;
}

.sidebar ul li a {
  display:block;
  padding:15px 20px; /* updated padding from second block */
  background:#f4f5f7; /* from second block */
  color:#2f3e53;      /* from second block */
  text-decoration:none;
  font-weight:500;
  border-radius:8px;  /* from second block */
  transition:0.3s;
  text-align:center;  /* from second block */
  font-size:16px;     /* from second block */
}

.sidebar ul li a:hover {
  background:#f9c851;
  color:#000;
}

.sidebar ul li a.active {
  background:#f9c851;
  color:#000;
  font-weight:600;
}

/* ===== MAIN CONTENT ===== */
.main-content {
  flex:1;
  display:flex;
  flex-direction:column;
}

header {
  background:#f9c851;
  color:#000;
  padding:15px 20px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

header button {
  padding:8px 15px;
  background:#4fc3f7;
  border:none;
  border-radius:5px;
  color:#fff;
  cursor:pointer;
  font-weight:500;
}

.content {
  padding:20px;
  flex:1;
}

h3 {
  margin-bottom:20px;
  font-weight:600;
  color:#333;
}

/* ===== Form Styling ===== */
form {
  background:#fff;
  padding:20px;
  border-radius:8px;
  box-shadow:0 0 10px rgba(0,0,0,0.05);
  display:flex;
  flex-direction:column;
  gap:15px;
}

form label {
  font-weight:500;
  margin-bottom:5px;
  color:#555;
}

form input, form select, form textarea {
  padding:10px;
  border:1px solid #ccc;
  border-radius:5px;
  width:100%;
  font-size:14px;
}

form textarea {
  resize:vertical;
  min-height:120px;
}

form input[type="file"] {
  padding:5px;
}

form button {
  width:150px;
  padding:10px;
  background:#4fc3f7;
  color:#fff;
  border:none;
  border-radius:5px;
  cursor:pointer;
  font-weight:500;
  margin-top:10px;
}

/* ===== Table ===== */
table {
  width:100%;
  border-collapse:collapse;
  background:#fff;
  border-radius:8px;
  overflow:hidden;
  box-shadow:0 0 10px rgba(0,0,0,0.05);
  margin-top:20px;
}

table th, table td {
  padding:12px 15px;
  text-align:left;
  border-bottom:1px solid #ddd;
}

table th {
  background:#f9c851;
  color:#000;
  font-weight:600;
}

table tr:hover {
  background:#f1f1f1;
}

/* ===== Responsive ===== */
@media(max-width:768px){
  body {
    flex-direction:column;
  }

  .sidebar {
    width:100%;
    flex-direction:row;
    overflow-x:auto;
  }

  .sidebar ul {
    flex-direction:row;
    gap:10px;
  }
}

</style>
</head>
<body>

<aside class="sidebar">
  <div class="">
    <img src="https://via.placeholder.com/60" alt="">
    <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
  </div>
  <ul>
    <li><a href="dashboard.php" class="<?php echo ($ currentpage=='dashboard')?'active':''; ?>">Dashboard</a></li>
    <li><a href="my_complaints.php">View My Complaints</a></li>
    <li><a href="my_profile.php">my profile</a></li>
 <li><a href="logout.php">Logout</a></li>
  </ul>
</aside>

<div class="main-content">
  <header>
    <h2>STUDENT COMPLAINT REGISTRATIONS SYSTEM</h2>
  </header>

  <div class="content">
    <h3>Register New Complaint</h3>
    <form action="submit_complaint.php" method="POST" enctype="multipart/form-data">
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <div style="flex:1;">
          <label>Category</label>
          <select name="category" required>
            <option value="">Select Category</option>
            <option value="Category 1">Category 1</option>
            <option value="Category 2">Category 2</option>
          </select>
        </div>
        <div style="flex:1;">
          <label>Sub Category</label>
          <select name="subcategory" required>
            <option value="">Select Subcategory</option>
            <option value="Subcategory 1">Subcategory 1</option>
          </select>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <div style="flex:1;">
          <label>Complaint Type</label>
          <select name="complaint_type" required>
            <option value="Complaint">Complaint</option>
          </select>
        </div>
        <div style="flex:1;">
          <label>State</label>
          <select name="state" required>
            <option value="Pending" selected>Pending</option>
          </select>
        </div>
      </div>

      <div>
        <label>Nature of Complaint</label>
        <input type="text" name="nature" placeholder="Enter nature of complaint" required>
      </div>

      <div>
        <label>Complaint Details (max 2000 words)</label>
        <textarea name="details" placeholder="Enter complaint details..." required></textarea>
      </div>

      <button type="submit">Submit Complaint</button>
    </form>
