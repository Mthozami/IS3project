<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit;
}

$userEmail = $_SESSION['email'];

// Database connection
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// SQL Query to fetch borrowings for the logged-in user
$sql = "SELECT BorrowingID, BookID, Quantity, BorrowDate, ReturnDate, Status FROM Borrowings WHERE UserEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$rowsHtml = "";
if ($result->num_rows > 0) {
    // Loop through each borrowing record
    while ($row = $result->fetch_assoc()) {
        $rowsHtml .= "<tr>
            <td>{$row['BorrowingID']}</td>
            <td>{$row['BookID']}</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['BorrowDate']}</td>
            <td>{$row['ReturnDate']}</td>
            <td>{$row['Status']}</td>
        </tr>";
    }
} else {
    // If no records are found, display this message
    $rowsHtml = "<tr><td colspan='6'>No borrowings found.</td></tr>";
}

$stmt->close();
$conn->close();

// Load the HTML template
$html = file_get_contents("MyBorrowings.html");

// Replace the placeholder {{BORROWING_ROWS}} with the actual rows HTML
$html = str_replace("{{BORROWING_ROWS}}", $rowsHtml, $html);

// Output the final HTML to the browser
echo $html;
?>
