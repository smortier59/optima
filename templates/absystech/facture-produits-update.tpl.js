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
	, "{$current_class->table}.visible"
]}





{if ATF::_r(id_commande)}
	{$q->reset()->where("id_commande",classes::decryptId(ATF::_r(id_commande)))->setView([order=>$fields])->end()}

	{if ATF::affaire()->select( ATF::commande()->select(ATF::_r("id_commande"), "id_affaire")  , "nature") == "consommable"}
		{include file="devis-consommables-lignes_consommable.tpl.js" proxyUrl="commande_ligne,extJSgsa.ajax,pager={$pager}" function="toFactureLigne"}
	{else}
		{include file="devis-produits-lignes.tpl.js" proxyUrl="commande_ligne,extJSgsa.ajax,pager={$pager}" function="toFactureLigne"}
	{/if}
{else if ATF::_r(id_facture)}
	{$q->reset()->where("id_facture",classes::decryptId(ATF::_r(id_facture)))->setView([order=>$fields])->end()}
	
	{if ATF::affaire()->select(ATF::facture()->select(ATF::_r(id_facture), "id_affaire") , "nature") == "consommable"}
		{include file="devis-consommables-lignes_consommable.tpl.js"}
	{else}
		{include file="devis-produits-lignes.tpl.js"}
	{/if}
{else}
	{$q->reset()->where("id_facture",0)->setView([order=>$fields])->end()}
	{include file="devis-produits-lignes.tpl.js"}
{/if}	