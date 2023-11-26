<!--version du 02/04/2023-->
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


if (isset($_REQUEST['username'], $_REQUEST['email'], $_REQUEST['password'])){
  $username = stripslashes($_REQUEST['username']);
  $username = mysqli_real_escape_string($conn, $username);

  $email = stripslashes($_REQUEST['email']);
  $email = mysqli_real_escape_string($conn, $email);

  $password = stripslashes($_REQUEST['password']);
  $password = mysqli_real_escape_string($conn, $password);

  // Additional fields
  $first_name = stripslashes($_REQUEST['first_name']);
  $first_name = mysqli_real_escape_string($conn, $first_name);

  $last_name = stripslashes($_REQUEST['last_name']);
  $last_name = mysqli_real_escape_string($conn, $last_name);

  $postal_address = stripslashes($_REQUEST['postal_address']);
  $postal_address = mysqli_real_escape_string($conn, $postal_address);

  $phone_number = stripslashes($_REQUEST['phone_number']);
  $phone_number = mysqli_real_escape_string($conn, $phone_number);

  $profil = 'Student'; // Assuming a default value for the profile field

  // verification si l'adresse mail est deja utilise
  $query = "SELECT * FROM `users` WHERE email='$email'";
  $result = mysqli_query($conn,$query) or die(mysql_error());
  $rows = mysqli_num_rows($result);
  if($rows==1){// verification si l'on a exactement une ligne qui corrspond a la requete
    $message = "L'adresse mail est déjà utilisée.";
  }
  else{
      //requete SQL
      $query = "INSERT INTO `users` (profil, first_name, last_name, email, postal_address, phone_number, username, password, is_registered) 
      VALUES ('$profil', '$first_name', '$last_name', '$email', '$postal_address', '$phone_number', '$username', '$password', 'false')";
    // execution de la requete sur la base de donnees
    $res = mysqli_query($conn, $query);
    if($res){
    echo "<div class='come-sucess'>
        <h3>Vous vous êtes inscrit avec succès.</h3>
        <p class='come-sucess'>Cliquez ici pour vous <a href='login.php'>connecter</a></p>
    </div>";
    }
  }
}
?>
<main>
  <form class="come-boite" action="" method="post" name="register">
      <h1 class="come-boite-title">S'inscrire</h1>
      <input type="text" class="come-boite-input" name="username" placeholder="Nom d'utilisateur" required />
      <input type="text" class="come-boite-input" name="email" placeholder="Email" required />
      <input type="password" class="come-boite-input" name="password" placeholder="Mot de passe" required />
      <!-- Additional fields -->
      <input type="text" class="come-boite-input" name="first_name" placeholder="Prénom" required />
      <input type="text" class="come-boite-input" name="last_name" placeholder="Nom de famille" required />
      <input type="text" class="come-boite-input" name="postal_address" placeholder="Adresse postale" required />
      <input type="text" class="come-boite-input" name="phone_number" placeholder="Numéro de téléphone" required />
      <input type="submit" name="submit" value="S'inscrire" class="come-boite-button" />
      <p class="come-boite-register">Dejà inscrit? <a href="login.php">Connectez-vous ici</a></p>
      <?php if (! empty($message)) { ?>
      <p class="come-errorMessage"><?php echo $message; ?></p>
      <?php } ?>
</main>

<?php include "footer.html" ?>
</form>
</body>
</html>