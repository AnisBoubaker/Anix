var anixMenu =
[
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Pages Statiques");?>", null, null, null,
    <?php
    	$found=false;
    	foreach ($pageCategories as $idPageCat => $pageCat){
    		echo ",\n";
    		echo "['<img src=\"../js/ThemeOffice/folder.gif\" style=\"float:left;margin-right:3px;\" />', \"".$pageCat["name"]."\", './list_pages.php?idCategory=".$idPageCat."', null,null]";
    	}
    	echo "\n";
    ?>
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Menus");?>", null, null, null,
    <?php
    	$found=false;
    	foreach ($menuCategories as $idMenuCat => $menuCat){
    		echo ",\n";
    		echo "['<img src=\"../js/ThemeOffice/folder.gif\" style=\"float:left;margin-right:3px;\" />', \"".$menuCat["name"]."\", './list_menus.php?idCategory=".$idMenuCat."', null,null]";
    	}
    	echo "\n";
    ?>
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Recherche");?>", './search.php', null, null]
];
