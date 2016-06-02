/**
 * Highstock JS v11.2.0 (2023-10-30)
 *
 * Advanced Highcharts Stock tools
 *
 * (c) 2010-2021 Highsoft AS
 * Author: Torstein Honsi
 *
 * License: www.highcharts.com/license
 */!function(s){"object"==typeof module&&module.exports?(s.default=s,module.exports=s):"function"==typeof define&&define.amd?define("highcharts/modules/price-indicator",["highcharts","highcharts/modules/stock"],function(i){return s(i),s.Highcharts=i,s}):s("undefined"!=typeof Highcharts?Highcharts:void 0)}(function(s){"use strict";var i=s?s._modules:{};function t(s,i,t,e){s.hasOwnProperty(i)||(s[i]=e.apply(null,t),"function"==typeof CustomEvent&&window.dispatchEvent(new CustomEvent("HighchartsModuleLoaded",{detail:{path:i,module:s[i]}})))}t(i,"Extensions/PriceIndication.js",[i["Core/Utilities.js"]],function(s){let{addEvent:i,isArray:t,merge:e,pushUnique:o}=s,r=[];function a(){let s=this.options,i=s.lastVisiblePrice,o=s.lastPrice;if((i||o)&&"highcharts-navigator-series"!==s.id){let r;let a=this.xAxis,c=this.yAxis,l=c.crosshair,h=c.cross,n=c.crossLabel,d=this.points,u=this.yData.length,p=d.length,b=this.xData[this.xData.length-1],P=this.yData[u-1];if(o&&o.enabled&&(c.crosshair=c.options.crosshair=s.lastPrice,!this.chart.styledMode&&c.crosshair&&c.options.crosshair&&s.lastPrice&&(c.crosshair.color=c.options.crosshair.color=s.lastPrice.color||this.color),c.cross=this.lastPrice,r=t(P)?P[3]:P,this.lastPriceLabel&&this.lastPriceLabel.destroy(),delete c.crossLabel,c.drawCrosshair(null,{x:b,y:r,plotX:a.toPixels(b,!0),plotY:c.toPixels(r,!0)}),this.yAxis.cross&&(this.lastPrice=this.yAxis.cross,this.lastPrice.addClass("highcharts-color-"+this.colorIndex),this.lastPrice.y=r),this.lastPriceLabel=c.crossLabel),i&&i.enabled&&p>0){c.crosshair=c.options.crosshair=e({color:"transparent"},s.lastVisiblePrice),c.cross=this.lastVisiblePrice;let i=d[p-1].isInside?d[p-1]:d[p-2];this.lastVisiblePriceLabel&&this.lastVisiblePriceLabel.destroy(),delete c.crossLabel,c.drawCrosshair(null,i),c.cross&&(this.lastVisiblePrice=c.cross,i&&"number"==typeof i.y&&(this.lastVisiblePrice.y=i.y)),this.lastVisiblePriceLabel=c.crossLabel}c.crosshair=c.options.crosshair=l,c.cross=h,c.crossLabel=n}}return{compose:function(s){o(r,s)&&i(s,"afterRender",a)}}}),t(i,"masters/modules/price-indicator.src.js",[i["Core/Globals.js"],i["Extensions/PriceIndication.js"]],function(s,i){i.compose(s.Series)})});//# sourceMappingURL=price-indicator.js.map