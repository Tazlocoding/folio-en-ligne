<?php
//Je connecte la librairie de fonction.php
require_once("../outils/fonctions.php");
//Je stocke dans une variable ($connexion) le résultat de la fonction connexion()
$connexion = connexion();
$contact = "form_contact.html";

//on teste si le bouton "ENVOYER" a été utilisé

if(isset($_POST['submit'])) {
  //on déclare la variable type au tableau associatif
  $message = array();
  $color = array();
  //on teste les champs obligatoires
  if (empty($_POST['nom_contact'])) 
  {
    $message['nom_contact'] = "<label class=\"pas_ok\">Mettre son nom</label>";
    $color['nom_contact'] = "class=\"avertissement\" ";
  }
  if (empty($_POST['prenom_contact'])) 
  {
    $message['prenom_contact'] = "<label class=\"pas_ok\">Mettre son prenom</label>";
  }
  if (empty($_POST['mel_contact'])) 
  {
    $message['mel_contact'] = "<label class=\"pas_ok\">Mettre son email</label>";
    $color['mel_contact'] = "class=\"avertissement\" ";
  }
  if (empty($_POST['message_contact'])) 
  {
    $message['message_contact'] = "<label class=\"pas_ok\">Mettre un message</label>";
    $color['message_contact'] = "class=\"avertissement\" ";
    //si tout est rempli
  }
  if (!empty($_POST['nom_contact']) && !empty($_POST['mel_contact']) && !empty($_POST['message_contact']))
  {
    //on crée la requete d'insertion des données dans la table contacts
    //addslashes permet l'insertion de caractéres spéciaux dans la table
    $requete = "INSERT INTO contacts
                SET nom_contact = '".addlslashes($_POST['nom_contact'])."',
                SET prenom_contact = '".addlslashes($_POST['prenom_contact'])."',
                SET mel_contact = '".$_POST['mel_contact']."',
                SET message_contact = '".addlslashes($_POST['message_contact'])."',
                SET date_contact = '".date("Y-m-d H:i:s")."'";
    //on execute la requete pour produire le resultat
    $resultat = mysqli_query($connexion, $requete);
    $contact = "merci.html";
  }

}
//on referme la connexion ouverte en ligne 5
mysqli_close($connexion);

include("front.html");