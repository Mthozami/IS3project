<?php
// UpdateBook.php

// DB connection
$conn = new mysqli("localhost", "root", "@Sihle24", "LibraryDB");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Update logic
  $bookID = $_POST["bookID"];
  $title = $_POST["title"];
  $quantity = $_POST["quantity"];
  $isbn = $_POST["isbn"];

  $stmt = $conn->prepare("UPDATE books SET Title = ?, Quantity = ?, ISBN = ? WHERE BookID = ?");
  $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

  if ($stmt->execute()) {
    echo "Book updated successfully.";
    header("Location: index.html"); // Change if needed
    exit;
  } else {
    echo "Error updating book: " . $conn->error;
  }

  $stmt->close();
} else {
  // Show form with existing data from GET
  $bookID = $_GET["BookID"];
  $title = $_GET["Title"];
  $quantity = $_GET["Quantity"];
  $isbn = $_GET["ISBN"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Book</title>
</head>
<body>
  <h2>Edit Book</h2>
  <form method="POST" action="UpdateBook.php">
    <input type="hidden" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>">

    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" value="<?php echo $quantity; ?>" required><br><br>

    <label>ISBN:</label><br>
    <input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" required><br><br>

    <input type="submit" value="Update Book">
  </form>
</body>
</html>
<?php
}
$conn->close();
?>
