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

    if ($bookID && $title && $quantity && $isbn) {
        $stmt = $conn->prepare("UPDATE books SET title = ?, quantity = ?, isbn = ? WHERE id = ?");
        $stmt->bind_param("sisi", $title, $quantity, $isbn, $bookID);

        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else {
        echo "error";
    }
    $conn->close();
}
?>
