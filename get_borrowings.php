<?php
// This is where we tell the computer how to talk to the database
$host = "localhost";
$dbname = "LibraryDB";
$username = "root";
$password = "Mthozami@2004";

try {
    // Try to connect to the library database
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all the books that have been borrowed and the info about them
    $stmt = $conn->prepare("
        SELECT 
            b.BorrowingID,
            u.FullName,
            b.UserID,
            bk.Title AS BookTitle,
            b.BookID,
            b.Quantity,
            b.BorrowedDate,
            b.ReturnDate,
            b.Status,
            EXISTS (
                SELECT 1 FROM Fines f WHERE f.BorrowingID = b.BorrowingID
            ) AS HasFine
        FROM Borrowings b
        JOIN Users u ON b.UserID = u.UserID
        JOIN Books bk ON b.BookID = bk.BookID
        ORDER BY b.BorrowingID ASC
    ");
    $stmt->execute();

    // Get all the rows back
    $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Show each row as a line in a table
    foreach ($borrowings as $row) {
        $status = htmlspecialchars($row['Status']);
        $borrowingID = htmlspecialchars($row['BorrowingID']);
        $bookID = htmlspecialchars($row['BookID']);
        $quantity = htmlspecialchars($row['Quantity']);
        $hasFine = $row['HasFine'];

        echo "<tr>";
        echo "<td>{$borrowingID}</td>";
        echo "<td>" . htmlspecialchars($row['BookTitle']) . "</td>";
        echo "<td>{$bookID}</td>";
        echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
        echo "<td>{$quantity}</td>";
        echo "<td>" . htmlspecialchars($row['BorrowedDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReturnDate']) . "</td>";
        echo "<td>{$status}</td>";

        // Show buttons based on status
        echo "<td class='actions'>";
        if ($status === 'borrowed') {
            if ($hasFine) {
                echo "<button disabled class='returned'>Fine Issued</button>";
            } else {
                echo "<button onclick='markReturned(\"{$borrowingID}\", \"{$bookID}\", \"{$quantity}\")' class='returned'>Mark Returned</button>";
            }
            echo "<button onclick='markLost(\"{$borrowingID}\")' class='lost'>Mark as Lost</button>";
        } else {
            echo "-";
        }
        echo "</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='10'>Error loading borrowings: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
