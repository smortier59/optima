{$id_societe=$requests.id_societe|default:$requests.devis.id_societe}
{include file="generic-field-textfield.tpl.js" 
fieldLabel=ATF::$usr->trans(societe,societe) 
value=$id_societe
est_nul=true}