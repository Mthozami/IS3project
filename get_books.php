<?php
// fetch_books.php

$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT BookID, Title, Quantity, ISBN FROM Books ORDER BY BookID ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $bookID = str_pad($row['BookID'] ?? '', 3, '0', STR_PAD_LEFT);
    $title = htmlspecialchars($row['Title'] ?? '');
    $quantity = $row['Quantity'] ?? '';
    $isbn = htmlspecialchars($row['ISBN'] ?? '');

    echo "<tr>
      <td>$bookID</td>
      <td>$title</td>
      <td>$quantity</td>
      <td>$isbn</td>
      <td class='actions'>
        <button class='edit-btn'>Edit</button>
        <button class='delete-btn'>Delete</button>
      </td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='5'>No books found</td></tr>";
}

$conn->close();
?>
