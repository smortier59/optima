{strip}
[{
	xtype:'awesomeuploader'
	,frame:true
	,width:500
	,height:300
	,ATFParams: {
		staticServer:"{ATF::$staticserver}"
		,url:'upload,xhrupload.ajax,field={$key}&table={$current_class->table}'
		,basicUploadUrl:'upload,basicupload.ajax,field={$key}&table={$current_class->table}'
		,flashUploadUrl:'{if $smarty.const.__ADMIN_WEB_PATH__!==__ADMIN_WEB_PATH__}{$smarty.const.__ADMIN_WEB_PATH__}{else}{$smarty.const.__ABSOLUTE_WEB_PATH__}{/if}upload,flashupload.ajax,field={$key}&table={$current_class->table}&id_user={ATF::$usr->getId()}&codename={ATF::$codename}' 
		,field:"{$key}"
		,table:"{$current_class->table}"
		{if ATF::_r("id_{$current_class->table}")}
			,element:'{ATF::_r("id_{$current_class->table}")}'
		{/if}
		,data:{ATF::upload()->getAllFiles($current_class->table,"{ATF::_r("id_{$current_class->table}")}")}
	}
}]
{strip}