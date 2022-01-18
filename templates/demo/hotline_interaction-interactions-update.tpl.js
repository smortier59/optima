{$pager="{$current_class->table}InteractionsUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("hotline_interaction")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.date"
	, "{$current_class->table}.detail"
	, "{$current_class->table}.id_user"
]}
{if ATF::_r(id_hotline)}
	{$q->reset()->addCondition("id_hotline",classes::decryptId(ATF::_r(id_hotline)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->addCondition("id_hotline",0)->setView([order=>$fields])->end()}
{/if}

{include file="hotline_interaction-lignes.tpl.js"}