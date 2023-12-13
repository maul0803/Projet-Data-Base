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
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($conn === false) {
    die("ERROR: Unable to connect. " . mysqli_connect_error());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            // Processing the add author form
            $newPubisherName = $_POST['newPubisherName'];

            // SQL query to add a new author
            $insertQuery = "INSERT INTO project.Publisher (Publisher_Name) VALUES ('$newPubisherName')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                $message = "Publisher added successfully!";
            } else {
                $message = "Error adding the publisher.";
            }
        } elseif ($_POST['action'] === 'delete') {
            $idToDelete = $_POST['idPublisher'];
            $deleteQuery = "DELETE FROM project.Publisher WHERE idPublisher = $idToDelete";
            $deleteResult = mysqli_query($conn, $deleteQuery);

            if ($deleteResult) {
                $message = "Publisher deleted successfully!";
            } else {
                $message = "Error deleting the publisher.";
            }
        }
    } else {
        $idPublisher = $_POST['idPublisher'];
        $Publisher_Name = $_POST['Publisher_Name'];

        $query = "UPDATE project.Publisher SET Publisher_Name = '$Publisher_Name' WHERE idPublisher = $idPublisher";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $message = "Modification successful!";
        } else {
            $message = "Error modifying the publisher.";
        }
    }
}

// Display the table
$query = "SELECT idPublisher, Publisher_Name FROM project.Publisher ORDER BY idPublisher ASC";
$result = mysqli_query($conn, $query);
echo "<table>";
echo "
    <tr>
      <th>".'idPublisher'."</th>
      <th>".'Publisher_Name'."</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idPublisher = $rowData['idPublisher'];
    $Publisher_Name = $rowData['Publisher_Name'];
    echo "
      <tr>
          <form method='POST' name='admin_publisher' >
            <td><input type='text' name='idPublisher'  value='$idPublisher' readonly size='5'></td>
            <td><input type='text' name='Publisher_Name' value='$Publisher_Name'></td>
            <td><input type='submit' value='Modify'></td>
            <td><button type='submit' name='action' value='delete'>Delete</button></td>
          </form>
      </tr>
    ";
}
echo "</table>";

// Form to add an publisher
echo "
    <form method='POST' name='add'>
        <input type='text' name='newPubisherName' placeholder='Publisher Name'>
        <input type='hidden' name='action' value='add'>
        <input type='submit' value='Add Publisher'>
    </form>
";
?>
</body>
<?php
echo $message; // Display the confirmation message
?>
</html>
