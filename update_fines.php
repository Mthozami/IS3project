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

$query = "
  SELECT 
    b.BorrowingID, b.ReturnDate
  FROM Borrowings b
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

            // Check for existing fine
            $check = $conn->prepare("SELECT FineID, IsPaid FROM Fines WHERE BorrowingID = ? ORDER BY FineID DESC LIMIT 1");
            $check->bind_param("i", $borrowingID);
            $check->execute();
            $resultCheck = $check->get_result();

            if ($resultCheck && $resultCheck->num_rows > 0) {
                $fine = $resultCheck->fetch_assoc();
                if (!$fine["IsPaid"]) {
                    $update = $conn->prepare("UPDATE Fines SET Amount = ? WHERE FineID = ?");
                    $update->bind_param("di", $amount, $fine["FineID"]);
                    $update->execute();
                }
            } else {
                $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount) VALUES (?, ?)");
                $insert->bind_param("id", $borrowingID, $amount);
                $insert->execute();
            }
        }
    }
}

$conn->close();
echo "Fines updated.";
?>
