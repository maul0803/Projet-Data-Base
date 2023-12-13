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
      if ($_POST['action'] === 'add_card') {
          $RessourceType = $_POST['RessourceType'];
          $idUser = $_POST['idUser'];

          $insertQuery = "INSERT INTO Card (RessourceType, Activation_Date, is_active, idUser) 
                          VALUES
                          ('$RessourceType', NOW(), 1, $idUser)";
          $insertResult = mysqli_query($conn, $insertQuery);

          if ($insertResult) {
              $message = "Card added successfully!";
          } else {
              $message = "Error adding the card." . mysqli_error($conn);
          }
        } elseif ($_POST['action'] === 'delete_card') {
            $idToDelete = $_POST['idCard'];
            $deleteQuery = "DELETE FROM project.Card WHERE idCard = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Card deleted successfully!" : "Error deleting the card.";
        }
        elseif ($_POST['action'] === 'desactivate_activate_card') {
          $RessourceType = $_POST['RessourceType'];
          $idUser = $_POST['idUser'];
          $Activation_Date = $_POST['Activation_Date'];
          $is_active = isset($_POST['is_active']) ? 1 : 0;
          $idCard = $_POST['idCard'];
      
          // Utiliser des guillemets autour des valeurs dans la requête SQL
          if ($is_active == 1) {
              $queryUpdate = "UPDATE project.Card
                              SET RessourceType='$RessourceType',
                                  Activation_Date='$Activation_Date',
                                  is_active=0,
                                  idUser='$idUser'
                              WHERE idCard=$idCard";
      
              $resultUpdate = mysqli_query($conn, $queryUpdate);
              $message = $resultUpdate ? "Card deactivated successfully!" : "Error.";
          } else {
              $queryUpdate = "UPDATE project.Card
                              SET RessourceType='$RessourceType',
                                  Activation_Date=NOW(),
                                  is_active=1,
                                  idUser='$idUser'
                              WHERE idCard=$idCard";
      
              $resultUpdate = mysqli_query($conn, $queryUpdate);
              $message = $resultUpdate ? "Card activated successfully!" : "Error.";
          }
      }
      
        } else {
          $RessourceType = $_POST['RessourceType'];
          $idUser = $_POST['idUser'];
          $Activation_Date = $_POST['Activation_Date'];
          $is_active = isset($_POST['is_active']) ? 1 : 0;
          $idCard = $_POST['idCard'];

          $queryUpdate = "UPDATE project.Card
          SET RessourceType='$RessourceType',
              Activation_Date=NOW(),
              is_active=$is_active,
              idUser='$idUser'
          WHERE idCard=$idCard";

          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Card updated successfully!" : "Error.";
      }
    
    
}

$query = "SELECT  idCard,RessourceType,Activation_Date,is_active,email,Card.idUser
          FROM Card
          JOIN Users
          ON Card.idUser=Users.idUser ORDER BY idUser ASC;";

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
      <th>" . 'email' . "</th>
      <th>" . 'idUser' . "</th>
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

            <td><select name='RessourceType'>
            <option value='$RessourceType'>$RessourceType</option>
            ";
            foreach ($RessourceTypes as $Ressource_Type) {
              if ($Ressource_Type!=$RessourceType){
                echo "<option value='$Ressource_Type'>$Ressource_Type</option>";
              }

            }
            echo "
            </select></td>

            <td><input type='date' name='Activation_Date' value='$Activation_Date' readonly></td>
            <td><input type='checkbox' name='is_active' value='$is_active' " . ($is_active ? 'checked' : '') . "></td>
            <td><input type='text' name='idUser' value='$idUser' readonly size='5'></td>
            <td><input type='text' name='email' value='$email' readonly></td>
            <td><input type='submit' value='Modify'></td>
            <td><button type='submit' name='action' value='delete_card'>Delete</button></td>
            <td><button type='submit' name='action' value='desactivate_activate_card'>Activate/Desactivate Card</button></td>
          </form>
      </tr>
    ";
}
echo "</table>";

echo "
    <form method='POST' name='add_card'>

        <select name='RessourceType'>
        <option value='Book'>Book</option>
        <option value='Computer'>Computer</option>
        <option value='MeetingRoom'>MeetingRoom</option>
        </select>

        <select name='idUser'>;
        ";
        foreach ($users as $user) {
          $idUser = $user['idUser'];
          $email = $user['email'];
          echo "<option value='$idUser'>$email</option>";
        }
        echo "
        </select>

        <input type='hidden' name='action' value='add_card'>
        <input type='submit' value='Add Card'>
    </form>
";

echo $message;

?>
</body>
</html>
