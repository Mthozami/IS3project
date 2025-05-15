<?php
// I'm connecting to my Library's MySQL
$host = 'localhost';
$username = 'root';
$password = 'Mthozami@2004';
$dbname = 'LibraryDB';

$conn = new mysqli($host, $username, $password, $dbname);
// If I can't connect, I stop
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// I check if the person filled in title, quantity, and isbn
if (isset($_POST['title'], $_POST['quantity'], $_POST['isbn'])) {
    $title = $_POST['title'];
    //  Quantity must be a number
    $quantity = intval($_POST['quantity']); 
    $isbn = $_POST['isbn'];

    // Start transaction
    $conn->begin_transaction(); // I start a safe transaction

    // Now I insert the book into my shelf safely
    $stmt = $conn->prepare("INSERT INTO Books (Title, Quantity, ISBN) VALUES (?, ?, ?)");
    // Safe! No SQL injection!
    $stmt->bind_param("sis", $title, $quantity, $isbn); 

    if ($stmt->execute()) {
        // All good, I save the changes
        $conn->commit(); 
        //  Go back to the add page with a smile
        header("Location: AddBook.html?success=true"); 
        exit;
    } else {
        // Something went wrong, I undo
        $conn->rollback(); 
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid input!";
}

$conn->close();
?>
