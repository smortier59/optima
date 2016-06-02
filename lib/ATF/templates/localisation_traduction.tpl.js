<script type="text/javascript">/*<![CDATA[*/
/******* SELECT_ALL *******/

/* pouvoir reinitialiser le temp si ya une modif */
var test=new ATF.countdownTimer(1500);

ATF.__submitSearch = function(id_trad,champs,cle) {
	test.start("$('#__res"+champs+"_"+cle+"_loading').show(); ATF.__callSearch('"+id_trad+"','"+champs+"','"+cle+"');");
}
ATF.__callSearch = function(id_trad,champs,cle) {
	ATF.ajax('{$current_class->name()},maj.ajax','nocr=true&id_traduction='+id_trad+'&field='+champs+'&cle='+cle+'&search=OK&new_text='+$('#__res'+champs+'_'+cle).val(),{ onComplete:function(obj) {  $('#__res'+champs+'_'+cle+'_loading').hide(); } });
}
/*]]>*/</script>