{$pager="{$current_class->table}ProduitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
		"{$current_class->table}.ref"
		, "{$current_class->table}.produit"
		, "{$current_class->table}.quantite"
		, "{$current_class->table}.prix"
		, "{$current_class->table}.prix_achat"
		, "{$current_class->table}.id_fournisseur"
		, "{$current_class->table}.id_compte_absystech"
		, "{$current_class->table}.serial"		
		, "{$current_class->table}.prix_nb"
		, "{$current_class->table}.prix_couleur"
		, "{$current_class->table}.prix_achat_nb"
		, "{$current_class->table}.prix_achat_couleur"
		, "{$current_class->table}.index_nb"
		, "{$current_class->table}.index_couleur"
		, "{$current_class->table}.serial"
		, "{$current_class->table}.visible"
	]}

{if ATF::_r(id_devis)}
	{$q->reset()->where("id_devis",classes::decryptId(ATF::_r(id_devis)))->setView([order=>$fields])->end()}
	
	{if ATF::devis()->select(ATF::_r(id_devis) , "type_devis") == "consommable"}		
		{include file="devis-consommables-lignes_consommable.tpl.js" proxyUrl="devis_ligne,extJSgsa.ajax,pager={$pager}" function="toCommandeLigne"}
	{else}		
		{include file="devis-produits-lignes.tpl.js" proxyUrl="devis_ligne,extJSgsa.ajax,pager={$pager}" function="toCommandeLigne"}
	{/if}
{else if ATF::_r(id_commande)}
	{$q->reset()->where("id_commande",classes::decryptId(ATF::_r(id_commande)))->setView([order=>$fields])->end()}
	{if ATF::affaire()->select( ATF::commande()->select(ATF::_r("id_commande"), "id_affaire")  , "nature") == "consommable"}
		{include file="devis-consommables-lignes_consommable.tpl.js"}
	{else}		
		{include file="devis-produits-lignes.tpl.js"}
	{/if}	
{else}
	{$q->reset()->where("id_commande",0)->setView([order=>$fields])->end()}
	{include file="devis-produits-lignes.tpl.js"}
{/if}	
