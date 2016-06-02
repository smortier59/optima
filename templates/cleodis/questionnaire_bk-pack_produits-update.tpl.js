{$pager="pack_produitsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	  "{$current_class->table}.nom"
	, "{$current_class->table}.id_pack_produit"
	
]}
{if ATF::_r(id_questionnaire_bk)}
	{$q->reset()->addCondition("questionnaire_bk_ligne.id_questionnaire_bk",classes::decryptId(ATF::_r(id_questionnaire_bk)))->setView([order=>$fields],true)->end()}
{else}
	{$q->reset()->addCondition("questionnaire_bk_ligne.id_questionnaire_bk",0)->setView([order=>$fields],true)->end()}
{/if}

{include file="pack_produits-update.tpl.js"}