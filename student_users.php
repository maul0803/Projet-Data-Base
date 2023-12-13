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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idUser = $_POST['idUser'];
        $profil = $_POST['profil'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $postal_address = $_POST['postal_address'];
        $phone_number = $_POST['phone_number'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $is_registered = isset($_POST['is_registered']) ? 1 : 0;
    
        $queryUpdate = "UPDATE project.Users
                        SET profil='$profil',
                            first_name='$first_name',
                            last_name='$last_name',
                            postal_address='$postal_address',
                            phone_number='$phone_number',
                            username='$username',
                            password='$password',
                            is_registered='$is_registered'
                        WHERE idUser=$idUser";
    
        $resultUpdate = mysqli_query($conn, $queryUpdate);
        $message = $resultUpdate ? "User updated successfully!" : "Error updating user.";
    
}

$query = "SELECT idUser,profil,first_name,last_name,email,postal_address,phone_number,username,password,is_registered
          FROM Users 
          WHERE Users.email='".$_SESSION['email']."';";

$result = mysqli_query($conn, $query);


$userQuery = "SELECT idUser,email 
              FROM Users;";

$profils=['Administrator', 'Library Agent', 'Student'];

echo "<table>";
echo "
    <tr>
      <th>" . 'idUser' . "</th>
      <th>" . 'profil' . "</th>
      <th>" . 'first_name' . "</th>
      <th>" . 'last_name' . "</th>
      <th>" . 'email' . "</th>
      <th>" . 'postal_address' . "</th>
      <th>" . 'phone_number' . "</th>
      <th>" . 'username' . "</th>
      <th>" . 'password' . "</th>
      <th>" . 'is_registered' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idUser = $rowData['idUser'];
    $profil = $rowData['profil'];
    $first_name = $rowData['first_name'];
    $last_name = $rowData['last_name'];
    $email = $rowData['email'];
    $postal_address = $rowData['postal_address'];
    $phone_number = $rowData['phone_number'];
    $username = $rowData['username'];
    $password = $rowData['password'];
    $is_registered = $rowData['is_registered'];


    echo "
      <tr>
          <form method='POST' name='admin_book' >
            <td><input type='text' name='idUser'  value='$idUser' readonly size='5'></td>
            <td><input type='text' name='profil' value='$profil' size='15' readonly></td>
            <td><input type='text' name='first_name' value='$first_name' size='15'></td>
            <td><input type='text' name='last_name' value='$last_name' size='15'></td>
            <td><input type='text' name='email' value='$email' size='15' readonly></td>
            <td><input type='text' name='postal_address' value='$postal_address' size='15'></td>
            <td><input type='number' name='phone_number' value='$phone_number' min='0' required size='15'></td>
            <td><input type='text' name='username' value='$username' size='15'></td>
            <td><input type='text' name='password' value='$password' size='15'></td>
            <td><input type='checkbox' name='is_registered' value='$is_registered' " . ($is_registered ? 'checked' : '') . "></td>
            <td><input type='submit' value='Modify'></td>
          </form>
      </tr>
    ";
}
echo "</table>";

echo $message;

?>
</body>
<?php include "footer.html"; ?>
</html>
