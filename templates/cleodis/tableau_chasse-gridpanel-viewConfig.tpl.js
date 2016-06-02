{strip}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.json["tableau_chasse.interesse"]=="non") {			
			return 'non_interesse';
		}else if (record.json["tableau_chasse.interesse"]=="oui") {			
			return 'interesse';
		}else if (!record.json){
			return 'aggregate';	
		}
	}
}
{/strip}