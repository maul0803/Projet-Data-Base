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
            $newAuthorName = $_POST['newAuthorName'];

            // SQL query to add a new author
            $insertQuery = "INSERT INTO project.Author (Author_Name) VALUES ('$newAuthorName')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                $message = "Author added successfully!";
            } else {
                $message = "Error adding the author.";
            }
        } elseif ($_POST['action'] === 'delete') {
            $idToDelete = $_POST['idAuthor'];
            $deleteQuery = "DELETE FROM project.Author WHERE idAuthor = $idToDelete";
            $deleteResult = mysqli_query($conn, $deleteQuery);

            if ($deleteResult) {
                $message = "Author deleted successfully!";
            } else {
                $message = "Error deleting the author.";
            }
        }
    } else {
        $idAuthor = $_POST['idAuthor'];
        $Author_Name = $_POST['Author_Name'];

        $query = "UPDATE project.Author SET Author_Name = '$Author_Name' WHERE idAuthor = $idAuthor";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $message = "Modification successful!";
        } else {
            $message = "Error modifying the author.";
        }
    }
}

// Display the table
$query = "SELECT idAuthor, Author_Name FROM project.Author ORDER BY idAuthor ASC;";
$result = mysqli_query($conn, $query);
echo "<table style='margin: auto; margin-top: -300px;'>";
echo "
    <tr>
      <th>".'idAuthor'."</th>
      <th>".'Author_Name'."</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idAuthor = $rowData['idAuthor'];
    $Author_Name = $rowData['Author_Name'];
    echo "
      <tr>
          <form method='POST' name='admin_author' >
            <td><input type='text' name='idAuthor'  value='$idAuthor' readonly size='5'></td>
            <td><input type='text' name='Author_Name' value='$Author_Name'></td>
            <td><input type='submit' value='Modify'></td>
            <td><button type='submit' name='action' value='delete'>Delete</button></td>
          </form>
      </tr>
    ";
}
echo "</table><br>";

// Form to add an author
echo "
    <form method='POST' name='add' style='margin-left: 20px'>
        <input type='text' name='newAuthorName' placeholder='Author Name'>
        <input type='hidden' name='action' value='add'>
        <input type='submit' value='Add Author'>
    </form><br>
";
?>
</body>
<?php
echo "<div style='margin-left: 20px'>";
echo $message;
echo "</div>";
?>
<div style='bottom: 0; width: 100%;'>
<?php include "footer.html"; ?>
</div>
</html>
