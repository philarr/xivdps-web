<?php

$sql = "SELECT SwingType, SkillID FROM `CombatantDamage` WHERE EncounterID='".$EncounterID."' GROUP BY `SkillID`";
 	$result=$conn->query($sql);
 	$Damage = [];
	while($row = $result->fetch_assoc()){
		$getInfo = getData($_LANG, $row['SwingType'], $row['SkillID']);
		$name = $getInfo[0];
		if ($row['SwingType'] == 20) $row['SkillID'] = $row['SkillID'].'_';
		$icon = $getInfo[1];
		$Damage[$row['SkillID']] = [$name, $icon];
	}
$sql = "SELECT SwingType, SkillID FROM `CombatantHealing` WHERE EncounterID='".$EncounterID."' GROUP BY `SkillID`";
 	$result=$conn->query($sql);
 	$Healing = [];
	while($row = $result->fetch_assoc()){
		$getInfo = getData($_LANG, $row['SwingType'], $row['SkillID']);
		$name = $getInfo[0];
		if ($row['SwingType'] == 11) $row['SkillID'] = $row['SkillID'].'_';
		$icon = $getInfo[1];
		$Healing[$row['SkillID']] = [$name, $icon];
	}
$sql = "SELECT SkillID FROM `CombatantBuff` WHERE EncounterID='".$EncounterID."' GROUP BY `SkillID`";
 	$result=$conn->query($sql);
 	$Buffs = [];
	while($row = $result->fetch_assoc()){
		$getInfo = getData($_LANG, 21, $row['SkillID']);
		$name = $getInfo[0];
		$icon = $getInfo[1];
		$Buffs[$row['SkillID']] = [$name, $icon];
	}

	$resultJSON = json_encode(["Damage"=>$Damage, "Healing"=>$Healing, "Buffs"=>$Buffs], 
					 JSON_NUMERIC_CHECK);
 

?>