{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.json["exa_zone_geo.status"]=="inactif") {
			return 'StatusInactif';
		}else if (!record.json){
			return 'aggregate';	
		}
	}
}
{/strip}