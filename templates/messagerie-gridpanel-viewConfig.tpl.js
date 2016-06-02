{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,enableRowBody:true
	,emptyText:"Aucune données"
	,showPreview:false
	,getRowClass: function(record, rowIndex, p, store){
		if (record.json["messagerie.seen"]!=1) {
			return "unseen-row";
		}
/*		ATF.log(rowIndex);
		ATF.log(p);
		ATF.log(store);
*/	}
}
{/strip}