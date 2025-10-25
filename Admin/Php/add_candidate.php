<?php
session_start();
if ($_SESSION['role'] != 'admin') {
   header("Location: index.php");
   exit;
}

$conn = new mysqli("localhost", "root", "", "db_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];

    $stmt = $conn->prepare("INSERT INTO voting_candidates (candidate_name, position) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $position);
    $stmt->execute();
    $stmt->close();

    header("Location: edit_candidates.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Candidate</title>
</head>
<body>
  <h1>Add New Candidate</h1>
  <form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required><br>
    <label>Position:</label>
    <input type="text" name="position" required><br>
    <button type="submit">Add Candidate</button>
  </form>
  <a href="edit_candidates.php">Back</a>
</body>
</html>
