<?php
// Check if the form sent a UserID (this means someone is trying to update a user)
if (isset($_POST['UserID'])) {
    
    // Get all the values the user typed into the form
    $userId = $_POST['UserID'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Set up the database info to connect (like telling the computer where the library is)
    $host = "localhost"; // The server is on the same computer
    $username = "root"; // The username to open the database
    $password = "Mthozami@2004"; // The secret password
    $dbname = "LibraryDB"; // The name of our library database

    // Try to connect to the database
    $conn = new mysqli($host, $username, $password, $dbname);

    // If something goes wrong while connecting, stop and show an error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Make the update message (with ? placeholders to avoid bad code tricks)
    $sql = "UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ? WHERE UserID = ?";
    
    // Tell the computer to prepare the update message
    $stmt = $conn->prepare($sql);

    // Give the real values to the placeholders (safe from hackers using SQL injection)
    $stmt->bind_param("ssssi", $fullName, $email, $phone, $role, $userId);

    // Try to do the update
    if ($stmt->execute()) {
        // If everything worked, go back to the student registration page and say "updated=true"
        header("Location: RegisterStudent.html?updated=true");
        exit; // Stop the script now
    } else {
        // If something went wrong, show the error
        echo "Error updating user: " . $stmt->error;
    }

    // Close the update command and the connection to the database
    $stmt->close();
    $conn->close();

} else {
    // If there was no UserID sent, show this error
    echo "Invalid request.";
}
?>
