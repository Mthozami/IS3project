<?php
header("Content-Type: application/json");

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

$sql = "SELECT FineID, BorrowingID, FullName, BookTitle, Amount, IsPaid, CreatedAt FROM Fines ORDER BY FineID DESC";
$result = $conn->query($sql);

$unpaidRows = "";
$paidRows = "";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fineID = htmlspecialchars($row["FineID"]);
        $borrowingID = htmlspecialchars($row["BorrowingID"]);
        $fullName = htmlspecialchars($row["FullName"]);
        $bookTitle = htmlspecialchars($row["BookTitle"]);
        $amount = htmlspecialchars($row["Amount"]);
        $createdAt = htmlspecialchars($row["CreatedAt"]);

        if ($row["IsPaid"]) {
            $paidRows .= "<tr>
                <td>$fineID</td>
                <td>$borrowingID</td>
                <td>$fullName</td>
                <td>$bookTitle</td>
                <td>R$amount</td>
                <td class='paid'>Paid</td>
                <td>$createdAt</td>
              </tr>";
        } else {
            $unpaidRows .= "<tr>
                <td>$fineID</td>
                <td>$borrowingID</td>
                <td>$fullName</td>
                <td>$bookTitle</td>
                <td>R$amount</td>
                <td class='unpaid'>Unpaid</td>
                <td>$createdAt</td>
                <td>
                  <button class='pay-btn' data-fine-id='$fineID'>Mark as Paid</button>
                  <button class='edit-btn'>Adjust</button>
                  <button class='delete-btn'>Cancel</button>
                </td>
              </tr>";
        }
    }
} else {
    $unpaidRows = "<tr><td colspan='8'>No unpaid fines found.</td></tr>";
    $paidRows = "<tr><td colspan='7'>No paid fines found.</td></tr>";
}

$conn->close();

echo json_encode([
    "unpaid" => $unpaidRows,
    "paid" => $paidRows
]);
