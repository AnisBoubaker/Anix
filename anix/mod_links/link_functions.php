<?php
function getCatalogueCatTable($result){
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

function isCatalogueChild($table, $idCategory, $idParent){
	if($idParent==$idCategory) return true;
	if(!isset($table[$idParent]["subcats"]) || !count($table[$idParent]["subcats"])) return false;
	foreach($table[$idParent]["subcats"] as $currentCat){
		if(isCatalogueChild($table,$idCategory,$currentCat)) return true;
	}
	return false;
}

function showCatalogueProductsLinks($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$prohibited,$link){
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
		if(!$level || isCatalogueChild($table,$showCategory,$row["id"])) $displayTable = true;
		//If it's a category at at the same level
		elseif(isset($table[$row["id_parent"]]) && isset($table[$row["id_parent"]]["subcats"]) && isset($table[$row["id_parent"]]["subcats"][$showCategory])) $displayTable=true;
		elseif(isset($table[$showCategory]["subcats"]) && isset($table[$showCategory]["subcats"][$row["id"]])) $displayTable=true;
		else $displayTable=false;
		if(!$displayTable) $returnStr.=" style='display:none;'";
		$returnStr.=">";
		$returnStr.="<tr>";
		//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($reloadURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		//DISPLAY THE SUB-CATEGORIES
		if(count($row["subcats"])) {
			$returnStr.="<tr>";
			$returnStr.="<td colspan='2'>";
			$returnStr.=showCatalogueProductsLinks($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$prohibited,$link);
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		//DISPLAY THE PRODUCTS
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store,$TBL_catalogue_info_products.name,$TBL_catalogue_brands.name brand_name FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages) LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id) WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun produit dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
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

function showCatalogueCategoriesLinks($table,$idParent,$level,$actionURL,$showButtons,$linkToNonProductCategories){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr valign='middle'>";
		//$returnStr.="<td align='left' valign='middle' width='122' bgcolor='#e7eff2'>";
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
		if($actionURL!="" && $link) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && $link) $returnStr.="</a>";
		if(!$link) echo "</font>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCatalogueCategoriesLinks($table,$row["id"],$level+1,$actionURL,$showButtons,$linkToNonProductCategories);
	}
	return $returnStr;
}

?>