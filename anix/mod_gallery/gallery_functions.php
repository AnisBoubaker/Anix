<?php
function getCatTable($result){
	$photoCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $photoCat[$lastInserted]["last"]=false;
		$photoCat[$row->id]=array();
		$photoCat[$row->id]["id"]=$row->id;
		$photoCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $photoCat[$row->id]["deletable"]=$row->deletable;
		if(isset($row->contain_items)) $photoCat[$row->id]["contain_items"]=$row->contain_items;
		$photoCat[$row->id]["ordering"]=$row->ordering;
		$photoCat[$row->id]["name"]=$row->name;
		$photoCat[$row->id]["description"]=$row->description;
		$photoCat[$row->id]["id_parent"]=$row->id_parent;
		$photoCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $photoCat[$row->id]["first"]=true;
		else $photoCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $photoCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$photoCat[$row->id]["nbActivePhoto"]=0;
		$photoCat[$row->id]["nbInactivePhoto"]=0;
		$photoCat[$row->id]["nbExpiredPhoto"]=0;
		$photoCat[$row->id]["nbAwaitingPhoto"]=0;
		$photoCat[$row->id]["nbArchivedPhoto"]=0;
		$photoCat[$row->id]["nbTotalPhoto"]=0;
	}
	return $photoCat;
}
function getStats($table,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_photo;
	$currentDate=date("Y-m-d");
	//Get how many active photo we have in each Cat
	$request = request("select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and ($TBL_gallery_photo.active='Y' or ($TBL_gallery_photo.active='DATE' and $TBL_gallery_photo.from_date<=$currentDate and $TBL_gallery_photo.to_date>=$currentDate)) group by $TBL_gallery_photo.id_category", $link);
	//echo "select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and ($TBL_gallery_photo.active='Y' or ($TBL_gallery_photo='DATE' and $TBL_gallery_photo.from_date<=$currentDate and $TBL_gallery_photo.to_date>=$currentDate)) group by $TBL_gallery_photo.id_category";
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbActivePhoto"]=$cat->nbPhotos;
	}
	//Get how many inactive photo we have in each Cat
	$request = request("select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and $TBL_gallery_photo.active='N' group by $TBL_gallery_photo.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbInactivePhoto"]=$cat->nbPhotos;
	}
	//Get how many awaiting photo we have in each Cat
	$request = request("select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and $TBL_gallery_photo.active='DATE' and $TBL_gallery_photo.from_date>'$currentDate' group by $TBL_gallery_photo.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbAwaitingPhoto"]=$cat->nbPhotos;
	}
	//Get how many expired photo we have in each Cat
	$request = request("select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and $TBL_gallery_photo.active='DATE' and $TBL_gallery_photo.to_date<'$currentDate' group by $TBL_gallery_photo.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbExpiredPhoto"]=$cat->nbPhotos;
	}
	//Get how many archived photo we have in each Cat
	$request = request("select $TBL_gallery_categories.id,COUNT(*) as nbPhotos from  $TBL_gallery_categories,$TBL_gallery_photo where $TBL_gallery_photo.id_category=$TBL_gallery_categories.id and $TBL_gallery_photo.active='ARCHIVE' group by $TBL_gallery_photo.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbArchivedPhoto"]=$cat->nbPhotos;
	}
	//Compute general values
	foreach($table as $category){
		$table[$category["id"]]["nbTotalPhoto"]=$category["nbActivePhoto"]+$category["nbInactivePhoto"]+$category["nbAwaitingPhoto"]+$category["nbExpiredPhoto"]+$category["nbArchivedPhoto"];
	}
	return $table;
}

function getOtherCatTable($result,$prohibited){
	$photoCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if($row->id!=$prohibited && ($row->id_parent==0 || isset($photoCat[$row->id_parent]))){
			if(!$first && $lastParent==$row->id_parent) $photoCat[$lastInserted]["last"]=false;
			$photoCat[$row->id]=array();
			$photoCat[$row->id]["id"]=$row->id;
			$photoCat[$row->id]["subcats"]=array();
			$photoCat[$row->id]["ordering"]=$row->ordering;
			$photoCat[$row->id]["name"]=$row->name;
			$photoCat[$row->id]["description"]=$row->description;
			$photoCat[$row->id]["id_parent"]=$row->id_parent;
			$photoCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $photoCat[$row->id]["first"]=true;
			else $photoCat[$row->id]["first"]=false;
			if($row->id_parent!=0) $photoCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
	}
	return $photoCat;
}
function getCategoriesList($table,$idParent,$level){
	$returnTable=array();
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnTable[$row["id"]]=array();
		$returnTable[$row["id"]]["id"]=$row["id"];
		$returnTable[$row["id"]]["name"]="";
		for($i=0;$i<$level;$i++) $returnTable[$row["id"]]["name"].="&nbsp;&nbsp;";
		$returnTable[$row["id"]]["name"].=$row["name"];
		if(count($row["subcats"])) {
			$subTable=getCategoriesList($table,$row["id"],$level+1);
			$returnTable = $returnTable + $subTable;
		}
	}
	return $returnTable;
}
/**
 * Display the photo categories (used to list categories or to choose a catgeory where to copy or move....)
 * if link_to_containers_onlu is set to true, the cqategpries that couldnt contain items will not have the link set.
 *
 * @param unknown_type $table
 * @param unknown_type $idParent
 * @param unknown_type $level
 * @param unknown_type $actionURL
 * @param unknown_type $showButtons
 * @param unknown_type $link_containters_only
 * @return unknown
 */
function showCategories($table,$idParent,$level,$actionURL,$showButtons,$link_containters_only=false,$except=0){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		$showActionURL=$actionURL!="" && (!$link_containters_only || $row["contain_items"]=="Y");
		if($except && $except==$row["id"]) $showActionURL=false;
		if($showActionURL) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($showActionURL) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategories($table,$row["id"],$level+1,$actionURL,$showButtons,$link_containters_only,$except);
	}
	return $returnStr;
}
function showStats($table,$idParent,$level,$unitsPerPixel){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.=$row["name"]."(".$row["nbTotalPhoto"].")";
		$returnStr.="</td>";
		$returnStr.="<td width='130' align='left' valign='middle'>";
		if($row["nbTotalPhoto"]){
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbAwaitingPhoto"]*$unitsPerPixel."' alt='".$row["nbAwaitingPhoto"]." "._("Nouvelle(s) en attente")."(".$row["nbAwaitingPhoto"]*100/$row["nbTotalPhoto"]."%)'>";
			$returnStr.="<img src='../images/bar_green.jpg' height='5' width='".$row["nbActivePhoto"]*$unitsPerPixel."' alt='".$row["nbActivePhoto"]." "._("Nouvelle(s) actives")."(".$row["nbActivePhoto"]*100/$row["nbTotalPhoto"]."%)'>";
			$returnStr.="<img src='../images/bar_orange.jpg' height='5' width='".$row["nbExpiredPhoto"]*$unitsPerPixel."' alt='".$row["nbExpiredPhoto"]." "._("Nouvelle(s) expirées")."(".$row["nbExpiredPhoto"]*100/$row["nbTotalPhoto"]."%)'>";
			$returnStr.="<img src='../images/bar_red.jpg' height='5' width='".$row["nbInactivePhoto"]*$unitsPerPixel."' alt='".$row["nbInactivePhoto"]." "._("Nouvelle(s) inactives")."(".$row["nbInactivePhoto"]*100/$row["nbTotalPhoto"]."%)'>";
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbArchivedPhoto"]*$unitsPerPixel."' alt='".$row["nbArchivedPhoto"]." "._("Nouvelle(s) archivées")."(".$row["nbArchivedPhoto"]*100/$row["nbTotalPhoto"]."%)'>";
		}
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showStats($table,$row["id"],$level+1,$unitsPerPixel);
	}
	return $returnStr;
}
function showCategoriesExcept($table,$idParent,$level,$actionURL,$showButtons,$idProhibited){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategoriesExcept($table,$row["id"],$level+1,$actionURL,$showButtons,$idProhibited);
	}
	return $returnStr;
}

function showPhoto($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_info_photo;
	global $TBL_gallery_photo;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
			$returnStr.="<td style='width:122px;background:#e7eff2;text-align:right;'>";
			if($row["contain_items"]=="Y") $returnStr.="<a href='./mod_photo.php?action=add&idCat=".$row["id"]."'><img src='../images/add.gif' alt=\""._("Ajouter une nouvelle à cette catégorie.")."\" /></a>";
			$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie courante")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$maxOrder = getMaxPhotoOrder($showCategory,$link)-1;
			$request = request("SELECT $TBL_gallery_photo.id,$TBL_gallery_photo.ordering,$TBL_gallery_photo.active,$TBL_gallery_photo.from_date,$TBL_gallery_photo.to_date,$TBL_gallery_info_photo.date,$TBL_gallery_info_photo.title FROM $TBL_gallery_photo,$TBL_gallery_info_photo,$TBL_gen_languages WHERE $TBL_gallery_photo.id_category='$showCategory' and $TBL_gallery_photo.id=$TBL_gallery_info_photo.id_photo and $TBL_gallery_info_photo.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_gallery_photo.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucune nouvelle dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($photo=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>";
				if($showButtons){
					$returnStr.="<a href='./mod_photo.php?action=edit&idPhoto=".$photo->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la nouvelle")."\"></a>";
					$returnStr.="<a href='./copy_photo.php?idPhoto=".$photo->id."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la nouvelle")."\"></a>";
					$returnStr.="<a href='./move_photo.php?idPhoto=".$photo->id."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer la nouvelle")."\"></a>";
					$returnStr.="<a href='./del_photo.php?idPhoto=".$photo->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la nouvelle")."\"></a>";
					if($photo->ordering!=1) $returnStr.="<a href='./list_photos.php?action=moveup&idCat=$showCategory&idPhoto=".$photo->id."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($photo->ordering<$maxOrder) $returnStr.="<a href='./list_photos.php?action=movedown&idCat=$showCategory&idPhoto=".$photo->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				$returnStr.="</td>";
				$returnStr.="<td>";
				//Get the activation status of the photo
				if($photo->active=="Y") $active=_("Active");
				elseif($photo->active=="N") $active=_("Désactivée");
				elseif($photo->active=="ARCHIVE") $active=_("Archivée");
				elseif($photo->active=="DATE") {
					$currentDate=date("Y-m-d");
					if($currentDate<$photo->from_date) $active=_("En attente");
					elseif($currentDate>$photo->to_date) $active=_("Expirée");
					else $active=_("Active");
				}
				//Get the substring to show from short_desc of the photo (100 Cars totally)
				$totalCars =strlen($photo->date)+2; //+2 for parenthesis.
				$totalCars+=strlen($active)+3; //+3 for the dash.
				$maxLength = 110;
				if(strlen($photo->title)+$totalCars>$maxLength){
					$showDesc=substr($photo->title, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$photo->title;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$returnStr.="(".$photo->date.") ".$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showPhoto($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
	}
	return $returnStr;
}
function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_info_photo;
	global $TBL_gallery_photo;
	global $TBL_gen_languages;
	$category=request("select $TBL_gallery_categories.id,$TBL_gallery_info_categories.name,$TBL_gallery_categories.id_parent from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_categories.id='$idFirstParent' and $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_gallery_categories.id,$TBL_gallery_info_categories.name,$TBL_gallery_categories.id_parent from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_categories.id='".$row->id_parent."' and $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_info_photo;
	global $TBL_gallery_photo;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_gallery_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxPhotoOrder($idCat,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_info_photo;
	global $TBL_gallery_photo;
	$request=request("SELECT id FROM $TBL_gallery_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_gallery_photo where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_photo;
	global $TBL_gallery_info_photo;
	global $TBL_links_catalogue_photo;
	global $CATALOG_folder_images;
	global $ANIX_messages;

	foreach($table[$idCat]["subcats"] as $subcategory){
		deleteCategory($subcategory,$table,$thread,$link);
	}
	//Delete the item'images from the file system
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT image_file_orig, image_file_large,image_file_small,image_file_icon from $TBL_gallery_photo where id_category=$idCat",$link);
		while($images=mysql_fetch_object($request)) {
			if($images->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'image de la nouvelle."));
				}
			}
			if($images->image_file_small!="imgphoto_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de la nouvelle."));
				}
			}
			if($images->image_file_large!="imgphoto_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image de la nouvelle."));
				}
			}
			if($images->image_file_icon!="imgphoto_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone de la nouvelle."));
				}
			}
		}
	}
	//Delete the links with the products
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_links_catalogue_photo
               FROM $TBL_gallery_photo,
                    $TBL_links_catalogue_photo
               WHERE $TBL_gallery_photo.id_category='$idCat'
               AND $TBL_links_catalogue_photo.photo='item'
               AND $TBL_links_catalogue_photo.id_photo=$TBL_gallery_photo.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des liens de questions avec les produits."));
		}
		request("DELETE FROM $TBL_links_catalogue_photo
               WHERE $TBL_links_catalogue_photo.photo='cat'
               AND $TBL_links_catalogue_photo.id_photo='$idCat'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des liens de la catégorie avec les produits."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_gallery_photo,$TBL_gallery_info_photo
               FROM $TBL_gallery_photo,$TBL_gallery_info_photo
               WHERE $TBL_gallery_photo.id_category='$idCat'
               AND $TBL_gallery_info_photo.id_photo = $TBL_gallery_photo.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des nouvelles reliées à la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_gallery_categories,$TBL_gallery_info_categories
               FROM  $TBL_gallery_categories,$TBL_gallery_info_categories
               WHERE $TBL_gallery_categories.id='$idCat'
               AND $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression de la catégorie."));
		}
	}
	//re-order the category
	if(!$ANIX_messages->nbErrors && $idCat==$thread){
		request("UPDATE $TBL_gallery_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre des catégories."));
		}
	}
}

function copyCategory($idCat,$copyTo,$table,$thread,$link){
	global $TBL_gallery_categories;
	global $TBL_gallery_info_categories;
	global $TBL_gallery_photo;
	global $anix_username;
	global $ANIX_messages;

	//Check if the category exists
	$request = request("SELECT * FROM `$TBL_gallery_categories` WHERE `id`='$idCat'",$link);
	if(!mysql_num_rows($request)){
		$return["errors"]++;
		$return["errMessage"]=_("La catégorie spécifiée n'existe pas.");
		return $return;
	} else {
		$originalCat = mysql_fetch_object($request);
	}
	$ordering=getMaxCategoryOrder($copyTo,$link );
	if(!$ANIX_messages->nbErrors){
		request("INSERT INTO `$TBL_gallery_categories`
						(`ordering`,
						 `id_parent`,
						 `id_menu`,
						 `photo_icon_width`,
						 `photo_icon_height`,
						 `photo_small_width`,
						 `photo_small_height`,
						 `photo_large_width`,
						 `photo_large_height`,
						 `alias_prepend`,
						 `alias_photo_prepend`,
						 `created_on`,
						 `created_by`,
						 `modified_on`,
						 `modified_by`)
				VALUES ('$ordering',
						'$copyTo',
						'".$originalCat->id_menu."',
						'".$originalCat->photo_icon_width."',
						'".$originalCat->photo_icon_height."',
						'".$originalCat->photo_small_width."',
						'".$originalCat->photo_small_height."',
						'".$originalCat->photo_large_width."',
						'".$originalCat->photo_large_height."',
						'".$originalCat->alias_prepend."',
						'".$originalCat->alias_photo_prepend."',
						'".getDBDate()."',
						'$anix_username',
						'".getDBDate()."',
						'$anix_username')"
			,$link);

		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie de la catégorie."));
		} else {
			$newCatId=mysql_insert_id($link);
		}
	}
	//Copy the category information
	if(!$ANIX_messages->nbErrors){
		request("INSERT INTO $TBL_gallery_info_categories (`id_gallery_cat`,`id_language`,`name`,`description`,`htmltitle`,`htmldescription`,`keywords`,`alias_name`) SELECT '$newCatId',id_language,name,description,htmltitle,htmldescription,keywords,alias_name FROM $TBL_gallery_info_categories WHERE id_gallery_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie des informations de la catégorie."));
		}
	}
	//Copy the photo of the category
	if(!$ANIX_messages->nbErrors){
		$photoList=request("SELECT * from $TBL_gallery_photo where id_category=$idCat",$link);
		while($photo=mysql_fetch_object($photoList)){
			copyPhoto($photo->id,$newCatId,0,$link);
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Recursive copy of the subcategories
		foreach($table[$idCat]["subcats"] as $subcategory){
			copyCategory($subcategory,$newCatId,$table,$thread,$link);
		}
	}
}

//Copy photo to specified category
// if conserve the same ordering, put 0
function copyPhoto($idPhoto,$copyTo,$ordering,$link){
	global $TBL_gallery_photo;
	global $TBL_gallery_info_photo;
	global $CATALOG_folder_images;
	global $anix_username;
	global $ANIX_messages;

	$request = request("SELECT * from $TBL_gallery_photo where id='$idPhoto'",$link);
	if(mysql_num_rows($request)){
		$photo=mysql_fetch_object($request);
	} else {
		$ANIX_messages->addError(_("La nouvelle n'existe pas."));
	}
	if(!$ANIX_messages->nbErrors){
		if(!$ordering) $ordering=$photo->ordering;
		request("INSERT INTO `$TBL_gallery_photo` (`id_category`,`active`,`from_date`,`to_date`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$copyTo','".$photo->active."','".$photo->from_date."','".$photo->to_date."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie de la nouvelle")." $idPhoto");
		} else {
			$idNewPhoto=mysql_insert_id($link);
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("INSERT INTO `$TBL_gallery_info_photo` (`id_photo`,`id_language`,`title`,`date`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`,`alias_name`) SELECT '$idNewPhoto',id_language,title,date,short_desc,details,keywords,htmltitle,htmldescription,alias_name FROM $TBL_gallery_info_photo WHERE id_photo='$idPhoto'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie des informations de la nouvelle")." $idPhoto.");
		}
	}
	//Copy the images for the new photo
	if(!$ANIX_messages->nbErrors){
		//Get the file name
		$destFileName ="";
		$destLargeFileName = "";
		$destSmallFileName = "";
		$destIconFileName = "";
		//Copy the original image
		$fileName = $photo->image_file_orig;
		if($fileName==""){
			$destFileName = "";
		} else {
			$tmp = explode("_",$fileName,4);
			$destFileName = $tmp[3];
			$destFileName = "imgphoto_orig_".$idNewPhoto."_".$destFileName;
			if(!copy("../".$CATALOG_folder_images.$photo->image_file_orig,"../".$CATALOG_folder_images.$destFileName)){
				$destFileName = "";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la grande image de l'élément."));
			}
		}
		//Copy the large image
		//$tmp = explode("/",$items->image_file_large);
		$fileName = $photo->image_file_large;
		if($fileName=="imgphoto_large_no_image.jpg"){
			$destLargeFileName = "imgphoto_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgphoto_large_".$idNewPhoto."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$photo->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgphoto_large_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la grande image de l'élément."));
			}
		}
		//Copy the small image
		//$tmp = explode("/",$items->image_file_small);
		$fileName = $photo->image_file_small;
		if($fileName=="imgphoto_small_no_image.jpg"){
			$destSmallFileName = "imgphoto_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgphoto_small_".$idNewPhoto."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$photo->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgphoto_small_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la petite image de l'élément."));
			}
		}
		//Copy the icon image
		$fileName = $photo->image_file_icon;
		if($fileName=="imgphoto_icon_no_image.jpg"){
			$destIconFileName = "imgphoto_icon_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destIconFileName = $tmp[3];
			$destIconFileName = "imgphoto_icon_".$idNewPhoto."_".$destIconFileName;
			if(!copy("../".$CATALOG_folder_images.$photo->image_file_icon,"../".$CATALOG_folder_images.$destIconFileName)){
				$destIconFileName = "imgphoto_icon_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la petite image de l'élément."));
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		request ("UPDATE $TBL_gallery_photo
                SET `image_file_large`='$destLargeFileName',
                `image_file_small`='$destSmallFileName',
                `image_file_orig`='$destFileName',
                `image_file_icon`='$destIconFileName'
                WHERE id='$idNewPhoto'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'inscription des images de l'élément en base de données."));
		}
	}
}

function getPrdCatTable($result){
	$catalogueCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["subcats"]=array();
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["name"]=$row->name;
		$catalogueCat[$row->id]["description"]=$row->description;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["contain_products"]=$row->contain_products;
		$catalogueCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
		else $catalogueCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$catalogueCat[$row->id]["nbActiveProducts"]=0;
		$catalogueCat[$row->id]["nbInactiveProducts"]=0;
		$catalogueCat[$row->id]["nbTotalProducts"]=0;
	}
	return $catalogueCat;
}

function showPrdCategories($table,$idParent,$level,$actionURL){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="") $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showPrdCategories($table,$row["id"],$level+1,$actionURL);
	}
	return $returnStr;
}

function showProducts($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<a name='".$row["id"]."'>";
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie courante")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store,$TBL_catalogue_info_products.name FROM $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun produit dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the photo
				if($products->active=="Y") $active=_("Actif");
				elseif($products->active=="N") $active=_("Désactivée");
				//Get the substring to show from short_desc of the photo (100 Cars totally)
				$totalCars=strlen($active)+3; //+3 for the dash.
				if($products->ref_store!="") $totalCars+=strlen($products->ref_store)+3; //+2 for parenthesis
				$maxLength = 110;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				if(strlen($products->name)+$totalCars>$maxLength){
					$showDesc=substr($products->name, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$products->name;
				if($products->ref_store!="") $returnStr.="(".$products->ref_store.") ";
				$returnStr.=$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.=" <a href='$actionURL".$products->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showProducts($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$link);
	}
	return $returnStr;
}

function getMenusList($idCat,$link,$idLanguage,$idParent=0,$level=0){
	global $TBL_content_menuitems,$TBL_content_info_menuitems;
	//echo str_pad("getMenusList($idCat,link,language,$idParent,$level)\n",$level,"  ",STR_PAD_LEFT);
	$return = array();
	//get submenus
	$request = request(
	"SELECT `$TBL_content_info_menuitems`.`id_menuitem`,`$TBL_content_info_menuitems`.`title`,`$TBL_content_menuitems`.`type`
  		 FROM `$TBL_content_info_menuitems`,`$TBL_content_menuitems`
  		 WHERE `$TBL_content_menuitems`.`id_category`='$idCat'
  		 AND `$TBL_content_menuitems`.`id_parent`='$idParent'
  		 AND `$TBL_content_info_menuitems`.`id_menuitem`=`$TBL_content_menuitems`.`id`
  		 AND `id_language`='$idLanguage'
  		 ORDER BY `id_parent`,`ordering`"
	,$link
	);
	//echo "nb submenus: ".mysql_num_rows($request)."\n";
	$counter = 0;
	while($submenu = mysql_fetch_object($request)){
		$return[$counter]=array();
		$return[$counter]["id"]=$submenu->id_menuitem;
		$return[$counter]["type"]=$submenu->type;
		$return[$counter]["title"]=$submenu->title;
		if($submenu->type=="submenu") $return[$counter]["title"]="|_".$return[$counter]["title"];
		$return[$counter]["title"]=str_repeat("&nbsp;&nbsp;",$level+1).$return[$counter]["title"];
		if($submenu->type=="submenu") {
			$tmp = getMenusList($idCat,$link,$idLanguage,$submenu->id_menuitem,$level+1);
			$return=array_merge($return,$tmp);
			$counter+=count($tmp);
		}
		$counter++;
	}
	return $return;
}
?>
