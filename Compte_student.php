<!--version du 16/04/2023-->
<!--Modification du 02/04/2023 par Côme QUINTYN:
-ajout du mise en page minimale
-->
<!--Modifications du 16/04/2023 par Romain GUENNEAU:
  -complétion de la mise en page
-->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Compte</title>
    <link href="login.css" rel="stylesheet">
</head>
<?php include "header.php"; ?>
<body>
<?php
// Require('config.php');
// Initialiser la session
// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
?>

<div class="come-success">
    <h1>Welcome <?php echo $_SESSION['email']; ?>!</h1>
    <h1>Your statut is <?php echo $_SESSION['profile']; ?>!</h1>
    <p><font color="red">You are connected.</font></p>
    <a href="logout.php"><font color="red">Logout</font></a>
</div>
</body>


<?php include "student_footer.html"; ?>
</body>
</html>
