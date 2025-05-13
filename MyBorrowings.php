<?php
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT b.Title, br.Quantity, br.BorrowedDate, br.ReturnDate
        FROM Borrowings br
        INNER JOIN Books b ON br.BookID = b.BookID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
    echo "<td>" . htmlspecialchars($row['BorrowedDate']) . "</td>";
    echo "<td>" . ($row['ReturnDate'] ? htmlspecialchars($row['ReturnDate']) : 'Not Returned') . "</td>";
    echo "<td>" . ($row['ReturnDate'] ? 'Returned' : 'Not Returned') . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='5'>No borrowings found.</td></tr>";
}

$conn->close();
?>
