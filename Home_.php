 <?php
 



        $conn = new mysqli(DBAuth::$server, DBAuth::$user, DBAuth::$pass, DBAuth::$name);
        $sql="SELECT ZoneID, COUNT(*) AS Num FROM `Encounter` WHERE ZoneID IN (193,194,195,196) GROUP BY ZoneID";
        $result=$conn->query($sql);
        $zone = [
        '193' => 0,
        '194' => 0,
        '195' => 0,
        '196' => 0
        ];
        while($row = $result->fetch_assoc()) {
            $zone[$row['ZoneID']] = $row['Num'];
        }

 ?>


 <div id="midmenu" style="background: #282e3d url('../img/bg.png') ; background-repeat: repeat-x;">
  <div class="boxhead"><img src="img/icon/raid.png" class="classicon raid" />Final Coil of Bahamut</div>
  <div class="boxmenu turnmenu"> 
      <div class="turnbox"><big><?php echo $zone['193']; ?></big><hr />Imdgud</div>
      <div class="turnbox"><big><?php echo $zone['194']; ?></big><hr />Kaliya</div>
      <div class="turnbox"><big><?php echo $zone['195']; ?></big><hr />Phoenix</div>
      <div class="turnbox" style="margin-right: 0px"><big><?php echo $zone['196']; ?></big><hr />Bahamut Prime</div>
  </div>
  <div class="boxhead"><img src="img/icon/raid.png" class="classicon raid" />Second Coil of Bahamut (Savage)</div>
  <div class="boxmenu turnmenu"> 
      <div class="turnbox"><div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Rafflesia</div>
      <div class="turnbox"><div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Melusine</div>
      <div class="turnbox"><div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />The Avatar</div>
      <div class="turnbox" style="margin-right: 0px"><div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Nael Deus Darnus</div>
  </div>
  <div class="boxhead"><img src="img/icon/raid.png" class="classicon raid" />Second Coil of Bahamut</div>
  <div class="boxmenu turnmenu"> 
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Rafflesia
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Melusine
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />The Avatar
      </div>
      <div class="turnbox" style="margin-right: 0px">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Nael Deus Darnus
      </div>
  </div>
  <div class="boxhead"><img src="img/icon/raid.png" class="classicon raid"  />Binding Coil of Bahamut</div>
  <div class="boxmenu turnmenu"> 
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Caduceus
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />ADS
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Elevator
      </div>
      <div class="turnbox" style="margin-right: 0px">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Twintania
      </div>
  </div>
  <div class="boxhead"><img src="img/icon/raid.png" class="classicon raid" />Primal (Extreme)</div>
  <div class="boxmenu turnmenu"> 
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Ifrit
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Garuda
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Titan
      </div>
      <div class="turnbox" style="margin-right: 0px">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Moogle
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Leviathan
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Ramuh
      </div>
      <div class="turnbox">
      <div style="height:41px"><span class="icon-lock" style="font-size:16px;line-height:41px;"></span></div><hr />Shiva
      </div>
  </div>
</div>




<div id="midmenu">
  <div class="boxhead">Recent Upload</div>
 
  <div class="boxmenu"> 
 
  <?php
 

        $sql="SELECT ID, ZoneID, PartyID, BattleID, StartTime, Duration, TotalFriendlyDamage, LimitBreakDamage FROM `Encounter` WHERE ZoneID IN (193,194,195,196)  ORDER BY ID DESC";
        $result=$conn->query($sql);
 
        while($row = $result->fetch_assoc()) {
        $ZoneInfo = ZoneID($row["ZoneID"]);
            echo " <a href=\"/".$row["PartyID"]."_".$row["BattleID"]."\">
                    <div class=\"raidheader\" style=\"background-image: url('/img/raid/".$ZoneInfo[2].".png');\">
                           <div class=\"rtext\" style=\"float:left;\"> ".$ZoneInfo[0]." <br /><small>".$ZoneInfo[1]."</small></div>
                              <div class=\"rtext\" style=\"margin-top: 20px;float:right\">
                              <div style=\"font-size:\">
                                  <span class=\"icon-clock\"></span> ".durationFormatLetter($row['Duration'])." &nbsp;&nbsp;&nbsp;
                                 <span class=\"icon-fire\"></span> ".formatDPS($row['TotalFriendlyDamage']+$row['LimitBreakDamage'], $row['Duration'])."
                              </div>

 

                          </div>
                   </div> 
                  </a>
                  ";
        }
        echo "</div>";


 ?>
 
</div>
 
