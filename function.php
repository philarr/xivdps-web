<?php

function getData($lang, $type, $id) {
 
  switch($type) {
  	case 1: $n = "s"; //aa
  	break;
  	case 2: $n = "s"; //ability
  	break;
  	case 10: $n = "s"; //healing
  	break;
  	case 11: $n = "b"; //hot
  	break;
  	case 15: $n = "s"; //dispel
  	break;
  	case 20: $n = "b"; //dot
  	break;
  	case 21: $n = "b"; //buff
  	break;
  	case 22: $n = "b"; //debuff
  	break;
  	case 30: $n = "s"; //powerdrain
  	break;
  	case 31: $n = "s"; //powerhealing
  	break;
  	case 40: $n = "s"; //tpdrain
  	break;
  	case 41: $n = "s"; //tpheal
  	break;
  	case 50: $n = "s"; //threat
  	break;
  	default: $n = "s";
  	break;
  }

  $getInfo = apc_fetch($lang.":".$n.":".$id);

  	return $getInfo;

}


function getZoneNPC($lang, $id) {
$f = apc_fetch($lang.':z:'.$id);
if ($f) return $f;
else return "?";

}

function getName($lang, $id) {
$f = apc_fetch($lang.':n:'.$id);
if ($f) return $f;
else return "?";
}


function cmp_by_optionNumber($a, $b) {
  return $a[3] - $b[3];
}
function PartyDmg($a, $b) {
  return $a['TotalDamage'] < $b['TotalDamage'];
}
function PartyHeal($a, $b) {
  return $a['TotalHealing'] < $b['TotalHealing'];
}
function iconize($iconID) {
$i = str_pad($iconID, 6, '0', STR_PAD_LEFT);
return "/img/icon/".$i[0].$i[1].$i[2]."000/".$i.".png";
}

function sortDPS( $array )
{
 
usort($array, "PartyDmg");
 
  return $array;
}
function getSongName($id) {
	if ($id == 135) return "Mage\'s Ballad"; //mageballad
	if ($id == 137) return "Army\'s Paeon"; //paeon
	if ($id == 139) return "Foe\'s Requiem"; //foe
}


function getSongColor($id) {
	if ($id == 135) return "'rgba(190, 155, 224, 0.2)'"; //mageballad
	if ($id == 137) return "'rgba(207, 115, 10, 0.2)'"; //paeon
	if ($id == 139) return "'rgba(76, 69, 222, 0.2)'"; //foe
}

function durationFormat($milli) {

            $minutes = floor($milli / 60000);  
            $seconds = floor(($milli / 1000) % 60);
            if ($seconds < 10) {
              $seconds = "0" . $seconds;
            }
 
            return $minutes . ":" . $seconds;

}
function durationFormatLetter($milli) {

            $minutes = floor($milli / 60000);  
            $seconds = floor(($milli / 1000) % 60);
            if ($seconds < 10) {
              $seconds = "0" . $seconds;
            }

            if ($minutes > 0) {
            return $minutes."m " . $seconds."s";
            }
            else {
            	  return $seconds."s";
            }

}
 
function formatDPS($dmg, $duration) {
	if ($dmg <= 0 || $duration <= 0) {
		return 0;
	}
	return floor($dmg / ($duration/1000));
}


function ZoneID($i) {
	switch ($i) {
		case 140:
			return array("Striking Dummy", "Central Thanalan", "none");
			break;
		case 340:
			return array("Striking Dummy", "Lavender Beds", "none");
			break;
		case 193:
			return array("Imdugud", "Final Coil of Bahamut - Turn 1", "t10");
			break;
		case 194:
			return array("Kaliya", "Final Coil of Bahamut - Turn 2", "t11");
			break;
		case 195:
			return array("Phoenix", "Final Coil of Bahamut - Turn 3", "t12");
			break;
		case 196:
			return array("Bahamut Prime", "Final Coil of Bahamut - Turn 4", "t13");
			break;
		default:
			return array("Unknown", "Unknown", "none");
			break;
	}

}


function ServerID($i) {

 
	
}

function humanTiming ($time)
{

    $time = time() - strtotime($time); // to get the time since that moment

    $tokens = array (
        31536000 => 'y',
        2592000 => 'm',
        604800 => 'w',
        86400 => 'd',
        3600 => 'h',
        60 => 'm',
        1 => 's'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.''.$text.(($numberOfUnits>1)?'':'');
    }

}

  


function getTwitter() {

	$getcache = apc_fetch('twitter');

	if (!$getcache || (time() - $getcache[0]) > (60*5)) {

		require_once("twitteroauth/twitteroauth/twitteroauth.php"); //Path to twitteroauth library you downloaded in step 3
		$twitteruser = "xivdps"; //user name you want to reference
		$notweets = 1; //how many tweets you want to retrieve
		$consumerkey = "....................."; //Noted keys from step 2
		$consumersecret = "....................."; //Noted keys from step 2
		$accesstoken = "....................."; //Noted keys from step 2
		$accesstokensecret = "....................."; //Noted keys from step 2
		function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
		  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
		  return $connection;
		}
		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
		$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitteruser."&count=".$notweets);
  
		$getcache = [time(), $tweets[0]->text, $tweets[0]->id, $tweets[0]->created_at];
 
		apc_store('twitter', $getcache);
		 
 	}

 	return '<a target="_blank" href="https://twitter.com/XIVDPS/status/'.$getcache[2].'"> '. $getcache[1] . ' (' . humanTiming($getcache[3]) . ' ago)</a>';
 
}






function JobID($i) {
	switch ($i) {
		case 1:
			return "gld";
			break;
		case 2:
			return "pgl";
			break;
		case 3:
			return "mrd";
			break;
		case 4:
			return "lnc";
			break;
		case 5:
			return "arc";
			break;
		case 6:
			return "cnj";
			break;
		case 7:
			return "thm";
			break;
		case 8:
			return "cpt";
			break;
		case 9:
			return "bsm";
			break;
		case 10:
			return "arm";
			break;
		case 11:
			return "gsm";
			break;
		case 12:
			return "ltw";
			break;
		case 13:
			return "wvr";
			break;
		case 14:
			return "alc";
			break;			
		case 15:
			return "cul";
			break;
		case 16:
			return "min";
			break;
		case 17:
			return "btn";
			break;
		case 18:
			return "fsh";
			break;
		case 19:
			return "pld";
			break;
		case 20:
			return "mnk";
			break;
		case 21:
			return "war";
			break;
		case 22:
			return "drg";
			break;
		case 23:
			return "brd";
			break;
		case 24:
			return "whm";
			break;
		case 25:
			return "blm";
			break;
		case 26:
			return "acn";
			break;
		case 27:
			return "smn";
			break;
		case 28:
			return "sch";
			break;
		case 29:
			return "rog";
			break;
		case 30:
			return "nin";
			break;
		case 99:
			return "pet";
			break;
		case 100:
			return "boss";
			break;			
	}
}









function JobID2($i) {
	switch ($i) {
		case 1:
			return "Gladiator";
			break;
		case 2:
			return "Pugilist";
			break;
		case 3:
			return "Marauder";
			break;
		case 4:
			return "Lancer";
			break;
		case 5:
			return "Archer";
			break;
		case 6:
			return "Conjurer";
			break;
		case 7:
			return "Thaumaturge";
			break;
		case 8:
			return "Carpenter";
			break;
		case 9:
			return "Blacksmith";
			break;
		case 10:
			return "Armorer";
			break;
		case 11:
			return "Goldsmith";
			break;
		case 12:
			return "Leatherworker";
			break;
		case 13:
			return "Weaver";
			break;
		case 14:
			return "Alchemist";
			break;			
		case 15:
			return "Cul";
			break;
		case 16:
			return "min";
			break;
		case 17:
			return "btn";
			break;
		case 18:
			return "fsh";
			break;
		case 19:
			return "Paladin";
			break;
		case 20:
			return "Monk";
			break;
		case 21:
			return "Warrior";
			break;
		case 22:
			return "Dragoon";
			break;
		case 23:
			return "Bard";
			break;
		case 24:
			return "White Mage";
			break;
		case 25:
			return "Black Mage";
			break;
		case 26:
			return "Arcanist";
			break;
		case 27:
			return "Summoner";
			break;
		case 28:
			return "Scholar";
			break;
		case 29:
			return "Rogue";
			break;
		case 30:
			return "Ninja";
			break;
		case 99:
			return "Pet";
			break;
		case 100:
			return "Boss";
			break;			
	}
}





?>