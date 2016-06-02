{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.json["contact.etat"]=="inactif") {
			return 'contactInactif';
		}else if (!record.json){
			return 'aggregate';	
		}
	}
}
{/strip}