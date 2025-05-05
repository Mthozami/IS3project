<?php
// delete_user.php
if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    // Replace with your actual DB credentials
    $host = "localhost";
    $username = "root";
    $password = "Mzamoh@25";
    $dbname = "LibraryDB";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM users WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId); // "i" for integer
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the users page
    header("Location:RegisterStudent.html");
    exit;
}
?>
