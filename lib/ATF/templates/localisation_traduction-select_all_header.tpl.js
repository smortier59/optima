{
	text: "{ATF::$usr->trans('generer',$current_class->table)}",
	cls: 'x-btn-text-icon details',
	icon: '{ATF::$staticserver}images/module/16/exporter.png',
	handler: function(b,e){
		ATF.tpl2div('localisation_traduction,export_base_vers_fichier.ajax');
	}
}