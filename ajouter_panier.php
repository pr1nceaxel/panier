<?php

include_once "con_dbb.php";

 if(!isset($_SESSION)){

    session_start();
 }
 //creer la session
 if(!isset($_SESSION['panier'])){

    $_SESSION['panier'] = array();
 }
 //récupération de l'id dans le lien
  if(isset($_GET['id'])){
    $id = $_GET['id'] ;

    $produit = mysqli_query($con ,"SELECT * FROM products WHERE id = $id") ;
    if(empty(mysqli_fetch_assoc($produit))){
        //si ce produit n'existe pas
        die("Ce produit n'existe pas");
    }

    if(isset($_SESSION['panier'][$id])){// si le produit est déjà dans le panier
        $_SESSION['panier'][$id]++; //Représente la quantité 
    }else {
        //si non on ajoute le produit
        $_SESSION['panier'][$id]= 1 ;
    }

   //redirection vers la page index.php
   header("Location:index.php");


  }
?>