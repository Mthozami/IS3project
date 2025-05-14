<?php
// Database connection details
$host = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection to the database failed
if ($conn->connect_error) {
    // Send HTTP 500 error response
    http_response_code(500); 
    // Display error message
    echo "Database connection failed."; 
    // Stop further execution
    exit; 
}

/* SQL query to fetch all borrowings that are still marked as 'borrowed'
and where the return date has already passed (i.e., they are overdue)*/
$query = "
  SELECT 
    b.BorrowingID, b.ReturnDate
  FROM Borrowings b
  WHERE b.Status = 'borrowed' AND b.ReturnDate < CURDATE()
";

// Execute the query
$result = $conn->query($query);

// If the query returns rows (i.e., there are overdue borrowings)
if ($result && $result->num_rows > 0) {
    // Loop through each overdue borrowing
    while ($row = $result->fetch_assoc()) {
        // Get the borrowing ID and return date
        $borrowingID = $row["BorrowingID"];
        // Convert return date to DateTime object
        $returnDate = new DateTime($row["ReturnDate"]); 
        $today = new DateTime(); // Get today's date

        // Check again if the book is overdue (extra validation)
        if ($returnDate < $today) {
            // Calculate how many days the book is overdue
            $daysOverdue = $returnDate->diff($today)->days;

            // Fine amount: R25 per overdue day
            $amount = 25 * $daysOverdue;

            // Check if a fine already exists for this borrowing
            $check = $conn->prepare("SELECT FineID, IsPaid FROM Fines WHERE BorrowingID = ? ORDER BY FineID DESC LIMIT 1");
            // Bind borrowing ID as integer
            $check->bind_param("i", $borrowingID); 
            // Execute the prepared statement
            $check->execute(); 
            // Get result
            $resultCheck = $check->get_result(); 

            // If a fine record exists for this borrowing
            if ($resultCheck && $resultCheck->num_rows > 0) {
                // Get fine record
                $fine = $resultCheck->fetch_assoc(); 
                // If the fine has not been paid, update the fine amount
                if (!$fine["IsPaid"]) {
                    $update = $conn->prepare("UPDATE Fines SET Amount = ? WHERE FineID = ?");
                    // Bind amount (double) and fine ID (int)
                    $update->bind_param("di", $amount, $fine["FineID"]); 
                    // Execute update
                    $update->execute(); 
                }
            } else {
                // No fine exists for this borrowing; insert a new fine
                $insert = $conn->prepare("INSERT INTO Fines (BorrowingID, Amount) VALUES (?, ?)");
                // Bind borrowing ID and calculated amount
                $insert->bind_param("id", $borrowingID, $amount); 
                // Execute insert
                $insert->execute(); 
            }
        }
    }
}

// Close the database connection
$conn->close();

// Output a success message
echo "Fines updated.";
?>
