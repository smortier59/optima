{$pager="ProduitFournisseurUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("produit_fournisseur_loyer")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.id_fournisseur",
	"{$current_class->table}.nb_loyer",
	"{$current_class->table}.loyer",
	"{$current_class->table}.ordre",
	"{$current_class->table}.periodicite",
	"{$current_class->table}.nature",
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
{include file="produit_loyer_fournisseur-lignes.tpl.js"}