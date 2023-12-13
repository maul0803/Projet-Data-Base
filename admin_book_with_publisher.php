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
        if ($_POST['action'] === 'add_book') {
            $newTitle = $_POST['newTitle'];
            $newLanguage = $_POST['newLanguage'];
            $newPages = $_POST['newPages'];
            $newYear = $_POST['newYear'];
            $newSubject = $_POST['newSubject'];
            $newRack = $_POST['newRack'];


            $idAuthor = $_POST['idAuthor'];
            $idPublisher = $_POST['idPublisher'];

            
            $insertQuery1 = "INSERT INTO project.Book (Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number) 
                            VALUES ('$newTitle', '$newLanguage', $newPages, '$newYear', '$newSubject', $newRack)";
            $insertResult1 = mysqli_query($conn, $insertQuery1);
            $idBook = mysqli_insert_id($conn);
          
            $insertQueryAuthor = "INSERT INTO project.Write_ (idBook, idAuthor) VALUES ($idBook, $idAuthor)";
            $insertResultAuthor = mysqli_query($conn, $insertQueryAuthor);

            $insertQueryPublisher = "INSERT INTO project.Publish (idBook, idPublisher) VALUES ($idBook, $idPublisher)";
            $insertResultPublisher = mysqli_query($conn, $insertQueryPublisher);

            if ($insertResult1 and $insertResultAuthor and $insertResultPublisher){
              $message = "Publisher added successfully!";
            }
            else{
              $message = "Error adding the publisher.";
            }
        } elseif ($_POST['action'] === 'delete_book') {
            $idToDelete = $_POST['idBook'];
            $deleteQuery = "DELETE FROM project.Book WHERE idBook = $idToDelete";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Publisher deleted successfully!" : "Error deleting the publisher.";
        }
    } else {
      $idBook = $_POST['idBook'];
      $Title = $_POST['Title'];
      $Language_ = $_POST['Language'];
      $Number_Of_Pages = $_POST['Number_Of_Pages'];
      $Year_Of_Production = $_POST['Year_Of_Production'];
      $Subject = $_POST['Subject'];
      $rack_number = $_POST['Rack_number'];
      $idPublisher = $_POST['idPublisher'];
      
      $query1 = "UPDATE project.Book 
                 SET Title = '$Title',
                 Language_ = '$Language_', 
                 Number_Of_Pages = $Number_Of_Pages,
                 Year_Of_Production = '$Year_Of_Production',
                 Subject = '$Subject',
                 rack_number = $rack_number
                 WHERE idBook = $idBook";
      
      $result1 = mysqli_query($conn, $query1);
      
      $query3 = "UPDATE Publish SET idPublisher=$idPublisher WHERE idBook=$idBook";
      $result3 = mysqli_query($conn, $query3);
      
      $message = $result3 ? "Modification successful!" : "Error modifying the publisher.";
    }
}

$query = "SELECT project.Book.idBook,Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number, project.Publisher.Publisher_Name,Publisher.idPublisher,idAuthor
          FROM project.Book JOIN Publish
          ON project.Book.idBook = project.Publish.idBook
          JOIN Publisher
          ON project.Publisher.idPublisher = project.Publish.idPublisher
          JOIN Write_
          ON project.Book.idBook = project.Write_.idBook
          ORDER BY idBook ASC;";
$result = mysqli_query($conn, $query);

$authorQuery = "SELECT idAuthor, Author_Name FROM project.Author";
$authorResult = mysqli_query($conn, $authorQuery);
$authors = [];
while ($authorData = mysqli_fetch_assoc($authorResult)) {
    $authors[] = $authorData;
}

$publisherQuery = "SELECT idPublisher, Publisher_Name FROM project.Publisher";
$publisherResult = mysqli_query($conn, $publisherQuery);
$publishers = [];
while ($publisherData = mysqli_fetch_assoc($publisherResult)) {
    $publishers[] = $publisherData;
}
echo "<table>";
echo "
    <tr>
      <th>" . 'idBook' . "</th>
      <th>" . 'Title' . "</th>
      <th>" . 'idLanguageBook' . "</th>
      <th>" . 'Number_Of_Pages' . "</th>
      <th>" . 'Year_Of_Production' . "</th>
      <th>" . 'Subject' . "</th>
      <th>" . 'Rack_number' . "</th>
      <th>" . 'Publisher' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idAuthor = $rowData['idAuthor'];
    $idBook = $rowData['idBook'];
    $Title = $rowData['Title'];
    $Language_ = $rowData['Language_'];
    $Number_Of_Pages = $rowData['Number_Of_Pages'];
    $Year_Of_Production = $rowData['Year_Of_Production'];
    $Subject = $rowData['Subject'];
    $rack_number = $rowData['rack_number'];
    $Publisher_Name = $rowData['Publisher_Name'];
    $idPublisher = $rowData['idPublisher'];


    echo "
        <tr>
            <form method='POST' name='admin_book' >
            <td><input type='text' name='idBook'  value='$idBook' readonly size='5'></td>
            <td><input type='text' name='Title' value='$Title'></td>
            <td><input type='text' name='Language' value='$Language_'></td>
            <td><input type='number' name='Number_Of_Pages' value='$Number_Of_Pages'></td>
            <td><input type='date' name='Year_Of_Production' value='$Year_Of_Production'></td>
            <td><input type='text' name='Subject' value='$Subject'></td>
            <td><input type='number' name='Rack_number' value='$rack_number'></td>
            <td><select name='idPublisher'>
                <option value='$idPublisher'>$Publisher_Name</option>";
                foreach ($publishers as $publisher) {
                    $publisherId = $publisher['idPublisher'];
                    $publisherName = $publisher['Publisher_Name'];
                    if ($publisherId != $idPublisher) {
                        echo "<option value='$publisherId'>$publisherName</option>";
                    }
                }

              echo "
              </select></td>
              <td><input type='submit' value='Modify'></td>
              <td><button type='submit' name='action' value='delete_book'>Delete</button></td>
            </form>
        </tr>
      ";
}
echo "</table>";

echo "
    <form method='POST' name='add_book'>
        <input type='text' name='newTitle' placeholder='Title'>
        <input type='text' name='newLanguage' placeholder='Language'>
        <input type='number' name='newPages' placeholder='Number Of Pages'>
        <input type='date' name='newYear' placeholder='Year Of Production'>
        <input type='text' name='newSubject' placeholder='Subject'>
        <input type='number' name='newRack' placeholder='Rack Number'>
        <select name='idAuthor'>";

        foreach ($authors as $author) {
        $authorId = $author['idAuthor'];
        $authorName = $author['Author_Name'];
        echo "<option value='$authorId'>$authorName</option>";
        }

        echo "
      </select>
      <select name='idPublisher'>";

      foreach ($publishers as $publisher) {
      $publisherId = $publisher['idPublisher'];
      $publisherName = $publisher['Publisher_Name'];
      echo "<option value='$publisherId'>$publisherName</option>";
      }

      echo "
    </select>
        <input type='hidden' name='action' value='add_book'>
        <input type='submit' value='Add Publisher'>
    </form>
";

echo $message;

?>
</body>
</html>
