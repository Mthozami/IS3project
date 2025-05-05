<?php
// ✅ Include database connection using PDO
$host = "localhost";
$dbname = "LibraryDB";
$username = "root";
$password = "Mzamoh@25";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ✅ SHOW FORM WHEN ACCESSED VIA GET (Edit button sends data via GET)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['BookID'])) {
    $bookID = $_GET['BookID'];
    $title = $_GET['Title'] ?? '';
    $quantity = $_GET['Quantity'] ?? '';
    $isbn = $_GET['ISBN'] ?? '';
    ?>

    <!-- ✅ Edit Form -->
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

// ✅ ORIGINAL POST HANDLER (Do not remove)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookID = $_POST['bookID'];  // The book ID to identify which record to update
    $title = $_POST['title'];    // The new title for the book
    $quantity = $_POST['quantity'];  // The new quantity of the book
    $isbn = $_POST['isbn'];  // The new ISBN for the book

    // Debugging: Log the received values
    error_log("Received data: BookID: $bookID, Title: $title, Quantity: $quantity, ISBN: $isbn");

    // Validate the inputs
    if (!empty($bookID) && !empty($title) && !empty($quantity) && !empty($isbn)) {
        // Update the book in the database
        $sql = "UPDATE books SET title = ?, quantity = ?, isbn = ? WHERE id = ?";
        $stmt = $db->prepare($sql);

        // Execute the query with the user inputs
        $result = $stmt->execute([$title, $quantity, $isbn, $bookID]);

        // Check if the update was successful
        if ($result) {
            error_log("Book updated successfully.");
            echo 'Book updated successfully.';
        } else {
            error_log("Error executing query: " . $stmt->errorInfo()[2]);
            echo 'Error updating book.';
        }
    } else {
        error_log("Error: Empty fields detected.");
        echo 'Please fill in all fields.';
    }
}
?>
