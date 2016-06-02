if (Ext.ComponentMgr.get('emailing_job[prix]')) {
	Ext.Ajax.request({
		url: 'emailing_job,estimationPrix.ajax',
		success: function (r) {
			var result = $.parseJSON(r.responseText).result;
			Ext.ComponentMgr.get('emailing_job[prix]').update(result+" €");
		},
		params: { 
			idEL: record.data.id 
		}
	});	
	
}
