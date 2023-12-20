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
            $idBook = $_POST['idBook'];
            $price = $_POST['price'];
            $date_of_purchase = $_POST['date_of_purchase'];
            $availability = True;
            
            
            $insertQuery = "INSERT INTO BookInLibrary (price, date_of_purchase, availability, idBook) 
                              VALUE ($price, $date_of_purchase, $availability, $idBook);";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult){
              $message = "Book added successfully!";
            }
            else{
              $message = "Error adding the book.";
            }
        } elseif ($_POST['action'] === 'delete_book') {
            $idToDelete = $_POST['idBookInLibrary'];
            $deleteQuery = "DELETE FROM project.BookInLibrary WHERE idBookInLibrary = $idToDelete";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Book deleted successfully!" : "Error deleting the book.";
        }
        } else {
          $idBookInLibrary = $_POST['idBookInLibrary'];
          $Title = $_POST['Title'];
          $price = $_POST['price'];
          $date_of_purchase = $_POST['date_of_purchase'];
          $availability = isset($_POST['availability']) ? 1 : 0;  // Convertir la valeur en 1 ou 0
      
          // Utiliser des guillemets autour des valeurs dans la requête SQL
          $queryUpdate = "UPDATE project.BookInLibrary
                          SET price=$price,
                              date_of_purchase='$date_of_purchase',
                              availability='$availability'
                          WHERE idBookInLibrary=$idBookInLibrary";
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Modification successful!" : "Error modifying the book.";
      }
    
}

$query = "SELECT idBookInLibrary,Title,price,date_of_purchase,availability
          FROM project.BookInLibrary
          JOIN project.Book
          ON project.BookInLibrary.idBook=project.Book.idBook ORDER BY idBookInLibrary ASC;";
$result = mysqli_query($conn, $query);

$titleQuery = "SELECT idBook,Title FROM project.Book";
$titleResult = mysqli_query($conn, $titleQuery);
$titles = [];
while ($titleData = mysqli_fetch_assoc($titleResult)) {
    $titles[] = $titleData;
}

echo "<table style='margin: auto; margin-top: -300px;'>";
echo "
    <tr>
      <th>" . 'idBookInLibrary' . "</th>
      <th>" . 'Title' . "</th>
      <th>" . 'price' . "</th>
      <th>" . 'date_of_purchase' . "</th>
      <th>" . 'availability' . "</th>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idBookInLibrary = $rowData['idBookInLibrary'];
    $Title = $rowData['Title'];
    $price = $rowData['price'];
    $date_of_purchase = $rowData['date_of_purchase'];
    $availability = $rowData['availability'];

    echo "
      <tr>
          <form method='POST' name='admin_book' >
            <td><input type='text' name='idBookInLibrary'  value='$idBookInLibrary' readonly size='5'></td>
            <td><input type='text' name='Title' value='$Title' readonly></td>
            <td><input type='number' name='price' value='$price'></td>
            <td><input type='date' name='date_of_purchase' value='$date_of_purchase'></td>
            <td><input type='checkbox' name='availability' value='$availability' " . ($availability ? 'checked' : '') . "></td>
            <td><input type='submit' value='Modify'></td>
            <td><button type='submit' name='action' value='delete_book'>Delete</button></td>
          </form>
      </tr>
    ";
}
echo "</table><br>";

echo "
    <form method='POST' name='add_book' style='margin-left: 20px'>
      
        <select name='idBook'>";

        foreach ($titles as $title) {
          $bookId = $title['idBook'];
          $bookTitle = $title['Title'];
          echo "<option value='$bookId'>$bookTitle</option>";
      }
      
        echo "
        </select>
        <input type='number' name='price' placeholder='Price'>
        <input type='date' name='date_of_purchase' placeholder='Date of purchase'>


        <input type='hidden' name='action' value='add_book'>
        <input type='submit' value='Add Book'>
    </form><br>
";

echo "<div style='margin-left: 20px'>";
echo $message;
echo "</div>";

?>
<div style='bottom: 0; width: 100%;'>
<?php include "footer.html"; ?>
</div>
</body>
</html>
