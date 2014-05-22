//<![CDATA[

/*================================FONCTION MESSAGE================================*/

/**
 * Fonction présent au chargement de la page qui permet de mettre les informations
 * en cas changement de visiteur
 * @returns {undefined}
 */
window.onload = function() {
    document.getElementById("idLstVisiteur").onchange = function() {
        document.getElementById('formChoixVisiteur').submit();
    }
} 

/*================================FONCTION MESSAGE================================*/

/**
 * Rend visible les calques concernant la mise à jour d'une valeur
 * des frais forfaitisé
 * @return void
 */
function afficheMsgInfosForfaitAActualisees() {
    document.getElementById('msgFraisForfait').style.display = "block";
    document.getElementById('actionsFraisForfait').style.display = "inline";
    document.getElementById('lkActualiserLigneFraisForfait').style.display = "inline";
    document.getElementById('lkReinitialiserLigneFraisForfait').style.display = "inline";
}

/**
 * Rend visible les calques concernant la mise à jour d'une valeur
 * des frais hors forfait
 * @return void
 */
function afficheMsgInfosHorsForfaitAActualisees(idAMontrer) {
    document.getElementById('msgFraisHorsForfait' + idAMontrer).style.display = "block";
    document.getElementById('lkActualiserLigneFraisHF' + idAMontrer).style.display = "inline";
    document.getElementById('lkReinitialiserLigneFraisHF' + idAMontrer).style.display = "inline";
}

/**
 * Rend visible les calques concernant la mise à jour d'une valeur
 * du nombre de justificatifs
 * @return void
 */
function afficheMsgNbJustificatifs() {
    document.getElementById('msgNbJustificatifs').style.display = "block";
    document.getElementById('lkActualiserNbJustificatifs').style.display = "inline";
    document.getElementById('lkReinitialiserNbJustificatifs').style.display = "inline";
}

/*================================FONCTION FRAIS FORFAIT================================*/

/**
 * Lance l'actualisation d'une ligne au forfait en vérifiant quelles
 * sont les modifications réalisées pour afficher une demande de confirmation
 * @param char(3) rep nombre de repas
 * @param char(3) nui nombre de nuitées
 * @param char(3) etp nombre d'étapes
 * @param char(3) km nombre de kilomètres
 * @return void
 */
function actualiserLigneFraisForfait(rep,nui,etp,km4d,km4e,km56d,km56e) {
    // Trouver quelles sont les mises à jour à réaliser
    var modif = false;
    var txtModifs = '';
    if (rep != document.getElementById('idREP').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de repas : ' + rep + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idREP').value;
    }
    if (nui != document.getElementById('idNUI').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de nuitées : ' + nui + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idNUI').value;
    }
    if (etp != document.getElementById('idETP').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité d\'étapes : ' + etp + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idETP').value;
    }
    if (km4d != document.getElementById('idKM4d').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de kilomètres : ' + km4d + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idKM4d').value;
    }
    if (km4e != document.getElementById('idKM4e').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de kilomètres : ' + km4e + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idKM4e').value;
    }
    if (km56d != document.getElementById('idKM56d').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de kilomètres : ' + km56d + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idKM56d').value;
    }
    if (km56e != document.getElementById('idKM56e').value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne quantité de kilomètres : ' + km56e + ' \n \'--> Nouvelle quantité : ' + document.getElementById('idKM56e').value;
    }
    if (modif) {
        var question = 'Souhaitez-vous vraiment effectuer la ou les modifications suivantes cette ligne de frais forfaitisés ?' + txtModifs;
        if (confirm(question)) {
            document.getElementById('formFraisForfait').submit();
        }
    } else {
        alert('Aucune modification à actualiser...');
        reinitialiserLigneFraisForfait();
    }
}

/**
 * Réinitialise les valeurs des frais au forfait
 * @return void
 */
function reinitialiserLigneFraisForfait() {
    document.getElementById('formFraisForfait').reset();
//    document.getElementById('msgFraisForfait').style.display = "none";
//    document.getElementById('actionsFraisForfait').style.display = "none";        
}

/*================================FONCTION FRAIS HORS FORFAIT================================*/

/**
 * Lance l'actualisation d'une ligne hors forfait en vérifiant quelles
 * sont les modifications réalisées pour afficher une demande de confirmation
 * @param int idElementHF identidiant d'une ligne hors forfait
 * @param date dateElementHF date d'une ligne hors forfait
 * @param varchar(100) libelleElementHF libellé d'une ligne hors forfait
 * @param decimal(10,2) montantElementHF montant d'une ligne hors forfait
 * @return void
 */
function actualiserLigneFraisHorsForfait(idElementHF,dateElementHF,libelleElementHF,montantElementHF) {
    // Trouver quelles sont les mises à jour à réaliser
    var modif = false;
    var txtModifs = '';
    if (dateElementHF != document.getElementById('idDate' + idElementHF).value) {
        // Modification portant sur la date
        modif = true;
        txtModifs += '\n\nAncienne date : "' + dateElementHF + '" \n \'--> Nouvelle date : "' + document.getElementById('idDate' + idElementHF).value + '"';
    }
    if (libelleElementHF != document.getElementById('idLibelle' + idElementHF).value) {
        // Modification portant sur le libellé
        modif = true;
        txtModifs += '\n\nAncien libellé : "' + libelleElementHF + '" \n \'--> Nouveau libellé : ' + document.getElementById('idLibelle' + idElementHF).value + '"';
    }
    if (montantElementHF != document.getElementById('idMontant' + idElementHF).value) {
        // Modification portant sur le montant
        modif = true;
        txtModifs += '\n\nAncien montant : ' + montantElementHF + '\u20AC \n \'--> Nouveau montant : ' + document.getElementById('idMontant' + idElementHF).value + '\u20AC';
    }
    // Demande de confirmation s'il y a des modifications à réellement actualiser
    if (modif) {
        var question = 'Souhaitez-vous vraiment effectuer la ou les modifications suivantes cette ligne de frais hors forfait ?' + txtModifs;
        if (confirm(question)) {
            document.getElementById('formFraisHorsForfait' + idElementHF).submit();
        }
    } else {
        alert('Aucune modification à actualiser...');
        reinitialiserLigneFraisHorsForfait(idElementHF);
    }
}

/**
 * Réinitialise les valeurs des frais hors forfait
 * @param int idElementHF identifiant de ligne hors forfait
 * @return void
 */
function reinitialiserLigneFraisHorsForfait(idElementHF) { // TODO: Modifier la fonctionnalite de renitialisation du libelle c'est une mauvaise idée
    document.getElementById('formFraisHorsForfait' + idElementHF).reset();
    document.getElementById('idLibelle' + idElementHF).value = document.getElementById('idLibelle' + idElementHF).value.replace('REFUSÉ : ',"");
    document.getElementById('formFraisHorsForfait' + idElementHF).submit;
    
    //document.getElementById('msgFraisHorsForfait' + idElementHF).style.display = "none"; 
    //document.getElementById('lkActualiserLigneFraisHF' + idElementHF).style.display = "none";
    //document.getElementById('lkReinitialiserLigneFraisHF' + idElementHF).style.display = "none";
}

/**
 * Lance le refus d'une ligne de frais hors forfait en ajoutant
 * le texte "REFUSÉ" en début de libellé
 * @param int idElementHF identidiant d'une ligne hors forfait
 * @return void
 */
function refuseLigneFraisHorsForfait(idElementHF) {
    var strTest = 'REFUSÉ : ';
    var strTestRegExp = new RegExp(strTest);
    var message = "";
    // on test avec des expressions régulière la présence de la chaine 'REFUSÉ : '
    if (strTestRegExp.test(document.getElementById('idLibelle' + idElementHF).value)) {
        alert("La ligne est déja refusé");
    } else {    
        message = 'Souhaitez-vous vraiment supprimer la ligne de frais hors forfait du ' + document.getElementById('idDate' + idElementHF).value ;
        question += ' portant le libellé "' + document.getElementById('idLibelle' + idElementHF).value + '"';
        question += ' pour un montant de ' + document.getElementById('idMontant' + idElementHF).value + '\u20AC ?';
        if (confirm(message)) {
            // On ajoute en début de libelle le texte "REFUSÉ : "
            document.getElementById('idLibelle' + idElementHF).value = 'REFUSÉ : ' + document.getElementById('idLibelle' + idElementHF).value;
            document.getElementById('formFraisHorsForfait' + idElementHF).submit();
        }
    }
}

/**
 * Lance le report d'une ligne de frais hors forfait au mois suivant
 * @param int idElementHF identidiant d'une ligne hors forfait
 * @return void
 */
function reporterLigneFraisHorsForfait(idElementHF) {
    var question = 'Souhaitez-vous vraiment reporter la ligne de frais hors forfait du ' + document.getElementById('idDate' + idElementHF).value ;
    question += ' portant le libellé "' + document.getElementById('idLibelle' + idElementHF).value + '"';
    question += ' pour un montant de ' + document.getElementById('idMontant' + idElementHF).value + '\u20AC ?';
    if (confirm(question)) {
        // On passe par l'étape "reporterLigneFraisHF"
        document.getElementById('idEtape' + idElementHF).value = 'reporterLigneFraisHF';
        document.getElementById('formFraisHorsForfait' + idElementHF).submit();
    }
}

/**
 * Lance la réintégration d'une ligne de frais hors forfait en retirant
 * le texte "REFUSÉ" en début de libellé
 * @param int idElementHF identidiant d'une ligne hors forfait
 * @return void
 */
function reintegrerLigneFraisHorsForfait(idElementHF) {
    var question = 'Souhaitez-vous vraiment réintégrer la ligne de frais hors forfait du ' + document.getElementById('idDate' + idElementHF).value ;
    question += ' portant le libellé "' + document.getElementById('idLibelle' + idElementHF).value.replace('REFUSÉ : ',"") + '"';
    question += ' pour un montant de ' + document.getElementById('idMontant' + idElementHF).value + '\u20AC ?';
    if (confirm(question)) {
        // On retire en début de libelle le texte "REFUSÉ : "
        document.getElementById('idLibelle' + idElementHF).value = document.getElementById('idLibelle' + idElementHF).value.replace('REFUSÉ : ',"");
        document.getElementById('formFraisHorsForfait' + idElementHF).submit();
    }
}

/*================================FONCTION JUSTIFICATIF================================*/

/**
 * Lance l'actualisation du nombre de justificatifs
 * @param int nbJustificatifs nombre de justificatifs
 * @return void
 */
function actualiserNbJustificatifs(nbJustificatifs) {
    if (nbJustificatifs != document.getElementById('idNbJustificatifs').value) {
        if (confirm('Souhaitez-vous vraiment passer le nombre de justificatifs de ' + nbJustificatifs + ' à ' + document.getElementById('idNbJustificatifs').value + ' ?')) {
        document.getElementById('formNbJustificatifs').submit();
        }
    } else {
        alert('Aucune modification à actualiser...');
        reinitialiserNbJustificatifs();
    }
}

/**
 * Réinitialise le nombre de justificatifs
 * @return void
 */
function reinitialiserNbJustificatifs() {
    document.getElementById('formNbJustificatifs').reset();
    document.getElementById('msgNbJustificatifs').style.display = "none"; 
//    document.getElementById('lkActualiserNbJustificatifs').style.display = "none"; // mettre en mouse over
//    document.getElementById('lkReinitialiserNbJustificatifs').style.display = "none";
}

/*================================FONCTION VALIDATION PAGE================================*/

/**
 * Lance la validation d'une fiche de frais en vérifiant si le nombre
 * de justificatifs est au moins égal au nombre de ligne hors forfait
 * et en proposant une confirmation de la validation
 * @return void
 */
function validerFiche() {
    var nbRefus = 0;
    var nbValid = 0;
    var forms = document.getElementsByTagName('form');
    for (var cpt = 0; cpt < forms.length; cpt++) {
        var unForm = forms[cpt];
        if (unForm.id) {
            if(unForm.id.search('formFraisHorsForfait') != -1) {
                if(document.getElementById('idLibelle'+ unForm.id.replace('formFraisHorsForfait',"")).value.search('REFUSÉ : ') != -1) {
                    nbRefus++;
                } else {            
                    nbValid++;
                }
            }
        }   
    }
    // Vérification supplémentaire sur le nombre de justificatifs, qui au minimum doit être égal au nombre de ligne de frais validées
    if ((nbValid) > document.getElementById('idNbJustificatifs').value) {
        alert('Attention, le nombre de justificatifs devrait être au minimum égal au nombre de ligne validées...');
    }
    else {
        var synthese = '\n\n Détails de la validation :';
        synthese += '\n - Refus : ' + nbRefus;
        synthese += '\n - Validation : ' + nbValid;
        if(getModifsEnCours()) {
            if(confirm('Attention, des modifications n\'ont pas été actualisées. Souhaitez-vous vraiment valider cette fiche et perdre toutes les modifications non actualisées ?')) {
                if(confirm('Une fois validée, cette fiche n\'apparaîtra plus dans les fiches à valider et vous ne pourrez plus la modifier. Souhaitez-vous valider tout de même cette fiche ?' + synthese)) {
                    document.getElementById('formValidFiche').submit();
                }
            }
        } else {
            if(confirm('Une fois validée, cette fiche n\'apparaîtra plus dans les fiches à valider et vous ne pourrez plus la modifier. Souhaitez-vous valider tout de même cette fiche ?' + synthese)) {
                document.getElementById('formValidFiche').submit();
            }
        }
    }
}

/*
 * Notifie à l'utilisateur la validation de la fiche en état de mise en paiement
 * @returns void
 */
function mettreEnPaiementFicheFrais() {
    if (confirm('Souhaitez-vous vraiment marquer cette fiche comme mise en paiement ?')) {
            document.getElementById('formFinaliserFicheFrais').submit();
    }
}

/*
 * Notifie à l'utilisateur la validation de la fiche en état remboursée
 * @returns void
 */
function rembourséeFicheFrais() {
    if (confirm('Souhaitez-vous vraiment marquer cette fiche comme remboursée ?')) {
            document.getElementById('formFinaliserFicheFrais').submit();
    }
} 

/*================================FONCTION OUTIL================================*/

/**
 * Réinitialise la page afin de changer de visiteur
 * @param char(4) idVisiteur identifiant de visiteur
 * @return void
 */
function changerVisiteur(idVisiteur) {
    if(getModifsEnCours()) {
        if(confirm('Attention, des modifications n\'ont pas été actualisées. Souhaitez-vous vraiment changer de visiteur et perdre toutes les modifications non actualisées ?')) {
            if(!idVisiteur) {
                // C'est le bouton "Changer de visiteur" qui a été utilisé
                // On recharge la page comme si on avait cliqué dans le sommaire
                window.location = "./cValidFichesFrais.php";
            } else {
                // On change de visiteur avec le visiteur choisi
                document.getElementById('formChoixVisiteur').submit();
            }
        }
    } else {
        if(!idVisiteur) {
            alert("Pouet");
            // C'est le bouton "Changer de visiteur" qui a été utilisé
            // On recharge la page comme si on avait cliqué dans le sommaire
            window.location = "./cValidFichesFrais.php";
        } else {
            // On change de visiteur avec le visiteur choisi
            document.getElementById('formChoixVisiteur').submit();
        }
    }
}

/**
 * Vérifie sur une modification est en cours
 * Si c'est le cas, la valeur vraie est renvoyée
 * @return boolean modif
 */
function getModifsEnCours() {
    var modif = false;
    // Si cet élément existe, c'est que l'on a bien dépassé le stade du choix de visiteur
    if(document.getElementById('msgFraisForfait')) {
        // Modification en cours sur les frais forfaitisés ?
        if(document.getElementById('msgFraisForfait').style.display == "block") {
            modif = true;
            return modif;
        }
        // Modification en cours sur les frais hors forfaits ?
        var forms = document.getElementsByTagName('form');
        for (var cpt = 0; cpt < forms.length; cpt++) {
            var unForm = forms[cpt];
            if (unForm.id) {
                if(unForm.id.search('formFraisHorsForfait') != -1) {
                    if(document.getElementById('msgFraisHorsForfait' + unForm.id.replace('formFraisHorsForfait',"")).style.display == "block") {
                        modif = true;
                        return modif;
                    }
                }
            }   
        }
        // Modification en cours sur le nombre de justificatifs ?
        if(document.getElementById('msgNbJustificatifs').style.display == "block") {
            modif = true;
            return modif;
        }
    }
    return modif
}

//]]>
