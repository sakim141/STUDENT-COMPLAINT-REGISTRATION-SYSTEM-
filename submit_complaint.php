<?php
session_start();
require_once "connection.php"; 

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location:dashboard.php");
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    exit;
}

// Collect & trim data
$student_id = $_SESSION['student_id'];
$category = trim($_POST['category'] ?? '');
$subcategory = trim($_POST['subcategory'] ?? '');
$complaint_type = trim($_POST['complaint_type'] ?? '');
$state = trim($_POST['state'] ?? 'Pending');
$nature = trim($_POST['nature'] ?? '');
$details = trim($_POST['details'] ?? '');

// Check required fields
if (!$category || !$subcategory || !$complaint_type || !$nature || !$details) {
    header("Location:index.php?error=Please fill all required fields");
   die("required");
}
$document_path = null;

$stmt = $conn->prepare(
    "INSERT INTO complaints 
    (student_id, category, subcategory, complaint_type, state, nature, details, document) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "isssssss",
    $student_id,
    $category,
    $subcategory,
    $complaint_type,
    $state,
    $nature,
    $details,
    $document_path
);

// Execute & check errors
if ($stmt->execute()) {
    header("location:dashboard.php?success=1");
} else {
    header("location:dashboard.php?error=Database error: " . $stmt->error);
}

?>
