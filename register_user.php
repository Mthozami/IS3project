<?php
// Database connection settings
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if all required fields are provided
if (
  isset($_POST['fullname'], $_POST['email'], $_POST['password'],
        $_POST['phonenumber'], $_POST['role'], $_POST['createdAt'])
) {
  $fullName = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $rawPassword = $_POST['password'];
  $phone = trim($_POST['phonenumber']);
  $role = $_POST['role'];
  $createdAt = $_POST['createdAt'];

  // Hash the password
  $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

  // Begin transaction
  $conn->begin_transaction(); // Start transaction

  // Prepare SQL query
  $sql = "INSERT INTO Users (FullName, Email, Password, PhoneNumber, Role, CreatedAt)
          VALUES (?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    // Rollback on error
    $conn->rollback(); 
    die("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param("ssssss", $fullName, $email, $hashedPassword, $phone, $role, $createdAt);

  if ($stmt->execute()) {
     // Commit on success
    $conn->commit();
    echo "<script>alert('User registered successfully!'); window.location.href='RegisterStudent.html';</script>";
  } else {
    // Rollback on failure
    $conn->rollback(); 
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
} else {
  die("Missing required fields.");
}

$conn->close();
?>
