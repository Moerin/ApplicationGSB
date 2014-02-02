<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Crypte les mot de passe à l'intérieur de la base de donnée

$repInclude = './include/';
require($repInclude . "_init.inc.php");

$req = "select id, mdp from utilisateur";
$idJeuMotDePasse = mysql_query($req, $idConnexion);

while ($lgMdp = mysql_fetch_array($idJeuMotDePasse)) {
   $truc = $lgMdp['mdp'];
   $req2 = "UPDATE utilisateur SET mdp ='" . crypt($truc) . "' where id = '" . $lgMdp['id'] . "'";
   mysql_query($req2, $idConnexion);
}
 mysql_free_result($idJeuMotDePasse);

?>