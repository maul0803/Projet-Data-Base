<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <link href="login.css" rel="stylesheet">
</head>
<?php include "header.php"; ?>
<?php include "student_footer.html"; ?>

<body>
<?php
$DB_USERNAME = $_SESSION['DB_USERNAME'];
$DB_PASSWORD = $_SESSION['DB_PASSWORD'];
$DB_SERVER = $_SESSION['DB_SERVER'];
$DB_NAME = $_SESSION['DB_NAME'];
$email = $_SESSION['email'];
$idUser = $_SESSION['idUser'];
$profile = $_SESSION['profile'];
if ($profile!='Student'){
  header("Location: login.php");
}
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME) or die("ERROR: Unable to connect. " . mysqli_connect_error());
$message = "";

$query = "SELECT idBorrow, Title, Borrow.idCard, Borrow.idBookInLibrary, DateBorrowStart, DateBorrowEnd
          FROM project.Borrow
          JOIN project.BookInLibrary
          ON Borrow.idBookInLibrary = BookInLibrary.idBookInLibrary
          JOIN Book
          ON BookInLibrary.idBook = Book.idBook
          JOIN Card
          ON Card.idCard = Borrow.idCard
          JOIN Users
          ON Card.idUser = Users.idUser
          WHERE Users.email='" . $_SESSION['email'] . "'
          ORDER BY idBorrow ASC;";
$result = mysqli_query($conn, $query);

$registerQuery = "SELECT is_registered
              FROM Users
              WHERE Users.email='" . $_SESSION['email'] . "'";
$registerResult = mysqli_query($conn, $registerQuery);
$registerData = mysqli_fetch_assoc($registerResult);
$is_registered=$registerData['is_registered'];

$countQuery = "SELECT COUNT(*)
          FROM project.Borrow
          JOIN project.BookInLibrary
          ON Borrow.idBookInLibrary = BookInLibrary.idBookInLibrary
          JOIN Book
          ON BookInLibrary.idBook = Book.idBook
          JOIN Card
          ON Card.idCard = Borrow.idCard
          JOIN Users
          ON Card.idUser = Users.idUser
          WHERE availability=False
          AND Users.email='" . $_SESSION['email'] . "'";
$countResult = mysqli_query($conn, $countQuery);
$countData = mysqli_fetch_assoc($countResult);
$count=$countData['COUNT(*)'];

echo "<table>";
echo "
    <tr>
      <th>" . 'idBorrow' . "</th>
      <th>" . 'idBookInLibrary' . "</th>
      <th>" . 'Title' . "</th>
      <th>" . 'idCard' . "</th>
      <th>" . 'DateBorrowStart' . "</th>
      <th>" . 'DateBorrowEnd' . "</th>
    </tr>
";
while ($rowData = mysqli_fetch_assoc($result)) {
    $idBorrow = $rowData['idBorrow'];
    $Title = $rowData['Title'];
    $idCard = $rowData['idCard'];
    $DateBorrowStart = $rowData['DateBorrowStart'];
    $DateBorrowEnd = $rowData['DateBorrowEnd'];
    $idBookInLibrary = $rowData['idBookInLibrary'];

    if ($DateBorrowEnd == '0000-00-00') {
      $DateEnd = time();
      $DateStart = strtotime($DateBorrowStart);
      $dateDifference = $DateEnd - $DateStart;
      $daysDifference = floor($dateDifference / (60 * 60 * 24));
      $color = ($daysDifference <= 15) ? 'green' : 'red';
  } else {
      $DateEnd = strtotime($DateBorrowEnd);
      $DateStart = strtotime($DateBorrowStart);
      $dateDifference = $DateEnd - $DateStart;
      $daysDifference = floor($dateDifference / (60 * 60 * 24));
      $color = ($daysDifference <= 15) ? 'green' : 'red';
  }
  
    echo "
      <tr>
          <form method='POST' name='admin_book' readonly >
            <td><input type='text' name='idBorrow'  value='$idBorrow' readonly size='5' style='color: $color;'></td>
            <td><input type='text' name='idBookInLibrary'  value='$idBookInLibrary' readonly size='5' style='color: $color;'></td>
            <td><input type='text' name='Title' value='$Title' readonly style='color: $color;'></td>
            <td><input type='text' name='idCard' value='$idCard' readonly size='5' style='color: $color;'></td>
            <td><input type='date' name='DateBorrowStart' value='$DateBorrowStart' readonly style='color: $color;'></td>
            <td><input type='date' name='DateBorrowEnd' value='$DateBorrowEnd' readonly style='color: $color;'></td>
          </form>
      </tr>
    ";
}
echo "</table>";

if ($is_registered) {
  echo (5 - $count) . " borrow(s) remaining";
} else {
  echo (1 - $count) . " borrow(s) remaining";
}

echo $message;

?>
<?php include "footer.html"; ?>
</body>
</html>
