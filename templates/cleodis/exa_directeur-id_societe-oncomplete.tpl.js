Ext.getCmp('restriction_geo_tree').loader.dataUrl = 'societe,getTree.ajax,type=restriction_geo&id_societe='+record.data.id;
Ext.getCmp('restriction_geo_tree').root.reload();


Ext.getCmp('restriction_services_tree').loader.dataUrl = 'societe,getTree.ajax,type=restriction_services&id_societe='+record.data.id;
Ext.getCmp('restriction_services_tree').root.reload();
