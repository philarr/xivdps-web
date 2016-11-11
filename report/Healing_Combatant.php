<?php
 
	$filterSQL = "";
	if ($filter == "All") {$filterSQL = "";}
	else if ($filter == "Friendly") {$filterSQL = "AND Type='1'";}	
	else if ($filter == "Enemy") {$filterSQL = "AND Type='2'";}
	else {$filterSQL = "";}
			 $sql = "SELECT ZoneID, Duration, TotalFriendlyDamage FROM `Encounter` WHERE ID ='".$EncounterID."' LIMIT 0,1";
			 $result = $conn->query($sql);
			 $Meta = $result->fetch_assoc();
			 $ZoneNPC = getZoneNPC($_LANG, $Meta['ZoneID']);

		if ($filter == "Friendly" || $filter == "Enemy" || $filter == "All") {
///////////////////////////////////////////////////////////////////////////////////////////////////////////NONBD
			if ($filter == "All") $filterSQL = "";
 
			 $sql = "SELECT GameID, Type, Name, Job, OwnerID, TotalHealing FROM `Combatant` WHERE EncounterID = '".$EncounterID."' ".$filterSQL."";
					 $result=$conn->query($sql);
					 $Combatant = [];
					 $CombatantDPS = [];
					 $totals = 0;
					 $cSQL = [];
					 while($row = $result->fetch_assoc()){
					 	if ($row['Type'] == 2) $row['Name'] = $ZoneNPC[$row['Name']][0];
					 	if ($row['Type'] == 1 && $row['OwnerID'] != 0) $row['Name'] = getName($_LANG, $row['Name']);
					 //	if ($row['Type'] == 1 && $row['OwnerID'] == 0 ) $row['Name'] = JobID2($row['Job']);
					 	$Combatant[$row['GameID']] = $row;
					 	 $CombatantDPS[$row['GameID']] = [9999, 0, 0];
					 	 if ($row['TotalHealing'] > 0) {
					    	 $cSQL[] = $row['GameID'];
					     }
					  }
					 $ids = join(',', $cSQL);
					 $genGraph = array();
			if (count($cSQL) > 0) {
				$sql=" SELECT Time, Value, GameID FROM `CombatantHealing` WHERE EncounterID='".$EncounterID."' AND GameID IN (".$ids.") AND SkillID NOT IN (".$limitBreakID.")";
					$result=$conn->query($sql);
					 $damage = array();
					 $total = array();
					 while($row = $result->fetch_assoc()) {
						$row['Time'] = (ceil($row['Time']/1000))*1000;
 
					 	if (!isset($total[$row['GameID']])) $total[$row['GameID']] = [];
			 			if (!isset($total[$row['GameID']][$row['Time']])) $total[$row['GameID']][$row['Time']] = 0;
					 	$total[$row['GameID']][$row['Time']] += $row['Value'];
					 }
					 $dps = [];
					 $lastTime = [];
					 foreach ($total as $cID => $timeList) {
					 	$damage[$cID][] = [0, 0];
					 	foreach ($timeList as $time => $amount) {
					 		if (!isset($dps[$cID])) $dps[$cID] = 0;
					 		if (!isset($lastTime[$cID])) $lastTime[$cID] = 0;
					 		if ($time == 0) $d = 1;
					 		else $d = ceil($time/1000);
					 		$dps[$cID] += $amount;
					 		$lastTime[$cID] = $time;
					 		$thisdps = ($dps[$cID] / $d);
					 		if ($time > 30000) if ($CombatantDPS[$cID][0] > $thisdps) $CombatantDPS[$cID][0] = $thisdps;
						    if ($CombatantDPS[$cID][1] < $thisdps) $CombatantDPS[$cID][1] = $thisdps;
					 		$damage[$cID][] = [$time, floor($thisdps)];
					 	}
					 		$enddps = $dps[$cID] / ($Meta['Duration']/1000);
					 		$damage[$cID][] = [$Meta['Duration'],  $enddps];
					 		$CombatantDPS[$cID][2] = $enddps;
					 	if ($CombatantDPS[$cID][0] > $enddps) $CombatantDPS[$cID][0] = $enddps;

					 }
					 foreach ($damage as $key => $value) {
					 	$genGraph[] = ["type" => "line", "name" =>$Combatant[$key]['Name'], "data" => $value];
					 }
					 foreach ($dps as $key => $value) {
					 	$totals += $value;
					 }
				}
							 $resultJSON = json_encode(array("Table" =>generateCombatantTable($Combatant, $CombatantDPS, $totals, $Meta),
							 						"Chart" => $genGraph), 
							 JSON_NUMERIC_CHECK);
			}
			else {
////////////////////////////////////////////////////////////////BD/////////////////////////////////////////////////////////////
 
	 			    $sql = "SELECT GameID, Name, Type, OwnerID FROM `Combatant` WHERE EncounterID = '".$EncounterID."' ";
					$result=$conn->query($sql);
				    $CombatantTaken = [];
				    $genGraph = [];
					while($row = $result->fetch_assoc()){
						if ($row['Type'] == 2) $row['Name'] = $ZoneNPC[$row['Name']][0];
					    if ($row['Type'] == 1 && $row['OwnerID'] != 0) $row['Name'] = getName($_LANG, $row['Name']);
						$CombatantTaken[$row['GameID']] = [
							"Name" => $row['Name'],
							"Type" => $row['Type'],
						];
 
				 	}
				if (count($CombatantTaken) > 0) {
				$sql=" SELECT Time, Value, VictimID FROM `CombatantHealing` WHERE EncounterID='".$EncounterID."' AND GameID IN (".$filter.") AND SkillID NOT IN (".$limitBreakID.")";
					$result=$conn->query($sql);
					 $damage = array();
					 $total = array();
					 $idx = [];
					 $finalList = [];

					 while($row = $result->fetch_assoc()) {
					 	if ($row['Value'] == 0) continue;
						$row['Time'] = (ceil($row['Time']/1000))*1000;
					 	if (!isset($total[$row['VictimID']])) $total[$row['VictimID']] = [];
			 			if (!isset($total[$row['VictimID']][$row['Time']])) $total[$row['VictimID']][$row['Time']] = 0;
					 	$total[$row['VictimID']][$row['Time']] += $row['Value'];
					 }
 
					 foreach ($total as $key => $value) {
					 	$finalList[$key] = [
					 	"Name" => $CombatantTaken[$key]['Name'],
					 	"Type" => $CombatantTaken[$key]['Type'],
					 	"Amount" => 0,
					 	"Spawn" => 0,
					 	"Death" => $Meta['Duration'],
					 	"startPoint" => false
					 	];
					 	$idx[] = $key;
					 }

					 if (count($idx) > 0) {


						 $idx = join(',', $idx);

					  	$sql = "SELECT GameID, VictimID, Time, Type FROM `CombatantEvent` WHERE EncounterID='".$EncounterID."' AND ((GameID IN (".$idx.") AND Type ='1') OR (VictimID IN (".$idx.") AND Type ='4'))";
					    $result=$conn->query($sql);

					      while($row = $result->fetch_assoc())
					      {
					      	if ($row['Type'] == 1 ) {

					      		if ($finalList[$row['GameID']]['Type'] == 2) $finalList[$row['GameID']]['Spawn'] = $row['Time'];
					      	}
					      	else {
					      		if ($finalList[$row['VictimID']]['Type'] == 2) $finalList[$row['VictimID']]['Death'] = $row['Time'];
					      	}
					      }

		 				foreach ($total as $cID => $timeList) {
						 	foreach ($timeList as $time => $amount) {	
						 		if ($time == 0) $d = 1;
						 		else $d = ceil(($time-$finalList[$cID]['Spawn'])/1000) ;

								if (!$finalList[$cID]['startPoint']) {
									$damage[$cID][] = [$time, 0];
									$finalList[$cID]['startPoint'] = true;
									}

						 		$finalList[$cID]['Amount'] += $amount;
						 		$thisdps = ($finalList[$cID]['Amount'] / $d);
						 		$damage[$cID][] = [$time, floor($thisdps)];

						 	}

						 	if ($finalList[$cID]['Death'] == 0) $endTime = $Meta['Duration'];
						 	else $endTime = $finalList[$cID]['Death'];
						 		$enddps = $finalList[$cID]['Amount'] / (($endTime-$finalList[$cID]['Spawn'])/1000);
						 		$damage[$cID][] = [$endTime,  round($enddps, 2)];
						 		if ($endTime != $Meta['Duration']) $damage[$cID][] = [$endTime,  0];
						 }


						 foreach ($damage as $key => $value) {
						 	$genGraph[] = ["type" => "line", "name" => $CombatantTaken[$key]['Name'], "data" => $value];
						 }

					 }
 				 }
 				 	 
  
							 $resultJSON = json_encode(array("Type" => "line", "Table" => generateCombatantDone($finalList, $Meta),
							 						"Chart" => $genGraph), 
							 JSON_NUMERIC_CHECK);

			}

 
function generateCombatantDone($Combatant, $Meta) {

 
		$page = "
		<table id=\"TableSort\"  class=\"MainTable\">
		<thead>
		<tr class=\"MainHead\">
			<th data-sort=\"string\" class=\"MainFirst\" style=\"width: 24%\"><span class=\"icon-user\"></span> Healing Breakdown</td>
			<th>&nbsp;</td>
			<th data-sort=\"int\" style=\"width: 19%\">Active</td>
			<th data-sort=\"int\" style=\"width: 12%\">Amount</td>
			<th data-sort=\"int\"  class=\"MainLast\" style=\"width: 15%\">HPS <small>(EncHPS)</small></td>
		 
		</tr>
		</thead>
		<tbody>
		";
$num = 0;
$totalCount = "";
if (count($Combatant) > 0) {
	$count1 = 0;
	$count2 = 0;
		foreach($Combatant as $c) {
			$edps = $c['Amount']/($Meta['Duration']/1000);
			$active = $c['Death']-$c['Spawn'];
			if ($active == 0) {
				$adps = $edps;
				$active = $Meta['Duration']-$c['Spawn'];
			}
			else $adps = $c['Amount']/($active/1000);
		 
	 
				if ($c['Death'] == 0) $c['Death'] = $Meta['Duration']; 
				$activeTime = durationFormat($c['Spawn'])."&mdash;".durationFormat($c['Death'])." (".durationFormatLetter($active).")";
		 


		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			<td><div class=\"sname\">".$c['Name']."</div></td>		 
			<td>-</div></td>
			<td>".$activeTime."</td>
			<td>".$c['Amount']."</td>
			<td>".round($adps,2)." <small>(".round($edps, 2).")</small></td>
			</tr>";

				$count1 += $c['Amount'];
				$count2 += $edps;

		if ($num == 0) $num = 1;
		else $num = 0;
		}	


	$totalCount = "<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			 	<td><span style=\"font-style:italic;\">Total</span></td>
				<td></td>
				<td></td>
				<td>".$count1."</td>
				<td>(".round($count2, 2).")</td>
			</tr>";
 
}
 
else {

		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			  <td colspan=\"6\"><center>No available data.</center></td>
			</tr>";
}

		$page = $page . "</tbody><tfoot>
		".$totalCount."
			</tfoot></table>";
		return preg_replace("@[\\r|\\n|\\t]+@", "", $page);
}


function generateCombatantTable($Combatant, $DPS, $total, $Meta) {
$cc = $Combatant;
if ($total == 0) $total = 1;

		$page = "
		<table id=\"TableSort\"  class=\"MainTable\">
		<thead>
		<tr class=\"MainHead\">
			<th data-sort=\"string\" class=\"MainFirst\" style=\"width: 24%\"><span class=\"icon-user\"></span> Ranking</td>
			<th>&nbsp;</td>
			<th data-sort=\"int\" style=\"width: 16%\">Amount</td>
			<th data-sort=\"int\" style=\"width: 9%\">Low*</td>
			<th data-sort=\"int\" style=\"width: 9%\">High</td>
			<th data-sort=\"int\"  class=\"MainLast\" style=\"width: 9%\">HPS</td>
		 
		</tr>
		</thead>
		<tbody>
		";
$num = 0;
$totalCount = "";
if (count($Combatant) > 0) {
		 usort($Combatant, "PartyHeal");
   $count1 = 0;
   $count2 = 0;
  foreach($Combatant as $c) {

 	if ($DPS[$c['GameID']][0] == 9999) $DPS[$c['GameID']][0] = 0;
 	if ($c['OwnerID'] != 0) $owner = "<br /><small style=\"color:gray;\">(".$cc[$c['OwnerID']]['Name'].")</small>";
 	else $owner = "";
 	if (array_values($Combatant)[0]['TotalHealing'] == 0) $dnum = 1;
	else $dnum = array_values($Combatant)[0]['TotalHealing'];
	if ($c['Job'] != 0) {
		$jobpic = JobID($c['Job']);
		$jobicon =   "<img src=\"img/class/".$jobpic."_.png\" class=\"classicon\" />";
	}
	else {
		$jobicon = "";
	}

		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			<td>".$jobicon."<div class=\"sname\">".$c['Name']." ".$owner."</div></td>
			<td><div class=\"lengthbar\" style=\"width: ".(($c['TotalHealing']/$dnum)*80)."% \"></div></div></td>
			<td>".$c['TotalHealing']." (".round(($c['TotalHealing']/$total)*100, 2)."%)</td>
			<td>".round($DPS[$c['GameID']][0], 2)."</td>
			<td>".round($DPS[$c['GameID']][1], 2)."</td>
			<td>".round($DPS[$c['GameID']][2], 2)."</td>
			</tr>";
			$count1 += $c['TotalHealing'];
			$count2 += $DPS[$c['GameID']][2];
		if ($num == 0) $num = 1;
		else $num = 0;
  }	

	$totalCount = "<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			 	<td><span style=\"font-style:italic;\">Total</span></td>
				<td></td>
				<td>".$count1."</td>
				<td>-</td>
				<td>-</td>
				<td>".round($count2, 2)."</td>
			</tr>";
 
}
 
else {

		$page = $page . "
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			  <td colspan=\"6\"><center>No available data.</center></td>
			</tr>";
}

		$page = $page . "</tbody><tfoot>
		".$totalCount."
			<tr style=\"background-color:rgba(0,0,0,0.".$num.");\">
			  <td colspan=\"6\" style=\"border-top: 1px #2c333c solid;\"><small  style=\"color:gray;\" data-sort-ignore>*Lowest HPS after 30 seconds has elasped since start of encounter.</small></td>
			</tr></tfoot></table>";
		return preg_replace("@[\\r|\\n|\\t]+@", "", $page);
}

?>