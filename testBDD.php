<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//initSession();

$tabErreurs = array();

$hote = "localhost";
$login = "moerin_gsb";
$mdp = "#5y?{7SSl+5.";

$idCnx = mysql_connect($hote, $login, $mdp) or die("ERREUR : " . mysql_error());


$bd = "moerin_database";
$query = "SET CHARACTER SET utf8";
// Modification du jeu de caractères de la connexion
mysql_query($query, $idCnx) or die("ERREUR " . mysql_error()); 
mysql_select_db($bd, $idCnx) or die("ERREUR " . mysql_error());

$query = "SELECT mdp from utilisateur";
echo $idCnx;
$var = mysql_query($query, $idCnx) or die("ERREUR " . mysql_error());
if (!$var) {
    echo "pouet";
} else {
    echo "prout";
}

while ($lgTruc = mysql_fetch_assoc($var)) {
    echo $lgTruc['mdp'];
}

mysql_close($idCnx);