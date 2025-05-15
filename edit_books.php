<?php
//  This file edits a book’s details

// Connect to the database using PDO (a safe method)
$host = "localhost";
$dbname = "LibraryDB";
$username = "root";
$password = "@Mthozami@2004";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Show errors if anything goes wrong
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
     // Show error if can't connect
    die("Connection failed: " . $e->getMessage());
}

//  If we are coming from the Edit button (GET method)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['BookID'])) {
    $bookID = $_GET['BookID'];
    $title = $_GET['Title'] ?? '';
    $quantity = $_GET['Quantity'] ?? '';
    $isbn = $_GET['ISBN'] ?? '';
    ?>

    <!-- Form to change book information -->
    <h2>Edit Book</h2>
    <form method="POST" action="edit_books.php">
        <input type="hidden" name="bookID" value="<?= htmlspecialchars($bookID) ?>">

        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br>

        <label>Quantity:</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($quantity) ?>" required><br>

        <label>ISBN:</label>
        <input type="text" name="isbn" value="<?= htmlspecialchars($isbn) ?>" required><br>

        <button type="submit">Update Book</button>
    </form>

    <hr>

<?php
}

// If form was submitted (POST method)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookID = $_POST['bookID'];
    $title = $_POST['title'];
    $quantity = $_POST['quantity'];
    $isbn = $_POST['isbn'];

    // Prepare a safe update query
    $stmt = $db->prepare("UPDATE books SET title = ?, quantity = ?, isbn = ? WHERE id = ?");
    // Use array values safely
    $success = $stmt->execute([$title, $quantity, $isbn, $bookID]); 

    echo $success ? "success" : "error";
}
?>
