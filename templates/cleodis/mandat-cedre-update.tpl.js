{$table="mandat"}
{$no_update='true'}
{$pager="cedre"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	 "{$current_class->table}.texte"
	, "{$current_class->table}.valeur"
	, "{$current_class->table}.type"
	, "{$current_class->table}.ligne_titre"
	, "{$current_class->table}.mandat_type"
	
]}
{if ATF::_r(id_mandat)}
	{$q->reset()->addCondition("id_mandat",classes::decryptId(ATF::_r(id_mandat)))->addCondition("mandat_type",$pager)->addOrder("id_mandat_ligne")->setView([order=>$fields])->end()}
{else}
	{$q->reset()->addCondition("id_mandat",1)->addCondition("mandat_type",$pager)->addOrder("id_mandat_ligne")->setView([order=>$fields])->end()}
{/if}
{include file="mandat_ligne-update.tpl.js"}