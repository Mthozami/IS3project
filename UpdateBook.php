<?php
// UpdateBook.php

$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $bookID = $_POST["bookID"];
  $title = $_POST["title"];
  $quantity = $_POST["quantity"];
  $isbn = $_POST["isbn"];

  $stmt = $conn->prepare("UPDATE books SET Title = ?, Quantity = ?, ISBN = ? WHERE BookID = ?");
  $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

  if ($stmt->execute()) {
    echo "<script>alert('Book updated successfully.'); window.location.href='AddBook.html';</script>";
    exit;
  } else {
    echo "<script>alert('Error updating book: " . $conn->error . "');</script>";
  }

  $stmt->close();
} else {
  $bookID = $_GET["BookID"];
  $title = $_GET["Title"];
  $quantity = $_GET["Quantity"];
  $isbn = $_GET["ISBN"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Book</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    form {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 350px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <form method="POST" action="UpdateBook.php">
    <h2>Edit Book</h2>
    <input type="hidden" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>">

    <label>Title:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

    <label>Quantity:</label>
    <input type="number" name="quantity" value="<?php echo $quantity; ?>" required>

    <label>ISBN:</label>
    <input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" required>

    <input type="submit" value="Update Book">
  </form>
</body>
</html>
<?php
}
$conn->close();
?>
