<?php
class menuList{
	//Array
	private $tabPrefix = 'tab_';
	public $menuTable=array();
	private $selectedMenu=-1;
	private $selectedMenusTree=array();
	private $homePageID=1;


	function menuList($selectedMenu=-1){
		//NOTICE: menuTable must be ordered by parent than by ordering!!
		$this->menuTable[1]=array("id"=>1,"type"=>"submenu","title"=>"Home","link"=>"./","parent"=>0,"ordering"=>1,"selected"=>false);
		$this->menuTable[2]=array("id"=>2,"type"=>"submenu","title"=>"About Us","link"=>"./sample_page.php?id=2","parent"=>0,"ordering"=>2,"selected"=>false);
		$this->menuTable[3]=array("id"=>3,"type"=>"submenu","title"=>"Information Center","link"=>"./sample_page.php?id=3","parent"=>0,"ordering"=>3,"selected"=>false);
		$this->menuTable[4]=array("id"=>4,"type"=>"submenu","title"=>"Training","link"=>"./sample_page.php?id=4","parent"=>0,"ordering"=>4,"selected"=>false);
		$this->menuTable[5]=array("id"=>5,"type"=>"submenu","title"=>"Accreditation &amp; Certification","link"=>"./sample_page.php?id=5","parent"=>0,"ordering"=>5,"selected"=>false);
		$this->menuTable[6]=array("id"=>6,"type"=>"submenu","title"=>"Events &amp; Activities","link"=>"./sample_page.php?id=6","parent"=>0,"ordering"=>6,"selected"=>false);
		$this->menuTable[7]=array("id"=>7,"type"=>"submenu","title"=>"Advocacy","link"=>"./sample_page.php?id=7","parent"=>0,"ordering"=>7,"selected"=>false);

			$this->menuTable[8]=array("id"=>8,"type"=>"submenu","title"=>"Historical Background","link"=>"./sample_page.php?id=8","parent"=>2,"ordering"=>1,"selected"=>false);
			$this->menuTable[9]=array("id"=>9,"type"=>"submenu","title"=>"Vision &amp; Strategies","link"=>"./sample_page.php?id=9","parent"=>2,"ordering"=>2,"selected"=>false);
			$this->menuTable[10]=array("id"=>10,"type"=>"submenu","title"=>"Corporate Structure","link"=>"./sample_page.php?id=10","parent"=>2,"ordering"=>3,"selected"=>false);
			$this->menuTable[11]=array("id"=>11,"type"=>"submenu","title"=>"Staff","link"=>"./sample_page.php?id=11","parent"=>2,"ordering"=>4,"selected"=>false);
			$this->menuTable[12]=array("id"=>12,"type"=>"submenu","title"=>"Contact us","link"=>"./sample_page.php?id=12","parent"=>2,"ordering"=>5,"selected"=>false);

			$this->menuTable[13]=array("id"=>13,"type"=>"submenu","title"=>"What is GeoExchange?","link"=>"./sample_page.php?id=13","parent"=>3,"ordering"=>1,"selected"=>false);
			$this->menuTable[14]=array("id"=>14,"type"=>"submenu","title"=>"Newsroom","link"=>"./sample_page.php?id=14","parent"=>3,"ordering"=>2,"selected"=>false);
			$this->menuTable[15]=array("id"=>15,"type"=>"submenu","title"=>"CGC Publications","link"=>"./sample_page.php?id=15","parent"=>3,"ordering"=>3,"selected"=>false);
			$this->menuTable[16]=array("id"=>16,"type"=>"submenu","title"=>"Industry &amp; Academic Resources","link"=>"./sample_page.php?id=16","parent"=>3,"ordering"=>4,"selected"=>false);
			$this->menuTable[17]=array("id"=>17,"type"=>"submenu","title"=>"Goverment Resources","link"=>"./sample_page.php?id=17","parent"=>3,"ordering"=>5,"selected"=>false);
			$this->menuTable[18]=array("id"=>18,"type"=>"submenu","title"=>"International Resources","link"=>"./sample_page.php?id=18","parent"=>3,"ordering"=>6,"selected"=>false);
			$this->menuTable[19]=array("id"=>19,"type"=>"submenu","title"=>"FAQ","link"=>"./sample_page.php?id=19","parent"=>3,"ordering"=>7,"selected"=>false);

			$this->menuTable[20]=array("id"=>20,"type"=>"submenu","title"=>"CGC Quality Training Commitment","link"=>"./sample_page.php?id=20","parent"=>4,"ordering"=>1,"selected"=>false);
			$this->menuTable[21]=array("id"=>21,"type"=>"submenu","title"=>"CGC Training Program","link"=>"./sample_page.php?id=21","parent"=>4,"ordering"=>1,"selected"=>false);

			$this->menuTable[22]=array("id"=>22,"type"=>"submenu","title"=>"Program Description","link"=>"./sample_page.php?id=22","parent"=>5,"ordering"=>1,"selected"=>false);
			$this->menuTable[23]=array("id"=>23,"type"=>"submenu","title"=>"Public Registry","link"=>"./sample_page.php?id=23","parent"=>5,"ordering"=>2,"selected"=>false);
			$this->menuTable[24]=array("id"=>24,"type"=>"submenu","title"=>"FAQ","link"=>"./sample_page.php?id=24","parent"=>5,"ordering"=>3,"selected"=>false);

			$this->menuTable[25]=array("id"=>25,"type"=>"submenu","title"=>"Annual Conference","link"=>"./sample_page.php?id=25","parent"=>6,"ordering"=>1,"selected"=>false);
			$this->menuTable[26]=array("id"=>26,"type"=>"submenu","title"=>"Industry Excellence &amp; Leadership Awards","link"=>"./sample_page.php?id=26","parent"=>6,"ordering"=>2,"selected"=>false);
			$this->menuTable[27]=array("id"=>27,"type"=>"submenu","title"=>"Workshop &amp; Seminars","link"=>"./sample_page.php?id=27","parent"=>6,"ordering"=>3,"selected"=>false);
			$this->menuTable[28]=array("id"=>28,"type"=>"submenu","title"=>"Special Projects","link"=>"./sample_page.php?id=28","parent"=>6,"ordering"=>4,"selected"=>false);

			$this->menuTable[29]=array("id"=>29,"type"=>"submenu","title"=>"Industry Leadership","link"=>"./sample_page.php?id=29","parent"=>7,"ordering"=>1,"selected"=>false);
			$this->menuTable[30]=array("id"=>30,"type"=>"submenu","title"=>"Current Goverment Affairs","link"=>"./sample_page.php?id=30","parent"=>7,"ordering"=>2,"selected"=>false);
			$this->menuTable[31]=array("id"=>31,"type"=>"submenu","title"=>"Communications &amp; Testimony","link"=>"./sample_page.php?id=31","parent"=>7,"ordering"=>3,"selected"=>false);
			$this->menuTable[32]=array("id"=>32,"type"=>"submenu","title"=>"Codes &amp; Standards","link"=>"./sample_page.php?id=32","parent"=>7,"ordering"=>4,"selected"=>false);
			$this->menuTable[33]=array("id"=>33,"type"=>"submenu","title"=>"Partnerships","link"=>"./sample_page.php?id=33","parent"=>7,"ordering"=>5,"selected"=>false);

				$this->menuTable[34]=array("id"=>34,"type"=>"link","title"=>"Bylaws","link"=>"./sample_page.php?id=34","parent"=>10,"ordering"=>1,"selected"=>false);
				$this->menuTable[35]=array("id"=>35,"type"=>"link","title"=>"Board of Directors","link"=>"./sample_page.php?id=35","parent"=>10,"ordering"=>2,"selected"=>false);
				$this->menuTable[36]=array("id"=>36,"type"=>"submenu","title"=>"Membership","link"=>"./sample_page.php?id=36","parent"=>10,"ordering"=>3,"selected"=>false);
				$this->menuTable[37]=array("id"=>37,"type"=>"submenu","title"=>"Committees","link"=>"./sample_page.php?id=37","parent"=>10,"ordering"=>4,"selected"=>false);

				$this->menuTable[38]=array("id"=>38,"type"=>"link","title"=>"Key Facts","link"=>"./sample_page.php?id=38","parent"=>13,"ordering"=>1,"selected"=>false);
				$this->menuTable[39]=array("id"=>39,"type"=>"link","title"=>"How it Works","link"=>"./sample_page.php?id=39","parent"=>13,"ordering"=>2,"selected"=>false);
				$this->menuTable[40]=array("id"=>40,"type"=>"submenu","title"=>"Business Cases","link"=>"./sample_page.php?id=40","parent"=>13,"ordering"=>3,"selected"=>false);

				$this->menuTable[41]=array("id"=>41,"type"=>"link","title"=>"Press Releases","link"=>"./sample_page.php?id=41","parent"=>14,"ordering"=>1,"selected"=>false);
				$this->menuTable[42]=array("id"=>42,"type"=>"link","title"=>"Speaches &amp; Presentations","link"=>"./sample_page.php?id=42","parent"=>14,"ordering"=>2,"selected"=>false);
				$this->menuTable[43]=array("id"=>43,"type"=>"submenu","title"=>"Newsletter","link"=>"./sample_page.php?id=43","parent"=>14,"ordering"=>3,"selected"=>false);

				$this->menuTable[44]=array("id"=>44,"type"=>"link","title"=>"Case Studies","link"=>"./case_studies.php","parent"=>15,"ordering"=>1,"selected"=>false);
				$this->menuTable[45]=array("id"=>45,"type"=>"link","title"=>"Technical Reports","link"=>"./sample_page.php?id=45","parent"=>15,"ordering"=>2,"selected"=>false);
				$this->menuTable[46]=array("id"=>46,"type"=>"link","title"=>"Other CGC Publications","link"=>"./sample_page.php?id=46","parent"=>15,"ordering"=>3,"selected"=>false);

				$this->menuTable[47]=array("id"=>47,"type"=>"link","title"=>"Links","link"=>"./sample_page.php?id=47","parent"=>16,"ordering"=>1,"selected"=>false);
				$this->menuTable[48]=array("id"=>48,"type"=>"link","title"=>"Technical Docuents","link"=>"./sample_page.php?id=48","parent"=>16,"ordering"=>2,"selected"=>false);

				$this->menuTable[49]=array("id"=>49,"type"=>"link","title"=>"Links","link"=>"./sample_page.php?id=49","parent"=>17,"ordering"=>1,"selected"=>false);
				$this->menuTable[50]=array("id"=>50,"type"=>"link","title"=>"Technical Docuents","link"=>"./sample_page.php?id=50","parent"=>17,"ordering"=>2,"selected"=>false);

				$this->menuTable[51]=array("id"=>51,"type"=>"submenu","title"=>"Industry &amp; Academic","link"=>"./sample_page.php?id=51","parent"=>18,"ordering"=>1,"selected"=>false);
				$this->menuTable[52]=array("id"=>52,"type"=>"submenu","title"=>"Goverments","link"=>"./sample_page.php?id=52","parent"=>18,"ordering"=>2,"selected"=>false);

				$this->menuTable[53]=array("id"=>53,"type"=>"link","title"=>"Market Transformation Initiative","link"=>"./sample_page.php?id=53","parent"=>20,"ordering"=>1,"selected"=>false);
				$this->menuTable[54]=array("id"=>54,"type"=>"link","title"=>"Permanent Training Fund","link"=>"./sample_page.php?id=54","parent"=>20,"ordering"=>2,"selected"=>false);
				$this->menuTable[55]=array("id"=>55,"type"=>"link","title"=>"Partners Network","link"=>"./sample_page.php?id=55","parent"=>20,"ordering"=>3,"selected"=>false);

				$this->menuTable[56]=array("id"=>56,"type"=>"submenu","title"=>"Training for Drillers","link"=>"./sample_page.php?id=56","parent"=>21,"ordering"=>1,"selected"=>false);
				$this->menuTable[57]=array("id"=>57,"type"=>"submenu","title"=>"Training for Installers","link"=>"./sample_page.php?id=57","parent"=>21,"ordering"=>2,"selected"=>false);
				$this->menuTable[58]=array("id"=>58,"type"=>"submenu","title"=>"Training for Residential Systems Designers","link"=>"./sample_page.php?id=58","parent"=>21,"ordering"=>3,"selected"=>false);
				$this->menuTable[59]=array("id"=>59,"type"=>"submenu","title"=>"Training for Commercial Systems Designers","link"=>"./sample_page.php?id=59","parent"=>21,"ordering"=>4,"selected"=>false);
				$this->menuTable[60]=array("id"=>60,"type"=>"link","title"=>"Other Training Courses","link"=>"./sample_page.php?id=60","parent"=>21,"ordering"=>4,"selected"=>false);

				$this->menuTable[61]=array("id"=>61,"type"=>"link","title"=>"Industry Specialists","link"=>"./sample_page.php?id=61","parent"=>23,"ordering"=>1,"selected"=>false);
				$this->menuTable[62]=array("id"=>62,"type"=>"link","title"=>"Firms","link"=>"./sample_page.php?id=62","parent"=>23,"ordering"=>2,"selected"=>false);

					$this->menuTable[63]=array("id"=>63,"type"=>"link","title"=>"Become a Member","link"=>"./sample_page.php?id=63","parent"=>36,"ordering"=>1,"selected"=>false);
					$this->menuTable[64]=array("id"=>64,"type"=>"link","title"=>"Our Members","link"=>"./sample_page.php?id=64","parent"=>36,"ordering"=>2,"selected"=>false);

					$this->menuTable[65]=array("id"=>65,"type"=>"link","title"=>"Training Committee","link"=>"./sample_page.php?id=65","parent"=>37,"ordering"=>1,"selected"=>false);
					$this->menuTable[66]=array("id"=>66,"type"=>"link","title"=>"Technology Committee","link"=>"./sample_page.php?id=66","parent"=>37,"ordering"=>2,"selected"=>false);

					$this->menuTable[67]=array("id"=>67,"type"=>"link","title"=>"Latest Newsletter","link"=>"./sample_page.php?id=67","parent"=>43,"ordering"=>1,"selected"=>false);
					$this->menuTable[68]=array("id"=>68,"type"=>"link","title"=>"Archives","link"=>"./sample_page.php?id=68","parent"=>43,"ordering"=>2,"selected"=>false);

					$this->menuTable[69]=array("id"=>69,"type"=>"link","title"=>"Links","link"=>"./sample_page.php?id=69","parent"=>50,"ordering"=>1,"selected"=>false);
					$this->menuTable[70]=array("id"=>70,"type"=>"link","title"=>"Technical Documents","link"=>"./sample_page.php?id=70","parent"=>50,"ordering"=>2,"selected"=>false);

					$this->menuTable[71]=array("id"=>71,"type"=>"link","title"=>"Links","link"=>"./sample_page.php?id=71","parent"=>51,"ordering"=>1,"selected"=>false);
					$this->menuTable[72]=array("id"=>72,"type"=>"link","title"=>"Technical Documents","link"=>"./sample_page.php?id=72","parent"=>51,"ordering"=>2,"selected"=>false);

					$this->menuTable[73]=array("id"=>73,"type"=>"link","title"=>"Course Description","link"=>"./sample_page.php?id=73","parent"=>55,"ordering"=>1,"selected"=>false);
					$this->menuTable[74]=array("id"=>74,"type"=>"link","title"=>"Scheduled Sessions","link"=>"./sample_page.php?id=74","parent"=>55,"ordering"=>2,"selected"=>false);

					$this->menuTable[75]=array("id"=>75,"type"=>"link","title"=>"Course Description","link"=>"./sample_page.php?id=75","parent"=>56,"ordering"=>1,"selected"=>false);
					$this->menuTable[76]=array("id"=>76,"type"=>"link","title"=>"Scheduled Sessions","link"=>"./sample_page.php?id=76","parent"=>56,"ordering"=>2,"selected"=>false);

					$this->menuTable[77]=array("id"=>77,"type"=>"link","title"=>"Course Description","link"=>"./sample_page.php?id=77","parent"=>57,"ordering"=>1,"selected"=>false);
					$this->menuTable[78]=array("id"=>78,"type"=>"link","title"=>"Scheduled Sessions","link"=>"./sample_page.php?id=78","parent"=>57,"ordering"=>2,"selected"=>false);

					$this->menuTable[79]=array("id"=>79,"type"=>"link","title"=>"Course Description","link"=>"./sample_page.php?id=79","parent"=>58,"ordering"=>1,"selected"=>false);
					$this->menuTable[80]=array("id"=>80,"type"=>"link","title"=>"Scheduled Sessions","link"=>"./sample_page.php?id=80","parent"=>58,"ordering"=>2,"selected"=>false);

		if(isset($this->menuTable[$selectedMenu])) $this->selectedMenu = $selectedMenu;
		else $this->selectedMenu=$this->homePageID;

		//compute menu's levels
		foreach ($this->menuTable as &$menu){
			$level=1;
			$selected=$menu;
			while(isset($this->menuTable[$selected["parent"]])){
				$level++;
				$selected=$this->menuTable[$selected["parent"]];
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

		//usort($this->menuTable,array($this, "sortByParentId"));
	}

	private function sortByParentId($a,$b){
		if($a["parent"]<$b["parent"]) return -1;
		if($a["parent"]>$b["parent"]) return 1;
		if($a["ordering"]<$b["ordering"]) return -1;
		if($a["ordering"]>$b["ordering"]) return 1;
		return 0;
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
		//NOTE: Menus muste be arranged by level and ordering first before calling this function!!
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

	public function printBreadCrumb(){
		$str="";
		$str.="<div id='breadcrumb'>\n";
		$str.="<a href='".$this->menuTable[$this->homePageID]["link"]."'>".$this->menuTable[$this->homePageID]["title"]."</a> > ";
		for($i=1;isset($this->selectedMenusTree[$i]);$i++){
			if($i!=1) $str.=" > ";
			$menuItem=$this->menuTable[$this->selectedMenusTree[$i]];
			$bold=($menuItem["id"]==$this->selectedMenu);
			$str.="<a href='".$menuItem["link"]."'>".($bold?"<b>":"").$menuItem["title"].($bold?"</b>":"")."</a>";
		}
		$str.="\n</div>\n";
		return $str;
	}
}
?>