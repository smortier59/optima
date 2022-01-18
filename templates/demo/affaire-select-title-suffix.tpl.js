{
	xtype: 'button',
	iconCls: 'iconAffaireTerminee',
	id:'btnAffaireTerminee',
	scale: 'medium',
	tooltip:'{ATF::$usr->trans("termine",$current_class->table)}',
	{if $infos.etat=="terminee"}disabled: true,{/if}
	style: {
		paddingTop: '5px', 
		marginLeft: "5px",
		float: 'left'
	},
	handler: function () {
		Ext.Msg.confirm(
			'Confirmation',
			'{ATF::$usr->trans(Etes_vous_sur)|escape:javascript}',
			function (value) {
				if (value=="yes") {
					ATF.tpl2div(
						'{$current_class->table},u.ajax',
						'id_{$current_class->table}={$infos["id_{$current_class->table}"]|cryptid}&etat=terminee'
					); 			
				}

			}
		);
	}
}