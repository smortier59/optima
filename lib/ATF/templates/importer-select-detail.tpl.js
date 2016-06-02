{
	title:'IMPORT : {ATF::importer()->nom($requests[$current_class->table].id_importer)}',
	layout:'column',
	frame: true,
	frameBorder: true,
	anchor:'100%',
	items: [
    	{
    		xtype:'panel',
    		columnWidth:0.5,
    		border:true,			
    		style: {
				margin: '5px',
				border: '1px solid #d0d0d0',
				paddingBottom: '5px'
    		},
    		bodyStyle:'padding:5px 5px 0',
        	tpl: new Ext.XTemplate(
        		'<table style="width:100%">',
        			'<tbody>',
        				{foreach $current_class->colonnes.primary as $key=>$item}
	        				'<tr>',
	        					'<td class="field">{ATF::$usr->trans($key,$current_class->table)|escape:"javascript"}</td>',
	        					'<td class="value">',

					                {if $item.type == 'date'}
					                        {if $requests[$current_class->table][$key]}
					                            '{ATF::$usr->date_trans($requests[$current_class->table][$key],true)}',
					                        {else}
					                            '-',
					                        {/if}
					                {elseif $item.type == 'datetime' || $key.type == 'timestamp'}
					                        {if $requests[$current_class->table][$key]}
					                            '{ATF::$usr->date_trans($requests[$current_class->table][$key],true,true)}',
					                        {else}
					                            '-',
					                        {/if}
					                {elseif $item.type==textarea}
					                    {if $item.editor}
					                    	'<span class="standard">{literal}{{/literal}{$key}{literal}|nl2br}{/literal}</span>',
					                    {else}
					                    	'{literal}{{/literal}{$key|nl2br|default:"-"}{literal}}{/literal}',
					                    {/if}
					                {elseif $item.type==set}
					                	'{ATF::$usr->transFromSet($key,$current_class->table,false,true,$key)|escape:"javascript"}',
	
					                {else}
					                    {if $item.FCKEditor}                              
					                    {elseif $requests[$current_class->table][$key]}
				                            {include file="hrefv2.tpl.htm" 
				                                link=$current_class->fk_from($key) 
				                                text=$requests[$current_class->table][$key]|nl2br|default:"-"
				                                id=$requests[$current_class->table]["{$key}_fk"]|default:$requests[$current_class->table]["{$current_class->table}.id_{$current_class->table}"]|default:$requests[$current_class->table]["id_{$current_class->table}"] 
				                                table=ATF::getClass($current_class->fk_from($key,false,true))->name()
				                                field=$key
				                                field_info=$item
				                                truncate=false}
					                    {else}
					                    	-
					                    {/if}				                    	
					                {/if}
        						'</td>',
	        				'</tr>',
        				{/foreach}
        			'</tbody>',
    			'</table>'
        	),
        	data: {
        		{foreach $current_class->colonnes.primary as $key=>$item}
    				{if !$item@first},{/if}
    				'{$key}':'{$requests["{$current_class->table}"][$key]|addslashes}'
    			{/foreach}
        	}
    	},{
    		xtype:'panel',
    		columnWidth:0.5,
    		border:true,			
    		style: {
				paddingBottom: '5px'
    		},
			layout:'accordion',
			layoutConfig: {
		        // layout-specific configs go here
		        titleCollapse: true,
		        animate: true,
		        fill: false, /* Evite un redimensionnement assez foireux ma foi*/
		    },
    		items:[
    			{foreach $current_class->colonnes.panel as $k=>$i}
    				{if !$current_class->panels[$k].hideOnSelect}
	    				{
	    					xtype: 'panel',
	    					title: "{ATF::$usr->trans($k,$current_class->table)|escape:"javascript"}",
				    		style: {
								margin: '5px',
								border: '1px solid #d0d0d0',
								paddingBottom: '5px'
				    		},
				    		bodyStyle:'padding:5px 5px 0',
				        	tpl: new Ext.XTemplate(
				        		'<table style="width:100%">',
				        			'<tbody>',
				        				{foreach $i as $key=>$item}
					        				'<tr>',
					        					'<td class="field">{ATF::$usr->trans($key,$current_class->table)|escape:"javascript"}</td>',
					        					'<td class="value">',
				
									                {if $item.type == 'date'}
									                        {if $requests[$current_class->table][$key]}
									                            'ATF::$usr->date_trans($requests[$current_class->table][$key],true)',
									                        {else}
									                            '-',
									                        {/if}
									                {elseif $item.type == 'datetime' || $key.type == 'timestamp'}
									                        {if $requests[$current_class->table][$key]}
									                            '{ATF::$usr->date_trans($requests[$current_class->table][$key],true,true)}',
									                        {else}
									                            '-',
									                        {/if}
									                {elseif $item.type==textarea}
									                    {if $item.editor}
									                    	'<span class="standard">{literal}{{/literal}{$key}{literal}|nl2br}{/literal}</span>',
									                    {else}
									                    	'{literal}{{/literal}{$key|nl2br|default:"-"}{literal}}{/literal}',
									                    {/if}
									                {elseif $item.type==set}
									                	'{ATF::$usr->transFromSet($key,$current_class->table,false,true,$key)|escape:"javascript"}',
					
									                {else}
									                    {if $item.FCKEditor}                              
									                    {elseif $requests[$current_class->table][$key]}
								                            {include file="hrefv2.tpl.htm" 
								                                link=$current_class->fk_from($key) 
								                                text=$requests[$current_class->table][$key]|nl2br|default:"-"
								                                id=$requests[$current_class->table]["{$key}_fk"]|default:$requests[$current_class->table]["{$current_class->table}.id_{$current_class->table}"]|default:$requests[$current_class->table]["id_{$current_class->table}"] 
								                                table=ATF::getClass($current_class->fk_from($key,false,true))->name()
								                                field=$key
								                                field_info=$item
								                                truncate=false}
									                    {else}
									                    	-
									                    {/if}				                    	
									                {/if}
				        						'</td>',
					        				'</tr>',
				        				{/foreach}
				        			'</tbody>',
				    			'</table>'
				        	),
				        	data: {
				        		{foreach $i as $key=>$item}
				    				{if !$item@first},{/if}
				    				'{$key}':'{$requests["{$current_class->table}"][$key]|addslashes}'
				    			{/foreach}
				        	}
	    				},
    				{/if}
    			{/foreach}
    			{if $cols_sec=$current_class->colonnes("secondary",'select')}
	    			{
						xtype: 'panel',
						title: "{t w=cadre_information_supplementaire p=$current_class->table}",
			    		style: {
			    			padding: '10px'
			    		},
			    		bodyStyle:'padding:5px 5px 0',
			        	tpl: new Ext.XTemplate(
			        		'<table style="width:100%">',
			        			'<tbody>',
			        				{foreach $cols_sec as $key=>$item}
				        				'<tr>',
				        					'<td class="field">{ATF::$usr->trans($key,$current_class->table)|escape:"javascript"}</td>',
				        					'<td class="value">',
							                {if $item.type == 'date'}
							                        {if $requests[$current_class->table][$key]}
							                            'ATF::$usr->date_trans($requests[$current_class->table][$key],true)',
							                        {else}
							                            '-',
							                        {/if}
							                {elseif $item.type == 'datetime' || $key.type == 'timestamp'}
							                        {if $requests[$current_class->table][$key]}
							                            '{ATF::$usr->date_trans($requests[$current_class->table][$key],true,true)}',
							                        {else}
							                            '-',
							                        {/if}
							                {elseif $item.type==textarea}
							                    {if $item.editor}
							                    	'<span class="standard">{literal}{{/literal}{$key}{literal}|nl2br}{/literal}</span>',
							                    {else}
							                    	'{literal}{{/literal}{$key|nl2br|default:"-"}{literal}}{/literal}',
							                    {/if}
							                {elseif $item.type==set}
							                	'{ATF::$usr->transFromSet($key,$current_class->table,false,true,$key)|escape:"javascript"}',
			
							                {else}
							                    {if $item.FCKEditor}                              
							                    {elseif $requests[$current_class->table][$key]}
						                            {include file="hrefv2.tpl.htm" 
						                                link=$current_class->fk_from($key) 
						                                text=$requests[$current_class->table][$key]|nl2br|default:"-"
						                                id=$requests[$current_class->table]["{$key}_fk"]|default:$requests[$current_class->table]["{$current_class->table}.id_{$current_class->table}"]|default:$requests[$current_class->table]["id_{$current_class->table}"] 
						                                table=ATF::getClass($current_class->fk_from($key,false,true))->name()
						                                field=$key
						                                field_info=$item
						                                truncate=false}
							                    {else}
							                    	-
							                    {/if}				                    	
							                {/if}

			
			        						'</td>',
				        				'</tr>',
			        				{/foreach}
			        			'</tbody>',
			    			'</table>'
			        	),
			        	data: {
			        		{foreach $cols_sec as $key=>$item}
			    				{if !$item@first},{/if}
			    				'{$key}':'{$requests["{$current_class->table}"][$key]|addslashes}'
			    			{/foreach}
			        	}
	    			}
	    		{/if}
    		]
    	}
	]
}