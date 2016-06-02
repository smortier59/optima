{$pager="{$current_class->table}DepensesUpdate"}
{$parent_class=$current_class}
{$current_class=ATF::getClass("{$current_class->table}_ligne")}
{$q=ATF::_s(pager)->getAndPrepare($pager)}

{$q->reset()->setView([order=>["note_de_frais_ligne.date","note_de_frais_ligne.id_societe","note_de_frais_ligne.montant","note_de_frais_ligne.objet","note_de_frais_ligne.id_frais_kilometrique","note_de_frais_ligne.km"]])->end()}

{if ATF::_r("id_note_de_frais")}
	{$q->where("{$current_class->table}.id_note_de_frais",classes::decryptId(ATF::_r("id_note_de_frais")))->end()}
{elseif $identifiant}
	{$q->where("{$current_class->table}.id_note_de_frais",$identifiant)->end()}
{else}
	{$q->where("{$current_class->table}.id_note_de_frais",0)->end()}
{/if}


{include file="note_de_frais-depenses-lignes.tpl.js"}