<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit;
}

$userEmail = $_SESSION['email'];

// Database connection
$conn = new mysqli("localhost", "root", "Mthozami@2004", "LibraryDB");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// SQL Query to fetch borrowings
$sql = "SELECT BorrowingID, BookID, Quantity, BorrowDate, ReturnDate, Status FROM Borrowings WHERE UserEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

$rowsHtml = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rowsHtml .= "<tr>
            <td>{$row['BorrowingID']}</td>
            <td>{$row['BookID']}</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['BorrowDate']}</td>
            <td>{$row['ReturnDate']}</td>
            <td>{$row['Status']}</td>
        </tr>";
    }
} else {
    $rowsHtml = "<tr><td colspan='6'>No borrowings found.</td></tr>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Borrowings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f7fb;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background-color: #0a1a3f;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 20px;
      font-weight: bold;
    }

    .nav-links a {
      color: white;
      margin-left: 15px;
      text-decoration: none;
      font-weight: bold;
    }

    .logout-btn {
      background-color: #e74c3c;
      border: none;
      padding: 8px 15px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    h1 {
      text-align: center;
      margin: 30px 0 10px;
      color: #0a1a3f;
    }

    .container {
      max-width: 1000px;
      margin: 0 auto 40px;
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #ecf0f1;
    }

    .footer {
      background-color: #0a1a3f;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <header class="navbar">
    <div class="logo">📚 Siza Library</div>
    <nav class="nav-links">
      <a href="UserDashboard.html">Dashboard</a>
      <a href="MyBorrowings.php">My Borrowings</a>
      <a href="#">My Fines</a>
      <a href="#">Profile</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>

  <h1>My Borrowings</h1>

  <div class="container">
    <table>
      <thead>
        <tr>
          <th>Borrowing ID</th>
          <th>Book ID</th>
          <th>Quantity</th>
          <th>Borrow Date</th>
          <th>Return Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php echo $rowsHtml; ?>
      </tbody>
    </table>
  </div>

  <footer class="footer">
    <p>© 2025 Uni Library System. All rights reserved.</p>
    <p>Contact: help@unilibrary.com | +123 456 7890</p>
  </footer>
</body>
</html>
