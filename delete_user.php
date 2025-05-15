<?php
// This file deletes a user from the library system

// Check if we got the user's ID from the page link
if (isset($_GET['userId'])) {
    // Save the user’s ID
    $userId = $_GET['userId']; 

    // Connect to the database
    $host = "localhost";
    $username = "root";
    $password = "Mthozami@2004";
    $dbname = "LibraryDB";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        // If connection fails, show error
        die("Connection failed: " . $conn->connect_error);
    }

    // We prepare a query to delete the user safely
    $sql = "DELETE FROM users WHERE userId = ?";
    //  Safe from SQL injection
    $stmt = $conn->prepare($sql); 
     // "i" means it’s an integer number
    $stmt->bind_param("i", $userId);
     //  Run the delete
    $stmt->execute();

    //  Let the user know what happened
    if ($stmt->affected_rows > 0) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user.";
    }

    //  Clean up
    $stmt->close(); 
    $conn->close();

    //  Go back to the Borrower page
    header("Location:RegisterStudent.html");
    exit;
}
?>
