{strip}
{if ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe"}
{
  text: '{ATF::$usr->trans(export_cegid,$current_class->name())}'
  , handler: function(b,e){
    window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}';
  }
},
{
  text: '{ATF::$usr->trans(export_servantissimmo,$current_class->name())}'
  , handler: function(b,e){
    window.location='{$current_class->name()},export_servantissimmo.ajax,onglet={$pager}';
  }
},
{/if}
{/strip}
