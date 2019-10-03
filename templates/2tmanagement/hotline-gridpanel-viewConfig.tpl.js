{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,getRowClass: function(record, index) {
		var css ='';
		if (record.data.hotline__dot__visible=="non") {
			css += ' hotlineInvisible';
		}
		if (record.data.hotline__dot__wait_mep=="oui") {
			css +=' hotlineMEP';
		} else if (record.data.hotline__dot__etat=="wait") {
			css +=' hotlineWait';
		} else if (!record.json){
			css +=' aggregate';	
		}

		if(record.data.id_hotline){
			if(record.data.ratio != null){
				if(record.data.ratio <= {$smarty.const.__RATIO_ROI_CREDIT2__}){
					css +=' vbad_ratio';	
				}else if(record.data.ratio < {$smarty.const.__RATIO_ROI_CREDIT__}){
					css +=' bad_ratio';	
				}
			}
		}
		return css;
	}
}
{/strip}