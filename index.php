 <?php
session_start();


 
 
if (!isset($_GET['lang'])) {
  if (!isset($_COOKIE['lang'])) {
    setcookie("lang", "en"); //default (en)
    $_LANG = "en";
  }
  else $_LANG = filter_var($_COOKIE['lang'], FILTER_SANITIZE_ENCODED);
}
else {
  setcookie("lang", $_GET['lang']);
  $_LANG = filter_var($_GET['lang'], FILTER_SANITIZE_ENCODED);
}



   switch($_LANG) {
    case "ja": $_LANG="ja"; $_LANG_CODE = 0; break;
    case "jp": $_LANG="ja"; $_LANG_CODE = 0; break;
    case "en": $_LANG_CODE = 1; break;
    case "fr": $_LANG_CODE = 3; break;
    case "de": $_LANG_CODE = 2; break;
    default: $_LANG_CODE = 1; break;
  }

 

 
 
    require_once("db.php");
    require_once("function.php");
    $page = "";
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }
    if($page == "data") {

      require_once("data.php");
      die();
    }


    //require_once("twitter.php");

 
 






 ?>
<html>
<head>
<meta charset="utf-8" /> 
  <title>XIVDPS.com</title>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="media/layout.css" />
  <link rel="stylesheet" href="media/style.css" />
  <link href='http://fonts.googleapis.com/css?family=Roboto:100' rel='stylesheet' type='text/css'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenLite.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/plugins/CSSPlugin.min.js"></script>
  <script src="media/tooltip.js"></script>
  <script src="media/xivdb_tooltip.js"></script>
  <script type="text/javascript">
  var xivdps_global_lang = <?php echo $_LANG_CODE; ?>
  </script>
 
</head>
<body>
 
  <div id="main">
   <div id="mleft">

 
    <a href="/" onmouseover="TweenLite.to('#logo', 0.5, {top: '-155px'});"  onmouseout="TweenLite.to('#logo', 0.5, {top: '-75px'});">
    <div class="logoc">
     <div class="logobg" id="logob"></div>
      
       <div id="logo" />
<img src="/img/rs.png"><br /><br /><br />
<img src="/img/bfb.png"> 
          </div>
       
    </div>
 </a>
 
  <div id="leftnav">
      <div class="opt">
        <a href="/profile"><span class="icon-user micon"></span></a>
      </div>
       <div class="opt">
        <a id="leftmenu_opt"><span class="icon-cog2 micon"></span></a>
 
      </div>
        <div class="opt">
        <a href="/search"><span class="icon-search micon"></span></a>
      </div>
      <div class="opt">
        <a href="/rank"><span class="icon-trophy micon"></span></a>
      </div>
      <div class="opt">
        <a href="/plugin"><span class="icon-download micon"></span></a>
      </div>
    </div>
</div>
 


	<?php
 
			if ($page == "") {
        getPage("Home_.php", $_LANG, " ");
      }
      else if ($page == "profile") {
        if (!isset($_SESSION['userid'])) {
          getPage("Account/Register.php", $_LANG,  "Register");

        }
        else {
          getPage("Home_.php", $_LANG,  "Profile");
        }

      }
      else if ($page == "about") {
        getPage("Home_.php", $_LANG,  "");
      }
      else if ($page == "rank") {
        getPage("Home_.php", $_LANG,  "Leaderboard");
      }
      else if ($page == "plugin") {
        getPage("Home_.php", $_LANG,  "Plugin");
      }
      else if ($page == "search") {
        getPage("Home_.php", $_LANG,  "Search");
      }
      else if ($page == "register") {
        getPage("Account/Register.php", $_LANG,  "Register");
      }
      else if ($page == "test") {
        getPage("test.php", $_LANG,  "Register");
      }


      else {
        getPage("Encounter.php", $_LANG);
      }

	?>
 
</div>
<div></div>
</div>
 
</body>
</html>


<?php
function getPage($file, $_LANG,  $header = null) {
  if ($header != null) {
    $headerTitle = $header;
    require_once("Header.php");
  }
  echo "<div id=\"maincontent\">";
  require_once($file);
}
?>
 