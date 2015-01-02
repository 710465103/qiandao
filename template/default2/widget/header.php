<?php
if(!defined('IN_KKFRAME')) exit();
$extra_title = getSetting('extra_title');
$title = '贴吧签到助手';
if($extra_title) $title .= "<span class=\"extra_title mobile_hidden\">—— {$extra_title}</span>";
?>
<h1><?php echo $title; ?>
<object type="application/x-shockwave-flash" data="http://kujiyou.coding.io/dewplayer-vol.swf" width="200" height="20" id="dewplayer" name="dewplayer"> 
<param name="wmode" value="transparent" />
<param name="movie" value="http://kujiyou.coding.io/dewplayer-vol.swf" /> 
<param name="flashvars" value="mp3=http://kujiyou.coding.io/1.mp3&amp;autostart=1
&amp;showtime=1&amp;nopointer=1&amp;volume=50" /> 
</object>
</h1>
<div class="avatar"><?php echo $username; echo $_COOKIE["avatar_{$uid}"] ? '<img id="avatar_img" src="'.$_COOKIE["avatar_{$uid}"].'">' : '<img id="avatar_img" class="hidden" src="hthttp://oneweekfriends.tk/style/member.png">'; ?></div>
<ul class="menu hidden" id="member-menu">
<li id="menu_password"><a href="javascript:;">修改密码</a></li>
<?php
if(getSetting('account_switch')){
	foreach ($users as $_uid => $username){
		echo '<li class="menu_switch_user"><span class="del" href="member.php?action=unbind_user&uid='.$_uid.'&formhash='.$formhash.'">x</span><a href="member.php?action=switch&uid='.$_uid.'&formhash='.$formhash.'">切换至: '.$username.'</a></li>';
	}
	echo '<li id="menu_adduser"><a href="#user-new">关联其他帐号</a></li>';
}
?>
<li id="menu_logout"><a href="member.php?action=logout&hash=<?php echo $formhash; ?>">退出登录</a></li>
</ul>
<div class="menubtn"><p>-</p><p>-</p><p>-</p></div>