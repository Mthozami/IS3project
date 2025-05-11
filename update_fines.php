<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

// Select overdue borrowings (status is 'borrowed' and ReturnDate < today)
$query = "
  SELECT 
    b.BorrowingID, b.ReturnDate,
    u.FullName, bk.Title AS BookTitle
  FROM Borrowings b
  JOIN Users u ON b.UserID = u.UserID
  JOIN Books bk ON b.BookID = bk.BookID
  WHERE b.Status = 'borrowed' AND b.ReturnDate < CURDATE()
";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $borrowingID = $row["BorrowingID"];
        $returnDate = new DateTime($row["ReturnDate"]);
        $today = new DateTime();

        if ($returnDate < $today) {
            $daysOverdue = $returnDate->diff($today)->days;
            $amount = 25 * $daysOverdue;

            // Check if any fine exists (paid or unpaid)
            $check = $conn->prepare("SELECT FineID, IsPaid FROM Fines WHERE BorrowingID = ? ORDER BY FineID DESC LIMIT 1");
            $check->bind_param("i", $borrowingID);
            $check->execute();
            $resultCheck = $check->get_result();

            if ($resultCheck && $resultCheck->num_rows > 0) {
                $fine = $resultCheck->fetch_assoc();
                if ($fine["IsPaid"] == 0) {
                    // Update existing unpaid fine
                    $update = $conn->prepare("UPDATE Fines SET Amount = ? WHERE FineID = ?");
                    $update->bind_param("di", $amount, $fine["FineID"]);
                    $update->execute();
                }
                // If paid, do nothing — skip
            } else {
                // Insert only if no fine exists for this borrowing
                $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount, FullName, BookTitle) VALUES (?, ?, ?, ?)");
                $insert->bind_param("idss", $borrowingID, $amount, $row["FullName"], $row["BookTitle"]);
                $insert->execute();
            }
        }
    }
}

$conn->close();
echo "Fines updated.";
?>
