{strip}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.json["affaire_candidat.recrute"]=="non") {			
			return 'non_recrute';
		}else if (record.json["affaire_candidat.recrute"]=="oui") {			
			return 'recrute';
		}else if (!record.json){
			return 'aggregate';	
		}
	}
}
{/strip}