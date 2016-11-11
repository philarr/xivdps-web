<?php

$filterSQL = "";
	if ($filter == "All") {$filterSQL = "";}
	else if ($filter == "Friendly") {$filterSQL = "AND GameID IN (SELECT GameID FROM Combatant WHERE EncounterID='".$EncounterID."' AND Type='1' )";}	
	else if ($filter == "Enemy") {$filterSQL = "AND GameID IN (SELECT GameID FROM Combatant WHERE EncounterID='".$EncounterID."' AND Type='2' )";}
	else {$filterSQL = "AND GameID = ".$filter."";}


	$GenGraph = [];
 

		$sql = "SELECT VictimID,SkillID, Time, Fade FROM `CombatantBuffDuration` WHERE EncounterID = '".$EncounterID."' $filterSQL AND Type='1' ";
		$result=$conn->query($sql);
		$BuffDuration = [];
		$BuffIdx = 0;
		$Category = [];
		$Formatter = [];
		while($row = $result->fetch_assoc()){
			if (!isset($BuffIndex[$row['SkillID']])) {
				$BuffIndex[$row['SkillID']] = $BuffIdx;
				$BuffIdx++;
			}
			$BuffDuration[$row['SkillID']][] = [$row['Time'], $BuffIndex[$row['SkillID']]];
			$BuffDuration[$row['SkillID']][] = [$row['Fade'], $BuffIndex[$row['SkillID']]];
			$BuffDuration[$row['SkillID']][] = [null, null];
		}
 
		foreach ($BuffDuration as $key => $value) {
			$getInfo = getData($_LANG, 21, $key);
			$Category[] = $key;
			$Formatter[$key] = [$getInfo[0], iconize($getInfo[1])];
			$GenGraph[] = ["name"=>$getInfo[0], "data"=> $value];
		}
 
 	$resultJSON = json_encode(array("Height"=>true, "Type" => "line", "Table" => "",
					 						"Chart" => $GenGraph, "Category"=> $Category, "Formatter"=>$Formatter), 
					 JSON_NUMERIC_CHECK);



 


?>