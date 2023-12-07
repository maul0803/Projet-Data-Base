<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <link href="login.css" rel="stylesheet">
</head>
<?php include "header.php"; ?>
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
      if ($_POST['action'] === 'add_room') {
          $insertQuery = "INSERT INTO Computer (availability) 
                          VALUE
                          (true);";
          $insertResult = mysqli_query($conn, $insertQuery);

          if ($insertResult) {
              $message = "Computer added successfully!";
          } else {
              $message = "Error adding the computer.";
          }
        } elseif ($_POST['action'] === 'delete_room') {
            $idToDelete = $_POST['idComputer'];
            $deleteQuery = "DELETE FROM project.Computer WHERE idComputer = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Computer deleted successfully!" : "Error deleting the computer.";
        }
      }
      
      else {
        $idComputer = $_POST['idComputer'];
        $availability = isset($_POST['availability']) ? 1 : 0;
    
        $queryUpdate = "UPDATE project.Computer
                        SET availability=$availability
                        WHERE idComputer=$idComputer";
    
        $resultUpdate = mysqli_query($conn, $queryUpdate);
        $message = $resultUpdate ? "Computer updated successfully!" : "Error updating the computer.";
    }
    
    
    
}

$query = "SELECT idComputer,availability 
          FROM Computer ORDER BY idComputer ASC;";

$result = mysqli_query($conn, $query);


echo "
    <tr>
      <td>" . 'idComputer' . "</td>
      <td>" . 'is_available' . "</td>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idComputer = $rowData['idComputer'];
    $availability = $rowData['availability'];

    echo "
      <tr>
        <td>
          <form method='POST' name='admin_book' >
            <input type='text' name='idComputer'  value='$idComputer' readonly size='5'>
            <input type='checkbox' name='availability' value='$availability' " . ($availability ? 'checked' : '') . ">
            <input type='submit' value='Modify'>
            <button type='submit' name='action' value='delete_room'>Delete</button>
          </form>
        </td>
      </tr>
";

}

echo "
    <form method='POST' name='add_room'>
        <input type='hidden' name='action' value='add_room'>
        <input type='submit' value='Add Computer'>
    </form>
";

echo $message;

?>
<?php include "admin_footer.html"; ?>
</body>
</html>
