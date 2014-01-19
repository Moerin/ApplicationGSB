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

/* récupération des données entrées pour l'affichage des différentes parties de la page
infos id visiteur, mois et etape de traitement */
$visiteurChoisi = lireDonnee("lstVisiteur");
$moisChoisi = lireDonnee("lstMois");
$etapeChoisi = lireDonnee("etape");
$tabQteEltsForfait = lireDonneePost("txtEltsForfait", "");

// variable d'information sur la fiche et l'utilisateur
//$visiteurNom;
//$visiteurPrenom;
$libelleMois;
// actions sur les difféntes étapes du cas d'utilisation
if ($etapeChoisi == "choixVisiteur") {

} elseif ($etapeChoisi == "choixMois") {

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
}

?>

<div id="contenu">
    <h1>Valider les fiches de frais</h1>
    <form id="formChoixVisiteur" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="choixVisiteur" />
            <label class="title">Choisir le visiteur :</label>
            <select name="lstVisiteur" id="idLstVisiteur" class="zone" >
                <?php
                // Dans le cas où aucun visiteur n'a été choisi on le signifie dans le sélection de liste
                if ( $visiteurChoisi == "") {
                    ?>
                    <option value="-1"> Sélectionner un visiteur médical </option>
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
                $req = obtenirReqMoisFicheFrais($visiteurChoisi, 'CL'); // on recupère les mois ou la saisie est cloturée = 'CL'
                $idJeuMois = mysql_query($req, $idConnexion);
                $lgMois = mysql_fetch_assoc($idJeuMois);
                // ref cas utilisation 4-a Si il n4existe pas de fiche de frais on affiche l'erreur "Pas de fiche de frais pour ce visiteur ce mois"
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
    if ($visiteurChoisi != "" && $moisChoisi !== "") {
    ?>
    <?php 
    $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi);
    ?>
    Situation de la fiche de frais au mois de <?php echo $libelleMois; ?> : 
    <?php echo $tabFicheFrais["libelleEtat"]; ?>
    <h2>Frais au forfait</h2>
    <?php
    $req = obtenirReqEltsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuForfait = mysql_query($req, $idConnexion);
    $lgEltsForfait = mysql_fetch_assoc($idJeuForfait);
    ?>
    <form id="formFraisForfait" method="post" action="" >
        <p>
            <input type="hidden" name="etape" value="actualiserFraisForfait" />
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
        </p>
        <table id="tableF">
            <tr>
                <th>Repas midi</th><th>Nuitée</th><th>Etape</th><th>Km</th><th>Action</th>
            </tr>    
            <tr>
                <?php
                while(is_array($lgEltsForfait)) {
                    if ($lgEltsForfait["idFraisForfait"] == "ETP") {
                        $etp = $lgEltsForfait["quantite"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "KM") {
                        $km = $lgEltsForfait["quantite"];
                    } elseif ($lgEltsForfait["idFraisForfait"] == "NUI") {
                        $nui = $lgEltsForfait["quantite"]; 
                    } else {
                        $rep = $lgEltsForfait["quantite"];
                    }
                    $lgEltsForfait = mysql_fetch_assoc($idJeuForfait);
                }
                // 
                ?>
                <td><input type="number" id="idETP" name="txtEltsForfait[ETP]" value="<?php echo $etp; ?>" /></td>
                <td><input type="number" id="idKM" name="txtEltsForfait[KM]" value="<?php echo $km; ?>" </td>
                <td><input type="number" id="idNUI" name="txtEltsForfait[NUI]" value="<?php echo $nui; ?>" </td>
                <td><input type="number" id="idREP" name="txtEltsForfait[REP]" value="<?php echo $rep; ?>" /></td>
                <td>
                    <div id="actionsFraisForfait" class="actions">
                           <img src="images/actualiserIcon.png" id="lkActualiserLigneFraisForfait" class="icon"
                           alt="icone Actualiser"  onclick="actualiserLigneFraisForfait(<?php echo $rep; ?>,
                           <?php echo $nui; ?>,<?php echo $etp; ?>,<?php echo $km; ?>);"  title="Actualiser la ligne de frais forfaitisé" />
                           <img src="images/reinitialiserIcon.png" id="lkReinitialiserLigneFraisForfait" class="icon"
                           alt="icone Réinitialiser" onclick="reinitialiserLigneFraisForfait();" title="Rénitialiser la ligne de frais forfaitisé" />
                    </div>
                </td>
                <?php
                    mysql_free_result($idJeuForfait);
                ?>
            </tr>
        </table>
    </form>
    <div id="msgFraisForfait" class="infosNonActualisees">
        Attention, les modifications doivent être actualisées pour être réellement prises en compte...
    </div>
    <h2>Hors forfait</h2>
    <?php
    $req = obtenirReqEltsHorsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuHorsForfait = mysql_query($req, $idConnexion);
    $lgEltsHorsForfait = mysql_fetch_assoc($idJeuHorsForfait);
    $nbJustificatif = 0;
    ?>
        <?php
        while(is_array($lgEltsHorsForfait)) {
        ?>
    <form id="formFraisHorsForfait<?php echo $lgEltsHorsForfait['id'];?>" method="post" action="" >
        <p>
            <input type="hidden" id="idEtape<?php echo $lgEltsHorsForfait['id']; ?>" name="etape" value="actualiserFraisHorsForfait"/>
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
            <input input type="hidden" name="txtEltsHorsForfait[id]" value="<?php echo $lgEltsHorsForfait['id']; ?>" />
        </p>
        <table id="tableHF">
            <tr>
                <th>Date</th><th>Libellé</th><th>Montant</th><th>Action</th>
            </tr>
            <tr>
                <td><input value="<?php echo convertirDateAnglaisVersFrancais($lgEltsHorsForfait["date"]); ?>" /></td>
                <?php
                // Si lle libelle REFUSÉ : est présent on barre le texte sinon on l'affiche normalement
                if (strpos($lgEltsHorsForfait["libelle"], "REFUSÉ : ") == false) {
                ?>
                <td>
                <?php    
                } else {
                ?>
                <td class="tdLineThrough">  
                <?php  
                }
                ?>
                <input value="<?php echo $lgEltsHorsForfait["libelle"]; ?>" /></td>
                <td><input value="<?php echo $lgEltsHorsForfait["montant"]; ?>" /></td>
                <td id="tdAction">
                    <div id="actionsFraisForfait" class="actions">
                           <img src="images/actualiserIcon.png" id="lkActualiserLigneFraisHF" class="icon"
                           alt="icone Actualiser"  onclick=""  title="Actualiser la ligne hors forfait" />
                           <img src="images/reinitialiserIcon.png" id="lkReinitialiserLigneFraisHF" class="icon"
                           alt="icone Réinitialiser" onclick="reinitialiserLigneFraisHorsForfait(idElementHF);" title="Rénitialiser la ligne hors forfait" />
                    </div>
                </td>
            <?php
                $lgEltsHorsForfait = mysql_fetch_assoc($idJeuHorsForfait);
                $nbJustificatif += 1;
            }
            mysql_free_result($idJeuHorsForfait)
            ?>
            </tr>
        </table>
    </form>
    <p>Nombre de justificatif</p><input name="justificatif" value="<?php echo $nbJustificatif; ?>"/>
    <?php    
    }
    ?>
</div>

<script type="text/javascript">
<?php
    require($repInclude . "_fonctionsValidFichesFrais.inc.js");
?>
</script>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>