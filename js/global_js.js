function highlight_menu(page, type){
    $('#sub_nav_'+page+'_'+type).addClass('highlight_li');
    $('#'+page+'_sub_nav').css("display", "block");
}

function trim(str) {
	str = str.replace(/^\s+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}

function sendMsg(request, target){
	$('#cover').css('display', 'block');
	$(target).css('opacity', '0.5');
	$.ajax({
		type: "POST",
		url: "cgi-bin/listener.py",
		data: request,
		success: function(msg){
			$(target).html(msg);
			$('#cover').css('display', 'none');
			$(target).css('opacity', '1');
		}
	});
} 
function open_post( post_id ){
	window.open("http://www.acfun.tv/v/ac"+post_id);
	// $("#wiki_post_"+post_id).slideUp('fast');
	// document.getElementById('wiki_sortable').removeChild(document.getElementById("wiki_post_"+post_id));
}
