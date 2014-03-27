var $=jQuery;var esomap;var siteurl="http://tamrielfoundry.com/";var assets="http://tamrielfoundry.com/wp-content/themes/apocrypha/library/map/";var zone;var locations=[];var activeMarkers=[];var zoneCoords;var allowedBounds;var iconver=0.72;var tilever=0.3;var infowindow;$(document).ready(function(){interactiveMap(zone)});function interactiveMap(k){var b="http://tamriel.objects.dreamhost.com/";var c={name:"Tamriel",mapTypeId:"tamriel",getTileUrl:function(n,m){var l=Math.pow(2,m);n.x=(Math.abs(n.x)<l)?Math.abs(n.x):l-1;n.y=(Math.abs(n.y)<l)?Math.abs(n.y):l-1;return b+m+"_"+n.x+"_"+n.y+".jpg?ver="+tilever},tileSize:new google.maps.Size(256,256),maxZoom:7,minZoom:2,opacity:1};var f=new google.maps.ImageMapType(c);var h="http://coldharbour.objects.dreamhost.com/";var a={name:"Coldharbour",mapTypeId:"coldharbour",getTileUrl:function(n,m){var l=Math.pow(2,m);n.x=(Math.abs(n.x)<l)?Math.abs(n.x):l-1;n.y=(Math.abs(n.y)<l)?Math.abs(n.y):l-1;return h+m+"_"+n.x+"_"+n.y+".jpg?ver="+tilever},tileSize:new google.maps.Size(256,256),maxZoom:4,minZoom:2,opacity:1};var e=new google.maps.ImageMapType(a);var d={center:new google.maps.LatLng(0,0),zoom:2,streetViewControl:false,scaleControl:false,panControl:false,zoomControl:true,mapTypeControl:true,mapTypeControlOptions:{mapTypeIds:["tamriel","coldharbour"]},backgroundColor:"black",draggableCursor:"crosshair"};esomap=new google.maps.Map(document.getElementById("map-canvas"),d);esomap.mapTypes.set("tamriel",f);esomap.mapTypes.set("coldharbour",e);esomap.setMapTypeId("tamriel");allowedBounds=set_bounds("new");google.maps.event.addListener(esomap,"zoom_changed",function(){set_bounds();maybe_hide_markers()});var g=esomap.getCenter();google.maps.event.addListener(esomap,"center_changed",function(){if(allowedBounds.contains(esomap.getCenter())){g=esomap.getCenter();return}esomap.panTo(g)});google.maps.event.addListener(esomap,"maptypeid_changed",function(){var m=esomap.getMapTypeId();var l=$("select#zone-select :selected").attr("value");$("select#zone-select").val("");clear_markers();coords=get_zone_coords(m);zoneCoords=new google.maps.LatLng(coords[0],coords[1]);esomap.setZoom(coords[2]);esomap.panTo(zoneCoords)});infowindow=new google.maps.InfoWindow();if(""!==k){get_markers()}}function set_bounds(){var k=esomap.getZoom();var a=esomap.getCenter();var c=[[-35,-20,35,20],[-70,-100,70,100],[-75,-120,75,120],[-80,-130,80,130],[-85,-135,85,135],[-87,-140,87,140]];var d=c[k-2][0];var e=c[k-2][1];var f=c[k-2][2];var h=c[k-2][3];allowedBounds=new google.maps.LatLngBounds(new google.maps.LatLng(d,e),new google.maps.LatLng(f,h));function b(l){return l>0?1:l<0?-1:0}if(!allowedBounds.contains(a)){if(Math.abs(a.lat())>Math.abs(d)){goodLat=b(a.lat())*(Math.abs(d)-0.01)}else{goodLat=a.lat()}if(Math.abs(a.lng())>Math.abs(e)){goodLng=b(a.lng())*(Math.abs(e)-0.01)}else{goodLng=a.lng()}var g=new google.maps.LatLng(goodLat,goodLng);esomap.panTo(g)}return allowedBounds}function clear_markers(){for(var a=0;a<activeMarkers.length;a++){activeMarkers[a].setMap(null)}activeMarkers=[]}function maybe_hide_markers(){var a=true;var c=(esomap.getMapTypeId()=="tamriel")?3:1;if(esomap.getZoom()<=c){a=false}for(var b=0;b<activeMarkers.length;b++){activeMarkers[b].setVisible(a)}}function get_zone_coords(a){var b=(esomap.getMapTypeId()=="tamriel")?"tamriel":"coldharbour";a=(a==="")?b:a;var c={};c.tamriel=[0,0,2];c.roost=[-68,1.2,6];c.auridon=[-48.93,-76.4,5];c.grahtwood=[-55.42,-18.81,5];c.greenshade=[-48.7,-46.9,5];c.malabal=[-33.4,-39.5,5];c.reapers=[-29.88,-12,5];c.stros=[-12.43,-96.52,6];c.betnikh=[16.06,-120.61,6];c.glenumbra=[37.04,-121.27,5];c.stormhaven=[46.85,-80.66,5];c.rivenspire=[60.13,-92.49,5];c.bangkorai=[42.76,-59.81,5];c.alikr=[29.71,-90.13,5];c.bleakrock=[56.2,53.8,7];c.balfoyen=[16.1,95.1,7];c.stonefalls=[15.15,74,5];c.deshaan=[-3.95,85.33,5];c.shadowfen=[-22.25,77.75,5];c.therift=[35,38,5];c.eastmarch=[50.6,38.75,5];c.cyrodiil=[0.52,16.82,4];c.tactics=[0,0,4];c.crafting=[0,0,4];c.coldharbour=[0,0,2];return c[a]}function get_filtered_types(){var a=["locales","skyshard","lorebook","boss","treasure"];$("#marker-filters :checked").each(function(){for(i=a.length;i>=0;i--){if($(this).val()==a[i]){a.splice(i,1)}}});return a}function is_zone_enabled(a){var b=["roost","bleakrock","balfoyen","stros","betnikh","auridon","stonefalls","glenumbra"];for(i=0;i<b.length;i++){if(b[i]==a){return true}}return false}function get_markers(){clear_markers();var d=esomap.getMapTypeId();var a=$("select#zone-select :selected").attr("value");if(a==="coldharbour"){if(d!=="coldharbour"){esomap.setMapTypeId("coldharbour");return}}else{if(a!==""){if(d!=="tamriel"){esomap.setMapTypeId("tamriel");return}}}var b=get_filtered_types();var c=["skyshard","lorebook","boss","treasure"];coords=get_zone_coords(a);zoneCoords=new google.maps.LatLng(coords[0],coords[1]);esomap.setZoom(coords[2]);esomap.panTo(zoneCoords);if(is_zone_enabled(a)){$.getScript(assets+"zones/"+a+".js",function(){for(i=0;i<locations.length;i++){var g=locations[i][1];if(c.indexOf(g)===-1){g="locales"}var e=true;for(j=0;j<b.length;j++){if(g==b[j]){e=false}}if(e===false){continue}var f={url:assets+"icons/"+locations[i][1]+".png?ver="+iconver,size:new google.maps.Size(24,24),origin:new google.maps.Point(0,0),anchor:new google.maps.Point(12,12)};marker=new google.maps.Marker({position:new google.maps.LatLng(locations[i][3],locations[i][4]),map:esomap,id:i,title:locations[i][0],desc:locations[i][2],type:locations[i][1],icon:f});activeMarkers.push(marker);google.maps.event.addListener(marker,"click",(function(){var l=$("<div />").html(this.desc).text();var k=$("<div />").html(this.title).text();var h='<div class="marker-window"><h3 class="marker-title">'+k+'</h3><p class="marker-content">'+l+"</p></div>";infowindow.setContent(h);infowindow.open(esomap,this)}))}})}};