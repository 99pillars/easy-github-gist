<?php
/*
Plugin Name: Easy GitHub Gist
Plugin URI: http://wordpress.org/extend/plugins/easy-github-gist/
Description: Easy GitHub Gist Plugin allows you to embed GitHub Gists from https://gist.github.com/.
Usage: Just put the GitHub Gist url in the content.
Version: 0.1 
Author: Sivan 
Author URI: http://lightcss.com/
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

define("REGEXP_GIST_URL","\"http:\/\/gist.github.com\/(.+)\.js\?file=(.+)\"");

//catch the code
function get_content_from_url($url) {
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}
//catch raw code
function gist_raw($id, $file) {
	$request = "http://gist.github.com/raw/".$id."/".$file;
	return get_content_from_url($request);
}
//noscript callback,是否把inline style移到style.css?
function gist_raw_html($gist_raw) {
	return "<div style='margin-bottom:1em;padding:0;'><noscript><code><pre style='overflow:auto;margin:0;padding:0;border:1px solid #DDD;'>".htmlentities($gist_raw)."</pre></code></noscript></div>";
}

function gist_shortcode($atts) {
	return sprintf(
		'<script src="https://gist.github.com/%s.js%s"></script>', 
		$atts['id'], 
		$atts['file'] ? '?file=' . $atts['file'] : ''
	);
}
add_shortcode('gist','gist_shortcode');

function gist_shortcode_filter($content) {
	return preg_replace('/https:\/\/gist.github.com\/([\d]+)[\.js\?]*[\#]*file[=|-|_]+([\w\.]+)(?![^<]*<\/a>)/i', '[gist id="${1}" file="${2}"]', $content );
}
add_filter( 'the_content', 'gist_shortcode_filter', 9);

?>