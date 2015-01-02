<?php
if (! defined ( 'IN_KKFRAME' ))	exit ();
function get_random_content(){
	$text=<<<EOF
ずっと光の中
昨日までは无かった
足迹を辿ってきたほど
ゴールの见えない迷路
雨に打たれた地図
後悔も何もないけど
叫んだ声は
きっと届くから
この物语の始まる
钟の音が繋ぐ旅の途中で
君が描いた未来の世界は
いつかの空に导かれて
どんな梦重ねて伪る
事のないあの光りを
もっと先に见える
希望だけ残した
伤迹が愈える事はない
最低何かーつ
手に入る物があったら
それだけで何もいらない
届けた空が
まだ孤独なら
この物语の终わりを
告げる音闻かせて
そっとゆるやかに
立ち込める风
いつの日か终わる旅の途中で
君が描いた未来の世界は
今もどこかに生き続けて
本当の自分を
受け入れてくれたあの光りを
どこまでも行こう
繋いだその手が
いつか离れそうでも
しつまでも信じてここで待つから
もう少しだけここにいて
君が描いた未来の世界は
いつかの空に导かれて
二つの想いを一つずつ形にして
奇迹をもっと越えて
君が描いた未来の世界は
今もどこかで生き続けて
どんな梦重ねて伪る
事のないあの光りを届けて
EOF;
	$contents=explode("\n", $text);
	$content=$contents[array_rand($contents)];
	return $content;
}

function get_random_tid($tieba){
	$ch = curl_init ('http://tieba.baidu.com/f?kw='.urlencode(iconv('utf-8', 'gbk', $tieba)).'&fr=index');
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$contents = curl_exec ( $ch );
	curl_close ( $ch );
	preg_match_all('/<li class="j_thread_list[A-z0-9 -_]+" data-field=\'{(?<json>.*?)}\'/', $contents, $jsontids);
	foreach ($jsontids['json'] as $jsontid){
		$jsontid=str_replace('&quot;','"', '{'.$jsontid.'}');
		$tids[]=json_decode($jsontid)->id;
	}
	$tid=$tids[rand(0,count($tids)-1)];
	return $tid;
}

function client_rppost($uid, $tieba, $content) {
	$cookie = get_cookie ( $uid );
	preg_match ( '/BDUSS=([^ ;]+);/i', $cookie, $matches );
	$BDUSS = trim ( $matches [1] );
	$setting = DB::fetch_first ( "SELECT * FROM xxx_post_setting WHERE uid='{$uid}'" );
	if ($setting ['client_type'] == 5)
		$setting ['client_type'] = rand ( 1, 4 );
	if (! $BDUSS) return array (- 1,'找不到 BDUSS Cookie' );
	if (! $content) $content=get_random_content();
	if (! $tieba['tid']) $tieba['tid']=get_random_tid($tieba ['name']);
	$formdata = array (
			'BDUSS' => $BDUSS,
			'_client_id' => 'wappc_136' . random ( 10, true ) . '_' . random ( 3, true ),
			'_client_type' => $setting ['client_type'],
			'_client_version' => '5.0.0',
			'_phone_imei' => md5 ( random ( 16 ) ),
			'anonymous' => 0,
			'content' => $content,
			'fid' => $tieba ['fid'],
			'kw' => urldecode ( $tieba ['name'] ),
			'net_type' => 3,
			'tbs' => get_tbs ( $tieba ['uid'] ),
			'tid' => $tieba ['tid'],
			'title' => ""
	);
	$adddata = '';
	foreach ( $formdata as $k => $v )
		$adddata .= $k . '=' . $v;
	$sign = strtoupper ( md5 ( $adddata . 'tiebaclient!!!' ) );
	$formdata ['sign'] = $sign;
	$ch = curl_init ( 'http://c.tieba.baidu.com/c/c/post/add' );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/x-www-form-urlencoded'
	) );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_COOKIE, $cookie );
	curl_setopt ( $ch, CURLOPT_POST, true );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $formdata ) );
	$re = @json_decode ( curl_exec ( $ch ), ture );
	curl_close ( $ch );
	switch ($setting ['client_type']) {
		case '1' :
			$client_res = "iphone";
			break;
		case '2' :
			$client_res = "android";
			break;
		case '3' :
			$client_res = "WindowsPhone";
			break;
		case '4' :
			$client_res = "Windows8";
			break;
	}
	if (!$re) return array (0,'JSON 解析错误' );
	if ($re ['error_code'] == 0) return array (2,"使用" . $client_res . '客户端发帖成功，<a href="http://tieba.baidu.com/p/' . $tieba ['tid'] . '" target="_blank">查看帖子</a>');
	else if ($re ['error_code'] == 5) return array (5,"需要输入验证码，请检查你是否已经关注该贴吧。" 	);
	else if ($re ['error_code'] == 7) return array (7,"您的操作太频繁了！" );
	else if ($re ['error_code'] == 8) return array (8,"您已经被封禁" );
	else return array($re ['error_code'],"未知错误，错误代码：" . $re ['error_code']);
}

?>