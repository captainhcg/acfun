<div id="load_up_div" class="load_div">
    <div style="margin-right: 10px; padding: 12px 20px; border: 1px solid #ccc; box-shadow:inset 0 0 20px #ccc;">
        <table style="margin: auto; float: right; height: 100%;">
            <tr class="wiki_load_remove">
                <td colspan="2" class="dark_orange bold" style="font-size: 16px; text-align: center;">编写UP主的wiki</td>
            </tr>
            <tr class="wiki_load_remove">
                <td colspan="2" style="font-size: 12px; text-align: center; padding: 8px 0">
                   输入UP主的昵称搜索<br>或者从下拉条中选择名人堂的成员<br>昵称必须准确 
                </td>
            </tr>
            <tr>
                <td width="180px" style="display: block">
                    <input placeholder="--选择一个基佬--"  type="text" name="up_name" id="up_name"
                    	style="position: absolute; width:140px; padding: 2px; border-radius: 20px 0 0 20px; z-index: 9999; height: 18px; padding-left:10px" class="wiki_text" 
                        onchange="document.getElementById('select_up_name').selectedIndex = -1">
                    <select name="select_up_name" id="select_up_name"
                    	style="position: absolute; width:160px; height: 24px; margin-left: 10px; position: relative; border: none; font-size:14px; font-family:'Microsoft Yahei',Arial,sans-serif" 
                        onchange="document.getElementById('up_name').value = document.getElementById('select_up_name').options[document.getElementById('select_up_name').selectedIndex].text">
                        <option>pachincko</option>
                        <option>宇宙鱼</option>
                        <option>天花板上吊着猫</option>
                        <option>H娘</option>
                        <option>babyf0x</option>
                        <option>雪月华</option>
                        <option>面档老板</option>
                        <option>小路</option>
                        <option>无聊的夜</option>
                        <option>芙兰的枕头</option> 
                    </select>
                </td>
                <td>
                    <input type="button" class="button" value="编写"
                    	style="border-radius: 0 20px 20px 0; height: 24px; width: 70px; padding-right: 5px"
                    	onclick="load_up(document.getElementById('up_name').value)" />
                </td>
            </tr>
        </table>       
    </div>
</div>
<div id="load_post_div" class="load_div">
    <div style="margin-left: 0px; padding: 12px; border: 1px solid #ccc; box-shadow:inset 0 0 20px #ccc;">
        <table style="margin: auto; float: left; height: 100%">
            <tr class="wiki_load_remove">
                <td colspan="2" class="dark_orange bold" style="font-size: 16px; text-align: center;">编写投稿的wiki</td>
            </tr>
            <tr class="wiki_load_remove">
                <td colspan="2" style="font-size: 12px; text-align: center; padding: 8px 0">
                    左边输入UP主的昵称，右边输入投稿的名称<br>UP主最多只能填一个，可以留空，否则必须准确<br>投稿名称可以填关键词，用空格分开
                </td>
            </tr>
            <tr>
                <td style="padding-right: 4px" height="22px">
                    <input type="text" name="author_name" id="author_name" value="" 
                    	style="width:110px; padding: 2px; border-radius: 20px 0 0 20px; height: 18px; padding-left:10px" class="wiki_text" placeholder="--ID--">
                    <input placeholder="--关键词--" type="text" name="post_keywords" id="post_keywords" value=""
                    	style="width:100px; padding: 2px  4px; height: 18px;" class="wiki_text">
                <td>
                    <input type="button" class="button" value="编写"
                    	style="border-radius: 0 20px 20px 0; width: 60px; height: 24px; "
                        onclick="load_post(document.getElementById('author_name').value, document.getElementById('post_keywords').value)"/>
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="wiki_main">
	<div id='section_load_post'></div>
    <div style='display:none' id="section_process_post">
        <div style="margin: 20px auto 10px; text-align:center; width:550px" class="div_paper">
            <span class="dark_orange bold" style="font-size: 16px">说明</span><br>
            <span style="font-size:12px;">系统默认的四项为title（标题）、link（链接）、author（up主）、date（发布日期）<br>各项之间用“|”分隔，可以相互交换顺序，可以删除，也可加入自定义项<br>如“link||title|备注”可以生成一个包含三列的表，第一项为链接，第二项为标题，第三项为备注<br>因为“备注”是自定义项，所以该项的内容需要手动填入（也可不填）</span><br>            
        </div>
        <div style="margin:auto">    
            <table style="width:650px">
                <tr>
                    <td style="padding-right:6px;" align="right" width="425px">
                        <input type='text' class='wiki_text' id='wiki_table_format' value='title|link|author|date'>
                    </td>
                    <td align="left">
                        <input class="button" id="wiki_table_generate" type="button" value="生成wiki代码" onclick="generate_wiki()">
                    </td>
                </tr>
                <tr>
                	<td colspan="2">
                    	<textarea style="width: 100%; height: 400px; font-size:12px; margin-top: 10px;"  id="wiki_output"></textarea>
                    </td>
                </tr>
            </table>
        </div>
        <!-- <div class="wiki_nav_buttons" style="margin-top:10px">
        	<input class="button wiki_button_previous" type="button" value="上一步">
        	<input class="button wiki_button_next" type="button" value="下一步">
        </div>    -->
    </div>
</div>
