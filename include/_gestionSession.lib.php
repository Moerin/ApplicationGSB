<?php
/** 
 * Regroupe les fonctions de gestion d'une session utilisateur.
 * @package default
 * @todo  RAS
 */

/** 
 * Démarre ou poursuit une session.                     
 *
 * @return void
 */
function initSession() {
    session_start();
}

/** 
 * Fournit l'id de l'utilisateur connecté.                     
 *
 * Retourne l'id de l'utilisateur connecté, une chaîne vide si pas de utilisateur connecté.
 * @return string id de l'utilisateur connecté
 */
function obtenirIdUserConnecte() {
    $ident="";
    if ( isset($_SESSION["loginUser"]) ) {
        $ident = (isset($_SESSION["idUser"])) ? $_SESSION["idUser"] : '';   
    }
    return $ident ;
}

/**
 * Conserve en variables session les informations de l'utilisateur connecté
 * 
 * Conserve en variables session l'id $id et le login $login de l'utilisateur connecté
 * @param string id de l'utilisateur
 * @param string login de l'utilisateur
 * @return void    
 */
function affecterInfosConnecte($id, $login, $fonction) {
    $_SESSION["idUser"] = $id;
    $_SESSION["loginUser"] = $login;
    $_SESSION["fonctionUser"] = $fonction;
}

/** 
 * Déconnecte le visiteur qui s'est identifié sur le site.                     
 *
 * @return void
 */
function deconnecterUtilisateur() { 
    unset($_SESSION["idUser"]);
    unset($_SESSION["loginUser"]);
    unset($_SESSION["fonctionUser"]);
}

/** 
 * Vérifie si un visiteur s'est connecté sur le site.                     
 *
 * Retourne true si un visiteur s'est identifié sur le site, false sinon. 
 * @return boolean échec ou succès
 */
function estUtilisateurConnecte() { 
    // verifie si la variable $_SESSION["loginUser"] n'est pas nulle
    return (isset($_SESSION["loginUser"]));
}
?>