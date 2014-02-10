<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Reinitialisation de la base de donnée

$repInclude = './include/';
require($repInclude . "_init.inc.php");

// Efface les tables de la base données
$mysqli = new mysqli("localhost", "userGsb", "secret", "gsb_frais");
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
$filename = 'test.sql';

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

echo utf8_decode('Base de donnée reinitialisée');

?>