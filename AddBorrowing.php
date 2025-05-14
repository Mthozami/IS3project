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

    // ✅ Enforce only 1 book borrowing
    if ((int)$quantity !== 1) {
        die("You are only allowed to borrow 1 book at a time.");
    }

    // ✅ Validate date format and logic
    $today = new DateTime(); // Today
    $borrowDateObj = DateTime::createFromFormat('Y-m-d', $borrowDate);
    $returnDateObj = DateTime::createFromFormat('Y-m-d', $returnDate);

    if (!$borrowDateObj || !$returnDateObj) {
        die("Invalid date format. Use YYYY-MM-DD.");
    }

    // Remove time for fair comparison
    $today->setTime(0, 0, 0);
    $borrowDateObj->setTime(0, 0, 0);
    $returnDateObj->setTime(0, 0, 0);

    if ($borrowDateObj < $today) {
        die("Borrow date cannot be in the past.");
    }

    if ($returnDateObj < $borrowDateObj) {
        die("Return date cannot be before the borrow date.");
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
    if ((int)$row["Quantity"] < 1) {
        die("No copies available.");
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
        $updateQuery = "UPDATE Books SET Quantity = Quantity - 1 WHERE BookID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $bookId);
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
