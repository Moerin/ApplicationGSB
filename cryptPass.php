<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Crypte les mots de passe à l'intérieur de la base de donnée
$repInclude = './include/';
require($repInclude . "_init.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>Cryptage base de données GSB</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="./styles/styles.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico" />
    <script type="text/javascript" src="../javascript/jquery-1.11.0.js"></script>
  </head>
    <body class="crypt" style="background-color: white;">
<?php
$req = "select id, mdp, hash from utilisateur";
$idJeuMotDePasse = mysql_query($req, $idConnexion);

// Variable de vérification d'encodage

$lgMdp = mysql_fetch_array($idJeuMotDePasse);
if ($lgMdp['hash']) {
       // si la base de données est déja cryptée on propose de la réinitialiser

   echo '<p>Base de donnée déja encodée, pour voir les résultats encodés, vous pouvez la réinitialiser en cliquant sur le lien suivant.<br />';
   echo '<a href="resetDatabase.php" title="reinitialisation">Reinitialiser base de données</a></p>';

} else {
    $idJeuMotDePasse = mysql_query($req, $idConnexion);
    while ($lgMdp = mysql_fetch_array($idJeuMotDePasse)) {

        $mdp = $lgMdp['mdp'];   
        $req2 = "UPDATE utilisateur SET mdp ='" . crypt($mdp,'$2a$07$'.md5($mdp).'$') . "', hash=1 where id = '" . $lgMdp['id'] . "'";
        $reqSuccess = mysql_query($req2, $idConnexion);
        if ($reqSuccess) {
            echo '<p>';
            echo '<b>Requête valide</b><br \>';
            echo 'Mot de passe en clair : <em>' . $lgMdp['mdp'] . '</em><br \>'; 
            echo 'Mot de passe crypté : ' . crypt($mdp,'$2a$07$'.md5($mdp).'$') . '<br \>'; 
            echo '</p>';

        } else {
         $message  = utf8_decode('Requête invalide : ') . mysql_error() . "\n";
         $message .= utf8_decode('Requête complète : ') . $query;
         die($message);
         }
    }
}
 mysql_free_result($idJeuMotDePasse);


?>
    </body>
</html>