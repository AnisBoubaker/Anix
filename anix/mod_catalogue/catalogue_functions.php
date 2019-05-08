<?php
function getCatTable($result){
	$catalogueCat=array();
	$updateSubcatsPool = array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $catalogueCat[$row->id]["deletable"]=$row->deletable;
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["name"]=$row->name;
		$catalogueCat[$row->id]["description"]=$row->description;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["contain_products"]=$row->contain_products;
		$catalogueCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
		else $catalogueCat[$row->id]["first"]=false;
		if($row->id_parent!=0) {
			//Declare the child to his parent
			if(isset($catalogueCat[$row->id_parent])) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			else {
				//if the parent have not been processed yet, put the declaration in updaye pool; declaration will be made later on
				$updateSubcatsPool[$row->id]=array();
				$updateSubcatsPool[$row->id]["child"]=$row->id;
				$updateSubcatsPool[$row->id]["parent"]=$row->id_parent;
			}
		}
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$catalogueCat[$row->id]["nbActiveProducts"]=0;
		$catalogueCat[$row->id]["nbInactiveProducts"]=0;
		$catalogueCat[$row->id]["nbTotalProducts"]=0;
	}
	foreach($updateSubcatsPool as $toUpdate){
		$catalogueCat[$toUpdate["parent"]]["subcats"][$toUpdate["child"]]=$toUpdate["child"];
	}
	return $catalogueCat;
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

function isInTree($table,$idCat,$idNode){
	if($idCat==$idNode) return true;
	if($idCat && $table[$idCat]["id_parent"]==$idNode) return true;
	if($idNode){
		foreach($table[$idNode]["subcats"] as $subNode){
			if(isInTree($table,$idCat,$subNode)) return true;
		}
	} else return true; //Because every node is in the root node

	return false;
}

function getOtherCatTable($result,$prohibited){
	$catalogueCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if($row->id!=$prohibited && ($row->id_parent==0 || isset($catalogueCat[$row->id_parent]))){
			if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
			$catalogueCat[$row->id]=array();
			$catalogueCat[$row->id]["id"]=$row->id;
			$catalogueCat[$row->id]["subcats"]=array();
			$catalogueCat[$row->id]["ordering"]=$row->ordering;
			$catalogueCat[$row->id]["name"]=$row->name;
			$catalogueCat[$row->id]["description"]=$row->description;
			$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
			$catalogueCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
			else $catalogueCat[$row->id]["first"]=false;
			if($row->id_parent!=0) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
	}
	return $catalogueCat;
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

function getProductRef($refPattern,$idPrd,$idCat){
	$generatedRef = $refPattern;
	if($idPrd) $generatedRef = str_replace("%idPrd%",$idPrd,$generatedRef);
	if($idCat) $generatedRef = str_replace("%idCat%",$idCat,$generatedRef);
	$timeStamp=time();
	$YY = date("y",$timeStamp);
	$YYYY = date("Y",$timeStamp);
	$MM = date("m",$timeStamp);
	$tmp = date("n",$timeStamp);
	$M = chr(65+$tmp-1);
	$DD = date("d",$timeStamp);
	$DDD = date("z",$timeStamp);
	$generatedRef = str_replace("%YY%",$YY,$generatedRef);
	$generatedRef = str_replace("%YYYY%",$YYYY,$generatedRef);
	$generatedRef = str_replace("%MM%",$MM,$generatedRef);
	$generatedRef = str_replace("%M%",$M,$generatedRef);
	$generatedRef = str_replace("%DD%",$DD,$generatedRef);
	$generatedRef = str_replace("%DDD%",$DDD,$generatedRef);
	return $generatedRef;
}
function showCategories($table,$idParent,$level,$actionURL,$showButtons,$linkToNonProductCategories){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr valign='middle'>";
		$returnStr.="<td align='left' valign='middle' width='122' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./move_category.php?idCat=".$row["id"]."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer")."\"></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		$link=true;
		if($row["contain_products"]=='N' && $linkToNonProductCategories==false) {
			$link=false;
			$returnStr.="<font color='black'>";
		}
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		$id = $row["id"];
		$actionURLChanged=str_replace("%%ID_CAT%%",$id,$actionURL);
		if($actionURL!="" && $link) $returnStr.="<a href=\"".$actionURLChanged."\">";
		$returnStr.=$row["name"];
		if($actionURL!="" && $link) $returnStr.="</a>";
		if(!$link) echo "</font>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategories($table,$row["id"],$level+1,$actionURL,$showButtons,$linkToNonProductCategories);
	}
	return $returnStr;
}

function showBrands($link){
	global $TBL_catalogue_brands;
	$returnStr="";
	$request = request("SELECT * from $TBL_catalogue_brands order by name",$link);
	if(!mysql_num_rows($request)){
		$returnStr.="<center><i>"._("Aucune marque n'a été trouvée en base de données...")."</i></center>";
	}
	while($brand=mysql_fetch_object($request)){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' valign='middle' width='42' bgcolor='#e7eff2'>";
		$returnStr.="<a href='./mod_brand.php?action=edit&idBrand=".$brand->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la marque")."\"></a>";
		$returnStr.="&nbsp;<a href='./del_brand.php?idBrand=".$brand->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la marque")."\"></a>";
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.=$brand->name;
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
	}
	return $returnStr;
}

function showFeaturedList($link){
	global $TBL_catalogue_featured;
	global $TBL_catalogue_info_featured;
	global $featuredCategories;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach ($featuredCategories as $category){
		$request = request("SELECT $TBL_catalogue_featured.id,$TBL_catalogue_featured.active,$TBL_catalogue_featured.from_date,$TBL_catalogue_featured.to_date,$TBL_catalogue_featured.ordering,$TBL_catalogue_info_featured.title FROM $TBL_catalogue_featured,$TBL_catalogue_info_featured,$TBL_gen_languages WHERE $TBL_catalogue_featured.id_category='".$category["id"]."' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_featured.id_featured=$TBL_catalogue_featured.id AND $TBL_catalogue_info_featured.id_language=$TBL_gen_languages.id order by ordering",$link);
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='right' valign='middle' width='82' bgcolor='#e7eff2'>";
		if($category["nbAllowed"]==-1 || mysql_num_rows($request)<$category["nbAllowed"]){
			$returnStr.="<a href='./mod_featured.php?action=add&idCategory=".$category["id"]."'><img src='../images/add.gif' border='0' alt=\""._("Ajouter une vedette")."\"></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\"><B>".$category["name"]."</B>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if(mysql_num_rows($request)){
			$request2=request("SELECT MAX(ordering) as maximum FROM $TBL_catalogue_featured WHERE id_category='".$category["id"]."' GROUP BY id_category",$link);
			$tmp = mysql_fetch_object($request2);
			$maxOrdering=$tmp->maximum;
			while($featured=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' valign='middle' width='82' bgcolor='#e7eff2'>";
				$returnStr.="<a href='./mod_featured.php?action=edit&idCategory=".$category["id"]."&idFeatured=".$featured->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la vedette")."\"></a>";
				$returnStr.="&nbsp;<a href='./del_featured.php?idCategory=".$category["id"]."&idFeatured=".$featured->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la vedette")."\"></a>";
				if($featured->ordering!=1) $returnStr.="<a href='./list_featured.php?action=moveup&idFeatured=".$featured->id."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
				else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				if($featured->ordering!=$maxOrdering) $returnStr.="<a href='./list_featured.php?action=movedown&idFeatured=".$featured->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
				else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				$returnStr.="</td>";
				$returnStr.="<td>";
				$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;".$featured->title;
				//Get the activation status of the news
				if($featured->active=="Y") $active=_("Active");
				elseif($featured->active=="N") $active=_("Désactivée");
				elseif($featured->active=="DATE") {
					$currentDate=date("Y-m-d");
					if($currentDate<$featured->from_date) $active=_("En attente");
					elseif($currentDate>$featured->to_date) $active=_("Expirée");
					else $active=_("Active");
				}
				$returnStr.=" - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		} else {
			$returnStr.="<tr>";
			$returnStr.="<td align='left' valign='middle' width='42' bgcolor='#e7eff2'>&nbsp;</td>";
			$returnStr.="<td align='center'>";
			$returnStr.="<i>"._("Aucune vedette dans cette catégorie")."</i>";
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		$returnStr.="</table>";
	}
	return $returnStr;
}

function showCategoriesExcept($table,$idParent,$level,$actionURL,$showButtons,$idProhibited){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="<td align='right' valign='middle'>";
		if($showButtons){
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
		}
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategoriesExcept($table,$row["id"],$level+1,$actionURL,$showButtons,$idProhibited);
	}
	return $returnStr;
}

function showCategoriesExceptTree($table,$idParent,$level,$actionURL,$showButtons,$idProhibited,$idTreeProhibited){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && !isInTree($table,$row["id"],$idTreeProhibited) && $row["id"]!=$idProhibited) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && !isInTree($table,$row["id"],$idTreeProhibited) && $row["id"]!=$idProhibited) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="<td align='right' valign='middle'>";
		if($showButtons){
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
		}
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategoriesExceptTree($table,$row["id"],$level+1,$actionURL,$showButtons,$idProhibited,$idTreeProhibited);
	}
	return $returnStr;
}

/**
 * Returns true if $idCategory is a child of $idParent (or if $idCategory and $idParent are the same)
 * $table must contain the categories table returned by getCatTable()
 *
 * @param Array $table
 * @param unknown_type $idCategory
 * @param unknown_type $idParent
 * @return unknown
 */
function isChild($table, $idCategory, $idParent){
	if($idParent==$idCategory) return true;
	if(!isset($table[$idParent]["subcats"]) || !count($table[$idParent]["subcats"])) return false;
	foreach($table[$idParent]["subcats"] as $currentCat){
		if(isChild($table,$idCategory,$currentCat)) return true;
	}
	return false;
}

function showProducts($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link,$checkboxes=true){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products,$TBL_catalogue_brands;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){

		//Decide if we display the category
		//If it's a top level category or the category to display is a child of the current category
		if(!$level || isChild($table,$showCategory,$row["id"])) $displayTable = true;
		//If it's a category at at the same level
		elseif(isset($table[$row["id_parent"]]) && isset($table[$row["id_parent"]]["subcats"]) && isset($table[$row["id_parent"]]["subcats"][$showCategory])) $displayTable=true;
		elseif(isset($table[$showCategory]["subcats"]) && isset($table[$showCategory]["subcats"][$row["id"]])) $displayTable=true;
		else $displayTable=false;
		if(!$displayTable) $style="display:none;";
		else $style="";
		$returnStr.="<div class='product_list' style='$style;'>";
		/**
		 * Display the Category Action Icons
		 */
		$returnStr.="<div>";
		$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
		//$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
		//$returnStr.="<a href='./move_category.php?idCat=".$row["id"]."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer")."\"></a>";
		$returnStr.="<a href='javascript:void(0);' onclick=\"javascript:anixPopup('./select_catalogue_cat.php?action=copyCategory&idCat=".$row["id"]."')\"><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
		$returnStr.="<a href='javascript:void(0);' onclick=\"javascript:anixPopup('./select_catalogue_cat.php?action=moveCategory&idCat=".$row["id"]."')\"><img src='../images/move.gif' border='0' alt=\""._("Déplacer")."\"></a>";
		if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
		else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		if($row["contain_products"]=="Y"){
			$returnStr.="<a href='./mod_product.php?action=add&idCat=".$row["id"]."'><img src='../images/add_product.gif' alt=\""._("Ajouter un produit à cette catégorie.")."\" /></a>";
		} else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		$returnStr.="<a href='./mod_category.php?action=add&idCat=".$row["id"]."'><img src='../images/add_category.gif' alt=\""._("Ajouter une sous catégorie de produits.")."\" /></a>";
		if(!$row["first"]) $returnStr.="<a href='./list_products.php?action=moveCatUp&move=".$row["id"]."&idCat=$showCategory'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
		else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		if(!$row["last"]) $returnStr.="<a href='./list_products.php?action=moveCatDown&move=".$row["id"]."&idCat=$showCategory'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
		else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		$returnStr.="</div>";
		/**
		 * CATEGORY NAME
		 */
		//$returnStr.="<td style='margin:0px;padding:0px;'>";
		$padding = $level * 20;
		$returnStr.="<p style='padding-left:".$padding."px;'>";
		$returnStr.="<a name='".$row["id"]."'>";
		//for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</p>";
		$returnStr.="</div>";
		/**
		 * DISPLAY THE SUB-CATEGORIES
		 */
		if(count($row["subcats"])) {
			$returnStr.=showProducts($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
		}
		/**
		 * DISPLAY THE PRODUCTS
		 */
		if($showCategory==$row["id"]){
			$maxOrder = getMaxProductsOrder($showCategory,$link)-1;
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store, $TBL_catalogue_products.image_file_orig ,$TBL_catalogue_info_products.name,$TBL_catalogue_brands.name brand_name FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages) LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id) WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
			if(!mysql_num_rows($request) && !count($row["subcats"])){
				$returnStr.="<div class='product_list'>";
				$returnStr.="<div>&nbsp;</div>";
				$returnStr.="<p style='padding-left:".($padding*2)."px;'><i>"._("Aucun produit dans cette catégorie.")."</i></p>";
				$returnStr.="</div>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<div class='product_list'>";
				$returnStr.="<div>";
				if($showButtons){
					$returnStr.="<a href='./mod_product.php?action=edit&idProduct=".$products->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier le produit")."\"></a>";
					$returnStr.="<a href='javascript:void(0);' onclick=\"javascript:anixPopup('./select_catalogue_cat.php?action=copyProduct&idProduct=$products->id')\"><img src='../images/copy.gif' border='0' alt=\""._("Copier le produit")."\"></a>";
					$returnStr.="<a href='javascript:void(0);' onclick=\"javascript:anixPopup('./select_catalogue_cat.php?action=moveProduct&idProduct=$products->id')\"><img src='../images/move.gif' border='0' alt=\""._("Copier le produit")."\"></a>";
					$returnStr.="<a href='./del_product.php?idProduct=".$products->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer le produit")."\"></a>";
				}
				$returnStr.="</div>";
				$returnStr.="<p style='padding-left:".$padding."px;'>";
				//for($i=0;$i<$level+1;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$showDesc=$products->name;
				if($products->name=="") $showDesc="<i>"._("Sans Nom")."</i>";
				if($products->image_file_orig=="") $showDesc.=" <img src='../images/no_picture.jpg' alt=\""._("Pas d'image pour ce produit.")."\" style='vertical-align:middle;' />";
				if($showButtons){
					if($products->ordering!=1) $returnStr.="<a href='./list_products.php?action=moveup&idCat=$showCategory&idProduct=".$products->id."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($products->ordering<$maxOrder) $returnStr.="<a href='./list_products.php?action=movedown&idCat=$showCategory&idProduct=".$products->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				if($checkboxes) $returnStr.="<input type='checkbox' name='checkedPrd[$products->id]' />";
				if($products->active=="N") $returnStr.="<i><font color='red'><b>!"._("Désactivé")."!</b></font></i> ";
				if($products->ref_store!="") $returnStr.="(".$products->ref_store.") ";
				if($products->brand_name!=NULL) $returnStr.="<I><b>".$products->brand_name."</b> </I>- ";
				else $returnStr.="<I><b>"._("Sans Marque")."</b> </I>- ";
				$returnStr.=$showDesc;
				$returnStr.="</p>";
				$returnStr.="</div>";
			}
		}
	}
	return $returnStr;
}

function showProductsLinks($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$prohibited,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products,$TBL_catalogue_brands;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<a name='".$row["id"]."'>";
		$returnStr.="<table class='edittable_text' width='100%' rowspan='0' colspan='0'";
		//Decide if we display the category
		//If it's a top level category or the category to display is a child of the current category
		if(!$level || isChild($table,$showCategory,$row["id"])) $displayTable = true;
		//If it's a category at at the same level
		elseif(isset($table[$row["id_parent"]]) && isset($table[$row["id_parent"]]["subcats"]) && isset($table[$row["id_parent"]]["subcats"][$showCategory])) $displayTable=true;
		elseif(isset($table[$showCategory]["subcats"]) && isset($table[$showCategory]["subcats"][$row["id"]])) $displayTable=true;
		else $displayTable=false;
		if(!$displayTable) $returnStr.=" style='display:none;'";
		$returnStr.=">";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		//DISPLAY THE SUB-CATEGORIES
		if(count($row["subcats"])) {
			$returnStr.="<tr>";
			$returnStr.="<td colspan='2'>";
			$returnStr.=showProductsLinks($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$prohibited,$link);
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		//DISPLAY THE PRODUCTS
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store,$TBL_catalogue_info_products.name,$TBL_catalogue_brands.name brand_name FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages) LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id) WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
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
				for($i=0;$i<$level+1;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$showDesc=$products->name;
				if($products->name=="") $showDesc="<i>"._("Sans Nom")."</i>";
				//Get the activation status of the news
				if($products->active=="N") $returnStr.="<i><font color='red'><b>!"._("Désactivé")."!</b></font></i> ";
				if($products->ref_store!="") $returnStr.="(".$products->ref_store.") ";
				if($products->brand_name!=NULL) $returnStr.="<I><b>".$products->brand_name."</b> </I>- ";
				else $returnStr.="<I><b><font color='#505050'>"._("Sans Marque")."</font></b> </I>- ";
				$returnStr.=$showDesc;
				if($products->id!=$prohibited) $returnStr.=" <a href='$actionURL".$products->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
	}
	return $returnStr;
}

function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;
	global $TBL_gen_languages;
	$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_info_categories.name,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_categories.id='$idFirstParent' and $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_info_categories.name,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_categories.id='".$row->id_parent."' and $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getParentsPathIds($idFirstParent,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;
	global $TBL_gen_languages;
	$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories where $TBL_catalogue_categories.id='$idFirstParent'", $link);
	$row = mysql_fetch_object($category);
	$ret[$row->id] = $row->id;
	while($row->id_parent!=0){
		$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories where $TBL_catalogue_categories.id='".$row->id_parent."'", $link);
		$row = mysql_fetch_object($category);
		$ret[$row->id]=$row->id;
	}
	return $ret;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;

	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_catalogue_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxProductsOrder($idCat,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;
	$request=request("SELECT id FROM $TBL_catalogue_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_catalogue_products where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_products;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_extracategorysection;
	global $TBL_catalogue_info_extracategorysection;
	global $TBL_catalogue_extrafields;
	global $TBL_catalogue_info_extrafields;
	global $TBL_catalogue_extrafields_values;
	global $TBL_catalogue_product_prices;
	global $TBL_catalogue_attachments;
	global $TBL_catalogue_product_options;
	global $TBL_catalogue_info_options;
	global $TBL_catalogue_product_option_choices;
	global $TBL_catalogue_info_choices;
	global $TBL_catalogue_featured;
	global $CATALOG_folder_images;
	global $CATALOG_folder_attachments;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	foreach($table[$idCat]["subcats"] as $subcategory){
		$tmp = deleteCategory($subcategory,$table,$thread,$link);
		$return["errors"]+=$tmp["errors"];
		$return["errMessage"].=$tmp["errMessage"];
		$return["message"].=$tmp["message"];
	}
	//Delete the product'images from the file system
	if(!$return["errors"]){
		$request=request("SELECT image_file_orig, image_file_large,image_file_small from $TBL_catalogue_products where id_category=$idCat",$link);
		while($images=mysql_fetch_object($request)) {
			if($images->image_file_small!=""){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_orig)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image du produit.")."<br />";
				}
			}
			if($images->image_file_small!="imgprd_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_small)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image du produit.")."<br />";
				}
			}
			if($images->image_file_large!="imgprd_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_large)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la grande image du produit.")."<br />";
				}
			}
		}
	}
	//Delete the product attachment files from the file system (products and category)
	if(!$return["errors"]){
		//retrieve and delete products attachments from file system
		$request=request("SELECT file_name from $TBL_catalogue_attachments,$TBL_catalogue_products where $TBL_catalogue_products.id_category='$idCat' AND $TBL_catalogue_attachments.id_product=$TBL_catalogue_products.id",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression du fichier attaché au produit.")."<br />";
				}
			}
		}
		//retrieve and delete category attachments from file system
		$request=request("SELECT file_name from $TBL_catalogue_attachments where id_category='$idCat'",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression du fichier attaché au produit.")."<br />";
				}
			}
		}
		//Delete products attachments from database
		request("DELETE $TBL_catalogue_attachments
               FROM $TBL_catalogue_products,
                    $TBL_catalogue_attachments
               WHERE $TBL_catalogue_products.id_category='$idCat'
               AND $TBL_catalogue_attachments.id_product=$TBL_catalogue_products.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des fichiers attaché.")."<br />";
		}
		//Delete category attachments from database
		request("DELETE FROM $TBL_catalogue_attachments
               WHERE id_category='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des fichiers attaché.")."<br />";
		}
	}
	//Delete the links
	if(!$return["errors"]){
		//DELETE PRODUCTS LINKS
		$request = request("SELECT `id` FROM `$TBL_catalogue_products` WHERE `id_category`='$idCat'",$link);
		while($product = mysql_fetch_object($request)){
			Link::deleteAllLinks(1,$product->id);
		}
		//DELETE CATEGORY LINKS
		Link::deleteAllLinks(2,$idCat);
	}
	//Update the featured that links to the category or any product from the category
	if(!$return["errors"]){
		request("UPDATE $TBL_catalogue_featured
               SET `id_catalogue_cat`=0,`active`='N'
               WHERE id_catalogue_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la mise à jour des vedettes.")."<br />";
		}
		request("UPDATE `$TBL_catalogue_featured`,`$TBL_catalogue_products`
               SET `$TBL_catalogue_featured`.`id_catalogue_prd`=0,`$TBL_catalogue_featured`.`active`='N'
               WHERE `$TBL_catalogue_products`.`id_category`='$idCat'
               AND `$TBL_catalogue_featured`.`id_catalogue_prd`=`$TBL_catalogue_products`.`id`",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la mise à jour des vedettes.")."<br />";
		}
	}
	//Delete the product options
	if(!$return["errors"]){
		request("DELETE $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
               FROM $TBL_catalogue_product_option_choices,
                    $TBL_catalogue_info_choices,
                    $TBL_catalogue_product_options,
                    $TBL_catalogue_products
               WHERE $TBL_catalogue_products.id_category='$idCat'
               AND $TBL_catalogue_product_options.id_product=$TBL_catalogue_products.id
               AND $TBL_catalogue_product_option_choices.id_option=$TBL_catalogue_product_options.id
               AND $TBL_catalogue_info_choices.id_choice=$TBL_catalogue_product_option_choices.id",$link);
		if(mysql_errno($link)){
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des choix des options.")."<br />";
		}
		request("DELETE $TBL_catalogue_product_options,$TBL_catalogue_info_options
               FROM $TBL_catalogue_product_options,
                    $TBL_catalogue_info_options,
                    $TBL_catalogue_products
               WHERE $TBL_catalogue_products.id_category='$idCat'
               AND $TBL_catalogue_product_options.id_product=$TBL_catalogue_products.id
               AND $TBL_catalogue_info_options.id_option=$TBL_catalogue_product_options.id",$link);
		if(mysql_errno($link)){
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des options de produits.")."<br />";
		}
	}
	//Delete the products from the database
	if(!$return["errors"]){
		request("DELETE $TBL_catalogue_product_prices
               FROM $TBL_catalogue_products,
                    $TBL_catalogue_product_prices
               WHERE $TBL_catalogue_products.id_category='$idCat'
               AND $TBL_catalogue_product_prices.id_product=$TBL_catalogue_products.id",$link);
		request("DELETE $TBL_catalogue_products,$TBL_catalogue_info_products
               from $TBL_catalogue_products,
                    $TBL_catalogue_info_products
               WHERE $TBL_catalogue_products.id_category='$idCat'
               AND $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des produits reliés à la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the category extrasections
		request("DELETE
                $TBL_catalogue_extracategorysection,
                $TBL_catalogue_info_extracategorysection
              FROM
                $TBL_catalogue_extracategorysection,
                $TBL_catalogue_info_extracategorysection
              WHERE $TBL_catalogue_extracategorysection.id_cat='$idCat'
              AND $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des sections additionnelles de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the category extrafields values from products
		request("DELETE
                $TBL_catalogue_extrafields_values
              FROM
                $TBL_catalogue_extrafields,
                $TBL_catalogue_extrafields_values
              WHERE $TBL_catalogue_extrafields.id_cat='$idCat'
              AND $TBL_catalogue_extrafields_values.id_extrafield=$TBL_catalogue_extrafields.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des valeurs des champs additionnels.")."<br />";
		}
		//Delete the category extrafields
		request("DELETE
                $TBL_catalogue_extrafields,
                $TBL_catalogue_info_extrafields
              FROM
                $TBL_catalogue_extrafields,
                $TBL_catalogue_info_extrafields
              WHERE $TBL_catalogue_extrafields.id_cat='$idCat'
              AND $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des champs additionnels de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the images from file system
		$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_categories where id=$idCat",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_small!="imgcat_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image de la catégorie.")."<br />";
			}
		}
		if($editCategory->image_file_large!="imgcat_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la grande image de la catégorie.")."<br />";
			}
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_catalogue_categories,
                      $TBL_catalogue_info_categories
               from $TBL_catalogue_categories,
                    $TBL_catalogue_info_categories
               WHERE $TBL_catalogue_categories.id='$idCat'
               AND $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la catégorie.")."<br />";
		}
	}
	//re-order the category
	if(!$return["errors"] && $idCat==$thread){
		request("UPDATE $TBL_catalogue_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la mise à jour de l'ordre des catégories.")."<br />";
		}
	}
	return $return;
}

function moveCategory($idCat,$moveTo,$table,$link){
	global $TBL_catalogue_categories;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_catalogue_categories where id='$idCat'",$link);
	if(mysql_num_rows($request)){
		$originalCat=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"].=_("La catégorie à déplacer est invalide.")."<br />";
	}
	if(!$return["errors"]){
		if(isInTree($table,$moveTo,$idCat)){
			echo "is in tree $idCat $moveTo";
			$return["errors"]++;
			$return["errMessage"].=_("La catégorie ne peut être déplacée vers cette destination.")."<br />";
		}
	}
	if(!$return["errors"]){
		if($moveTo==$originalCat->id_parent){
			$return["errors"]++;
			$return["errMessage"].=_("La catégorie ne peut être déplacée vers cette destination.")."<br />";
		}
	}
	if(!$return["errors"]){
		//get the ordering in the destination
		$maxCatOrder = getMaxCategoryOrder($moveTo,$link);
		//Move the category
		request("UPDATE `$TBL_catalogue_categories` set ordering='$maxCatOrder',id_parent='$moveTo' where id='$idCat'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors du déplacement de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//update orderings from where we are moving
		request("UPDATE $TBL_catalogue_categories set ordering=ordering-1 where id_parent=".$originalCat->id_parent." and ordering > ".$originalCat->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors du déplacement de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		$return["message"]=_("La catégorie a été déplacée correctement.")."<br />";
	}
	return $return;
}

function copyCategory($idCat,$copyTo,$table,$thread,$extraFieldsCopy,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_products;
	global $CATALOG_folder_images;
	global $TBL_catalogue_extracategorysection;
	global $TBL_catalogue_info_extracategorysection;
	global $TBL_catalogue_extrafields;
	global $TBL_catalogue_info_extrafields;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	if($extraFieldsCopy==NULL) $extraFieldsCopy=array();
	$ordering=getMaxCategoryOrder($copyTo,$link );
	$request = request("SELECT * from $TBL_catalogue_categories where id='$idCat'",$link);
	if(mysql_num_rows($request)){
		$originalCat=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"].=_("La catégorie à copier est invalide.")."<br />";
	}
	if(!$return["errors"]){
		request("INSERT INTO $TBL_catalogue_categories
					(`ordering`,
					 `id_parent`,
					 `contain_products`,
					 `hide_products`,
					 `reference_pattern`,
					 `alias_prepend`,
					 `alias_prd_prepend`,
					 `id_menu`,
					 `productimg_icon_width`,
					 `productimg_icon_height`,
					 `productimg_small_width`,
					 `productimg_small_height`,
					 `productimg_large_width`,
					 `productimg_large_height`,
					 `created_on`,
					 `created_by`,
					 `modified_on`,
					 `modified_by`
					 )
				  VALUES
				  	('$ordering',
				  	 '$copyTo',
				  	 '".$originalCat->contain_products."',
				  	 '".$originalCat->hide_products."',
				  	 '".$originalCat->reference_pattern."',
				  	 '".$originalCat->alias_prepend."',
				  	 '".$originalCat->alias_prd_prepend."',
				  	 '".$originalCat->id_menu."',
				  	 '".$originalCat->productimg_icon_width."',
				  	 '".$originalCat->productimg_icon_height."',
				  	 '".$originalCat->productimg_small_width."',
				  	 '".$originalCat->productimg_small_height."',
				  	 '".$originalCat->productimg_large_width."',
				  	 '".$originalCat->productimg_large_height."',
				  	 '".getDBDate()."',
				  	 '$anix_username',
				  	 '".getDBDate()."',
				  	 '$anix_username'
				  	 )",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la catégorie.")."<br />";
		} else {
			$newCatId=mysql_insert_id($link);
		}
	}
	//Copy the category information
	if(!$return["errors"]){
		request("INSERT INTO $TBL_catalogue_info_categories (`id_catalogue_cat`,`id_language`,`name`,`description`,`alias_name`,`keywords`,`htmltitle`,`htmldescription`) SELECT '$newCatId',id_language,name,description,alias_name,keywords,htmltitle,htmldescription FROM $TBL_catalogue_info_categories WHERE id_catalogue_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la copie des informations de la catégorie.")."<br />";
		}
	}
	//Copy the images for the new category
	if(!$return["errors"]){
		//Get the file name
		$destLargeFileName = "";
		$destSmallFileName = "";
		//Copy the large image
		$fileName = $originalCat->image_file_large;
		if($fileName=="imgcat_large_no_image.jpg"){
			$destLargeFileName = "imgcat_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgcat_large_".$newCatId."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$originalCat->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgcat_large_no_image.jpg";
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image de la catégorie.")."<br />";
			}
		}
		//Copy the small image
		$fileName = $originalCat->image_file_small;
		if($fileName=="imgcat_small_no_image.jpg"){
			$destSmallFileName = "imgcat_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgcat_small_".$newCatId."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$originalCat->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgcat_small_no_image.jpg";
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image de la catégorie.")."<br />";
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		request ("UPDATE $TBL_catalogue_categories set `image_file_large`='$destLargeFileName',`image_file_small`='$destSmallFileName' where id='$newCatId'",$link);
		if(mysql_errno($link)){
			$return["errMessage"].=_("Une erreur s'est produite lors de l'inscription des images de la catégorie en base de données.")."<br />";
		}
	}
	//Copy the extrasections of the category
	if(!$return["errors"]){
		//copy the extrasections
		$request=request("SELECT id,id_cat,ordering from $TBL_catalogue_extracategorysection WHERE id_cat='$idCat'",$link);
		while($extrasection=mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_catalogue_extracategorysection` (`id_cat`,`ordering`) values ('$newCatId','".$extrasection->ordering."')",$link);
			if(mysql_errno($link)){
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie des sections additionnelles de la catégorie.")."<br />";
			} else {
				$newExtraSection=mysql_insert_id($link);
				request("INSERT INTO $TBL_catalogue_info_extracategorysection (`id_extrasection`,`id_language`,`name`,`value`) SELECT '$newExtraSection',id_language,name,value FROM $TBL_catalogue_info_extracategorysection WHERE id_extrasection='".$extrasection->id."'",$link);
				if(mysql_errno($link)){
					$return["errors"]++;
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie des informations de section additionnelle de la catégorie.")."<br />";
				}
			}
		}
	}
	//Copy products' extra fields
	if(!$return["errors"]){
		$request = request("SELECT id,datatype,params,ordering from $TBL_catalogue_extrafields where id_cat='$idCat'",$link);
		while($extraField=mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_catalogue_extrafields` (`datatype`,`id_cat`,`params`,`ordering`) values ('".$extraField->datatype."','$newCatId','".$extraField->params."','".$extraField->ordering."')",$link);
			if(mysql_errno($link)){
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie des champs additionnels de produits.")."<br />";
			} else {
				$newExtraField = mysql_insert_id($link);
			}
			if(!$return["errors"]){
				$extraFieldsCopy[$extraField->id]=array();
				$extraFieldsCopy[$extraField->id]["original"]=$extraField->id;
				$extraFieldsCopy[$extraField->id]["copy"]=$newExtraField;
				request("INSERT INTO $TBL_catalogue_info_extrafields (`id_extrafield`,`id_language`,`name`,`selection_values`) SELECT '$newExtraField',id_language,name,selection_values FROM $TBL_catalogue_info_extrafields WHERE id_extrafield='".$extraField->id."'",$link);
				if(mysql_errno($link)){
					$return["errors"]++;
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie des informations des champs additionnels de produits.")."<br />";
				}
			}
		}
	}
	//Copy the products of the category
	if(!$return["errors"]){
		$productsList=request("SELECT * from $TBL_catalogue_products where id_category=$idCat",$link);
		while($products=mysql_fetch_object($productsList)){
			$tmp=copyProduct($products->id,$newCatId,0,$extraFieldsCopy,$originalCat->reference_pattern,$link);
			$return["errors"]+=$tmp["errors"];
			$return["errMessage"].=$tmp["errMessage"];
			$return["message"].=$tmp["message"];
		}
	}
	if(!$return["errors"]){
		//Recursive copy of the subcategories
		foreach($table[$idCat]["subcats"] as $subcategory){
			$tmp = copyCategory($subcategory,$newCatId,$table,$thread,$extraFieldsCopy,$link);
			$return["errors"]+=$tmp["errors"];
			$return["errMessage"].=$tmp["errMessage"];
			$return["message"].=$tmp["message"];
		}
	}
	return $return;
}

// Copy product to specified category
// if conserve the same ordering, put 0
// $copiedExtrafields is a boolean and means that we do copied the extrafields
// of the category and we should use the matching copy_from field of the extrafield.
function copyProduct($idProduct,$copyTo,$ordering,$copiedExtraFields,$refPattern,$link){
	global $TBL_catalogue_products;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_extrafields_values;
	global $CATALOG_folder_images;
	global $TBL_catalogue_product_prices;
	global $TBL_catalogue_attachments;
	global $CATALOG_folder_attachments;
	global $CATALOG_default_products_ref;
	global $TBL_catalogue_product_options;
	global $TBL_catalogue_info_options;
	global $TBL_catalogue_product_option_choices;
	global $TBL_catalogue_info_choices;
	global $generateRefOnCopy;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_catalogue_products where id='$idProduct'",$link);
	if(mysql_num_rows($request)){
		$products=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"]=_("Le produit n'existe pas.")."<br />";
	}
	if(!$return["errors"]){
		if(!$ordering) $ordering=$products->ordering;
		request("INSERT INTO `$TBL_catalogue_products`
					   (`id_category`,`active`,`product_type`,`is_in_special`,`ordering`,`ref_store`,`brand`,`ref_manufacturer`,`url_manufacturer`,`upc_code`,`dim_W`,`dim_H`,`dim_L`,`weight`,`public_price`,`ecotaxe`,`public_special`,`special_price`,`stock`,`restocking_delay`,`id_supplier1`,`cost_supplier1`,`id_supplier2`,`cost_supplier2`,`id_supplier3`,`cost_supplier3`,`id_supplier4`,`cost_supplier4`,`created_on`,`created_by`,`modified_on`,`modified_by`)
				VALUES ('$copyTo','".$products->active."','".$products->product_type."','".$products->is_in_special."','$ordering','".$products->ref_store."','".$products->brand."','".$products->ref_manufacturer."','".$products->url_manufacturer."','".$products->upc_code."','".$products->dim_W."','".$products->dim_H."','".$products->dim_L."','".$products->weight."','".$products->public_price."','".$products->ecotaxe."','".$products->public_special."','".$products->special_price."','".$products->stock."','".$products->restocking_delay."','".$products->id_supplier1."','".$products->cost_supplier1."','".$products->id_supplier2."','".$products->cost_supplier2."','".$products->id_supplier3."','".$products->cost_supplier3."','".$products->id_supplier4."','".$products->cost_supplier4."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')
				",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie du produit")." $idProduct.<br>";
		} else {
			$idNewProduct=mysql_insert_id($link);
		}
	}
	if(!$return["errors"]){
		request("INSERT INTO `$TBL_catalogue_info_products` (`id_product`,`id_language`,`name`,`description`) SELECT '$idNewProduct',id_language,name,description FROM $TBL_catalogue_info_products WHERE id_product='$idProduct'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations du produit")." $idProduct.<br>";
		}
	}
	//Copy the images for the new product
	if(!$return["errors"]){
		//Get the file name
		$destFileName ="";
		$destLargeFileName = "";
		$destSmallFileName = "";
		$destIconFileName = "";
		//Copy the original image
		$fileName = $products->image_file_orig;
		if($fileName==""){
			$destFileName = "";
		} else {
			$tmp = explode("_",$fileName,4);
			$destFileName = $tmp[3];
			$destFileName = "imgprd_orig_".$idNewProduct."_".$destFileName;
			if(!copy("../".$CATALOG_folder_images.$products->image_file_orig,"../".$CATALOG_folder_images.$destFileName)){
				$destFileName = "";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image du produit.")."<br />";
			}
		}
		//Copy the large image
		//$tmp = explode("/",$products->image_file_large);
		$fileName = $products->image_file_large;
		if($fileName=="imgprd_large_no_image.jpg"){
			$destLargeFileName = "imgprd_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgprd_large_".$idNewProduct."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$products->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgprd_large_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image du produit.")."<br />";
			}
		}
		//Copy the small image
		//$tmp = explode("/",$products->image_file_small);
		$fileName = $products->image_file_small;
		if($fileName=="imgprd_small_no_image.jpg"){
			$destSmallFileName = "imgprd_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgprd_small_".$idNewProduct."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$products->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgprd_small_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image du produit.")."<br />";
			}
		}
		//Copy the icon image
		$fileName = $products->image_file_icon;
		if($fileName=="imgprd_icon_no_image.jpg"){
			$destIconFileName = "imgprd_icon_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destIconFileName = $tmp[3];
			$destIconFileName = "imgprd_icon_".$idNewProduct."_".$destIconFileName;
			if(!copy("../".$CATALOG_folder_images.$products->image_file_icon,"../".$CATALOG_folder_images.$destIconFileName)){
				$destIconFileName = "imgprd_icon_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image du produit.")."<br />";
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		if($generateRefOnCopy){
			$generatedRef = getProductRef($refPattern,$idNewProduct,$copyTo);
		} else {
			$generatedRef = $products->ref_store;
		}
		request ("UPDATE $TBL_catalogue_products
                SET `image_file_large`='$destLargeFileName',
                `image_file_small`='$destSmallFileName',
                `image_file_icon`='$destIconFileName',
                `image_file_orig`='$destFileName',
                `ref_store`='$generatedRef'
                WHERE id='$idNewProduct'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de l'inscription des images du produit en base de données.")."<br />";
		}
	}
	//copy the product prices
	if(!$return["errors"]){
		request("INSERT INTO `$TBL_catalogue_product_prices` (`id_product`,`id_price_group`,`price`,`is_in_special`,`special_price`) SELECT '$idNewProduct',id_price_group,price,is_in_special,special_price FROM $TBL_catalogue_product_prices WHERE id_product='$idProduct'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des prix du produit")." $idProduct.<br>";
		}
	}
	//copy the product options
	if(!$return["errors"]){
		$request=request("SELECT id,ordering from $TBL_catalogue_product_options WHERE id_product='$idProduct'",$link);
		while($option=mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_catalogue_product_options` (`id_product`,`ordering`) VALUES ('$idNewProduct','".$option->ordering."')",$link);
			if(mysql_errno($link)){
				$return["errors"]++;
				$return["errMessage"]=_("Une erreur s'est produite lors de la copie des options.")."<br />";
			} else {
				$newOption = mysql_insert_id($link);
			}
			if(!$return["errors"]){
				request("INSERT INTO `$TBL_catalogue_info_options` (`id_option`,`id_language`,`name`) SELECT '$newOption',id_language,name from $TBL_catalogue_info_options WHERE id_option='".$option->id."'",$link);
				if(mysql_errno($link)){
					$return["errors"]++;
					$return["errMessage"]=_("Une erreur s'est produite lors de la copie des options.")."<br />";
				}
			}
			if(!$return["errors"]){
				$request2 = request("SELECT id,default_choice,price_diff,price_value,price_method,ordering from $TBL_catalogue_product_option_choices where id_option='".$option->id."'",$link);
				while($choice = mysql_fetch_object($request2)){
					request("INSERT INTO `$TBL_catalogue_product_option_choices` (`id_option`,`default_choice`,`price_diff`,`price_value`,`price_method`,`ordering`) VALUES ('$newOption','".$choice->default_choice."','".$choice->price_diff."','".$choice->price_value."','".$choice->price_method."','".$choice->ordering."')",$link);
					if(mysql_errno($link)){
						$return["errors"]++;
						$return["errMessage"]=_("Une erreur s'est produite lors de la copie des choix des options.")."<br />";
					} else {
						$newChoice = mysql_insert_id($link);
					}
					if(!$return["errors"]){
						request("INSERT INTO `$TBL_catalogue_info_choices` (`id_choice`,`id_language`,`value`) SELECT '$newChoice',id_language,value from $TBL_catalogue_info_choices WHERE id_choice='".$choice->id."'",$link);
						if(mysql_errno($link)){
							$return["errors"]++;
							$return["errMessage"]=_("Une erreur s'est produite lors de la copie des choix des options.")."<br />";
						}
					}
				}
			}
		}
	}
	//copy the attachments
	if(!$return["errors"]){
		$request = request("SELECT * from `$TBL_catalogue_attachments` WHERE id_product='$idProduct'",$link);
		while($attachment=mysql_fetch_object($request)){
			request("INSERT INTO $TBL_catalogue_attachments (`id_product`,`id_language`,`file_name`,`title`,`description`,`ordering`) VALUES ('$idNewProduct','".$attachment->id_language."','','".$attachment->title."','".$attachment->description."','".$attachment->ordering."')",$link);
			$newAttachment = mysql_insert_id($link);
			if($attachment->file_name!=""){
				//We copy the file
				//$tmp = explode("/",$attachment->file_name);
				$fileName = $attachment->file_name;
				$tmp = explode("_",$fileName,2);
				$destFileName="catalogue".$newAttachment."_".$tmp[1];
				if(!copy("../".$CATALOG_folder_attachments.$attachment->file_name,"../".$CATALOG_folder_attachments.$destFileName)){
					$destFileName = "";
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
			if($destFileName!=""){
				request("UPDATE $TBL_catalogue_attachments set `file_name`='$destFileName' WHERE id='$newAttachment'",$link);
				if(mysql_errno($link)){
					$return["errMessage"]=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
		}
	}
	//Copy extrafields values matching with $copiedExtraFields table
	if(!$return["errors"] && $copiedExtraFields!=NULL){
		foreach($copiedExtraFields as $extraField){
			request("INSERT INTO `$TBL_catalogue_extrafields_values` (`id_extrafield`,`id_product`,`id_language`,`value`) SELECT '".$extraField["copy"]."','$idNewProduct',id_language,value FROM $TBL_catalogue_extrafields_values WHERE id_extrafield='".$extraField["original"]."' AND id_product='$idProduct'",$link);
		}
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des valeurs du champs additionnels du produit")." $idProduct.<br>";
		}
	}
	return $return;
}

function moveProduct($idProduct,$moveTo,$ordering,$link){
	global $TBL_catalogue_products;
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_extrafields_values;
	global $CATALOG_folder_images;
	global $TBL_catalogue_product_prices;
	global $TBL_catalogue_attachments;
	global $CATALOG_folder_attachments;
	global $CATALOG_default_products_ref;
	global $TBL_catalogue_product_options;
	global $TBL_catalogue_info_options;
	global $TBL_catalogue_product_option_choices;
	global $TBL_catalogue_info_choices;
	global $generateRefOnCopy;
	global $anix_username;

	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";

	$request = request("SELECT `id` FROM `$TBL_catalogue_categories` WHERE `id`='$moveTo'",$link);
	if(!mysql_num_rows($request)){
		$return["errors"]++;
		$return["errMessage"]=_("La catégories de déplacement du produit n'est pas valide.");
	}

	if(!$return["errors"]){
		//get the product information
		$request = request("SELECT `id`,`id_category`,`ordering` FROM `$TBL_catalogue_products` WHERE `id`='$idProduct'",$link);
		if(!mysql_num_rows($request)){
			$return["errors"]++;
			$return["errMessage"]=_("La catégories de déplacement du produit n'est pas valide.");
		} else {
			$product = mysql_fetch_object($request);
		}
	}
	if(!$return["errors"]){
		//Move the product
		request("UPDATE `$TBL_catalogue_products` SET `id_category`='$moveTo', `ordering`='$ordering' WHERE `id`='$idProduct'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors du déplacement du produit");
		}
	}
	if(!$return["errors"]){
		//re-order the products in the original category
		request("UPDATE `$TBL_catalogue_products` SET `ordering`=`ordering`-1 WHERE `id_category`='$product->id_category' AND `ordering`>'$product->ordering'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la mise à jour de l'ordre des produits dans la catégorie d'origine.");
		}
	}
	return $return;
}
function getFaqCatTable($result){
	$faqCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $faqCat[$lastInserted]["last"]=false;
		$faqCat[$row->id]=array();
		$faqCat[$row->id]["id"]=$row->id;
		$faqCat[$row->id]["subcats"]=array();
		$faqCat[$row->id]["ordering"]=$row->ordering;
		$faqCat[$row->id]["name"]=$row->name;
		$faqCat[$row->id]["description"]=$row->description;
		$faqCat[$row->id]["id_parent"]=$row->id_parent;
		$faqCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $faqCat[$row->id]["first"]=true;
		else $faqCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $faqCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$faqCat[$row->id]["nbActiveFaq"]=0;
		$faqCat[$row->id]["nbInactiveFaq"]=0;
		$faqCat[$row->id]["nbTotalFaq"]=0;
	}
	return $faqCat;
}

function showFaq($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_info_faq;
	global $TBL_faq_faq;
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
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($showCategory!=$row["id"]) $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_faq_faq.id,$TBL_faq_faq.ordering,$TBL_faq_faq.active,$TBL_faq_info_faq.question FROM $TBL_faq_faq,$TBL_faq_info_faq,$TBL_gen_languages WHERE $TBL_faq_faq.id_category='$showCategory' and $TBL_faq_faq.id=$TBL_faq_info_faq.id_faq and $TBL_faq_info_faq.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_faq.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucune question dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($faq=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the news
				if($faq->active=="Y") $active=_("Active");
				elseif($faq->active=="N") $active=_("Désactivée");
				//Get the substring to show from short_desc of the news (100 Cars totally)
				$totalCars=strlen($active)+3; //+3 for the dash.
				$maxLength = 110;
				if(strlen($faq->question)+$totalCars>$maxLength){
					$showDesc=substr($faq->question, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$faq->question;
				//$showDesc = "<a href='$actionURL".$faq->id."'>$showDesc</a>";
				$returnStr.=$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.=" <a href='$actionURL".$faq->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showFaq($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$link);
	}
	return $returnStr;
}

function showFaqCategories($table,$idParent,$level,$actionURL,$showButtons){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="") $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showFaqCategories($table,$row["id"],$level+1,$actionURL,$showButtons);
	}
	return $returnStr;
}

function getNewsCatTable($result){
	$newsCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
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
		//Stats fields
		$newsCat[$row->id]["nbActiveNews"]=0;
		$newsCat[$row->id]["nbInactiveNews"]=0;
		$newsCat[$row->id]["nbExpiredNews"]=0;
		$newsCat[$row->id]["nbAwaitingNews"]=0;
		$newsCat[$row->id]["nbTotalNews"]=0;
	}
	return $newsCat;
}

function showNews($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$link){
	global $TBL_news_categories;
	global $TBL_news_info_categories;
	global $TBL_news_info_news;
	global $TBL_news_news;
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
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_news_news.id,$TBL_news_news.ordering,$TBL_news_news.active,$TBL_news_news.from_date,$TBL_news_news.to_date,$TBL_news_info_news.date,$TBL_news_info_news.short_desc FROM $TBL_news_news,$TBL_news_info_news,$TBL_gen_languages WHERE $TBL_news_news.id_category='$showCategory' and $TBL_news_news.id=$TBL_news_info_news.id_news and $TBL_news_info_news.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_news.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucune nouvelle dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($news=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the news
				if($news->active=="Y") $active=_("Active");
				elseif($news->active=="N") $active=_("Désactivée");
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
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				if(strlen($news->short_desc)+$totalCars>$maxLength){
					$showDesc=substr($news->short_desc, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$news->short_desc;
				$returnStr.="(".$news->date.") ".$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.=" <a href='$actionURL".$news->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showNews($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$link);
	}
	return $returnStr;
}

function showNewsCategories($table,$idParent,$level,$actionURL,$showButtons){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="") $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showNewsCategories($table,$row["id"],$level+1,$actionURL,$showButtons);
	}
	return $returnStr;
}
?>
