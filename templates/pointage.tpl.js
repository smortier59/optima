{literal}
ATF.pointage = new Object();
var timer = new ATF.countdownTimer(500);

	updateHeight = function () {
		$('#div.slipContentContainer_feuille').each(function (e) {
			e.css({ height: $('#feuilleContainer').height()+"px" });
		});
		$('#div.slipContentContainer_feuilleMois').each(function (e) {
			e.css({ height: $('#feuilleMoisContainer').height()+"px" });
		});
		var hauteur=$('#primary_pointage').height()+10;
		$('#mMiddle').css({ height: hauteur+"px" });
	}

	ATF.__submitMaj = function(id,id_user) {
		timer.start("ATF.__callMaj("+id+","+id_user+");");
	}
	ATF.__callMaj = function(id,id_user) {
		ATF.ajax('pointage,maj.ajax','id_pointage='+id+'&id_user='+id_user+'&temps='+$("#temps["+id+"]").val());
	}
	
	/** Gère les chagement d'état du formulaire à la selection d'un projet.
	 *  Auteur : Quentin JANON <qjanon@absystech.fr>
	 *  date : 20/07/2009
	 *  param : type de projet
	 */
	ATF.pointage.selection_projet = function (type) {
		//Réactivation du submit
		$("#gep").disabled=true;
		$("#cours").disabled=true;
		$("#personnalise").disabled=true;
		$("#type").disabled=true;
		$("#"+type).disabled=false;
		if (type=='personnalise') {
			$("#type").disabled=false;
		}
	}
	
	/** Incrémente le temps de travail (dans le champs input)
	 *  Auteur : Quentin JANON <qjanon@absystech.fr>
	 *  date : 16/10/2009
	 *  param : name le nom de l'élément
	 *  param : type le type du projet
	 *  param : weekend true si le jour fait parti du week-end
	 */
	ATF.pointage.incr_horaire = function (name,type,weekend){
		tab = $('#'+name).val().split(':');
		var time = new Date(2000,01,01,tab[0],tab[1],00,00);
		var hours = time.getHours();
		var minute = time.getMinutes();

		//Incrémentation de l'horaire
		if(hours<24) {
			var newTime = new Date(2000,01,01,hours,minute+15,00,00);
			if (newTime.getMinutes()==0) {
				$('#'+name).val(newTime.getHours()+":0"+newTime.getMinutes());
			} else {
				$('#'+name).val(newTime.getHours()+":"+newTime.getMinutes());
			}
		} else {
			alert('Impossible de travailler plus longtemps !');
			return false;
		}
		
		//Gestion de la couleur
		ATF.pointage.change_color_case_horaire(name,type,weekend);
		return true;
	}
	
	/** Décrémente le temps de travail (dans le champs input)
	 *  Auteur : Quentin JANON <qjanon@absystech.fr>
	 *  date : 16/10/2009
	 *  param : name le nom de l'élément
	 *  param : type le type du projet
	 *  param : weekend true si le jour fait parti du week-end
	 */
	 ATF.pointage.decr_horaire = function (name,type,weekend){
		tab = $('#'+name).val().split(':');
		var time = new Date(2000,01,01,tab[0],tab[1],00,00);
		var hours = time.getHours();
		var minute = time.getMinutes();
		 
		//Décrémentation de l'horaire
		if(hours>0 || minute>0) {
			var newTime = new Date(2000,01,01,hours,minute-15,00,00);
			if (newTime.getMinutes()==0) {
				$('#'+name).val(newTime.getHours()+":0"+newTime.getMinutes());
			} else {
				$('#'+name).val(newTime.getHours()+":"+newTime.getMinutes());
			}
		} else {
			return false;
		}
		
		//Changement de la couleur
		ATF.pointage.change_color_case_horaire(name,type,weekend);
		return true;
	}
	
	
	/** Change la couleur du fond de la case en fonction du type de projet (production,développement)
	 *  Auteur : Quentin JANON <qjanon@absystech.fr>
	 *  date : 16/10/2009
	 *  param : name l'id de l'élément
	 *  param : type le type du projet
	 *  param : weekend true si le jour fait parti du week-end
	 */
	ATF.pointage.change_color_case_horaire = function (name,type,weekend){
		//Gestion du type
		switch (type) {
			case "production" :
				bgcolor="green";
				color="white";
				break;
			case "rd" :
				bgcolor="orange";
				color="black";
				break;
			case "cours" :
				bgcolor="yellow";
				color="black";
				break;
			case "conge" :
				bgcolor="red";
				color="black";
				break;
		}
		
		//Définition de la couleur des éléments
		if($('#'+name).val()=="0:00") {
			if (weekend){
				$('#'+name).style.backgroundColor="white";
				$('#'+name).style.color="black";
			} else {
				$('#'+name).style.backgroundColor="white";
				$('#'+name).style.color="black";
			}
		} else {
			$('#'+name).style.backgroundColor=bgcolor;
			$('#'+name).style.color=color;
		}
	
	}
	
	/** Ajoute le projet sur toute la journée
	*  Auteur : Quentin JANON <qjanon@absystech.fr>
	*  date : 16/10/2009
	*  param : name l'id de l'élément
	*  param : le type du projet
	*/
	ATF.pointage.ajout_journee = function (name,type){
		$('#'+name).val("7:00");
		ATF.pointage.change_color_case_horaire(name,type);
		return true;
	}
		
	ATF.verifChoix = function(name) {
		var radioType = document.getElementsByName(name);
		var checked = false;
		for (var cpt = 0 ; (cpt < radioType.length) && !checked ; cpt++) {
			checked = checked || radioType[cpt].checked;
		}
		
		if (!checked) {
			return false;
		}
		return true;
	}
	
	affiche_hot = function (){
		for (var i=1; i<= document.getElementsByTagName('fieldset').length; i++)
		{
			if(element = document.getElementsByTagName('fieldset')[i]){
				//var element = document.getElementsByTagName('fieldset')[i];
				if(element.id){
					$(element.id).toggle();
				}
			}
		} 
		updateHeight();
	}
	
{/literal}