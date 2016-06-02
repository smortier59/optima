{$calcul_loyer='true'}
{$pager="ProduitLoyerUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("produit_loyer")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	  "{$current_class->table}.loyer"
	, "{$current_class->table}.duree"
	, "{$current_class->table}.nature"
]}


{if ATF::_r(id_produit)}
	{$id_produit=ATF::_r(id_produit)}
{/if}


{if $id_produit}	
	{$q->reset()->where("id_produit",ATF::produit()->decryptId($id_produit))->addOrder("ordre")->setView([order=>$fields])->end()}	
{else}
	{$q->reset()->where("id_produit",0)->addOrder("ordre")->setView([order=>$fields])->end()}
{/if}
{include file="produit_loyer-lignes.tpl.js"}