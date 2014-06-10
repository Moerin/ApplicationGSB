<?php
// Reinitialisation de la base de donnée

$repInclude = './include/';
require($repInclude . "_init.inc.php");

// Connexion à la BDD en PDO
$dsn = 'mysql:host=localhost;dbname=moerin_database';
$user = 'moerin_gsb';
$password = '#5y?{7SSl+5.';

//// Efface les tables de la base données
$mysqli = new mysqli("localhost", "moerin_gsb", "#5y?{7SSl+5.", "moerin_database");
$mysqli->query('SET foreign_key_checks = 0');

if ($result = $mysqli->query("SHOW TABLES"))
{
    while($row = $result->fetch_array(MYSQLI_NUM))
    {
        $mysqli->query('DROP TABLE IF EXISTS '.$row[0]);
    }
}

$mysqli->query('SET foreign_key_checks = 1');
$mysqli->close();

// Nom du fichier conteant le script de creation de la base de donnée
$filename = 'database_final_state.sql';

$templine = '';
// Lecture du fichier
$lines = file($filename);

foreach ($lines as $line) {
    // Evite si c'est un commentaire
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;

        // Concatene les requètes
            $templine .= $line;
        // Si il y a un point virgule c'est que c'est la fin de la requète
        if (substr(trim($line), -1, 1) == ';')
        {
            // Exécute la requète
            mysql_query($templine, $idConnexion) or print('Error d\'execution de requete \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
            
            $templine = '';
        }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>Reinitialisation base de donnee GSB</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="./styles/styles.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico" />
    <script type="text/javascript" src="../javascript/jquery-1.11.0.js"></script>
  </head>
    <body style="background-color: white;">
        <div id="reinitialisation">
            <?php
            // Message signalant que la base de donnee est reinitialisée
                echo 'Base de données reinitialisée';
            ?>
        </div>
        <div id="overlay">
            <img src="images/loading.gif" alt="Loading" />
        </div>
        <script type="text/javascript">
            $(document).ready(function (){
                // Fonction pour centrer un element
                jQuery.fn.center = function () {
                    this.css("position","absolute");
                    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
                    $(window).scrollTop()) + "px");
                    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
                    $(window).scrollLeft()) + "px");
                    return this;
                };

                // Centrage
                $('#overlay').center();
                
                // On change des elements de style pour le css
                $('#reinitialisation').css({
                    "display" : "none",
                    "font-size" : "5em",
                    "font-family" : "Trebuchet MS,Verdana,Geneva,Arial,Helvetica,sans-serif"
                });
                
                // Centrage
                $('#reinitialisation').center();
                
                // On gere l'affichage du loading et ensuite l'appel du callback pour faire apparaitre le message
                $('#overlay').fadeOut(3000,function(){
                    $('#reinitialisation').fadeIn();
                });
            });
        </script>
    </body>
</html>
  
