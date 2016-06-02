{strip}
[
	{
		xtype: 'panel',
		id:'crImport',
		title:"{ATF::$usr->trans('compteRenduImport','emailing_source')}",
		items: [
			{
				html:"<span class='labelReportImport'>{ATF::$usr->trans('numEnrTraite','emailing_source')}</span> : <span class='resultReportImport success'>{$numEnrTraite}</span>"
			}
			,{
				html:"<span class='labelReportImport'>{ATF::$usr->trans('numInsert','emailing_source')}</span> : <span class='resultReportImport success'>{$numInsertOK}</span>"
			}
			,{
				html:"<span class='labelReportImport'>{ATF::$usr->trans('numInsertNOK','emailing_source')}</span> : <span class='resultReportImport erreur'>{$numInsertNOK}</span>"
			}
			,{
				html:"<span class='labelReportImport'>{ATF::$usr->trans('numUpdate','emailing_source')}</span> : <span class='resultReportImport success'>{$numUpdateOK}</span>"
			}
			,{
				html:"<span class='labelReportImport'>{ATF::$usr->trans('numUpdateNOK','emailing_source')}</span> : <span class='resultReportImport erreur'>{$numUpdateNOK}</span>"
			}
			{if $champsInconnu}
				,{
					html:"<div class='labelReportImport'>{ATF::$usr->trans('champsInconnu','emailing_source')}</div><span class='reportImportErreur erreur'>{$champsInconnu}</span>",
					name:"champsInconnu"
				}
			{/if}
			{if $erreurs}
				,{
					html:"<div class='labelReportImport'>{ATF::$usr->trans('rapportErreurBrut','emailing_source')}</div><div class='reportImportErreur erreur'>{$erreurs}</div>",
					name:"erreurs"
				}
			{/if}
		]
	}
]	
{strip}