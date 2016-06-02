,height:{$item.height|default:$height|default:200}
,plugins: [
	new Ext.ux.form.HtmlEditor.Word(),  
	new Ext.ux.form.HtmlEditor.Divider(),  
	new Ext.ux.form.HtmlEditor.IndentOutdent(), 
	new Ext.ux.form.HtmlEditor.SubSuperScript(),  
	new Ext.ux.form.HtmlEditor.RemoveFormat(),
	new Ext.ux.form.HtmlEditor.SpeedMailInfo(),
	new Ext.ux.form.HtmlEditor.SpeedMailLink(),
	new Ext.ux.form.HtmlEditor.FileManager({
		id:"{ATF::_r('id_emailing_projet')|default:ATF::_r('emailing_projet.id_emailing_projet')}"
		,table: "emailing_projet"
	}) 
]