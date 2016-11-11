<?php
$warning = "";
 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
      require_once('ReCaptcha/ReCaptcha.php');
      require_once('ReCaptcha/RequestParameters.php');
      require_once('ReCaptcha/Response.php');
      require_once('ReCaptcha/RequestMethod.php');
      require_once('ReCaptcha/RequestMethod/Post.php');
      $secret = "6LdsVQUTAAAAAKahjYeJLlh7LC_s9NSFiTjJ3LHn";
      $gRecaptchaResponse = $_POST['g-recaptcha-response'];

      $recaptcha = new \ReCaptcha\ReCaptcha($secret);
      $resp = $recaptcha->verify($gRecaptchaResponse);

      if(isset($_POST['s_username']) && isset($_POST['s_password']) && $resp->isSuccess()) {

        if( ctype_alnum( $_POST['s_username']) ) {
 
              $user = filter_var($_POST['s_username'], FILTER_SANITIZE_SPECIAL_CHARS);
              $pass = md5($_POST['s_password']);
              $email = "";
              if (isset($_POST['s_email'])) $email = filter_var($_POST['s_email'], FILTER_VALIDATE_EMAIL);
              $conn = new mysqli(DBAuth::$server, DBAuth::$user, DBAuth::$pass, DBAuth::$name);

              // check connection
              if ($conn->connect_error) {
                die('Database connection failed');
              }

              $sql = "SELECT ID FROM `Account` WHERE Username LIKE '".$user."' LIMIT 0,1";
              $result=$conn->query($sql);
              $row_cnt = $result->num_rows;


              if ($row_cnt == 0) {

                $sql = "INSERT INTO `Account` SET Username = '".$user."', Password = '".$pass."', Email = '".$email."', Level = '0'";
                $result=$conn->query($sql);
               // $_SESSION['userid'] = $result->insert_id;

                echo '
                <div id="midmenu" > <br />
                <div style="background-image:url(\'img/sbg.png\');margin:0 auto;margin-top:25px; width:700px;">
                  <div class="boxhead"><span class="icon-user"></span>&nbsp;&nbsp;XIVDPS Account</div>
                  <div class="formbox"><br/>
                      Account created!
                      <br />
                  </div>
               </div>
                </div>
                ';
              }
              else {
                $warning = "<small style=\"color:#aa4040;\">Username already exists.</small> <br/> <br/>";
                require_once("Register_1.php");
              }

            }
            else {

                $warning = "<small style=\"color:#aa4040;\">Invalid username (Only alphabets and numbers allowed)</small> <br/> <br/>";
                require_once("Register_1.php");

            }
 
     }
     else {
        $errors = $resp->getErrorCodes();
        $warning = "<small style=\"color:#aa4040;\">One or more field needs to be completed!</small> <br/> <br/>";
        require_once("Register_1.php");
     }
 
}
else {
  require_once("Register_1.php");
}













 ?>

 

 