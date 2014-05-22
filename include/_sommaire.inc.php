<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    <?php
    // Si l'utilisateur est un visiteur on affiche ses informations
      if ( estUtilisateurConnecte() ) {
          $idUser = obtenirIdUserConnecte() ;
          $lgUser = obtenirDetailUtilisateur($idConnexion, $idUser);
          $nom = $lgUser['nom'];
          $prenom = $lgUser['prenom'];
          $fonction = $lgUser['libelleFonction'];
    ?>
        <h2>
    <?php  
            echo $nom . " " . $prenom ;
    ?>
        </h2>
        <h3><?php echo $fonction; ?></h3>
        <?php
      }
        ?>
      </div>  
<?php      
  if ( estUtilisateurConnecte() ) {
?>
        <ul id="menuList">
           <li class="smenu">
              <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
           </li>
           <li class="smenu">
              <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
           </li>
           <?php
           if ($fonction == "Visiteur") { // si c'est un visiteur on met l'affichage correspondant
           ?> 
           <li class="smenu">
              <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
        </ul>
        <?php
                // affichage des éventuelles erreurs déjà détectées
                if ( nbErreurs($tabErreurs) > 0 ) {
                    echo toStringErreurs($tabErreurs) ;   
                }
                
          } else if ( $fonction == "Comptable" ) { // si c'est un comptable on met l'affichage correspondant
        ?>
           <li class="smenu">
              <a href="cValidFichesFrais.php" title="Saisie fiche de frais du mois courant">Valider fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cSuiviePaiementFichesFrais.php" title="Consultation de mes fiches de frais">Suivre paiement fiches de frais</a>
           </li>
         </ul>
        <?php
                // affichage des éventuelles erreurs déjà détectées
                if ( nbErreurs($tabErreurs) > 0 ) {
                    echo toStringErreurs($tabErreurs) ;
                }
        }
  }
        ?>
    </div>