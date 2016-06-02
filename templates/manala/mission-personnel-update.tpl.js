{$pager="{$current_class->table}PersonnelUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}
{$fields=[
	"{$current_class->table}.id_personnel"
	, "{$current_class->table}.prix"
	, "{$current_class->table}.panier_repas"
	, "{$current_class->table}.nb_panier_repas"
	, "{$current_class->table}.prix_panier_repas"
	, "{$current_class->table}.defraiement"
	, "{$current_class->table}.indemnite_defraiement"
	, "{$current_class->table}.poste"
	, "{$current_class->table}.heure_totale"
]}
{if ATF::_r(id_mission)}
	{$q->reset()->where("id_mission",classes::decryptId(ATF::_r(id_mission)))->where('etat','en_attente')->setView([order=>$fields])->end()}
{else}
	{$q->reset()->where("id_mission",0)->setView([order=>$fields])->end()}
{/if}
{include file="mission-personnel-lignes.tpl.js"}