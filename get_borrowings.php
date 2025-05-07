<?php
$servername = "localhost";
$username = "root";
$password = "Mthozami@2004";
$dbname = "LibraryDB";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
        SELECT 
            b.BorrowingID,
            b.BookID,
            b.UserID,
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
        echo "<tr data-book-id='" . htmlspecialchars($row['BookID']) . "' data-quantity='" . htmlspecialchars($row['Quantity']) . "'>";
        echo "<td>" . htmlspecialchars($row['BorrowingID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BookID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BorrowedDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReturnDate']) . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td class='actions'>";
        if ($status === 'borrowed') {
            echo "<button onclick='markReturned(this)' class='returned'>Mark Returned</button>";
        }
        echo "</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='8'>Error loading borrowings: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
