<?php
 	$useCache = true;


 
	if (isset($_GET['e']) && isset($_GET['type'])) {
		$mem = memory_get_usage();
		$limitbreak = [200, 201, 202, 203, 204, 205];
 		$limitBreakID = join(',', $limitbreak);
		$resultJSON = "";
		$filter = "All";
		$section = "";
		if (isset($_GET['filter'])) {
			$filter = $_GET['filter'];
		}
		if (isset($_GET['section'])) {
			$section = $_GET['section'];
		}
		$_URLP = "HTML:".$_LANG.":".$_GET['e'].":".$_GET['type'].":".$section.":".$filter;

	   ////CHECK CACHE HERE
			$redis = new Redis();
			$redis->pconnect('127.0.0.1');
			$redis->select(1);

			if ($redis) {
				if (!$useCache) {
					$cache = false;
				}
				else {
					$cache = $redis->get($_URLP);
				}
			if (!$cache) {
 
				/////////NO CACHE//////////////
			   $conn = new mysqli(DBAuth::$server, DBAuth::$user, DBAuth::$pass, DBAuth::$name);

				// check connection
				if ($conn->connect_error) {
				  die('Database connection failed');
				}
				$EncounterID = mysqli_escape_string($conn, $_GET['e']);
 

					if (isset($_GET['filter'])) {
						if ($_GET['filter'] == "All") {
							$filter = "All";
						}
						else if ($_GET['filter'] == "Friendly") {
							$filter = "Friendly";

						}
						else if ($_GET['filter'] == "Enemy") {
							$filter = "Enemy";
						}
						else {
							$filterCombatant = mysqli_escape_string($conn, $_GET['filter']);
							$filter = $filterCombatant;
						}
					}

					if ($_GET['type'] == "Summary") {
		  				 require_once("report/Summary.php");
					}
					else if ($_GET['type'] == "Damage") {
						if ($section == "Ability") {
							 require_once("report/Damage_Ability.php");
						}
						else if ($section == "Combatant") {
							require_once("report/Damage_Combatant.php");
						}
						else {
							die;
						}
					}
					else if ($_GET['type'] == "Healing") {
						if ($section == "Ability") {
							 require_once("report/Healing_Ability.php");
						}
						else if ($section == "Combatant") {
							require_once("report/Healing_Combatant.php");
						}
						else {
							die;
						}
					}
					else if ($_GET['type'] == "Buff") {
						if ($section == "Casted") {
							 require_once("report/Buff_Casted.php");
						}
						else if ($section == "Received") {
							require_once("report/Buff_Received.php");
						}
						else {
							die;
						}
					}

					else if ($_GET['type'] == "Debuff") {
						if ($section == "Casted") {
							 require_once("report/Debuff_Casted.php");
						}
						else if ($section == "Received") {
							require_once("report/Debuff_Received.php");
						}
						else {
							die;
						}
					}
					else if ($_GET['type'] == "Replay")
						require_once("report/Replay.php");
					else {

					}
					if ($useCache) {
						$redis->set($_URLP, gzencode($resultJSON, 1));
						$redis->setTimeout($_URLP, 60*30);
					}
			}
			else {
				$resultJSON = gzdecode($cache);
			}
		}

			$resultJSON = substr($resultJSON, 0, -1);
			$memEnd = (memory_get_usage() - $mem)/1024;
 			$resultJSON = $resultJSON . ", \"cache\": \"".$useCache."\", \"mem\": ".$memEnd." }";
			echo $resultJSON;


}
 
 

 
 
?>