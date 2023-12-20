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

$query = "SELECT idComputer,availability 
          FROM Computer 
          WHERE availability=True
          ORDER BY idComputer ASC;";

$result = mysqli_query($conn, $query);

echo "<table  style='margin-left: 40%; margin-top: -8%;'>";
echo "
    <tr>
      <th>" . 'idComputer' . "</th>
      <th>" . 'is_available' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idComputer = $rowData['idComputer'];
    $availability = $rowData['availability'];

    echo "
      <tr>
          <form method='POST' name='admin_book' >
            <td><input type='text' name='idComputer'  value='$idComputer' readonly size='5'></td>
            <td><input type='checkbox' disabled name='availability' value='$availability' " . ($availability ? 'checked' : '') . "></td>
          </form>
      </tr>
    ";
}
echo "</table><br>";

echo "<div style='margin-left: 40%;'>";
echo $message;
echo "</div>";
?>

<div style='bottom: 0; position: fixed; width: 99%;'>
<?php include "footer.html"; ?>
</div>
</body>
</html>
