<?php
//  script that gets book info from the library database

//  Step 1: Set up database connection
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

//  Open the  database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Step 2: Check if someone wants data in JSON form
if (isset($_GET['format']) && $_GET['format'] === 'json') {
  //  Get BookID and Title from the Books table
  $sql = "SELECT BookID, Title FROM Books ORDER BY BookID ASC";
  $result = $conn->query($sql);

  $books = [];

  if ($result->num_rows > 0) {
    //  For each book, save the info in an array
    while ($row = $result->fetch_assoc()) {
      $books[] = [
        'BookID' => $row['BookID'],
        'Title' => $row['Title']
      ];
    }
  }

  //  Tell the browser we are sending JSON
  header('Content-Type: application/json');
  echo json_encode($books);
  $conn->close();
  exit;
}

//  Step 3: Show a table if not asking for JSON
$sql = "SELECT BookID, Title, Quantity, ISBN FROM Books ORDER BY BookID ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Loop through the books and print rows
  while ($row = $result->fetch_assoc()) {
    $bookID = $row['BookID'];
    //  stops HTML attacks
    $title = htmlspecialchars($row['Title']); 
    $quantity = $row['Quantity'];
    //  stops HTML attacks
    $isbn = htmlspecialchars($row['ISBN']); 

    //  Show each book in a table row
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
  // When  no books found 
  echo "<tr><td colspan='5'>No books found</td></tr>";
}

// Close the database when done
$conn->close();
?>

<!-- JS: When I click Edit, go to the Update page with info -->
<script>
function editBook(bookID, title, quantity, isbn) {
  window.location.href = "UpdateBook.php?BookID=" + bookID + 
                         "&Title=" + encodeURIComponent(title) + 
                         "&Quantity=" + quantity + 
                         "&ISBN=" + encodeURIComponent(isbn);
}
</script>
