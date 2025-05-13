<?php
$host = "localhost";
$dbname = "LibraryDB";
$username = "root";
$password = "Mthozami@2004";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Use JOINs to get FullName from Users and Title from Books
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
            b.Status
        FROM Borrowings b
        JOIN Users u ON b.UserID = u.UserID
        JOIN Books bk ON b.BookID = bk.BookID
        ORDER BY b.BorrowingID ASC
    ");
    $stmt->execute();
    $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($borrowings as $row) {
        $status = htmlspecialchars($row['Status']);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['BorrowingID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BookTitle']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BookID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BorrowedDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReturnDate']) . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td class='actions'>";
        if ($status === 'borrowed') {
            echo "<button onclick='markReturned(\"{$row['BorrowingID']}\", \"{$row['BookID']}\", \"{$row['Quantity']}\")' class='returned'>Mark Returned</button>";
        }
        echo "</td></tr>";
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='10'>Error loading borrowings: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
