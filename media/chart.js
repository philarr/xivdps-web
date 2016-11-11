/**
* Dark theme for Highcharts JS
* @author Torstein Honsi
*/
      var tooltip_opt = {
          placement: 'e',
          smartPlacement: true,
   mouseOnToPopup: true
        };

 
 

     var dropdown_opt = {
          placement: 'sw-alt',
          smartPlacement: true,
          mouseOnToPopup: true,
          fadeInTime: 0,
          fadeOutTime: 0,
          
     };


      var box_opt = {
          placement: 's',
          smartPlacement: true,
      
        };


Highcharts.theme = {
  colors: ["#aaeeee", "#f45b5b", "#90ee7e", "#7798BF", "#777777", "#ff0066", "#7E4DB1", "#55BF3B", "#DF5353", "#7798BF", '#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
  chart: {
      backgroundColor: 'transparent',
      style: {
     fontFamily: "'Arial', sans-serif"
     },
     plotBorderColor: '#606063',
     spacing:[25,0,0,0],
     zoomType: 'x',
     panning: true,
     panKey: 'shift',
                 resetZoomButton: {
                relativeTo: 'chart',

                theme: {
                     
                    fill: '#272727',
                    stroke: '#000',
                    'stroke-width': 2,
                    r: 3,
                          style: {
                                color: 'white'
                      },
                    states: {
                        hover: {
                            fill: '#353535',
                            stroke: '#000',
                            style: {
                                color: 'white'
                            }
                        }
                    }
                }
           
            }
 },



 title:{
  text:''
},
 lang: {
        numericSymbols: null  
    },

subtitle: {
  style: {
   color: '#E0E0E3',
   textTransform: 'uppercase'
 }
},

tooltip: {

    backgroundColor: 'rgba(0,0,0, 0.9)',
    borderColor: '#252a38',
    borderWidth: 3,
    crosshairs: true,


  dateTimeLabelFormats: {
    millisecond:'%M:%S.%L',
    second: '%M:%S',
    minute: '%M:%S',
    hour: '%M:%S',
    day: '%M:%S',
    week: '%M:%S',
    month: '%M:%S',
    year: '%M:%S'
  },

   formatter: function() {
      return '<span style="font-family:Myriad Pro;color: white;">['+Highcharts.dateFormat('%M:%S.%L', this.x)+'] </span><span style="font-family:Myriad Pro;color:'+this.series.color+';">' + this.series.name + ': </span><span style="font-family:Myriad Pro;color: white;">' + this.y + '</span></span>';
   },
},

xAxis: {

  lineWidth: 0,
  minorGridLineWidth: 0,
  lineColor: 'transparent',
  gridLineWidth: 0,
  tickLength: 0,
  min: 0,
 
  maxRange: 60*1000,
  type: 'datetime',
   
          dateTimeLabelFormats: { //force all formats to be minute:second
            millisecond:'%M:%S.%L',
            second: '%M:%S',
            minute: '%M:%S',
            hour: '%M:%S',
            day: '%M:%S',
            week: '%M:%S',
            month: '%M:%S',
            year: '%M:%S'
          }
 },
yAxis: {
          title: {
            text: ' '
          },
          lineWidth: 0,
          gridLineWidth: 1,
          gridLineColor: '#32363d',
          minorGridLineWidth: 0,
         minorTickLength: 0,
         tickLength: 0,
  min: 0,

 },

plotOptions: {

  column: {
 
    grouping: false,
      shadow: false,
 
      stacking: 'normal',
    
      borderColor: '#272727',
      borderWidth: 1,
      stickyTracking: false,
    
 
  },

  scatter: {
    marker: {

        width: 5,
        height: 5,
  
    },
 
  },
areaspline: {
  
      marker: {
    enabled: false,
    symbol: 'circle',
    radius: 0
  }
},
 area: {
 
  lineWidth: 2,
     stickyTracking: false,
  marker: {
    enabled: false,
    symbol: 'circle',
    radius: 0
  }
},
line: {
 
 connectNulls: false,
     stickyTracking: false,
 
   marker: {
    enabled: false,
    symbol: 'circle',
    radius: 0
  }


},            

 
series: {
 dataLabels: {
  color: '#B0B0B3'
},

marker: {
  lineColor: '#333'
}
},
boxplot: {
  fillColor: '#505053'
},
candlestick: {
  lineColor: 'white'
},
errorbar: {
  color: 'white'
}
},
legend: {
 
      backgroundColor: 'rgba(0,0,0,0.1)',
      width: 900,
      padding: 15,
      margin: 15,
      maxHeight: 50,
      symbolWidth: 8,
      symbolHeight: 8,
      itemStyle: {
        fontSize:'9px',
       color: '#E0E0E3',
      },
     itemHoverStyle: {
       color: '#FFF'
     },
     itemHiddenStyle: {
       color: '#606063'
     },

     labelFormatter: function () {         
        return this.name;
      }
},
credits: {
  enabled: false
},
labels: {
  style: {
   color: '#707073'
 }
},

drilldown: {
  activeAxisLabelStyle: {
   color: '#F0F0F3'
 },
 activeDataLabelStyle: {
   color: '#F0F0F3'
 }
},

navigation: {
  buttonOptions: {
   symbolStroke: '#DDDDDD',
   theme: {
    fill: '#505053'
  }
}
},

 // scroll charts
 rangeSelector: {
  buttonTheme: {
   fill: '#505053',
   stroke: '#000000',
   style: {
    color: '#CCC'
  },
  states: {
    hover: {
     fill: '#707073',
     stroke: '#000000',
     style: {
      color: 'white'
    }
  },
  select: {
   fill: '#000003',
   stroke: '#000000',
   style: {
    color: 'white'
  }
}
}
},
inputBoxBorderColor: '#505053',
inputStyle: {
  backgroundColor: '#333',
  color: 'silver'
},
labelStyle: {
  color: 'silver'
}
},

navigator: {
  handles: {
   backgroundColor: '#666',
   borderColor: '#AAA'
 },
 outlineColor: '#CCC',
 maskFill: 'rgba(255,255,255,0.1)',
 series: {
   color: '#7798BF',
   lineColor: '#A6C7ED'
 },
 xAxis: {
   gridLineColor: '#505053'
 }
},

scrollbar: {
  barBackgroundColor: '#808083',
  barBorderColor: '#808083',
  buttonArrowColor: '#CCC',
  buttonBackgroundColor: '#606063',
  buttonBorderColor: '#606063',
  rifleColor: '#FFF',
  trackBackgroundColor: '#404043',
  trackBorderColor: '#404043',
  liveRedraw: false
},

 
 
};

 