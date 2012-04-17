// JavaScript Document
function load_up(str){
	if(!str.length){
		alert("UP主的昵称不能为空");
		return;
	}
	sendMsg("type=author&author="+encodeURIComponent(str), document.getElementById("section_load_post"));
	reset_wiki();
}
function load_post( author, keywords){
	if(!trim(keywords).length){
		alert("关键词不能为空");
		return;
	}	
	sendMsg("type=post&author="+encodeURIComponent(author)+"&keywords="+encodeURIComponent(keywords), document.getElementById("section_load_post"));
	reset_wiki();
}
function reset_wiki(){
	$(".load_div").css("height", "20px");
	$(".wiki_load_remove").css("display", "none");
	$("#section_process_post").css("display", "none");
	$("#section_load_post").html('');
	$("#wiki_output").html('');
	$("#section_load_post").css("display", "table");
	articles = null;	
}
var articles;
function process_posts(){
	posts = $(".wiki_post_li");
	num = posts.length;
	if(num<1){
		alert("没有投稿啊？！");
		return;
	}
	articles = new Array();
	for(i = 0; i < num; i++){
		article = posts[i];
		title = "<nowiki>"+$(article).find('.wiki_post_title').attr('title')+"</nowiki>";
		author =  "<nowiki>"+$(article).find('.wiki_post_author').html()+"</nowiki>";
		date = $(article).find('.wiki_post_date').html();
		number = "http://www.acfun.tv/v/ac"+$(article).attr('number');		
		article = new Array(title, author, date, number);
		articles.push(article);	
	}
	
	if(!articles){
		alert('error!');
		return;
	}
	$("#section_load_post").css("display", "none");
	$("#section_process_post").css("display", "table");	
}
function remove_post( post_id ){
	$("#wiki_post_"+post_id).slideUp('fast', function() {
    	document.getElementById('wiki_sortable').removeChild(document.getElementById("wiki_post_"+post_id))
  	 });
}
function generate_wiki(){
	if(document.getElementById('wiki_table_format').value == ''){
		alert("表项不能为空！");
		return;
	}
	if(!articles){
		alert("error!");
		return;
	}
	columns = document.getElementById('wiki_table_format').value.split('|');
	if(columns.length < 1){
		alert("表项不能为空！");
		return;		
	}
	headline = "!";
	map = new Array();
	num_columns = columns.length; 
	for(i = 0; i < num_columns; i++){
		columns[i] = trim(columns[i]);
		if(columns[i] == ''){
			alert("表项不能为空！");
			return;				
		}
		else{
			if(columns[i].toLowerCase() == 'title'){
				columns[i] = "标题";
				map[i] = 0;
			}
			else if(columns[i].toLowerCase() == 'author'){
				columns[i] = "作者";
				map[i] = 1;
			}
			else if(columns[i].toLowerCase() == 'date'){
				columns[i] = "日期";
				map[i] = 2;
			}
			else if(columns[i].toLowerCase() == 'link'){
				columns[i] = "链接";	
				map[i] = 3;					
			}
			else{
				map[i] = -1;
			}
		}
		if( i != num_columns - 1){
			headline += columns[i] + "!!";
		}
		else{
			headline += columns[i]+'\r\n';
		}
	}
	str = "{| class=\"wikitable\" style=\"font-size: small;\"\r\n";
	str += "|-\r\n";
	str += headline;
	for(j = 0; j<articles.length;j++){
		str += "|-\r\n";
		str += "|";
		for( k = 0; k<num_columns; k++){
			if(map[k]>=0){
				str += articles[j][map[k]];
			}
			if(k != num_columns - 1){
				str += "||";
			}
			else{
				str += "\r\n";
			}
		}
	}
	str += "|}";
	document.getElementById('wiki_output').textContent = str;
}
