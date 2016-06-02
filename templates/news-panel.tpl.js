{strip}
var html = '{$infos.details|default:"Aucun détails"}<br><br>';
html += "<span class='newsBottom'>Rédigé par {$infos.redacteur} le {ATF::$usr->date_trans($infos.date)}</span>";


Ext.onReady(function(){
	new Ext.Panel({
		renderTo: 'news_{$infos.id_news}',
		width:'100%',
		height:'auto',
		bodyStyle:'padding:5px',
		title:'{$infos.news}',
		html:html
	});
});
{/strip}