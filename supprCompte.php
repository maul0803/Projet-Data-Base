<html>
<head>
    <meta charset="utf-8" >
    <title>Recette</title>
    <link href="recette.css" rel="stylesheet" >
</head>
<?php include "header.php"?>
<body>
<?php
if (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $query = "SELECT idUser FROM `Users` WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        // Handle the query error
        die("Erreur lors de la sélection de l'utilisateur : " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $idUser = $row["idUser"];

        $querySupr = "DELETE FROM Users WHERE `idUser`=$idUser";
        $resultSupr = mysqli_query($conn, $querySupr);

        if (!$resultSupr) {
            // Handle the query error
            die("Erreur lors de la suppression de l'utilisateur : " . mysqli_error($conn));
        }

        if (session_destroy()) {
            // Redirection vers la page de connexion
            header("Location: login.php");
        }
    } else {
        // Handle the case where no Users is found
        echo "Utilisateur non trouvé.";
    }
}
?>

</body>
</html>
