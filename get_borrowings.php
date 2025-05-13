<?php
$host = "localhost";
$dbname = "LibraryDB";
$username = "root";
$password = "Mthozami@2004";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        echo "<td class='actions'>";
        if ($status === 'borrowed') {
            if ($hasFine) {
                echo "<button disabled class='returned'>Fine Issued</button>";
            } else {
                echo "<button onclick='markReturned(\"{$borrowingID}\", \"{$bookID}\", \"{$quantity}\")' class='returned'>Mark Returned</button>";
            }
            // Always allow "Mark as Lost"
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
