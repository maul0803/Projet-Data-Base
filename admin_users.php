<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <link href="login.css" rel="stylesheet">
</head>
<?php include "header.php"; ?>
<?php include "admin_footer.html"; ?>
<body>
<?php
$DB_USERNAME = $_SESSION['DB_USERNAME'];
$DB_PASSWORD = $_SESSION['DB_PASSWORD'];
$DB_SERVER = $_SESSION['DB_SERVER'];
$DB_NAME = $_SESSION['DB_NAME'];
$email = $_SESSION['email'];
$idUser = $_SESSION['idUser'];
$profile = $_SESSION['profile'];
if ($profile!='Administrator'){
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


echo "
    <tr>
      <td>" . 'idUser' . "</td>
      <td>" . 'profil' . "</td>
      <td>" . 'first_name' . "</td>
      <td>" . 'last_name' . "</td>
      <td>" . 'email' . "</td>
      <td>" . 'postal_address' . "</td>
      <td>" . 'phone_number' . "</td>
      <td>" . 'username' . "</td>
      <td>" . 'password' . "</td>
      <td>" . 'is_registered' . "</td>
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
        <td>
          <form method='POST' name='admin_book' >
            <input type='text' name='idUser'  value='$idUser' readonly size='5'>

            <select name='profil'>
            <option value='$profil'>$profil</option>
            ";
            foreach ($profils as $profil_) {
              if ($profil_!=$profil){
                echo "<option value='$profil_'>$profil_</option>";
              }

            }
            echo "
            </select>

            <input type='text' name='first_name' value='$first_name' size='15'>
            <input type='text' name='last_name' value='$last_name' size='15'>
            <input type='text' name='email' value='$email' size='15'>
            <input type='text' name='postal_address' value='$postal_address' size='15'>
            <input type='number' name='phone_number' value='$phone_number' size='15'>
            <input type='text' name='username' value='$username' size='15'>
            <input type='text' name='password' value='$password' size='15'>
            <input type='checkbox' name='is_registered' value='$is_registered' " . ($is_registered ? 'checked' : '') . ">
            <input type='submit' value='Modify'>
            <button type='submit' name='action' value='delete_user'>Delete</button>
          </form>
        </td>
      </tr>
";

}

echo "
    <form method='POST' name='add_user'>

        <select name='profil'>
        ";
        foreach ($profils as $profil_) {
          echo "<option value='$profil_'>$profil_</option>";
        }
        echo "
        </select>

        <input type='text' name='first_name' placeholder='First Name' size='15'>
        <input type='text' name='last_name' placeholder='Last Name' size='15'>
        <input type='text' name='email' placeholder='email' size='15'>
        <input type='text' name='postal_address' placeholder='postal address' size='15'>
        <input type='number' name='phone_number' placeholder='phone number' size='15'>
        <input type='text' name='username' placeholder='username'>
        <input type='text' name='password' placeholder='password' size='15'>
        <input type='checkbox' name='is_registered' value=0>

        <input type='hidden' name='action' value='add_user'>
        <input type='submit' value='Add User'>
    </form>
";

echo $message;

?>
</body>
</html>
