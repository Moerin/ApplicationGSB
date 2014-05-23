<?php
/** 
 * Initialise les ressources n�cessaires au fonctionnement de l'application
 * @package default
 * @todo  RAS
 */
  require("_bdGestionDonnees.lib.php");
  require("_gestionSession.lib.php");
  require("_utilitairesEtGestionErreurs.lib.php");
  // d�marrage ou reprise de la session
  initSession();
  // initialement, aucune erreur ...
  $tabErreurs = array();
    
  // Demande-t-on une d�connexion ?
  $demandeDeconnexion = lireDonneeUrl("cmdDeconnecter");
  if ( $demandeDeconnexion == "on") {
      deconnecterUtilisateur();
      header("Location: cAccueil.php");
  }
    
  // �tablissement d'une connexion avec le serveur de donn�es 
  // puis s�lection de la BD qui contient les donn�es des visiteurs et de leurs frais
  $idConnexion=connecterServeurBD();

  if (!$idConnexion) {
      ajouterErreur($tabErreurs, "Echec de la connexion au serveur MySql");
  }
  elseif (!activerBD($idConnexion)) {
      ajouterErreur($tabErreurs, "La base de donn�es gsb_frais est inexistante ou non accessible");
  }
  
?>