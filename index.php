<?php

	$channel = $_GET["c"];

	// Get news
	$data = get_data('https://t.me/s/'.$channel);
	$data = preg_replace('/\s+/', '_', $data);

	$m_start = '<div_class="tgme_widget_message_text_js-message_text"_dir="auto">';
	$m_end = '<div_class="tgme_widget_message_bubble">';

	$data = str_replace($m_start,"<start_message>", $data);
	$data = str_replace($m_end, "<end_message>", $data);

	preg_match_all("/<start_message>(.*)<end_message>/U", $data, $out, PREG_PATTERN_ORDER);
	
	$max_index = max(array_keys($out[0]));


// RSS section	
	header( "Content-type: text/xml");
 
	echo "<?xml version='1.0' encoding='UTF-8'?>
			<rss version='2.0'>
				<channel>
					<title>$channel</title>
					<link>/</link>
					<description>News from Telegram channel - $channel</description>
					<language>en-us</language>";

	
	for ($i = $max_index; $i >= 0; $i--) {
	$news = $out[0][$i];
	$news =  preg_replace('/_/', ' ', $news);
	$news =  str_replace("<br/>", " ", $news );
	$news = str_replace('&quot;', '"', $news);
	$news = str_replace('&#33;', '!', $news);
	$news_title = implode(' ', array_slice(explode(' ', $news), 0, 16));
	$news_title = str_replace('<b>', '', $news_title);
	$news_title = str_replace('"', '', $news_title);
	$news_title = str_replace('<start message>', '', $news_title);
	$news_title = preg_replace(array('/,/', '/\./'), '', $news_title);
	$r1 = get_between($news_title, '<i' , 'i>');
	$news_title = str_replace($r1, '', $news_title);
	$news_title = str_replace('<ii>', '', $news_title);
	$r2 = get_between($news_title, '<a' , 'a>');
	$news_title = str_replace('<a'.$r2.'a>', '', $news_title);
	$news_title = str_replace(array('(', ')'), '', $news_title);
	$news_title = str_replace('<i>', '', $news_title);
	$news_title = str_replace('</i>', '', $news_title);
	$news_title = str_replace('<div class=tgme widget message footer compact js-message footer>', '', $news_title);
	$news_title = str_replace('</div>', '', $news_title);
	$r3 = trim(get_between($news_title, '<', '>'));
	$news_title = str_replace($r3, '', $news_title);
	$news_title = $news_title.' a>';
	$r4 = trim(get_between($news_title, '<a', 'a>'));
	$news_title = str_replace($r4, '', $news_title);
	$news_title = str_replace('<a', '', $news_title);
	$news_title = str_replace('a>', '', $news_title);
	$news_title = $news_title.' div>';
	$r5 = trim(get_between($news_title, '<div', 'div>'));
	$news_title = str_replace($r5, '', $news_title);
	$news_title = str_replace('<div', '', $news_title);
	$news_title = str_replace('div>', '', $news_title);
	$news_title = trim($news_title);
	$news_body = trim(get_between($news, '<start message>', '<'));
	$news_url = trim(get_between($news, 'tgme widget message date" href="', '">'));
	$news_date = trim(get_between($news, 'datetime="', '">'));

	
	echo "<item>
			<title>$news_title </title>
			<link>$news_url</link>
			<description>$news_body</description>
			<pubDate>$news_date</pubDate>
		</item>";
	
	}
    echo "</channel></rss>";

// Get data from URL
function get_data($url) {
	$ch = curl_init();
	$timeout = 60;
	$proxy = 'socks5://127.0.0.1:9150';
	$useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36';
//	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
//	curl_setopt($ch, CURLOPT_PROXY, $proxy);
	curl_setopt($ch,CURLOPT_USERAGENT, $useragent);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

// Function for trim string between particular character
function get_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

?>
