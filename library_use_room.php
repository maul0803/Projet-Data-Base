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
        if ($_POST['action'] === 'add_room') {
            $idMeetingRoom = $_POST['idMeetingRoom'];
            $idCard = $_POST['idCard'];
            $DateBorrowStart = $_POST['DateBorrowStart'];
            
            if (empty($idMeetingRoom)){
              $message ="There is no room to book.";
            }
            else{
              $insertQuery = "INSERT INTO UseRoom (DateBorrowStart, idCard, idMeetingRoom) 
                              VALUE
                              ('$DateBorrowStart', '$idCard', '$idMeetingRoom')";
              $insertResult = mysqli_query($conn, $insertQuery);

              if ($insertResult){
                $message = "Room added successfully!";
              }
              else{
                $message = "Error adding the room.";
              }
            }
        } elseif ($_POST['action'] === 'delete_room') {
            $idToDelete = $_POST['idUseRoom'];
            $deleteQuery = "DELETE FROM project.UseRoom WHERE idUseRoom = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Room deleted successfully!" : "Error deleting the room.";
        }
        elseif ($_POST['action'] === 'return_room') {
          $idUseRoom = $_POST['idUseRoom'];
          $idMeetingRoom = $_POST['idMeetingRoom'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
      
          // Utiliser des guillemets autour des valeurs dans la requête SQL
          $queryUpdate = "UPDATE project.UseRoom
                          SET idMeetingRoom=$idMeetingRoom,
                              idCard='$idCard',
                              DateBorrowStart='$DateBorrowStart',
                              DateBorrowEnd=NOW()
                          WHERE idUseRoom=$idUseRoom";
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Room returned successfuly!" : "Error returning the room.";
        } 
      }else {

          $idUseRoom = $_POST['idUseRoom'];
          $idMeetingRoom = $_POST['idMeetingRoom'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
          $DateBorrowEnd = $_POST['DateBorrowEnd'];
          
          if (empty($DateBorrowEnd)) {
              $queryUpdate = "UPDATE project.UseRoom
                              SET idMeetingRoom=$idMeetingRoom,
                                  idCard='$idCard',
                                  DateBorrowStart='$DateBorrowStart'
                              WHERE idUseRoom=$idUseRoom";
          } else {
              $queryUpdate = "UPDATE project.UseRoom
                              SET idMeetingRoom=$idMeetingRoom,
                                  idCard='$idCard',
                                  DateBorrowStart='$DateBorrowStart',
                                  DateBorrowEnd='$DateBorrowEnd'
                              WHERE idUseRoom=$idUseRoom";
          }
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Modification successful!" : "Error modifying the room.";
      }
    }      
    

$query = "SELECT idUseRoom,DateBorrowStart,DateBorrowEnd,idCard,idMeetingRoom 
          FROM UseRoom ORDER BY idUseRoom ASC;";

$result = mysqli_query($conn, $query);

$roomQuery = "SELECT idMeetingRoom 
              FROM MeetingRoom 
              WHERE availability = TRUE;";
$roomResult = mysqli_query($conn, $roomQuery);
$rooms = [];
while ($roomData = mysqli_fetch_assoc($roomResult)) {
    $rooms[] = $roomData;
}

$cardQuery = "SELECT idCard
              FROM Card
              WHERE RessourceType='MeetingRoom';";
$cardResult = mysqli_query($conn, $cardQuery);
$cards = [];
while ($cardData = mysqli_fetch_assoc($cardResult)) {
    $cards[] = $cardData;
}


echo "<table>";
echo "
    <tr>
      <th>" . 'idUseRoom' . "</th>
      <th>" . 'idMeetingRoom' . "</th>
      <th>" . 'idCard' . "</th>
      <th>" . 'DateBorrowStart' . "</th>
      <th>" . 'DateBorrowEnd' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idUseRoom = $rowData['idUseRoom'];
    $idMeetingRoom = $rowData['idMeetingRoom'];
    $idCard = $rowData['idCard'];
    $DateBorrowStart = $rowData['DateBorrowStart'];
    $DateBorrowEnd = $rowData['DateBorrowEnd'];

    echo "
      <tr>
          <form method='POST' name='admin_book' >
            <td><input type='text' name='idUseRoom'  value='$idUseRoom' readonly size='5'></td>
            <td><input type='text' name='idMeetingRoom'  value='$idMeetingRoom' readonly size='5'></td>
            <td><input type='number' name='idCard' value='$idCard' readonly size='5'></td>
            <td><input type='date' name='DateBorrowStart' value='$DateBorrowStart'></td>
            <td><input type='date' name='DateBorrowEnd' value='$DateBorrowEnd'></td>
            <td><input type='submit' value='Modify'></td>
            <td><button type='submit' name='action' value='delete_room'>Delete</button></td>
            <td><button type='submit' name='action' value='return_room'>Return room</button></td>
          </form>
      </tr>
    ";
}
echo "</table>";

echo "
    <form method='POST' name='add_room'>

        <select name='idMeetingRoom'>;

        ";
        foreach ($rooms as $room) {
          $MeetingRoomId = $room['idMeetingRoom'];
          echo "<option value='$MeetingRoomId'>$MeetingRoomId</option>";
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

        <input type='hidden' name='action' value='add_room'>
        <input type='submit' value='Book a room'>
    </form>
";

echo $message;

?>
</body>
</html>
