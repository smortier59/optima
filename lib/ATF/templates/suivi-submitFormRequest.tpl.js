{strip}
Ext.MessageBox.show({
   title:'{ATF::$usr->trans(tache,module)}',
   msg: '{ATF::$usr->trans(creer_tache,suivi)}',
   buttons: Ext.MessageBox.YESNOCANCEL,
   fn: function (btn,e) {
	   if (btn=="yes") {
			var redirect='tache-insert.html,id_suivi=';
		   {include file="generic-submitFormRequest.tpl.js"}
	   } else if (btn=="no") {
		   {include file="generic-submitFormRequest.tpl.js"}
	   } else {
		 	return false;  
	   }
   },
   icon: Ext.MessageBox.QUESTION
});
{/strip}