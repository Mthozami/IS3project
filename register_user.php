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

  // Gmail-only email validation
  if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    echo "<script>
      alert('Invalid email. Only Gmail addresses (e.g., user@gmail.com) are allowed.');
      window.history.back();
    </script>";
    exit;
  }

// Phone number validation (must start with 0 and be 10 digits)
  if (!preg_match('/^0\d{9}$/', $phone)) {
    echo "<script>
      alert('Invalid phone number. It must start with 0 and be exactly 10 digits.');
      window.history.back();
    </script>";
    exit;
  }

  // Password validation
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $rawPassword)) {
    echo "<script>
      alert('Password must be at least 8 characters and include:\\n- One uppercase letter\\n- One lowercase letter\\n- One number\\n- One special character.');
      window.history.back();
    </script>";
    exit;
  }

  // Hash the password
  $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

  //begin transaction
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
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
  }

  $stmt->close();
} else {
  // Rollback on error
  $conn->rollback();
  echo "<script>alert('Missing required fields.'); window.history.back();</script>";
}

$conn->close();
?>