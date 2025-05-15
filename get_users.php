<?php
// Set database connection variables
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
  // If connection fails, return a JSON error response and stop execution
  echo json_encode(['error' => 'Connection failed', 'message' => $conn->connect_error]);
  exit();
}

// Begin transaction
$conn->begin_transaction();

try {
  // SQL query to fetch all users from the "users" table
  $sql = "SELECT UserID, FullName, Email, Password, PhoneNumber, Role, CreatedAt FROM users ORDER BY userId ASC";

  // Execute the query
  $result = $conn->query($sql);

  // Initialize an empty array to hold the user records
  $users = [];

  // If the result is valid and contains rows
  if ($result && $result->num_rows > 0) {
    // Fetch each row and add it to the $users array
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }
  } else {
    // If no users found, return a message in the array
    $users = ['message' => 'No users found'];
  }

  // Commit the transaction
  $conn->commit();
} catch (Exception $e) {
  // If any error occurs, rollback the transaction
  $conn->rollback();
  echo json_encode(['error' => 'Transaction failed', 'message' => $e->getMessage()]);
  $conn->close();
  exit();
}

// Set the response type to JSON
header('Content-Type: application/json');

// Output the result as a JSON object
echo json_encode($users);

// Close the database connection
$conn->close();
?>
