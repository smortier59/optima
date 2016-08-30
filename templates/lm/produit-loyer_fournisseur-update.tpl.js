{$pager="ProduitFournisseurUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("produit_fournisseur")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.id_fournisseur",
	"{$current_class->table}.prix_prestation",
	"{$current_class->table}.recurrence",
	"{$current_class->table}.departement"
]}


{if ATF::_r(id_produit)}
	{$id_produit=ATF::_r(id_produit)}
{/if}


{if $id_produit}
	{$q->reset()->where("id_produit",ATF::produit()->decryptId($id_produit))->setView([order=>$fields])->end()}	
{else}
	{$q->reset()->where("id_produit",0)->setView([order=>$fields])->end()}
{/if}
{include file="produit_fournisseur-lignes.tpl.js"}