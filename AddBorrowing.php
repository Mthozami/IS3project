<?php
// DB connection
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

// Only handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookId     = $_POST["bookId"]     ?? "";
    $userId     = $_POST["userId"]     ?? "";
    $quantity   = $_POST["quantity"]   ?? 1;
    $borrowDate = $_POST["borrowDate"] ?? "";
    $returnDate = $_POST["returnDate"] ?? "";

    // Validate required fields
    if (empty($bookId) || empty($userId) || empty($borrowDate) || empty($returnDate)) {
        die("Missing required fields.");
    }

    // 1. Check available book quantity
    $checkQuery = "SELECT Quantity FROM Books WHERE BookID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Book not found.");
    }

    $row = $result->fetch_assoc();
    if ((int)$row["Quantity"] < (int)$quantity) {
        die("Not enough copies available.");
    }

    // 2. Insert into Borrowings table
    $insertQuery = "
        INSERT INTO Borrowings (UserID, BookID, Quantity, BorrowedDate, ReturnDate, Status)
        VALUES (?, ?, ?, ?, ?, 'borrowed')
    ";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiiss", $userId, $bookId, $quantity, $borrowDate, $returnDate);

    if ($stmt->execute()) {
        // 3. Update quantity in Books
        $updateQuery = "UPDATE Books SET Quantity = Quantity - ? WHERE BookID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $quantity, $bookId);
        $updateStmt->execute();
        echo "Borrowing recorded successfully.";
    } else {
        echo "Failed to record borrowing.";
    }

} else {
    echo "Invalid request method.";
}

$conn->close();
?>
