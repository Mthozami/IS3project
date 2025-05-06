<?php
if (isset($_POST['UserID'])) {
    $userId = $_POST['UserID'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Database connection
    $host = "localhost";
    $username = "root";
    $password = "@Sihle24";
    $dbname = "LibraryDB";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update query
    $sql = "UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ? WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fullName, $email, $phone, $role, $userId);

    if ($stmt->execute()) {
        header("Location: RegisterStudent.html?updated=true");
        exit;
    } else {
        echo "Error updating user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
