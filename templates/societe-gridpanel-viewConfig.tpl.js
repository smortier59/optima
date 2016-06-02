{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.json["societe.etat"]=="inactif") {
			return 'societeInactif';
		}else if (!record.json){
			return 'aggregate';	
		}
	}
}
{/strip}