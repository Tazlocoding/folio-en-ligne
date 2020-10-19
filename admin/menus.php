<?php
if(isset($_SESSION['id_compte']))
	{
	if(isset($_GET['action']))
		{
		switch($_GET['action'])
			{
			case "afficher_menus":
			$entete="<h1>Gestion des menus</h1>";
			$action_form="afficher_menus";
			//2. on insert les champs dans la table comptes (modele : front.php)
			if(isset($_POST['submit']))
				{	
				if(empty($_POST['intitule_menu']))
					{
					$message="<label class=\"pas_ok\">Mets un intitulé</label>";	
					$color['intitule_menu']="class=\"avertissement\" ";						
					}					
				elseif(empty($_POST['lien_menu']))
					{
					$message="<label class=\"pas_ok\">Mets un lien à ton item</label>";	
					$color['lien_menu']="class=\"avertissement\" ";						
					}	
				elseif(empty($_POST['rang_menu']))
					{
					$message="<label class=\"pas_ok\">Mets un rang</label>";	
					$color['rang_menu']="class=\"avertissement\" ";						
					}
				else{
					$requete="INSERT INTO menus SET intitule_menu='".addslashes($_POST['intitule_menu'])."',
													  lien_menu='".addslashes($_POST['lien_menu'])."',
													  rang_menu='".$_POST['rang_menu']."'";
					$resultat=mysqli_query($connexion,$requete);
					$message="<label class=\"ok\">Nouvel item créé</label>";
					
					//on vide tous les champs du formulaire
					foreach($_POST AS $cle => $valeur)
						{
						unset($_POST[$cle]);	
						}					
					}
				}
			break;
			
			case "modifier_menu":
			
			//si qq valide le formulaire (appui sur le bouton ENVOYER)
			if(isset($_POST['submit']))
				{
				$requete="UPDATE menus SET intitule_menu='".addslashes($_POST['intitule_menu'])."',
										 lien_menu='".addslashes($_POST['lien_menu'])."',
										 rang_menu='".addslashes($_POST['rang_menu'])."' 
										 WHERE id_menu='".$_GET['id_menu']."'";	
				$resultat=mysqli_query($connexion,$requete);
				$message="<label class=\"ok\">L'item a été modifié</label>";
				
				//on se replace sur l'action afficher_comptes
				$action_form="afficher_menus";
				
				//on suprime la variable $_GET['id_menu']
				//afin de ne pas executer le if(isset($_GET['id_menu'])) qui suit
				unset($_GET['id_menu']);
				
				//on vide tous les champs du formulaire
				foreach($_POST AS $cle => $valeur)
					{
					unset($_POST[$cle]);	
					}		
				}
				
			if(isset($_GET['id_menu']))
				{
				$action_form="modifier_menu&id_menu=" . $_GET['id_menu'];
				
				//on récupere dans la table menus les infos du id_menu recu depuis l'url (methode GET)	
				$requete="SELECT * FROM menus WHERE id_menu='".$_GET['id_menu']."'";
				$resultat=mysqli_query($connexion,$requete);
				$ligne=mysqli_fetch_object($resultat);
				
				//on recharge le formulaire d'admin des comptes avec les données stockées dans la table
				$_POST['intitule_menu']=$ligne->intitule_menu;
				$_POST['lien_menu']=$ligne->lien_menu;
				$_POST['rang_menu']=$ligne->rang_menu;
				}			
			
			break;
			
			case "supprimer_menu":
			if(isset($_GET['id_menu']))
				{
				$entete="<h1 class=\"ouinon\">Vous-voulez vraiment supprimer cet item ? 
				<a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=".$_GET['id_menu']."&confirm=1\">OUI</a>
				<a href=\"admin.php?module=menus&action=afficher_menus\">NON</a>
				</h1>";
				//si l'internaute à confirmer la suppression (bouton oui)
				if(isset($_GET['confirm']) && $_GET['confirm']==1)
					{
					$requete2="DELETE FROM menus WHERE id_menu='".$_GET['id_menu']."'";	
					$resultat2=mysqli_query($connexion,$requete2);
					$entete="<h1 class=\"ok\">Item supprimé</h1>";						
					}
				}
			break;		
			}
			
		$requete="SELECT * FROM menus ORDER BY rang_menu";
		$tab_resultats=afficher_menus($connexion,$requete);
		}
	}
else{
	header("Location:../index.php");	
	}		
?>