{if $infos.facturation_ticket == "oui" && $infos.etat != "done" && $infos.etat != "payee" && $infos.etat != "annulee"}
    {if $infos.ok_facturation=="non"}
    	,{
    		html: 	'<div class="alert alert-danger w50">{ATF::$usr->trans(refus_facturation,$current_class->table)|escape:javascript}<br><a href="javascript:;" onclick="ATF.tpl2div(\'{$current_class->table},boostBilling.ajax\',\'id_hotline={$infos.id_hotline|cryptid}\');"><strong>[{ATF::$usr->trans(relance_client,$current_class->table)|escape:javascript}]</strong></a></div>'
    		,border: false
    	}
    	
    {elseif $infos.ok_facturation!="oui"}
    	,{
    		html: 	'<div class="alert alert-warning w50">{ATF::$usr->trans(facturation_non_valid,$current_class->table)|escape:javascript}<br><a href="javascript:;" onclick="ATF.tpl2div(\'{$current_class->table},boostBilling.ajax\',\'id_hotline={$infos.id_hotline|cryptid}\');"><strong>[{ATF::$usr->trans(relance_client,$current_class->table)|escape:javascript}]</strong></a></div>'
    		,border: false
    	}
    {/if}
{elseif ATF::hotline()->getBillingMode($infos.id_hotline,true)=="Nature de la charge à définir"}
    ,{
        html:   '<div class="alert alert-info w50">{ATF::$usr->trans(facturation_non_set,$current_class->table)|escape:javascript}</div>'
        ,border: false
    }
{/if}