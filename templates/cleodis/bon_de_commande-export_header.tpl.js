{strip}
{if ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe"}
{
  text: 'Export CEGID'
  , handler: function(b,e){
    window.location='{$current_class->name()},export_cegid.ajax,onglet={$pager}';
  }
},
{
  text: 'Export SERVANTISSIMMO'
  , handler: function(b,e){
    window.location='{$current_class->name()},export_servantissimmo.ajax,onglet={$pager}';
  }
},
{/if}
{/strip}
