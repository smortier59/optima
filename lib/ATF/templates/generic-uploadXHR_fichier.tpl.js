{strip}
{
	{$key=$key|default:$smarty.request.key}
	{$table=$table|default:$smarty.request.table|default:$current_class->table}
	autoHeight:true,
	id:'xhrUpload-{$table}-{$key}',
	html:'
		<style type="text/css">
		.progress-bar-container { color:white; background-color:#CCC; border:1px solid black; overflow:hidden; position:relative; height:15px;   }
		.progress-bar-container > div.progress-bar { position:absolute; color:white; background-color:#00C; height:15px; overflow:hidden; }
		.uploaded { background-color:green; }
		#drop-area{$key} { background-color:#EEE; border:1px dashed black; cursor:crosshair; height:50px;  padding:1em; font-size:13px; }
		#drop-area{$key}.over { background-color:#DDD; }
		#file-list{$key} > li { float:left; width:250px; margin:.5em; }
		</style>
		<div>
			<input id="bc-FileUpload{$key}" type="file" name="bc-video" class="x-form-file" multiple>
		</div>
		<div id="ajaxUpload{$key}">
			<p id="drop-area{$key}">
				'+ATF.usr.trans('ou_glissez_et_deplacez_un_fichier_dans_cette_zone')+' (Max. {ATF::$maxFileSize}Mo)
				<br />'+ATF.usr.trans('extensions_acceptees')+' [
				{if $current_class->files[$key].convert_from}
					{foreach from=$current_class->files[$key].convert_from item=i}
						{if !$i@first},{/if}
						{$i}
					{/foreach}
				{else}
					{foreach from=$current_class->files[$key].type item=i}
						{if !$i@first},{/if}
						{$i}
					{foreachelse}
						toutes
					{/foreach}
				{/if}
				] 
				{*if !$current_class->files[$key].type || $current_class->files[$key].type==zip}
					<br />Que vous envoyiez un ou plusieurs fichiers, finalement une archive ZIP sera créée sur le serveur contenant ce que vous avez déposé.
				{/if*}
			</p>
			<ul id="file-list{$key}">
				<li class="no-items">'+ATF.usr.trans('aucun_fichier_telecharge_pour_l_instant')+'</li>
			</ul>
		</div>'
	,listeners:{
		'afterRender':function (e) {
			var filesUpload = document.getElementById("bc-FileUpload{$key}"),
				dropArea = document.getElementById("drop-area{$key}"),
				fileList = document.getElementById("file-list{$key}");


			function uploadFile (file) {
				var li = document.createElement("li"),
					div = document.createElement("div"),
					img,
					progressBarContainer = document.createElement("div"),
					progressBar = document.createElement("div"),
					reader,
					xhr,
					fileInfo;
				
				li.appendChild(div);
				progressBarContainer.className = "progress-bar-container";
				progressBar.className = "progress-bar";
				progressBarContainer.appendChild(progressBar);
				li.appendChild(progressBarContainer);
		
				xhr = new XMLHttpRequest();
		
				function handleHttpResponse() {
					if(xhr.readyState == 4 && xhr.status == 200) {
					}
				};
				
				if(xhr.upload.addEventListener){
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var pct = Math.round((evt.loaded / evt.total) * 100);
							progressBar.style.width = pct+"%";
							progressBar.innerHTML = pct+"% ("+evt.loaded+"/"+evt.total+")";
						}
					},false);
		
					xhr.addEventListener("load", function (a,z,e,r) {
						progressBarContainer.className += " uploaded";
						progressBar.style.width = '100%';
						progressBar.innerHTML = ATF.usr.trans('recu_par_le_serveur_web');
						var o = $.parseJSON(this.responseText);
						ATF.ajax_refresh(o);		
						if (Ext.getCmp("{$table}[{$key}]")) {
							Ext.getCmp("{$table}[{$key}]").setValue(o.{$key});
						} else if (Ext.getCmp("{$key}")) {
							Ext.getCmp("{$key}").setValue(o.{$key});
						}
						Ext.getCmp("oldSchoolUploadLabel-{$table}-{$key}").setValue(o.{$key});
					}, false);
				}
		
				xhr.onreadystatechange = handleHttpResponse;
				
				
				xhr.open("post", "{$current_class->table},uploadXHR.ajax,field={$key}&extTpl[{$key}]=generic-upload_fichier", true);
		
				xhr.setRequestHeader("Content-Type", "multipart/form-data");
				xhr.setRequestHeader("X-File-Name",file.name || file.fileName);
				xhr.setRequestHeader("X-File-Size", file.fileSize);
				xhr.setRequestHeader("X-File-Type", file.type);
				xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
				
				if (file.fileSize>{ATF::$maxFileSize}*1024*1024) {
					alert("Fichier trop gros : limite {ATF::$maxFileSize}Mo !");
					return;
				}
				
				xhr.send(file);
				
				var s = parseInt(file.size / 1024, 10);
				fileInfo = "<div><strong>"+ATF.usr.trans('filename')+"</strong> " + file.name + "</div>";
				fileInfo += "<div><strong>"+ATF.usr.trans('filesize')+"</strong> " + s + " kb</div>";
				fileInfo += "<div><strong>"+ATF.usr.trans('filetype')+"</strong> " + file.type + "</div>";
				div.innerHTML = fileInfo;
					
				if (true) { /* UN SEUL FICHIER POUR L'INSTANT ! */
					fileList.innerHTML = "";
				}
				fileList.appendChild(li);
			}
		
			function traverseFiles (files) {
				if (typeof files !== "undefined") {
					for (var i=0, l=files.length; i<l; i++) {
						{if $current_class->files[$key].type}
							var ext = [{foreach from=$current_class->files[$key].convert_from item=i}{if !$i@first},{/if}"{$i}"{/foreach}];
							if (ext.length==0 || !ext) var ext = [{foreach from=$current_class->files[$key].type item=i}{if !$i@first},{/if}"{$i}"{/foreach}];
							if (ext.indexOf(files[i].name.substr(files[i].name.indexOf('.')+1).toLowerCase())>-1) {
								uploadFile(files[i]);
							} else {
								alert("Mauvais "+ATF.usr.trans('filetype')+" ("+files[i].name+")");
								return;
							}
						{else}
							uploadFile(files[i]);
						{/if}
					}
				} else {
					fileList.innerHTML = "No support for the File API in this web browser";
				}
			}
			
			if (filesUpload && dropArea && filesUpload.addEventListener && dropArea.addEventListener) {
				Ext.getCmp("oldSchoolUpload-{$table}-{$key}").destroy();
				if (Ext.getCmp("oldSchoolUpload-download-{$table}-{$key}")) {
					Ext.getCmp("oldSchoolUpload-download-{$table}-{$key}").destroy();
				}
				filesUpload.addEventListener("change", function () {
					traverseFiles(this.files);
				}, false);
			
				dropArea.addEventListener("dragleave", function (evt) {
					var target = evt.target;
					if (target && target === dropArea) {
						this.className = "";
					}
					evt.preventDefault();
					evt.stopPropagation();
				}, false);
			
				dropArea.addEventListener("dragenter", function (evt) {
					this.className = "over";
					evt.preventDefault();
					evt.stopPropagation();
				}, false);
			
				dropArea.addEventListener("dragover", function (evt) {
					evt.preventDefault();
					evt.stopPropagation();
				}, false);
			
				dropArea.addEventListener("drop", function (evt) {
					traverseFiles(evt.dataTransfer.files);
					this.className = "";
					evt.preventDefault();
					evt.stopPropagation();
				}, false);
			} else if (Ext.getCmp("xhrUpload-{$table}-{$key}")) {
				Ext.getCmp("xhrUpload-{$table}-{$key}").destroy();
			}
		}
	}
}
{strip}