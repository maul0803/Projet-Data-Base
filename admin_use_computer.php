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
        if ($_POST['action'] === 'add_computer') {
            $idComputer = $_POST['idComputer'];
            $idCard = $_POST['idCard'];
            $DateBorrowStart = $_POST['DateBorrowStart'];
            
            if (empty($idComputer)){
              $message ="There is no room to book.";
            }
            else{
              $insertQuery = "INSERT INTO UseComputer (DateBorrowStart, idCard, idComputer) 
                              VALUE
                              ('$DateBorrowStart', '$idCard', '$idComputer')";
              $insertResult = mysqli_query($conn, $insertQuery);

              if ($insertResult){
                $message = "Computer added successfully!";
              }
              else{
                $message = "Error adding the computer.";
              }
            }
        } elseif ($_POST['action'] === 'delete_computer') {
            $idToDelete = $_POST['idUseComputer'];
            $deleteQuery = "DELETE FROM project.UseComputer WHERE idUseComputer = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Computer deleted successfully!" : "Error deleting the computer.";
        }
        elseif ($_POST['action'] === 'return_computer') {
          $idUseComputer = $_POST['idUseComputer'];
          $idComputer = $_POST['idComputer'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
      
          // Utiliser des guillemets autour des valeurs dans la requête SQL
          $queryUpdate = "UPDATE project.UseComputer
                          SET idComputer=$idComputer,
                              idCard='$idCard',
                              DateBorrowStart='$DateBorrowStart',
                              DateBorrowEnd=NOW()
                          WHERE idUseComputer=$idUseComputer";
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Computer returned successfuly!" : "Error returning the computer.";
        } 
      }else {

          $idUseComputer = $_POST['idUseComputer'];
          $idComputer = $_POST['idComputer'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
          $DateBorrowEnd = $_POST['DateBorrowEnd'];
          
          if (empty($DateBorrowEnd)) {
              $queryUpdate = "UPDATE project.UseComputer
                              SET idComputer=$idComputer,
                                  idCard='$idCard',
                                  DateBorrowStart='$DateBorrowStart'
                              WHERE idUseComputer=$idUseComputer";
          } else {
              $queryUpdate = "UPDATE project.UseComputer
                              SET idComputer=$idComputer,
                                  idCard='$idCard',
                                  DateBorrowStart='$DateBorrowStart',
                                  DateBorrowEnd='$DateBorrowEnd'
                              WHERE idUseComputer=$idUseComputer";
          }
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Modification successful!" : "Error modifying the computer.";
      }
    }      
    

$query = "SELECT idUseComputer,DateBorrowStart,DateBorrowEnd,idCard,idComputer 
          FROM UseComputer ORDER BY idUseComputer ASC;";

$result = mysqli_query($conn, $query);

$computerQuery = "SELECT idComputer 
              FROM Computer 
              WHERE availability = 1;";
$computerResult = mysqli_query($conn, $computerQuery);
$computers = [];
while ($computerData = mysqli_fetch_assoc($computerResult)) {
    $computers[] = $computerData;
}

$cardQuery = "SELECT idCard
              FROM Card
              WHERE RessourceType='Computer';";
$cardResult = mysqli_query($conn, $cardQuery);
$cards = [];
while ($cardData = mysqli_fetch_assoc($cardResult)) {
    $cards[] = $cardData;
}



echo "
    <tr>
      <td>" . 'idUseComputer' . "</td>
      <td>" . 'idComputer' . "</td>
      <td>" . 'idCard' . "</td>
      <td>" . 'DateBorrowStart' . "</td>
      <td>" . 'DateBorrowEnd' . "</td>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idUseComputer = $rowData['idUseComputer'];
    $idComputer = $rowData['idComputer'];
    $idCard = $rowData['idCard'];
    $DateBorrowStart = $rowData['DateBorrowStart'];
    $DateBorrowEnd = $rowData['DateBorrowEnd'];

    echo "
      <tr>
        <td>
          <form method='POST' name='admin_book' >
            <input type='text' name='idUseComputer'  value='$idUseComputer' readonly size='5'>
            <input type='text' name='idComputer'  value='$idComputer' readonly size='5'>
            <input type='number' name='idCard' value='$idCard' readonly size='5'>
            <input type='date' name='DateBorrowStart' value='$DateBorrowStart'>
            <input type='date' name='DateBorrowEnd' value='$DateBorrowEnd'>
            <input type='submit' value='Modify'>
            <button type='submit' name='action' value='delete_computer'>Delete</button>
            <button type='submit' name='action' value='return_computer'>Return computer</button>
          </form>
        </td>
      </tr>
";

}

echo "
    <form method='POST' name='add_computer'>

        <select name='idComputer'>;

        ";
        foreach ($computers as $computer) {
          $ComputerId = $computer['idComputer'];
          echo "<option value='$ComputerId'>$ComputerId</option>";
      }
        echo "
        </select>

        <select name='idCard'>;
        ";
        foreach ($cards as $card) {
          $cardId = $card['idCard'];
          echo "<option value='$cardId'>$cardId</option>";
      }
        echo "
        </select>



        <input type='date' name='DateBorrowStart' placeholder='Date borrow start'>

        <input type='hidden' name='action' value='add_computer'>
        <input type='submit' value='Book a computer'>
    </form>
";

echo $message;

?>
</body>
</html>
