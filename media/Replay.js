 
var Replay = function(data, lang) {
	$('#container').html("");
	this.langp = lang;
	this.origin = [0, 0];
	this.Combatant = {};
	this.test = "";
	this.instanceFloor = '<div id="bgFloor"></div>';
	var LastTime = 0;
 	var tl = new TimelineLite();
 	this.tl = tl;
 	var scale = 10;
	var xOff = 0;
 	var yOff = 0;
 	var Boss = "Unknown";
 	var endTime = 0;
 
 	//Turn 13
 	if (Map == 196) {
 		Boss = "Bahamut Prime";
 		this.instanceFloor = '<div id="bgFloor"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"></div></div></div></div></div></div></div>';
 		xOff = 0-(450*9);
 		yOff = 0+(275*1);
 	}
  	//Turn 12
 	if (Map == 195) {
 		Boss = "Phoenix";
 		this.instanceFloor = '<div id="bgFloor"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"></div></div></div></div></div></div></div>';
 		xOff = 0+(450*1);
 		yOff = 0+(275*7)-60;
 	}
  	//Turn 11
 	if (Map == 194) {
 		Boss = "Kaliya";
 		this.instanceFloor = '<div id="bgFloor"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"></div></div></div></div></div></div></div>';
 		xOff = 0+(450*1);
 		yOff = 0+(275*1);
 	}
  	//Turn 10
 	if (Map == 193) {
 		Boss = "Imdugud";
 		this.instanceFloor = '<div id="bgFloor"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"><div class="t10_r"></div></div></div></div></div></div></div>';
 		xOff = 0+(450*1)-15;
 		yOff = 0+(275*12)-60;
 	}

 
	  var cEle = "";
	  for (var f in CombatantList) {
	      if (CombatantList.hasOwnProperty(f) ) {
	      	if (typeof(CombatantList[f]['Job']) != 'undefined') cEle += '<div class="replayEntity" id="cc_'+f+'" ><img src="/img/class/'+CombatantList[f]['Job']+'.png" class="replayClass" /><div class="flyingDmg" id="ff_dmg_'+f+'"></div></div>';
	      	else {
		      	if (getCombatantName(f) == Boss) cEle += '<div class="replayEntity" id="cc_'+f+'" ><img src="/img/icon/boss.png" class="replayBoss" /><div class="flyingDmg" id="ff_dmg_'+f+'"></div></div>';
		      	else cEle += '<div class="replayEntity" id="cc_'+f+'"><div class="cc_circle"></div><div class="cc_name">'+getCombatantName(f)+'</div><div  class="flyingDmg"  id="ff_dmg_'+f+'"></div></div>';
	  		}
	 		 this.Combatant[f] = {'HP': 0+CombatantList[f]['HP'] , 'Over': 0, 'Spawned':false, 'DamageIn': "", 'HealingIn': "", 'BuffIn': "", 'OtherIn': ""};
	      }
	   }

	   $('#container').html(this.instanceFloor + cEle);


 
	if (typeof(data) != undefined || typeof(data) != null) {
	 
		for (var _i=0, _l = data.length; _i < _l; _i++) {
			var LogType = data[_i][0];
			//Damage
			if (LogType == 1 && data[_i][8] == 0 && data[_i][9] == 0) {
 				//place damage on victim
				tl.set('#ff_dmg_'+data[_i][2], {onStart: setFlyingDmg, onStartParams:[data[_i][2], data[_i][4], data[_i][5], data[_i][6]]}, 1+(data[_i][3]/1000)).fromTo('#ff_dmg_'+data[_i][2], 1.5, {opacity:1, left: 0, top: 0}, {opacity: 0, left: 0, top: -80}, 1+(data[_i][3]/1000));
			}
			else if (LogType == 2 && data[_i][8] == 0) {
 		 		tl.set('#ff_heal_'+data[_i][2], {onStart: setFlyingHeal, onStartParams:[data[_i][1], data[_i][2], data[_i][4], data[_i][5], data[_i][6]]}, 1+(data[_i][3]/1000)).fromTo('#ff_heal_'+data[_i][2], 1.5, {opacity:1, top: -50}, {opacity: 0, top: 0}, 1+(data[_i][3]/1000));
			 
			}		
			//Position
			else if (LogType == 5) {

				var time = data[_i][1]/1000;

				for (var __i = 0, __l = data[_i][2].length; __i < __l; __i++) {
					var id = data[_i][2][__i][0],
						x = data[_i][2][__i][1],
						y = data[_i][2][__i][2];
						if (typeof(this.Combatant[id]) != 'undefined') {
							if (this.Combatant[id]['Spawned']) tl.to('#cc_'+id, 2, {left: (x*scale)+xOff, top: (y*scale)+yOff}, time);
							else {
								tl.set('#cc_'+id, {left: (x*scale)+xOff, top: (y*scale)+yOff}, time).fromTo('#cc_'+id, 1, {opacity: 0}, {opacity: 1}, time);
								this.Combatant[data[_i][2][__i][0]]['Spawned'] = true;
							}
						}
				}
			}
			//Event
			else if (LogType == 4) {

				var time = data[_i][4]/1000;

				if (data[_i][3] == 2) { //despawn
					tl.to('#cc_'+data[_i][1], 1, {opacity: 0}, time);
				}
				else if (data[_i][3] == 4) { //death
						tl.to('#cc_'+data[_i][2], 1, {opacity: 0.2, onStart: deadPlayer, onStartParams:[data[_i][2]]}, time);
						 
					}
				else if (data[_i][3] == 3) { //raise (weakness gained)
						tl.fromTo('#cc_'+data[_i][2], 1, {opacity: 0.2}, {opacity: 1}, time);
				}
				else if (data[_i][3] == 6) {
						endTime = time[0]/1000;
				}
				else {
					continue;
				}
			}
			else {
				continue;
			}
 
		}  
	}
 

 this.createMeter = function() {
 	$('#contentbottom').html('<div id="replayBar"><div id="replayControl"> <span onclick="Timeline.tl.play()" class="icon-play"></span><span onclick="Timeline.tl.pause()" class="icon-pause"></span></div><div id="replayTimer"><span id="ctime">00:00</span>/'+tl.totalDuration().toHHMMSS()+'</div><div id="playoutter"><div id="playmeter"></div><div id="playhead"></div></div></div>');
 };
this.createMeter();
 

 Draggable.create($('#playhead'),{
   type:"x",
   lockAxis:true,
   edgeResistance:1, 
   bounds:"#playoutter",
	  onDrag:function(){
	 	tl.progress((this.x+1)/625).pause();
 
	 	}
});


tl.eventCallback("onUpdate", function() {
 
 	TweenLite.set('#playhead', { x: 650*tl.progress()  });
	TweenLite.set('#playmeter', { width: 650*tl.progress()  });
	$('#ctime').html(tl.time().toHHMMSS());

})


tl.pause();

 //var update = setInterval(function() { $('#sec').css({'width':tl.totalProgress()*100+'%'})}, 100)
 
}

Number.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10);  
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = minutes+':'+seconds;
    return time;
}


function createTime(mili) {
	var time = mili;
	var wholeSecond = Math.floor(time/1000)*1000;
	var offset = (time - wholeSecond) / 1000;
	return [wholeSecond, offset]
 }

function deadPlayer(id) {
	Timeline.Combatant[id]['HP'] = 0;
	TweenLite.to('#hp_'+id, 1, {'width':'0%'});
}
function setFlyingHeal(actor, id, swing, name, value) {
		if (swing == 11) name = name+'_';
n = Timeline.langp['Healing'][name];

var i = iconPad(n[1]);
 
	$('#ff_heal_'+id).html('<img src="/img/icon/'+i[0]+i[1]+i[2]+'000/'+i+'.png" class="flyingIcon" /> <span class="flynumber heal">'+ value + '</span><br /><span style="font-size:10px;position:relative;top:-30px;left:18px">'+n[0]+'</span>');

 
 	var overheal = 0;
	Timeline.Combatant[id]['HP'] += value;

	if (name == 297 || name == 129) return; 
	var aa = (Timeline.Combatant[id]['HP']/CombatantList[id]['HP'])*100 + '%';
	if (Timeline.Combatant[id]['HP'] > CombatantList[id]['HP']) {
		//overheal = Timeline.Combatant[id]['HP']-CombatantList[id]['HP'];
		Timeline.Combatant[id]['HP'] = 0+CombatantList[id]['HP'];
		//CombatantList[actor]['Over'] += overheal;
		aa = '100%';
	}
	 TweenLite.to('#hp_'+id, 1, {'width':aa});
 
}

function setFlyingDmg(element, swing, name, value) {
	if (swing == 20) name = name+'_';
n = Timeline.langp['Damage'][name];
 

 
var i = iconPad(n[1]);
 if (name != 7) {
	$('#ff_dmg_'+element).html('<img src="/img/icon/'+i[0]+i[1]+i[2]+'000/'+i+'.png" class="flyingIcon" /> <span class="flynumber dmg">'+ value + '</span><br /><span style="font-size:10px;position:relative;top:-30px;left:18px">'+n[0]+'</span>');
}
else {
	$('#ff_dmg_'+element).html('<span class="flynumber dmg">'+ value + '</span>');
}
//damage hp meter portion

	Timeline.Combatant[element]['HP'] -= value;
 	var aa = (Timeline.Combatant[element]['HP']/CombatantList[element]['HP'])*100 + '%';
 	if (Timeline.Combatant[element]['HP'] < 0) { 
 		aa = '0%'; 
 		Timeline.Combatant[element]['HP'] = 0;
 	}
  	TweenLite.fromTo('#hp_'+element, 1, {'background-color': '#fff'}, {'background-color': '#602d2d', 'width':aa});

}

function iconPad (icon) {
var str = "" + icon;
var pad = "000000";
var i = pad.substring(0, pad.length - str.length) + str;
return i;
}