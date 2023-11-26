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
                    <img src="images/logo.jpg" alt="Logo Site" height='80' width='80'>
                </a>
                <div class="leftHeaderText">
                    <a href="login.php">Accueil</a>
                    <?php
                    if(isset($_SESSION["email"])){
                        echo'
                        <a href="mesRecettes.php">Mes recettes</a>';
                    }
                    ?>
                    <a href="apropos.php">À propos</a>
                </div>
            </div>
            <div class="rightHeader">
                    <?php
                     if(!isset($_SESSION["email"])){
                        // Si l'utilisateur n'est pas connecté, affiche se connecter
                        echo '<a href="login.php">Se connecter</a>';
                    }
                    if(isset($_SESSION["email"])){
                        // Si l'utilisateur est connecté, affiche le lien "Mon profil"
                        echo '<a href="login.php">Mon profil</a>';
                    }
                    ?>
                    <a href="rechercheTAG.php">Recherche par tags</a>
                <a></a>
            </div>
        </div>
    </nav>
</header>
