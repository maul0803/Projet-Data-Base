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
        if ($_POST['action'] === 'add_card') {
            $idBookInLibrary = $_POST['idBookInLibrary'];
            $idCard = $_POST['idCard'];
            $DateBorrowStart = $_POST['DateBorrowStart'];

            
            
            $insertQuery = "INSERT INTO Borrow (DateBorrowStart, idCard, idBookInLibrary) 
                            VALUE
                            ('$DateBorrowStart', '$idCard', '$idBookInLibrary')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult){
              $message = "Borrow added successfully!";
            }
            else{
              $message = "Error adding the borrow.";
            }
        } elseif ($_POST['action'] === 'delete_borrow') {
            $idToDelete = $_POST['idBorrow'];
            $deleteQuery = "DELETE FROM project.Borrow WHERE idBorrow = '$idToDelete'";
            $deleteResult = mysqli_query($conn, $deleteQuery);
            $message = $deleteResult ? "Borrow deleted successfully!" : "Error deleting the Borrow.";
        }
        elseif ($_POST['action'] === 'return_book') {
          $idBorrow = $_POST['idBorrow'];
          $idBookInLibrary = $_POST['idBookInLibrary'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
      
          // Utiliser des guillemets autour des valeurs dans la requête SQL
          $queryUpdate = "UPDATE project.Borrow
                          SET idBookInLibrary=$idBookInLibrary,
                              idCard='$idCard',
                              DateBorrowStart='$DateBorrowStart',
                              DateBorrowEnd=NOW()
                          WHERE idBorrow=$idBorrow";
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
      
          if ($resultUpdate) {
              $message = "Book returned successfully!";
          } else {
              // Récupérer le message d'erreur de la requête SQL
              $error_message = mysqli_error($conn);
              $message = "Error returning the book. Error: $error_message";
          }
      }
      
        } else {
          $idBorrow = $_POST['idBorrow'];
          $idBookInLibrary = $_POST['idBookInLibrary'];
          $idCard = $_POST['idCard'];
          $DateBorrowStart = $_POST['DateBorrowStart'];
          $DateBorrowEnd = $_POST['DateBorrowEnd'];
          if (empty($DateBorrowEnd)) {
            $queryUpdate = "UPDATE project.Borrow
            SET idBookInLibrary=$idBookInLibrary,
                idCard='$idCard',
                DateBorrowStart='$DateBorrowStart'
            WHERE idBorrow=$idBorrow";
          } else {
            $queryUpdate = "UPDATE project.Borrow
            SET idBookInLibrary=$idBookInLibrary,
                idCard='$idCard',
                DateBorrowStart='$DateBorrowStart',
                DateBorrowEnd='$DateBorrowEnd'
            WHERE idBorrow=$idBorrow";
          }
      
          $resultUpdate = mysqli_query($conn, $queryUpdate);
          $message = $resultUpdate ? "Modification successful!" : "Error modifying the borrow.";
      }
    
    
}

$query = "SELECT idBorrow,Title,idCard,Borrow.idBookInLibrary,DateBorrowStart,DateBorrowEnd
          FROM project.Borrow
          JOIN project.BookInLibrary
          ON Borrow.idBookInLibrary=BookInLibrary.idBookInLibrary
          JOIN Book
          ON BookInLibrary.idBook=Book.idBook
          ORDER BY idBorrow ASC;";

$result = mysqli_query($conn, $query);

$titleQuery = "SELECT idBookInLibrary,Title
              FROM BookInLibrary
              JOIN Book
              ON BookInLibrary.idBook=Book.idBook
              WHERE availability=True;";
$titleResult = mysqli_query($conn, $titleQuery);
$titles = [];
while ($titleData = mysqli_fetch_assoc($titleResult)) {
    $titles[] = $titleData;
}

$cardQuery = "SELECT idCard
              FROM Card
              WHERE RessourceType='Book'
              AND is_active=True;";
$cardResult = mysqli_query($conn, $cardQuery);
$cards = [];
while ($cardData = mysqli_fetch_assoc($cardResult)) {
    $cards[] = $cardData;
}


echo "
    <tr>
      <td>" . 'idBorrow' . "</td>
      <td>" . 'idBookInLibrary' . "</td>
      <td>" . 'Title' . "</td>
      <td>" . 'idCard' . "</td>
      <td>" . 'DateBorrowStart' . "</td>
      <td>" . 'DateBorrowEnd' . "</td>
    </tr>
";

while ($rowData = mysqli_fetch_assoc($result)) {
    $idBorrow = $rowData['idBorrow'];
    $Title = $rowData['Title'];
    $idCard = $rowData['idCard'];
    $DateBorrowStart = $rowData['DateBorrowStart'];
    $DateBorrowEnd = $rowData['DateBorrowEnd'];
    $idBookInLibrary = $rowData['idBookInLibrary'];

    echo "
      <tr>
        <td>
          <form method='POST' name='admin_book' >
            <input type='text' name='idBorrow'  value='$idBorrow' readonly size='5'>
            <input type='text' name='idBookInLibrary'  value='$idBookInLibrary' readonly size='5'>
            <input type='text' name='Title' value='$Title' readonly>
            <input type='text' name='idCard' value='$idCard' readonly size='5'>
            <input type='date' name='DateBorrowStart' value='$DateBorrowStart'>
            <input type='date' name='DateBorrowEnd' value='$DateBorrowEnd'>
            <input type='submit' value='Modify'>
            <button type='submit' name='action' value='delete_borrow'>Delete</button>
            <button type='submit' name='action' value='return_book'>Return book</button>
          </form>
        </td>
      </tr>
";

}

echo "
    <form method='POST' name='add_card'>

        <select name='idBookInLibrary'>;

        ";
        foreach ($titles as $title) {
          $idBookInLibrary = $title['idBookInLibrary'];
          $bookTitle = $title['Title'];
          echo "<option value='$idBookInLibrary'>$bookTitle</option>";
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

        <input type='hidden' name='action' value='add_card'>
        <input type='submit' value='Borrow Book'>
    </form>
";

echo $message;

?>
<?php include "admin_footer.html"; ?>
</body>
</html>
