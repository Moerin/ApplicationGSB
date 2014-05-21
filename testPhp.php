<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Reinitialisation de la base de donnée

$repInclude = './include/';
require($repInclude . "_init.inc.php");
$salt = "iklo";

$mdp = crypt("iklo", '$2a$07$'.$salt.'$');
echo "<br \> mdp: " .$mdp;

$motDePasse = $_POST['txtMdp'];
echo "<br \> motDePasse: " .$motDePasse;
$mdp2 = crypt($motDePasse, '$2a$07$'.$salt.'$');
echo "<br \> mdp2: " .$mdp2;

if ($mdp == $mdp2) {
    echo "Good";
    echo "<br \>". $mdp;
} else {
    echo $mdp . '\n';
    echo $mdp2 . '\n';
}

?>
<form id="frmConnexion" action="" method="post">
      <div class="corpsForm">
        <input type="hidden" name="etape" id="etape" value="validerConnexion" />
      <p>
        <label for="txtLogin" accesskey="n">* Login : </label>
        <input type="text" id="txtLogin" name="txtLogin" maxlength="20" size="15" value="" title="Entrez votre login" />
      </p>
      <p>
        <label for="txtMdp" accesskey="m">* Mot de passe : </label>
        <input type="password" id="txtMdp" name="txtMdp" maxlength="8" size="15" value=""  title="Entrez votre mot de passe"/>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input type="submit" id="ok" value="Valider" />
        <input type="reset" id="annuler" value="Effacer" />
      </p> 
      </div>
      </form>
    </div>


