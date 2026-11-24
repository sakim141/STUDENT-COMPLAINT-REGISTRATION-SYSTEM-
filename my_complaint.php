<?php
session_start();
require_once "connection.php";

// =====================
// LOGIN CHECK
// =====================
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}
$student_id= $_SESSION['student_id'];

// =====================
// DELETE COMPLAINT
// =====================
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare(
        "DELETE FROM complaints
         WHERE id = ? AND user_id = ? AND LOWER(state) = 'pending'"
    );
    $stmt->bind_param("ii", $delete_id, $student_id);
    $stmt->execute();
    header("Location: my_complaints.php");
    exit;
}

// =====================
// EDIT COMPLAINT
// =====================
if (isset($_POST['edit_id']) && is_numeric($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $category = trim($_POST['category']);
    $subcategory = trim($_POST['subcategory']);
    $details = trim($_POST['details']);

    $stmt = $conn->prepare(
        "UPDATE complaints
         SET category = ?, subcategory = ?, details = ?
         WHERE student= ? AND student_id = ? AND LOWER(state) = 'pending'"
    );
    $stmt->bind_param("sssii", $category, $subcategory, $details, $edit_id, $student_id);
    $stmt->execute();
    header("Location: my_complaints.php");
    exit;
}

// =====================
// FETCH COMPLAINTS WITH SEARCH
// =====================
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

if ($search) {
    $search_param = "%{$search}%";
    $stmt = $conn->prepare(
        "SELECT *, admin_feedback FROM complaints 
         WHERE student_id = ? 
         AND (category LIKE ? OR subcategory LIKE ? OR complaint_type LIKE ? OR details LIKE ?)
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("issss", $student_id, $search_param, $search_param, $search_param, $search_param);
} else {
    $stmt = $conn->prepare(
        "SELECT *, admin_feedback FROM complaints 
         WHERE student_id = ? 
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("i", $student_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Complaints</title>
<style>
/* === GLOBAL STYLES === */
body { font-family: 'Arial', sans-serif; margin: 0; background: #f4f7f9; display: flex; }
a { text-decoration: none; }

/* === SIDEBAR === */
.sidebar { width: 250px; background: rgba(2, 55, 141, 0.91); color: #fff; min-height: 100vh; }
.sidebar h2 { text-align: center; padding: 20px; border-bottom: 1px solid #0a2945ff; }
.sidebar a { display: block; color: #fff; padding: 15px 20px; transition: 0.3s; }
.sidebar a:hover { background: #1a5d6cff; }

/* === MAIN CONTENT === */
.main { flex: 1; padding: 30px; }
h2 { margin-bottom: 15px; }

/* === SEARCH BAR === */
.search-bar { margin-bottom: 20px; }
.search-bar input[type="text"] { padding: 8px 12px; width: 250px; border-radius: 5px; border: 1px solid #ccc; }
.search-bar button { padding: 8px 12px; border-radius: 5px; border: none; background: #3498db; color: #fff; cursor: pointer; transition: 0.2s; }
.search-bar button:hover { background: #2980b9; }

/* === TABLE STYLES === */
table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 3px 10px rgba(13, 134, 161, 0.05); }
th, td { padding: 12px 15px; text-align: left; vertical-align: top; }
th { background: #f1f1f1; }
tr:nth-child(even) { background: #f9f9f9; }
.Pending { background: #fff8dc; }
.Resolved { background: #e6ffed; }
td.feedback { color: #16a085; font-weight: bold; }

/* === BUTTONS === */
button { padding: 6px 12px; cursor: pointer; border: none; border-radius: 5px; background: #3498db; color: #fff; transition: 0.2s; }
button:hover { background: #2980b9; }

/* === MODAL === */
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; animation: fadeIn 0.3s ease; }
.modal-content { background: #fff; padding: 25px; border-radius: 10px; width: 400px; max-width: 90%; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
.close { position: absolute; top: 10px; right: 15px; color: #e74c3c; font-weight: bold; cursor: pointer; font-size: 18px; }
input[type="text"], textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
textarea { resize: vertical; min-height: 80px; }

/* === ANIMATIONS === */
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
</head>

<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="my_complaints.php">My Complaints</a></li>
</div>

<div class="main">
<h2>My Complaints</h2>

<!-- SEARCH BAR -->
<form class="search-bar" method="GET">
    <input type="text" name="search" placeholder="Search complaints..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<?php if ($result->num_rows > 0): ?>
<table>
<tr>
    <th>ID</th>
    <th>Category</th>
    <th>Subcategory</th>
    <th>Type</th>
    <th>Details</th>
    <th>Status</th>
    <th>Admin Feedback</th>
    <th>Date</th>
    <th>Edit</th>
    <th>Delete</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr class="<?= htmlspecialchars($row['state']) ?>">
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td><?= htmlspecialchars($row['subcategory']) ?></td>
    <td><?= htmlspecialchars($row['complaint_type']) ?></td>
    <td><?= htmlspecialchars($row['details']) ?></td>
    <td><?= htmlspecialchars($row['state']) ?></td>
    <td class="feedback"><?= htmlspecialchars($row['admin_feedback'] ?: 'No feedback yet') ?></td>
    <td><?= $row['created_at'] ?></td>
    <td>
        <?php if (strtolower($row['state']) === 'pending'): ?>
        <button onclick='openEditModal(
            <?= $row["id"] ?>,
            <?= json_encode($row["category"]) ?>,
            <?= json_encode($row["subcategory"]) ?>,
            <?= json_encode($row["details"]) ?>
        )'>Edit</button>
        <?php else: ?> - <?php endif; ?>
    </td>
    <td>
        <?php if (strtolower($row['state']) === 'pending'): ?>
        <button onclick="confirmDelete(<?= (int)$row['id'] ?>)">Delete</button>
        <?php else: ?> - <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No complaints found.</p>
<?php endif; ?>
</div>

<!-- EDIT MODAL -->
<div class="modal" id="editModal">
<div class="modal-content">
<span class="close" onclick="closeEditModal()">&times;</span>
<h3>Edit Complaint</h3>
<form method="POST">
<input type="hidden" name="edit_id" id="edit_id">

<label>Category</label>
<input type="text" name="category" id="edit_category" required>

<label>Subcategory</label>
<input type="text" name="subcategory" id="edit_subcategory" required>

<label>Details</label>
<textarea name="details" id="edit_details" required></textarea>

<br><br>
<button type="submit">Update</button>
</form>
</div>
</div>

<script>
function confirmDelete(id){
    if(confirm("Are you sure you want to delete this complaint?")){
        window.location.href = "my_complaints.php?delete_id=" + id;
    }
}

const modal = document.getElementById('editModal');
function openEditModal(id,category,subcategory,details){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_subcategory').value = subcategory;
    document.getElementById('edit_details').value = details;
    modal.style.display='flex';
}
function closeEditModal(){ modal.style.display='none'; }
window.onclick = e => { if(e.target === modal) closeEditModal(); }
</script>

</body>
</html>
