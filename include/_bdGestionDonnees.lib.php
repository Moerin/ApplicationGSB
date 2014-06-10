<?php
/** 
 * Regroupe les fonctions d'accès aux données.
 * 
 * @package default
 * @author Arthur Martin, Sebastien Charret
 * @todo Fonctions retournant plusieurs lignes sont à réécrire.
 */

/** 
 * Se connecte au serveur de données MySql.   
 *                    
 * Se connecte au serveur de données MySql à partir de valeurs
 * prédéfinies de connexion (hote, compte utilisateur et mot de passe). 
 * Retourne l'identifiant de connexion si succés obtenu, le booléen false 
 * si problème de connexion.
 * @return resource identifiant de connexion
 */
function connecterServeurBD() {
    $hote = "localhost";
    $login = "moerin_gsb";
    $mdp = "#5y?{7SSl+5.";
    $idConnexion = mysql_connect($hote, $login, $mdp) or die("ERREUR " . mysql_error());
    return $idConnexion;
   
}

/**
 * Sélectionne (rend active) la base de données.
 * 
 * Sélectionne (rend active) la BD prédéfinie gsb_frais sur la connexion
 * identifiée par $idCnx. Retourne true si succés, false sinon.
 * @param resource $idCnx identifiant de connexion
 * @return boolean succés ou échec de sélection BD 
 */
function activerBD($idCnx) {
    $bd = "moerin_database";
    $query = "SET CHARACTER SET utf8";
    // Modification du jeu de caractères de la connexion
    $res = mysql_query($query, $idCnx); 
    $ok = mysql_select_db($bd, $idCnx);

    return $ok;
}

/** 
 * Ferme la connexion au serveur de données.
 * 
 * Ferme la connexion au serveur de données identifiée par l'identifiant de 
 * connexion $idCnx.
 * @param resource $idCnx identifiant de connexion
 * @return void  
 */
function deconnecterServeurBD($idCnx) {
    mysql_close($idCnx);
}

/**
 * Echappe les caractères spéciaux d'une chaîne.
 *
 * Envoie la chaîne $str échappée, càd avec les caractères considérés spéciaux
 * par MySql (tq la quote simple) précédés d'un \, ce qui annule leur effet spécial
 * @param string $str chaîne à échapper
 * @return string chaîne échappée 
 */    
function filtrerChainePourBD($str) {
    if ( ! get_magic_quotes_gpc() ) { 
        // si la directive de configuration magic_quotes_gpc est activée dans php.ini,
        // toute chaine reçue par get, post ou cookie est déjà échappée 
        // par conséquent, il ne faut pas échapper la chaîne une seconde fois                              
        $str = mysql_real_escape_string($str);
    }
    return $str;
}

/** 
 * Fournit les informations sur un utilisateur demandé.
 * 
 * Retourne les informations du utilisateur d'id $unId sous la forme d'un tableau
 * associatif dont les clés sont les noms des colonnes(id, nom, prenom).
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array  tableau associatif du utilisateur
 */
function obtenirDetailUtilisateur($idCnx, $unId) {
    $id = filtrerChainePourBD($unId);
    $requete = "SELECT utilisateur.id, nom, prenom, libelleFonction 
        FROM utilisateur JOIN fonction ON idFonction = fonction.id 
        WHERE utilisateur.id='" . $unId . "'";
    $idJeuRes = mysql_query($requete, $idCnx);  
    $ligne = false;     
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }
    return $ligne ;
}

/** 
 * Fournit les informations des utilisateurs.
 * 
 * Retourne les noms des utilisateurs sous la forme d'un tableau
 * associatif dont les clés sont les noms des colonnes(nom).
 * @param resource $idCnx identifiant de connexion
 * @return array  tableau associatif du utilisateur
 */
function obtenirReqListeUtilisateur() { // TODO: renommer de façon plus pertinante
    $requete = "SELECT DISTINCT utilisateur.id, utilisateur.nom, utilisateur.prenom
        FROM utilisateur JOIN lignefraisforfait ON utilisateur.id = idVisiteur
        WHERE idFonction = 1 order by nom";
    return $requete;
}

/** 
 * Fournit les informations d'une fiche de frais.
 * 
 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
 * sous la forme d'un tableau associatif dont les clés sont les noms des colonnes
 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id utilisateur  
 * @return array tableau associatif de la fiche de frais
 */
function obtenirDetailFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $ligne = false;
    $requete="SELECT IFNULL(nbJustificatifs,0) as nbJustificatifs, etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide 
    FROM fichefrais INNER JOIN etat ON idEtat = etat.id 
    WHERE idVisiteur='" . $unIdVisiteur . "' AND mois='" . $unMois . "'";
    $idJeuRes = mysql_query($requete, $idCnx);  
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
    }        
    mysql_free_result($idJeuRes) or die(mysql_error());
    
    return $ligne ;
}
              
/** 
 * Vérifie si une fiche de frais existe ou non.
 * 
 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du utilisateur 
 * $idUtilisateur existe, false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id utilisateur  
 * @return booléen existence ou non de la fiche de frais
 */
function existeFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "SELECT idVisiteur FROM fichefrais WHERE idVisiteur='" . $unIdVisiteur . 
              "' AND mois='" . $unMois . "'";
    $idJeuRes = mysql_query($requete, $idCnx) or die(mysql_error());  
    $ligne = false ;
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes) or die(mysql_error());
    }        
    
    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
    return is_array($ligne) ;
}

/** 
 * Fournit le mois de la dernière fiche de frais d'un utilisateur.
 * 
 * Retourne le mois de la dernière fiche de frais du utilisateur d'id $unIdUtilisateur.
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdUtilisateur id utilisateur  
 * @return string dernier mois sous la forme AAAAMM
 */
function obtenirDernierMoisSaisi($idCnx, $unIdVisiteur) {
    $requete = "select max(mois) as dernierMois from fichefrais where idVisiteur='" .
            $unIdVisiteur . "'";
    $idJeuRes = mysql_query($requete, $idCnx);
    $dernierMois = false ;
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        $dernierMois = $ligne["dernierMois"];
        mysql_free_result($idJeuRes);
    }        
    return $dernierMois;
}

/**
 * Sélectionne les noms et les id des utilisateurs
 * 
 * triés dans l'ordre alphabétique pour les fiches de frais
 * @return req id et nom visiteur
 */
function obtenirReqUtilisateurFicheFrais() {
    $requete = "select Distinct utilisateur.id, utilisateur.nom, utilisateur.prenom"
            . "from utilisateur order by nom asc";
    return $requete;
} 

/** 
 * Ajoute une nouvelle fiche de frais et les éléments forfaitisés associés.
 *  
 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) de l'utilisateur 
 * $idUtilisateur, avec les éléments forfaitisés associés dont la quantité initiale
 * est affectée à 0. Clot éventuellement la fiche de frais précédente du visiteur. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return void
 */
function ajouterFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    // modification de la dernière fiche de frais du visiteur
    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);
    $laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);
    if ( is_array($laDerniereFiche) && $laDerniereFiche['idEtat']=='CR'){
            modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');
    }

    // ajout de la fiche de frais à l'état Créé
    $requete = "insert into fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('" 
              . $unIdVisiteur 
              . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
    mysql_query($requete, $idCnx);
    
    // ajout des éléments forfaitisés
    $requete = "select id from fraisforfait";
    $idJeuRes = mysql_query($requete, $idCnx);
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        while ( is_array($ligne) ) {
            $idFraisForfait = $ligne["id"];
            // insertion d'une ligne frais forfait dans la base
            $requete = "insert into lignefraisforfait (idVisiteur, mois, idFraisForfait, quantite)
                        values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
            mysql_query($requete, $idCnx);
            // passage au frais forfait suivant
            $ligne = mysql_fetch_assoc ($idJeuRes);
        }
        mysql_free_result($idJeuRes);       
    }        
}

/**
 * Retourne le texte de la requète select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais cloturée. 
 * 
 * La requète de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais cloturée. 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requète select
 */                                                 
function obtenirReqMoisFicheFraisCree($unIdVisiteur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
        . $unIdVisiteur . "' order by fichefrais.mois desc ";
    return $req ;
}  

/**
 * Retourne le texte de la requète select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais cloturée. 
 * 
 * La requète de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais cloturée. 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requète select
 */                                                 
function obtenirReqMoisFicheFraisCloturee($unIdVisiteur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
        . $unIdVisiteur . "' and idEtat ='CL' order by fichefrais.mois desc ";
    return $req ;
}  
 
/**
 * Retourne le texte de la requète select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais validée. 
 * 
 * La requète de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais validée ou mise en paiement. 
 * @param string $unIdVisiteur id visiteur 
 * @param string $unEtat id etat 
 * @return string texte de la requète select
 */                                                 
function obtenirReqMoisFicheFraisValidee($unIdVisiteur) {
    $req = "select fichefrais.mois as mois, fichefrais.idEtat as etat from  fichefrais where fichefrais.idvisiteur ='"
        . $unIdVisiteur . "' and not idEtat = 'CL' and not idEtat = 'CR' and not idEtat = 'RB' order by fichefrais.mois desc ";
    return $req ;
}

/**
 * Retourne le texte de la requète select concernant les éléments forfaitisés 
 * d'un visiteur pour un mois donnés. 
 * 
 * La requète de sélection fournie permettra d'obtenir l'id, le libellé et la
 * quantité des éléments forfaitisés de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requète select
 */                                                 
function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "select idFraisForfait, libelle, quantite, montant from lignefraisforfait
              inner join fraisforfait on fraisforfait.id = lignefraisforfait.idFraisForfait
              where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Retourne le texte de la requète select concernant les éléments hors forfait 
 * d'un visiteur pour un mois donnés. 
 * 
 * La requète de sélection fournie permettra d'obtenir l'id, la date, le libellé 
 * et le montant des éléments hors forfait de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requète select
 */                                                 
function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "select id, date, libelle, montant from lignefraishorsforfait
              where idVisiteur='" . $unIdVisiteur 
              . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Reporte d'un mois une ligne de frais hors forfait
 * 
 * @param resource $idCnx identifiant de connexion
 * @param int $unIdLigneHF identifiant de ligne hors forfait
 * @return void
 */
function reporterLigneHorsForfait($idCnx, $unIdLigneHF) {
    mysql_query('CALL reporterLigneFraisHorsForfait(' . $unIdLigneHF . ');', $idCnx) or die(mysql_error());
}

/**
 * Supprime une ligne hors forfait.
 * 
 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
 * @param resource $idCnx identifiant de connexion
 * @param string $idLigneHF id de la ligne hors forfait
 * @return void
 */
function supprimerLigneHF($idCnx, $unIdLigneHF) {
    $requete = "delete from lignefraishorsforfait where id = " . $unIdLigneHF;
    mysql_query($requete, $idCnx) or die(mysql_error());
}

/**
 * Ajoute une nouvelle ligne hors forfait.
 * 
 * Insère dans la BD la ligne hors forfait de libellé $unLibelleHF du montant 
 * $unMontantHF ayant eu lieu à la date $uneDateHF pour la fiche de frais du mois
 * $unMois du visiteur d'id $unIdVisiteur
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (AAMMMM)
 * @param string $unIdVisiteur id du visiteur
 * @param string $uneDateHF date du frais hors forfait
 * @param string $unLibelleHF libellé du frais hors forfait 
 * @param double $unMontantHF montant du frais hors forfait
 * @return void
 */
function ajouterLigneHF($idCnx, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
    $unLibelleHF = filtrerChainePourBD($unLibelleHF);
    $uneDateHF = filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF));
    $unMois = filtrerChainePourBD($unMois);
    $requete = "insert into lignefraishorsforfait(idVisiteur, mois, date, libelle, montant) 
                values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF .")";
    mysql_query($requete, $idCnx);
}

/**
 * Modifie les quantités des éléments forfaitisés d'une fiche de frais.
 * 
 * Met à jour les éléments forfaitisés contenus  
 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
 * le mois $unMois dans la table LigneFraisForfait, aprés avoir filtré 
 * (annulé l'effet de certains caractères considérés comme spéciaux par 
 *  MySql) chaque donnée   
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA) 
 * @param string $unIdVisiteur  id visiteur
 * @param array $desEltsForfait tableau des quantités des éléments forfait
 * avec pour clés les identifiants des frais forfaitisés 
 * @return void  
 */
function modifierEltsForfait($idCnx, $unMois, $unIdVisiteur, $desEltsForfait) {
    $unMois=filtrerChainePourBD($unMois);
    $unIdVisiteur=filtrerChainePourBD($unIdVisiteur);
    foreach ($desEltsForfait as $idFraisForfait => $quantite) {
        $requete = "update lignefraisforfait set quantite = " . $quantite 
                    . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"
                    . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
      mysql_query($requete, $idCnx);
    }
}

/**
 * Modifie les quantités des éléments d'une fiche de frais hors forfait. 
 * 
 * Met à jour les éléments forfaitisés contenus  
 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
 * le mois $unMois dans la table LigneFraisForfait, aprés avoir filtré 
 * (annulé l'effet de certains caractères considérés comme spéciaux par 
 *  MySql) chaque donnée   
 * @param resource $idCnx identifiant de connexion
 * @param array $desEltsHorsForfait tableau des quantités des éléments hors forfait
 * avec pour clés les identifiants des frais forfaitisés 
 * @return void  
 */
function modifierEltsHorsForfait($idCnx, $desEltsHorsForfait) {
    foreach ($desEltsHorsForfait as $cle => $val) {
        switch ($cle) {
            case 'id':
                $idFraisHorsForfait = $val;
                break;
            case 'libelle':
                $libelleFraisHorsForfait = $val;
                break;
            case 'date':
                $dateFraisHorsForfait = $val;
                break;
            case 'montant':
                $montantFraisHorsForfait = $val;
                break;
        }
    }
    $requete = "update lignefraishorsforfait"
            . " set libelle = '" . filtrerChainePourBD($libelleFraisHorsForfait) . "',"
            . " date = '" . convertirDateFrancaisVersAnglais($dateFraisHorsForfait) . "',"
            . " montant = " . $montantFraisHorsForfait
            . " where id = " . $idFraisHorsForfait;
    $status = mysql_query($requete, $idCnx);
}

/**
 * Contrôle les informations de connexion d'un utilisateur Visiteur.
 * 
 * Vérifie si les informations de connexion $unLogin, $unMdp sont ou non validés.
 * Retourne les informations de l'utilisateur sous forme de tableau associatif 
 * dont les clés sont les noms des colonnes (id, nom, prenom, login, mdp)
 * si login et mot de passe existent, le booléen false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unLogin login
 * @param string $unMdp mot de passe 
 * @return array tableau associatif ou booléen false 
 */
function verifierInfosConnexionUtilisateur($idCnx, $unLogin, $unMdp) {
    $unLogin = filtrerChainePourBD($unLogin);
    $unMdp = filtrerChainePourBD($unMdp);
    // le mot de passe est crypté dans la base avec la fonction de hachage md5
    $req = "select id, nom, prenom, login, mdp , idFonction from utilisateur where login='".$unLogin."' and mdp='" . $unMdp . "'";
    $idJeuRes = mysql_query($req, $idCnx);
    $ligne = false;
    if ( $idJeuRes ) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }
    return $ligne;
}

/**
 * Modifie le nombre de justificatif d'une fiche de frais
 * 
 * Met à jour le nombre de justificatif de la fiche de frais 
 * du visiteur $unIdVisiteur pour le mois $unMois 
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur 
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function modifierJustificatifFicheFrais($idCnx, $unMois, $unIdVisiteur, $desJustificatifs) {
    $requete = "update fichefrais set nbJustificatifs=" . $desJustificatifs . " where idVisiteur='" . $unIdVisiteur ."' 
    and mois='". $unMois . "';";
    mysql_query($requete, $idCnx);
}

/**
 * Modifie l'état et la date de modification d'une fiche de frais
 * 
 * Met à jour l'état de la fiche de frais du visiteur $unIdVisiteur pour
 * le mois $unMois à la nouvelle valeur $unEtat et passe la date de modif à 
 * la date d'aujourd'hui
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur 
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function modifierEtatFicheFrais($idCnx, $unMois, $unIdVisiteur, $unMontant, $unEtat) {
    $requete = "update fichefrais set idEtat = '" . $unEtat . 
               "', dateModif = CURDATE(), montantValide= '" . $unMontant . "' where idVisiteur ='" .
               $unIdVisiteur . "' and mois = '". $unMois . "'";
    mysql_query($requete, $idCnx);
}

/**
 * Cloture les fiches de frais antécédante au mois $unMois
 *
 * Cloture les fiches de frais antécédante au mois $unMois
 * et par la suite créer une nouvelle fiche pour le mois courant
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function cloturerFichesFrais($idCnx, $unMois) {
    $req = "SELECT idVisiteur, mois , montantValide FROM fichefrais WHERE idEtat = 'CR' AND CAST(mois AS unsigned) < $unMois ;";
    $idJeuFichesFrais = mysql_query($req, $idCnx) or die(mysql_error());
    while ($lgFicheFrais = mysql_fetch_assoc($idJeuFichesFrais)) {
        modifierEtatFicheFrais($idCnx, $lgFicheFrais['mois'], $lgFicheFrais['idVisiteur'], $lgFicheFrais['montantValide'],'CL');
        // Vérification de l'existence de la fiche de frais pour le mois courant
        $existeFicheFrais = existeFicheFrais($idCnx, $unMois, $lgFicheFrais['idVisiteur']);
        // si elle n'existe pas, on la crée avec les éléments de frais forfaitisés à 0
        if (!$existeFicheFrais) {
            ajouterFicheFrais($idCnx, $unMois, $lgFicheFrais['idVisiteur']);
        }
    }
}

/**
 * Finalise la fiche de frais
 *
 * Finalise la fiche de frais prenant un etat $etat en paramètre
 * et le fait passer à l'étape supérieur
 * ex : Validée -> Mise en paiement
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois sous la forme aaaamm
 * @param string $unIdVisiteur 
 * @param string $unEtat
 * @return void 
 */
function finaliserFichesFrais($idCnx, $unMois, $unVisiteur, $unMontant, $unEtat) {
    if ($unEtat == "VA") {
       modifierEtatFicheFrais($idCnx, $unMois, $unVisiteur, $unMontant, "MP");
    } elseif ($unEtat == "MP") {
        modifierEtatFicheFrais($idCnx, $unMois, $unVisiteur, $unMontant, "RB");
    }
}
?>
