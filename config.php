<?php
// Informations d'identification
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'student');
define('DB_PASSWORD', '123');
define('DB_NAME', 'project');
 
// Connexion � la base de donn�es MySQL 
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// V�rifier la connexion
if($conn === false){
    die("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
}
?>