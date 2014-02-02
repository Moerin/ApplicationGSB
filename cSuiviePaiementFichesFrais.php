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
$validationChoisi = lireDonneePost("validation");
$lgVisiteur = obtenirDetailUtilisateur($idConnexion, $visiteurChoisi);


// Declaration des variables :
// -- variable d'information sur la fiche et l'utilisateur --
$libelleMois = "";
$etatFiche = "";

// -- variable sur les éléments hors forfait --
$libelleFraisHorsForfait = "";
$montantFraisHorsForfait = "";
$dateFraisHorsForfait = "";

// actions sur les difféntes étapes du cas d'utilisation
if ($etapeChoisi == "choixVisiteur") {

} elseif ($etapeChoisi == "choixFiche") {

// Finalisation de la fiche de Frais modification différente selon l'etat de la fiche pendant sa consultation
} elseif ($etapeChoisi == "finaliserFicheFrais") {
    finaliserFichesFrais($idConnexion, $moisChoisi, $visiteurChoisi, $validationChoisi);
}
?>

<!-- Division principale -->
<div id="contenu">
    <h1>Suivi des fiches de frais</h1>
    <?php
    // Selection du visiteur
    ?>
    <form id="formChoixVisiteur" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="choixVisiteur" />
            <label class="title">Choisir le visiteur :</label>
            <select name="lstVisiteur" id="idLstVisiteur" class="zone" onchange="this.form.submit();" >
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
    // Tant qu'un visiteur n'a pas été sélectionné on ne propose pas la sélection des fiches
    if ($visiteurChoisi != "") {
        /* Selection de la fiche en fonction de son mois de création et son état
         * Cas d'utilisation 2
         */
        ?>
        <form id="formChoixFiche" method="post" action="">
            <p>
                <input type="hidden" name="etape" value="choixFiche" />
                <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
                
                <?php
                $req = obtenirReqMoisFicheFraisValidée($visiteurChoisi);
                $idJeuFiches = mysql_query($req, $idConnexion);
                $lgFiche = mysql_fetch_assoc($idJeuFiches);
                /* On vérifie que l'utilisateur possède bien des fiches validée, mise en paiement ou remboursée
                 * dans le cas contraire on le signifie avec un affichage spécifique
                 * Cas d'utilisation 4a
                 */
                if (empty($lgFiche)){
                    ajouterErreur($tabErreurs, "Pas de fiche de frais à mettre en paiement pour ce visiteur");
                    echo toStringErreurs($tabErreurs);
                }else {
                ?>
                <label class="title">Choisir la fiche :</label>
                <select name="lstMois" id="idLstMois" class="zone" onchange="this.form.submit();">
                    <?php
                    if ($moisChoisi == "") { 
                    // Selection mise par défaut pour inviter l'utilisateur à sélectionner une fiche
                    ?>
                    <option value="-1"> Sélectionner un mois </option>
                    <?php
                    }
                    // Boucle pour remplir la sélection
                    while (is_array($lgFiche)) {
                        $mois = $lgFiche["mois"];
                        $noMois = intval(substr($mois, 4, 2));
                        $libelleMois = obtenirLibelleMois($noMois);
                        $annee = intval(substr($mois,0,4));
                        $etatFiche = $lgFiche['etat'];
                        
                        // Si la fiche est validée on la met dans une sous catégorie validée
                        if ($etatFiche == "VA") {
                        ?>    
                        <optgroup label="Validée">
                            <option value="<?php echo $mois;?>"><?php echo $noMois . ' ' . $libelleMois . ' ' . $annee;?></option>
                        </optgroup>
                        <?php    
                        }
                        // Si la fiche est mise en paiement on la met dans une sous catégorie mise en paiement
                        if ($etatFiche == "MP") {
                        ?>    
                        <optgroup label="Mise Paiement">
                            <option value="<?php echo $mois;?>"><?php echo $noMois . ' ' . $libelleMois . ' ' . $annee;?></option>
                        </optgroup>
                        <?php    
                        }
                        $lgFiche = mysql_fetch_assoc($idJeuFiches);
                    }
                    ?>
                </select>
                <?php
                }    
                mysql_free_result($idJeuFiches);
                ?>
                
            </p>
        </form>
    <?php
    }
    /* Le formulaire de gestion des frais n'est visible que si le visiteur et le mois ont été sélectionné
     * Cas d'utilisation 4
     */
    if ($visiteurChoisi != "" && $moisChoisi !== "") {
    // Affichage de la situation de la fiche de frais
    $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi);
    ?>
    <p class="ecritureLargeGras">Situation de la fiche de frais au mois de <?php echo $libelleMois; ?> : 
    <?php echo $tabFicheFrais["libelleEtat"]; ?> </p>
    <h2>Frais au forfait</h2>
    <?php
    // Requète sur les éléments forfaitisés
    $req = obtenirReqEltsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuForfait = mysql_query($req, $idConnexion);
    echo mysql_error();
    $lgEltsForfait = mysql_fetch_array($idJeuForfait);
 
    // Les valeurs sont affectés en fonction des clefs du tableau associatif
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
    ?>
    <div class ="corpsForm">
       <?php
       // Tableau présentant les éléments forfaitisés
       ?>
       <table class="tabForfait">
           <caption>Eléments forfaitisés :</caption>
           <tbody>
               <tr>
                   <td class="alignGauche"> * Forfait Etape : </td>
                   <td> <?php echo $etp; ?> x 110.00 € = </td>
                   <td class="alignDroite"> <?php $etpTotal = $etp * 110.00; echo $etpTotal; ?> € </td> 
                   <?php
                   // TODO : remplacer les valeurs en dur des montant par leurs homologues de la base de donnée
                   ?>
               </tr>
               <tr>
                   <td class="alignGauche"> * Frais Kilométrique (Véhicule 4CV Diesel) : </td>
                   <td> <?php echo $km; ?> x 0.52 € = </td>
                   <td class="alignDroite"> <?php $kmTotal = $km * 0.52; echo $kmTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche"> * Frais Kilométrique (Véhicule 4CV Essence) : </td>
                   <td> <?php echo $km; ?> x 0.62 € = </td>
                   <td class="alignDroite"> <?php $kmTotal = $km * 0.62; echo $kmTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche"> * Frais Kilométrique (Véhicule 5-6CV Diesel) : </td>
                   <td> <?php echo $km; ?> x 0.58 € = </td>
                   <td class="alignDroite"> <?php $kmTotal = $km * 0.58; echo $kmTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche"> * Frais Kilométrique (Véhicule 5-6CV Essence) : </td>
                   <td> <?php echo $km; ?> x 0.67 € = </td>
                   <td class="alignDroite"> <?php $kmTotal = $km * 0.67; echo $kmTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche"> * Nuitée Hôtel : </td>
                   <td> <?php echo $nui; ?> x 80.00 € = </td>
                   <td class="alignDroite"> <?php $nuiTotal = $nui * 80.00; echo $nuiTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche"> * Repas Restaurant : </td>
                   <td> <?php echo $rep; ?> x 25.00 € = </td>
                   <td class="alignDroite"> <?php $repTotal = $rep * 25.00; echo $repTotal; ?> € </td>
               </tr>
               <tr>
                   <td class="alignGauche ecritureLargeGras" colspan="2"> TOTAL DES ELEMENTS FORFAITISES :  </td>
                   <td class="alignDroite ecritureLargeGras"> <?php echo ($etpTotal + $kmTotal + $nuiTotal + $repTotal ); ?> € </td>
               </tr>
           </tbody>
       </table>
    </div>
    <?php
    mysql_free_result($idJeuForfait);
    ?>

    <p class="titre">&nbsp;</p>
    <div style="clear:left;"><h2>Hors forfait</h2></div>
    
    <?php
    // On récupère les lignes hors forfaits
    $req = obtenirReqEltsHorsForfaitFicheFrais($moisChoisi, $visiteurChoisi);
    $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
    $lgEltsHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
    ?>
    <div class ="corpsForm">
        <?php
        // Tableau présentant les éléments hors forfait
        ?>
        <table class="tabHF" >
            <caption>Descriptif des éléments hors forfait : </caption>
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class="Montant">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalHF = ""; // Variable pour le montant total des frais hors forfait cumulé
                while (is_array($lgEltsHorsForfait)) {
                ?>
                <tr>
                    <td class="alignCentre"><?php echo convertirDateAnglaisVersFrancais($lgEltsHorsForfait['date']); ?></td>
                    <td class="alignCentre"><?php echo filtrerChainePourNavig($lgEltsHorsForfait['libelle']); ?></td>
                    <td class="alignDroite"><?php echo $lgEltsHorsForfait['montant']; $totalHF += $lgEltsHorsForfait['montant']; ?> € </td>
                </tr>
                <?php                    
                    $lgEltsHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
                }
                 ?>
                <tr>
                    <td class="alignGauche ecritureLargeGras" colspan="2"> TOTAL DES ELEMENTS HORS FORFAIT :  </td>
                    <td class="alignDroite ecritureLargeGras"><?php echo $totalHF;?>  € </td>
                </tr>
            </tbody>
        </table>
        <?php
        $laFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisChoisi, $visiteurChoisi);
        ?>
        <p> Nombre de Justificatif : <?php echo $laFicheFrais['nbJustificatifs'];?> </p> 
        <?php        
        mysql_free_result($idJeuEltsHorsForfait);
        ?>
    </div>
    <?php
    
    /* Form de validation de la fiche de frais
     * L'input appelle des fonctions diffèrentes en fonction
     * de l'état de la fiche. Il passera la fiche en mise en paiement si elle est
     * validée et en remboursée si elle est mise en paiement.
     * Cas d'utilisation 5 - 5a
     * Cas d'utilisation 6 - 6a
     */
    ?>
    <form id="formFinaliserFicheFrais" method="post" action="">
        <p>
            <input type="hidden" name="etape" value="finaliserFicheFrais" />
            <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurChoisi; ?>" />
            <input type="hidden" name="lstMois" value="<?php echo $moisChoisi; ?>" />
            <?php
            if ($etatFiche == "VA") { // Si la fiche est validée
            ?>
            <input type="hidden" name="validation" value="VA" />
            <input id="inputValiderFiche" class="zone" type="button" onclick="mettreEnPaiementFicheFrais();" 
                value="Mettre En Paiement" />
            <?php
            } else { // Si la fiche est mise en paiement
            ?>
            <input type="hidden" name="validation" value="MP" />
            <input id="inputValiderFiche" class="zone" type="button" onclick="rembourséeFicheFrais();" 
                value="Remboursée" />
            <?php
            }
            ?>
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