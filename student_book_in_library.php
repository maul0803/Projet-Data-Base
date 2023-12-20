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

$query = "SELECT idBookInLibrary,Title,price,date_of_purchase,availability
          FROM project.BookInLibrary
          JOIN project.Book 
          ON project.BookInLibrary.idBook=project.Book.idBook 
          WHERE BookInLibrary.availability=True
          ORDER BY idBookInLibrary ASC;";
$result = mysqli_query($conn, $query);

$titleQuery = "SELECT idBook,Title FROM project.Book";
$titleResult = mysqli_query($conn, $titleQuery);
$titles = [];
while ($titleData = mysqli_fetch_assoc($titleResult)) {
    $titles[] = $titleData;
}

echo "<table style='margin-left: 45%; margin-top: -8%;'>";
echo "
    <tr>
      <th>" . 'Title' . "</th>
      <th>" . 'availability' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $Title = $rowData['Title'];
    $availability = $rowData['availability'];

    echo "
      <tr>
          <form method='POST' name='admin_book' readonly >
            <td><input type='text' name='Title' value='$Title' readonly></td>
            <td><input type='checkbox' name='availability' disabled value='$availability' " . ($availability ? 'checked' : '') . "></td>
          </form>
      </tr>
    ";
}
echo "</table><br>";

?>
<div style='bottom: 0; position: fixed; width: 99%;'>
<?php include "footer.html"; ?>
</div>
</body>
</html>
