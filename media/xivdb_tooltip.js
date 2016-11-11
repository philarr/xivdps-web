
var cacheTooltip = {
  skill: {},
  status: {},
  item: {}
};

var spell_opt = {
  placement: 'ne',
      fadeOutTime: 0,
 
  popupId: 'powerTip2',
  fadeInTime: 0,
};
var xivdb_tooltips_config =
{
  // General
  'version'       : '1.6',
  'domain'        : 'xivdb.com',
  // Set options
  'zindex'        : '99999999',
  // Custom options
  'replaceName'   : true,
  'colorName'     : true,
  'showIcon'      : true,
  'debug'         : false,
  // Accept url domains
  hrefs:
  [
  'xivdb.com',
  'xivdatabase.com',
  'www.xivdb.com',
  'www.xivdatabase.com',
  'jp.xivdb.com',
  'en.xivdb.com',
  'de.xivdb.com',
  'fr.xivdb.com',
  ],
  // List of languages
  language:
  {
    list: ["JP", "EN", "DE", "FR"],
    value: 1,
  },

};

function xivdb_get(element, type, id)
{
  "undefined" != typeof Prototype && jQuery.noConflict();
 //Check cache and if attached event
 var e = $(element);
 e.css({'cursor': 'help'})
 if (cacheTooltip[type].hasOwnProperty(id)) {

  try {
    e.powerTip('show');
  }
  catch(err) {
          //console.log('reattaching event from cache');
          e.data('powertip', function() {
            return cacheTooltip[type][id]['html'];
          });
          e.powerTip(spell_opt);
          e.powerTip('show');
        }
      }

      else {
  //Retrieve from xivdb.com, no cache from this id
  //console.log('no cache, get from xivdb');
  void 0 == id || !jQuery.isNumeric(id) || (jQuery.ajax({
    url: "http://xivdb.com/modules/fpop/fpop.php",
    data: 
    {
      lang: xivdps_global_lang,
      version: xivdb_tooltips_config.version,
      type: type,
      id: id,
    },
    cache: true,
    type: 'GET',
    success: function (data) 
    {
      data = JSON.parse(data);
      if (void 0 != data)
      {
        e.data('powertip', function() {
          return data['html'];
        });
        e.powerTip(spell_opt);
        e.powerTip('show');
        cacheTooltip[type][id] = data;
      }
    },
  }));
}
}
// Oncall event
initXIVDBTooltips=function(){var e=document.createElement("link");e.setAttribute("rel","stylesheet");e.setAttribute("href","http://"+xivdb_tooltips_config.domain+"/css/tooltip.css");e.setAttribute("type","text/css");document.getElementsByTagName("head")[0].appendChild(e);}
document.addEventListener('DOMContentLoaded',function(){ initXIVDBTooltips(); })

