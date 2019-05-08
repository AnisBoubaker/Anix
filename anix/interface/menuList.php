<?php
// Requires class productCat and product
class menuList{
	//variables
	var $idCategory=0;
	var $menuTable=array();
	private $tabPrefix = 'tab_';
	private $selectedMenu=-1;
	public $selectedMenusTree=array();
	private $homePageID=1;

	private $counter=0;

	//constructors
	function __construct($idCategory,$selectedMenu=-1,$idLanguage,$link){
		global $TBL_content_menuitems,$TBL_content_info_menuitems;
		if($idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$requestStr="SELECT $TBL_content_menuitems.id,
                $TBL_content_menuitems.type,
                $TBL_content_info_menuitems.title,
                $TBL_content_info_menuitems.alt_title,
                $TBL_content_info_menuitems.link,
                $TBL_content_info_menuitems.img_off,
                $TBL_content_info_menuitems.img_on,
                $TBL_content_info_menuitems.img_mover,
                $TBL_content_info_menuitems.img_click,
                $TBL_content_info_menuitems.img_release,
                $TBL_content_menuitems.txt_color_off,
                $TBL_content_menuitems.txt_color_on,
                $TBL_content_menuitems.txt_color_mover,
                $TBL_content_menuitems.txt_color_click,
                $TBL_content_menuitems.txt_color_release,
                $TBL_content_menuitems.id_parent,
                $TBL_content_menuitems.ordering
          FROM  $TBL_content_menuitems,$TBL_content_info_menuitems
          WHERE $TBL_content_menuitems.id_category='$idCategory'
          AND   $TBL_content_info_menuitems.id_language='$idLanguage'
          AND   $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id
          ORDER BY $TBL_content_menuitems.id_parent,$TBL_content_menuitems.ordering";
		$request=request($requestStr,$insideLink);
		if(!mysql_num_rows($request)){
			mysql_close($insideLink);
			return;
		}
		$this->idCategory = $idCategory;
		while($row=mysql_fetch_object($request)){
			$this->menuTable[$row->id]=array();
			$this->menuTable[$row->id]["id"]=$row->id;
			$this->menuTable[$row->id]["type"]=$row->type;
			$this->menuTable[$row->id]["title"]=$row->title;
			$this->menuTable[$row->id]["alt_title"]=$row->alt_title;
			$this->menuTable[$row->id]["link"]=$row->link;
			//$this->menuTable[$row->id]["link"]="./sample_page.php?id=".$row->id;
			$this->menuTable[$row->id]["img_off"]=$row->img_off;
			$this->menuTable[$row->id]["img_on"]=$row->img_on;
			$this->menuTable[$row->id]["img_mover"]=$row->img_mover;
			$this->menuTable[$row->id]["img_click"]=$row->img_click;
			$this->menuTable[$row->id]["img_release"]=$row->img_release;
			$this->menuTable[$row->id]["txt_color_off"]=$row->txt_color_off;
			$this->menuTable[$row->id]["txt_color_on"]=$row->txt_color_on;
			$this->menuTable[$row->id]["txt_color_mover"]=$row->txt_color_mover;
			$this->menuTable[$row->id]["txt_color_click"]=$row->txt_color_click;
			$this->menuTable[$row->id]["txt_color_release"]=$row->txt_color_release;
			$this->menuTable[$row->id]["selected"]=false;
			$this->menuTable[$row->id]["parent"]=$row->id_parent;
			$this->menuTable[$row->id]["ordering"]=$row->ordering;
		}
		if(!$link) mysql_close($insideLink);

		if(isset($this->menuTable[$selectedMenu])) $this->selectedMenu = $selectedMenu;
		else $this->selectedMenu=$this->homePageID;

		//compute menu's levels
		foreach ($this->menuTable as &$menu){
			$level=1;
			$selected=$menu;
			//echo "current:".$selected["title"]."<br />";
			while(isset($this->menuTable[$selected["parent"]])){
				$level++;
				$selected=$this->menuTable[$selected["parent"]];
				//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->".$selected["title"]."<br />";
			}
			$menu["level"]=$level;
		}

		//Select the appropriate menus
		if(isset($this->menuTable[$this->selectedMenu])){
			$selected = &$this->menuTable[$this->selectedMenu];
			$selected["selected"]=true;
			$this->selectedMenusTree[$selected["level"]]=$selected["id"];
			while(isset($this->menuTable[$selected["parent"]])){
				$selected=&$this->menuTable[$selected["parent"]];
				$selected["selected"]=true;
				$this->selectedMenusTree[$selected["level"]]=$selected["id"];
			}
		}
	}

	private function sortByParentId($a,$b){
		if($a["parent"]<$b["parent"]) return -1;
		if($a["parent"]>$b["parent"]) return 1;
		if($a["ordering"]<$b["ordering"]) return -1;
		if($a["ordering"]>$b["ordering"]) return 1;
		return 0;
	}

	public function getMenusByLevel($level){
		$return =array();
		foreach ($this->menuTable as $id => $menuItem)	{
			if($menuItem["level"]==$level) $return[$id] = $menuItem;
		}
		return $return;
	}

	public function printTabs(){
		$tabsStr= "";
		//menu must be sorted by parent ID
		foreach ($this->menuTable as $menu){
			if($menu["parent"]!=0) break;
			$tmp = "";
			$tmp.="<a id='".$this->tabPrefix.$menu["id"]."' ";
			if(isset($menu["selected"]) && $menu["selected"]) $tmp.="class='tab_on'";
			else  $tmp.="class='tab_off'";
			$tmp.=" href='".$menu["link"]."'><b>".$menu["title"]."</b></a>\n";
			$tabsStr=$tmp.$tabsStr;
		}
		//prefixe
		$tabsStr = "<div id='tabsMarker' style='float:right;width:1px;height:1px;'></div>\n".$tabsStr;
		$tabsStr = "<div id='tabs_box'>\n".$tabsStr;
		//suffixe
		$tabsStr.= "</div>\n";
		return $tabsStr;
	}

	public function printSubtabs(){
		$subtabsStr= "";
		//menu must be sorted by parent ID
		$lastTabProcessed = 0;
		foreach ($this->menuTable as $id=>$menu)
		if($menu["level"]==2){ //Only 2nd level menu: parent!=0 and it's parent's parent=0.
			if($lastTabProcessed!=$menu["parent"]){
				if($lastTabProcessed!=0) $subtabsStr.="</div>\n"; //if this is not the first loop => We have a div to close
				$subtabsStr.="<div id='subtab_".$menu["parent"]."' class='tab_subtab' style='display:none;z-index:500'>\n";
				$lastTabProcessed = $menu["parent"];
			}
			$subtabsStr.="<a href='".$menu["link"]."'>";
			if(isset($this->selectedMenusTree[2]) && $id==$this->selectedMenusTree[2]) $subtabsStr.="<b>".$menu["title"]."</b>";
			else $subtabsStr.=$menu["title"];
			$subtabsStr.="</a>\n";
		}
		$subtabsStr.="</div>\n"; //close the latest div opened
		return $subtabsStr;
	}

	public function printMenu(){
		//NOTE: Menus must be arranged by level and ordering first before calling this function!!
		//usually arrangement is done by the DB but we have to be sure...
		$str="";
		$mainMenu = $this->selectedMenusTree[1]; //level 1 selected menu
		$level2Strs= array();
		$countL2=0;
		$str.="<div id='left_menu'>\n";
		$str.="<h1>".$this->menuTable[$mainMenu]["title"]."</h1>";
		foreach ($this->menuTable as $id=>$menu){
			if($menu["level"]==2 && $menu["parent"]==$mainMenu){
				$level2Strs[$id]="";
				if(isset($this->selectedMenusTree[2]) && $id==$this->selectedMenusTree[2]) $className='menul1_on';
				else $className='menul1';
				$level2Strs[$id].="<a class='$className' href='".$menu["link"]."'>".$menu["title"]."</a>\n";
				$level2Strs[$id].="<div class='sublevel'>\n";
			}
			if($menu["level"]==3 && isset($level2Strs[$menu["parent"]])){
				$printId=false; //Whether we print the ID or not. Note: The id is useless if the current menu does not have submenus
				$level2Strs[$menu["parent"]].="<a ";
				if($menu["type"]=="submenu" && isset($this->selectedMenusTree[3]) && $id==$this->selectedMenusTree[3]) { $className="menul2e_on"; $printId=true;}
				elseif($menu["type"]=="submenu") {$className="menul2e"; $printId=true;}
				elseif($menu["type"]=="link" && isset($this->selectedMenusTree[3]) && $id==$this->selectedMenusTree[3]) $className="menul2_on";
				else $className = "menul2";
				$level2Strs[$menu["parent"]].="class='$className' ";
				if($printId)  $level2Strs[$menu["parent"]].="id='menu_$id' ";
				$level2Strs[$menu["parent"]].="href='".$menu["link"]."'>".$menu["title"]."</a>";
			}
		}
		//closeup all the sublevels
		foreach($level2Strs as $tmp){
			$str.=$tmp;
			$str.="</div>\n";
		}
		$str.="<div class='menu_close'><img src='./images/spacer.gif' alt='' style='height:1px;width:50px;' /></div>\n";
		$str.="</div><!-- left_menu -->\n";
		return $str;
	}
	public function printSubMenus(){
		$tmpTable = array();
		//we get all the level3 menus and fill them with their submenus
		foreach ($this->menuTable as $id=>$menu){
			if($menu["level"]==3 && $this->menuTable[$menu["parent"]]["parent"]==$this->selectedMenusTree[1] && $menu["type"]=="submenu"){
				$tmpTable[$id]=array();
			}
			if($menu["level"]==4 && isset($tmpTable[$menu["parent"]])){//level 4 menu with displayed parent
				$tmpTable[$menu["parent"]][$id]=true;
			}
		}
		$str="";
		foreach ($tmpTable as $idL3 => $l3Content){
			$str.="<div id='submenu_$idL3' class='menu_submenu' style='display:none;z-index:500'>\n";
			$found=false;
			foreach($l3Content as $idL4 => $ignore){
				$bold=(isset($this->selectedMenusTree[4]) && $idL4==$this->selectedMenusTree[4]);
				$str.="<a href='".$this->menuTable[$idL4]["link"]."'>".($bold?"<b>":"").$this->menuTable[$idL4]["title"].($bold?"</b>":"")."</a>";
				$found=true;
			}
			if(!$found) $str.="<p>-- "._("Empty")." --</p>";
			$str.="</div>\n";
		}
		return $str;
	}

	public function isHomePage(){
		return $this->selectedMenu==$this->homePageID;
	}

	public function printBreadCrumb($addLinks=true){
		$str="";
		if($addLinks) $str.="<div id='breadcrumb'>\n";
		if($this->selectedMenu!=$this->homePageID){
			if($addLinks) $str.="<a href='".$this->menuTable[$this->homePageID]["link"]."'>";
			$str.=$this->menuTable[$this->homePageID]["title"];
			if($addLinks) $str.="</a>";
			$str.=" > ";
		}
		for($i=1;isset($this->selectedMenusTree[$i]);$i++){
			if($i!=1) $str.=" > ";
			$menuItem=$this->menuTable[$this->selectedMenusTree[$i]];
			$bold=($addLinks && $menuItem["id"]==$this->selectedMenu);
			if($addLinks) $str.="<a href='".$menuItem["link"]."'>";
			$str.=($bold?"<b>":"").$menuItem["title"].($bold?"</b>":"");
			if($addLinks) $str.="</a>";
		}
		if($addLinks) $str.="\n</div>\n";
		return $str;
	}

	public function getBreadCrumbById($idMenu,$addLinks=true){
		$str="";
		if(!isset($this->menuTable[$idMenu])) return "";
		if($addLinks) $str.="<a href='".$this->menuTable[$idMenu]["link"]."'>";
		$str.=$this->menuTable[$idMenu]["title"];
		if($addLinks) $str.="</a>";
		$currentId=$this->menuTable[$idMenu]["parent"];

		while($currentId){
			$tmp = "";
			if($addLinks) $tmp.="<a href='".$this->menuTable[$currentId]["link"]."'>";
			$tmp.=$this->menuTable[$currentId]["title"];
			if($addLinks) $tmp.="</a>";
			$str = $tmp." > ".$str;
			$currentId=$this->menuTable[$currentId]["parent"];
		}
		return $str;
	}

	public function printPlan($idParent){
		//copy the table
		$returnStr="";
		$found=false;
		foreach($this->menuTable as $item) if($item["parent"]==$idParent){
			if(!$found){
				$returnStr.="<ul>\n";
			}
			//for($i=0;$i<$item["level"];$i++) $returnStr.="&nbsp;&nbsp;";
			$returnStr.="<li>";
			if($idParent==0) $returnStr.="<h2>";
			$returnStr.="<a href='".$item["link"]."'>".$item["title"]."</a>";
			if($idParent==0) $returnStr.="</h2>";
			$returnStr.="</li>\n";
			$returnStr.=$this->printPlan($item["id"]);
			$found=true;
		}
		if($found) $returnStr.="</ul>\n";
		return $returnStr;
	}
} //Class
?>
