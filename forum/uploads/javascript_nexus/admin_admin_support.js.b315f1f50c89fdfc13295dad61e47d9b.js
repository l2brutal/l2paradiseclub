;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.emailsetup',{initialize:function(){var self=this;var scope=$(this.scope);scope.find('[data-role="toggleView"]').click(function(e){e.preventDefault();var height=scope.height();scope.html('').css('min-height',height).addClass('ipsLoading');ips.getAjax()($(e.currentTarget).attr('href')).done(function(response){scope.removeClass('ipsLoading').css('min-height',0).html(response);self.initialize();}).fail(function(){window.location=$(e.currentTarget).attr('href');});});},});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.filterCheckboxSet',{initialize:function(){this.on('click','[data-action="checkAll"]',this.checkAll);this.on('click','[data-action="checkNone"]',this.checkNone);},checkAll:function(e){e.preventDefault();this.scope.find('input[type="checkbox"]').prop('checked',true).closest('.ipsSideMenu_item').addClass('ipsSideMenu_itemActive');},checkNone:function(e){e.preventDefault();this.scope.find('input[type="checkbox"]').prop('checked',false).closest('.ipsSideMenu_item').removeClass('ipsSideMenu_itemActive');}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.list',{_ajaxObj:null,_editAjaxObj:null,_storedCustomForm:'',initialize:function(){this.on('click','[data-action="reloadTable"]',this.reloadTableClick);this.on('menuItemSelected',this.itemSelected);this.on('click','[data-action="quickToggleCount"]',this.streamCountClicked);this.on('change','[data-action="quickToggle"]',this.toggleStream);this.on('click','[data-action="editStream"]',this.editStream);this.on('click','tr.cNexusSupportTable_row',this.rowClick);},itemSelected:function(e,data){if($(data.menuElem).attr('id')==='elSortMenu_menu'||$(data.menuElem).attr('id')==='elOrderMenu_menu'){this._reloadTable(data.menuElem.find('[data-ipsMenuValue="'+data.selectedItemID+'"] a').attr('href'));}},reloadTableClick:function(e){e.preventDefault();this._reloadTable($(e.target).attr('href'));},streamCountClicked:function(e){$(e.target).next('label').click();},toggleStream:function(e){if($(e.target).attr('value')!='custom'){this.scope.find('[data-role="mainTable"]').show();this._reloadTable($(e.target).attr('data-url'));}else{if(this._storedCustomForm){var form=$(this.scope).find('[data-role="filterForm"]');form.html(this._storedCustomForm);this._storedCustomForm='';$(document).trigger('contentChange',[form]);$('#elRadio_stream_custom').click();}
$(e.target).closest('.ipsTabs').find('.ipsTabs_item').removeClass('ipsTabs_activeItem').end().end().closest('.ipsTabs_item').addClass('ipsTabs_activeItem');this.scope.find('[data-role="mainTable"]').hide();}},editStream:function(e){e.preventDefault();this.scope.find('[data-role="mainTable"]').hide();var url=$(e.target).attr('href');var form=$(this.scope).find('[data-role="filterForm"]');this._storedCustomForm=form.html();form.append('<div class="ipsLoading ipsLoading_small cNexusFormLoading"></div>');if(this._ajaxObj&&_.isFunction(this._ajaxObj.abort)){this._ajaxObj.abort();}
var self=this;this._ajaxObj=ips.getAjax()(url).done(function(response,status,jqxhr){form.html(response);$(document).trigger('contentChange',[form]);$('#elFilterFormFull').show();}).fail(function(){window.location=url;});},_reloadTable:function(url){var scope=$(this.scope);var mainTable=scope.find('[data-role="mainTable"]');var resultsTable=scope.find('[data-role="resultsTable"]');var form=scope.find('[data-role="filterForm"]');resultsTable.addClass('ipsLoading').css({opacity:0.4});if(this._ajaxObj&&_.isFunction(this._ajaxObj.abort)){this._ajaxObj.abort();}
this._ajaxObj=ips.getAjax()(url).done(function(response,status,jqxhr){form.html(response.form);mainTable.html(response.results);resultsTable.removeClass('ipsLoading').css({opacity:1});$(document).trigger('contentChange',[scope]);}).fail(function(){window.location=url;});},rowClick:function(e){var target=$(e.target);if(target.is('a')||target.is('i')||target.is('input')||target.is('textarea')||target.closest('a').length||target.closest('.ipsMenu').length){return;}
if(e.which!==1&&e.which!==2){return;}
if(e.altKey||e.shiftKey){return;}
if(target.is('td')){var checkbox=target.find('input[type="checkbox"]');if(checkbox.length){checkbox.prop('checked',!checkbox.prop('checked')).trigger('change');return;}}
var link=$(e.currentTarget).find('[data-role="supportLink"]');if(e.metaKey||e.ctrlKey||e.which==2){link.attr('target','_blank');link.get(0).click();link.attr('target','');}else{link.get(0).click();}}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.message',{initialize:function(){this.setup();},setup:function(){this.scope.find('a').each(this._obscureLink);},_obscureLink:function(){var elem=$(this);var realUrl=decodeURIComponent(ips.utils.url.getParam('url',elem.attr('href')));elem.attr('target','_blank');if(realUrl!=='undefined'&&realUrl!=elem.text()){var icon=$('<i class="fa fa-external-link ipsCursor_pointer ipsType_medium" title="'+ips.getString('click_to_show_url')+'" data-ipsTooltip></i>');icon.on('click',function(){ips.ui.alert.show({type:'alert',icon:'info',message:_.escape(realUrl),});});elem.after(icon).after(' ');}}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.metamenu',{initialize:function(){var scope=this.scope;this.scope.parent().find('li a').on('click',function(e){e.preventDefault();var target=$(e.currentTarget);if(!target.parent().attr('data-noSet')){scope.find('[data-role="title"]').text(target.find('[data-role="title"]').text());}
if(target.parent().attr('data-group')){var siblings=target.parent().parent().find('li[data-group="'+target.parent().attr('data-group')+'"]');}else{var siblings=target.parent().parent().find('li');}
siblings.removeClass('ipsMenu_itemChecked');target.parent().addClass('ipsMenu_itemChecked');ips.getAjax()(target.attr('href')).done(function(response){console.log(response);var i;for(i in response){if(i=='alert'){ips.ui.alert.show({type:'alert',icon:'warn',message:response[i],});}else if(i=='staff'){var staffMenu=$('[data-role="staffMenu"]');staffMenu.find('[data-role="title"]').text(response[i].name);$('li[data-group="staff"]').removeClass('ipsMenu_itemChecked');$('li[data-group="staff"][data-id="'+response[i].id+'"]').addClass('ipsMenu_itemChecked');}else if(i=='staffBadge'){if(response[i]){$('[data-role="requestAssignedToBadge"]').css('display','inline-block');}else{$('[data-role="requestAssignedToBadge"]').hide();}
$('[data-role="requestAssignedToText"]').text(response[i]);}else if(i=='severityBadge'){if(response[i]){$('[data-role="requestSeverityBadge"]').css('display','inline-block').html(response[i]);}else{$('[data-role="requestSeverityBadge"]').hide();}}else if(i=='statusBadge'){$('[data-role="requestStatusBadge"]').html(response[i]);}else if(i=='stockActions'){$('#elSelect_stock_action').children().remove();var j;for(j in response[i]){$('#elSelect_stock_action').append($('<option>').attr('value',j).text(response[i][j]));}}else if(i=='purchaseWarning'){$('[data-role="purchaseWarning"]').hide();if(response[i]){$('[data-purchaseWarning="'+response[i]+'"]').show();}}else if(i.substr(0,5)=='note_'){$('#elNoteForm_form [name="'+i.substr(5)+'"]').val(response[i]);}else{$('#elSupportReplyForm [name="'+i+'"]').val(response[i]);}}}).fail(function(){ips.ui.alert.show({type:'alert',icon:'info',message:ips.getString('support_ajax_error')});})});this.on('menuOpened',this.startListeningForKeyPress);this.on('menuClosed',this.stopListeningForKeyPress);},startListeningForKeyPress:function(e){$('#'+$(this.scope).attr('id')+'_menu ul').find('.ipsMenu_hover').removeClass('ipsMenu_hover');this._boundKeyPress=_.bind(this.keyPress,this);$(document).on('keydown',this._boundKeyPress);},stopListeningForKeyPress:function(e){$('#'+$(this.scope).attr('id')+'_menu ul').removeClass('ipsMenu_keyNav');$('#'+$(this.scope).attr('id')+'_menu ul').find('.ipsMenu_hover').removeClass('ipsMenu_hover');$(document).off('keydown',this._boundKeyPress);},keyPress:function(e,data){e.preventDefault();var menuList=$('#'+$(this.scope).attr('id')+'_menu ul');menuList.addClass('ipsMenu_keyNav');var active=menuList.find('.ipsMenu_hover');switch(e.which){case 38:if(active.length){active.removeClass('ipsMenu_hover');var prev=active.prev();if(prev.length){prev.addClass('ipsMenu_hover');}else{menuList.children().last().addClass('ipsMenu_hover');}}else{menuList.children().last().addClass('ipsMenu_hover');}
break;case 40:if(active.length){active.removeClass('ipsMenu_hover');var next=active.next();if(next.length){next.addClass('ipsMenu_hover');}else{menuList.children().first().addClass('ipsMenu_hover');}}else{menuList.children().first().addClass('ipsMenu_hover');}
break;case 13:if(active.length){active.find('a').click();}
break;case 27:$(this.scope).click();break;}}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.multimod',{initialize:function(){this.on('submit','[data-role="moderationTools"]',this.moderationSubmit);},moderationSubmit:function(e){var action=this.scope.find('[data-role="moderationAction"]').val();switch(action){case'delete':this._modActionDelete(e);break;case'split':this._modActionDialog(e,'split','wide');break;default:$(document).trigger('moderationSubmitted');break;}},_modActionDelete:function(e){var self=this;var form=this.scope.find('[data-role="moderationTools"]');if(self._bypassDeleteCheck){return;}
e.preventDefault();var count=parseInt(this.scope.find('[data-role="moderation"]:checked').length);ips.ui.alert.show({type:'confirm',icon:'warn',message:(count>1)?ips.pluralize(ips.getString('delete_confirm_many'),count):ips.getString('delete_confirm'),callbacks:{ok:function(){$(document).trigger('moderationSubmitted');self._bypassDeleteCheck=true;self.scope.find('[data-role="moderationTools"]').submit();}}});},_modActionDialog:function(e,title,size){e.preventDefault();var form=this.scope.find('[data-role="moderationTools"]');var moveDialog=ips.ui.dialog.create({url:form.attr('action')+'&'+form.serialize().replace(/%5B/g,'[').replace(/%5D/g,']'),modal:true,title:ips.getString(title),forceReload:true,remoteVerify:false,size:size});$(form).data('_dialog',moveDialog);moveDialog.show();$(document).trigger('moderationSubmitted');}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.packageInfo',{initialize:function(){this.on('click','[data-action="showMoreRows"]',this.showRows);this.on('click','[data-action="showFewerRows"]',this.hideRows);},showRows:function(e){e.preventDefault();this.scope.find('.cNexusSupportHeader_optional').show().end().find('[data-action="showMoreRows"]').hide().end().find('[data-action="showFewerRows"]').show();ips.utils.cookie.set('showAllPackageInfo',true);},hideRows:function(e){e.preventDefault();this.scope.find('.cNexusSupportHeader_optional').hide().end().find('[data-action="showMoreRows"]').show().end().find('[data-action="showFewerRows"]').hide();ips.utils.cookie.unset('showAllPackageInfo');}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.pendingalert',{initialize:function(){ips.ui.alert.show({type:'alert',icon:'warn',message:$(this.scope).text(),});$(this.scope).remove();},});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.replyArea',{initialize:function(){this.on('tabChanged',this.tabChanged);this.on('click','[data-action="showCCForm"]',this.showCCForm);this.on('change','#elSelect_stock_action',this.stockAction);},showCCForm:function(e){e.preventDefault();this.scope.find('[data-role="sendToInfo"]').hide();this.scope.find('[data-role="sendToForm"]').show();this.scope.find('#elInput_cc_wrapper').click();},tabChanged:function(e,data){if(data.tab.attr('data-role')=='noteTab'){this.scope.addClass('cNexusSupportForm_note');}else{this.scope.removeClass('cNexusSupportForm_note');}},stockAction:function(e){var self=this;var val=$(e.currentTarget).val();var action=this.scope.find('#elSupportReplyForm').attr('action');ips.getAjax()(action+'&stockActionData='+val,{showLoading:true}).done(function(response){if(_.isObject(response)){_.each(response,function(value,key){if(key==='message'){CKEDITOR.instances.message.setData(value);}else{self.scope.find('[name="'+key+'"]').val(value);}});self.scope.find('[data-role="primarySubmit"]').focus();}}).fail(function(response){ips.ui.alert.show({type:'alert',icon:'warn',message:ips.getString('support_ajax_error'),});});}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.request',{initialize:function(){this.on(document,'keypress',this.keyPress);this.on('quoteBuilt.quote','.ipsQuote',this.quoteBuilt);this.on(window,'resize',this.resizeInfo);this.setup();},setup:function(){var self=this;if(navigator.userAgent.indexOf('Mac OS X')!=-1){this.scope.find('[data-role="replyForm"] [data-role="primarySubmit"]').attr({'title':ips.getString('cmd_and_enter'),'data-ipsTooltip':''});this.scope.find('[data-role="noteForm"] button[type="submit"]').attr({'title':ips.getString('cmd_and_enter'),'data-ipsTooltip':''});}else{this.scope.find('[data-role="replyForm"] [data-role="primarySubmit"]').attr({'title':ips.getString('ctrl_and_enter'),'data-ipsTooltip':''});this.scope.find('[data-role="noteForm"] button[type="submit"]').attr({'title':ips.getString('ctrl_and_enter'),'data-ipsTooltip':''});}
this.scope.find('.ipsQuote:not([data-commerceHandled])').each(function(){self._handleQuote($(this));});this.resizeInfo();},resizeInfo:function(){if(ips.utils.responsive.currentIs('phone')){this.scope.find('#elNexusRequestInfo').css({height:'auto'});}else{this.scope.find('#elNexusRequestInfo').css({height:$(window).height()+'px'});}},quoteBuilt:function(e){this._handleQuote($(e.currentTarget));},_handleQuote:function(quote){quote.attr('data-commerceHandled',true);var cite=quote.find('.ipsQuote_citation');if(!cite.hasClass('ipsQuote_closed')){var height=quote.height();if(height>500){cite.siblings().hide();cite.removeClass('ipsQuote_open').addClass('ipsQuote_closed');}}},keyPress:function(e){var tag=e.target.tagName.toLowerCase();if(tag!='body'){return;}
switch(e.which){case 114:e.preventDefault();this.scope.find('[data-role="replyTab"]').click();var editor=ips.ui.editor.getObj(this.scope.find('[data-role="replyForm"] [data-ipsEditor]'));editor.unminimize();editor.focus();break;case 110:e.preventDefault();this.scope.find('[data-role="noteTab"]').click();var editor=ips.ui.editor.getObj(this.scope.find('[data-role="noteForm"] [data-ipsEditor]'));editor.unminimize();editor.focus();break;case 115:this.scope.find('[data-role="statusMenu"]').click();break;case 118:this.scope.find('[data-role="severityMenu"]').click();break;case 100:this.scope.find('[data-role="departmentMenu"]').click();break;case 97:this.scope.find('[data-role="staffMenu"]').click();break;case 116:this.scope.find('[data-role="trackMenu"]').click();break;case 112:this.scope.find('[data-role="associatePurchaseMenu"]').click();break;case 107:var next=this.scope.find('[data-role="nextRequestLink"]');if(next.length){window.location=next.attr('href');}
break;case 106:var prev=this.scope.find('[data-role="prevRequestLink"]');if(prev.length){window.location=prev.attr('href');}
break;}}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('nexus.admin.support.splitForm',{initialize:function(){this.on('submit',this.formSubmit);},formSubmit:function(e){e.preventDefault();var form=$(this.scope);if(form.attr('data-bypassValidation')){return false;}
var dialog=ips.ui.dialog.getObj($('[data-role="moderationTools"]'));dialog.setLoading(true);var newWindow=window.open('','_blank');newWindow.blur();window.focus();ips.getAjax()(form.attr('action'),{data:form.serialize(),type:'post'}).done(function(response,status,jqXHR){if(jqXHR.getAllResponseHeaders().indexOf('X-IPS-FormError: true')!==-1||jqXHR.getAllResponseHeaders().indexOf('X-IPS-FormNoSubmit: true')!==-1||jqXHR.getAllResponseHeaders().indexOf('x-ips-formerror: true')!==-1||jqXHR.getAllResponseHeaders().indexOf('x-ips-formnosubmit: true')!==-1){dialog.setLoading(false);dialog.updateContent(response);}else{try{var json=$.parseJSON(jqXHR.responseText);newWindow.location=json.newUrl;window.location=json.oldUrl;}catch(err){newWindow.close();dialog.setLoading(false);dialog.updateContent(response);}}}).fail(function(response){newWindow.close();form.attr('data-bypassValidation',true).submit();});}});}(jQuery,_));;