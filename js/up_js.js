// JavaScript Document
function load_tag(tag, date){
    if(!tag.length){
        alert("tag不能为空");
        return;
    }
    sendMsg("type=tag&tag="+encodeURIComponent(tag)+"&date="+date, document.getElementById("ac_tag_cloud_body"));
}
var lastkeyword = "";
function search(str, strlen){
    str = $.trim(str);
    if(str.length>=strlen && str!= lastkeyword){
        sendMsg("type=search&keywords="+encodeURIComponent(str), document.getElementById("sidebar_search_result"));
        lastkeyword = str;
        // alert(str);
    }
}
