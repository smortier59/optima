{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.data.adherent__dot__archive=="oui") {
			return 'adherentInvisible';
		}
	}
}
{/strip}