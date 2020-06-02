{strip}
{
  text: '{ATF::$usr->trans(export_middleware,$current_class->name())}', 
  handler: function(b,e){
    window.location='{$current_class->name()},export_middleware.ajax,onglet={$pager}';

  }
},
{/strip}