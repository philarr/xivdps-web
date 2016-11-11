<?php

 

          $sql=" SELECT  Floor(Time/1000), SUM(Value) FROM `CombatantDamage` WHERE EncounterID='".$EncounterID."'  AND GameID IN (SELECT GameID FROM Combatant WHERE EncounterID='".$EncounterID."' AND Type='1' ) GROUP BY Floor(Time/1000)";
          $result=$conn->query($sql);
           $damage = array();
           $lastTime = 0;
           $total = 0;
           while($row = $result->fetch_row()){

            if ($row[0] > $lastTime+1 ) {
               for($i = $lastTime+1; $i < $row[0]; $i++) {
                $damage[] = [$i*1000, 0];
              }
            }
              $damage[] = [$row[0]*1000, $row[1]];
            $lastTime = $row[0];
          

            /*DPS Algorithm
            $total += $row[1];
            if ($row[0] == 0) {
              $dps = $total;
            }
            else {
            $dps = $total/$row[0];
            }
            $damage[] = [$row[0]*1000, floor($dps)]; 
            */

           }

 
 
          $sql="SELECT  Floor(Time/1000), SUM(Value) FROM `CombatantHealing` WHERE EncounterID='".$EncounterID."' GROUP BY Floor(Time/1000)";
          $result=$conn->query($sql);
           $healing = array();
           $lastTime = 0;
           while($row = $result->fetch_row()){
            if ($row[0] > $lastTime+1 ) {
               for($i = $lastTime+1; $i < $row[0]; $i++) {
                $healing[] = [$i*1000, 0];
              }
            }
              $healing[] = [$row[0]*1000, $row[1]];
            $lastTime = $row[0];
           }
 
 
         $sql="SELECT Duration, TotalFriendlyDamage, Death FROM `Encounter` WHERE ID='".$EncounterID."' LIMIT 0,1";
              $result=$conn->query($sql);
              $Meta = $result->fetch_assoc();
            $minutes = floor($Meta['Duration'] / 60000);  

            $seconds = floor(($Meta['Duration'] / 1000) % 60);
            if ($seconds < 10) {
              $seconds = "0" . $seconds;
         }

           echo json_encode(array("Table" => generateSummaryTable($Meta["Death"], $minutes . ":" . $seconds, ceil($Meta["TotalFriendlyDamage"] / ($Meta['Duration']/1000)), [1,8532,3,6]),
                      "Chart" => array(
                        array("name"=>"Damage", 
                            "data"=>$damage),
                        array("name"=>"Healing",
                            "data"=>$healing)
                        )
                      ), 
           JSON_NUMERIC_CHECK);

function generateSummaryTable($death, $duration, $dps, $loot) {


$page = "  <div class=\"sumbox\">
                <div class=\"suminner\">
                  <div style=\"height: 44px;\">
                   <big>".$death."</big>
                   <hr />
                   <span style=\"font-size: 16px; \"> <span class=\"icon-skull\"></span> Death</span>
                 </div>
               </div>
             </div>
             <div class=\"sumbox sumbig\">
              <div class=\"suminner\">
               <div style=\"height: 44px;\">
                 <big>".$duration."</big>
                 <hr />
                 <span style=\"font-size: 16px; \"> <span class=\"icon-clock\"></span> Duration</span>
               </div>
             </div>
           </div>
           <div class=\"sumbox sumbig\">
            <div class=\"suminner\">
             <div style=\"height: 44px;\">
               <big>".$dps."</big>
               <hr />
               <span style=\"font-size: 16px; \"> <span class=\"icon-fire\"></span> Group DPS</span>
             </div>
           </div>
         </div>
         <div class=\"sumbox sumbig\" style=\"width:250px;float:right; overflow:hidden;\">
          <div class=\"suminner\" >
            <div style=\"height: 44px;\" id=\"_xivdb\">";


            foreach($loot as $i)
            {
          	  $page = $page . " <a href=\"http://xivdb.com/?item/".$i."\" data-replacename=\"0\" data-colorname=\"0\" data-showicon=\"1\" ></a> ";
            }

           $page = $page . "
           </div>
           <hr />
           <span style=\"font-size: 16px; \"> <span class=\"icon-download\"></span> Loot Awarded</span>
         </div>

       </div>";

       return $page;


}
?>