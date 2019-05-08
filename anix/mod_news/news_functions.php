<?php
function getCatTable($result){
	$newsCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $newsCat[$lastInserted]["last"]=false;
		$newsCat[$row->id]=array();
		$newsCat[$row->id]["id"]=$row->id;
		$newsCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $newsCat[$row->id]["deletable"]=$row->deletable;
		if(isset($row->contain_items)) $newsCat[$row->id]["contain_items"]=$row->contain_items;
		$newsCat[$row->id]["ordering"]=$row->ordering;
		$newsCat[$row->id]["name"]=$row->name;
		$newsCat[$row->id]["description"]=$row->description;
		$newsCat[$row->id]["id_parent"]=$row->id_parent;
		$newsCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $newsCat[$row->id]["first"]=true;
		else $newsCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $newsCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$newsCat[$row->id]["nbActiveNews"]=0;
		$newsCat[$row->id]["nbInactiveNews"]=0;
		$newsCat[$row->id]["nbExpiredNews"]=0;
		$newsCat[$row->id]["nbAwaitingNews"]=0;
		$newsCat[$row->id]["nbArchivedNews"]=0;
		$newsCat[$row->id]["nbTotalNews"]=0;
	}
	return $newsCat;
}
function getStats($table,$link){
	global $TBL_news_categories;
	global $TBL_news_news;
	$currentDate=date("Y-m-d");
	//Get how many active news we have in each Cat
	$request = request("select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and ($TBL_news_news.active='Y' or ($TBL_news_news.active='DATE' and $TBL_news_news.from_date<=$currentDate and $TBL_news_news.to_date>=$currentDate)) group by $TBL_news_news.id_category", $link);
	//echo "select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and ($TBL_news_news.active='Y' or ($TBL_news_news='DATE' and $TBL_news_news.from_date<=$currentDate and $TBL_news_news.to_date>=$currentDate)) group by $TBL_news_news.id_category";
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbActiveNews"]=$cat->nbNews;
	}
	//Get how many inactive news we have in each Cat
	$request = request("select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and $TBL_news_news.active='N' group by $TBL_news_news.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbInactiveNews"]=$cat->nbNews;
	}
	//Get how many awaiting news we have in each Cat
	$request = request("select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and $TBL_news_news.active='DATE' and $TBL_news_news.from_date>'$currentDate' group by $TBL_news_news.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbAwaitingNews"]=$cat->nbNews;
	}
	//Get how many expired news we have in each Cat
	$request = request("select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and $TBL_news_news.active='DATE' and $TBL_news_news.to_date<'$currentDate' group by $TBL_news_news.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbExpiredNews"]=$cat->nbNews;
	}
	//Get how many archived news we have in each Cat
	$request = request("select $TBL_news_categories.id,COUNT(*) as nbNews from  $TBL_news_categories,$TBL_news_news where $TBL_news_news.id_category=$TBL_news_categories.id and $TBL_news_news.active='ARCHIVE' group by $TBL_news_news.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbArchivedNews"]=$cat->nbNews;
	}
	//Compute general values
	foreach($table as $category){
		$table[$category["id"]]["nbTotalNews"]=$category["nbActiveNews"]+$category["nbInactiveNews"]+$category["nbAwaitingNews"]+$category["nbExpiredNews"]+$category["nbArchivedNews"];
	}
	return $table;
}

function getOtherCatTable($result,$prohibited){
	$newsCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if($row->id!=$prohibited && ($row->id_parent==0 || isset($newsCat[$row->id_parent]))){
			if(!$first && $lastParent==$row->id_parent) $newsCat[$lastInserted]["last"]=false;
			$newsCat[$row->id]=array();
			$newsCat[$row->id]["id"]=$row->id;
			$newsCat[$row->id]["subcats"]=array();
			$newsCat[$row->id]["ordering"]=$row->ordering;
			$newsCat[$row->id]["name"]=$row->name;
			$newsCat[$row->id]["description"]=$row->description;
			$newsCat[$row->id]["id_parent"]=$row->id_parent;
			$newsCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $newsCat[$row->id]["first"]=true;
			else $newsCat[$row->id]["first"]=false;
			if($row->id_parent!=0) $newsCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
	}
	return $newsCat;
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
 * Display the news categories (used to list categories or to choose a catgeory where to copy or move....)
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
		$returnStr.=$row["name"]."(".$row["nbTotalNews"].")";
		$returnStr.="</td>";
		$returnStr.="<td width='130' align='left' valign='middle'>";
		if($row["nbTotalNews"]){
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbAwaitingNews"]*$unitsPerPixel."' alt='".$row["nbAwaitingNews"]." "._("Nouvelle(s) en attente")."(".$row["nbAwaitingNews"]*100/$row["nbTotalNews"]."%)'>";
			$returnStr.="<img src='../images/bar_green.jpg' height='5' width='".$row["nbActiveNews"]*$unitsPerPixel."' alt='".$row["nbActiveNews"]." "._("Nouvelle(s) actives")."(".$row["nbActiveNews"]*100/$row["nbTotalNews"]."%)'>";
			$returnStr.="<img src='../images/bar_orange.jpg' height='5' width='".$row["nbExpiredNews"]*$unitsPerPixel."' alt='".$row["nbExpiredNews"]." "._("Nouvelle(s) expirées")."(".$row["nbExpiredNews"]*100/$row["nbTotalNews"]."%)'>";
			$returnStr.="<img src='../images/bar_red.jpg' height='5' width='".$row["nbInactiveNews"]*$unitsPerPixel."' alt='".$row["nbInactiveNews"]." "._("Nouvelle(s) inactives")."(".$row["nbInactiveNews"]*100/$row["nbTotalNews"]."%)'>";
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbArchivedNews"]*$unitsPerPixel."' alt='".$row["nbArchivedNews"]." "._("Nouvelle(s) archivées")."(".$row["nbArchivedNews"]*100/$row["nbTotalNews"]."%)'>";
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

function showNews($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_info_news;
	global $TBL_news_news;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
			$returnStr.="<td style='width:122px;background:#e7eff2;text-align:right;'>";
			if($row["contain_items"]=="Y") $returnStr.="<a href='./mod_news.php?action=add&idCat=".$row["id"]."'><img src='../images/add.gif' alt=\""._("Ajouter une nouvelle à cette catégorie.")."\" /></a>";
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
			$maxOrder = getMaxNewsOrder($showCategory,$link)-1;
			$request = request("SELECT $TBL_news_news.id,$TBL_news_news.ordering,$TBL_news_news.active,$TBL_news_news.from_date,$TBL_news_news.to_date,$TBL_news_info_news.date,$TBL_news_info_news.title FROM $TBL_news_news,$TBL_news_info_news,$TBL_gen_languages WHERE $TBL_news_news.id_category='$showCategory' and $TBL_news_news.id=$TBL_news_info_news.id_news and $TBL_news_info_news.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_news.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucune nouvelle dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($news=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>";
				if($showButtons){
					$returnStr.="<a href='./mod_news.php?action=edit&idNews=".$news->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la nouvelle")."\"></a>";
					$returnStr.="<a href='./copy_news.php?idNews=".$news->id."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la nouvelle")."\"></a>";
					$returnStr.="<a href='./move_news.php?idNews=".$news->id."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer la nouvelle")."\"></a>";
					$returnStr.="<a href='./del_news.php?idNews=".$news->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la nouvelle")."\"></a>";
					if($news->ordering!=1) $returnStr.="<a href='./list_news.php?action=moveup&idCat=$showCategory&idNews=".$news->id."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($news->ordering<$maxOrder) $returnStr.="<a href='./list_news.php?action=movedown&idCat=$showCategory&idNews=".$news->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				$returnStr.="</td>";
				$returnStr.="<td>";
				//Get the activation status of the news
				if($news->active=="Y") $active=_("Active");
				elseif($news->active=="N") $active=_("Désactivée");
				elseif($news->active=="ARCHIVE") $active=_("Archivée");
				elseif($news->active=="DATE") {
					$currentDate=date("Y-m-d");
					if($currentDate<$news->from_date) $active=_("En attente");
					elseif($currentDate>$news->to_date) $active=_("Expirée");
					else $active=_("Active");
				}
				//Get the substring to show from short_desc of the news (100 Cars totally)
				$totalCars =strlen($news->date)+2; //+2 for parenthesis.
				$totalCars+=strlen($active)+3; //+3 for the dash.
				$maxLength = 110;
				if(strlen($news->title)+$totalCars>$maxLength){
					$showDesc=substr($news->title, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$news->title;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$returnStr.="(".$news->date.") ".$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showNews($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
	}
	return $returnStr;
}
function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_info_news;
	global $TBL_news_news;
	global $TBL_gen_languages;
	$category=request("select $TBL_news_categories.id,$TBL_news_info_categories.name,$TBL_news_categories.id_parent from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_categories.id='$idFirstParent' and $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_news_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_news_categories.id,$TBL_news_info_categories.name,$TBL_news_categories.id_parent from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_categories.id='".$row->id_parent."' and $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_news_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_info_news;
	global $TBL_news_news;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_news_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxNewsOrder($idCat,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_info_news;
	global $TBL_news_news;
	$request=request("SELECT id FROM $TBL_news_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_news_news where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_news;
	global $TBL_news_info_news;
	global $TBL_news_attachments;
	global $CATALOG_folder_images;
	global $CATALOG_folder_attachments;
	global $ANIX_messages;

	foreach($table[$idCat]["subcats"] as $subcategory){
		deleteCategory($subcategory,$table,$thread,$link);
	}
	//Delete the item'images from the file system
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT image_file_orig, image_file_large,image_file_small,image_file_icon from $TBL_news_news where id_category=$idCat",$link);
		while($images=mysql_fetch_object($request)) {
			if($images->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'image de la nouvelle."));
				}
			}
			if($images->image_file_small!="imgnews_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de la nouvelle."));
				}
			}
			if($images->image_file_large!="imgnews_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image de la nouvelle."));
				}
			}
			if($images->image_file_icon!="imgnews_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone de la nouvelle."));
				}
			}
		}
	}
	//Delete the item attachment files from the file system (items and category)
	if(!$ANIX_messages->nbErrors){
		//retrieve and delete news attachments from file system
		$request=request("SELECT file_name from $TBL_news_attachments,$TBL_news_news where $TBL_news_news.id_category='$idCat' AND $TBL_news_attachments.id_news=$TBL_news_news.id",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression du fichier attaché à l'élément."));
				}
			}
		}
		//retrieve and delete category attachments from file system
		$request=request("SELECT file_name from $TBL_news_attachments where id_category='$idCat'",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression du fichier attaché à l'élément."));
				}
			}
		}
		//Delete items attachments from database
		request("DELETE $TBL_news_attachments
               FROM $TBL_news_news,
                    $TBL_news_attachments
               WHERE $TBL_news_news.id_category='$idCat'
               AND $TBL_news_attachments.id_news=$TBL_news_news.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression des fichiers attachés."));
		}
		//Delete category attachments from database
		request("DELETE FROM $TBL_news_attachments
               WHERE id_category='$idCat'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des fichiers attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_news_news,$TBL_news_info_news
               FROM $TBL_news_news,$TBL_news_info_news
               WHERE $TBL_news_news.id_category='$idCat'
               AND $TBL_news_info_news.id_news = $TBL_news_news.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des nouvelles reliées à la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_news_categories,$TBL_news_info_categories
               FROM  $TBL_news_categories,$TBL_news_info_categories
               WHERE $TBL_news_categories.id='$idCat'
               AND $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression de la catégorie."));
		}
	}
	//re-order the category
	if(!$ANIX_messages->nbErrors && $idCat==$thread){
		request("UPDATE $TBL_news_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre des catégories."));
		}
	}
}

function copyCategory($idCat,$copyTo,$table,$thread,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_news;
	global $anix_username;
	global $ANIX_messages;

	//Check if the category exists
	$request = request("SELECT * FROM `$TBL_news_categories` WHERE `id`='$idCat'",$link);
	if(!mysql_num_rows($request)){
		$return["errors"]++;
		$return["errMessage"]=_("La catégorie spécifiée n'existe pas.");
		return $return;
	} else {
		$originalCat = mysql_fetch_object($request);
	}
	$ordering=getMaxCategoryOrder($copyTo,$link );
	if(!$ANIX_messages->nbErrors){
		request("INSERT INTO `$TBL_news_categories`
						(`ordering`,
						 `id_parent`,
						 `id_menu`,
						 `newsimg_icon_width`,
						 `newsimg_icon_height`,
						 `newsimg_small_width`,
						 `newsimg_small_height`,
						 `newsimg_large_width`,
						 `newsimg_large_height`,
						 `alias_prepend`,
						 `alias_news_prepend`,
						 `created_on`,
						 `created_by`,
						 `modified_on`,
						 `modified_by`)
				VALUES ('$ordering',
						'$copyTo',
						'".$originalCat->id_menu."',
						'".$originalCat->newsimg_icon_width."',
						'".$originalCat->newsimg_icon_height."',
						'".$originalCat->newsimg_small_width."',
						'".$originalCat->newsimg_small_height."',
						'".$originalCat->newsimg_large_width."',
						'".$originalCat->newsimg_large_height."',
						'".$originalCat->alias_prepend."',
						'".$originalCat->alias_news_prepend."',
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
		request("INSERT INTO $TBL_news_info_categories (`id_news_cat`,`id_language`,`name`,`description`,`htmltitle`,`htmldescription`,`keywords`,`alias_name`) SELECT '$newCatId',id_language,name,description,htmltitle,htmldescription,keywords,alias_name FROM $TBL_news_info_categories WHERE id_news_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie des informations de la catégorie."));
		}
	}
	//Copy the news of the category
	if(!$ANIX_messages->nbErrors){
		$newsList=request("SELECT * from $TBL_news_news where id_category=$idCat",$link);
		while($news=mysql_fetch_object($newsList)){
			copyNews($news->id,$newCatId,0,$link);
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Recursive copy of the subcategories
		foreach($table[$idCat]["subcats"] as $subcategory){
			copyCategory($subcategory,$newCatId,$table,$thread,$link);
		}
	}
}

//Copy news to specified category
// if conserve the same ordering, put 0
function copyNews($idNews,$copyTo,$ordering,$link){
	global $TBL_news_news;
	global $TBL_news_info_news;
	global $TBL_news_attachments;
	global $CATALOG_folder_images;
	global $CATALOG_folder_attachments;
	global $anix_username;
	global $ANIX_messages;

	$request = request("SELECT * from $TBL_news_news where id='$idNews'",$link);
	if(mysql_num_rows($request)){
		$news=mysql_fetch_object($request);
	} else {
		$ANIX_messages->addError(_("La nouvelle n'existe pas."));
	}
	if(!$ANIX_messages->nbErrors){
		if(!$ordering) $ordering=$news->ordering;
		request("INSERT INTO `$TBL_news_news` (`id_category`,`active`,`from_date`,`to_date`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$copyTo','".$news->active."','".$news->from_date."','".$news->to_date."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie de la nouvelle")." $idNews");
		} else {
			$idNewNews=mysql_insert_id($link);
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("INSERT INTO `$TBL_news_info_news` (`id_news`,`id_language`,`title`,`date`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`,`alias_name`) SELECT '$idNewNews',id_language,title,date,short_desc,details,keywords,htmltitle,htmldescription,alias_name FROM $TBL_news_info_news WHERE id_news='$idNews'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la copie des informations de la nouvelle")." $idNews.");
		}
	}
	//Copy the images for the new news
	if(!$ANIX_messages->nbErrors){
		//Get the file name
		$destFileName ="";
		$destLargeFileName = "";
		$destSmallFileName = "";
		$destIconFileName = "";
		//Copy the original image
		$fileName = $news->image_file_orig;
		if($fileName==""){
			$destFileName = "";
		} else {
			$tmp = explode("_",$fileName,4);
			$destFileName = $tmp[3];
			$destFileName = "imgnews_orig_".$idNewNews."_".$destFileName;
			if(!copy("../".$CATALOG_folder_images.$news->image_file_orig,"../".$CATALOG_folder_images.$destFileName)){
				$destFileName = "";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la grande image de l'élément."));
			}
		}
		//Copy the large image
		//$tmp = explode("/",$items->image_file_large);
		$fileName = $news->image_file_large;
		if($fileName=="imgnews_large_no_image.jpg"){
			$destLargeFileName = "imgnews_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgnews_large_".$idNewNews."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$news->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgnews_large_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la grande image de l'élément."));
			}
		}
		//Copy the small image
		//$tmp = explode("/",$items->image_file_small);
		$fileName = $news->image_file_small;
		if($fileName=="imgnews_small_no_image.jpg"){
			$destSmallFileName = "imgnews_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgnews_small_".$idNewNews."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$news->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgnews_small_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la petite image de l'élément."));
			}
		}
		//Copy the icon image
		$fileName = $news->image_file_icon;
		if($fileName=="imgnews_icon_no_image.jpg"){
			$destIconFileName = "imgnews_icon_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destIconFileName = $tmp[3];
			$destIconFileName = "imgnews_icon_".$idNewNews."_".$destIconFileName;
			if(!copy("../".$CATALOG_folder_images.$news->image_file_icon,"../".$CATALOG_folder_images.$destIconFileName)){
				$destIconFileName = "imgnews_icon_no_image.jpg";
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie de la petite image de l'élément."));
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		request ("UPDATE $TBL_news_news
                SET `image_file_large`='$destLargeFileName',
                `image_file_small`='$destSmallFileName',
                `image_file_orig`='$destFileName',
                `image_file_icon`='$destIconFileName'
                WHERE id='$idNewNews'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'inscription des images de l'élément en base de données."));
		}
	}
	//copy the attachments
	if(!$ANIX_messages->nbErrors){
		$request = request("SELECT * from `$TBL_news_attachments` WHERE id_news='$idNews'",$link);
		while($attachment=mysql_fetch_object($request)){
			request("INSERT INTO $TBL_news_attachments (`id_news`,`id_language`,`file_name`,`title`,`description`,`ordering`) VALUES ('$idNewNews','".$attachment->id_language."','','".$attachment->title."','".$attachment->description."','".$attachment->ordering."')",$link);
			$newAttachment = mysql_insert_id($link);
			if($attachment->file_name!=""){
				//We copy the file
				//$tmp = explode("/",$attachment->file_name);
				$fileName = $attachment->file_name;
				$tmp = explode("_",$fileName,2);
				$destFileName="news".$newAttachment."_".$tmp[1];
				if(!copy("../".$CATALOG_folder_attachments.$attachment->file_name,"../".$CATALOG_folder_attachments.$destFileName)){
					$destFileName = "";
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie du fichier attaché."));
				}
			}
			if($destFileName!=""){
				request("UPDATE $TBL_news_attachments set `file_name`='$destFileName' WHERE id='$newAttachment'",$link);
				if(mysql_errno($link)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la copie du fichier attaché."));
				}
			}
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
				//Get the activation status of the news
				if($products->active=="Y") $active=_("Actif");
				elseif($products->active=="N") $active=_("Désactivée");
				//Get the substring to show from short_desc of the news (100 Cars totally)
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
