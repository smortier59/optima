{$pager="formation_devis_fournisseur"}
{$parent_class="formation_devis"}
{$current_class=ATF::getClass("formation_devis_fournisseur")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}

{$fields=["{$current_class->table}.id_societe","{$current_class->table}.type", "{$current_class->table}.montant"]}

{if ATF::_r(id_formation_devis)}
	{$q->reset()->where("id_formation_devis",ATF::formation_devis()->decryptId(ATF::_r(id_formation_devis)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_formation_devis",0)->setView([order=>$fields])->end()}
{/if}

{include file="formation_devis-formation_devis_fournisseur.tpl.js"}
