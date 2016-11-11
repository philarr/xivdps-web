

<?php

        $conn = new mysqli(DBAuth::$server, DBAuth::$user, DBAuth::$pass, DBAuth::$name);

        $sql="SELECT ID, ZoneID, PartyID, BattleID, StartTime, Duration, TotalFriendlyDamage, Death FROM `Encounter` ORDER BY ID DESC";
        $result=$conn->query($sql);
?>

 





<?php
        while($row = $result->fetch_assoc()) {
        $ZoneInfo = ZoneID($row["ZoneID"]);
            echo "<div style=\"padding:25px;background-color:rgba(0,0,0,0.2);\">
                  <a \" href=\"/".$row["PartyID"]."_".$row["BattleID"]."\">".$ZoneInfo[0]." - ".$ZoneInfo[1]."</a>
                  </div>
                  ";
        }
        echo "</div>";


 ?>


 