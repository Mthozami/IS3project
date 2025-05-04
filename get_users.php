<?php
// Replace with your actual DB credentials
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  // Return detailed error message in JSON format
  echo json_encode(['error' => 'Connection failed', 'message' => $conn->connect_error]);
  exit();
}

// SQL query to get users
$sql = "SELECT UserID, FullName, Email, Password, PhoneNumber, Role, CreatedAt FROM users ORDER BY userId ASC";

// Run the query and check if it was successful
$result = $conn->query($sql);

// Initialize an array to hold the users
$users = [];

// If the query was successful and we have results
if ($result && $result->num_rows > 0) {
  // Fetch all users and add them to the $users array
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
} else {
  // If no users are found, you can return an empty array or an error message
  $users = ['message' => 'No users found'];
}

// Set the header to indicate that the response is JSON
header('Content-Type: application/json');

// Return the results as JSON
echo json_encode($users);

// Close the database connection
$conn->close();
?>
