{strip}
{if ATF::$codename == "cleodis" || ATF::$codename == "cleodisbe"  || ATF::$codename=="assets" || ATF::$codename=="itrenting" || ATF::$codename=="solo" || ATF::$codename=="arrow"}
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
{
  text: 'Export SERVANTISSIMMO (y compris déja exporté)'
  , handler: function(b,e){
    window.location='{$current_class->name()},export_servantissimmo.ajax,onglet={$pager}&force=true';
  }
},
{/if}
{/strip}
