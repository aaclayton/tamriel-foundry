/*! Define Some Constants */
var	siteurl		= 'http://localhost/tamrielfoundry/';
var themeurl	= siteurl + 'wp-content/themes/apocrypha/';
var ajaxurl 	= siteurl + 'wp-admin/admin-ajax.php';
var $			= jQuery;

/*! Back To Top Link Scrolling */
;$(document).ready(function(){$("a.backtotop").click(function(){$("html, body").animate({scrollTop:0},600);return false})});

/*! Collapsing Quotes */
;$(document).ready(function(){$("div.quote").children("div.quote").addClass("subquote");$("div.subquote").children("p.quote-author").append('<button class="quote-toggle button-dark">Expand Quote</button>');$("div.subquote").children().not("p.quote-author,div.subquote").hide();$("button.quote-toggle").click(function(){var a=newtext="";$(this).parent().parent().children().not("p.quote-author,div.subquote").slideToggle(500,"swing");a=$(this).text();newtext=(a=="Expand Quote")?"Collapse Quote":"Expand Quote";$(this).text(newtext)})});

/*! Collapsing Spoilers */
;$(document).ready(function(){$("div.spoiler").children("p.spoiler-title").append('<button class="spoiler-toggle button-dark">Reveal Spoiler</button>');$("div.spoiler").children().not("p.spoiler-title").hide();$("button.spoiler-toggle").click(function(){var a=newtext="";$(this).parent().parent().children().not("p.spoiler-title").slideToggle(500,"swing");a=$(this).text();newtext=(a=="Reveal Spoiler")?"Conceal Spoiler":"Reveal Spoiler";$(this).text(newtext)})});

/*! jQuery Document Ready Functions */
;$(document).ready(function(){

/*! Admin bar AJAX login function */
;$("#top-login-form").submit(function(){$("#login-submit").attr("disabled","disabled");$("#login-submit").html('<i class="icon-unlock-alt"></i> ... ');$.ajax({type:"POST",dataType:"json",data:$(this).serialize(),url:ajaxurl,success:function(a){if(a.success==1){window.location=a.redirect}else{$("#login-submit").removeAttr("disabled");$("#login-submit").html('<i class="icon-lock"></i>Log In');$("#top-login-error").html(a.error);$("#top-login-error").fadeToggle("slow")}}});return false});$("#top-login-logout").click(function(){$(this).html('<i class="icon-lock"></i>Logging Out')});

/*! Buddypress Frontend Notifications */
;$("a.clear-notification").click(function(e){var a=$(this);var d=get_var_in_url(a.attr("href"),"_wpnonce");var c=get_var_in_url(a.attr("href"),"notid");var b=get_var_in_url(a.attr("href"),"type");e.preventDefault();a.removeAttr("href");a.html('<i class="icon-ok"></i>');$.post(ajaxurl,{action:"apoc_clear_notification",_wpnonce:d,notid:c},function(f){if(f){counter=$("li#notifications-"+b+" span.notifications-number");count=parseInt(counter.text());if(count>1){counter.text(count-1);a.parent().remove()}else{counter.remove();a.parent().text("Notifications cleared!")}title=$("title").text();count=title.split("]")[0].substr(1);if(1<count){title.replace(count,count-1)}else{title.replace(/\[.*\]/,"")}document.title=title}})});function get_var_in_url(b,a){var e=b.split("?");var d=e[1].split("&");for(var c=0;c<d.length;c++){var f=d[c].split("=");if(f[0]==a){return f[1]}}return""}function title_notification_count(){count=0;$.each(["activity","messages","groups","friends"],function(b,c){target=$("li#notifications-"+c+" span.notifications-number");if(target.is("*")){count=count+parseInt(target.text())}});if(count>0){var a=$("title").text().replace(/\[.*\]/,"");a="["+count+"]"+a;$("title").text(a)}}title_notification_count();

/*! AJAX Posts Loop */
;$("#posts").on("click","nav.ajaxed a.page-numbers",function(c){var b=newPage=type=id=tooltip=dir="";var a=$(this);c.preventDefault();type=$("nav.pagination").data("type");id=$("nav.pagination").data("id");baseURL=window.location.href.replace(window.location.hash,"");b=parseInt($(".page-numbers.current").text());newPage=parseInt(a.text());if(a.hasClass("next")){newPage=b+1}else{if(a.hasClass("prev")){newPage=b-1}}dir=(newPage>b)?".next":".prev";tooltip=(newPage>b)?"Loading &raquo;":"&laquo; Loading";$("a.page-numbers"+dir).html(tooltip);$.post(ajaxurl,{action:"apoc_load_posts",type:type,id:id,paged:newPage,baseurl:baseURL},function(d){if(d!="0"){$(".post").fadeOut("slow").promise().done(function(){$("nav.pagination").remove();$("#posts").empty().append(d);$("html, body").animate({scrollTop:$("#content").offset().top},600);$("#posts").hide().fadeIn("slow")})}if(1==b){newURL=baseURL+"page/"+newPage+"/"}else{if(1==newPage){newURL=baseURL.replace("/page/"+b,"")}else{newURL=baseURL.replace("/page/"+b,"/page/"+newPage)}}window.history.replaceState({id:id,paged:b},document.title,newURL)})});

/*! Load Comments */
;$("#comments").on("click","nav.ajaxed a.page-numbers",function(c){var b=newPage=postid=tooltip=dir="";var a=$(this);c.preventDefault();postid=$("nav.pagination").data("postid");baseURL=window.location.href.replace(window.location.hash,"");b=parseInt($(".page-numbers.current").text());newPage=parseInt(a.text());if(a.hasClass("next")){newPage=b+1}else{if(a.hasClass("prev")){newPage=b-1}}dir=(newPage>b)?".next":".prev";tooltip=(newPage>b)?"Loading &raquo;":"&laquo; Loading";$("a.page-numbers"+dir).html(tooltip);$.post(ajaxurl,{action:"apoc_load_comments",postid:postid,paged:newPage,baseurl:baseURL},function(d){if(d!="0"){$(".reply").fadeOut("slow").promise().done(function(){$("nav.pagination").remove();$("ol#comment-list").empty().append(d);$("ol#comment-list").after($("nav.pagination"));$("html, body").animate({scrollTop:$("#comments").offset().top},600);$("ol#comment-list").hide().fadeIn("slow");$("#respond").show()});if(1==b){newURL=baseURL+"comment-page-"+newPage}else{if(1==newPage){newURL=baseURL.replace("/comment-page-"+b,"")}else{newURL=baseURL.replace("/comment-page-"+b,"/comment-page-"+newPage)}}window.history.replaceState({type:"comments",id:postid,paged:b},document.title,newURL)}})});

/*! Insert Comments */
;$("form#commentform").submit(function(f){var c="";var e=$(this);var a=e.attr("action");var d=$("#submit",e);var b=$("#comment",e);f.preventDefault();d.attr("disabled","disabled");if($("#comment-notice").length==0){d.parent().prepend('<div id="comment-notice"></div>');$("#comment-notice").hide()}tinyMCE.triggerSave();if(""==b.val()){c="You didn't write anything!"}if(!c){d.html('<i class="icon-pencil"></i>Submitting...');$.ajax({url:a,type:"post",data:e.serialize(),success:function(g){$("#respond").slideUp("slow",function(){$("ol#comment-list").append(g);$("#comments .discussion-header").removeClass("noreplies");$("ol#comment-list li.reply:last-child").hide().slideDown("slow");tinyMCE.activeEditor.setContent("");tinyMCE.triggerSave();d.removeAttr("disabled");d.html('<i class="icon-pencil"></i>Post Comment')})},error:function(g,i,h){c="An error occurred during posting."}})}if(c){$("#comment-notice").addClass("error").text(c).fadeIn("slow");d.removeAttr("disabled");d.removeAttr("disabled");d.html('<i class="icon-pencil"></i>Post Comment')}});

/*! Delete Comments */
;$("ol#comment-list").on("click","a.delete-comment-link",function(a){a.preventDefault();confirmation=confirm("Permanently delete this comment?");if(confirmation){button=$(this);button.text("Deleting...");commentid=$(this).data("id");nonce=$(this).data("nonce");$.post(ajaxurl,{action:"apoc_delete_comment",_wpnonce:nonce,commentid:commentid},function(b){if(b){$("li#comment-"+commentid+" div.reply-body").slideUp("slow",function(){$("li#comment-"+commentid).remove()})}})}});

/*! Submit New bbPress Topic */
;$(".forum form#new-post").submit(function(e){var b="";var d=$(this);var c=$("#bbp_topic_submit",d);var a=$("#bbp_topic_content",d);var f=$("#bbp_topic_title",d);c.attr("disabled","disabled");if($("#topic-notice").length==0){d.prepend('<div id="topic-notice"></div>');$("#topic-notice").hide()}c.html('<i class="icon-pencil"></i>Submitting ...');tinyMCE.triggerSave();if(""==f.val()){b="Your topic must have a title!"}else{if(""==a.val()){b="You didn't write anything!"}}if(b){e.preventDefault();$("#topic-notice").addClass("error").text(b).fadeIn("slow");c.removeAttr("disabled");c.removeAttr("disabled");c.html('<i class="icon-pencil"></i>Post New Topic')}});

/*! Submit New bbPress Reply */
;$(".topic form#new-post").submit(function(f){var c=data="";var e=$(this);var d=$("#bbp_reply_submit",e);var a=$("#bbp_reply_content",e);var b=$("#bbp_topic_id",e).val();f.preventDefault();d.attr("disabled","disabled");if($("#topic-notice").length==0){e.prepend('<div id="topic-notice"></div>');$("#topic-notice").hide()}tinyMCE.triggerSave();if(""==a.val()){c="You didn't write anything!"}if(!c){$("input#bbp_post_action").attr("value","apoc_bbp_reply");data=e.serialize();d.html('<i class="icon-pencil"></i>Submitting ...');$.ajax({url:ajaxurl,type:"post",data:e.serialize(),success:function(g){$("#respond").slideUp("slow",function(){$("ol#topic-"+b).append(g);$("ol#topic-"+b+" li.reply:last-child").hide().slideDown("slow");tinyMCE.activeEditor.setContent("");tinyMCE.triggerSave();d.removeAttr("disabled");d.html('<i class="icon-pencil"></i>Post Reply')})},error:function(g,i,h){c="An error occurred during posting."}})}if(c){$("#topic-notice").addClass("error").text(c).fadeIn("slow");d.removeAttr("disabled");d.removeAttr("disabled");d.html('<i class="icon-pencil"></i>Post Reply')}});

/*! Tab Into TinyMCE From Topic Title */
;$("#bbp_topic_title").bind("keydown.editor-focus",function(b){if(b.which!==9){return}if(!b.ctrlKey&&!b.altKey&&!b.shiftKey){if(typeof(tinymce)!=="undefined"){if(!tinymce.activeEditor.isHidden()){var a=tinymce.activeEditor.editorContainer;$("#"+a+" td.mceToolbar > a").focus()}else{$("textarea.bbp-the-content").focus()}}else{$("textarea.bbp-the-content").focus()}b.preventDefault()}});

/*! Load bbPress Replies */
;$("#forums").on("click","nav.ajaxed a.page-numbers",function(c){var b=newPage=postid=tooltip=dir="";var a=$(this);c.preventDefault();id=$("nav.pagination").data("id");baseURL=window.location.href.replace(window.location.hash,"");b=parseInt($(".page-numbers.current").text());newPage=parseInt(a.text());if(a.hasClass("next")){newPage=b+1}else{if(a.hasClass("prev")){newPage=b-1}}dir=(newPage>b)?".next":".prev";tooltip=(newPage>b)?"Loading &raquo;":"&laquo; Loading";$("a.page-numbers"+dir).html(tooltip);$.post(ajaxurl,{action:"apoc_load_replies",id:id,paged:newPage,baseurl:baseURL},function(d){if(d!="0"){$(".reply").fadeOut("slow").promise().done(function(){$("nav.pagination").remove();$("ol#topic-"+id).empty().append(d);$("ol#topic-"+id).after($("nav.pagination"));$("html, body").animate({scrollTop:$("#forums").offset().top},600);$("ol#topic-"+id).hide().fadeIn("slow");$("#respond").show()});if(1==b){newURL=baseURL+"page/"+newPage+"/"}else{if(1==newPage){newURL=baseURL.replace("/page/"+b,"")}else{newURL=baseURL.replace("page/"+b,"page/"+newPage)}}window.history.pushState({id:id,paged:b},document.title,newURL)}})});

/*! Forum Quotes */
;$("#comments,#forums").on("click","a.quote-link",function(b){var a=quoteParent=quoteSource=posttext=quote="";b.preventDefault();a=$(this).data("context");postid=$(this).data("id");author=$(this).data("author");date=$(this).data("date");if("reply"==a){quoteParent="#post-"+postid;quoteSource="#post-"+postid+" .reply-content";editor="bbp_reply_content"}else{if("comment"==a){quoteParent="#comment-"+postid;quoteSource="#comment-"+postid+" .reply-content";editor="comment"}}if(window.getSelection){posttext=window.getSelection().toString()}else{if(document.selection&&document.selection.type!="Control"){posttext=document.selection.createRange().text}else{return}}if(""!=posttext){postlines=posttext.split(/\r?\n/);firstline=postlines[0];lastline=postlines[postlines.length-1];if(0==$(quoteSource).find(":contains("+firstline+")").length||0==$(quoteSource).find(":contains("+lastline+")").length){alert("This is not a valid quote selection. Either select a specific passage or select nothing to quote the full post.");return}}if(""==posttext){posttext=$(quoteSource).html()}posttext=posttext.replace(/<ul id="bbp-reply-revision((.|\n)*?)(<\/ul>)/,"");posttext=posttext.replace(/<ul id="bbp-topic-revision((.|\n)*?)(<\/ul>)/,"");posttext=posttext.replace(/<div class="spoiler">((.|\n)*?)(<\/div>)/g,"");posttext=posttext.replace(/<img((.|\n)*?)(>)/g,"");posttext=posttext.replace(/<br>/g,"");posttext=posttext.replace(/&nbsp;/g,"");posttext=posttext.replace(/<button class="quote-toggle((.|\n)*?)(<\/button>)/g,"");posttext=posttext.replace(/display: none;/g,"");quote='\r\n\r\n[quote author="'+author+"|"+quoteParent.substring(1)+"|"+date+'"]';quote+="\r\n"+posttext;quote+="\r\n[/quote]\r\n\r\n&nbsp;";editor_html=document.getElementById(editor+"-html");switchEditors.switchto(editor_html);document.getElementById(editor).value+=quote;editor_tmce=document.getElementById(editor+"-tmce");switchEditors.switchto(editor_tmce);$("html, body").animate({scrollTop:$("#respond").offset().top},600)});

/*! Reply Link Scrolling */
;$("#comments,#forums").on("click","a.reply-link",function(a){a.preventDefault();$("html, body").animate({scrollTop:$("#respond").offset().top},600)});


/*! End Document Ready */
;});



/* ______ REFACTORED / NEW BY ZAYDOK BELOW (TEMPORARY COMMENT) _____ */

// DOM ready
jQuery(function() {
	// Assign jQuery back to $ alias
	var $ = jQuery,
	// Define faux constants
			SITE_URL = document.URL,
			AJAX_URL 	= SITE_URL + 'wp-admin/admin-ajax.php',
	// Define elements
			advSearchForm = $( '#advanced-search' ),
			searchFor = advSearchForm.find( '#search-for' ),
			submitBtn = advSearchForm.find( 'input[type=submit]' )
	;

	// Display appropriate form fields based on "Search For" dropdown
	searchFor.bind( 'change', function() {
		var currentValue = searchFor.val(),
				currentFields = advSearchForm.find( '.dynamic-form-section:visible' ),
				inboundFields = $( '#if-' + currentValue )
		;
		// Animate out current fields
		currentFields
			.hide()
			.slideUp( 'slow' );
		// Animate in new fields based on newly selected "Search For" value
		inboundFields
			.show()
			.slideDown( 'slow' );
	});

	// Custom "Advanced Search" submit handling via AJAX
	submitBtn.bind( 'click', function() {
		// Temporarily do nothing until queries are worked out.
		return false;
	});
});