<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Saisir fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
      
  // page inaccessible si comptable non connecté
  if (!estComptableConnecte()) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  // acquisition du mois pour verification de la presence d'une fiche de frais
  $moisValid = lireDonneePost("txtMoisValid", "");
  $idVisiteur = lireDonnee("lstVisiteur", "");
  $etape = lireDonnee("etape", "");
  
  if ($etape == "validerSaisie") { // l'utilisateur valide la fiche de frais
     $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisValid, $idVisiteur);
     
     if ( !$detailFicheFrais ) { // verifi si l'utilisateur possede une fiche a cette date
        ajouterErreur($tabErreurs, "Pas de fiche de frais pour l'utilisateur ce mois"); 
     }
  }
?>
    
<!-- Division principale -->
<div id="contenu">
    <h2>Validation des Frais </h2>
    <form name="formValidFrais" method="post" action="">
        <div class="corpsForm">
            <input type="hidden" name="etape" value="validerSaisie" />
            <fieldset>
                <legend>Validation des frais par visiteur</legend>
                <label class="titre">Choisir le visiteur :</label>
                <select name="lstVisiteur" title="Sélectionner le visiteur">
                    <?php
                        //On recupère la liste des visiteurs trie par ordre alphalbetiaque
                        $req = obtenirReqVisiteurFicheFrais();
                        $idJeuRes = mysql_query($req, $idConnexion);
                        $lgVisiteur = mysql_fetch_assoc($idJeuRes);
                        while ( is_array($lgVisiteur) ) {
                            $idVisiteur = $lgVisiteur["id"];
                            $nomVisiteur = $lgVisiteur["nom"];
                    ?>
                    <option value="<?php echo $idVisiteur; ?>" ><?php echo $nomVisiteur; ?></option>
                    <?php
                            $lgVisiteur = mysql_fetch_assoc($idJeuRes);
                        }
                        mysql_free_result($idJeuRes);
                    ?>
                </select>
                <label class="titre">Mois :</label> <input class="zone" type="txtMois" name="txtMoisValid" size="12" />
                <!--<select name="lstMois" title="Sélectionner le Mois">
                    <option>Janvier</option>
                    <option>Fevrier</option>
                    <option>Mars</option>
                    <option>Avril</option>
                    <option>Mai</option>
                    <option>Juin</option>
                    <option>Juillet</option>
                    <option>Aout</option>
                    <option>Septembre</option>
                    <option>Octobre</option>
                    <option>Novembre</option>
                    <option>Decembre</option>
                </select>-->
            </fieldset> 
            <input id="ok" type="submit" value="Valider" size="20"
            title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
        </div>
        <div name="droite" style="float:left;width:80%;">
            <div name="bas" style="margin : 10 2 2 2;clear:left;background-color:EE8844;color:white;height:88%;">
                
                    <p class="titre" />
                    <div style="clear:left;"><h2>Frais au forfait </h2></div>
                    <table style="color:white;" border="1">
                        <tr><th>Repas midi</th><th>Nuitée </th><th>Etape</th><th>Km </th><th>Situation</th></tr>
                        <tr align="center"><td width="80" >
                             <?php
                                //On recupère la liste des visiteurs trie par ordre alphalbetiaque
                                $req = obtenirReqEltsForfaitFicheFrais($moisValid, $idVisiteur);
                                $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
                                $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
                                while ( is_array($lgEltForfait) ) {
                                    $idFraisForfait = $lgEltForfait["idFraisForfait"];
                                    $quantiteFraisForfait = $lgEltForfait["quantite"];
                                }
                            ?>    
                            <input type="text" size="3" name="repas"/></td>
                            <td width="80"><input type="text" size="3" name="nuitee" value="
                                <?php/* if ($idFraisForfait == "NUI") { 
                                    echo $quantiteFraisForfait 
                                            
                                } */?>" /></td> 
                            <td width="80"> <input type="text" size="3" name="etape"/></td>
                            <td width="80"> <input type="text" size="3" name="km" /></td>
                            <td width="80"> 
                                <select size="3" name="situ">
                                    <option value="E">Enregistré</option>
                                    <option value="V">Validé</option>
                                    <option value="R">Remboursé</option>
                                </select></td>
                        </tr>
                    </table>
                        
                    <p class="titre" /><div style="clear:left;"><h2>Hors Forfait</h2></div>
                    <table style="color:white;" border="1">
                        <tr><th>Date</th><th>Libellé </th><th>Montant</th><th>Situation</th></tr>
                        <tr align="center"><td width="100" ><input type="text" size="12" name="hfDate1"/></td>
                            <td width="220"><input type="text" size="30" name="hfLib1"/></td> 
                            <td width="90"> <input type="text" size="10" name="hfMont1"/></td>
                            <td width="80"> 
                                <select size="3" name="hfSitu1">
                                    <option value="E">Enregistré</option>
                                    <option value="V">Validé</option>
                                    <option value="R">Remboursé</option>
                                </select></td>
                        </tr>
                    </table>		
                    <p class="titre"></p>
                    <div class="titre">Nb Justificatifs</div><input type="text" class="zone" size="4" name="hcMontant" />		
                    <p class="titre" /><label class="titre">&nbsp;</label><input class="zone"type="reset" /><input class="zone"type="submit" />
                </form>
            </div>
        </div>
    </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>