<?php


//si admin.php reçoit le parametre action (si un client à cliqué sur un bouton)
if (isset($_GET['action'])) {
  $contenu="form_" .$_GET['action']. ".html";
  switch($_GET['action'])
  {
    case"comptes": 
				
    break;

    case"competences":
      
    break;

    case"slider":
      
    break;

    case"messagerie":
      $contenu="messagerie.html";
      
    break;
  }
  }
  else //personne n'a cliqué sur un bouton(à l'arrivée sur le back (tableau de bord))
  {
    $contenu="intro.html";
  }

include("admin.html");

?>