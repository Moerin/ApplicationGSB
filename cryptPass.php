<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Crypte les mots de passe à l'intérieur de la base de donnée
$repInclude = './include/';
require($repInclude . "_init.inc.php");

$req = "select id, mdp, hash from utilisateur";
$idJeuMotDePasse = mysql_query($req, $idConnexion);

// Variable de vérification d'encodage
$hashedDatabase = false;
$lgMdp = mysql_fetch_array($idJeuMotDePasse);
if ($lgMdp['hash'] == 1) {
       $hashedDatabase = true;
} else {
    $idJeuMotDePasse = mysql_query($req, $idConnexion);
    while ($lgMdp = mysql_fetch_array($idJeuMotDePasse)) {
   

        $mdp = $lgMdp['mdp'];   
        $req2 = "UPDATE utilisateur SET mdp ='" . crypt($mdp,'$2a$07$'.md5($mdp).'$') . "', hash=1 where id = '" . $lgMdp['id'] . "'";
        $reqSuccess = mysql_query($req2, $idConnexion);
        if ($reqSuccess) {
            echo utf8_decode('<br \>Requête valide');
            echo '<br \> Mot de passe en clair : ' . $lgMdp['mdp']; 
            echo utf8_decode('<br \> Mot de passe crypté : ') . crypt($mdp,'$2a$07$'.md5($mdp).'$'); 

        } else {
         $message  = utf8_decode('Requête invalide : ') . mysql_error() . "\n";
         $message .= utf8_decode('Requête complète : ') . $query;
         die($message);
         }
    }
}
 mysql_free_result($idJeuMotDePasse);

 // si la base de donnée est déja crypté on propose de la réinitialiser
 if ($hashedDatabase) { 
    echo utf8_decode('<p>Base de donnée déja encodée, pour voir les résultat encodées vous pouvez la réinitialiser en cliquant sur le lien suivant.</p><br />');
    echo utf8_decode('<a href="resetDatabase.php" title="reinitialisation">Reinitialiser base de donnée</a>');
 }
 
?>