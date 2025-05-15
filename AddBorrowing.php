<?php
//  I make a tunnel to my database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

// I only want to help if the user is posting a form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookId     = $_POST["bookId"]     ?? "";
    $userId     = $_POST["userId"]     ?? "";
    $quantity   = $_POST["quantity"]   ?? 1;
    $borrowDate = $_POST["borrowDate"] ?? "";
    $returnDate = $_POST["returnDate"] ?? "";

    //  I make sure all parts are filled
    if (empty($bookId) || empty($userId) || empty($borrowDate) || empty($returnDate)) {
        die("Missing required fields.");
    }

    //  You can only borrow 1 book at once! Sorry!
    if ((int)$quantity !== 1) {
        die("You are only allowed to borrow 1 book at a time.");
    }

    // I make sure the dates are not weird or broken
    $today = new DateTime();
    $borrowDateObj = DateTime::createFromFormat('Y-m-d', $borrowDate);
    $returnDateObj = DateTime::createFromFormat('Y-m-d', $returnDate);

    if (!$borrowDateObj || !$returnDateObj) {
        die("Invalid date format. Use YYYY-MM-DD.");
    }

    $today->setTime(0, 0, 0);
    $borrowDateObj->setTime(0, 0, 0);
    $returnDateObj->setTime(0, 0, 0);

    if ($borrowDateObj < $today) {
        die("Borrow date cannot be in the past.");
    }

    if ($returnDateObj < $borrowDateObj) {
        die("Return date cannot be before the borrow date.");
    }

    //  I check if there's any book left on the shelf
    $checkQuery = "SELECT Quantity FROM Books WHERE BookID = ?";
    $stmt = $conn->prepare($checkQuery);
    //  SQL injection blocked here
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

    // Start transaction
    $conn->begin_transaction(); // I begin a safe transaction

    // I add the borrowing to the borrowings list
    $insertQuery = "
        INSERT INTO Borrowings (UserID, BookID, Quantity, BorrowedDate, ReturnDate, Status)
        VALUES (?, ?, ?, ?, ?, 'borrowed')
    ";
    $stmt = $conn->prepare($insertQuery);
     // make my database  safe from sql injection!
    $stmt->bind_param("iiiss", $userId, $bookId, $quantity, $borrowDate, $returnDate);

    if ($stmt->execute()) {
        // I  take 1 book off the shelf
        $updateQuery = "UPDATE Books SET Quantity = Quantity - 1 WHERE BookID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $bookId);
        
        if ($updateStmt->execute()) {
            // All good, I save everything
            $conn->commit(); 
            echo "Borrowing recorded successfully.";
        } else {
            // I undo everything if update fails
            $conn->rollback(); 
            echo "Failed to update book quantity.";
        }
    } else {
         // I undo everything if insert fails
        $conn->rollback();
        echo "Failed to record borrowing.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
