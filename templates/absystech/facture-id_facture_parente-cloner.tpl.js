{strip} {* Condition : ne parcourir que les factures de l'affaire *}
{$affaire = ATF::facture()->select(ATF::_r("id_facture") , "id_affaire")}
  {include file="generic-field-textfield.tpl.js" condition_field=id_affaire condition_value=$affaire}
{/strip}
