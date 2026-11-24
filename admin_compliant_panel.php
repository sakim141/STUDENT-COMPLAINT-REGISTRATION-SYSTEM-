<?php
session_start();
require_once "connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle complaint update (status + feedback)
if (isset($_POST['update_status'])) {
    $id = $_POST['complaint_id'];
    $status = $_POST['status'];
    $feedback = $_POST['feedback'] ?? '';

    $stmt = $conn->prepare("UPDATE complaints SET state=?, admin_feedback=? WHERE id=?");
    $stmt->bind_param("ssi", $status, $feedback, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: all_complaints.php");
    exit;
}

// Fetch all complaints with student info
$sql = "SELECT c.id, u.fullname, u.email, c.category, c.subcategory, c.complaint_type, c.state, c.nature, c.details, c.admin_feedback, c.created_at
        FROM complaints c
        JOIN student u ON c.student_id = u.student_id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Complaints</title>
<style>
body { font-family:Arial,sans-serif; background:#f4f4f9; padding:20px; }
h1 { color:#2c3e50; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.1); border-radius:10px; overflow:hidden; }
th, td { padding:12px 15px; text-align:left; border-bottom:1px solid #ddd; vertical-align:top; }
th { background:#2c3e50; color:#fff; }
tr:hover { background:#f1f1f1; }
select, button, textarea { padding:5px 10px; margin:2px; border-radius:5px; border:1px solid #ccc; font-family:Arial,sans-serif; }
button { background:#16a085; color:#fff; border:none; cursor:pointer; }
textarea { resize:none; }
form { display:flex; flex-direction:column; }
</style>
</head>
<body>

<h1>All Complaints</h1>

<table>
<tr>
<th>ID</th>
<th>Student</th>
<th>Email</th>
<th>Category</th>
<th>Subcategory</th>
<th>Type</th>
<th>Status</th>
<th>Nature</th>
<th>Details</th>
<th>Admin Feedback</th>
<th>Submitted On</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo htmlspecialchars($row['fullname']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['category']); ?></td>
<td><?php echo htmlspecialchars($row['subcategory']); ?></td>
<td><?php echo htmlspecialchars($row['complaint_type']); ?></td>
<td><?php echo $row['state']; ?></td>
<td><?php echo htmlspecialchars($row['nature']); ?></td>
<td><?php echo htmlspecialchars($row['details']); ?></td>
<td><?php echo htmlspecialchars($row['admin_feedback']); ?></td>
<td><?php echo $row['created_at']; ?></td>
<td>
<form method="post">
<input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">

<select name="status">
<option value="Pending" <?php if($row['state']=='Pending') echo 'selected'; ?>>Pending</option>
<option value="In Progress" <?php if($row['state']=='In Progress') echo 'selected'; ?>>In Progress</option>
<option value="Resolved" <?php if($row['state']=='Resolved') echo 'selected'; ?>>Resolved</option>
</select>

<textarea name="feedback" placeholder="Add feedback..." style="width:150px; height:50px;"><?php echo htmlspecialchars($row['admin_feedback']); ?></textarea>

<button type="submit" name="update_status">Update</button>
</form>
</td>
</tr>
<?php endwhile; ?>

</table>
</body>
</html>
