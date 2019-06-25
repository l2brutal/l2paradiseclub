;(function($,_,undefined){"use strict";ips.controller.register('plugins.manageclubfeatures',{initialize:function()
{var featureList=this.scope.find('ol');var self=this;Debug.log(featureList);ips.loader.get(['core/interface/jquery/jquery-ui.js']).then(function()
{featureList.sortable({dragHandle:'.ipsDrag_dragHandle',update:_.bind(self.update,self)}).disableSelection();});},update:function()
{ips.getAjax()(this.scope.attr('data-clubUpdateOrderUrl'),{method:'POST',data:{order:this.scope.find('ol').sortable('toArray')}}).done(function(){});}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('plugins.tb.BumpUpTopics',{_bumpDays:0,_bumpHours:0,_bumpMinutes:0,_bumpSeconds:0,_updater:null,initialize:function(){this.setupTimer();},setupTimer:function(){var timer=this.scope.find('#elBumpDisabled').attr('data-bumpTime');if(timer>0)
{this._bumpDays=Math.floor((timer /(60*60*24)));this._bumpHours=Math.floor((timer /(60*60))%24);this._bumpMinutes=Math.floor((timer / 60)%60);this._bumpSeconds=Math.floor((timer)%60);this._updater=setInterval(_.bind(this._runTimer,this),1000);}},_runTimer:function(){if(this._bumpSeconds==0)
{if(this._bumpMinutes==0)
{if(this._bumpHours==0)
{if(this._bumpDays>0)
{this._bumpSeconds=59;this._bumpMinutes=59;this._bumpHours=23;this._bumpDays-=1;}}
else
{this._bumpSeconds=59;this._bumpMinutes=59;this._bumpHours-=1;}}
else
{this._bumpSeconds=59;this._bumpMinutes-=1;}}
else
{this._bumpSeconds-=1;}
var timeValue=''+((this._bumpDays>0)?this._bumpDays+':':'');timeValue+=''+((this._bumpDays>0||this._bumpHours>0)?((this._bumpDays&&this._bumpHours<10)?'0':'')+this._bumpHours+':':'');timeValue+=((this._bumpMinutes<10)?'0':'')+this._bumpMinutes+':';timeValue+=((this._bumpSeconds<10)?'0':'')+this._bumpSeconds;this.scope.find('#elBumpDisabled [data-bumpCountdown]').text(timeValue);if(this._bumpSeconds==0&&this._bumpMinutes==0&&this._bumpHours==0&&this._bumpDays==0)
{clearInterval(this._updater);this.scope.find('#elBumpDisabled').hide();this.scope.find('#elBumpEnabled').show();}}});}(jQuery,_));;