<!--version du 05/04/2023-->
<!-- ajout de code permettant de rediriger l'utilisateur sur sa page de profil si il est deja connecte-->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" >
    <title>Accueil</title>
    <link href="login.css" rel="stylesheet" >
</head>
<?php include "header.php"?>
<body>
<?php

// Permet de rediriger l'utilisateur sur sa page de profil s'il est déjà connecté
if(isset($_SESSION["email"])){
  $profile=$_SESSION['profile'];
  switch ($profile) {
    default:
    header("Location: Compte_student.php");
    exit();
    // Ajoutez d'autres cas selon les profils nécessaires
    case 'Administrator':
      header("Location: Compte_admin.php");
      exit();
    case 'Library Agent':
      header("Location: Compte_library.php");
      exit();
  }
  header("Location: monCompte.php");
  exit(); 
}

if (isset($_POST['email'])){
  $email = stripslashes($_REQUEST['email']);
  $email = mysqli_real_escape_string($conn, $email);
  $password = stripslashes($_REQUEST['password']);
  $password = mysqli_real_escape_string($conn, $password);

  //Get the user
  $query = "SELECT * FROM `users` WHERE email='$email' and password='$password'";
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $user = mysqli_fetch_assoc($result);
  if ($user) {
    $profile = $user['profil'];
  
    // Utiliser le nom d'utilisateur et le mot de passe appropriés en fonction du profil
    switch ($profile) {
      default:
          $db_username = 'student';
          $db_password = '123';
          break;
      // Ajoutez d'autres cas selon les profils nécessaires
      case 'Administrator':
            $db_username = 'root';
            $db_password = '';
            break;
      case 'Library Agent':
          $db_username = 'library_agent';
          $db_password = '123';
          break;
    }

    // Connexion à la base de données avec les informations spécifiques au profil
    $conn_profile = mysqli_connect('localhost', $db_username, $db_password, 'project');
    // Stocker les informations de la session et rediriger
    $_SESSION['DB_USERNAME'] = $db_username;
    $_SESSION['DB_PASSWORD'] = $db_password;
    $_SESSION['DB_SERVER'] = 'localhost';
    $_SESSION['DB_NAME'] = 'project';
    $_SESSION['email'] = $email;
    $_SESSION['profile'] = $profile;
    switch ($profile) {
      case 'Administrator':
        header("Location: Compte_admin.php");
        exit();
      case 'Library Agent':
        header("Location: Compte_library.php");
        exit();
      default:
        header("Location: Compte_student.php");
        exit();
    }

    if (!$conn_profile) {
        die("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
    }
  }
  else{
    $message = "Le nom d'utilisateur ou le mot de passe ou l'adresse mail est incorrect.";
  }
}

?>
<main>
  <form class="come-boite" method="post" name="login">
    <h1 class="come-boite-title">Se connecter</h1>
      <input type="text" class="come-boite-input" name="email" placeholder="Email" required />
      <input type="password" class="come-boite-input" name="password" placeholder="Mot de passe">
      <input type="submit" value="Connexion " name="submit" class="come-boite-button">
    <p class="come-boite-register">Vous êtes nouveau ici?   <a href="register.php">S'inscrire</a></p>
    <?php if (! empty($message)) { ?>
        <p class="come-errorMessage"><?php echo $message; ?></p>
    <?php } ?>
</main>

<?php include "footer.html" ?>
</body>
</html>
