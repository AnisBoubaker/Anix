<?php
if($action=="insert"){
	$request = request("SELECT $TBL_catalogue_info_products.name from $TBL_catalogue_info_products,$TBL_gen_languages WHERE $TBL_catalogue_info_products.id_product='$idProduct' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
	if(!mysql_num_rows($request)) {
		$errors++;
		$errMessage.=_("Ce produit n'existe pas.")."<br />";
	}
	if(!$errors){
		//get the ordering
		$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_product_options` WHERE id_product='$idProduct' GROUP BY id_product",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum+1;
		} else $maxOrderValue=1;
		request("INSERT INTO `$TBL_catalogue_product_options` (`id_product`,`ordering`) VALUES ('$idProduct','$maxOrderValue')",$link);
		if(!mysql_errno($link)){
			$idOption = mysql_insert_id($link);
		} else {
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de l'ajout de l'option au produit.")."<br />";
		}
	}
	if(!$errors){
		$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y'",$link);
		while($language=mysql_fetch_object($languages)){
			$name = htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8");
			$requestString="INSERT INTO `$TBL_catalogue_info_options` (`id_option`,`id_language`,`name`) values ('$idOption','".$language->id."','$name')";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations de l'option.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message.=_("L'option a été ajoutée au produit correctement.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="update"){
	$request = request("SELECT $TBL_catalogue_product_options.id from `$TBL_catalogue_product_options` WHERE $TBL_catalogue_product_options.id='$idOption'",$link);
	if(!mysql_num_rows($request)) {
		$errors++;
		$errMessage.=_("Cette option n'existe pas.")."<br />";
	}
	if(!$errors){
		$languages=request("SELECT $TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y'",$link);
		$nbLanguages= mysql_num_rows($languages);
		while($language = mysql_fetch_object($languages)){
			request("UPDATE `$TBL_catalogue_info_options` set name='".htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8")."' WHERE id_option='$idOption' and id_language='".$language->id."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'option.")."<br />";
			}
			$choices=request("
          SELECT id,default_choice,price_diff,price_value,price_method
          FROM `$TBL_catalogue_product_option_choices`
          WHERE $TBL_catalogue_product_option_choices.id_option = '$idOption'
          ORDER BY $TBL_catalogue_product_option_choices.id",$link);
			while($choice=mysql_fetch_object($choices)){
				request("UPDATE `$TBL_catalogue_info_choices` set value='".htmlentities($_POST["choice_".$choice->id."_".$language->id],ENT_QUOTES,"UTF-8")."' WHERE id_choice='".$choice->id."' and id_language='".$language->id."'",$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.=_("Une erreur s'est produite lors de la mise à jour d'un choix.")."<br />";
				}
			}
		}
		$choices=request("
        SELECT id,default_choice,price_diff,price_value,price_method
        FROM `$TBL_catalogue_product_option_choices`
        WHERE $TBL_catalogue_product_option_choices.id_option = '$idOption'
        ORDER BY $TBL_catalogue_product_option_choices.id",$link);
		while($choice=mysql_fetch_object($choices)){
			request("UPDATE `$TBL_catalogue_product_option_choices` SET
                default_choice='".(($_POST["default_choice"]==$choice->id)?"Y":"N")."',
                price_diff='".$_POST["price_diff_".$choice->id]."',
                price_value='".$_POST["price_value_".$choice->id]."',
                price_method='".$_POST["price_method_".$choice->id]."'
                WHERE id='".$choice->id."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des valeurs d'un choix.")."<br />";
			}
		}
		if(isset($_POST["addChoice"])){
			//get the ordering
			$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_product_option_choices` WHERE id_option='$idOption' GROUP BY id_option",$link);
			if(mysql_num_rows($tmp)) {
				$maxOrder= mysql_fetch_object($tmp);
				$maxOrderValue = $maxOrder->maximum+1;
			} else $maxOrderValue=1;
			request("INSERT INTO `$TBL_catalogue_product_option_choices` (`id_option`,`default_choice`,`price_diff`,`price_value`,`price_method`,`ordering`)
                 VALUES ('$idOption','".(($_POST["default_choice"]==0)?"Y":"N")."','".$_POST["price_diff_new"]."','".$_POST["price_value_new"]."','".$_POST["price_method_new"]."','$maxOrderValue')",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'ajout du choix.")."<br />";
			} else {
				$insertedChoice = mysql_insert_id($link);
			}
			if(!$errors){
				$languages=request("SELECT $TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y'",$link);
				$nbLanguages= mysql_num_rows($languages);
				while($language = mysql_fetch_object($languages)){
					request("INSERT INTO `$TBL_catalogue_info_choices` (`id_choice`,`id_language`,`value`)
                     VALUES ('$insertedChoice','".$language->id."','".htmlentities($_POST["choice_new_".$language->id],ENT_QUOTES,"UTF-8")."')",$link);
					if(mysql_errno($link)){
						$errors++;
						$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations du nouveau choix.")."<br />";
					}
				}
			}

		}
	} // if !errors
	if(!$errors){
		$message.=_("L'option a été mise à jour correctement.")."<br />";
	}

	$action="edit";
}
?>
<?
if($action=="delChoice"){
	if(isset($_POST["idChoice"])){
		$idChoice=$_POST["idChoice"];
	} elseif(isset($_GET["idChoice"])){
		$idChoice=$_GET["idChoice"];
	} else $idChoice=0;
	$request = request("SELECT $TBL_catalogue_product_option_choices.id,$TBL_catalogue_product_option_choices.ordering from $TBL_catalogue_product_option_choices,$TBL_catalogue_product_options WHERE $TBL_catalogue_product_option_choices.id='$idChoice' AND $TBL_catalogue_product_options.id='$idOption' AND $TBL_catalogue_product_options.id_product='$idProduct' AND $TBL_catalogue_product_option_choices.id_option=$TBL_catalogue_product_options.id",$link);
	if(!mysql_num_rows($request)) {
		$errors++;
		$errMessage.=_("Ce choix n'existe pas ou l'option est invalide.")."<br />";
	} else $choice = mysql_fetch_object($request);
	if(!$errors){
		request("DELETE $TBL_catalogue_product_option_choices, $TBL_catalogue_info_choices
      FROM $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
      WHERE $TBL_catalogue_product_option_choices.id='$idChoice'
      AND $TBL_catalogue_info_choices.id_choice=$TBL_catalogue_product_option_choices.id",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression du choix.")."<br />";
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_option_choices` set ordering=ordering-1 where id_option='$idOption' and ordering > ".$choice->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des choix.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le choix a été supprimé correctement.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="moveChoiceUp"){
	if(isset($_POST["idChoice"])){
		$idChoice=$_POST["idChoice"];
	} elseif(isset($_GET["idChoice"])){
		$idChoice=$_GET["idChoice"];
	} else {
		$errors++;
		$errMessage.=_("Le choix n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_option_choices` where id='$idChoice'",$link);
		if(mysql_num_rows($request)){
			$choice=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le choix spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_option_choices` where id_option='$idOption' and ordering='".($choice->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upChoice=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le choix spécifié est dété au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_option_choices` set ordering='".($choice->ordering-1)."' where id='$idChoice'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du choix.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_option_choices` set ordering='".$choice->ordering."' where id='".$upChoice->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du choix.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre du choix a été mis à jour correctement.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="moveChoiceDown"){
	if(isset($_POST["idChoice"])){
		$idChoice=$_POST["idChoice"];
	} elseif(isset($_GET["idChoice"])){
		$idChoice=$_GET["idChoice"];
	} else {
		$errors++;
		$errMessage.=_("Le choix n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_option_choices` where id='$idChoice'",$link);
		if(mysql_num_rows($request)){
			$choice=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le choix spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_option_choices` where id_option='$idOption' and ordering='".($choice->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downChoice=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le choix spécifié est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_option_choices` set ordering='".($choice->ordering+1)."' where id='$idChoice'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du choix.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_option_choices` set ordering='".$choice->ordering."' where id='".$downChoice->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du choix.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre du choix a été mis à jour correctement.")."<br />";
	}
	$action="edit";
}
?>