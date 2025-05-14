<?php
// These are the details to connect to the database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// If we can’t connect, show an error and stop
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if all the form fields were filled in
if (
  isset($_POST['fullname'], $_POST['email'], $_POST['password'],
        $_POST['phonenumber'], $_POST['role'], $_POST['createdAt'])
) {
  // Get the values the user typed in
  $fullName = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $rawPassword = $_POST['password'];
  $phone = trim($_POST['phonenumber']);
  $role = $_POST['role'];
  $createdAt = $_POST['createdAt'];

  // Only allow Gmail emails
  if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    echo "<script>
      alert('Invalid email. Only Gmail addresses are allowed.');
      window.history.back();
    </script>";
    exit;
  }

  // Make sure phone number starts with 0 and has 10 digits
  if (!preg_match('/^0\d{9}$/', $phone)) {
    echo "<script>
      alert('Invalid phone number. It must start with 0 and be 10 digits.');
      window.history.back();
    </script>";
    exit;
  }

  // Make sure password is strong
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $rawPassword)) {
    echo "<script>
      alert('Password must be strong: include big/small letters, numbers, and symbols.');
      window.history.back();
    </script>";
    exit;
  }

  // Hide the real password with hashing (makes it unreadable)
  $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

  // Add the new user safely using a prepared statement
  $sql = "INSERT INTO Users (FullName, Email, Password, PhoneNumber, Role, CreatedAt)
          VALUES (?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  // Put the values into the placeholders safely
  $stmt->bind_param("ssssss", $fullName, $email, $hashedPassword, $phone, $role, $createdAt);

  // Try to save the user
  if ($stmt->execute()) {
    echo "<script>alert('User registered successfully!'); window.location.href='RegisterStudent.html';</script>";
  } else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
  }

  $stmt->close();
} else {
  // If fields were missing, show error
  echo "<script>alert('Missing required fields.'); window.history.back();</script>";
}

// Close the database connection
$conn->close();
?>
