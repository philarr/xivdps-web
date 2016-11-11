  <span class="icon-earth"></span>    
  <select id="langselect"  class="headerinput">
    <option value="en">English</option>
    <option value="ja">日本語</option>
    <option value="fr">Français</option>
    <option value="de">Deutsch</option>
  </select>

<script type="text/javascript">

var val = location.href.match(/[?&]lang=(.*?)[$&#]/);
if (typeof(val) == 'undefined' || val == null) val = <?php echo "'".$_LANG."'";  ?>;
else val = val[1];
$('#langselect').val(val); 

function insertParam(key, value)
{
    key = encodeURI(key); value = encodeURI(value);
    var kvp = document.location.search.substr(1).split('&');
    var i=kvp.length; var x; while(i--) 
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
            x[1] = value;
            kvp[i] = x.join('=');
            break;
        }
    }
    if(i<0) {kvp[kvp.length] = [key,value].join('=');}
    document.location.search = kvp.join('&'); 
}
$('#langselect').change(function(evnt){ insertParam('lang', $(this).val()) });
  </script>