<?php
//===============================
// la fonction connecter() permet de choisir une
// base de données et de s'y connecter.

function connexion()
	{
	require_once("connect.php");
	//si numéro de port
	//$connexion = mysqli_connect(SERVEUR,LOGIN,PASSE,BASE,PORT) or die("Error " . mysqli_error($connexion));
	//si pas de numéro de port	
	$connexion = mysqli_connect(SERVEUR,LOGIN,PASSE,BASE) or die("Error " . mysqli_error($connexion));
	
	return $connexion;
	}


//================================================
function protocole()
	{
	if(isset($_SERVER['HTTPS']))
		{
		$protocole="https://";	
		}
	else
		{
		$protocole="http://";	
		}
	//$protocole="http://";
	return $protocole;	
	}
	
//================================
function security($chaine){
	$connexion=connexion();
	$security=addcslashes(mysqli_real_escape_string($connexion,$chaine), "%_");
	mysqli_close($connexion);
	return $security;
}

//===========================pour se loguer=======================================================
function login($login,$password)
{	
	$connexion=connexion();
	$login=security($login);
	$password=security($password);

	$requete="SELECT * FROM comptes WHERE login_compte= '" . $login . "' AND pass_compte=SHA1('" . $password . "')";
	$resultat=mysqli_query($connexion, $requete);
	$nb=mysqli_num_rows($resultat);
	
	if($nb==0)
		{
		return false;
		}
	else
		{ 
		$ligne=mysqli_fetch_object($resultat);
		
		//on stocke en mémoire de session les infos que l'on souhaite afficher sur l'accueil du back
		$_SESSION['id_compte']=$ligne->id_compte;
		$_SESSION['prenom_compte']=$ligne->prenom_compte;    
		$_SESSION['nom_compte']=$ligne->nom_compte;
		$_SESSION['statut_compte']=$ligne->statut_compte;		
		if(!empty($ligne->fichier_compte))
			{
			$_SESSION['fichier_compte']="<img src=\"" . $ligne->fichier_compte . "\" alt=\"\" />";
			}
		header("Location:../admin/admin.php");    
		return true;
		}		
	mysqli_close($connexion); 	
}


// ====détecter l'extension du fichier================
function fichier_type($uploadedFile)
{
$tabType = explode(".", $uploadedFile);
$nb=sizeof($tabType)-1;
$typeFichier=$tabType[$nb];
 if($typeFichier == "jpeg")
   {
   $typeFichier = "jpg";
   }
$extension=strtolower($typeFichier);
return $extension;
}


//============================================
function redimage($img_src,$img_dest,$dst_w,$dst_h,$quality)
{
if(!isset($quality))
	{
	$quality=100;
	}
   $extension=fichier_type($img_src);

   // Lit les dimensions de l'image
   $size = @GetImageSize($img_src);
   $src_w = $size[0];
   $src_h = $size[1];
   // Crée une image vierge aux bonnes dimensions   truecolor
   $dst_im = @ImageCreatetruecolor($dst_w,$dst_h);
   imagealphablending($dst_im, false);
   imagesavealpha($dst_im, true);      
    
   // Copie dedans l'image initiale redimensionnée  
   
   if($extension=="jpg")
     {
     $src_im = @ImageCreateFromJpeg($img_src);
     imagecopyresampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
    
     // Sauve la nouvelle image
     @ImageJpeg($dst_im,$img_dest,$quality);     
     }
   if($extension=="png")
     {
     $src_im = @ImageCreateFromPng($img_src);    
     imagecopyresampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);     
     
     // Sauve la nouvelle image
     @ImagePng($dst_im,$img_dest,0);     
     }     
   if($extension=="gif")
     {
     $src_im = @ImageCreateFromGif($img_src);
     imagecopyresampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
     
     // Sauve la nouvelle image
     @ImagePng($dst_im,$img_dest,0);     
     }

   // Détruis les tampons
   @ImageDestroy($dst_im);
   @ImageDestroy($src_im);
}

//===============================
function format_date($date,$format)
{
if($format=="anglais")
   {
	$tab_date=explode("/",$date);
	$date_au_format=$tab_date[2] . "-" . $tab_date[1] . "-" . $tab_date[0];	
	 }
if($format=="francais")
   {
	$tab_date=explode("-",$date);
	$date_au_format=$tab_date[2] . "/" . $tab_date[1] . "/" . $tab_date[0];	
	 }
return $date_au_format;	
}

//===============================================

 function envoi_mel($destinataire,$sujet,$message_txt, $message_html,$expediteur)
  {
  if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $destinataire)) // On filtre les serveurs qui rencontrent des bogues.
    {
  	$passage_ligne = "\r\n";
    }
  else
    {
  	$passage_ligne = "\n";
    }
   
  //=====Création de la boundary
  $boundary = "-----=" . md5(rand());
  //==========
   
  //=====Création du header de l'email
  $header = "From: \"" . $_SESSION['expediteur'] . "\"<" . $expediteur . ">" . $passage_ligne;
  $header.= "Reply-to: \"" . $_SESSION['expediteur'] . "\" <" . $expediteur . ">" . $passage_ligne;
  $header.= "MIME-Version: 1.0" . $passage_ligne;
  $header.= "X-Priority: 3" . $passage_ligne;//1 : max et 5 : min
  $header.= "Content-Type: multipart/alternative;" . $passage_ligne . " boundary=\"" . $boundary . "\"" . $passage_ligne;
  //==========
   
  //=====Création du message
  $message = $passage_ligne . "--" . $boundary. $passage_ligne;
  //=====Ajout du message au format texte
  $message.= "Content-Type: text/plain; charset=\"UTF-8\"" . $passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
  $message.= $passage_ligne . $message_txt . $passage_ligne;
  //==========
  $message.= $passage_ligne . "--" . $boundary . $passage_ligne;
  //=====Ajout du message au format HTML
  $message.= "Content-Type: text/html; charset=\"UTF-8\"" . $passage_ligne;
  $message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
  $message.= $passage_ligne . $message_html . $passage_ligne;
  //==========
  $message.= $passage_ligne . "--" . $boundary."--" . $passage_ligne;
  $message.= $passage_ligne . "--" . $boundary."--" . $passage_ligne;
  //==========
   
  //=====Envoi de l'email
  mail($destinataire,$sujet,$message,$header);  
  }    
  
//=======================================
function afficher_contacts($connexion,$requete)
	{
	$resultat=mysqli_query($connexion,$requete);
	$i=0;
	$affichage="<table class=\"tab_resultats\">\n";
	$affichage.="<tr>\n";
	$affichage.="<th>Identité</th>\n";
	$affichage.="<th>Email</th>\n";
	$affichage.="<th>Date</th>\n";	
	$affichage.="<th>Action</th>\n";
	$affichage.="</tr>\n";		
	while($ligne=mysqli_fetch_object($resultat))
		{
		$affichage.="<tr>\n";
		$affichage.="<td>" . strtoupper($ligne->nom_contact) . " " . $ligne->prenom_contact . "</td>\n";
		$affichage.="<td>" . $ligne->mel_contact . "</td>\n";	
		$affichage.="<td>" . $ligne->date_contact . "</td>\n";	
		$affichage.="<td><a href=\"admin.php?action=contact&choix=supprimer&id_contact=" . $ligne->id_contact . "\"><span class=\"dashicons dashicons-trash\"></span></a></td>\n";						
		$affichage.="</tr>\n";
		$i++;					
		}
	$_SESSION['nb_contacts']=$i;
	$affichage.="</table>\n";

	return $affichage;
	}
//=======================================
function afficher_comptes($connexion,$requete)
	{
	$resultat=mysqli_query($connexion,$requete);
	$i=0;
	$affichage="<table class=\"tab_resultats\">\n";
	//on calcule les entêtes des colonnes
	$affichage.="<tr>\n";
	$affichage.="<th>Identité</th>\n";
	$affichage.="<th>Login</th>\n";
	$affichage.="<th>Statut</th>\n";
	$affichage.="<th>Avatar</th>\n";	
	$affichage.="<th>Actions</th>\n";
	$affichage.="</tr>\n";	
	while($ligne=mysqli_fetch_object($resultat))
		{
		//on affiche le contenu de chaque uplet présent dans la table
		$affichage.="<tr>\n";
		$affichage.="<td>" . strtoupper($ligne->nom_compte) . " " . $ligne->prenom_compte . "</td>\n";
		$affichage.="<td style=\"text-align:center\">" . $ligne->login_compte . "</td>\n";	
		$affichage.="<td style=\"text-align:center\">" . $ligne->statut_compte . "</td>\n";	
		if(!empty($ligne->fichier_compte))
			{
			$avatar="<img class=\"miniature\" src=\"" . $ligne->fichier_compte  . "\" alt=\"\" />";
			}
		else
			{
			$avatar="no photo !";	
			}
		$affichage.="<td style=\"text-align:center\">" . $avatar . "</td>\n";		
		$affichage.="<td>";
		$affichage.="<a href=\"admin.php?module=comptes&action=modifier_compte&id_compte=" . $ligne->id_compte . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
		$affichage.="&nbsp;&nbsp;&nbsp;";
		$affichage.="<a href=\"admin.php?module=comptes&action=supprimer_compte&statut_compte=" . $ligne->statut_compte . "&id_compte=" . $ligne->id_compte . "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
		$affichage.="</td>\n";						
		$affichage.="</tr>\n";
		$i++;					
		}
	$affichage.="</table>\n";

	return $affichage;
	}
//======================================
function extrait($texte,$nb_mots,$tolerance)	
	{
	//on coupe le texte sur les espaces
	$tab_mots=explode(" ",$texte);
	
	//on compte le nombre de valeurs dans le tableau de variables $tab_mots
	$nb_mots_dans_texte=count($tab_mots);
	
	//si le nb de valeur est inférieur ou égal à $nb_mots
	if($nb_mots_dans_texte<=($nb_mots+$tolerance))
		{
		$extrait=$texte;	
		}
	else//alors il faut raccourcir le texte et fgarder seulement les $nb_mots premiers mots
		{
		//on fait une boucle qui tourne $nb_mots fois	
		$extrait="";
		for($i=0;$i<$nb_mots;$i++)
			{
			//au premier tour de boucle
			if($i==0)
				{
				$extrait.=$tab_mots[$i];	
				}
			else
				{
				$extrait.=" " . $tab_mots[$i];
				}
			}
		$extrait.="...";
		}
	return $extrait;
	}
	
//=======================================
function afficher_articles($connexion,$requete,$cas)
	{
	$resultat=mysqli_query($connexion,$requete);
	
	if(isset($cas))
		{
		switch($cas)
			{
			case "back":

			$i=0;
			$affichage="<table class=\"tab_resultats\">\n";
			//on calcule les entêtes des colonnes
			$affichage.="<tr>\n";
			$affichage.="<th>Tri</th>\n";			
			$affichage.="<th>Titre</th>\n";
			$affichage.="<th>Extrait</th>\n";
			$affichage.="<th>Date</th>\n";	
			$affichage.="<th>RSS</th>\n";	
			$affichage.="<th>Image</th>\n";		
			$affichage.="<th>Actions</th>\n";
			$affichage.="</tr>\n";	
			while($ligne=mysqli_fetch_object($resultat))
				{
				//on affiche le contenu de chaque uplet présent dans la table
				$affichage.="<tr>\n";
				$affichage.="<td><a href=\"admin.php?action=article&choix=trier&id_article=" . $ligne->id_article . "&tri=up\"><span class=\"dashicons dashicons-arrow-up\"></span></a>&nbsp;&nbsp;<a href=\"admin.php?action=article&choix=trier&id_article=" . $ligne->id_article . "&tri=down\"><span class=\"dashicons dashicons-arrow-down\"></span></a></td>\n";	
				$affichage.="<td>" . $ligne->titre_article . "</td>\n";
				$affichage.="<td>" . extrait($ligne->contenu_article,8,4) . "</td>\n";
				$affichage.="<td>" . $ligne->date_article . "</td>\n";	
				$affichage.="<td>" . $ligne->rss . "</td>\n";
				if(empty($ligne->fichier_article))
					{
					$affichage.="<td class=\"td_img\">pas d'image</td>";
					}
				else
					{
					$affichage.="<td class=\"td_img\">
					<img class=\"miniature\" src=\"" . str_replace("_b","_s",$ligne->fichier_article) . "\" alt=\"\" />
					<a class=\"suppr_img\" href=\"admin.php?action=article&choix=supprimer_image&id_article=". $ligne->id_article ."\">
					<span class=\"dashicons dashicons-no-alt\"></span></a>
					</td>\n";		
					}
				$affichage.="<td>";
				$affichage.="<a href=\"admin.php?action=article&choix=dupliquer&id_article=" . $ligne->id_article . "\"><span class=\"dashicons dashicons-admin-page\"></span></a>";
				$affichage.="&nbsp;&nbsp;&nbsp;";				
				$affichage.="<a href=\"admin.php?action=article&choix=modifier&id_article=" . $ligne->id_article . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
				$affichage.="&nbsp;&nbsp;&nbsp;";
				$affichage.="<a href=\"admin.php?action=article&choix=supprimer&id_article=" . $ligne->id_article . "\"><span class=\"dashicons dashicons-trash\"></span></a>";
				$affichage.="</td>\n";						
				$affichage.="</tr>\n";
				$i++;					
				}
			$affichage.="</table>\n";
			
			break;

			case "front":
			
			$affichage="";
			$nom_mois=array("Jan","Fev","Mar","Avr","Mai","Juin","Juil","Aou","Sept","Oct","Nov","Dec");
			$i=0;
			while($ligne=mysqli_fetch_object($resultat))
				{				
				//calcul de la date en 3 morceaux
				$tab_date=explode("-",$ligne->date_article);
				$annee=$tab_date[0];
				$mois=$nom_mois[$tab_date[1]-1];
				$jour=$tab_date[2];

				$affichage.="<article class=\"blog w50\">\n";
				$affichage.="<div class=\"date backrose textblanc\">\n";
				$affichage.="<span class=\"jj \">" . $jour . "</span>\n";
				$affichage.="<span class=\"mm\">" . $mois . "</span>\n"; 
				$affichage.="<span class=\"aaaa\">" . $annee . "</span>\n";								
				$affichage.="</div>\n";
				if(!empty($ligne->fichier_article))
					{
					$affichage.="<img src=\"". str_replace("_b","_s",$ligne->fichier_article) . "\" alt=\"" . $ligne->titre_article . "\" />\n";
					}
				$affichage.="<h2 class=\"textvert\">" . $ligne->titre_article . "</h2>\n";
				$affichage.="<p>" . $ligne->contenu_article . "</p>\n";
				$affichage.="</article>\n";
				$i++;				
				}
					
			break;			
			}		
		}

	return $affichage;
	}
	
//=======================================
function afficher_menus($connexion,$requete)
	{
	$resultat=mysqli_query($connexion,$requete);
	
	$affichage="<table class=\"tab_resultats\">\n";
	//on calcule les entêtes des colonnes
	$affichage.="<tr>\n";				
	$affichage.="<th>Position</th>\n";					
	$affichage.="<th>Intitulé</th>\n";			
	$affichage.="<th>Actions</th>\n";
	$affichage.="</tr>\n";	
	while($ligne=mysqli_fetch_object($resultat))
		{
		//on affiche le contenu de chaque uplet présent dans la table
		$affichage.="<tr>\n";
		$affichage.="<td>" . $ligne->rang_menu . "</td>\n";				
		$affichage.="<td><a href=\"" . $ligne->lien_menu . "\" target=\"_blank\">" . $ligne->intitule_menu . "</a></td>\n";
		$affichage.="<td>";		
		$affichage.="<a href=\"admin.php?module=menus&action=modifier_menu&id_menu=" . $ligne->id_menu . "\"><span class=\"dashicons dashicons-edit\"></span></a>";
		$affichage.="&nbsp;&nbsp;&nbsp;";
		$affichage.="<a href=\"admin.php?module=menus&action=supprimer_menu&id_menu=" . $ligne->id_menu . "\"><span class=\"dashicons dashicons-no-alt\"></span></a>";
		$affichage.="</td>\n";						
		$affichage.="</tr>\n";
		$i++;					
		}
	$affichage.="</table>\n";

	return $affichage;
	}
	
?>





