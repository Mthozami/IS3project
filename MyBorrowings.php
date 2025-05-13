<?php
header("Content-Type: text/html");

// No session needed if not filtering by user
// session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
    echo "<tr><td colspan='5'>Connection failed.</td></tr>";
    exit;
}

// Query ALL borrowings
$sql = "
  SELECT 
    BookTitle,
    Quantity,
    BorrowedDate,
    ReturnDate,
    Status
  FROM Borrowings
  ORDER BY BorrowedDate DESC
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<tr><td colspan='5'>No borrowings found.</td></tr>";
    exit;
}

// Output table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["BookTitle"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Quantity"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["BorrowedDate"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["ReturnDate"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
    echo "</tr>";
}

$conn->close();
?>
