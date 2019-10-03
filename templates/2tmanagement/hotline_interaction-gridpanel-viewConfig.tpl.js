{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record.data.hotline_interaction__dot__visible=="non") {
			return 'hotlineInvisible';
		}
	}
}
{/strip}