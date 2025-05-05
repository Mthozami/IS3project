<?php
// get_books.php

$host = "localhost";
$username = "root";
$password = "Mzamoh@25";
$dbname = "LibraryDB";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT BookID, Title, Quantity, ISBN FROM Books ORDER BY BookID ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $bookID = $row['BookID'];
    $title = htmlspecialchars($row['Title']);
    $quantity = $row['Quantity'];
    $isbn = htmlspecialchars($row['ISBN']);

    echo "<tr>
      <td>" . str_pad($bookID, 3, '0', STR_PAD_LEFT) . "</td>
      <td>$title</td>
      <td>$quantity</td>
      <td>$isbn</td>
      <td class='actions'>
        <button class='edit-btn' onclick=\"editBook('$bookID', '$title', '$quantity', '$isbn')\">Edit</button>
        <button class='delete-btn' onclick=\"deleteBook('$bookID')\">Delete</button>
      </td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='5'>No books found</td></tr>";
}

$conn->close();
?>

<!-- ✅ Old editBook function (redirects to edit_book.php) -->
<script>
function editBook(bookID, title, quantity, isbn) {
  const url = `edit_book.php?BookID=${bookID}&Title=${encodeURIComponent(title)}&Quantity=${quantity}&ISBN=${encodeURIComponent(isbn)}`;
  window.location.href = url;
}
</script>

<!-- ✅ New editBook function (redirects to UpdateBook.php) -->
<script>
function editBook(bookID, title, quantity, isbn) {
  window.location.href = "UpdateBook.php?BookID=" + bookID + 
                         "&Title=" + encodeURIComponent(title) + 
                         "&Quantity=" + quantity + 
                         "&ISBN=" + encodeURIComponent(isbn);
}
</script>
