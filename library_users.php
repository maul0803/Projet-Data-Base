<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <link href="login.css" rel="stylesheet">
</head>
<?php include "header.php"; ?>
<?php include "library_footer.html"; ?>
<body>
<?php
$DB_USERNAME = $_SESSION['DB_USERNAME'];
$DB_PASSWORD = $_SESSION['DB_PASSWORD'];
$DB_SERVER = $_SESSION['DB_SERVER'];
$DB_NAME = $_SESSION['DB_NAME'];
$email = $_SESSION['email'];
$idUser = $_SESSION['idUser'];
$profile = $_SESSION['profile'];
if ($profile!='Library Agent'){
  header("Location: login.php");
}

$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME) or die("ERROR: Unable to connect. " . mysqli_connect_error());
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
      if ($_POST['action'] === 'add_user') {
          $profil = $_POST['profil'];
          $first_name = $_POST['first_name'];
          $last_name = $_POST['last_name'];
          $email = $_POST['email'];
          $postal_address = $_POST['postal_address'];
          $phone_number = $_POST['phone_number'];
          $username = $_POST['username'];
          $password = $_POST['password'];
          $is_registered = isset($_POST['is_registered']) ? 1 : 0;

          $insertQuery = "INSERT INTO Users (profil, first_name, last_name, email, postal_address, phone_number, username, password, is_registered) 
                          VALUES 
                          ('$profil', '$first_name', '$last_name', '$email', '$postal_address', '$phone_number', '$username', '$password', '$is_registered')";
          $insertResult = mysqli_query($conn, $insertQuery);

          if ($insertResult) {
              $message = "User added successfully!";
          } else {
              $message = "Error adding the User.";
          }
        } elseif ($_POST['action'] === 'delete_user') {
            $idToDelete = $_POST['idUser'];
            $deleteQuery = "DELETE FROM project.Users WHERE idUser = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "User deleted successfully!" : "Error deleting the user.";
        }
      }
      
      else {
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
}

$query = "SELECT idUser,profil,first_name,last_name,email,postal_address,phone_number,username,password,is_registered 
          FROM Users ORDER BY idUser ASC;";

$result = mysqli_query($conn, $query);


$userQuery = "SELECT idUser,email 
              FROM Users;";

$profils=['Administrator', 'Library Agent', 'Student'];

echo "<table style='margin-left: 15%; margin-top: -300px;'>";
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
            <td><input type='text' name='profil'  value='$profil' readonly></td>
            <td><input type='text' name='first_name' value='$first_name' readonly size='15'></td>
            <td><input type='text' name='last_name' value='$last_name' readonly size='15'></td>
            <td><input type='text' name='email' value='$email' readonly size='15'></td>
            <td><input type='text' name='postal_address' value='$postal_address' readonly size='15'></td>
            <td><input type='number' name='phone_number' value='$phone_number' readonly size='15'></td>
            <td><input type='text' name='username' value='$username' readonly size='15'></td>
            <td><input type='text' name='password' value='$password' readonly size='15'></td>
            <td><input type='checkbox' name='is_registered' disabled value='$is_registered' " . ($is_registered ? 'checked' : '') . "></td>
          </form>
        </td>
      </tr>
";
}
echo "</table><br>";

?>
<div style='bottom: 0; width: 100%;'>
<?php include "footer.html"; ?>
</div>
</body>
</html>
