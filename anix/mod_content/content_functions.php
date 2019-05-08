<?php
function getMenusTable($result){
	$catalogueCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["type"]=$row->type;
		$catalogueCat[$row->id]["subcats"]=array();
		$catalogueCat[$row->id]["deletable"]=$row->deletable;
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["title"]=$row->title;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
		else $catalogueCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
	}
	return $catalogueCat;
}
function getMenusList($idCat,$link,$idLanguage,$idParent=0,$level=0){
	global $TBL_content_menuitems,$TBL_content_info_menuitems;
	//echo str_pad("getMenusList($idCat,link,language,$idParent,$level)\n",$level,"  ",STR_PAD_LEFT);
	$return = array();
	//get submenus
	$request = request(
	"SELECT `$TBL_content_info_menuitems`.`id_menuitem`,`$TBL_content_info_menuitems`.`title`,`$TBL_content_menuitems`.`type`,`$TBL_content_menuitems`.`deletable`
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
		$return[$counter]["deletable"]=$submenu->deletable;
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
/*function getMenusSelectOptions($idCat,$link){
$menuTable=getMenusList($idCat,$link);
while($menu)
}*/
function getMenuitemLevel($table,$idMenuitem){
	$level=1;$tmpId=$idMenuitem;
	if(!$idMenuitem) return $level;
	while($table[$tmpId]["id_parent"]!=0) {
		$level++;
		$tmpId=$table[$tmpId]["id_parent"];
	}
	return $level;
}

function showPages($idCategory,$link){
	global $TBL_content_pages;
	global $TBL_content_info_pages;
	global $TBL_gen_languages,$used_language_id;
	global $pageCategories;
	$returnStr="";
	if(!isset($pageCategories[$idCategory])) return $returnStr;
	$category = $pageCategories[$idCategory];
	//foreach($pageCategories as $category){
		$request = request(
		"SELECT `$TBL_content_pages`.`id`,`$TBL_content_pages`.`id_category`,`$TBL_content_pages`.`type`,`$TBL_content_pages`.`link_module`,`$TBL_content_pages`.`link_id_item`,`$TBL_content_pages`.`deletable`,`$TBL_content_pages`.`ordering`,`$TBL_content_info_pages`.`title`
        FROM `$TBL_content_pages`,`$TBL_content_info_pages`,`$TBL_gen_languages`
        WHERE `$TBL_content_pages`.`id_category`=".$category["id"]."
        AND `$TBL_gen_languages`.`id`='$used_language_id'
        AND `$TBL_content_info_pages`.`id_page`=`$TBL_content_pages`.`id`
        AND `$TBL_content_info_pages`.`id_language`=`$TBL_gen_languages`.`id`
        ORDER BY `$TBL_content_pages`.`ordering`",$link);
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='right' valign='middle' width='82' bgcolor='#e7eff2'>";
		if(isset($category["linksAllowed"]) && $category["linksAllowed"]){
			$returnStr.="<a href='./add_link.php?idCategory=".$category["id"]."'><img src='../images/add_link.gif' border='0' alt='"._("Ajouter une page liée")."'></a>";
		}
		if($category["nbAllowed"]==-1 || mysql_num_rows($request)<$category["nbAllowed"]){
			$returnStr.="<a href='./mod_page.php?action=add&idCategory=".$category["id"]."'><img src='../images/add.gif' border='0' alt='"._("Ajouter une page à cette categorie")."'></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Categorie")."'><b>".$category["name"]."</b>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		$nbResults=mysql_num_rows($request);
		if(!$nbResults){
			$returnStr.="<center><i>"._("Aucune page n'a ete trouvee en base de donnees")."</i></center>";
		}
		while($page=mysql_fetch_object($request)){
			$returnStr.="<table class='edittable_text' width='100%'>";
			$returnStr.="<tr>";
			$returnStr.="<td align='left' valign='middle' width='82' bgcolor='#e7eff2'>";
			if($page->type=="page") $returnStr.="<a href='./mod_page.php?action=edit&idPage=".$page->id."'><img src='../images/edit.gif' border='0' alt='"._("Éditer la page")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if($page->deletable=="Y") $returnStr.="&nbsp;<a href='./del_page.php?idPage=".$page->id."'><img src='../images/del.gif' border='0' alt='"._("Supprimer la page")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if($page->ordering!=1) $returnStr.="<a href='./list_pages.php?action=moveup&idPage=".$page->id."&idCategory=$idCategory'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if($page->ordering!=$nbResults) $returnStr.="<a href='./list_pages.php?action=movedown&idPage=".$page->id."&idCategory=$idCategory'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="</td>";
			$returnStr.="<td>";
			if($page->type=="page"){
				$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;".$page->title." <i>(id:".$page->id.")</i>";
			}elseif($page->type=="link"){
				$linkName = getLinkName($page->link_module,$page->link_id_item,$link);
				if($linkName===false){
					deletePageLink($page->id,$link);
					$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;<img src='../images/link.gif' alt=\""._("Page Liée")."\" /><i>"._("Supprimé")." (id:".$page->id.")</i>";
				} else {
					$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;<img src='../images/link.gif' alt=\""._("Page Liée")."\" />".$linkName." <i>(id:".$page->id.")</i>";
				}
			}
			$returnStr.="</td>";
			$returnStr.="</tr>";
			$returnStr.="</table>";
		}
	//}
	return $returnStr;
}

function getLinkName($module,$id,$link){
	global $TBL_articles_info_article,$TBL_news_info_news;
	global $used_language_id;
	switch($module){
		case "articles":
			$request = request("SELECT `title` FROM `$TBL_articles_info_article` WHERE `id_article`='$id' AND `id_language`='$used_language_id'",$link);
			if(!mysql_num_rows($request)) return false;
			$tmp=mysql_fetch_object($request);
			return "["._("ARTICLE")."] ".$tmp->title;
			break;
		case "news":
			$request = request("SELECT `title`,`date` FROM `$TBL_news_info_news` WHERE `id_news`='$id' AND `id_language`='$used_language_id'",$link);
			if(!mysql_num_rows($request)) return false;
			$tmp=mysql_fetch_object($request);
			return "<i>["._("NOUVELLE")."]</i> (".$tmp->date.") ".$tmp->title;
			break;
		default: return false;
	}

}

function deletePageLink($idPage,$link){
	global $TBL_content_pages;
	global $TBL_content_info_pages;

	request("DELETE FROM `$TBL_content_pages` WHERE `id`='$idPage'",$link);
	request("DELETE FROM `$TBL_content_info_pages` WHERE `id_page`='$idPage'",$link);
}

function showMenuCategory($table,$idCategory,$nbAllowedInSubmenu,$idParent,$level){
	$returnStr="";
	foreach($table as $row){
		if($row["id_parent"]==$idParent){
			$returnStr.="<table class='edittable_text' width='100%'>";
			$returnStr.="<tr valign='middle'>";
			//Anchor for positioning
			$returnStr.="<a name='".$row["id"]."'>";
			$returnStr.="<td align='right' valign='middle' width='102' bgcolor='#e7eff2'>";
			$returnStr.="<a href='./mod_menus.php?action=edit&idMenuitem=".$row["id"]."&idCategory=$idCategory'><img src='../images/edit.gif' border='0' alt='"._("Modifier le composant")."'></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_menu.php?idMenuitem=".$row["id"]."&idCategory=$idCategory'><img src='../images/del.gif' border='0' alt='"._("Supprimer le composant")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["first"]) $returnStr.="<a href='./list_menus.php?action=moveup&idMenuitem=".$row["id"]."&idCategory=$idCategory'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_menus.php?action=movedown&idMenuitem=".$row["id"]."&idCategory=$idCategory'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$allowedToAdd=($row["type"]=="submenu" && count($row["subcats"])<$nbAllowedInSubmenu);
			if($row["type"]=="submenu" && $allowedToAdd) $returnStr.="<a href='./mod_menus.php?action=add&idCategory=$idCategory&idParent=".$row["id"]."'><img src='../images/add.gif' border='0' alt='"._("Ajouter un menu")."'></a>";
			elseif($row["type"]=="submenu") $returnStr.="<img src='../images/add_off.gif' border='0' alt='"._("Ajout indisponible")."'>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="</td>";
			$returnStr.="<td>";
			for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
			$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
			if($row["type"]=="submenu") $returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Sous-menu")."'>";
			elseif($row["type"]=="link") $returnStr.="<img src='../images/copy.gif' border='0' alt='"._("Lien")."'>";
			$returnStr.=$row["title"];
			$returnStr.="</td>";
			$returnStr.="</tr>";
			$returnStr.="</table>";
			if(count($row["subcats"])) $returnStr.=showMenuCategory($table,$idCategory,$nbAllowedInSubmenu,$row["id"],$level+1);
		}
	}
	return $returnStr;
}
function showMenusList($idCategory,$link){
	global $TBL_content_menuitems;
	global $TBL_content_info_menuitems;
	global $menuCategories;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	if(isset($menuCategories[$idCategory])){
		$category=$menuCategories[$idCategory];
		$request = request("SELECT $TBL_content_menuitems.id,$TBL_content_menuitems.type,$TBL_content_menuitems.deletable,$TBL_content_menuitems.ordering,$TBL_content_menuitems.id_parent,$TBL_content_info_menuitems.title FROM $TBL_content_menuitems,$TBL_content_info_menuitems,$TBL_gen_languages WHERE $TBL_content_menuitems.id_category='".$category["id"]."' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id AND $TBL_content_info_menuitems.id_language=$TBL_gen_languages.id order by id_parent,ordering",$link);
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='right' valign='middle' width='102' bgcolor='#e7eff2'>";
		if($category["nbAllowed"]==-1 || mysql_num_rows($request)<$category["nbAllowed"]){
			$returnStr.="<a href='./mod_menus.php?action=add&idCategory=".$category["id"]."&idParent=0'><img src='../images/add.gif' border='0' alt='"._("Ajouter un composant")."'></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Menu")."'><B>".$category["name"]."</B>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if(mysql_num_rows($request)){
			$returnStr.="</table>";
			$table = getMenusTable($request);
			$returnStr.=showMenuCategory($table,$category["id"],$category["nbAllowedInSublevels"],0,0);
			//$returnStr.="</tr></td>";
		} else {
			$returnStr.="<tr>";
			$returnStr.="<td align='left' valign='middle' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
			$returnStr.="<td align='center'>";
			$returnStr.="<i>"._("Aucun composant de menu dans cette categorie")."</i>";
			$returnStr.="</td>";
			$returnStr.="</tr>";
			$returnStr.="</table>";
		}
	}
	return $returnStr;
}
function deleteMenuitem($table,$idMenuitem,$link){
	global $TBL_content_menuitems;
	global $TBL_content_info_menuitems;
	global $folder_webLocalesRoot;
	global $TBL_gen_languages;
	global $menuCategories;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$category=null;
	$request = request("SELECT id,id_category,id_parent,ordering from $TBL_content_menuitems where id='$idMenuitem'",$link);
	if(mysql_num_rows($request)){
		$menuitem = mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"].=_("Le composant specifie est invalide");
	}
	//Reccursive delete of subfolders
	if(!$return["errors"]){
		foreach($table[$idMenuitem]["subcats"] as $subcat){
			$tmp=deleteMenuitem($table,$subcat,$link);
			$return["errors"]+=$tmp["errors"];
			$return["errMessage"].=$tmp["errMessage"];
		}
	}
	if(!$return["errors"]){
		//Update the orderings inside the category
		request("UPDATE $TBL_content_menuitems set ordering=ordering-1 WHERE id_category='".$menuitem->id_category."' and id_parent='".$menuitem->id_parent."' and ordering > ".$menuitem->ordering,$link);
		if(mysql_errno()){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la mise à jour de l'ordre des menus.")."<br>";
		}
	}
	if(!$return["errors"]){
		$request=request(
		"SELECT img_off,img_on,img_mover,img_click,img_release,locales_folder
          FROM $TBL_content_info_menuitems,$TBL_gen_languages
          WHERE $TBL_content_info_menuitems.`id_menuitem`='$idMenuitem'
          AND $TBL_gen_languages.`used`='Y'
          AND $TBL_content_info_menuitems.`id_language`=$TBL_gen_languages.`id`",$link);
		//Deletes the image files from filesystem
		while($menuInfos=mysql_fetch_object($request)){
			if($menuInfos->img_off!="" && !unlink("../".$folder_webLocalesRoot.$menuInfos->locales_folder."/images/".$menuInfos->img_off)){
				$return["errMessage"].=_("Une erreur s'est produite lors la suppression de l'image off du composant")."<br>";
			}
			if($menuInfos->img_on!="" && !unlink("../".$folder_webLocalesRoot.$menuInfos->locales_folder."/images/".$menuInfos->img_on)){
				$return["errMessage"].=_("Une erreur s'est produite lors la suppression de l'image on du composant")."<br>";
			}
			if($menuInfos->img_mover!="" && !unlink("../".$folder_webLocalesRoot.$menuInfos->locales_folder."/images/".$menuInfos->img_mover)){
				$return["errMessage"].=_("Une erreur s'est produite lors la suppression de l'image mover du composant.")."<br>";
			}
			if($menuInfos->img_click!="" && !unlink("../".$folder_webLocalesRoot.$menuInfos->locales_folder."/images/".$menuInfos->img_click)){
				$return["errMessage"].=_("Une erreur s'est produite lors la suppression de l'image click du composant.")."<br>";
			}
			if($menuInfos->img_release!="" && !unlink("../".$folder_webLocalesRoot.$menuInfos->locales_folder."/images/".$menuInfos->img_release)){
				$return["errMessage"].=_("Une erreur s'est produite lors la suppression de l'image release du composant.")."<br>";
			}
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_content_menuitems,$TBL_content_info_menuitems
               FROM $TBL_content_menuitems,$TBL_content_info_menuitems
               WHERE $TBL_content_menuitems.id='$idMenuitem'
               AND $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].=_("Une erreur s'est produite lors de la suppression du composant.");
		}
	}
	return $return;
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
		$newsCat[$row->id]["id_parent"]=$row->id_parent;
		$newsCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $newsCat[$row->id]["first"]=true;
		else $newsCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $newsCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
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
			$request = request("SELECT $TBL_news_news.id,$TBL_news_news.ordering,$TBL_news_news.active,$TBL_news_news.from_date,$TBL_news_news.to_date,$TBL_news_info_news.date,$TBL_news_info_news.title FROM $TBL_news_news,$TBL_news_info_news,$TBL_gen_languages WHERE $TBL_news_news.id_category='$showCategory' and $TBL_news_news.id=$TBL_news_info_news.id_news and $TBL_news_info_news.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_news.ordering",$link);
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
				if(strlen($news->title)+$totalCars>$maxLength){
					$showDesc=substr($news->title, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$news->title;
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

function getArticlesCatTable($result){
	$articlesCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $articlesCat[$lastInserted]["last"]=false;
		$articlesCat[$row->id]=array();
		$articlesCat[$row->id]["id"]=$row->id;
		$articlesCat[$row->id]["subcats"]=array();
		$articlesCat[$row->id]["ordering"]=$row->ordering;
		$articlesCat[$row->id]["name"]=$row->name;
		$articlesCat[$row->id]["id_parent"]=$row->id_parent;
		$articlesCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $articlesCat[$row->id]["first"]=true;
		else $articlesCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $articlesCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
	}
	return $articlesCat;
}

function showArticles($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_info_article;
	global $TBL_articles_article;
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
			$request = request("SELECT $TBL_articles_article.id,$TBL_articles_article.ordering,$TBL_articles_article.active,$TBL_articles_article.from_date,$TBL_articles_article.to_date,$TBL_articles_info_article.title FROM $TBL_articles_article,$TBL_articles_info_article,$TBL_gen_languages WHERE $TBL_articles_article.id_category='$showCategory' and $TBL_articles_article.id=$TBL_articles_info_article.id_article and $TBL_articles_info_article.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_article.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun article dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($article=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the news
				if($article->active=="Y") $active=_("Active");
				elseif($article->active=="N") $active=_("Désactivée");
				elseif($article->active=="DATE") {
					$currentDate=date("Y-m-d");
					if($currentDate<$article->from_date) $active=_("En attente");
					elseif($currentDate>$article->to_date) $active=_("Expirée");
					else $active=_("Active");
				}
				$returnStr.=$article->title." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.=" <a href='$actionURL".$article->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showNews($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$link);
	}
	return $returnStr;
}
?>
