{$pager="{$current_class->table}"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("adherent_enfant")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}

{$fields=[
	"{$current_class->table}.prenom"
	,"{$current_class->table}.nom"
	,"{$current_class->table}.date_naissance"
	,"{$current_class->table}.note"
]}
{if ATF::_r(id_adherent)}
	{$q->reset()->where("id_adherent",classes::decryptId(ATF::_r(id_adherent)))->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_adherent",0)->setView([order=>$fields])->end()}
{/if}

{include file="adherent-enfants-lignes.tpl.js"}
