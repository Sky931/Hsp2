<?php
/* Copyright © 2014 Hiroki's Co.Ltd. All Rights Reserved. */

/**
 * ログイン後のセッション管理.
 *
 * @author 5/18/2014 Hiroki Nogami
 */

	session_start();

	// ログイン状態のチェック
	if (!isset($_SESSION["USERID"])) {
		header("Location: logout.php");
		exit;
	}
 /**
  * 指定URLより,HTML取得.
  *
  * @author 5/18/2014 Hiroki Nogami
  */


 	//対象URL取得
    //$geturl = $_POST{'geturl'};
	header("Content-type:text/html;charset=UTF-8");
	mb_language('Japanese');
	$geturl_1 = "http://music.usen.com/usencms/search_nowplay1.php?npband=B&npch=14&nppage=yes";

  
        // HTMLソース取得
        $html = file_get_contents($geturl_1);

        
/**
 *取得HTMLより,曲名を取得.
 *
 * @author 5/18/2014 Hiroki Nogami
 */

        //曲名開始直前までをカット
        $num1 = strpos($html,"np-now'><li class='np03'>");
        $html = substr($html,$num1+25);
        
        //曲名最後尾以降をカット
        $num2 = strpos($html,"</li></ul>");
        $html = substr($html,0,$num2);
        
        //取得した曲名を『全角->半角』変換
        $html = mb_convert_kana($html, "r", "UTF-8");
        
        //文字の置換[/]->[+%2F+],[ ]->[+]
        $key = str_replace("／","%2F",$html);
        $key = str_replace(" ","+",$key);
        $key = str_replace("　","+",$key);
        //$key = preg_replace('/\s+/g',"+",$key);
        //echo $key;
        
/**
 *取得HTMLより,曲のコードを取得.
 *
 * @author 5/18/2014 Hiroki Nogami
 */        

        //曲のタイトルを検索画面のURLに結合
        $geturl_2 = "https://www.youtube.com/results?search_query=".$key;
 
        // HTMLソース取得
        $html_2 = file_get_contents($geturl_2);
        
        //id開始直前までをカット
        //$num3 = strpos($html_2,"<a href=\"/watch?v=");
        $num3 = strpos($html_2,"<h3 class=\"yt-lockup-title\"><a href=\"/watch?v=");
        $html_2 = substr($html_2,$num3+46);
        
        //id最後尾以降までをカット
        //$num4 = strpos($html_2,"\" class=\"contains-addto yt-uix-sessionlink spf-link \" data");
        $num4 = strpos($html_2,"\" class=\"yt-uix-tile-link yt-ui-ellipsis");
        
        $idcode = substr($html_2,0,$num4);
        
        $html_2 = htmlspecialchars($html_2);
        $html_2 = mb_ereg_replace('\r\n', '<br />', $html_2);
        $html_2 = mb_ereg_replace('\n', '<br />', $html_2);
        $html_2 = mb_ereg_replace('\r', '\<br />', $html_2);
        
        //echo $idcode;
        
        //For Debug
        /*$html = htmlspecialchars($html);
        $html = mb_ereg_replace('\r\n', '<br />', $html);
        $html = mb_ereg_replace('\n', '<br />', $html);
        $html = mb_ereg_replace('\r', '<br />', $html);*/
        
        //echo $key;
   
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>MusicPlayer</title>
<script>

      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '390',
          width: '640',
          //videoId: 'M7lc1UVf-VE',
          //ここに動画のコードを入れる。
          videoId: <?php echo "'".$idcode."',";?>
          playerVars: {
              rel      : 0,
              autoplay : 1
          },
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange,
            'onError': onPlayerError,
          }
        });
      }

   // 4. The API will call this function when the video player is ready.
   function onPlayerReady(event) {

  		// iPhoneやAndroid用
		if (navigator.userAgent.indexOf('iPhone') > 0 || 
		  	navigator.userAgent.indexOf('iPad') > 0 || 
		  	navigator.userAgent.indexOf('iPod') > 0 || 
		  	navigator.userAgent.indexOf('Android') > 0) {	
			
		} else {
			
	  		event.target.playVideo();
		}
    }

      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
    function onPlayerStateChange(event) {
      if (event.data == YT.PlayerState.ENDED && !done) {
        location.reload(true);
        done = true;
      }
    }
    
    //For debug output
    function stopVideo() {
      player.stopVideo();
    }
    
    var childwindow;	//新規タブ作成変数
    var timer1;			//タブのタイマー時間変数
    var timer2;			//reload用タイマー時間変数
    
    //著作権保護動画を再生する関数。
    function onPlayerError(event){
    	if(event.data == 101 || event.data == 150)
    	timer1 = player.getDuration()*1000;
    	timer2 = (player.getDuration()+2)*1000;
    	childwindow = window.open(player.getVideoUrl(), "new");
    	setTimeout("close()", this.timer1);
    	setTimeout("location.reload(true)", this.timer2);
    }
 
    //開いたタブを閉じる。
    function close(){
    	childwindow.close();
    }
    </script>

</head>
<body>

  <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
  	  <div id="player"></div>

  <ul>
  <li><a href="logout.php">logout</a></li>
  </ul>

</body>
</html>