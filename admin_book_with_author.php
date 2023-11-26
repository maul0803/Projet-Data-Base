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
        if ($_POST['action'] === 'add_book') {
            $newTitle = $_POST['newTitle'];
            $newLanguage = $_POST['newLanguage'];
            $newPages = $_POST['newPages'];
            $newYear = $_POST['newYear'];
            $newSubject = $_POST['newSubject'];
            $newRack = $_POST['newRack'];
            $Author_Name = $_POST['Author_Name'];
            $Publisher_Name = $_POST['Publisher_Name'];
            
            
            $insertQuery1 = "INSERT INTO project.Book (Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number) 
                            VALUES ('$newTitle', '$newLanguage', $newPages, '$newYear', '$newSubject', $newRack)";
            $insertResult1 = mysqli_query($conn, $insertQuery1);
            $idBook = mysqli_insert_id($conn);
          
            
            $selectQueryAuthor = "SELECT project.Author.idAuthor FROM Author WHERE project.Author.Author_Name='$Author_Name'";
            $selectResultAuthor = mysqli_query($conn, $selectQueryAuthor);
            $idAuthor = mysqli_fetch_assoc($selectResultAuthor)["idAuthor"];

            $insertQueryAuthor = "INSERT INTO project.Write_ (idBook, idAuthor) VALUES ($idBook, $idAuthor)";
            $insertResultAuthor = mysqli_query($conn, $insertQueryAuthor);
            
            $selectQueryPublisher = "SELECT project.Publisher.idPublisher FROM Publisher WHERE project.Publisher.Publisher_Name='$Publisher_Name'";
            $selectResultPublisher = mysqli_query($conn, $selectQueryPublisher);
            $idPublisher = mysqli_fetch_assoc($selectResultPublisher)["idPublisher"];

            $insertQueryPublisher = "INSERT INTO project.Publish (idBook, idPublisher) VALUES ($idBook, $idPublisher)";
            $insertResultPublisher = mysqli_query($conn, $insertQueryPublisher);

            if ($insertResult1 and $selectResultPublisher and $insertResultAuthor){
              $message = "Book added successfully!";
            }
            else{
              $message = "Error adding the book.";
            }
        } elseif ($_POST['action'] === 'delete_book') {
            $idToDelete = $_POST['idBook'];
            $deleteQuery = "DELETE FROM project.Book WHERE idBook = $idToDelete";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Book deleted successfully!" : "Error deleting the book.";
        }
    } else {
      $idBook = $_POST['idBook'];
      $Title = $_POST['Title'];
      $Language_ = $_POST['Language'];
      $Number_Of_Pages = $_POST['Number_Of_Pages'];
      $Year_Of_Production = $_POST['Year_Of_Production'];
      $Subject = $_POST['Subject'];
      $rack_number = $_POST['Rack_number'];
      $Author_Name = $_POST['Author_Name'];
      
      $query1 = "UPDATE project.Book 
                 SET Title = '$Title',
                 Language_ = '$Language_', 
                 Number_Of_Pages = $Number_Of_Pages,
                 Year_Of_Production = '$Year_Of_Production',
                 Subject = '$Subject',
                 rack_number = $rack_number
                 WHERE idBook = $idBook";
      
      $result1 = mysqli_query($conn, $query1);
      
      $query2 = "SELECT project.Author.idAuthor FROM Author WHERE project.Author.Author_Name='$Author_Name'";
      $result2 = mysqli_query($conn, $query2);
      $idNewAuthor = mysqli_fetch_assoc($result2)["idAuthor"];
      
      $query3 = "UPDATE Write_ SET idAuthor=$idNewAuthor WHERE idBook=$idBook";
      $result3 = mysqli_query($conn, $query3);
      
      $message = $result1 ? "Modification successful!" : "Error modifying the book.";
    }
}

$query = "SELECT project.Book.idBook,Title, Language_, Number_Of_Pages, Year_Of_Production, Subject, rack_number, Author_Name
          FROM project.Book JOIN project.Write_
          ON project.Book.idBook=project.Write_.idBook
          JOIN Author
          ON project.Author.idAuthor=project.Write_.idAuthor;";
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

echo "
    <tr>
      <td>" . 'idBook' . "</td>
      <td>" . 'Title' . "</td>
      <td>" . 'idLanguageBook' . "</td>
      <td>" . 'Number_Of_Pages' . "</td>
      <td>" . 'Year_Of_Production' . "</td>
      <td>" . 'Subject' . "</td>
      <td>" . 'Rack_number' . "</td>
      <td>" . 'Author' . "</td>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idBook = $rowData['idBook'];
    $Title = $rowData['Title'];
    $Language_ = $rowData['Language_'];
    $Number_Of_Pages = $rowData['Number_Of_Pages'];
    $Year_Of_Production = $rowData['Year_Of_Production'];
    $Subject = $rowData['Subject'];
    $rack_number = $rowData['rack_number'];
    $Author_Name = $rowData['Author_Name'];

    echo "
        <tr>
          <td>
            <form method='POST' name='admin_book' >
              <input type='text' name='idBook'  value='$idBook' readonly>
              <input type='text' name='Title' value='$Title'>
              <input type='text' name='Language' value='$Language_'>
              <input type='number' name='Number_Of_Pages' value='$Number_Of_Pages'>
              <input type='date' name='Year_Of_Production' value='$Year_Of_Production'>
              <input type='text' name='Subject' value='$Subject'>
              <input type='number' name='Rack_number' value='$rack_number'>
              <select name='Author_Name'>
                <option value='$Author_Name'>$Author_Name</option>";

    foreach ($authors as $author) {
        $authorId = $author['idAuthor'];
        $authorName = $author['Author_Name'];
        if ($authorName != $Author_Name) {
            echo "<option value='$authorName'>$authorName</option>";
        }
    }

    echo "
              </select>
              <input type='submit' value='Modify'>
              <button type='submit' name='action' value='delete_book'>Delete</button>
            </form>
          </td>
        </tr>
      ";
}

echo "
    <form method='POST' name='add_book'>
        <input type='text' name='newTitle' placeholder='Title'>
        <input type='text' name='newLanguage' placeholder='Language'>
        <input type='number' name='newPages' placeholder='Number Of Pages'>
        <input type='date' name='newYear' placeholder='Year Of Production'>
        <input type='text' name='newSubject' placeholder='Subject'>
        <input type='number' name='newRack' placeholder='Rack Number'>
        <select name='Author_Name'>";

        foreach ($authors as $author) {
        $authorId = $author['idAuthor'];
        $authorName = $author['Author_Name'];
        echo "<option value='$authorName'>$authorName</option>";
        }

        echo "
      </select>
      <select name='Publisher_Name'>";

      foreach ($publishers as $publisher) {
      $publisherId = $publisher['idPublisher'];
      $publisherName = $publisher['Publisher_Name'];
      echo "<option value='$publisherName'>$publisherName</option>";
      }

      echo "
    </select>
        <input type='hidden' name='action' value='add_book'>
        <input type='submit' value='Add Book'>
    </form>
";

echo $message;

?>
<?php include "admin_footer.html"; ?>
</body>
</html>
