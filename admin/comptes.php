<?php
if(isset($_SESSION['id_compte']))
	{
	if(isset($_GET['action']))
		{
		switch($_GET['action'])
			{
			case "afficher_comptes":
			$entete="<h1>Gestion des comptes</h1>";
			$action_form="afficher_comptes";
			//2. on insert les champs dans la table comptes (modele : front.php)
			if(isset($_POST['submit']))
				{
				//on gere la liste déroulante des statuts
				if(!empty($_POST['statut_compte']))
				  {
				  $selected[$_POST['statut_compte']]= "selected=\"selected\"";
				  }		
				if(empty($_POST['nom_compte']))
					{
					$message="<label class=\"pas_ok\">Mets ton nom</label>";	
					$color['nom_compte']="class=\"avertissement\" ";						
					}					
				elseif(empty($_POST['prenom_compte']))
					{
					$message="<label class=\"pas_ok\">Mets ton prénom</label>";	
					$color['prenom_compte']="class=\"avertissement\" ";						
					}	
				elseif(empty($_POST['login_compte']))
					{
					$message="<label class=\"pas_ok\">Mets ton login</label>";	
					$color['login_compte']="class=\"avertissement\" ";						
					}
				elseif(empty($_POST['statut_compte']))
					{
					$message="<label class=\"pas_ok\">Mets ton statut</label>";	
					$color['statut_compte']="class=\"avertissement\" ";						
					}					
				elseif(empty($_POST['pass_compte']))
					{
					$message="<label class=\"pas_ok\">Mets ton pass</label>";	
					$color['pass_compte']="class=\"avertissement\" ";						
					}
				else{
					$requete="INSERT INTO comptes SET nom_compte='".addslashes($_POST['nom_compte'])."',
													  prenom_compte='".addslashes($_POST['prenom_compte'])."',
													  login_compte='".addslashes($_POST['login_compte'])."',
													  statut_compte='".$_POST['statut_compte']."',
													  pass_compte=SHA1('".$_POST['pass_compte']."')";
					$resultat=mysqli_query($connexion,$requete);
					$message="<label class=\"ok\">Nouveau compte créé</label>";
					
					//on vide tous les champs du formulaire
					foreach($_POST AS $cle => $valeur)
						{
						unset($_POST[$cle]);	
						}					
					}	
				}
			break;
			
			case "modifier_compte":
			
			//si qq valide le formulaire (appui sur le bouton ENVOYER)
			if(isset($_POST['submit']))
				{
				$requete="UPDATE comptes SET nom_compte='".addslashes($_POST['nom_compte'])."',
										 prenom_compte='".addslashes($_POST['prenom_compte'])."',
										 login_compte='".addslashes($_POST['login_compte'])."',
										 statut_compte='".$_POST['statut_compte']."'";
			
				//si le champ pass_compte est rempli
				if(!empty($_POST['pass_compte']))
					{
					$requete.=",pass_compte=SHA1('".$_POST['pass_compte']."')";				
					}
				$requete.=" WHERE id_compte='".$_GET['id_compte']."'";	
				$resultat=mysqli_query($connexion,$requete);
				$message="<label class=\"ok\">Le compte a été modifié</label>";
				
				//on se replace sur l'action afficher_comptes
				$action_form="afficher_comptes";
				
				//on suprime la variable $_GET['id_compte']
				//afin de ne pas executer le if(isset($_GET['id_compte'])) qui suit
				unset($_GET['id_compte']);
				
				//on vide tous les champs du formulaire
				foreach($_POST AS $cle => $valeur)
					{
					unset($_POST[$cle]);	
					}		
				}
				
			if(isset($_GET['id_compte']))
				{
				$action_form="modifier_compte&id_compte=" . $_GET['id_compte'];
				
				//on récupere dans la table comptes les infos du id_compte recu depuis l'url (methode GET)	
				$requete="SELECT * FROM comptes WHERE id_compte='".$_GET['id_compte']."'";
				$resultat=mysqli_query($connexion,$requete);
				$ligne=mysqli_fetch_object($resultat);
				
				//on recharge le formulaire d'admin des comptes avec les données stockées dans la table
				$_POST['nom_compte']=$ligne->nom_compte;
				$_POST['prenom_compte']=$ligne->prenom_compte;
				$_POST['login_compte']=$ligne->login_compte;
				
				//pour recharger une liste déroulante
				$selected[$ligne->statut_compte]= "selected=\"selected\"";
				}			
			
			break;
			
			case "supprimer_compte":
			if(isset($_GET['id_compte']))
				{
				$entete="<h1 class=\"ouinon\">Vous-voulez vraiment supprimer ce compte ? 
				<a href=\"admin.php?module=comptes&action=supprimer_compte&statut_compte=".$_GET['statut_compte']."&id_compte=".$_GET['id_compte']."&confirm=1\">OUI</a>
				<a href=\"admin.php?module=comptes&action=afficher_comptes\">NON</a>
				</h1>";
				//si l'internaute à confirmer la suppression (bouton oui)
				if(isset($_GET['confirm']) && $_GET['confirm']==1)
					{
					//on vérifie que ce n'est pas le dernier statut admin	
					$requete="SELECT * FROM comptes WHERE statut_compte='admin'";
					$resultat=mysqli_query($connexion,$requete);
					$nb=mysqli_num_rows($resultat);
					
					if($nb==1 && $_GET['statut_compte']=="admin")
						{
						$entete="<h1 class=\"pas_ok\">Impossible ! Il faut au moins un compte admin</h1>";	
						}
					else{
						$requete2="DELETE FROM comptes WHERE id_compte='".$_GET['id_compte']."'";	
						$resultat2=mysqli_query($connexion,$requete2);
						$entete="<h1 class=\"ok\">Compte supprimé</h1>";						
						}
					}
				}
			break;		
			}
			
		$requete="SELECT * FROM comptes ORDER BY id_compte DESC";
		$tab_resultats=afficher_comptes($connexion,$requete);
		}
	}
else{
	header("Location:../index.php");	
	}		
?>