{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		if (record && record.json && record.json.stock_alert=="oui") {
			return 'stock_alert_ok';
		}else{
			return 'stock_alert_not';
		}
	}
}
{/strip}