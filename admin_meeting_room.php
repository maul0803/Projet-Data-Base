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

$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME) or die("ERROR: Unable to connect. " . mysqli_connect_error());
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
      if ($_POST['action'] === 'add_room') {
          $insertQuery = "INSERT INTO MeetingRoom (availability) 
                          VALUE
                          (true);";
          $insertResult = mysqli_query($conn, $insertQuery);

          if ($insertResult) {
              $message = "User added successfully!";
          } else {
              $message = "Error adding the User.";
          }
        } elseif ($_POST['action'] === 'delete_room') {
            $idToDelete = $_POST['idMeetingRoom'];
            $deleteQuery = "DELETE FROM project.MeetingRoom WHERE idMeetingRoom = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Meeting Room deleted successfully!" : "Error deleting the meeting room.";
        }
      }
      
      else {
        $idMeetingRoom = $_POST['idMeetingRoom'];
        $availability = isset($_POST['availability']) ? 1 : 0;
    
        $queryUpdate = "UPDATE project.MeetingRoom
                        SET availability=$availability
                        WHERE idMeetingRoom=$idMeetingRoom";
    
        $resultUpdate = mysqli_query($conn, $queryUpdate);
        $message = $resultUpdate ? "Meeting Room updated successfully!" : "Error updating meeting room.";
    }
    
    
    
}

$query = "SELECT idMeetingRoom,availability 
          FROM MeetingRoom ORDER BY idMeetingRoom ASC;";

$result = mysqli_query($conn, $query);


echo "
    <tr>
      <td>" . 'idMeetingRoom' . "</td>
      <td>" . 'is_available' . "</td>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idMeetingRoom = $rowData['idMeetingRoom'];
    $availability = $rowData['availability'];

    echo "
      <tr>
        <td>
          <form method='POST' name='admin_book' >
            <input type='text' name='idMeetingRoom'  value='$idMeetingRoom' readonly size='5'>
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
        <input type='submit' value='Add Room'>
    </form>
";

echo $message;

?>
<?php include "admin_footer.html"; ?>
</body>
</html>