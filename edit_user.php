<?php
if (!isset($_GET['UserID'])) {
    die("User ID not provided.");
}

$userId = $_GET['UserID'];

// Database connection
$host = "localhost";
$username = "root";
$password = "@Sihle24";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Edit User</h2>
  <form method="POST" action="update_user.php">
    <input type="hidden" name="UserID" value="<?= htmlspecialchars($user['UserID']) ?>">
    <div class="mb-3">
      <label for="fullName" class="form-label">Full Name</label>
      <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($user['FullName']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">Phone Number</label>
      <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['PhoneNumber']) ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="role" class="form-label">Role</label>
      <select name="role" id="role" class="form-select" required>
        <option value="Admin" <?= $user['Role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Borrower" <?= $user['Role'] == 'Borrower' ? 'selected' : '' ?>>Borrower</option>
      </select>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="RegisterStudent.html" class="btn btn-secondary">Cancel</a>
  </form>
</body>
</html>
