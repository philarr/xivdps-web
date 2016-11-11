<script src='https://www.google.com/recaptcha/api.js'></script>
 <div id="midmenu" >
<br />
 
<div style="background-image:url('img/sbg.png');margin:0 auto;margin-top:25px; width:700px;">


  <div class="boxhead"><span class="icon-user"></span>&nbsp;&nbsp;XIVDPS Account</div>
 
 <form action="/register" method="POST" name="registerForm" id="registerForm" autocomplete="off">
<input style="display:none">
<input type="password" name="aa" style="display:none">
      <div class="formbox">
    
     <div style="padding: 0 15px 0 15px;"><span class="icon-bullhorn"></span>&nbsp;&nbsp; It is recommended that you use a different login credential from FFXIV or any other websites!</div>
   
   <hr />

 

      <div style="float:left;width:190px;margin-right:45px;">
<div style="margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);">1 &rarr; Account Information </div>

<div style="margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);">

2 &rarr; Download Plugin <br/ >
<br />
  <center><a href="http://www.xivdps.com/plugin/XIVDPS_Plugin.dll"><B><U>1.5.0.10 (Link)</U></B></a></center>


</div>




<div style="margin-top:35px;margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);"><B>Requirements</B>

<br/>
<br/>
FFXIV_ACT_Plugin (1.3.0.16+)<br/>
Network parsing mode (on) 
 

 </div>
<div style="margin-top:35px;margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);"><B>How to use</B>

<br/>
<br/>
Logging with begin when you enter combat in:<br/><br/>
Turn 13 <br/>
Turn 12 <br/>
Turn 11 <br/>
Turn 10 <br/>
<br/>
It will be uploaded when boss dies or wipe<br/><br/>
 </div>
<!--
<div style="margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);color:gray;">2 &rarr; Import Character  </div>
<div style="margin-bottom:2px;padding:10px;background-color:rgba(0,0,0,0.2);color:gray;">3 &rarr; Confirmation </div>
-->
      </div>
 
          <table class="tform" style="width:405px;" >
         <?php echo $warning; ?>
                <tr>
                 <td colspan="2"><small style="color:gray">Account is needed to login to plugin and use site features (coming soon)</small></td>
               </tr>
              <tr>
                  <td>Username*</td><td><input type="text" name="s_username"  /> </td>
              </tr>
               <tr>
                   <td>Password*</td><td><input type="password" name="s_password" /></td>
              </tr>
              <tr>
              <td colspan="3"><hr /></td>
               </tr>         
               <tr>
                 <td colspan="2"><small style="color:gray">If you wish to have the option of password recovery.</small></td>
               </tr>
               <tr>
                  <td>E-mail</td><td><input type="text" name="s_email"  /></td>
              </tr>
 
                <td colspan="3"><hr /></td>
               <tr>
                   <td >Verification*</td><td><div class="g-recaptcha" data-theme="dark" data-sitekey="6LdsVQUTAAAAANMC7dqUa6IfBinPn50yw2RZZ4Fm"></div></td>
              </tr> 

          </table>
 
          <div style="margin-top:25px;height:50px;">
            <hr />
    <span style="float:left;"><small style="color:gray;">*Required field</small></span>
    <input type="submit" class="sinput" value="Register" style="margin-left:4px;float:right;" />
    <input type="reset"  class="sinput"  value="Reset" style="float:right;" />
  </div>
    
      </div>
</form>
</div>
  </div>