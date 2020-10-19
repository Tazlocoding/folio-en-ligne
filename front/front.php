<?php
session_start();
//je connecte la librairie de fonctions php
require_once("../outils/fonctions.php");
//je stocke dans une variable ($connexion)
//le résultat de la fonction connexion()
$connexion=connexion();

//on calcule un bouton de retour pour revenir au back
//si un utilisateur est connecté au back
if(isset($_SESSION['id_compte']))
	{
	$retour_back="<div id=\"back\"><a href=\"../admin/admin.php\">RETOUR BACK</a></div>";	
	}

$contact="form_contact.html";
//on teste si le bouton "ENVOYER" a été utilisé
if(isset($_POST['submit']))
	{
	//on déclare la variable type tableau associatif
	$message=array();
	$color=array();
	//on teste les champs obligatoires
	if(empty($_POST['nom_contact']))
		{
		$message['nom_contact']="<label class=\"pas_ok\">Mets ton nom</label>";	
		$color['nom_contact']="class=\"avertissement\" ";
		}
	if(empty($_POST['mel_contact']))
		{
		$message['mel_contact']="<label class=\"pas_ok\">Mets ton email</label>";	
		$color['mel_contact']="class=\"avertissement\" ";
		}
	if(empty($_POST['message_contact']))
		{
		$message['message_contact']="<label class=\"pas_ok\">Mets ton message</label>";	
		$color['message_contact']="class=\"avertissement\" ";
		}
	//si tout est bien rempli
	if(!empty($_POST['nom_contact']) && !empty($_POST['mel_contact']) && !empty($_POST['message_contact']))
		{
		//on créé la requete d'insertion des données dans la table contacts
		//addslashes permet l'insertion de caractères spéciaux dans la table
		$requete="INSERT INTO contacts 
					SET nom_contact='".addslashes($_POST['nom_contact'])."',
					prenom_contact='".addslashes($_POST['prenom_contact'])."',
					mel_contact='".$_POST['mel_contact']."',
					message_contact='".addslashes($_POST['message_contact'])."',
					date_contact='".date("Y-m-d H:i:s")."'";
					
		//on execute la requete pour produire le resultat
		$resultat=mysqli_query($connexion,$requete);
		$contact="merci.html";
		}
	}


//on referme la connexion ouverte en ligne 6
mysqli_close($connexion);

include("front.html");
?>