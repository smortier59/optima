record.data.nom = Ext.util.Format.stripTags(record.data.nom);
$('#{$id|replace:"[":"\\\\["|replace:"]":"\\\\]"}').val({if $extJSGridComboboxSeparator}record.data.nom + ATF.extJSGridComboboxSeparator + {/if}record.data.id);