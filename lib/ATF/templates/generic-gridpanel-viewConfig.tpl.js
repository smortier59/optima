{strip}
{*
@param string 
@param array $bodyCols Colonnes à afficher
*}
{
	forceFit:true
	,enableRowBody:true
	,emptyText:"Aucun enregistrement"
	{if $bodyCols}
		,showPreview:true
		,getRowClass: function(record, rowIndex, p, store){
			if(this.showPreview) {
				p.body = '<p>'{foreach from=$bodyCols item=item}+record.data.{$item}{if !$item@last}+'<br />'+{/if}{/foreach}+'</p>';
				return 'x-grid3-row-expanded';
			}
			return 'x-grid3-row-collapsed';
		}
	{else}
		,showPreview:false
		,getRowClass: function(record, rowIndex, p, store){
		}
	{/if}
}
{/strip}