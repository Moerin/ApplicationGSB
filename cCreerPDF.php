<?php
require("include/_gestionSession.lib.php");
require("include/_bdGestionDonnees.lib.php");
require("include/_utilitairesEtGestionErreurs.lib.php");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
initSession();

require('pdf/fpdf.php');

class PDF extends FPDF
{
    // Fonction qui convertit les caractères latin en en format UTF8 lisible ex: €
    function utf2latin($text) {
        $text=htmlentities($text,ENT_COMPAT,'UTF-8');
        return html_entity_decode($text,ENT_COMPAT,'ISO-8859-1');
    }
    
    // En-tête
    function Header() {
        // Logo
        $this->Image('images/logo.jpg',10 , 15, 30);
    }

    // Pied de page
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        $this->SetFont('Times', 'I', 8);
        // Numérotation des pages
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Entete de la fiche
    function enteteFicheFrais($bdd, $idMois, $idVisiteur) {
        $this->SetTextColor(31, 73, 125);
        $this->AddFont("Calibri",'','calibri.php');
        $this->AddFont("CalibriB",'','calibrib.php');
        $this->SetFont('Times', 'B', 20);
        $this->Ln(10);
        // Titre
        $this->Cell(170, 10, utf8_decode('FICHE DE FRAIS'), 0, 0, 'C');
        // Nom visiteur et date de la fiche
        $idJeuFicheDeFrais = $bdd->query
                ('select nom, prenom from utilisateur join fichefrais on id = idVisiteur 
                    where id="' . $idVisiteur . '" and mois="' . $idMois . '";');
        $lgFicheFrais = $idJeuFicheDeFrais->fetch();
        $idJeuFicheDeFrais->closeCursor();
        $this->Ln(15);
        $this->Cell(95,10,'',0);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 12);
        $this->Cell($this->GetStringWidth("Visiteur") +5 , 7, "Visiteur", 0);
        $this->Cell(30, 7, utf8_decode($lgFicheFrais['prenom']) . " " . strtoupper(utf8_decode($lgFicheFrais['nom'])), 0);
        $this->Cell($this->GetStringWidth("Mois")+ 5, 7, "Mois", 0);
        $noMois = intval(substr($idMois, 4, 2));
        $annee = intval(substr($idMois, 0, 4));
        $this->Cell(40, 7, utf8_decode(obtenirLibelleMois($noMois)) . ' ' . $annee, 0);
        $this->Ln(10);
        $this->Cell(10,10,'',0);

        $idJeuFicheDeFrais = $bdd->query
                ('select montantValide, dateModif, libelle from fichefrais join etat on idEtat = id 
                    where idVisiteur="' . $idVisiteur . '" and mois="' . $idMois . '";');
        $lgEtatFicheFrais = $idJeuFicheDeFrais->fetch();
        $idJeuFicheDeFrais->closeCursor();
        if ($lgEtatFicheFrais['montantValide'] == NULL) { 
            $montantValide = 0.0;
        } else {
            $montantValide = $lgEtatFicheFrais['montantValide'];
        }
        
        $this->SetFillColor(255, 0, 0);
        $this->Cell(170,20,"", 1,"",true);

        $this->SetXY(20, 47);
        $this->Cell(40, 7, "Etat de la fiche : " . $lgEtatFicheFrais['libelle'] ,0);
        $this->SetXY(20, 52);
        $this->Cell(40, 7, utf8_decode('Date de dernière modification : ') . $lgEtatFicheFrais['dateModif'] ,0);
        $this->SetXY(20, 57);
        $this->Cell(40, 7, utf8_decode('Montant validé : ') . $montantValide . iconv("UTF-8", "CP1252", " €"),0);  
    }   
    
    // Section frais forfait
    function tabFraisForfaits($bdd, $idMois, $idVisiteur) {
        // Entêtes de colonnes
        $this->Ln(15);
        $this->Cell(10,0,'',0);
        $this->SetTextColor(31, 73, 125);
        $this->SetFont('Times', 'BI', 12);
        $this->SetFillColor(255, 255, 255);
        $this->Cell(70, 7, 'Frais forfaitaires', 'LTB', 0, 'C', true);
        $this->Cell(20, 7, utf8_decode('Quantité'), 'TB', 0, 'C', true);
        $this->Cell(40, 7, 'Montant unitaire', 'TB', 0, 'C', true);
        $this->Cell(40, 7, 'Total', 'TRB', 0, 'C', true);
        // Données
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 12);
        $idJeuFraisForfait = $bdd->query("select libelle, quantite, montant, (quantite*montant) as total from LigneFraisForfait 
            inner join FraisForfait on (FraisForfait.id = LigneFraisForfait.idFraisForfait) 
            where idVisiteur='" . $idVisiteur . "' and mois='" . $idMois . "' and LigneFraisForfait.idFraisForfait != 'KM'");
        while ($lgFraisForfait = $idJeuFraisForfait->fetch()) {
            $this->Cell(10,0,'',0);
            $this->Cell(70, 7, $lgFraisForfait['libelle'], 1, 0, 'L', true);
            $this->Cell(20, 7, $lgFraisForfait['quantite'], 1, 0, 'R', true);
            $this->Cell(40, 7, $lgFraisForfait['montant'] . iconv("UTF-8", "CP1252", " €"), 1, 0, 'R', true);
            $this->Cell(40, 7, $lgFraisForfait['total'] . iconv("UTF-8", "CP1252", " €"), 1, 0, 'R', true);
            $this->Ln();
        }
        $idJeuFraisForfait->closeCursor();
    }

    // Section hors forfait
    function tabFraisHorsForfaits($bdd, $idMois, $idVisiteur) {
        $this->Ln(5);
        $this->Cell(10,0,'',0);
        $this->SetTextColor(31, 73, 125);
        $this->SetFont('Times', 'BI', 12);
        $this->Cell(170, 10, 'Autres frais', 0, 0, 'C');
        // Entêtes de colonnes
        $this->Ln(10);
        $this->Cell(10);
        $this->SetTextColor(31, 73, 125);
        $this->SetFont('Times', 'BI', 12);
        $this->SetFillColor(255, 255, 255);
        $this->Cell(50, 7, 'Date', 'LTB', 0, 'C', true);
        $this->Cell(80, 7, utf8_decode('Libellé'), 'TB', 0, 'C', true);
        $this->Cell(40, 7, 'Montant', 'TRB', 0, 'C', true);
        // Données
        $this->Ln();
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 12);
        $idJeuFraisHorsForfait = $bdd->query("select id, date, libelle, montant 
            from LigneFraisHorsForfait where idVisiteur='" . $idVisiteur . "' and mois='" . $idMois . "'");
        while ($lgFraisHorsForfait = $idJeuFraisHorsForfait->fetch()) {
            $this->Cell(10);
            $this->Cell(50, 7, convertirDateAnglaisVersFrancais($lgFraisHorsForfait['date']), 1, 0, 'L', true);
            $this->Cell(80, 7, $lgFraisHorsForfait['libelle'], 1, 0, 'L', true);
            $this->Cell(40, 7, $lgFraisHorsForfait['montant'], 1, 0, 'R', true);
            $this->Ln();
        }
        $idJeuFraisHorsForfait->closeCursor();
    }
    
    // Affichage de la somme total des frais
    function afficheTotal($bdd, $idMois, $idVisiteur) {
        $this->Ln();
        $this->Cell(100);
        $idJeuFicheFrais = $bdd->query("select montantValide from ficheFrais 
            where idVisiteur='" . $idVisiteur . "' and mois='" . $idMois . "'");
        $lgFicheFrais = $idJeuFicheFrais->fetch();
        $idJeuFicheFrais->closeCursor();
        $noMois = intval(substr($idMois, 4, 2));
        $annee = intval(substr($idMois, 0, 4));
        $this->Cell(40, 7, 'MONTANT TOTAL ', 1, 0, 'L', true);
        $this->Cell(40, 7, $lgFicheFrais['montantValide'], 1, 0, 'R', true);
    }
    
    // Affichage de la fiche de frais
    function afficheFicheFrais($idMois, $idVisiteur) {
        global $hote, $bd, $login, $mdp;
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Times', '', 12);
        // Connexion à la BDD en PDO
        $dsn = 'mysql:host=localhost;dbname=gsb_frais';
        $user = 'userGsb';
        $password = 'secret';
        try {
            $bdd = new PDO($dsn, $user, $password);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }

        // Affichage de l'entête de la fiche de frais
        $this->enteteFicheFrais($bdd, $idMois, $idVisiteur);
        // Affichage des frais forfaitisés
        $this->tabFraisForfaits($bdd, $idMois, $idVisiteur);
        // Affichage des frais hors forfaits
        $this->tabFraisHorsForfaits($bdd, $idMois, $idVisiteur);
        // Affichage du total
        $this->afficheTotal($bdd, $idMois, $idVisiteur);
    }
}

// Création des variables avec les données provenant de la page cConsutlFicheFrais
// en mode Post
$mois = lireDonneePost("idMois", "");
$visiteur = lireDonneePost("idVisiteur", "");
$fichier = 'pdf/generate/truc.pdf';

// Si la fiche n'existe déja pas on la crée
if (!file_exists($fichier)) {
    $pdf = new PDF();
    $pdf->afficheFicheFrais($mois, $visiteur);
    $pdf->Output();
}

?>