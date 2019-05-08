var anixMenu =
[
	['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Aujourd'hui"); ?>", './index.php', null, null],
    _cmSplit,
	['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Clients"); ?>", null,null,null,
        ['icon', "<?php echo _("Ajouter"); ?>", './mod_client.php?action=add', null, null],
        ['icon', "<?php echo _("Rechercher"); ?>", './search_client.php', null,null]
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Ventes"); ?>", null,null,null,
        ['icon', "<?php echo _("Nouvelle commande"); ?>", './choose_client.php?target=order', null, null],
        ['icon', "<?php echo _("Nouvelle facture"); ?>", './choose_order.php?target=invoice', null, null],
        ['icon', "<?php echo _("Nouveau paiement"); ?>", './choose_client.php?target=payment', null, null],
        ['icon', "<?php echo _("Rechercher une commande"); ?>", './search_SO.php', null,null],
        ['icon', "<?php echo _("Rechercher une facture"); ?>", './search_SI.php', null,null]
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Achats"); ?>", null,null,null,
    	['icon', "<?php echo _("Liste des fournisseurs"); ?>", './list_suppliers.php', null,null]
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Configuration"); ?>", null,null,null,
        ['icon', "<?php echo _("Courriels types"); ?>", './list_emails.php', null, null]
    ]
];