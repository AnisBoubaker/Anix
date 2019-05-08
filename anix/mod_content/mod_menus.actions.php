<?php
if($action=="insert"){
	$request = request("SELECT $TBL_content_menuitems.id,$TBL_content_menuitems.type,$TBL_content_menuitems.deletable,$TBL_content_menuitems.ordering,$TBL_content_menuitems.id_parent,$TBL_content_info_menuitems.title FROM $TBL_content_menuitems,$TBL_content_info_menuitems,$TBL_gen_languages WHERE $TBL_content_menuitems.id_category='$idCategory' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id AND $TBL_content_info_menuitems.id_language=$TBL_gen_languages.id order by id_parent,ordering",$link);
	$table = getMenusTable($request);
	//Check if we are allowed to add more items in this level
	if($_POST["idParent"]){
		if(count($table[$_POST["idParent"]]["subcats"])>=$category["nbAllowedInSublevels"]){
			$errors++;
			$errMessage.=_("Ce niveau est deja complet, vous ne pouvez plus y ajouter d'elements.")."<br>";//$str1;
		}
	} else {
		if($category["nbAllowed"]!=-1 && count($table)>=$category["nbAllowed"]){
			$errors++;
			$errMessage.=_("Ce niveau est deja complet, vous ne pouvez plus y ajouter d'elements.")."<br>";//$str1;
		}
	}

	if(!$errors && $_POST["type"]=="submenu"){
		//Check if we are allowed to add more levels
		$level = getMenuitemLevel($table,$_POST["idParent"]);
		if($level>=$category["nbLevelsAllowed"]){
			$errors++;
			$errMessage.=_("Vous avez atteint le nombre maximal de niveaux pou ce menu. Vous ne pouvez plus ajouter de niveaux supplementaires.")."<br>";//$str2;
		}
	}
	if(!$errors){
		//Insert the component in the database
		$request = request("SELECT MAX(ordering) as maximum from `$TBL_content_menuitems` WHERE id_parent='".$_POST["idParent"]."' GROUP BY id_parent",$link);
		$tmp = mysql_fetch_object($request);
		$ordering = 1;
		if($tmp) $ordering = ($tmp->maximum+1);
		$requestString="INSERT INTO `$TBL_content_menuitems` (`id_category`,`id_parent`,`type`,`ordering`,`txt_color_off`,`txt_color_on`,`txt_color_mover`,`txt_color_click`,`txt_color_release`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$idCategory','".$_POST["idParent"]."','".$_POST["type"]."','$ordering','".$_POST["txt_color_off"]."','".$_POST["txt_color_on"]."','".$_POST["txt_color_mover"]."','".$_POST["txt_color_click"]."','".$_POST["txt_color_release"]."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idMenuitem=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion du composant.")."<br>";//$str3;
			$errors++;
		}
	}

	if(!$errors){
		$request=request("SELECT id,name,locales_folder from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			//Insert and resize the images
			$locales_folder = $language->locales_folder;
			$imageOffUploaded = (strcmp ($_FILES["img_off_".$language->id]['name'],"")!=0);
			$imageOnUploaded = (strcmp ($_FILES["img_on_".$language->id]['name'],"")!=0);
			$imageMoverUploaded = (strcmp ($_FILES["img_mover_".$language->id]['name'],"")!=0);
			$imageClickUploaded = (strcmp ($_FILES["img_click_".$language->id]['name'],"")!=0);
			$imageReleaseUploaded = (strcmp ($_FILES["img_release_".$language->id]['name'],"")!=0);
			$fileNameOff="";$fileNameOn="";$fileNameMover="";$fileNameClick="";$fileNameRelease="";
			//Checks if the files are allowed
			if($imageOffUploaded && !isImageAllowed($_FILES["img_off_".$language->id]['name'])){
				$imageOffUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageOnUploaded && !isImageAllowed($_FILES["img_on_".$language->id]['name'])){
				$imageOnUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageMoverUploaded && !isImageAllowed($_FILES["img_mover_".$language->id]['name'])){
				$imageMoverUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageClickUploaded && !isImageAllowed($_FILES["img_click_".$language->id]['name'])){
				$imageClickUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageReleaseUploaded && !isImageAllowed($_FILES["img_release_".$language->id]['name'])){
				$imageReleaseUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			//Starts moving and resizing the files...
			if($imageOffUploaded){
				$fileNameTmp = 'imgmenu_off_tmp_'.$idMenuitem.'_'.$_FILES["img_off_".$language->id]['name'];
				$fileNameOff = 'imgmenu_off_'.$idMenuitem.'_'.$_FILES["img_off_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_off_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image off au serveur.")."<br>";//$str4;
					$imageOffUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameOff,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
				}
			}
			if($imageOnUploaded){
				$fileNameTmp = 'imgmenu_on_tmp_'.$idMenuitem.'_'.$_FILES["img_on_".$language->id]['name'];
				$fileNameOn = 'imgmenu_on_'.$idMenuitem.'_'.$_FILES["img_on_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_on_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image on au serveur.")."<br>";//$str6;
					$imageOnUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameOn,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
				}
			}
			if($imageMoverUploaded){
				$fileNameTmp = 'imgmenu_mover_tmp_'.$idMenuitem.'_'.$_FILES["img_mover_".$language->id]['name'];
				$fileNameMover = 'imgmenu_mover_'.$idMenuitem.'_'.$_FILES["img_mover_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_mover_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image mover au serveur.")."<br>";//$str7;
					$imageMoverUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameMover,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
				}
			}
			if($imageClickUploaded){
				$fileNameTmp = 'imgmenu_click_tmp_'.$idMenuitem.'_'.$_FILES["img_click_".$language->id]['name'];
				$fileNameClick = 'imgmenu_click_'.$idMenuitem.'_'.$_FILES["img_click_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_click_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image click au serveur.")."<br>";//$str8;
					$imageClickUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameClick,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
				}
			}
			if($imageReleaseUploaded){
				$fileNameTmp = 'imgmenu_release_tmp_'.$idMenuitem.'_'.$_FILES["img_release_".$language->id]['name'];
				$fileNameRelease = 'imgmenu_release_'.$idMenuitem.'_'.$_FILES["img_release_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_release_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image release au serveur.")."<br>";//$str9;
					$imageReleaseUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameRelease,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
				}
			}
			request("INSERT INTO $TBL_content_info_menuitems (`id_menuitem`,`id_language`,`title`,`alt_title`,`link`,`img_off`,`img_on`,`img_mover`,`img_click`,`img_release`)
                VALUES ('$idMenuitem','".$language->id."','".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["alt_title_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["link_".$language->id],ENT_QUOTES,"UTF-8")."','$fileNameOff','$fileNameOn','$fileNameMover','$fileNameClick','$fileNameRelease')",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertions des informations du composant.")."<br>";//$str10;
			}
		}
	}
	if(!$errors){
		$message = _("Le composant a ete insere correctement")."<br>";//$str11;
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT id,id_parent,id_category from $TBL_content_menuitems WHERE id='$idMenuitem'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.=_("Le composant specifie est invalide.")."<br>";//$str12;
	}
	if(!$errors){
		//Updates the component in the database
		$requestString ="UPDATE $TBL_content_menuitems SET ";
		$requestString.="`txt_color_off`='".$_POST["txt_color_off"]."',";
		$requestString.="`txt_color_on`='".$_POST["txt_color_on"]."',";
		$requestString.="`txt_color_mover`='".$_POST["txt_color_mover"]."',";
		$requestString.="`txt_color_click`='".$_POST["txt_color_click"]."',";
		$requestString.="`txt_color_release`='".$_POST["txt_color_release"]."',";
		$requestString.="`modified_on`='".getDBDate()."',`modified_by`='$anix_username' ";
		$requestString.="WHERE id='$idMenuitem'";
		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour du composant.")."<br>";//$str13;
			$errors++;
		}
	}
	if(!$errors){
		$request=request("SELECT id,name,locales_folder from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			$locales_folder = $language->locales_folder;
			$request2 = request("SELECT img_on,img_off,img_mover,img_click,img_release FROM $TBL_content_info_menuitems WHERE id_menuitem='$idMenuitem' AND id_language='".$language->id."'",$link);
			$oldFiles = mysql_fetch_object($request2);
			//Insert and resize the images
			$imageOffUploaded = (strcmp ($_FILES["img_off_".$language->id]['name'],"")!=0);
			$imageOnUploaded = (strcmp ($_FILES["img_on_".$language->id]['name'],"")!=0);
			$imageMoverUploaded = (strcmp ($_FILES["img_mover_".$language->id]['name'],"")!=0);
			$imageClickUploaded = (strcmp ($_FILES["img_click_".$language->id]['name'],"")!=0);
			$imageReleaseUploaded = (strcmp ($_FILES["img_release_".$language->id]['name'],"")!=0);
			$fileNameOff="";$fileNameOn="";$fileNameMover="";$fileNameClick="";$fileNameRelease="";
			//Checks if the files are allowed
			if($imageOffUploaded && !isImageAllowed($_FILES["img_off_".$language->id]['name'])){
				$imageOffUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageOnUploaded && !isImageAllowed($_FILES["img_on_".$language->id]['name'])){
				$imageOnUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageMoverUploaded && !isImageAllowed($_FILES["img_mover_".$language->id]['name'])){
				$imageMoverUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageClickUploaded && !isImageAllowed($_FILES["img_click_".$language->id]['name'])){
				$imageClickUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			if($imageReleaseUploaded && !isImageAllowed($_FILES["img_release_".$language->id]['name'])){
				$imageReleaseUploaded=false;
				$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br>";
			}
			//Starts moving and resizing...
			if($imageOffUploaded){
				$fileNameTmp = 'imgmenu_off_tmp_'.$idMenuitem.'_'.$_FILES["img_off_".$language->id]['name'];
				$fileNameOff = 'imgmenu_off_'.$idMenuitem.'_'.$_FILES["img_off_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_off_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image off au serveur.")."<br>";//$str4;
					$imageOffUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameOff,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
					if($oldFiles->img_off!="" && !unlink("../".$folder_webLocalesRoot.$locales_folder."/images/".$oldFiles->img_off)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne image off.")."<br>";//$str14;
					}
				}
			}
			if($imageOnUploaded){
				$fileNameTmp = 'imgmenu_on_tmp_'.$idMenuitem.'_'.$_FILES["img_on_".$language->id]['name'];
				$fileNameOn = 'imgmenu_on_'.$idMenuitem.'_'.$_FILES["img_on_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_on_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image on au serveur.")."<br>";//$str6;
					$imageOnUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameOn,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
					if($oldFiles->img_on!="" && !unlink("../".$folder_webLocalesRoot.$locales_folder."/images/".$oldFiles->img_on)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne image on.")."<br>";//$str15;
					}
				}
			}
			if($imageMoverUploaded){
				$fileNameTmp = 'imgmenu_mover_tmp_'.$idMenuitem.'_'.$_FILES["img_mover_".$language->id]['name'];
				$fileNameMover = 'imgmenu_mover_'.$idMenuitem.'_'.$_FILES["img_mover_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_mover_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image mover au serveur.")."<br>";//$str7;
					$imageMoverUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameMover,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
					if($oldFiles->img_mover!="" && !unlink("../".$folder_webLocalesRoot.$locales_folder."/images/".$oldFiles->img_mover)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne image mover.")."<br>";//$str16;
					}
				}
			}
			if($imageClickUploaded){
				$fileNameTmp = 'imgmenu_click_tmp_'.$idMenuitem.'_'.$_FILES["img_click_".$language->id]['name'];
				$fileNameClick = 'imgmenu_click_'.$idMenuitem.'_'.$_FILES["img_click_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_click_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image click au serveur.")."<br>";//$str8;
					$imageClickUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameClick,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
					if($oldFiles->img_click!="" && !unlink("../".$folder_webLocalesRoot.$locales_folder."/images/".$oldFiles->img_click)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne image click.")."<br>";//$str17;
					}
				}
			}
			if($imageReleaseUploaded){
				$fileNameTmp = 'imgmenu_off_release_'.$idMenuitem.'_'.$_FILES["img_release_".$language->id]['name'];
				$fileNameRelease = 'imgmenu_release_'.$idMenuitem.'_'.$_FILES["img_release_".$language->id]['name'];
				$filePath = $folder_webLocalesRoot.$locales_folder."/images/".$fileNameTmp;
				if(!move_uploaded_file($_FILES["img_release_".$language->id]['tmp_name'],'../'.$filePath)){
					$errMessage.=_("Vous n'avez pas specifie d'image ou une erreur s'est produite lors de l'envoi de l'image release au serveur.")."<br>";//$str9;
					$imageReleaseUploaded=false;
				} else{
					$imageEditor = new ImageEditor($fileNameTmp, '../'.$folder_webLocalesRoot.$locales_folder."/images/");
					$imageEditor->resizeFixed($category["img_maxW"],$category["img_maxH"]);
					$imageEditor->outputFile($fileNameRelease,'../'.$folder_webLocalesRoot.$locales_folder."/images/");
					if(!unlink('../'.$filePath)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br>";//$str5;
					}
					if($oldFiles->img_release!="" && !unlink("../".$folder_webLocalesRoot.$locales_folder."/images/".$oldFiles->img_release)){
						$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne image release.")."<br>";//$str18;
					}
				}
			}
			$requestString ="UPDATE $TBL_content_info_menuitems SET ";
			if($imageOffUploaded) $requestString.="`img_off`='$fileNameOff',";
			if($imageOnUploaded)$requestString.="`img_on`='$fileNameOn',";
			if($imageMoverUploaded)$requestString.="`img_mover`='$fileNameMover',";
			if($imageClickUploaded)$requestString.="`img_click`='$fileNameClick',";
			if($imageReleaseUploaded)$requestString.="`img_release`='$fileNameRelease',";
			$requestString.="`title`='".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`alt_title`='".htmlentities($_POST["alt_title_".$language->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`link`='".htmlentities($_POST["link_".$language->id],ENT_QUOTES,"UTF-8")."' ";
			$requestString.="WHERE id_menuitem='$idMenuitem' and id_language='".$language->id."'";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise a jour des informations du composant.")."<br>";//$str19;
			}
		}
	}
	if(!$errors){
		$message = _("Le composant a ete mis a jour correctement")."<br>";//$str20;
		$action="edit";
	}
}
?>
<?
if($action=="removeImage"){
	if(isset($_POST["imgType"])){
		$imgType=$_POST["imgType"];
	} elseif(isset($_GET["imgType"])){
		$imgType=$_GET["imgType"];
	} else {
		$errors++;
		$errMessage.=_("Le type de l'image n'a pas ete specifie")."<br>";//$str21;
	}
	if(isset($_POST["idLanguage"])){
		$idLanguage=$_POST["idLanguage"];
	} elseif(isset($_GET["idLanguage"])){
		$idLanguage=$_GET["idLanguage"];
	} else {
		$errors++;
		$errMessage.=_("La langue n'a pas ete specifiee")."<br>";//$str22;
	}
	if(!$errors){
		$request=request("SELECT $TBL_content_info_menuitems.img_on,$TBL_content_info_menuitems.img_off,$TBL_content_info_menuitems.img_mover,$TBL_content_info_menuitems.img_click,$TBL_content_info_menuitems.img_release,$TBL_gen_languages.locales_folder FROM $TBL_content_info_menuitems,$TBL_gen_languages WHERE $TBL_content_info_menuitems.id_menuitem='$idMenuitem' AND $TBL_gen_languages.id='$idLanguage' AND $TBL_content_info_menuitems.id_language=$TBL_gen_languages.id",$link);
		if(mysql_num_rows($request)){
			$files = mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le composant specifie est invalide.")."<br>";//$str12;
		}
	}
	if(!$errors && $imgType=="off"){
		if($files->img_off!="" && !unlink("../".$folder_webLocalesRoot.$files->locales_folder."/images/".$files->img_off)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors la suppression de l'image off du composant")."<br>";//$str24;
		}
		request("UPDATE $TBL_content_info_menuitems SET `img_off`='' WHERE id_menuitem='$idMenuitem' AND id_language='$idLanguage'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de la base de donnees.")."<br>";//$str23;
		}
	}elseif(!$errors && $imgType=="on"){
		if($files->img_on!="" && !unlink("../".$folder_webLocalesRoot.$files->locales_folder."/images/".$files->img_on)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors la suppression de l'image on du composant")."<br>";//$str25;
		}
		request("UPDATE $TBL_content_info_menuitems SET `img_on`='' WHERE id_menuitem='$idMenuitem' AND id_language='$idLanguage'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de la base de donnees.")."<br>";//$str23;
		}
	}elseif(!$errors && $imgType=="mover"){
		if($files->img_mover!="" && !unlink("../".$folder_webLocalesRoot.$files->locales_folder."/images/".$files->img_mover)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors la suppression de l'image mover du composant.")."<br>";//$str26;
		}
		request("UPDATE $TBL_content_info_menuitems SET `img_mover`='' WHERE id_menuitem='$idMenuitem' AND id_language='$idLanguage'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de la base de donnees.")."<br>";//$str23;
		}
	}elseif(!$errors && $imgType=="click"){
		if($files->img_click!="" && !unlink("../".$folder_webLocalesRoot.$files->locales_folder."/images/".$files->img_click)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors la suppression de l'image click du composant.")."<br>";//$str27;
		}
		request("UPDATE $TBL_content_info_menuitems SET `img_click`='' WHERE id_menuitem='$idMenuitem' AND id_language='$idLanguage'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de la base de donnees.")."<br>";//$str23;
		}
	}elseif(!$errors && $imgType=="release"){
		if($files->img_release!="" && !unlink("../".$folder_webLocalesRoot.$files->locales_folder."/images/".$files->img_release)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors la suppression de l'image release du composant.")."<br>";//$str28;
		}
		request("UPDATE $TBL_content_info_menuitems SET `img_release`='' WHERE id_menuitem='$idMenuitem' AND id_language='$idLanguage'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de la base de donnees.")."<br>";//$str23;
		}
	} elseif(isset($imgType)) {
		$errors++;
		$errMessage.=_("Le type d'image specifie est invalide.")."<br>";//$str29;
	}
	if(!$errors){
		$message.=_("L'image a bien ete supprimee.")."<br>";//$str30;
	}
	$action="edit";
}
?>