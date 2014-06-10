<?php
/**
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si utilisateur non connecté
if (!estUtilisateurConnecte()) {
    header("Location: cSeConnecter.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");

// affectation du mois précédent pour la validation des fiches de frais
$mois = sprintf("%04d%02d", date("Y"), date("m"));
// Cloture des fiches de frais antérieur au mois courant et au besoin, création des fiches pour le mois courant
cloturerFichesFrais($idConnexion, $mois);

/* récupération des données entrées pour l'affichage des différentes parties de la page
infos id visiteur, mois et etape de traitement */
$visiteurChoisi = lireDonnee("lstVisiteur");
$moisChoisi = lireDonnee("lstMois");
$etapeChoisi = lireDonnee("etape");
$tabQteEltsForfait = lireDonneePost("txtEltsForfait", "");
$tabQteEltsHorsForfait = lireDonneePost("txtEltsHorsForfait", "");
$nbJustificatifs = lireDonneePost("nbJustificatifs", "");
$montantTotalElts = lireDonneePost("lstMontantEF", "");
$montantTotalElts += lireDonneePost("lstMontantHF", "");
$lgVisiteur = obtenirDetailUtilisateur($idConnexion, $visiteurChoisi);

// Declaration des variables
// variable d'information sur la fiche et l'utilisateur
$libelleMois = "";

// variable sur les éléments forfaitisés
$montantElementForfaitise = 0.0;

// variable sur les éléments hors forfait
$libelleFraisHorsForfait = "";
$montantFraisHorsForfait = "";
$dateFraisHorsForfait = "";
$montantFraisHorsForfaitTotal = 0.0;

// actions sur les difféntes étapes du cas d'utilisation
if ($etapeChoisi == "choixVisiteur") {

} elseif ($etapeChoisi == "choixMois") {

// étape d'actualisation des éléments forfaitisés
} elseif ($etapeChoisi == "actualiserFraisForfait") {
    $valid = verifierEntiersPositifs($tabQteEltsForfait);
    if (!$valid) { // cas où les élements ne seraient pas des entiers positifs
        ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et un entier positif.");
    } else { // dans le cas contraire les éléments peuvent être ajoutés
        modifierEltsForfait($idConnexion, $moisChoisi, $visiteurChoisi, $tabQteEltsForfait);
        ?>
        <p class="info">L'actualisation des quantités au forfait a bien été enregistré</p>
        <?php
    }
    
// étape d'actualisation des éléments hors forfait
} elseif ($etapeChoisi == "actualiserFraisHorsForfait") {
    foreach ($tabQteEltsHorsForfait as $cle => $val) {
        switch ($cle) {
            case "libelle":
                $libelleFraisHorsForfait = $val;
                Break;
            case "montant":
                $montantFraisHorsForfait = $val;
                Break;
            case "date":
                $dateFraisHorsForfait = $val;
                Break;
        }
    }
    // Verification de éléments constituant la fiche de frais
    verifierLigneFraisHF($dateFraisHorsForfait, $libelleFraisHorsForfait, $montantFraisHorsForfait, $tabErreurs);
    if ($tabErreurs != 0) { // si aucune erreur est présente on passe à la modification
        modifierEltsHorsForfait($idConnexion, $tabQteEltsHorsForfait);
        ?>
        <p class="info">L'actualisation des éléments hors forfait a bien été enregistré</p>
        <?php
    }
// étape d'actualisation des nombres de justificatifs
} elseif ($etapeChoisi == "actualiserNbJustificatifs") {
    $valid = estEntierPositif($nbJustificatifs);
    if (!$valid) { // cas où les élements ne seraient pas des entiers positifs
        ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et un entier positif.");
    } else { // dans le cas contraire les éléments peuvent être ajoutés
        modifierJustificatifFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi, $nbJustificatifs);
        ?>
        <p class="info">L'actualisation des justificatifs a bien été enregistré</p>
        <?php
    }
} elseif ($etapeChoisi == "validerFiche") {
    modifierEtatFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi, $montantTotalElts, "VA");
    ?>
    <p class="info">La fiche de frais du visiteur <?php echo $lgVisiteur['prenom'] . " " . $lgVisiteur['nom']; ?> 
        pour <?php echo obtenirLibelleMois(intval(substr($moisChoisi, 4, 2))) . " " . intval(substr($moisChoisi, 0, 4)); ?> 
        a bien été enregistrée</p>        
    <?php
} elseif ($etapeChoisi === 'reporterLigneFrais') {
    reporterLigneHorsForfait($idConnexion, $tabQteEltsHorsForfait['id']);
    ?>
    <p class="info">La fiche à bien été reporté au mois suivant</p>
    <?php
}
?>

<!-- Division principale -->
<div id="contenu">
    <h1>Valider les fiches de frais</h1>
    <form id="formChoixVisiteur" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="choixVisiteur" />
            <label class="title">Choisir le visiteur :</label>
            <select name="lstVisiteur" id="idLstVisiteur" class="zone" onchange="this.form.submit();" >
                <?php
                // Dans le cas où aucun visiteur n'a été choisi on le signifie dans le sélection de liste
                if ( $visiteurChoisi == "") {
                    ?>
                    <option value="-1"> Sélectionner un visiteur medical </option>
                    <?php
                }
                $req = obtenirReqListeUtilisateur();
                $idJeuVisiteurs = mysql_query($req, $idConnexion);
                // Boucle permettant de remplir la liste de sélection
                while ($lgVisiteur = mysql_fetch_array($idJeuVisiteurs)) {
                    ?>
                    <option value="<?php echo $lgVisiteur['id'];?>"<?php 
                    if ($visiteurChoisi == $lgVisiteur['id']) { ?> selected="selected"<?php } ?>><?php echo $lgVisiteur['nom']
                            . " " . $lgVisiteur['prenom']; ?></option>
                    <?php
                }
           
                mysql_free_result($idJeuVisiteurs);
                ?>
            </select>
        </p>
    </form>
    <?php
    // Tant qu'un visiteur n'a pas été sélectionné on ne propose pas la sélection des mois
    if ($visiteurChoisi != "") {
        ?>
        <form id="formChoixMois" method="post" action="">
            <p>
                <input type="hidden" name="etape" value="choixMois" />
                <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
                <?php
                // On affiche les mois pour lesquels le visiteur dipose d'une fiche de frais
                $req = obtenirReqMoisFicheFraisCloturee($visiteurChoisi); // on recupère les mois ou la saisie est cloturée = 'CL'
                $idJeuMois = mysql_query($req, $idConnexion);
                $lgMois = mysql_fetch_assoc($idJeuMois);
                // ref cas utilisation 4-a Si il n'existe pas de fiche de frais on affiche l'erreur "Pas de fiche de frais pour ce visiteur ce mois"
                // retour au cas 2
                if (empty($lgMois)) {
                    ajouterErreur($tabErreurs, "Pas de fiche de frais à valider pour ce visiteur");
                    echo toStringErreurs($tabErreurs);
                } else {
                    ?>
                    <label class ="titre"> Mois :</label>
                    <select name="lstMois" id="idDateValid" class="zone" onchange="this.form.submit();">
                    <?php
                    // Si aucun mois n'a encore été choisi, on place en premier une invitation au choix
                    if ($moisChoisi == "") {
                        ?>
                        <option value="-1"> Sélectionner un mois </option>
                        <?php
                    } 
                    while (is_array($lgMois)) {
                        $mois = $lgMois["mois"];
                        $noMois = intval(substr($mois, 4, 2));
                        $libelleMois = obtenirLibelleMois($noMois);
                        $annee = intval(substr($mois,0,4));
                        ?>
                        <option value="<?php echo $mois; ?>"
                            <?php 
                            if ($moisChoisi == $mois) { 
                                ?> selected="selected"<?php 
                            } 
                            ?>>
                        <?php echo $libelleMois . ' ' . $annee; ?></option>
                        <?php
                        $lgMois = mysql_fetch_assoc($idJeuMois);
                    }
                    mysql_free_result($idJeuMois);
                }
                ?>
                </select>
            </p>
        </form>
        <p class="titre">&nbsp;</p>
    <?php
    }
	// Le formulaire de gestion des frais n'est visible que si le visiteur et le mois ont été sélectionné
    if ($visiteurChoisi != "" && $moisChoisi !== "") {
    ?>
    <?php 
    // Affichage de la situation de la fiche de frais
    $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi);
    ?>
    Situation de la fiche de frais au mois de <?php echo $libelleMois; ?> : 
    <?php echo $tabFicheFrais["libelleEtat"]; ?>
    <h2>Frais au forfait</h2>
    <?php
    $req = obtenirReqEltsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuForfait = mysql_query($req, $idConnexion);
    $lgEltsForfait = mysql_fetch_assoc($idJeuForfait) or die(mysql_error());
    ?>
    <form id="formFraisForfait" method="post" action="" >
        <p>
            <input type="hidden" name="etape" value="actualiserFraisForfait" />
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
            <input type="hidden" name="lstMontantEF" value="<?php echo $montantElementForfaitise; ?>" />
        </p>
        <table id="tableF">
            <tr>
                <th>Etape</th>
                <th>Repas midi</th>
                <th>Nuitée</th>
                <th>Véhicule 4CV Diesel</th>
                <th>Véhicule 4CV Essence</th>
                <th>Véhicule 5/6CV Diesel</th>
                <th>Véhicule 5/6CV Essence</th>
                <th>Action</th>
            </tr>    
            <tr>
                <?php
                // Les valeurs sont affectées en fonction de la clef du tableau associatif
                while(is_array($lgEltsForfait)) {
                    if ($lgEltsForfait["idFraisForfait"] == "ETP") {
                        $etp = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $etp * $lgEltsForfait["montant"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "REP") {
                        $rep = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $rep * $lgEltsForfait["montant"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "NUI") {
                        $nui = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $nui * $lgEltsForfait["montant"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "KM4d") {
                        $km4d = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $km4d * $lgEltsForfait["montant"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "KM4e") {
                        $km4e = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $km4e * $lgEltsForfait["montant"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "KM56d") {
                        $km56d = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $km56d * $lgEltsForfait["montant"];
                    }else {
                        $km56e = $lgEltsForfait["quantite"];
                        $montantElementForfaitise += $km56e * $lgEltsForfait["montant"];
                    }
                    $lgEltsForfait = mysql_fetch_assoc($idJeuForfait);
                }
                ?>
                <td><input type="text" id="idETP" name="txtEltsForfait[ETP]" value="<?php echo $etp; ?>" /></td>
                <td><input type="text" id="idKM4d" name="txtEltsForfait[KM4d]" value="<?php echo $km4d; ?>" </td>
                <td><input type="text" id="idKM4e" name="txtEltsForfait[KM4e]" value="<?php echo $km4e; ?>" </td>
                <td><input type="text" id="idKM56d" name="txtEltsForfait[KM56d]" value="<?php echo $km56d; ?>" </td>
                <td><input type="text" id="idKM56e" name="txtEltsForfait[KM56e]" value="<?php echo $km56e; ?>" </td>
                <td><input type="text" id="idNUI" name="txtEltsForfait[NUI]" value="<?php echo $nui; ?>" </td>
                <td><input type="text" id="idREP" name="txtEltsForfait[REP]" value="<?php echo $rep; ?>" /></td>
                <td>
                    <div id="actionsFraisForfait" class="actions">
                           <img src="images/actualiserIcon.png" id="lkActualiserLigneFraisForfait" class="icon"
                           alt="icone Actualiser"  onclick="actualiserLigneFraisForfait(<?php echo $rep; ?>,
                           <?php echo $nui; ?>,<?php echo $etp; ?>,<?php echo $km4d; ?>,<?php echo $km4e; ?>,<?php echo $km56d; ?>,<?php echo $km56e; ?>);"  title="Actualiser la ligne de frais forfaitisé" />
                           <img src="images/reinitialiserIcon.png" id="lkReinitialiserLigneFraisForfait" class="icon"
                           alt="icone Réinitialiser" onclick="reinitialiserLigneFraisForfait();" title="Rénitialiser la ligne de frais forfaitisé" />
                    </div>
                </td>
                <?php
                    mysql_free_result($idJeuForfait);
                ?>
            </tr>
        </table>

        <p>MONTANT TOTAL FRAIS FORFAITISE : <?php echo $montantElementForfaitise ?></p>
    </form>
    <div id="msgFraisForfait" class="infosNonActualisees">
        Attention, les modifications doivent être actualisées pour être réellement prises en compte...
    </div>
    
    <h2>Hors forfait</h2>
    <?php
    // On récupére les lignes hors forfait pour le traitement
    $req = obtenirReqEltsHorsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuHorsForfait = mysql_query($req, $idConnexion) or die(mysql_error());
    $lgEltsHorsForfait = mysql_fetch_assoc($idJeuHorsForfait);
      
    do
    {
        $montantFraisHorsForfaitTotal += $lgEltsHorsForfait["montant"];
    ?>
    <form id="formFraisHorsForfait<?php echo $lgEltsHorsForfait['id'];?>" method="post" action="" >
        <p>
            <input type="hidden" id="idEtape<?php echo $lgEltsHorsForfait['id']; ?>" name="etape" value="actualiserFraisHorsForfait"/>
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
            <input type="hidden" name="lstMontantHF" value="<?php echo $montantFraisHorsForfaitTotal; ?>" />
            <input input type="hidden" name="txtEltsHorsForfait[id]" value="<?php echo $lgEltsHorsForfait['id']; ?>" />
        </p>
        <table id="tableHF">
            <tr>
                <th>Date</th><th>Libellé</th><th>Montant</th><th>Action</th>
            </tr>
            <tr>
                <td>
                    <input id="idDate<?php echo $lgEltsHorsForfait["id"]?>" name="txtEltsHorsForfait[date]" value="<?php echo convertirDateAnglaisVersFrancais($lgEltsHorsForfait["date"]); ?>" />
                </td>
                <td>
                <?php
                // Si le libelle REFUSÉ : est présent on barre le texte sinon on l'affiche normalement
                if (strpos($lgEltsHorsForfait["libelle"], "REFUSÉ : ") === false) {
                ?>
                <input
                <?php   
                } else {
                ?>
                <input class="tdLineThrough"
                <?php  
                }
                ?>
                    id="idLibelle<?php echo $lgEltsHorsForfait["id"]?>" name="txtEltsHorsForfait[libelle]" 
                           value="<?php echo filtrerChainePourNavig($lgEltsHorsForfait["libelle"]); ?>" />
                </td>
                <td>
                    <input id="idMontant<?php echo $lgEltsHorsForfait["id"]?>" name="txtEltsHorsForfait[montant]" 
                           value="<?php echo $lgEltsHorsForfait["montant"]; ?>" />
                </td>
                <td id="tdAction">
                    <div id="actionsFraisHorsForfait<?php echo $lgEltsHorsForfait["id"] ?>" class="actions">
                        <img src="images/actualiserIcon.png" id="lkActualiserLigneFraisHF" class="icon"
                            alt="icone Actualiser"  onclick="actualiserLigneFraisHorsForfait('<?php echo $lgEltsHorsForfait["id"];?>',
                                   '<?php echo convertirDateAnglaisVersFrancais($lgEltsHorsForfait["date"]); ?>',     
                                   '<?php echo filtrerChainePourNavig($lgEltsHorsForfait["libelle"]); ?>',
                                   '<?php echo $lgEltsHorsForfait["montant"]; ?>')"
                            title="Actualiser la ligne hors forfait"  title="Actualiser la ligne de frais hors forfait" />
                        <!--<img src="images/reinitialiserIcon.png" id="lkReinitialiserLigneFraisHF" class="icon"
                            alt="icone Réinitialiser" onclick="reinitialiserLigneFraisHorsForfait('<?php echo $lgEltsHorsForfait['id']; ?>');" 
                            title="Rénitialiser la ligne hors forfait" />-->
                            <?php
                            // L'option "Supprimer" n'est proposée que si les frais n'ont pas déjà été refusés
                            if (strpos($lgEltsHorsForfait['libelle'], 'REFUSÉ : ') === false) {
                            ?>
                        <img src="images/refuseIcon.png" id="lkRefuserLigneFraisHF" class="icon"
                            alt="icone Refuser" onclick="refuseLigneFraisHorsForfait('<?php echo $lgEltsHorsForfait['id']; ?>');" 
                            title="Refuser la ligne hors forfait" />
                        <img src="images/reporterIcon.png" id="lkReporterLigneFraisHF" class="icon"
                            alt="icone Reporter" onclick="reporterLigneFraisHorsForfait('<?php echo $lgEltsHorsForfait['id']; ?>');" 
                            title="Reporter la ligne hors forfait" />
                            <?php // J'ai scindé ca avec reinitialiser a voir si c'est pertinent
                            } else {
                            ?>
                        <img src="images/reintegrerIcon.png" id="lkReintegrerLigneFraisHF" class="icon"
                            alt="icone Reintegrer" onclick="reintegrerLigneFraisHorsForfait('<?php echo $lgEltsHorsForfait['id']; ?>');" 
                            title="Reintegrer la ligne hors forfait" />
                            <?php
                            }
                            ?>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <div id="msgFraisHorsForfait<?php echo $lgEltsHorsForfait['id']; ?>" class="infosNonActualisees">
        Attention, les modifications doivent être actualisées pour être réellement prises en compte...</div>
        <?php
            $lgEltsHorsForfait = mysql_fetch_assoc($idJeuHorsForfait);
        }while(is_array($lgEltsHorsForfait));
        mysql_free_result($idJeuHorsForfait)
    // Form d'actualisation du nombre de justificatif  
    ?>
    <p>MONTANT TOTAL FRAIS HORS FORFAIT : <?php echo $montantFraisHorsForfaitTotal ?></p>
    <form id="formNbJustificatifs" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="actualiserNbJustificatifs" />
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
        </p>
        <div class="titre">Nombre de justificatifs :
            <?php
            $laFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi);
            ?>
            <input type="text" class="zone" size="4" id="idNbJustificatifs" name="nbJustificatifs" 
                   value="<?php echo $laFicheFrais['nbJustificatifs']; ?>" style="text-align:center;" 
                   onchange="afficheMsgNbJustificatifs();" />
            <div id="actionsNbJustificatifs" class="actions">
                <a class="actions" id="lkActualiserNbJustificatifs" 
                   onclick="actualiserNbJustificatifs(<?php echo $laFicheFrais['nbJustificatifs']; ?>);" 
                   title="Actualiser le nombre de justificatifs">&nbsp;
                    <img src="images/actualiserIcon.png" class="icon" alt="icone Actualiser" />
                </a>
                <a class="actions" id="lkReinitialiserNbJustificatifs" 
                   onclick="reinitialiserNbJustificatifs();" 
                   title="Réinitialiser le nombre de justificatifs">&nbsp;
                    <img src="images/reinitialiserIcon.png" class="icon" alt="icone Réinitialiser" />
                </a>
            </div>
        </div>
    </form>
    <div id="msgNbJustificatifs" class="infosNonActualisees">
         Attention, le nombre de justificatifs doit être actualisé pour être réellement pris en compte...</div>
    <form id="formValidFiche" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="validerFiche" />
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
            <input type="hidden" name="lstMontantEF" value="<?php echo $montantElementForfaitise; ?>" />
            <input type="hidden" name="lstMontantHF" value="<?php echo $montantFraisHorsForfaitTotal; ?>" />
        <p>
            <input id="validInput" class="zone" type="button" 
                   onclick="validerFiche();" value="Valider cette fiche" />
        </p>
    </form>
    <?php    
    }
    ?>
</div>
<?php
// Inclusion des fonctions javascript
?>
<script type="text/javascript">
<?php
    require($repInclude . "_fonctionsFiches.inc.js");
?>
</script>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
