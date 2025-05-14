<?php
// Set content type to HTML to output <tr> elements
header("Content-Type: text/html");

// Create a new MySQLi connection
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");

// Check for connection failure
if ($conn->connect_error) {
  echo "<tr><td colspan='8'>Database connection failed.</td></tr>";
  exit;
}

// SQL query to retrieve fines along with user, book, and borrowing info
$sql = "
  SELECT 
    f.FineID, f.Amount, f.IsPaid, f.CreatedAt,
    b.BorrowingID,
    u.FullName,
    bk.Title AS BookTitle
  FROM Fines f
  JOIN Borrowings b ON f.BorrowingID = b.BorrowingID
  JOIN Users u ON b.UserID = u.UserID
  JOIN Books bk ON b.BookID = bk.BookID
  ORDER BY f.FineID ASC
";

// Run the query
$result = $conn->query($sql);

// If no results found, print a placeholder row
if (!$result || $result->num_rows === 0) {
  echo "<tr><td colspan='8'>No fines found.</td></tr>";
  exit;
}

// Iterate through each row in the result
while ($row = $result->fetch_assoc()) {
  // Display 'Paid' or 'Unpaid' status based on IsPaid field
  $status = $row["IsPaid"] ? "<span class='paid'>Paid</span>" : "<span class='unpaid'>Unpaid</span>";

  // Display action buttons only if the fine is unpaid
  $actions = $row["IsPaid"] ? "-" : "
    <button class='pay-btn' data-fine-id='{$row["FineID"]}'>Mark as Paid</button>
    <button class='edit-btn' data-fine-id='{$row["FineID"]}'>Adjust</button>
  ";

  // Output the fine record in table row format
  echo "<tr>
          <td>{$row["FineID"]}</td>
          <td>{$row["BorrowingID"]}</td>
          <td>{$row["FullName"]}</td>
          <td>{$row["BookTitle"]}</td>
          <td>R" . number_format($row["Amount"], 2) . "</td>
          <td>$status</td>
          <td>{$row["CreatedAt"]}</td>
          <td>$actions</td>
        </tr>";
}

// Close the database connection
$conn->close();
?>
