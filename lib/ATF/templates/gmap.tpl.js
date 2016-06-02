
{* 
@param $table 
@param $pager 
@param $function
@param $noAutoLocate

@param $defaultLat
@param $defaultLong
@param $defaultZoom
*}
{if $table && $pager}
	{if !$data}
		{$q=ATF::_s(pager)->create($pager,null,true)}
		{ATF::getClass($table)->setQuerier($q)}
		{if $function}
			{ATF::getClass($table)->$function()}
		{elseif $condition_key && $condition_value}
			{ATF::getClass($table)->q->setCount(false)->addField("`$table`.id_`$table`,latitude,longitude")->where($condition_key,$condition_value)->reset('limit,page')->end()}
		{else}
			{ATF::getClass($table)->q->setCount(false)->addField("`$table`.id_`$table`,latitude,longitude")->where('latitude',"-1","AND","lat","!=")->where('latitude',null,"or","lat","IS NULL")->reset('limit,page')->end()}
		{/if}

		{$data=ATF::getClass($table)->select_all()}	
	{/if}
	{if $q->nb_rows>$smarty.const.__LIMITE_LOCALISATION__ && !$nolimit}
		Modalbox.show('<div>{ATF::$usr->trans(localisation_limitee)|mt:[limite=>$smarty.const.__LIMITE_LOCALISATION__]|escape:quotes}</div>', { title:'{ATF::$usr->trans(localisation_limitee_titre)|escape:quotes}' });
	{elseif $data}
		if (!$('#{$pager}__GMap').length) {
			ATF.loadMask.show();

			ATF.mapToLoad = function () {
				$('#{$pager}GMapContainer').show();
				var __GMap_myOptions = {
					center:new google.maps.LatLng(48.853,2.35),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				/*var image = '{ATF::$staticserver}images/module/48/{$table}.png';*/
				var markers = [];
				var bounds = new google.maps.LatLngBounds();
				var boundsCount=0;
				var marker;
				var div = $('#{$pager}GMap')[0];
				var {$pager}__GMap = new google.maps.Map(div, __GMap_myOptions);
				{foreach from=$data key=key item=item}
					{$id=$item["{$table}.id_{$table}_fk"]|default:$item["{$table}.id_{$table}"]|default:$item["id_{$table}"]}
					{if $item.latitude && $item.longitude && $item.latitude!="-1"}
						{$lat=$item.latitude}
						{$long=$item.longitude}
					{elseif !$noAutoLocate}
						{* si pas de latitude et longitude on va voir si on peut les générer nous meme *}
						{$coor=ATF::getClass($table)->genLatLong($id)}
						{if $coor}
							{$lat=$coor.lat}
							{$long=$coor.long}
						{/if}
					{/if}
					{if $lat && $long}
						var position = new google.maps.LatLng({$lat}, {$long});

						{if $item.radius}
							var marker = new google.maps.Circle({
								strokeColor: "#{$item.couleur|default:"000000"}",
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: "#{$item.couleur|default:"000000"}",
								fillOpacity: 0.35,
								map: {$pager}__GMap,
								center: position,
								radius: {if $item.radius>500}{$item.radius}{else}500{/if}
							});
						{else}
							var marker = new google.maps.Marker({
								position: position
							});
						{/if}

						google.maps.event.addListener(marker, "click", function addMarker{$id}() {
							var position = new google.maps.LatLng({$lat}, {$long});

							var content = document.createElement("DIV");
						    content.innerHTML = '{include file="gmap_info.tpl.htm" infos=ATF::getClass($table)->select($id) id=$id assign=i}{$i|escape:javascript}';
							var streetview = document.createElement("DIV");
							{if $streetview!==false}
								streetview.style.width = "200px";
								streetview.style.height = "200px";
								content.appendChild(streetview);
							{/if}
							var infowindow = new google.maps.InfoWindow({ 
								content: content
								,size: new google.maps.Size(25,25)
								,position: position
							});
							{if $streetview!==false}
								google.maps.event.addListenerOnce(infowindow, "domready", function streetViewDomready{$id}() {
								    var panorama = new google.maps.StreetViewPanorama(streetview, {
								        navigationControl: false,
								        enableCloseButton: false,
								        addressControl: false,
								        linksControl: false,
								        visible: true,
								        position: position
								    });
								});
							{/if}

							infowindow.open({$pager}__GMap);
						});
						bounds.extend(position);
						boundsCount++;
						markers.push(marker);
					{/if}
				{/foreach}

				var markerCluster = new MarkerClusterer({$pager}__GMap, markers);
				{if $defaultLat && $defaultLong && $defaultZoom}
					  {$pager}__GMap.setCenter(new google.maps.LatLng({$defaultLat}, {$defaultLong}));
					  {$pager}__GMap.setZoom({$defaultZoom});
				{else}
					if (boundsCount > 1) {
					  {$pager}__GMap.fitBounds(bounds);
					} else if (boundsCount == 1) {
					  {$pager}__GMap.setCenter(new google.maps.LatLng({$lat}, {$long}));
					  {$pager}__GMap.setZoom({$defaultZoom|default:25});
					}
				{/if}
				ATF.loadMask.hide();
			};
			var sgm = document.createElement("script");
			sgm.type = "text/javascript";
			sgm.src = "https://maps.google.com/maps/api/js?sensor=false&callback=ATF.mapToLoad";

			var s = document.createElement("script");
			s.type = "text/javascript";
			s.src = "{ATF::$staticserver}js/markerclusterer{if ATF::$debug}-debug{/if}.js";
			s.onload = function() {
			  document.body.appendChild(sgm);
			};
			document.body.appendChild(s);

      	} else {
			Modalbox.show('<div>Problème HTML</div>');
		}
	{else}
		Modalbox.show('<div>bug pas de $data...</div>');
	{/if}
{else} 
	Modalbox.show('<div>ERREUR LOCALISATION-1</div>');
{/if}
