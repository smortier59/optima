{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {		
		if (record.data.parc__dot__existence=="inactif") {
			return 'contactInactif';
		}
	}
}
{/strip}