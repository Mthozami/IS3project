<?php
// connect to my library database
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// I try to open a  database
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    //  when the is an issue with communicating with a database 
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

// I only help if someone POSTs to me and gives me a fine ID
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["fineID"])) {
    //  I make sure it's a number
    $fineID = intval($_POST["fineID"]); 
    if ($fineID <= 0) {
        //  No cheating with bad numbers!
        http_response_code(400);
        echo "Invalid fine ID.";
        exit;
    }

    // STEP 1: I go check the fine in the system
    $stmt = $conn->prepare("SELECT Amount, IsPaid FROM Fines WHERE FineID = ?");
    //  Safe from SQL injection!
    $stmt->bind_param("i", $fineID); 
    $stmt->execute();
    $stmt->bind_result($amount, $isPaid);

    if (!$stmt->fetch()) {
        //  I couldn't find the fine
        http_response_code(404);
        echo "Fine not found.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    if ($isPaid) {
        //  Sorry, you can't change something already paid!
        echo "This fine is already paid and cannot be adjusted.";
        exit;
    }

    // STEP 2: I add R600 because the book was lost or broken 
    $adjustedAmount = $amount + 600;

    $stmt = $conn->prepare("UPDATE Fines SET Amount = ? WHERE FineID = ?");
     // Safe again from sql injection!
    $stmt->bind_param("di", $adjustedAmount, $fineID);

    if ($stmt->execute()) {
        //  updated fine  now!
        echo "Fine adjusted for damage/loss. New amount: R" . number_format($adjustedAmount, 2);
    } else {
        http_response_code(500);
        echo "Failed to adjust fine.";
    }

    $stmt->close();
} else {
    //  Someone is being sneaky or confused!
    http_response_code(400);
    echo "Invalid request.";
}

//  I close the database  nicely
$conn->close(); 
?>
