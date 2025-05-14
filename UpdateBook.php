<?php
// Establish a connection to the MySQL database
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");

// Check  connection errors
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST (e.g,form was submitted using post method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Get the submitted form values
  $bookID = $_POST["bookID"];
  $title = $_POST["title"];
  $quantity = $_POST["quantity"];
  $isbn = $_POST["isbn"];

  // Use a prepared statement to safely update book info (SQL Injection prevention)
  $stmt = $conn->prepare("UPDATE books SET Title = ?, Quantity = ?, ISBN = ? WHERE BookID = ?");
  // Bind values to placeholders (s: string, i: int)
  $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

  // Execute the statement and give feedback to the user
  if ($stmt->execute()) {
  // Show success alert and redirect to AddBook.html
    echo "<script>alert('Book updated successfully.'); window.location.href='AddBook.html';</script>";
    exit;
  } else {
    // Show error if execution fails
    echo "<script>alert('Error updating book: " . $conn->error . "');</script>";
  }

  // Close the statement
  $stmt->close();
} else {

   /* This  is used to  handles GET requests: pre-fill form with book data from URL

    Retrieve values from the URL using GET (used to populate form fields)*/
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
    /* Style the body and center the form */
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
     /* Form container styling */

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
  <!-- Form to update book details -->
  <form method="POST" action="UpdateBook.php">
    <h2>Edit Book</h2>
    <!-- Hidden input to keep track of which book is being edited -->
    <input type="hidden" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>">

     <!-- Title input field (pre-filled from URL) -->
    <label>Title:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

        <!-- Quantity input field (pre-filled from URL) -->
    <label>Quantity:</label>
    <input type="number" name="quantity" value="<?php echo $quantity; ?>" required>

    <!-- ISBN input field (pre-filled from URL) -->
    <label>ISBN:</label>
    <input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" required>

     <!-- Submit button -->
    <input type="submit" value="Update Book">
  </form>
</body>
</html>
<?php
}
/* End of else (GET request)
Close the database connection*/
$conn->close();
?>
