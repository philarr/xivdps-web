<?php
  
$conn = new mysqli(DBAuth::$server, DBAuth::$user, DBAuth::$pass, DBAuth::$name);

if ($conn->connect_error) {
  die('Database connection failed');
}

$EncounterID = $_GET['page'];
$EncounterCode = explode("_", $EncounterID);  
//$petnames = ["Eos", "Selene", "Garuda-Egi", "Titan-Egi", "Ifrit-Egi", "Emerald Carbuncle", "Topaz Carbuncle"];
 
//Build Page

if (count($EncounterCode) > 1) {
  if ($EncounterCode[0] == "id") $sql="SELECT ID, GameID, ZoneID, StartTime, Duration, TotalFriendlyDamage, LimitBreakDamage, Death, Version1, Version2 FROM `Encounter` WHERE ID = '".$EncounterCode[1]."' ";
  else $sql="SELECT ID, ZoneID, GameID, StartTime, Duration, TotalFriendlyDamage, LimitBreakDamage, Death, Version1, Version2 FROM `Encounter` WHERE PartyID='".$EncounterCode[0]."' AND BattleID='".$EncounterCode[1]."' ";
    $result=$conn->query($sql);
  if ($result->num_rows == 0) {
    die("Encounter not found");
  }
   
   
    $Meta = $result->fetch_assoc();

    if ($Meta['Version1'] == "1.5.0.5") die("outdated version");
 
    $Meta["ZoneName"] = ZoneID($Meta["ZoneID"]);
    $ZoneNPC = getZoneNPC($_LANG, $Meta['ZoneID']);

    $sql="SELECT GameID, Type, Name, Job, HP, MP, StartBuff, OwnerID, TotalDamage, TotalHealing FROM `Combatant` WHERE EncounterID='".$Meta['ID']."' ";
    $result=$conn->query($sql);
    $BossName = "";
    $Party = array();
    $Enemy = array();
    $Pet = array();
    $PetID = array(); 
     while($row = $result->fetch_assoc())
     {
      //special cases
     	if ($row['Name'] == "4982896_3314" || $row['Name'] == "4982895_3314") continue;

      if ($row['GameID'] == $Meta['GameID']) { $Meta['GameID'] = $row['Name']; }


        if ($row['Type'] == 2  ) {
          if ($ZoneNPC[$row['Name']][1]) $BossName = $ZoneNPC[$row['Name']][0];
           $Enemy[$ZoneNPC[$row['Name']][0]][] = $row;
        }
        else {

          if ($row['OwnerID'] != 0) {
             $row['Job'] = 99;
             $row['Name'] = getName($_LANG, $row['Name']);
             $Pet[$row['OwnerID']][] = $row;
             $PetID[] = $row['GameID'];

          }
          else {
           
             $Party[$row['Name']] = $row; 
          }
           
        }
     }

     foreach ($Party as $key => $value) {
       if (isset($Pet[$value['GameID']])) {
         $Party[$key]['Pet'] = $Pet[$value['GameID']];
 
        foreach($Pet[$value['GameID']] as $cpet => $petinfo) {
              $Party[$key]['TotalDamage'] += $petinfo['TotalDamage'];
              $Party[$key]['TotalHealing'] += $petinfo['TotalHealing']; 
        }
 
       }
     }


     $sql = "SELECT GameID, Type, Time, VictimID FROM `CombatantEvent` WHERE EncounterID='".$Meta['ID']."' AND Type IN (1,4,7,8) ";
     $result=$conn->query($sql);
     $SpawnTime = array();
     $DeathTime = array();
 
      while($row = $result->fetch_assoc())
      {
          if ($row['Type'] == 1) $SpawnTime[$row['GameID']] = $row['Time'];
          if ($row['Type'] == 4 && !in_array($row['VictimID'], $PetID)) $DeathTime[$row['VictimID']][] = ["Killer" => $row['GameID'], "Time" => $row['Time']];
       //   if ($row['Type'] == 7) $SongStart[$row['GameID']][$row['VictimID']][] = $row['Time'];
       //   if ($row['Type'] == 8) $SongEnd[$row['GameID']][$row['VictimID']][] = $row['Time'];
          
      }

      $temp = sortDPS($Party);
 
      $plotSong = [];

    $sql = "SELECT GameID, Time, Fade, SkillID FROM `CombatantBuffDuration` WHERE EncounterID='".$Meta['ID']."' AND SkillID IN (135, 137, 139)";
     $result=$conn->query($sql);
      while($row = $result->fetch_assoc())
      {
        $plotSong[] = ['color' => getSongColor($row['SkillID']), 'from' => $row['Time'], 'to' => $row['Fade'], 'GameID'=>$row['GameID'], 'Name' => addslashes(getData($_LANG, 21, $row['SkillID'])[0])];
      }
 }
 else {
  die("Error!");
 }
?>
<div style="clear:both;height:109px;background-image: url('/img/header_2.51.jpg');position: relative; ">

  <!-- header -->
  <div style="background-color: rgba(0,0,0,0.3); padding:15px; width:100%;margin-bottom:10px;position: absolute; bottom: 0; ">
    <div id="encounterh" class="encounterbig" style="margin-left:-35px;opacity:0;">
      <?php echo $BossName; ?> <br />    
      <?php echo "<span class=\"encounterdesc\">".$Meta['ZoneName'][1]."</span>"; ?>
    </div>
    <div style="position:absolute;right:50px;bottom:30px">
      <span style="margin-right:25px;"><span class="icon-shield"></span>&nbsp;&nbsp;&nbsp;Freecompany</span>
      <span style="margin-right:25px;"><span class="icon-globe"></span>&nbsp;&nbsp;&nbsp;Server(RE)</span>
      <?php
        require_once("lang_selector.php");
      ?>
      </div>
    </div>
  </div>
  <!-- end of header -->


<div id="maincontent">

  <script src="http://code.highcharts.com/stock/highstock.js"></script> 
  <script src="http://code.highcharts.com/stock/highcharts-more.js"></script> 
  <script src="http://code.highcharts.com/modules/no-data-to-display.js"></script>
  <script src="media/sort.js"></script> 
  <script src="media/chart.js"></script>
  <script src="media/Replay.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/utils/Draggable.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TimelineMax.min.js"></script>
  <script type="text/javascript">
  /* override log function due to Highcharts not supporting jumped lines graphs */
  
    var console = {};
    console.log = function() {};


  /* Globals, very nasty */



    var Language;
    var Map = <?php echo $Meta['ZoneID']; ?>;
    var SongTip = "";
    var optionState = {
        ShowSong: true
    };
    var Timeline = null;
    var Type, Filter, Section, chart,
        SummaryPage = {
        chart: {
                renderTo: 'container',
                defaultSeriesType: 'column',
               spacing: [25, 0, 15, 0],
               height: 325
        },
        plotOptions: {
          column: {
              dataLabels: {
                inside: false,  
                enabled: true
              },
              showInLegend: false,
              borderColor: '#6e4191',
              color: '#512771',
              grouping: false,
              shadow: false,
              borderWidth: 1,
              stickyTracking: false,
              pointWidth: 45,
          },
        },
        xAxis: {
              categories: [<?php foreach ($temp as $key => $value) { if($value['OwnerID'] != "0") continue; echo "\"".$value['Name']."\","; } ?>]
            },
         series: [{
            name: 'DPS',
               data: [<?php  foreach ($temp as $key => $value) { if($value['OwnerID'] != "0") continue; echo floor($value['TotalDamage']/($Meta['Duration']/1000)) . ","; } $temp=null; ?>]
        }]
     };

    var CombatantList = {<?php
    foreach ($Party as $key => $value) {
     echo " \"".$value['GameID']."\" : {Name:\"".$key."\", Job:\"".JobID($value['Job'])."\", HP:".$value['HP']."}, ";
    }


     foreach ($Pet as $p) {
      foreach($p as $value) {
        echo " \"".$value['GameID']."\" : {Name:\"".$key."\", Job:\"".JobID($value['Job'])."\", HP:".$value['HP']."}, ";
      }
    }

    foreach ($Enemy as $key => $value) {
      
      foreach($value as $e) {
        echo " \"".$e['GameID']."\" : {Name:\"".$key."\", HP:".$e['HP']."}, ";
      }
    }
      echo " \"1\" : {Name: \"Environment\"}, ";
   ?>};


  //XIVDPS Module


  var XIVDPS = function() {
   

    function getCombatantName(ID) {
      if (typeof(CombatantList[ID]) == 'undefined') return 'Unknown';
        return CombatantList[ID]['Name'];
    }
 

    function initReport() {

        var getParam = window.location.hash.substr(1).split('/');
       Highcharts.setOptions(Highcharts.theme);
      TweenLite.to('#encounterh', 1, {opacity: 1, marginLeft: '15px'});
     
     
        
 
        Type = getParam[0];
        Section = getParam[1];
        Filter = getParam[2];
      
        if (typeof(getParam[0]) == 'undefined' || getParam[0] == "") Type = "Summary";

        if (typeof(getParam[2]) == 'undefined' || getParam[2] == "") Filter = "Friendly";

        ViewReport(Type, Section, Filter);
    }


    function toggleSong(){ 
      if (!optionState.showSong) {
          for (var i = chart.xAxis[0].plotLinesAndBands.length - 1; i >= 0; i--) {
             chart.xAxis[0].plotLinesAndBands[i].svgElem.hide();
            
          };
          optionState.showSong = true;
       }
       else {
          for (var i = chart.xAxis[0].plotLinesAndBands.length - 1; i >= 0; i--) {
             chart.xAxis[0].plotLinesAndBands[i].svgElem.show();

          };
          optionState.showSong = false;
       }
    }
     
 
    function ViewReport(a, b, c) 
    {
      if (Timeline != null) {
        Timeline.tl.pause(0, true);
        Timeline.tl.remove();
        Timeline = null;
      }
      if (typeof(b) == 'undefined') {
        $('#dd_'+Type+'_'+Section).css({'color': 'gray'});
        $('#dd_'+a+' div:first-child a').css({'color':'white'});
      }


 
      var ChartType,
          EncounterCode = "<?php echo $Meta['ID'] ?>",
          MPResource = ["Convert", "Aetherflow"],
          noLegend = false,
          ttformat = function() {
             return SongTip + '<span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.x)+'] </span><span style="font-family:Arial;color:'+this.series.color+';">' + this.series.name + ': </span><span style="font-family:Arial;color: white;">' + this.y + '</span></span>';
          };
          $('#Menu_'+Type).css({"border-bottom": "0px"}); 
          $('#opt_'+Type).css({"color": "gray"});
          $('#dd_'+Type).css({"display":"none"});
          if (a != 'Replay' && Type == 'Replay') {
            $('#container').css({'min-height':'325px'});
            $('.hpbar1').css({'display':'none'});
          }
 
          if (typeof(c) != 'undefined') {
            
            if (["All", "Friendly", "Enemy"].indexOf(c) > -1) {

              $('#dd_'+a+'_Filter_'+c).css({'color': 'white'});
              $('#dd_'+a+'_Filter_'+Filter).css({'color': 'gray'});
              $('#left_'+Filter).css({'background-color': '#1a1e20'});
            }
            else {
              //Filter by CombatantID
                $('#dd_'+Type+'_Filter_All').css({'color': 'gray'});
                $('#dd_'+Type+'_Filter_Friendly').css({'color': 'gray'});
                $('#dd_'+Type+'_Filter_Enemy').css({'color': 'gray'});
              
                $('#left_'+Filter).css({'background-color': '#1a1e20'});
                $('#left_'+c).css({'background-color': '#323232'});
            }
               Filter = c;
          }
        if (["All", "Friendly", "Enemy"].indexOf(Filter) > -1) {
          $('#dd_'+a+'_Filter_'+Filter).css({'color': 'white'});
        }
          if (Type != a) {
                $('#dd_'+Type+'_Filter_All').css({'color': 'gray'});
                $('#dd_'+Type+'_Filter_Friendly').css({'color': 'gray'});
                $('#dd_'+Type+'_Filter_Enemy').css({'color': 'gray'});
          }


          if (typeof(a) != 'undefined' && typeof(b) != 'undefined') {
            $('#dd_'+Type+'_'+Section).css({'color': 'gray'});
            $('#dd_'+a+'_'+b).css({'color': 'white'});
          }

 
          switch(a) {

            case 'Summary':
                Type = "Summary";
                chart = new Highcharts.Chart(SummaryPage);
                $('#contentbottom').html("");
                $('#summaryCache').css({"display":"block"});
              break;
/////////////////////////
            case 'Damage':
                Type = "Damage";
                ChartType = "area";
                if (typeof(b) == 'undefined') Section = "Combatant";
                else Section = b;
                $('#dd_'+Type).css({"display":"block"});
                if (Section == "Ability") {
                 ttformat = function() {
                  var critical = '';
                  if (this.point.Crit == 1) critical = ' Critical! ';
                    return (SongTip + '<span style="font-family:Arial;color: #b2bfcd;">'+getCombatantName(this.point.GameID)+'</span>'
                      +'<span style="font-family:Arial;color:white;"> uses </span>'
                      +'<span style="font-family:Arial;color:'+this.series.color+';">' + this.series.name + '.</span><br />'
                      +'<span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.point.Time)+']'+critical+'</span>'
                      +'<span style="font-family:Arial;color: #b2bfcd;"> '+ getCombatantName(this.point.VictimID) +'</span>'
                      +'<span style="font-family:Arial;color: white;"> takes ' + this.y + ' damage.</span>');
                 };
               }
               else {
               ttformat = function() {
                     return ( SongTip + '<span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.point.x)+']</span>'
                            +'<span style="font-family:Arial;color:'+this.series.color+';"> ' + this.series.name + '</span>'
                            +'<span style="font-family:Arial;color: white;"> => ' + this.y + ' DPS.</span>');
                    }
 
               }
              break;
 /////////////////////////////////
            case 'Healing':
                Type = "Healing";


                if (typeof(b) == 'undefined') Section = "Combatant";
                else Section = b;
               ChartType = "area";
                $('#dd_'+Type).css({"display":"block"});

                if (Section == "Ability") {
                ttformat = function() {
                  var critical = '';
                  var re = 'HP';
                  if (MPResource.indexOf(this.series.name) > -1 ) re = 'MP';
                  if (this.point.Crit == "1") critical = ' Critical! ';
                  if (this.series.name == "Galvanize") 
                  return SongTip + '<span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.point.Time)+'] </span><span style="font-family:Arial;color: #b2bfcd;">'+ getCombatantName(this.point.VictimID) +'</span><span style="font-family:Arial;color: white;"> gains the effect of <span style="font-family:Arial;color:'+this.series.color+';">' + this.series.name + ' (' + this.y + ').</span>';
                  else return SongTip + '<span style="font-family:Arial;color: #b2bfcd;">'+getCombatantName(this.point.GameID)+'</span><span style="font-family:Arial;color:white;"> uses </span><span style="font-family:Arial;color:'+this.series.color+';">' + this.series.name + '.</span><br /><span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.point.Time)+']'+critical+'</span><span style="font-family:Arial;color: #b2bfcd;"> '+ getCombatantName(this.point.VictimID) +'</span><span style="font-family:Arial;color: white;"> recovers ' + this.y + ' '+re+'.</span>';
                 };
               }
               else {
               ttformat = function() {
                     return ( SongTip + '<span style="font-family:Arial;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.point.x)+']</span>'
                            +'<span style="font-family:Arial;color:'+this.series.color+';"> ' + this.series.name + '</span>'
                            +'<span style="font-family:Arial;color: white;"> => ' + this.y + ' HPS.</span>');
                    }
               }

              break;

////////////////////////////////////
            case 'Buff':
            ChartType = "area"
              Type = "Buff";
                if (typeof(b) == 'undefined') Section = "Casted";
                else Section = b;
                $('#dd_'+Type).css({"display":"block"});
 
              break;
////////////////////////////////////
            case 'Debuff':
            ChartType = "area"
              Type = "Debuff";
                if (typeof(b) == 'undefined') Section = "Casted";
                else Section = b;
                $('#dd_'+Type).css({"display":"block"});

              break;
//////////////////////////////////
            case 'Event':

              Type = "Event";
              $('#dd_'+Type).css({"display":"block"});
              break;
 
            case 'Replay':
              Type = "Replay";
              if (chart != null) {
                chart.destroy();
                chart = null;
              }
 
              $('#container').css({'min-height':'550px'});
             // if (Timeline == null) {
               $.ajax({
                  dataType: "json",
                  url: '/data?e='+EncounterCode+'&type=Replay',
                  cache: true,
                  success: function(l) {  
 
                        $.ajax({
                        dataType: "json",
                        url: 'http://api.xivdps.com/'+EncounterCode,
                        cache: true,
                        success: function(data) {  
                          $('.hpbar1').css({'display':'block'}); 
                          Timeline = new Replay(data, l); 
                        },      
                        });
 
                  },      
                  });


              //}
            //  else {
              //   $('.hpbar1').css({'display':'block'}); 
              //   Timeline.tl.progress(0).pause();
           //   }
  
              break;
 
            default:
              return;
          }

 
          $('#Menu_'+Type).css({"border-bottom": "2px #6da0c7 solid"});
          $('#opt_'+Type).css({"color": "white"}); 


          if (Type != 'Summary') $('#summaryCache').css({"display":"none"});
          if (typeof(ChartType) != 'undefined' && ChartType != '' && Type != 'Replay') {
              if (noLegend) newSpacing = [25, 0, 15, 0];
              else newSpacing = [25, 0, 0, 0];
 
            var options = {
                  chart: {
                    renderTo: 'container',
                    defaultSeriesType: ChartType,
                    spacing: newSpacing,
                    height: 325 
                  },
                  xAxis: {
                    max: <?php echo $Meta['Duration']; ?>,


       plotBands: [<?php foreach($plotSong as $song) {

                  echo "{color: {
                    linearGradient: [0, 0, 0, 250],
                    stops: [
                        [0, ".$song['color']."],
                        [1, 'rgba(0, 0, 0, .0)']
                    ]
                  }, from: ".$song['from'].", to: ".$song['to'].",
                events: {
                    mouseover: function (e) {
                      SongTip = '<span style=\"font-family:Arial;color:#fff;\">['+ Highcharts.dateFormat('%M:%S.%L', ".$song['from'].") + '] ' + getCombatantName(".$song['GameID'].") + ' uses ".$song['Name']." </span><br/> <br/> <br/>';
                    },
                    mouseout: function (e) {
                       SongTip = '';
                    }
                  },
                  label: {
                    align: 'left',
                    useHTML: true,
            
        
                    x: 5,
                    text: '<span style=\"font-family:Arial;font-size:10px;color:rgba(255,255,255, 0.2)\">&#9835; ".$song['Name']."</span>'
                  }

                  },";
       }  ?>],

                  },
                  tooltip: {
                    snap: 5,
                    formatter: ttformat
                  },
                  series: [{}]
              };



             $.ajax({
                dataType: "json",
                url: '/data?e='+EncounterCode+'&type='+Type+'&section='+Section+'&filter='+Filter,
                cache: true,
                success: function(data) {  
               
                  if (data.hasOwnProperty('Type')) options.chart.defaultSeriesType = data['Type'];
                  if (data.hasOwnProperty('Chart')) options.series = data["Chart"];
                  if (data.hasOwnProperty('Category')) {
                    options.yAxis = { };
                    if (data.hasOwnProperty('Height'))  {
                      var nh = data['Category'].length * 30 + 120;
                      if (nh < 325) options.chart['height'] = 325;
                      else options.chart['height'] = nh;
                    }
                    options.yAxis.categories = data['Category'];
                  }
 
                  if (data.hasOwnProperty('Formatter')) {
                    options.yAxis.labels = { align:'right', useHTML: true, formatter: function () {
                    return   '<span onmouseover="xivdb_get(this, \'status\', '+this.value+');"><span class="gtext">' + data['Formatter'][this.value][0] + '</span> <img src="'+data['Formatter'][this.value][1]+'" class="gimg" /></span>';
                   } };
                 }
                  if (data.hasOwnProperty('Table')) $('#contentbottom').html(data["Table"]);
                   
 
                  chart = new Highcharts.Chart(options);
                  $("#TableSort").stupidtable();

                    if (data["cache"] == "") var usedcache = "Off";
                    else usedcache = "On";
                    $('#debuginfo').html('Cache: ' + usedcache + ' / Mem: ' + data["mem"])

                },      
            });

 
          }

        if (a == "Summary") window.location.hash = a;
        else {
          if (typeof(b) == 'undefined' && Section != "") b = Section;
          if (typeof(c) == 'undefined') c = Filter;
          window.location.hash = a + '/' + b + '/' + c;
        }
    }


    //Public methods

    return {
      ViewReport: ViewReport,
      initReport: initReport,
      getCombatantName: getCombatantName,
      toggleSong: toggleSong
    }
  }();


  /* Expose to window */
  window.toggleSong = XIVDPS.toggleSong;
  window.getCombatantName = XIVDPS.getCombatantName;
  window.ViewReport = XIVDPS.ViewReport;


  $(function() {

    XIVDPS.initReport();

  });


</script>
 
<div id="midmenu">
  <div class="boxhead"><span class="icon-users"></span> Combatants</div>

  <div class="boxmenu"> 


<?php
  
 
if (count($Party) > 0) {
     foreach ($Party as $c) {
  
      $jobpic = JobID($c['Job']);
      
      echo ("

         <div onclick=\"ViewReport(Type, Section, ".$c['GameID'].")\" class=\"inneropt\" id=\"left_".$c['GameID']."\">
         <img src=\"img/class/".$jobpic.".png\" class=\"classicon\" />
         <div class=\"midname\">".$c['Name']."</div>
          <div class=\"hpbar1\"><div id=\"hp_".$c['GameID']."\" class=\"hpbar\"></div>
          <div class=\"flyingHeal\" id=\"ff_heal_".$c['GameID']."\"></div>
         </div>
        </div>
        ");
      $buffContent = "<div style=\"display:none;\" id=\"tt_".$c['GameID']."\">
                      <div class=\"tooltiph\">
                      <div class=\"tin\"><B>HP:</B>&nbsp;&nbsp;&nbsp;".$c['HP']."</div><div class=\"tin\"><B>MP:</B>&nbsp;&nbsp;&nbsp;".$c['MP']."</div>
                      </div>";
      if ($c['StartBuff'] != "") {
       $buff = explode(":", $c['StartBuff']);
        $buffContent = $buffContent . "<div style=\"margin-top: 6px;margin-left:4px;\">";
        foreach ($buff as $buffID) {
          $buffContent = $buffContent . "<img  class=\"tableicon\" src=\"".iconize($buffID)."\" />";
        }
       $buffContent = $buffContent . "</div> ";
     }



  
       if (isset($Pet[$c['GameID']])) {
        
        $buffContent = $buffContent . "<hr style=\"margin-top:5px;margin-bottom:5px;\"/> ";

        foreach($Pet[$c['GameID']] as $p) {
             $jobpic = JobID($p['Job']);
          
                $buffContent = $buffContent . "

                   <div onclick=\"ViewReport(Type, Section, ".$p['GameID'].")\" class=\"tooltiph mob\" style=\"min-height:20px;\" id=\"left_".$p['GameID']."\">
                   <img src=\"img/class/".$jobpic.".png\" class=\"classicon\" />
                   <div class=\"midname\">".$p['Name']."</div>
                    <div class=\"hpbar1\"><div id=\"hp_".$p['GameID']."\" class=\"hpbar\"></div>
                    <div class=\"flyingHeal\" id=\"ff_heal_".$p['GameID']."\"></div>
                   </div>
                  </div>
                  ";
 
        }
 
       }



     echo ( $buffContent .
        "</div>
        <script type=\"text/javascript\">
        $('#left_".$c['GameID']."').data('powertiptarget', 'tt_".$c['GameID']."');
        $('#left_".$c['GameID']."').powerTip(tooltip_opt);
        </script>
      ");

 
   }
}
?>

 <hr />
<?php
if (count($Enemy) > 0) {
     foreach ($Enemy as $cName => $cEntityList) {
      $fName = str_replace(' ', '-', $cName);
      $specialIcon = "";
       if ($cName == $BossName) {  
          $specialIcon = " <img src=\"img/icon/boss.png\" class=\"classicon\" />";
       }

      echo ("  <div class=\"inneropt\" id=\"left_".$fName."\">
               ".$specialIcon."<div class=\"midname\">".$cName."</div>
               </div> ");



      $entityContent = "<div style=\"display:none;\" id=\"tt_".$fName."\">";

        foreach ($cEntityList as $cEntity) {
          if (isset($SpawnTime[$cEntity['GameID']])) {
            $inTime = durationFormat($SpawnTime[$cEntity['GameID']]);
          }
          else {
            $inTime = "0:00";
          }
          $entityContent = $entityContent . "<div onclick=\"ViewReport(Type, Section, ".$cEntity['GameID'].");\" id=\"left_".$cEntity['GameID']."\" class=\"tooltiph mob\">".$cName."<small class=\"stime\">".$inTime."</small></div>";
        }

        echo ($entityContent . '
        </div>
        <script type="text/javascript">
        $("#left_'.$fName.'").data("powertiptarget", "tt_'.$fName.'");
        $("#left_'.$fName.'").powerTip(tooltip_opt);
        </script>
        ');
     }
}
 ?>
 
 

</div>
</div>
<div id="mright">
 



  <div id="rightcontent">


 

<div class="chartmenu">

    <div class="chartopt" id="Menu_Summary">
     <a onclick="ViewReport('Summary');" id="opt_Summary">Summary</a>
   </div><div class="chartdd"></div>


   <div class="chartopt" id="Menu_Damage">
     <a onclick="ViewReport('Damage');" id="opt_Damage">Damage</a>
   </div><div class="chartdd">&nbsp;<span class="icon-arrow-down"></span></div>


   <div class="chartopt" id="Menu_Healing">
     <a onclick="ViewReport('Healing');" id="opt_Healing">Healing</a>
   </div><div class="chartdd">&nbsp;<span class="icon-arrow-down"></span></div>


   <div class="chartopt" id="Menu_Buff">
     <a onclick="ViewReport('Buff');" id="opt_Buff">Buffs</a>
   </div><div class="chartdd">&nbsp;<span class="icon-arrow-down"></span></div>


   <div class="chartopt" id="Menu_Debuff">
     <a onclick="ViewReport('Debuff');" id="opt_Debuff">Debuffs</a>
   </div><div class="chartdd">&nbsp;<span class="icon-arrow-down"></span></div>

 <!--
   <div class="chartopt" id="Menu_Event">
    <a onclick="ViewReport('Event');" id="opt_Event">Other</a>
   </div><div class="chartdd">&nbsp;<span class="icon-arrow-down"></span></div>
-->

   <div class="chartopt optr" id="Menu_Replay">
     <a onclick="ViewReport('Replay');" id="opt_Replay"><span class="icon-camera"></span> Replay</a>
   </div>
   <div class="chartopt optr" id="Menu_Options" >
     <a onclick=""><span class="icon-cog2"></span> Options</a>
   </div>
 

        <div id="optionsBox" style="display:none;">

              <div class="tooltiph mob"><label>Toggle Bard Songs <div style="float:right;"><input type="checkbox" onclick="toggleSong();" /></div></label></div>
           
         
        </div>


         <script type="text/javascript">
        $('#Menu_Options').data('powertiptarget', 'optionsBox');
        $('#Menu_Options').powerTip(dropdown_opt);
        </script>
 
</div>
 
 
 
<div class="Dropdown_menu" id="Dropmenu">

  <div class="Dropdown_cat" id="dd_Damage">
      <div class="dd_opt"><a onclick="ViewReport('Damage', 'Combatant');" id="dd_Damage_Combatant">Done by Combatant</a></div>
      <div class="dd_opt"><a onclick="ViewReport('Damage', 'Ability');" id="dd_Damage_Ability">Done by Ability</a></div>
 
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'All');" id="dd_Damage_Filter_All">All</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Enemy');" id="dd_Damage_Filter_Enemy">Enemy</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Friendly');" id="dd_Damage_Filter_Friendly">Friendly</a></div>
       
 
  </div>

  <div class="Dropdown_cat" id="dd_Healing">

      <div class="dd_opt"><a onclick="ViewReport('Healing', 'Combatant');" id="dd_Healing_Combatant">Done by Combatant</a></div>
      <div class="dd_opt"><a onclick="ViewReport('Healing', 'Ability');" id="dd_Healing_Ability">Done by Ability</a></div>
 
     <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'All');" id="dd_Healing_Filter_All">All</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Enemy');" id="dd_Healing_Filter_Enemy">Enemy</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Friendly');" id="dd_Healing_Filter_Friendly">Friendly</a></div>
  </div>
 

  <div class="Dropdown_cat" id="dd_Buff">

      <div class="dd_opt"><a onclick="ViewReport('Buff', 'Casted');" id="dd_Buff_Casted">Casted</a></div>
      <div class="dd_opt"><a onclick="ViewReport('Buff', 'Received');" id="dd_Buff_Received">Received</a></div>
 
     <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'All');" id="dd_Buff_Filter_All">All</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Enemy');" id="dd_Buff_Filter_Enemy">Enemy</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Friendly');" id="dd_Buff_Filter_Friendly">Friendly</a></div>
  </div>


  <div class="Dropdown_cat" id="dd_Debuff">

      <div class="dd_opt"><a onclick="ViewReport('Debuff', 'Casted');" id="dd_Debuff_Casted">Casted</a></div>
      <div class="dd_opt"><a onclick="ViewReport('Debuff', 'Received');" id="dd_Debuff_Received">Received</a></div>
 
     <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'All');" id="dd_Debuff_Filter_All">All</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Enemy');" id="dd_Debuff_Filter_Enemy">Enemy</a></div>
      <div class="dd_opt r"><a onclick="ViewReport(Type, Section, 'Friendly');" id="dd_Debuff_Filter_Friendly">Friendly</a></div>
  </div>




 <!--
    <div class="Dropdown_cat" id="dd_Event">
      <div class="dd_opt">Death</div>
      <div class="dd_opt">Raise</div>
      <div class="dd_opt">Dispel</div>
      <div class="dd_opt">Enmity</div>
  </div>

-->
 

 
 
</div>


 
<div style="background-color:rgba(0,0,0,0.2);margin-top:1px;">
  <div id="container" style="width:900px;   margin: 0 auto"></div>
</div>


<div id="contentbottom">

              <!--

   
               <div class="sumbox">
                  <div class="suminner">
   
                    <big style=" color:#42b059">1</big>(<span class="icon-arrow-up"></span>3)  
                      <hr />
                    <span style="font-size: 16px; "> <span class="icon-numbered-list"></span> Ranking</span>
                  </div>

               </div>  -->
  </div>

  <div id="summaryCache" style="display:none;">
            

               <div class="sumbox sumbig">
                <div class="suminner">
                 <div style="height: 44px;">
                   <big><?php  echo durationFormat($Meta['Duration']); ?></big>
                   <hr />
                   <span style="font-size: 16px; "> <span class="icon-clock"></span> Duration</span>
                 </div>
               </div>
             </div>

               <div class="sumbox sumbig" id="DamageBox">
              <div class="suminner">
               <div style="height: 44px;">
                 <big><?php echo floor(($Meta["TotalFriendlyDamage"]+$Meta["LimitBreakDamage"]) / ($Meta['Duration']/1000)) ?></big>
                 <hr />
                 <span style="font-size: 16px; "> <span class="icon-fire"></span> Total DPS</span>
               </div>
             </div>
             </div>

          <?php
            echo "
              <!-- Additional DPS Info -->
                  <script type=\"text/javascript\">
                    $('#DamageBox').data('powertip', function() {
                      return '<div class=\"tooltiph mob\">Group DPS <div class=\"stime\">".$Meta["TotalFriendlyDamage"]." (".floor($Meta["TotalFriendlyDamage"]/($Meta['Duration']/1000)).")</div></div><div class=\"tooltiph mob\">Limit Break <div class=\"stime\">".$Meta["LimitBreakDamage"]." (".floor($Meta["LimitBreakDamage"]/($Meta['Duration']/1000)).")</div>';
                    });
                    $('#DamageBox').powerTip(box_opt);
                    $('#DamageBox').css({'cursor': 'help'});
                    </script>


              <!-- end -->
            ";
            $returnDeath = "";
 
                          $deathCount = 0;
                          foreach ($Party as $key1 => $value1) {
                            if (isset($DeathTime[$value1['GameID']])) {
                              foreach ($DeathTime[$value1['GameID']] as $key => $value) {
                                 $deathCount++;
                                $returnDeath = $returnDeath . "deathCombatant += '<div class=\"tooltiph mob\"><span class=\"icon-skull\"></span> ' + getCombatantName(".$value1['GameID'].") + '<br /><small style=\"color: #b2bfcd\">by ' + getCombatantName(".$value['Killer'].") + ' <div class=\"stime\"><span class=\"icon-clock\"></span>&nbsp;".durationFormat($value['Time'])."</div></small></div>';";
                              }
                            }
                          }
                        ?>
 
            <div class="sumbox" id="DeathBox">
                <div class="suminner">
                  <div style="height: 44px;">
                   <big><?php echo $deathCount; ?></big>
                   <hr />
                   <span style="font-size: 16px; "> <span class="icon-skull"></span> Death</span>
                 </div>
               </div>
             </div>
                  <!-- Additional Death Info -->
                    <script type="text/javascript">
                    $('#DeathBox').data('powertip', function() {
                        var deathCombatant = "";
                          <?php echo $returnDeath; ?>
                         return deathCombatant;
                    });
                    $('#DeathBox').powerTip(box_opt);
                    $('#DeathBox').css({'cursor': 'help'});
                    </script>
                  <!-- end -->

           <div class="boxend" style="margin-right:0px;overflow:hidden;">
            <div class="suminner" >
              <div style="height: 44px;" id="_xivdb">
              <?php
                $loot = [];
                foreach($loot as $i)
                {
                  echo " <a href=\"http://xivdb.com/?item/".$i."\" data-replacename=\"0\" data-colorname=\"0\" data-showicon=\"1\" ></a> ";
                }
              ?>
             </div>
             <hr />
             <span style="font-size: 16px; "> <span class="icon-download"></span> Reward</span>
           </div>
         </div>  

  </div>

 

   </div>
 
   <hr />

  <small style="color:gray;float:left;"><span class="icon-info"></span> XIVDPS_Plugin (<?php echo $Meta['Version1']; ?>) / FFXIV_ACT_Plugin (<?php echo $Meta['Version2']; ?>)</small>
  <small style="color:gray;float:right;" id="debuginfo"></small></div>


 </div>
 

 