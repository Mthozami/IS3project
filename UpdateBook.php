<?php
//  This part connects to our Library database
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");

//  If something goes wrong while connecting, stop and show an error message
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//  Check if the user submitted the form using POST (means they clicked "Update Book")
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  //  Get all the form values they typed (the Book ID, title, quantity and ISBN)
  $bookID = $_POST["bookID"];
  $title = $_POST["title"];
  $quantity = $_POST["quantity"];
  $isbn = $_POST["isbn"];

  //  Use a prepared SQL statement (helps prevent bad people from hacking it)
  $stmt = $conn->prepare("UPDATE books SET Title = ?, Quantity = ?, ISBN = ? WHERE BookID = ?");

  // Replace the ?s above with the real values (s = string, i = integer)
  $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

  // Try to update the book in the database
  if ($stmt->execute()) {
    //  If it worked, show a message and send user back to AddBook.html
    echo "<script>alert('Book updated successfully.'); window.location.href='AddBook.html';</script>";
    exit;
  } else {
    //  If something failed, show an error message
    echo "<script>alert('Error updating book: " . $conn->error . "');</script>";
  }

  //  Close the statement after using it
  $stmt->close();

} else {
  //  If the user came to this page with GET (like clicking “edit” from a list), load the values from the URL

  $bookID = $_GET["BookID"];
  $title = $_GET["Title"];
  $quantity = $_GET["Quantity"];
  $isbn = $_GET["ISBN"];
?>
<!--  Start of the web page (HTML) -->
<!DOCTYPE html>
<html>
<head>
  <title>Edit Book</title>
  <style>
    /* Make the form look nice and centered */
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
  <!-- Form where the librarian can update the book details -->
  <form method="POST" action="UpdateBook.php">
    <h2>Edit Book</h2>

    <!--  Hidden input to remember which book we are updating -->
    <input type="hidden" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>">

    <!-- Input for book title -->
    <label>Title:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

    <!-- Input for book quantity -->
    <label>Quantity:</label>
    <input type="number" name="quantity" value="<?php echo $quantity; ?>" required>

    <!-- Input for book ISBN -->
    <label>ISBN:</label>
    <input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" required>

    <!-- Button to submit the form -->
    <input type="submit" value="Update Book">
  </form>
</body>
</html>
<?php
}
//  Close the connection to the database at the very end
$conn->close();
?>
