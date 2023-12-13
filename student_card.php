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

$query = "SELECT idCard, RessourceType, Activation_Date, is_active, email, Card.idUser
          FROM Card
          JOIN Users
          ON Card.idUser = Users.idUser
          WHERE Users.email='" . $_SESSION['email'] . "'
          ORDER BY Card.idUser ASC;";

$result = mysqli_query($conn, $query);


$userQuery = "SELECT idUser,email 
              FROM Users;";
$userResult = mysqli_query($conn, $userQuery);
$users = [];
while ($userData = mysqli_fetch_assoc($userResult)) {
    $users[] = $userData;
}

$RessourceTypes=['Book','Computer','MeetingRoom'];

echo "<table>";
echo "
    <tr>
      <th>" . 'idCard' . "</th>
      <th>" . 'RessourceType' . "</th>
      <th>" . 'Activation_Date' . "</th>
      <th>" . 'is_active' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idCard = $rowData['idCard'];
    $RessourceType = $rowData['RessourceType'];
    $Activation_Date = $rowData['Activation_Date'];
    $is_active = $rowData['is_active'];
    $email = $rowData['email'];
    $idUser = $rowData['idUser'];


    echo "
      <tr>
          <form method='POST' name='admin_book' >
            <td><input type='text' name='idCard'  value='$idCard' readonly size='5'></td>
            <td><input type='text' name='RessourceType' value='$RessourceType' readonly></td>
            <td><input type='date' name='Activation_Date' value='$Activation_Date' readonly></td>
            <td><input type='checkbox' disabled name='is_active' value='$is_active' " . ($is_active ? 'checked' : '') . "></td>
          </form>
      </tr>
    ";
}
echo "</table>";

echo $message;

?>
<?php include "footer.html"; ?>
</body>
</html>
