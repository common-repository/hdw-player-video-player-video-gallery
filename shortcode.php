<?php

/******************************************************************
/* User Function
******************************************************************/
require_once (dirname ( __FILE__ ) . '/config.php');
function hdwplayer_plugin_shortcode($atts) {
	global $wpdb;
	if (! $atts ['id'])
		$atts ['id'] = 1;
	
	$embed = '';
	$html5 = '';
	$isHtml5 = false;
	srand ((double) microtime( )*1000000);
	$dyn      = rand( );
	
	$player = $wpdb->get_row ($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "hdwplayer WHERE id=%d",intval($atts ['id'])));
	
	$siteurl = get_option ( 'siteurl' );
	$src = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/player.swf';
	$noimage = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/assets/noimage.jpg';
	$buttons = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/assets/buttons.png';
	$slider = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/js/jquery.slider.min.js';
	$inner = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/assets/inner1.png';
	$outer = plugins_url() .'/'  . basename ( dirname ( __FILE__ ) ) . '/assets/outer1.png';
	$playerurl = $siteurl . '/?embed=view';
	
	$flashvars = 'baseW=' . $siteurl . '&id=' . encrypt_decrypt ( 'encrypt', $player->id );
	
	$gallery = $wpdb->get_row ($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "hdwplayer_gallery WHERE id=%d",$player->galleryid));
	
	if ($player->videoid) {
		
		$results = $wpdb->get_row ($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "hdwplayer_videos WHERE id=%d",$player->videoid));
		
	} else if ($player->playlistid) {
		
		$results = $wpdb->get_row ($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "hdwplayer_videos WHERE playlistid=%d ORDER BY ordering LIMIT 1",intval ( $player->playlistid )) );
		$playlist = $wpdb->get_results ($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "hdwplayer_videos WHERE playlistid = %d ORDER BY ordering",intval ( $player->playlistid )));
		
	}
	$htmlNode  = '';
	$htmlNode .= '<div class="hdwplayer_video" style="width:'.$player->width.'px;height:'.$player->height.'px; clear:both"><div id="player'.$dyn.'" class="hdwhtml5player">';
	$htmlNode .= '</div></div>';
	
	$htmlNode .= '<script>hdwplayer({
					id       : "player'.$dyn.'",
					swf 	 : "'.$src.'?api=true&rand='.$dyn.'",
					width    : "100%",
					height   : "100%",
					baseW    : "'.$siteurl.'",
					api: true,
					wid      : "'.encrypt_decrypt ( 'encrypt', $player->id ).'",
					f:"'.$flashvars.'"
				});</script>';
	
	$detect = new HDWMobile_Detect();
	if ($detect->isMobile()) {
		$html5 .= '<style>
			@media only screen and (max-width: 640px) {
			#player_div' . $dyn . ', #player_container'.$dyn.', #player_div' . $dyn . ' a{
				width:100% !important;
				height: 360px !important;
			}
			}
			
			@media only screen and (max-width: 460px) {
			#player_div' . $dyn . ', #player_container'.$dyn.', #player_div' . $dyn . ' a{
				width:100% !important;
				height: 300px !important;
			}
			}
		
			@media only screen and (max-width: 320px) {
			#player_div' . $dyn . ', #player_container'.$dyn.', #player_div' . $dyn . ' a{
				width:100% !important;
				height: 200px !important;
			}
			}
			</style>';
	}
	$embed .= $htmlNode;
	/* ================ Gallery List ================ */
	
	if (@$gallery->id) {
		$link = "";
		$qstr = "";
		$column = 0;
		$row = 1;
		$k = 0;
		$n = 0;
		$exit = 0;
		if ($detect->isMobile() && (strpos($detect->userAgent(),'iPhone') !== FALSE || strpos($detect->userAgent(),'Android') !== FALSE))
		{
			$gallery->rows = $gallery->columns = 1;
		}
		$totalvideo = count ( $playlist );
		
		if ($totalvideo < $gallery->limit) {
			$gallery->limit = $totalvideo;
		}
		if($totalvideo > $gallery->limit){
			$totalvideo = $gallery->limit;
		}
		
		$pagelimit = $gallery->rows * $gallery->columns;
		$totaldiv = intval ( $totalvideo / $pagelimit );
		$remain = $totalvideo % $pagelimit;
		
		if ($remain > 0)
			$totaldiv ++;
		if($gallery->columns > $totalvideo){
			$cols = $totalvideo;
			$rows = 1;
		}else{
			$cols = $gallery->columns;
			if($totalvideo < $pagelimit){
				if(($totalvideo % $gallery->rows) == 0){
					$rows = intval ($totalvideo / $gallery->rows);
				}else{
					$rows = intval ($totalvideo / $gallery->rows) + 1;
				}
			}else{
				$rows = $gallery->rows;
			}			 
		}
		
		$vh = ((($gallery->height + 7) + 28) * $rows) + (($rows) * $gallery->space);
		$vw = (($gallery->width + 4) * $cols) + (($cols - 1) * $gallery->space);
		
		$embed .= '<style>#slider' . $dyn . ' *{box-sizing: content-box;}#slider' . $dyn . ' { height: 1%; overflow:hidden; padding: 10px 0;   width: '. ($vw+78) .'px; }
					#slider' . $dyn . ' .viewport { float: left; width: ' . $vw . 'px; overflow: hidden; position: relative; }
					#slider' . $dyn . ' .buttons { background:url("' . $buttons . '") no-repeat scroll 0 0 transparent; display: block; margin: ' . (($vh / 2) - 17) . 'px 0px 0 0; background-position: 0 -38px; text-indent: -999em; float: left; width: 39px; height: 37px; overflow: hidden; position: relative; }
					#slider' . $dyn . ' .next { background-position: 0 0; margin: ' . (($vh / 2) - 17) . 'px 0 0 0px;  }
					#slider' . $dyn . ' .disable { visibility: hidden; }
					#slider' . $dyn . ' .overview { list-style: none; position: absolute; padding: 0; margin: 0; width: ' . $vw . 'px; left: 0 top: 0; }
					#slider' . $dyn . ' .overview li{ float: left; margin: 0 20px 0 0; padding: 1px; height: 100%; border: 0px solid #dcdcdc; width: ' . $vw . 'px;}
					</style>';
		$embed .= '';
		$embed .= '<script src="' . $slider . '"></script>';
		$embed .= '<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(document).ready(function(){
			$j("#slider' . $dyn . '").slider({ display: 1 });
		});
		</script>';
		$embed .= '<div id="slider' . $dyn . '">';
		$embed .= '<a class="buttons prev" href="#">left</a>';
		$embed .= '<div id="viewport' . $dyn . '" class="viewport">';
		$embed .= '<ul id="overview' . $dyn . '" class="overview">';
		
		$index = 0;
		
		for($j = 0; $j < $totaldiv; $j ++) {
			$embed .= '<li>';
			if (($n + $pagelimit) > $totalvideo) {
				$n = $n + $remain;
			} else {
				$n = $n + $pagelimit;
			}
			for($i = $k; $i < $n; $i ++) {
				$item = $playlist [$i];
				$css = 'float:left';
				if ($column >= $gallery->columns) {
					$css = 'float:left; clear:both';
					$column = 0;
					$row ++;
				}
				$xpos = ($column > 0) ? $gallery->space : 0;
				$ypos = ($row > 0) ? $gallery->space : 0;
				$column ++;
				
				if ($item->thumb == '')
					$item->thumb = $noimage;
				
				$embed .= '<div style="cursor: pointer; margin:' . $ypos . 'px 0px 0px ' . $xpos . 'px; ' . $css . ' ">';
				$embed .= '<div style="width:' . $gallery->width . 'px; height:' . $gallery->height . 'px;"><img class="hdw_gallery" data-id="' . $index . '" data-pid="' . $dyn . '" style="height:' . $gallery->height . 'px; width:' . $gallery->width . 'px; " src="' . $item->thumb . '" width="' . $gallery->width . '" height="' . $gallery->height . '" title="' . 'Click to Watch : ' . $item->title . '" border="0"/></div>';
				$embed .= '<div style="width:' . $gallery->width . 'px; margin:2px; 2px;"><a class="hdw_gallery" data-id="' . $index . '" data-pid="' . $dyn . '" style="text-decoration:none;" title="' . $item->title . '">' . $item->title . '</a></div>';
				$embed .= '</div>';
				$index++;
				if (($i + 1) == $gallery->limit) {
					$exit = 1;
				}
			}
			$k = $k + $pagelimit;
			$embed .= '</li>';
			if ($exit == 1) {
				break;
			}
		}
		$embed .= '</ul>';
		$embed .= '</div>';
		$embed .= '<a class="buttons next" href="#">right</a>';
		$embed .= '</div>';
		$embed .= '<script type="text/javascript">
				$j(window).load(function(){
				$j("#viewport' . $dyn . '").css("height",$j("#overview' . $dyn . '").outerHeight());
			});</script>';
	}
	
	if($isHtml5 == true || @$gallery->id){
		$embed .= '<script>
			$j(document).ready(function(){
					$j(".hdw_gallery").on("click",function(){
						var pid = $j(this).data("pid");
						var i = $j(this).data("id");
						$j("#player"+pid).loadPlaylistItem(i);
					})
				});
			</script>';
	}
	return $embed;
}

add_shortcode ( 'hdwplayer', 'hdwplayer_plugin_shortcode' );

?>