<!--version du 03/04/2023-->
<!--Modification du 02/04/2023 par Côme QUINTYN:
-changement de index.html en index.php
-elevement de login.php
-changement du lien de register.php
-->
<?php session_start();
require('config.php');
?>

<head>
    <meta charset="utf-8" >
    <link href="style.css" rel="stylesheet" >
</head>
<header>
    <nav>
        <div class="headerNav">
            <div class="leftHeaderLink">
                <a href="login.php">
                    <img src="images/logolibrary.jpg" alt="Logo Site" height='100' width='100'>
                </a>
                <div class="leftHeaderText">
                    <a href="login.php">Welcome</a>
                </div>
            </div>
            <div class="rightHeader">
                    <?php
                     if(!isset($_SESSION["email"])){
                        // Si l'utilisateur n'est pas connecté, affiche se connecter
                        echo '<a href="login.php">Login</a>';
                    }
                    if(isset($_SESSION["email"])){
                        // Si l'utilisateur est connecté, affiche le lien "Mon profil"
                        echo '<a href="login.php">My profil</a>';
                    }
                    ?>
                <a></a>
            </div>
        </div>
    </nav>
</header>
