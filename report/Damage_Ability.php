<?php
 
	$filterSQL = "";
	if ($filter == "All") {$filterSQL = "";}
	else if ($filter == "Friendly") {$filterSQL = "AND GameID IN (SELECT GameID FROM Combatant WHERE EncounterID='".$EncounterID."' AND Type='1' )";}	
	else if ($filter == "Enemy") {$filterSQL = "AND GameID IN (SELECT GameID FROM Combatant WHERE EncounterID='".$EncounterID."' AND Type='2' )";}
	else {$filterSQL = "AND GameID = ".$filter."";}
		$GenGraph = [];

	$sql="SELECT SwingType, SkillID, COUNT( SkillID ) , SUM( Value ), MIN( Value ) , MAX( Value ) , SUM( Critical ) , SUM( Special ), SUM( Miss )
							FROM `CombatantDamage` 
							WHERE EncounterID = '".$EncounterID."' ".$filterSQL."
							GROUP BY `SkillID` ";
					 $result=$conn->query($sql);
					 $Skill = array();
					 $spellInfo = [];
					 $lastTime = 0;
					 while($row = $result->fetch_row()){
					 	$getInfo = getData($_LANG, $row[0], $row[1]);
					 	$spellInfo[$row[0].":".$row[1]] = [$getInfo[0], $getInfo[1]];
					 	$Skill[] = $row;
					 }

 if ($filter == "Friendly" || $filter == "Enemy" || $filter == "All") {
	$sql="SELECT  Floor(Time/1000), SUM(Value) FROM `CombatantDamage` WHERE EncounterID='".$EncounterID."'  ".$filterSQL." GROUP BY Floor(Time/1000)";
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
           
           }
            $GenGraph[] = ["type"=>"area", "name" => "Damage Done", "data" => $damage];
}
else {
 		if (count($Skill) > 0) {
	//Individual spell cast
					$sql="SELECT SwingType, GameID, VictimID, SkillID, Time, Value, Critical, Miss, Special
							FROM `CombatantDamage` 
							WHERE EncounterID = '".$EncounterID."' AND GameID='".$filter."' ORDER BY ID ASC";
					 $result=$conn->query($sql);
					 $SkillGraph = [];
					 $lastTime = 0;
					 while($row = $result->fetch_assoc()){

					 	$row['Name'] = $spellInfo[$row['SwingType'].":".$row['SkillID']][0];
 
					 	if ($row['Miss'] == 1 ||$row['Special'] == 1 ) continue;
					 	if ($row['SwingType'] == 20) $row['Name'] = $row['Name'] . " (*)";
						$x = ceil($row['Time']/1000)*1000;
					 	$SkillGraph[$row['Name'].":".$row['SkillID']][] = ["Time"=> $row['Time'], "x" => $x, "y" => $row['Value'], "Type" => $row['SwingType'], "GameID" => $row['GameID'], "VictimID" => $row['VictimID'], "Crit" => $row['Critical']];
					 	$lastTime = $x;
					 	$lastValue = $row['Value'];
					 }			 
					 $legendIndex = 99;
					 $spellIndex = 0;
					 foreach ($SkillGraph as $key => $value) {
 					    $n = explode(':', $key);
					 	$addSpell = ["name"=> $n[0], "data"=>$value];
					 	if (strpos($n[0],'(*)') !== false || $n[0] == "Attack" || $n[0] == "Shot") {
					 		$addSpell['index'] = $legendIndex;
					 		$legendIndex++;
					 	}
					 	else {
					 		$addSpell['index'] = $spellIndex;
					 		$spellIndex++;
					 	}
					 	if (in_array($n[1], $limitbreak)) {
					 		$addSpell['visible'] = false;
					 	}

					 	$GenGraph[] = $addSpell;
					 }
			}
}

	 				 $resultJSON = json_encode(array("Type" => "column", "Table" =>generateDamageTable($Skill, $spellInfo),
					 						"Chart" => $GenGraph), 
					 JSON_NUMERIC_CHECK);



function generateDamageTable($Spell, $spellInfo) {

		$max = 1;
		$maxspell = [];
		$lowspell = [];

		usort($Spell, "cmp_by_optionNumber");
		foreach($Spell as $sort) {
			if ($sort[3] > $max) $max = $sort[3];
		}
		$Spell = array_reverse($Spell);
		$page = "
		<table id=\"TableSort\"  class=\"MainTable\">
		<thead>
		<tr class=\"MainHead\">
			<th data-sort=\"string\" class=\"MainFirst\" style=\"width: 24%\"><span class=\"icon-fire\"></span> Skill Name</td>
			<th>&nbsp;</td>
			<th data-sort=\"int\" style=\"width: 8%\">Amount</td>
			<th data-sort=\"int\" style=\"width: 16%\">Hit <small>(Low/High)</small></td>
			<th data-sort=\"int\" style=\"width: 7%\">Miss</td>
			<th data-sort=\"int\" style=\"width: 7%\">Critical</td>
			<th data-sort=\"int\" class=\"MainLast\" style=\"width: 7%\">Interrupt</td>
		</tr>
		</thead>
		<tbody>
		";
$num = 0;
if (count($Spell) > 0) {
		 

		foreach($Spell as $s) {
	    $getInfo = $spellInfo[$s[0].":".$s[1]];
		$s["p"] = floor(($s[3] / $max)*80);
		 
		if ($s[0] == 11 || $s[0] == 20 ) { $type = "status"; $dot = "&#8226;"; }
		else {$type="skill"; $dot = "";}

		if ($s[6] == 0) $s[6] = "-";
		else $s[6] = $s[6] . "x";
		if ($s[7] == 0) $s[7] = "-";
		else $s[7] = $s[7] . "x";
		if ($s[8] == 0) $s[8] = "-";
		else $s[8] = $s[8] . "x";
 
 

		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			<td><span onMouseOver=\"xivdb_get(this, '".$type."', ".$s[1].");\"><img class=\"tableicon\" src=\"".iconize($getInfo[1])."\" /><div class=\"sname\">".$getInfo[0]." ".$dot."</div></span></td>
			<td><div class=\"lengthbar\" style=\"width: ".$s["p"]."% \"></div></td>
			<td>".$s[3]."</td>
			<td>".($s[2] - $s[7])."x  (<small>".$s[4]."</small> / ".$s[5].") </td>
			<td>".$s[8]."</td>
			<td>".$s[6]."</td>
			<td>".$s[7]."</td>
			</tr>";
		if ($num == 0) $num = 1;
		else $num = 0;
		}	
}
else {

		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			  <td colspan=\"7\"><center>No available data.</center></td>
			</tr>";
}



		$page = $page . "</tbody></table>";
		return preg_replace("@[\\r|\\n|\\t]+@", "", $page);
}

?>