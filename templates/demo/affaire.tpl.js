<script type="text/javascript">/*<![CDATA[*/
function update_session(key,table){
	return document.getElementById(table+"["+key+"]").value;
}

function check_acompte(check){
	if(check=="acompte"){
		document.getElementById('acompte_value').style.visibility="visible";
	}else{
		document.getElementById('acompte_value').style.visibility="hidden";
	}
}

function check_type_facture(nbelement){

	var nb_checked =0;
	for(i=0;i<nbelement;i++){
		if(document.getElementById('facture['+i+']').checked){
			nb_checked++;
		}else{
			document.getElementById('type_facture').style.visibility="hidden";
		}
	}

	if(nb_checked==1){
		document.getElementById('type_facture').style.visibility="visible";
	}else{
		document.getElementById('type_facture').style.visibility="hidden";
		document.getElementById('acompte_value').style.visibility="hidden";
	}
}

{*include file="stock-updateSerial.tpl.js"}
{include file="stock-updateSerialAT.tpl.js"}
{include file="stock-updateadresse_mac.tpl.js"*}
/*]]>*/</script>