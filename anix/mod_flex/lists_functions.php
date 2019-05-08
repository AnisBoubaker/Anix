<?php
function getCatTable($currentCat=0,$ordering='manual',$link=0){
	global $TBL_lists_categories,$TBL_lists_info_categories,$TBL_gen_languages,$used_language_id;
	$link = dbConnect();
	switch($ordering){
		case "manual":$sqlOrdering = "$TBL_lists_categories.ordering";break;
		case "alpha":$sqlOrdering = "$TBL_lists_info_categories.name";break;
		default:$sqlOrdering = "$TBL_lists_categories.ordering";
	}
	$result=request("select $TBL_lists_categories.id, $TBL_lists_categories.id_parent, $TBL_lists_categories.ordering,$TBL_lists_categories.deletable,$TBL_lists_categories.contain_items, $TBL_lists_categories.items_ordering, $TBL_lists_categories.subcats_ordering, $TBL_lists_info_categories.name, $TBL_lists_info_categories.description FROM  $TBL_lists_categories,$TBL_gen_languages,$TBL_lists_info_categories WHERE $TBL_lists_categories.id_parent='$currentCat' and $TBL_lists_info_categories.id_lists_cat=$TBL_lists_categories.id and $TBL_lists_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $sqlOrdering", $link);
	$catalogueCat=array();
	$first = true; $lastInserted = 0;
	while($row = mysql_fetch_object($result)){
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["first"]=$first;
		$catalogueCat[$row->id]["last"]=false;
		$first=false;
		$lastInserted=$row->id;

		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["subcats"]=array();
		$catalogueCat[$row->id]["deletable"]=$row->deletable;
		$catalogueCat[$row->id]["items_ordering"]=$row->items_ordering;
		$catalogueCat[$row->id]["subcats_ordering"]=$row->subcats_ordering;
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["name"]=$row->name;
		$catalogueCat[$row->id]["description"]=$row->description;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["contain_items"]=$row->contain_items;
		$catalogueCat[$row->id]["nbActiveItems"]=0;
		$catalogueCat[$row->id]["nbInactiveItems"]=0;
		$catalogueCat[$row->id]["nbTotalItems"]=0;
	}
	if(isset($catalogueCat[$lastInserted])) $catalogueCat[$lastInserted]["last"]=true;
	$childs = array();
	foreach($catalogueCat as $id=>$cat){
		//RECCURSIVE CALL
		$tmp = getCatTable($id,$cat["subcats_ordering"],$link);
		$childs=$childs+$tmp;
		foreach($tmp as $subcat){
			$catalogueCat[$cat["id"]]["subcats"][$subcat["id"]]=$subcat["id"];
		}
	}
	$catalogueCat=$catalogueCat+$childs;
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

function showCategories($table,$idParent,$level,$actionURL,$showButtons,$linkToNonItemCategories){
	$returnStr="";
	$parentOrdering = ($idParent?$table[$idParent]["subcats_ordering"]:"manual");
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		//$manualOrdering=(!$row["id_parent"] || $table[$row["id_parent"]]["subcats_ordering"]=="manual");
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr valign='middle'>";
		$returnStr.="<td align='left' valign='middle' width='82' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./move_category.php?idCat=".$row["id"]."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer")."\"></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		$link=true;
		if($row["contain_items"]=='N' && $linkToNonItemCategories==false) {
			$link=false;
			$returnStr.="<font color='black'>";
		}
		//for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/spacer.gif' style='width:".($level*2*20)."px;height:20px;' />";
		if($showButtons){
			if(!$row["first"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $link) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && $link) $returnStr.="</a>";
		if(!$link) echo "</font>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategories($table,$row["id"],$level+1,$actionURL,$showButtons,$linkToNonItemCategories);
	}
	return $returnStr;
}

function showCategoriesExcept($table,$idParent,$level,$actionURL,$showButtons,$idProhibited){
	$returnStr="";
	$parentOrdering = ($idParent?$table[$idParent]["subcats_ordering"]:"manual");
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$manualOrdering=$table[$row["id_parent"]]["subcats_ordering"]=="manual";
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
			if(!$row["first"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
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
	$parentOrdering = ($idParent?$table[$idParent]["subcats_ordering"]:"manual");
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
			if(!$row["first"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"] && $parentOrdering=="manual") $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
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

function showItems($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
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
			//Display the Add item button
			$returnStr.="<td style='width:102px;background:#e7eff2;text-align:right;'>";
			if($row["contain_items"]=="Y"){
				$returnStr.="<a href='./mod_item.php?action=add&idCat=".$row["id"]."'><img src='../images/add.gif' alt=\""._("Ajouter un élément à cette catégorie.")."\" /></a>";
			}
			$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		//DISPLAY THE SUB-CATEGORIES
		if(count($row["subcats"])) {
			$returnStr.="<tr>";
			$returnStr.="<td colspan='2'>";
			$returnStr.=showItems($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		//DISPLAY THE ITEMS
		if($showCategory==$row["id"]){
			$parentOrdering = $table[$showCategory]["items_ordering"];
			$maxOrder = getMaxItemsOrder($showCategory,$link)-1;
			switch($parentOrdering){
				case "manual":$sqlOrderBy="$TBL_lists_items.ordering";break;
				case "alpha":$sqlOrderBy="$TBL_lists_info_items.name";break;
				default:$sqlOrderBy="$TBL_lists_items.ordering";
			}
			$request = request("SELECT $TBL_lists_items.id,$TBL_lists_items.ordering,$TBL_lists_items.active,$TBL_lists_info_items.name FROM ($TBL_lists_items,$TBL_lists_info_items,$TBL_gen_languages) WHERE $TBL_lists_items.id_category='$showCategory' and $TBL_lists_items.id=$TBL_lists_info_items.id_item and $TBL_lists_info_items.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $sqlOrderBy",$link);
			if(!mysql_num_rows($request) && !count($row["subcats"])){
				$returnStr.="<tr height='20'>";
				$returnStr.="<td width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center' colspan='2'>";
				$returnStr.="<i>"._("Aucun élément dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($items=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='right' valign='middle' width='102' bgcolor='#e7eff2'>";
				if($showButtons){
					$returnStr.="<a href='./mod_item.php?action=edit&idItem=".$items->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier l'élément")."\"></a>";
					$returnStr.="<a href='./copy_item.php?idItem=".$items->id."'><img src='../images/copy.gif' border='0' alt=\""._("Copier l'élément")."\"></a>";
					$returnStr.="<a href='./del_item.php?idItem=".$items->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer l'élément")."\"></a>";
					if($parentOrdering=="manual" && $items->ordering!=1) $returnStr.="<a href='./list_items.php?action=moveup&idCat=$showCategory&idItem=".$items->id."'><img src='../images/order_up.gif' border='0' alt= alt=\""._("Monter")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($parentOrdering=="manual" && $items->ordering<$maxOrder) $returnStr.="<a href='./list_items.php?action=movedown&idCat=$showCategory&idItem=".$items->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				$returnStr.="</td>";
				$returnStr.="<td valign='middle'>";
				for($i=0;$i<$level+1;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$showDesc=$items->name;
				if($items->name=="") $showDesc="<i>"._("Sans Nom")."</i>";
				if($items->active=="N") $returnStr.="<i><font color='red'><b>!"._("Désactivé")."!</b></font></i> ";
				$returnStr.=$showDesc;
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
	}
	return $returnStr;
}

function showItemsLinks($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$prohibited,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;
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
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_items"]=="Y") $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_items"]=="Y") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		//DISPLAY THE SUB-CATEGORIES
		if(count($row["subcats"])) {
			$returnStr.="<tr>";
			$returnStr.="<td colspan='2'>";
			$returnStr.=showItemsLinks($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$prohibited,$link);
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		//DISPLAY THE ITEMS
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_lists_items.id,$TBL_lists_items.ordering,$TBL_lists_items.active,$TBL_lists_info_items.name FROM ($TBL_lists_items,$TBL_lists_info_items,$TBL_gen_languages) WHERE $TBL_lists_items.id_category='$showCategory' and $TBL_lists_items.id=$TBL_lists_info_items.id_item and $TBL_lists_info_items.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_lists_items.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun élément dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($items=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				for($i=0;$i<$level+1;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$showDesc=$items->name;
				if($items->name=="") $showDesc="<i>"._("Sans Nom")."</i>";
				//Get the activation status of the news
				if($items->active=="N") $returnStr.="<i><font color='red'><b>!"._("Désactivé")."!</b></font></i> ";
				$returnStr.=$showDesc;
				if($items->id!=$prohibited) $returnStr.=" <a href='$actionURL".$items->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
	}
	return $returnStr;
}

function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;
	global $TBL_gen_languages;
	$category=request("select $TBL_lists_categories.id,$TBL_lists_info_categories.name,$TBL_lists_categories.id_parent from  $TBL_lists_categories,$TBL_gen_languages,$TBL_lists_info_categories where $TBL_lists_categories.id='$idFirstParent' and $TBL_lists_info_categories.id_lists_cat=$TBL_lists_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_lists_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_lists_categories.id,$TBL_lists_info_categories.name,$TBL_lists_categories.id_parent from  $TBL_lists_categories,$TBL_gen_languages,$TBL_lists_info_categories where $TBL_lists_categories.id='".$row->id_parent."' and $TBL_lists_info_categories.id_lists_cat=$TBL_lists_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_lists_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getParentsPathIds($idFirstParent,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;
	global $TBL_gen_languages;
	$category=request("select $TBL_lists_categories.id,$TBL_lists_categories.id_parent from  $TBL_lists_categories where $TBL_lists_categories.id='$idFirstParent'", $link);
	$row = mysql_fetch_object($category);
	$ret[$row->id] = $row->id;
	while($row->id_parent!=0){
		$category=request("select $TBL_lists_categories.id,$TBL_lists_categories.id_parent from  $TBL_lists_categories where $TBL_lists_categories.id='".$row->id_parent."'", $link);
		$row = mysql_fetch_object($category);
		$ret[$row->id]=$row->id;
	}
	return $ret;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;

	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_lists_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxItemsOrder($idCat,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_info_items;
	global $TBL_lists_items;
	$request=request("SELECT id FROM $TBL_lists_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_lists_items where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_items;
	global $TBL_lists_info_items;
	global $TBL_lists_extracategorysection;
	global $TBL_lists_info_extracategorysection;
	global $TBL_lists_extrafields;
	global $TBL_lists_info_extrafields;
	global $TBL_lists_extrafields_values;
	global $TBL_lists_attachments;
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
	//Delete the item'images from the file system
	if(!$return["errors"]){
		$request=request("SELECT image_file_orig, image_file_large,image_file_small from $TBL_lists_items where id_category=$idCat",$link);
		while($images=mysql_fetch_object($request)) {
			if($images->image_file_small!=""){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_orig)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image de l'élément.")."<br />";
				}
			}
			if($images->image_file_small!="imgflex_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_small)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image de l'élément.")."<br />";
				}
			}
			if($images->image_file_large!="imgflex_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$images->image_file_large)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la grande image de l'élément.")."<br />";
				}
			}
		}
	}
	//Delete the item attachment files from the file system (items and category)
	if(!$return["errors"]){
		//retrieve and delete items attachments from file system
		$request=request("SELECT file_name from $TBL_lists_attachments,$TBL_lists_items where $TBL_lists_items.id_category='$idCat' AND $TBL_lists_attachments.id_item=$TBL_lists_items.id",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression du fichier attaché à l'élément.")."<br />";
				}
			}
		}
		//retrieve and delete category attachments from file system
		$request=request("SELECT file_name from $TBL_lists_attachments where id_category='$idCat'",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$return["errMessage"].=_("Une erreur s'est produite lors de la suppression du fichier attaché à l'élément.")."<br />";
				}
			}
		}
		//Delete items attachments from database
		request("DELETE $TBL_lists_attachments
               FROM $TBL_lists_items,
                    $TBL_lists_attachments
               WHERE $TBL_lists_items.id_category='$idCat'
               AND $TBL_lists_attachments.id_item=$TBL_lists_items.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des fichiers attaché.")."<br />";
		}
		//Delete category attachments from database
		request("DELETE FROM $TBL_lists_attachments
               WHERE id_category='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des fichiers attaché.")."<br />";
		}
	}
	//Delete the items from the database
	if(!$return["errors"]){
		request("DELETE $TBL_lists_items,$TBL_lists_info_items
               from $TBL_lists_items,
                    $TBL_lists_info_items
               WHERE $TBL_lists_items.id_category='$idCat'
               AND $TBL_lists_info_items.id_item=$TBL_lists_items.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des éléments reliés à la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the category extrasections
		request("DELETE
                $TBL_lists_extracategorysection,
                $TBL_lists_info_extracategorysection
              FROM
                $TBL_lists_extracategorysection,
                $TBL_lists_info_extracategorysection
              WHERE $TBL_lists_extracategorysection.id_cat='$idCat'
              AND $TBL_lists_info_extracategorysection.id_extrasection=$TBL_lists_extracategorysection.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des sections additionnelles de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the category extrafields values from items
		request("DELETE
                $TBL_lists_extrafields_values
              FROM
                $TBL_lists_extrafields,
                $TBL_lists_extrafields_values
              WHERE $TBL_lists_extrafields.id_cat='$idCat'
              AND $TBL_lists_extrafields_values.id_extrafield=$TBL_lists_extrafields.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des valeurs des champs additionnels.")."<br />";
		}
		//Delete the category extrafields
		request("DELETE
                $TBL_lists_extrafields,
                $TBL_lists_info_extrafields
              FROM
                $TBL_lists_extrafields,
                $TBL_lists_info_extrafields
              WHERE $TBL_lists_extrafields.id_cat='$idCat'
              AND $TBL_lists_info_extrafields.id_extrafield=$TBL_lists_extrafields.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression des champs additionnels de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//Delete the images from file system
		$request=request("SELECT image_file_large,image_file_small from $TBL_lists_categories where id=$idCat",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_small!="imgcatflex_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la petite image de la catégorie.")."<br />";
			}
		}
		if($editCategory->image_file_large!="imgcatflex_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la grande image de la catégorie.")."<br />";
			}
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_lists_categories,
                      $TBL_lists_info_categories
               from $TBL_lists_categories,
                    $TBL_lists_info_categories
               WHERE $TBL_lists_categories.id='$idCat'
               AND $TBL_lists_info_categories.id_lists_cat=$TBL_lists_categories.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression de la catégorie.")."<br />";
		}
	}
	//re-order the category
	if(!$return["errors"] && $idCat==$thread){
		request("UPDATE $TBL_lists_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la mise à jour de l'ordre des catégories.")."<br />";
		}
	}
	return $return;
}

function moveCategory($idCat,$moveTo,$table,$link){
	global $TBL_lists_categories;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_lists_categories where id='$idCat'",$link);
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
		request("UPDATE `$TBL_lists_categories` set ordering='$maxCatOrder',id_parent='$moveTo' where id='$idCat'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors du déplacement de la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		//update orderings from where we are moving
		request("UPDATE $TBL_lists_categories set ordering=ordering-1 where id_parent=".$originalCat->id_parent." and ordering > ".$originalCat->ordering,$link);
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
	global $TBL_lists_categories;
	global $TBL_lists_info_categories;
	global $TBL_lists_items;
	global $CATALOG_folder_images;
	global $TBL_lists_extracategorysection;
	global $TBL_lists_info_extracategorysection;
	global $TBL_lists_extrafields;
	global $TBL_lists_info_extrafields;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	if($extraFieldsCopy==NULL) $extraFieldsCopy=array();
	$ordering=getMaxCategoryOrder($copyTo,$link );
	$request = request("SELECT * from $TBL_lists_categories where id='$idCat'",$link);
	if(mysql_num_rows($request)){
		$originalCat=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"].=_("La catégorie à copier est invalide.")."<br />";
	}
	if(!$return["errors"]){
		request("INSERT INTO $TBL_lists_categories
						(`ordering`,
						`id_parent`,
						`contain_items`,
						`hide_items`,
						`alias_prepend`,
						`alias_prd_prepend`,
						`id_menu`,
						`itemimg_icon_width`,
						`itemimg_icon_height`,
						`itemimg_small_width`,
						`itemimg_small_height`,
						`itemimg_large_width`,
						`itemimg_large_height`,
						`created_on`,
						`created_by`,
						`modified_on`,
						`modified_by`)
				VALUES  ('$ordering',
						'$copyTo',
						'".$originalCat->contain_items."',
						'".$originalCat->hide_items."',
						'".$originalCat->alias_prepend."',
						'".$originalCat->alias_prd_prepend."',
						'".$originalCat->id_menu."',
						'".$originalCat->itemimg_icon_width."',
						'".$originalCat->itemimg_icon_height."',
						'".$originalCat->itemimg_small_width."',
						'".$originalCat->itemimg_small_height."',
						'".$originalCat->itemimg_large_width."',
						'".$originalCat->itemimg_large_height."',
						'".getDBDate()."',
						'$anix_username',
						'".getDBDate()."',
						'$anix_username')"
			,$link);

		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la catégorie.")."<br />";
		} else {
			$newCatId=mysql_insert_id($link);
		}
	}
	//Copy the category information
	if(!$return["errors"]){
		request("INSERT INTO $TBL_lists_info_categories (`id_lists_cat`,`id_language`,`name`,`description`,`alias_name`,`keywords`,`htmltitle`,`htmldescription`) SELECT '$newCatId',id_language,name,description,alias_name,keywords,htmltitle,htmldescription FROM $TBL_lists_info_categories WHERE id_lists_cat='$idCat'",$link);
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
		if($fileName=="imgcatflex_large_no_image.jpg"){
			$destLargeFileName = "imgcatflex_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgcatflex_large_".$newCatId."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$originalCat->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgcatflex_large_no_image.jpg";
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image de la catégorie.")."<br />";
			}
		}
		//Copy the small image
		$fileName = $originalCat->image_file_small;
		if($fileName=="imgcatflex_small_no_image.jpg"){
			$destSmallFileName = "imgcatflex_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgcatflex_small_".$newCatId."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$originalCat->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgcatflex_small_no_image.jpg";
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image de la catégorie.")."<br />";
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		request ("UPDATE $TBL_lists_categories set `image_file_large`='$destLargeFileName',`image_file_small`='$destSmallFileName' where id='$newCatId'",$link);
		if(mysql_errno($link)){
			$return["errMessage"].=_("Une erreur s'est produite lors de l'inscription des images de la catégorie en base de données.")."<br />";
		}
	}
	//Copy the extrasections of the category
	if(!$return["errors"]){
		//copy the extrasections
		$request=request("SELECT id,id_cat,ordering from $TBL_lists_extracategorysection WHERE id_cat='$idCat'",$link);
		while($extrasection=mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_lists_extracategorysection` (`id_cat`,`ordering`) values ('$newCatId','".$extrasection->ordering."')",$link);
			if(mysql_errno($link)){
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie des sections additionnelles de la catégorie.")."<br />";
			} else {
				$newExtraSection=mysql_insert_id($link);
				request("INSERT INTO $TBL_lists_info_extracategorysection (`id_extrasection`,`id_language`,`name`,`value`) SELECT '$newExtraSection',id_language,name,value FROM $TBL_lists_info_extracategorysection WHERE id_extrasection='".$extrasection->id."'",$link);
				if(mysql_errno($link)){
					$return["errors"]++;
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie des informations de section additionnelle de la catégorie.")."<br />";
				}
			}
		}
	}
	//Copy items' extra fields
	if(!$return["errors"]){
		$request = request("SELECT id,datatype,params,ordering from $TBL_lists_extrafields where id_cat='$idCat'",$link);
		while($extraField=mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_lists_extrafields` (`datatype`,`id_cat`,`params`,`ordering`) values ('".$extraField->datatype."','$newCatId','".$extraField->params."','".$extraField->ordering."')",$link);
			if(mysql_errno($link)){
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie des champs additionnels d'éléments.")."<br />";
			} else {
				$newExtraField = mysql_insert_id($link);
			}
			if(!$return["errors"]){
				$extraFieldsCopy[$extraField->id]=array();
				$extraFieldsCopy[$extraField->id]["original"]=$extraField->id;
				$extraFieldsCopy[$extraField->id]["copy"]=$newExtraField;
				request("INSERT INTO $TBL_lists_info_extrafields (`id_extrafield`,`id_language`,`name`,`selection_values`) SELECT '$newExtraField',id_language,name,selection_values FROM $TBL_lists_info_extrafields WHERE id_extrafield='".$extraField->id."'",$link);
				if(mysql_errno($link)){
					$return["errors"]++;
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie des informations des champs additionnels d'éléments.")."<br />";
				}
			}
		}
	}
	//Copy the items of the category
	if(!$return["errors"]){
		$itemsList=request("SELECT * from $TBL_lists_items where id_category=$idCat",$link);
		while($items=mysql_fetch_object($itemsList)){
			$tmp=copyItem($items->id,$newCatId,0,$extraFieldsCopy,false,$link);
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

// Copy item to specified category
// if conserve the same ordering, put 0
// $copiedExtrafields is a boolean and means that we do copied the extrafields
// of the category and we should use the matching copy_from field of the extrafield.
function copyItem($idItem,$copyTo,$ordering,$copiedExtraFields,$autoCopyExtraFields=false,$link){
	global $TBL_lists_items;
	global $TBL_lists_info_items;
	global $TBL_lists_extrafields_values;
	global $TBL_lists_extrafields;
	global $CATALOG_folder_images;
	global $TBL_lists_attachments;
	global $CATALOG_folder_attachments;
	global $CATALOG_default_items_ref;
	global $generateRefOnCopy;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_lists_items where id='$idItem'",$link);
	if(mysql_num_rows($request)){
		$items=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"]=_("L'élément n'existe pas.")."<br />";
	}
	if(!$return["errors"]){
		if(!$ordering) $ordering=$items->ordering;
		request("INSERT INTO `$TBL_lists_items` (`id_category`,`active`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$copyTo','".$items->active."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie de l'élément")." $idItem.<br>";
		} else {
			$idNewItem=mysql_insert_id($link);
		}
	}
	if(!$return["errors"]){
		request("INSERT INTO `$TBL_lists_info_items` (`id_item`,`id_language`,`name`,`description`,`alias_name`,`keywords`,`htmltitle`,`htmldescription`) SELECT '$idNewItem',id_language,name,description,alias_name,keywords,htmltitle,htmldescription FROM $TBL_lists_info_items WHERE id_item='$idItem'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations de l'élément")." $idItem.<br>";
		}
	}
	//Copy the images for the new item
	if(!$return["errors"]){
		//Get the file name
		$destFileName ="";
		$destLargeFileName = "";
		$destSmallFileName = "";
		$destIconFileName = "";
		//Copy the original image
		$fileName = $items->image_file_orig;
		if($fileName==""){
			$destFileName = "";
		} else {
			$tmp = explode("_",$fileName,4);
			$destFileName = $tmp[3];
			$destFileName = "imgflex_orig_".$idNewItem."_".$destFileName;
			if(!copy("../".$CATALOG_folder_images.$items->image_file_orig,"../".$CATALOG_folder_images.$destFileName)){
				$destFileName = "";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image de l'élément.")."<br />";
			}
		}
		//Copy the large image
		//$tmp = explode("/",$items->image_file_large);
		$fileName = $items->image_file_large;
		if($fileName=="imgflex_large_no_image.jpg"){
			$destLargeFileName = "imgflex_large_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destLargeFileName = $tmp[3];
			$destLargeFileName = "imgflex_large_".$idNewItem."_".$destLargeFileName;
			if(!copy("../".$CATALOG_folder_images.$items->image_file_large,"../".$CATALOG_folder_images.$destLargeFileName)){
				$destLargeFileName = "imgflex_large_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la grande image de l'élément.")."<br />";
			}
		}
		//Copy the small image
		//$tmp = explode("/",$items->image_file_small);
		$fileName = $items->image_file_small;
		if($fileName=="imgflex_small_no_image.jpg"){
			$destSmallFileName = "imgflex_small_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destSmallFileName = $tmp[3];
			$destSmallFileName = "imgflex_small_".$idNewItem."_".$destSmallFileName;
			if(!copy("../".$CATALOG_folder_images.$items->image_file_small,"../".$CATALOG_folder_images.$destSmallFileName)){
				$destSmallFileName = "imgflex_small_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image de l'élément.")."<br />";
			}
		}
		//Copy the icon image
		$fileName = $items->image_file_icon;
		if($fileName=="imgflex_icon_no_image.jpg"){
			$destIconFileName = "imgflex_icon_no_image.jpg";
		} else {
			$tmp = explode("_",$fileName,4);
			$destIconFileName = $tmp[3];
			$destIconFileName = "imgflex_icon_".$idNewItem."_".$destIconFileName;
			if(!copy("../".$CATALOG_folder_images.$items->image_file_icon,"../".$CATALOG_folder_images.$destIconFileName)){
				$destIconFileName = "imgflex_icon_no_image.jpg";
				$return["errors"]++;
				$return["errMessage"].=_("Une erreur s'est produite lors de la copie de la petite image de l'élément.")."<br />";
			}
		}
		//Updates the database
		//$destLargeFileName = $CATALOG_folder_images."/".$destLargeFileName;
		//$destSmallFileName = $CATALOG_folder_images."/".$destSmallFileName;
		request ("UPDATE $TBL_lists_items
                SET `image_file_large`='$destLargeFileName',
                `image_file_small`='$destSmallFileName',
                `image_file_icon`='$destIconFileName',
                `image_file_orig`='$destFileName'
                WHERE id='$idNewItem'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de l'inscription des images de l'élément en base de données.")."<br />";
		}
	}
	//copy the attachments
	if(!$return["errors"]){
		$request = request("SELECT * from `$TBL_lists_attachments` WHERE id_item='$idItem'",$link);
		while($attachment=mysql_fetch_object($request)){
			request("INSERT INTO $TBL_lists_attachments (`id_item`,`id_language`,`file_name`,`title`,`description`,`ordering`) VALUES ('$idNewItem','".$attachment->id_language."','','".$attachment->title."','".$attachment->description."','".$attachment->ordering."')",$link);
			$newAttachment = mysql_insert_id($link);
			if($attachment->file_name!=""){
				//We copy the file
				//$tmp = explode("/",$attachment->file_name);
				$fileName = $attachment->file_name;
				$tmp = explode("_",$fileName,2);
				$destFileName="flex".$newAttachment."_".$tmp[1];
				if(!copy("../".$CATALOG_folder_attachments.$attachment->file_name,"../".$CATALOG_folder_attachments.$destFileName)){
					$destFileName = "";
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
			if($destFileName!=""){
				request("UPDATE $TBL_lists_attachments set `file_name`='$destFileName' WHERE id='$newAttachment'",$link);
				if(mysql_errno($link)){
					$return["errMessage"]=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
		}
	}
	//Copy extrafields values matching with $copiedExtraFields table
	if(!$return["errors"] && $copiedExtraFields!=NULL){
		foreach($copiedExtraFields as $extraField){
			request("INSERT INTO `$TBL_lists_extrafields_values` (`id_extrafield`,`id_item`,`id_language`,`value`) SELECT '".$extraField["copy"]."','$idNewItem',id_language,value FROM $TBL_lists_extrafields_values WHERE id_extrafield='".$extraField["original"]."' AND id_item='$idItem'",$link);
		}
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des valeurs des champs additionnels de l'élément")." $idItem.<br>";
		}
	} elseif(!$return["errors"] && $autoCopyExtraFields){ //automatically determine which extrafield we want to copy
		$parentCategories= getParentsPathIds($copyTo,$link);
		//WE GET THE EXTRAFIELDS THAT APPLY TO THE NEW CATEGORY
		$requestString="select * from $TBL_lists_extrafields where (datatype='text' or datatype='selection' or datatype='date') and (";
		$first=true;
		foreach($parentCategories as $cat){
			if(!$first) $requestString.=" OR ";
			$requestString.="id_cat='$cat'";
			$first = false;
		}
		$requestString.=") order by id_cat,ordering";
		//echo $requestString;
		$request = request($requestString,$link);
		$newExtrafields = array();
		while($extraField=mysql_fetch_object($request)){
				$newExtrafields[$extraField->id]=$extraField->id;
				request("INSERT INTO `$TBL_lists_extrafields_values` (`id_extrafield`,`id_item`,`id_language`,`value`) SELECT '".$extraField->id."','$idNewItem',id_language,value FROM $TBL_lists_extrafields_values WHERE id_extrafield='".$extraField->id."' AND id_item='$idItem'",$link);
		}
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des valeurs des champs additionnels de l'élément")." $idItem.<br>";
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
