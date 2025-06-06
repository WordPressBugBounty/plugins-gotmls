<?php
/**
 * GOTMLS Plugin Global Variables and Functions
 * @package GOTMLS
 * @since 4.23.81
*/

define("GOTMLS_plugin_path", dirname(dirname(__FILE__))."/");

if (!function_exists("__")) {
function __($text, $domain = "gotmls") {
	return $text;
}}

require_once(GOTMLS_plugin_path."safe-load/trace.php");

GOTMLS_define("GOTMLS_local_images_path", substr(__FILE__, 0, GOTMLS_strlen(__FILE__) - GOTMLS_strlen(basename(__FILE__))));

if (!defined("ABSPATH")) {
	define("ABSPATH", dirname(dirname(__FILE__)).'/safe-load/');
	$root_path = dirname(ABSPATH);
	while (GOTMLS_strlen($root_path) > 1 && !is_file($root_path."/wp-config.php"))
		$root_path = dirname($root_path);
	if (is_file($root_path."/wp-config.php"))
		include_once($root_path."/wp-config.php");
	else
		die("No wp-config!");
}

$bad = array("eval", "preg_replace", "auth_pass");
$GLOBALS["GOTMLS"] = array(
	"MT" => microtime(true), 
	"tmp"=>array("debug_fix"=>"", "HeadersError"=>"", "onLoad"=>"", "file_contents"=>"", "new_contents"=>"", "threats_found"=>array(), 
		"base_page" => "GOTMLS-settings", 
		"pluginTitle" => "Anti-Malware", 
		"default_encodings" => array('UTF-8', 'ISO-8859-1', 'windows-1252'), 
		"skip_dirs" => array(".", ".."), "scanfiles" => array(), "nonce"=>array(),
		"mt" => ((isset($_REQUEST["mt"])&&GOTMLS_strlen($_REQUEST["mt"])==32)?$_REQUEST["mt"]:md5(microtime(true))), 
		"threat_files" => array("htaccess"=>".htaccess","timthumb"=>"thumb.php"), 
		"apache" => array(),
		"skip_ext"=>array("png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg", "webp", "doc", "docx", "otf", "ttf", "fla", "flv", "mov", "mp3", "pdf", "css", "pot", "po", "mo", "so", "exe", "zip", "7z", "gz", "rar"),
		"execution_time" => 60,
		"default" => array("msg_position" => array("80px", "40px", "400px", "600px")),
		"Definition" => array("Default" => "CCIGG"),
		"definitions_array" => array(
			"potential" => array(
				$bad[0] => array("CCIGG", "/[^a-z_\\/'\"]".$bad[0]."\\(.+\\)+\\s*;/i"),
				$bad[1]." /e" => array("CCIGG", "/".$bad[1]."[\\s*\\(]+(['\"])([\\!\\/\\#\\|\\@\\%\\^\\*\\~]).+?\\2[imsx]*e[imsx]*\\1\\s*,[^,]+,[^\\)]+[\\);\\s]+/i"),
				$bad[2] => array("CCIGG", "/\\\$".$bad[2]."\\s*=.+;/i"),
				"function add_action wp_enqueue_script json2" => array("CCIGG", "/json2\\.min\\.js/i"),
				"Tagged Code" => array("CCIGG", "/\\#(\\w+)\\#.+?\\#\\/\\1\\#/is"),
				"protected by copyright" => array("CCIGG", "/\\/\\* This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited. \\*\\//i")
			)
		)
	)
);
if (isset($_SERVER["HTTP_HOST"]))
	$SERVER_HTTP = 'HOST://'.GOTMLS_safe_domain($_SERVER["HTTP_HOST"]);
elseif (isset($_SERVER["SERVER_NAME"]))
	$SERVER_HTTP = 'NAME://'.GOTMLS_safe_domain($_SERVER["SERVER_NAME"]);
elseif (isset($_SERVER["SERVER_ADDR"]))
	$SERVER_HTTP = 'ADDR://'.GOTMLS_safe_ip($_SERVER["SERVER_ADDR"]);
else
	$SERVER_HTTP = "NULL://not.anything.com";
if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"])
	$SERVER_HTTP .= ":".GOTMLS_safe_ip($_SERVER["SERVER_PORT"]);
$SERVER_parts = explode(":", $SERVER_HTTP.":");
if ((isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1)) || (count($SERVER_parts) > 2 && $SERVER_parts[2] == "443"))
	$GLOBALS["GOTMLS"]["tmp"]["protocol"] = "https:";
else
	$GLOBALS["GOTMLS"]["tmp"]["protocol"] = "http:";
GOTMLS_define("GOTMLS_script_URI", preg_replace('/\&(last_)?mt=[0-9\.a-f]+/i', '', str_replace('&amp;', '&', GOTMLS_htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES))).'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"]);
GOTMLS_define("GOTMLS_plugin_home", "https://gotmls.net/");
if (function_exists("plugins_url"))
	GOTMLS_define("GOTMLS_images_path", plugins_url('/', __FILE__));
elseif (function_exists("plugin_dir_url"))
	GOTMLS_define("GOTMLS_images_path", plugin_dir_url(__FILE__));
elseif (isset($_SERVER["DOCUMENT_ROOT"]) && ($_SERVER["DOCUMENT_ROOT"]) && GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"]) < __FILE__ && substr(__FILE__, 0, GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"])) == $_SERVER["DOCUMENT_ROOT"])
	GOTMLS_define("GOTMLS_images_path", substr(dirname(__FILE__), GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"])).'/');
elseif (isset($_SERVER["SCRIPT_FILENAME"]) && isset($_SERVER["DOCUMENT_ROOT"]) && ($_SERVER["DOCUMENT_ROOT"]) && GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"]) < GOTMLS_strlen($_SERVER["SCRIPT_FILENAME"]) && substr($_SERVER["SCRIPT_FILENAME"], 0, GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"])) == $_SERVER["DOCUMENT_ROOT"])
	GOTMLS_define("GOTMLS_images_path", substr(GOTMLS_safe_url(dirname($_SERVER["SCRIPT_FILENAME"])), GOTMLS_strlen($_SERVER["DOCUMENT_ROOT"])).'/');
else
	GOTMLS_define("GOTMLS_images_path", "/wp-content/plugins/gotmls/images/");

function GOTMLS_user_can() {
	if (is_multisite())
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "manage_network";
	elseif (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]) || $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] == "manage_network")
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "activate_plugins";
	if (current_user_can($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]))
		return true;
	else
		return false;
}

function GOTMLS_update_option($index, $value = array(), $auto = true) {
	return update_option('GOTMLS_'.$index.'_blob', GOTMLS_encode(serialize($value)), $auto);
}

function GOTMLS_get_option($index, $value = array()) {
	if (is_array($tmp = get_option('GOTMLS_'.$index.'_array', array())) && count($tmp)) {
		GOTMLS_update_option($index, $tmp);
		delete_option('GOTMLS_'.$index.'_array');
	} else
		$tmp = $value;
	return GOTMLS_uckserialize(GOTMLS_decode(get_option('GOTMLS_'.$index.'_blob', GOTMLS_encode(serialize($tmp)))));
}

$GLOBALS["GOTMLS"]["tmp"]["nonce"] = GOTMLS_get_option('nonce', array());
$GLOBALS["GOTMLS"]["tmp"]["settings_array"] = get_option('GOTMLS_settings_array', array());
$GLOBALS["GOTMLS"]["tmp"]["definitions_array"] = GOTMLS_get_option('definitions', $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]);
GOTMLS_define("GOTMLS_siteurl", rtrim(get_option("siteurl", $GLOBALS["GOTMLS"]["tmp"]["protocol"].$SERVER_parts[1].((count($SERVER_parts) > 2 && ($SERVER_parts[2] == '80' || $SERVER_parts[2] == '443'))?"":":".$SERVER_parts[2])."/"), '\\/'));
GOTMLS_load_scanlog($GLOBALS["GOTMLS"]["tmp"]["mt"]);
if (!(isset($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]) && is_array($GLOBALS["GOTMLS"]["scan"]["log"]["settings"])))
	$GLOBALS["GOTMLS"]["scan"]["log"]["settings"] = $GLOBALS["GOTMLS"]["tmp"]["settings_array"];
GOTMLS_define("GOTMLS_installation_key", md5(GOTMLS_siteurl));
GOTMLS_define("GOTMLS_update_home", "//updates.gotmls.net/".GOTMLS_installation_key."/");

function GOTMLS_get_corefile_URL($path, $hash) {
	if (strpos($URL = GOTMLS_get_version("URL"), '&cp='))
	//$hash != md5($contents)."O".GOTMLS_strlen($contents)
		return 'http:'.GOTMLS_update_home.'cp_core.php?'.$URL.'&f='.GOTMLS_encode($path)."&h=$hash&ts=".gmdate("YmdHis").'&d='.rawurlencode(GOTMLS_siteurl);
	else
		return "http://core.svn.wordpress.org/tags/".GOTMLS_wp_version."$path";
}

function GOTMLS_Invalid_Nonce($pre = "//Error: ") {
	return sprintf(__("%s Invalid or expired Nonce Token! %s Refresh and try again?",'gotmls'), $pre, (isset($_REQUEST["GOTMLS_mt"])?(" (".GOTMLS_htmlspecialchars($_REQUEST["GOTMLS_mt"]).((GOTMLS_strlen($_REQUEST["GOTMLS_mt"]) == 32)?(isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["hour"])&&isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["user"])?(substr($pre, 0, 7)=="//DEBUG"?GOTMLS_htmlspecialchars(", U:".$GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["user"].", H:".$GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["hour"]."!) "):" !UH!) "):" !found!) "):" !len[".GOTMLS_strlen($_REQUEST["GOTMLS_mt"])."]!) ")):" (GOTMLS_mt !set!) "));
}

function GOTMLS_set_nonce($context = "NULL", $uid = 0) {
	$hour = (int) round(round($GLOBALS["GOTMLS"]["MT"]/60)/60);
	if (!$uid)
		$uid = GOTMLS_get_current_user_id(GOTMLS_REMOTEADDR);
	$transient_name = md5(substr(number_format(microtime(true), 9, '-', '/'), 6).GOTMLS_installation_key.GOTMLS_plugin_path.$context.$uid);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["nonce"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["nonce"])) {
		foreach ($GLOBALS["GOTMLS"]["tmp"]["nonce"] as $nonce_key => $token) {
			if ((!(is_array($token) && isset($token["hour"]) && is_numeric($token["hour"]))) || (($token["hour"] > $hour) || (($token["hour"] + 24) < $hour)))
				unset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$nonce_key]);
			elseif (is_array($token) && isset($token["hour"]) && isset($token["user"]) && isset($token["context"]) && ($token["hour"] == $hour) && ($token["user"] == $uid) && ($token["context"] == $context))
				$transient_name = $nonce_key;
		}
	}
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$transient_name])) {
		$GLOBALS["GOTMLS"]["tmp"]["nonce"][$transient_name] = array("hour" => $hour, "user" => $uid, "context" => $context);
		if (!GOTMLS_update_option('nonce', $GLOBALS["GOTMLS"]["tmp"]["nonce"], false))
			return (GOTMLS_sanitize($context)."=DB-err:".rawurlencode(preg_replace('/[\r\n]+/', " ", print_r($GLOBALS["GOTMLS"]["tmp"]["nonce"],1).$wpdb->last_error)));
	}
	return 'GOTMLS_mt='.rawurlencode($transient_name);
}

function GOTMLS_get_nonce($context = "", $uid = 0) {
	$return = false;
	if (isset($_REQUEST["GOTMLS_mt"])) {
		if (!$uid)
			$uid = GOTMLS_get_current_user_id(GOTMLS_REMOTEADDR);
		if (isset($_POST["GOTMLS_mt"]) && (GOTMLS_strlen($_POST["GOTMLS_mt"]) == 32) && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_POST["GOTMLS_mt"]]))
			$token = $GLOBALS["GOTMLS"]["tmp"]["nonce"][$_POST["GOTMLS_mt"]];
		elseif (isset($_GET["GOTMLS_mt"]) && (GOTMLS_strlen($_GET["GOTMLS_mt"]) == 32) && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_GET["GOTMLS_mt"]]))
			$token = $GLOBALS["GOTMLS"]["tmp"]["nonce"][$_GET["GOTMLS_mt"]];
		if (isset($token) && is_array($token) && isset($token["hour"]) && isset($token["user"]) && isset($token["context"])) {
			if (GOTMLS_strlen($context) && ($context != $token["context"]))
				$return = null;
			elseif ($uid == $token["user"] || (is_numeric($uid) && !is_numeric(GOTMLS_REMOTEADDR) && (GOTMLS_REMOTEADDR == $token["user"])))
				$return = (INT) $token["hour"];
			else
				$return = 0;
		} else
			$return = "";
	}
	return $return;
}

function GOTMLS_fileperms($file) {
	if ($prm = @fileperms($file)) {
		if (($prm & 0xC000) == 0xC000)
			$ret = "s";
		elseif (($prm & 0xA000) == 0xA000)
			$ret = "l";
		elseif (($prm & 0x8000) == 0x8000)
			$ret = "-";
		elseif (($prm & 0x6000) == 0x6000)
			$ret = "b";
		elseif (($prm & 0x4000) == 0x4000)
			$ret = "d";
		elseif (($prm & 0x2000) == 0x2000)
			$ret = "c";
		elseif (($prm & 0x1000) == 0x1000)
			$ret = "p";
		else
			$ret = "u";
		$ret .= (($prm & 0x0100)?"r":"-").(($prm & 0x0080)?"w":"-");
		$ret .= (($prm & 0x0040)?(($prm & 0x0800)?"s":"x" ):(($prm & 0x0800)?"S":"-"));
		$ret .= (($prm & 0x0020)?"r":"-").(($prm & 0x0010)?"w":"-");
		$ret .= (($prm & 0x0008)?(($prm & 0x0400)?"s":"x" ):(($prm & 0x0400)?"S":"-"));
		$ret .= (($prm & 0x0004)?"r":"-").(($prm & 0x0002)?"w":"-");
		$ret .= (($prm & 0x0001)?(($prm & 0x0200)?"t":"x" ):(($prm & 0x0200)?"T":"-"));
		return $ret;
	} else
		return "stat failed!";
}

function GOTMLS_file_details($file) {
	return '<div id="file_details_'.md5($file).'" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>File Details: '.GOTMLS_htmlspecialchars(basename($file)).'</b><br />in: '.dirname(realpath($file)).'<br />size: '.filesize(realpath($file)).' ( '.ceil(GOTMLS_strlen(GOTMLS_htmlspecialchars($GLOBALS["GOTMLS"]["tmp"]["file_contents"]))/1024).' KB )<br />encoding: '.(isset($GLOBALS["GOTMLS"]["tmp"]["encoding"])?$GLOBALS["GOTMLS"]["tmp"]["encoding"]:(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown")).'<br />permissions: '.GOTMLS_fileperms(realpath($file)).'<br />Owner/Group: '.fileowner(realpath($file)).'/'.filegroup(realpath($file)).(function_exists("getmyuid")&&function_exists("getmygid")?' (you are: '.getmyuid().'/'.getmygid():'(getmyuid does not exist').')<br />modified:'.gmdate(" Y-m-d H:i:s ", filemtime(realpath($file))).'<br />changed:'.gmdate(" Y-m-d H:i:s ", filectime(realpath($file))).'</div>';
}

function GOTMLS_esc_url($url) {
	if ("" === trim($url))
		return "";
	$original_url = $url;
	$url = str_replace(' ', '%20', ltrim($url));
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
	$url = wp_kses_normalize_entities($url);
	$url = str_replace('&amp;', '&#038;', $url);
	$url = str_replace("'", '&#039;', $url);
	if ((false !== strpos($url, '[')) || (false !== strpos($url, ']'))) {
		$end_dirty = preg_replace('/^([fhtps]+\:)?\/\/([^\@]+\@)*[^\/]++/i', '', $url);
		$end_clean = str_replace(array('[', ']'), array('%5B', '%5D'), $end_dirty);
		$url = str_replace($end_dirty, $end_clean, $url);
	}
	return $url;
}

function GOTMLS_admin_url($action, $url = '') {
	$return = admin_url("admin-ajax.php?action=$action");
	foreach (array('eli', 'oversize', 'GOTMLS_debug') as $pass_on)
		if (isset($_GET["$pass_on"]))
			$return .= "&$pass_on=".GOTMLS_esc_url($_GET["$pass_on"]);
	return ("$return&$url");
}

function GOTMLS_dashicon_button($title, $dashicon = "editor-help", $style = 'text-decoration: none;', $contents = "", $href = "javascript:void(0);") {
	$gt = ">"; // This local variable never changes
	$lt = "<"; // This local variable never changes
	return $lt.'a href="'.$href.'" title="'.$title.'" style="'.$style.'"'.$gt.$lt."span class='dashicons dashicons-$dashicon'$gt$lt/span$gt $contents$lt/a$gt\n";
}

function GOTMLS_close_button($box_id, $margin = '6px', $title = "Close") {
	return GOTMLS_dashicon_button($title, 'dismiss', "float: right; color: #F00; overflow: hidden; width: 20px; height: 20px; text-decoration: none; margin: $margin", "X", 'javascript:void(0);" onclick="showhide(\''.$box_id.'\');');
}

function GOTMLS_get_styles($pre_style = "") {
	$head_nonce = GOTMLS_set_nonce(__FUNCTION__."255");
	$gt = ">"; // This local variable never changes
	$lt = "<"; // This local variable never changes
	if (!GOTMLS_strlen(trim("$pre_style")))
		$pre_style = $lt."style$gt";
	return $pre_style.'
span.GOTMLS_date {float: right; width: 130px; white-space: nowrap;}
.GOTMLS_page {float: left; border-radius: 10px; padding: 0 5px;}
.GOTMLS_quarantine_item {margin: 4px 12px;}
.rounded-corners {margin: 10px; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border: 1px solid #000;}
.shadowed-box {box-shadow: -3px 3px 3px #666; -moz-box-shadow: -3px 3px 3px #666; -webkit-box-shadow: -3px 3px 3px #666;}
.sidebar-box {background-color: #CCC;}
iframe {border: 0;}
.GOTMLS-scanlog li a {display: none;}
.GOTMLS-scanlog li:hover a {display: block;}
.GOTMLS-sidebar-links {list-style: none;}
.GOTMLS-sidebar-links li img {margin: 3px; height: 16px; vertical-align: middle;}
.GOTMLS-sidebar-links li {margin-bottom: 0 !important;}
.popup-box {background-color: #FFC; display: none; position: absolute; left: 0px; z-index: 10;}
.shadowed-text {text-shadow: #00F -1px 1px 1px;}
.sub-option {float: left; margin: 3px 5px;}
.inside {margin: 10px; position: relative;}
.GOTMLS_li, .GOTMLS_plugin li {list-style: none;}
.GOTMLS_plugin {margin: 5px; background: #cfc; border: 1px solid #0C0; padding: 0 5px; border-radius: 3px;}
.GOTMLS_plugin.known, .GOTMLS_plugin.db_scan, .GOTMLS_plugin.htaccess, .GOTMLS_plugin.timthumb, .GOTMLS_plugin.errors {background: #f99; border: 1px solid #f00;}
.GOTMLS_plugin.potential, .GOTMLS_plugin.wp_core, .GOTMLS_plugin.skipdirs, .GOTMLS_plugin.skipped {background: #ffc; border: 1px solid #fc6;}
.GOTMLS ul li {margin-left: 12px;}
.GOTMLS h2 {margin: 0 0 10px;}
.postbox {margin-right: 10px; line-height: 1.4; font-size: 13px;}
#pastDonations li {list-style: none;}
#quarantine_buttons {margin: 0px; padding: 0px;}
#quarantine_buttons input.button-primary {margin-right: 20px;}
#reclean_buttons {
	color: #a00;
    min-height: 32px;
    border-top: solid 2px black;
    padding-top: 10px;
}
#reclean_buttons input.button-primary {float: right;}
#delete_button {
	background-color: #C33;
	color: #FFF;
	background-image: linear-gradient(to bottom, #C22, #933);
	border-color: #933 #933 #900;
	box-shadow: 0 1px 0 rgba(230, 120, 120, 0.5) inset;
	text-decoration: none; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
	margin-top: 10px;
}
#main-page-title {
	background: url("https://secure.gravatar.com/avatar/5feb789dd3a292d563fea3b885f786d6?s=64") no-repeat scroll 0 0 transparent;
	height: 64px;
	line-height: 58px;
	margin: 10px 0 0 0;
	max-width: 600px;
	padding: 0 110px 0 84px;
}
#main-page-title h1 {
	background: url("https://secure.gravatar.com/avatar/8151cac22b3fc543d099241fd573d176?s=64") no-repeat scroll top right transparent;
	height: 64px;
	line-height: 32px;
	margin: 0;
	padding: 0 84px 0 0;
	display: table-cell;
    text-align: center;
    vertical-align: middle;
}
'."$lt/style$gt\n$lt".'div id="div_file" class="shadowed-box rounded-corners sidebar-box" style="padding: 0; display: none; position: fixed; top: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'; left: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'; width: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'; height: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'; border: solid #c00; z-index: 112358;"'.$gt.$lt.'table style="width: 100%; height: 100%;" cellspacing="0" cellpadding="0"'.$gt.$lt.'tr'.$gt.$lt.'td style="border-bottom: 1px solid #EEE; height: 32px;" colspan="2"'.$gt.GOTMLS_close_button("div_file").$lt.'h3 onmousedown="grabDiv();" onmouseup="releaseDiv();" id="windowTitle" style="cursor: move; border-bottom: 0px none; z-index: 2345677; position: absolute; left: 0px; top: 0px; margin: 0px; padding: 6px; width: 90%; height: 20px;"'.$gt.GOTMLS_Loading_LANGUAGE."$lt/h3$gt$lt/td$gt$lt/tr$gt$lt".'tr'.$gt.$lt.'td colspan="2" style="height: 100%"'.$gt.$lt.'div style="width: 100%; height: 100%; position: relative; padding: 0; margin: 0; background-color: #fff;" class="inside"'.$gt.$lt.'center'.$gt.$lt.'img src="'.GOTMLS_images_path.'GOTMLS-Loading.gif" id="got-loading-gif" style="max-height: 280px;" alt="..."'.$gt.$lt.'br /'.$gt.GOTMLS_Loading_LANGUAGE.$lt.'br /'.$gt.$lt.'div id="gotmls_too_long" style="display: none;"'.$gt.__("If this is taking too long:",'gotmls').$lt.'br /'.$gt.$lt.'input type="button" onclick="showhide(\'div_file\');if (formx = document.getElementById(\'GOTMLS_Form_clean\')) formx.method = \'get\';" value="'.__("Go Back and Try Again",'gotmls').'" style="margin: 0 5px;" class="button-primary" /'.$gt.'or'.$lt.'input type="button" onclick="showhide(\'GOTMLS_iFrame\', true);" value="'.__("Show the Unloaded Page",'gotmls').'" style="margin: 0 5px;" class="button-primary" /'."$gt$lt/div$gt$lt/center$gt$lt".'iframe id="GOTMLS_iFrame" name="GOTMLS_iFrame" style="top: 0px; left: 0px; position: absolute; width: 100%; height: 100%; background-color: #CCC;"'."$gt$lt/iframe$gt$lt/td$gt$lt/tr$gt$lt".'tr'.$gt.$lt.'td style="height: 20px;"'.$gt.$lt.'iframe id="GOTMLS_statusFrame" name="GOTMLS_statusFrame" style="width: 100%; height: 20px; background-color: #CCC;"'."$gt$lt/iframe$gt$lt/div$gt$lt/td$gt$lt".'td style="height: 20px; width: 20px;"'.$gt.$lt.'h3 id="cornerGrab" onmousedown="grabCorner();" onmouseup="releaseCorner();" style="cursor: move; height: 24px; width: 24px; margin: 0; padding: 0; z-index: 2345678; overflow: hidden; position: absolute; right: 0px; bottom: 0px;"'.$gt.$lt.'span class="dashicons dashicons-editor-expand"'."$gt$lt/span$gt&#8690;$lt/h3$gt$lt/td$gt$lt/tr$gt$lt/table$gt$lt/div$gt\n$lt".'script type="text/javascript"'.$gt.'
function showhide(id) {
	divx = document.getElementById(id);
	if (divx) {
		if (divx.style.display == "none" || arguments[1]) {
			divx.style.display = "block";
			divx.parentNode.className = (divx.parentNode.className+"close").replace(/close/gi,"");
			return true;
		} else {
			divx.style.display = "none";
			return false;
		}
	}
}
function checkAllFiles(check) {
	var checkboxes = new Array(); 
	checkboxes = document["GOTMLS_Form_clean"].getElementsByTagName("input");
	for (var i=0; i<checkboxes.length; i++)
		if (checkboxes[i].type == "checkbox" && (checkboxes[i].id.substring(0, 6) == "check_" || checkboxes[i].id.substring(0, 24) == "GOTMLS_quarantine_check_"))
			checkboxes[i].checked = check;
}
function setvalAllFiles(val) {
	var checkboxes = document.getElementById("GOTMLS_fixing");
	if (checkboxes)
		checkboxes.value = val;
}
function getWindowWidth(min) {
	if (typeof window.innerWidth != "undefined" && window.innerWidth > min)
		min = window.innerWidth;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" && document.documentElement.clientWidth > min)
		min = document.documentElement.clientWidth;
	else if (typeof document.getElementsByTagName("body")[0].clientWidth != "undefined" && document.getElementsByTagName("body")[0].clientWidth > min)
		min = document.getElementsByTagName("body")[0].clientWidth;
	return min;
}
function getWindowHeight(min) {
	if (typeof window.innerHeight != "undefined" && window.innerHeight > min)
		min = window.innerHeight;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientHeight != "undefined" && document.documentElement.clientHeight > min)
		min = document.documentElement.clientHeight;
	else if (typeof document.getElementsByTagName("body")[0].clientHeight != "undefined" && document.getElementsByTagName("body")[0].clientHeight > min)
		min = document.getElementsByTagName("body")[0].clientHeight;
	return min;
}
function loadIframe(title) {
	showhide("gotmls_too_long", true);
	showhide("gotmls_too_long");
	showhide("GOTMLS_iFrame", true);
	showhide("GOTMLS_iFrame");
	document.getElementById("windowTitle").innerHTML = title;
	if (curDiv) {
		windowW = getWindowWidth(200);
		windowH = getWindowHeight(200);
		if (windowW > 200)
			windowW -= 30;
		if (windowH > 200)
			windowH -= 20;
		if (px2num(curDiv.style.width) > windowW) {
			curDiv.style.width = windowW + "px";
			curDiv.style.left = "0px";
		} else if ((px2num(curDiv.style.left) + px2num(curDiv.style.width)) > windowW) {
			curDiv.style.left = (windowW - px2num(curDiv.style.width)) + "px";
		}
		if (px2num(curDiv.style.height) > windowH) {
			curDiv.style.height = windowH + "px";
			curDiv.style.top = "0px";
		} else if ((px2num(curDiv.style.top) + px2num(curDiv.style.height)) > windowH) {
			curDiv.style.top = (windowH - px2num(curDiv.style.height)) + "px";
		}
		if (px2num(curDiv.style.left) < 0)
			curDiv.style.left = "0px";
		if (px2num(curDiv.style.top)< 0)
			curDiv.style.top = "0px";
	}
	showhide("div_file", true);
	if (IE)
		curDiv.scrollIntoView(true);
	setTimeout(function (){ showhide(\'gotmls_too_long\', true); }, 15000);
}
function cancelserver(divid) {
	document.getElementById(divid).innerHTML = "'.$lt."div class='error'$gt".GOTMLS_strip4java(__("No response from server!",'gotmls'))."$lt/div$gt".'";
}
var stopCheckingDefinitions = 0;
function checkPrimaryUpdateServer() {
	var updatescript = document.createElement("script");
	if (arguments[0])
		updatescript.setAttribute("src", pri_addr+arguments[0]);
	else
		updatescript.setAttribute("src", pri_addr);
	if (divx = document.getElementById("Definition_Updates"))
		divx.appendChild(updatescript);
	return setTimeout(function() {stopCheckingDefinitions = checkAlternateUpdateServer();}, 15000);
}
function checkAlternateUpdateServer() {
	var updatescript = document.createElement("script");
	if (arguments[0])
		updatescript.setAttribute("src", alt_addr+arguments[0]);
	else
		updatescript.setAttribute("src", alt_addr);
	if (divx = document.getElementById("Definition_Updates"))
		divx.appendChild(updatescript);
	return setTimeout(function() {stopCheckingDefinitions = cancelserver("Definition_Updates");}, 15000);
}
function checkupdateserver(server) {
	var updatescript = document.createElement("script");
	updatescript.setAttribute("src", server);
	if (divx = document.getElementById("GOTMLS_patch_searching"))
		divx.appendChild(updatescript);
	return setTimeout(function() {cancelserver("GOTMLS_patch_searching");}, '.(((INT) $GLOBALS["GOTMLS"]["tmp"]['execution_time'])+1).'000+3000);
}
var IE = document.all?true:false;
//if (!IE)	document.addEventListener("mousemove", getMouseXY);
document.onmousemove = getMouseXY;
var offsetX = 0;
var offsetY = 0;
var offsetW = 0;
var offsetH = 0;
var curX = 0;
var curY = 0;
var curDiv, loadingGif;
function getMouseXY(e) {
	if (IE) { // grab the mouse pos if browser is IE
		curX = event.clientX + document.body.scrollLeft;
		curY = event.clientY + document.body.scrollTop;
	} else {  // grab the mouse pos if browser is Not IE
		curX = e.pageX - document.body.scrollLeft;
		curY = e.pageY - document.body.scrollTop;
	}
	if (curX < 0) {curX = 0;}
	if (curY < 0) {curY = 0;}
	if (offsetX && curX > 10) {curDiv.style.left = (curX - offsetX)+"px";}
	if (offsetY && (curY - offsetY) > 0) {curDiv.style.top = (curY - offsetY)+"px";}
	if (offsetW && (curX - offsetW) > 360) {curDiv.style.width = (curX - offsetW)+"px";}
	if (offsetH && (curY - offsetH) > 200) {
		curDiv.style.height = (curY - offsetH)+"px";
		loadingGif.style.height = (curY - offsetH - 130)+"px";
	}
	return true;
}
function px2num(px) {
	return parseInt(px.substring(0, px.length - 2), 10);
}
function setDiv(DivID) {
	if (curDiv = document.getElementById(DivID)) {
		if (IE)
			curDiv.style.position = "absolute";
		curDiv.style.left = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'";
		curDiv.style.top = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'";
		curDiv.style.height = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'";
		curDiv.style.width = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'";
	}
	if (loadingGif = document.getElementById("got-loading-gif"))
		loadingGif.style.height = "'.(substr($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2], 0, -2) - 130).'px";
}
function grabDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetX=curX-px2num(curDiv.style.left); 
	offsetY=curY-px2num(curDiv.style.top);
}
function releaseDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="90%";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.GOTMLS_admin_url('GOTMLS_position', ($GOTMLS_position_nonce = GOTMLS_set_nonce(GOTMLS_position_msg)).'&GOTMLS_x=').'"+curDiv.style.left+"&GOTMLS_y="+curDiv.style.top;
	offsetX=0; 
	offsetY=0;
}
function grabCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetW=curX-px2num(curDiv.style.width); 
	offsetH=curY-px2num(curDiv.style.height);
}
function releaseCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="20px";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.GOTMLS_admin_url('GOTMLS_position', $GOTMLS_position_nonce.'&GOTMLS_w=').'"+curDiv.style.width+"&GOTMLS_h="+curDiv.style.height;
	offsetW=0; 
	offsetH=0;
}
function check_for_donation(chk) {
	if ((audl = document.getElementById("autoUpdateDownload")) && audl.src.replace(/^.+\?/,"")=="0")
		if (chk.substr(0, 8) != "Changed " || chk.substr(8, 1) != "0")
			chk += "\\n\\n'.__("Please make a donation for the use of this wonderful feature!",'gotmls').'";
	alert(chk);
}
setDiv("div_file");'."\n$lt/script$gt\n";
}

function GOTMLS_get_header($optional_box = "") {
	$gt = ">"; // This local variable never changes
	$lt = "<"; // This local variable never changes
	if (isset($_GET["check_site"]) && $_GET["check_site"])
		$pre_style = $lt.'div id="check_site" style="z-index: 1234567;"'.$gt.$lt.'img src="'.GOTMLS_images_path.'checked.gif" height=16 width=16 alt="&#x2714;"'.$gt.__("Tested your site. It appears we didn't break anything",'gotmls')." ;-)$lt/div$gt$lt".'script type="text/javascript"'.$gt.'if (csw = window.parent.document.getElementById("check_site_warning")) csw.style.backgroundColor=\'#0C0\';window.addEventListener(\'load\', (event) => {showhide(\'inside_ddd6dbd641b9a5909fe4d44da2017cc7\');});'."$lt/script$gt{$lt}li$gt Please $lt".'a target="_blank" href="https://wordpress.org/support/plugin/gotmls/reviews/#wporg-footer"'.$gt.'write a "Five-Star" Review'."$lt/a$gt".' on WordPress.org if you like this plugin.'."$lt/li$gt$lt".'style'.$gt.'#footer, #GOTMLS-metabox-container, #GOTMLS-right-sidebar, #admin-page-container, #wpadminbar, #adminmenuback, #adminmenuwrap, #adminmenu, .error, .updated, .notice, .update-nag {display: none !important;} #wpbody-content {padding-bottom: 0;} #wpbody, html.wp-toolbar {padding-top: 0 !important;} #wpcontent, #footer {margin-left: 5px !important;}';
	else
		$pre_style = $lt.'style'.$gt.'#GOTMLS-right-sidebar {float: right; margin-right: 0px;}';
	return GOTMLS_get_styles($pre_style).$lt.'div id="main-page-title"'.$gt.$lt.'h1 style="vertical-align: middle;"'.$gt.'Anti-Malware from&nbsp;GOTMLS.NET'."$lt/h1$gt$lt/div$gt";
}

function GOTMLS_object_to_array($obj) {
	if (is_object($obj))
		$obj = (array) $obj;
	$new = array();
    if (is_array($obj)) {
		foreach ($obj as $key => $val)
			$new[$key] = GOTMLS_object_to_array($val);
    } else
		$new = $obj;
    return $new;       
}

function GOTMLS_get_pagination($count, $wrap = "") {
	$Q_Paged = "";
	if (isset($_REQUEST["paged"]) && is_numeric($_REQUEST["paged"])) {
		if ((INT) $count < (INT) $_REQUEST["paged"])
			$GLOBALS["GOTMLS"]["Quarantine"]["paged"] = (INT) $count;
		else
			$GLOBALS["GOTMLS"]["Quarantine"]["paged"] = (INT) $_REQUEST["paged"];
	} else
		$GLOBALS["GOTMLS"]["Quarantine"]["paged"] = 1;
	for ($p = 1; $p <= $count; $p++) {
		$Q_Paged .= '<input class="GOTMLS_page" type="submit" value="'.$p.'"'.((isset($GLOBALS["GOTMLS"]["Quarantine"]["paged"]) && $GLOBALS["GOTMLS"]["Quarantine"]["paged"] == $p) || (!isset($GLOBALS["GOTMLS"]["Quarantine"]["paged"]) && 1 == $p)?" DISABLED":"").' name="paged">';
	}
	if ($Q_Paged) {
		foreach ($_GET as $name => $value) {
			if (substr($name, 0, 10) != 'paged') {
				if (is_array($value)) {
					foreach ($value as $val)
						$Q_Paged .= '<input type="hidden" name="'.GOTMLS_htmlspecialchars($name).'[]" value="'.GOTMLS_htmlspecialchars($val).'">';
				} else
					$Q_Paged .= '<input type="hidden" name="'.GOTMLS_htmlspecialchars($name).'" value="'.GOTMLS_htmlspecialchars($value).'">';
			}
		}
		$Q_Paged = '<form method="GET" name="GOTMLS_Form_page"><div style="float: left;">Page:</div>'."$Q_Paged\n</form><br style=\"clear: left;\" />\n";
	}
	if ($wrap)
		return "$Q_Paged<!-- p = $p , count = $count -->$wrap$Q_Paged";
	else
		return $Q_Paged;
}

function GOTMLS_get_quarantine($only = false) {
	global $wpdb, $post;
	if (is_numeric($only))
		return get_post($only, ARRAY_A);
	elseif ($only === true)
		return $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private'");
	else
		$args = array("orderby" => 'date', "post_type" => 'GOTMLS_quarantine', "post_status" => array('private'));
	if (isset($_REQUEST["post_status"]))
		$args["post_status"] = $_REQUEST["post_status"];
	if (isset($_REQUEST["paged"]) && is_numeric($_REQUEST["paged"]))
		$args["paged"] = (INT) $_REQUEST["paged"];
	if (isset($_REQUEST["posts_per_page"]) && is_numeric($_REQUEST["posts_per_page"]) && ($_REQUEST["posts_per_page"]))
		$args["posts_per_page"] = (INT) $_REQUEST["posts_per_page"];
	else
		$args["posts_per_page"] = 200;
	$my_query = new WP_Query($args);
	if ($my_query->have_posts()) {
		$Q_Page = '<form method="POST" action="'.admin_url('admin-ajax.php').'" target="GOTMLS_iFrame" id="GOTMLS_Form_clean" name="GOTMLS_Form_clean"><input type="hidden" id="GOTMLS_fixing" name="GOTMLS_fixing" value="1"><input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."581")).'"><input type="hidden" name="action" value="GOTMLS_fix"><p id="quarantine_buttons" style="display: none;"><input id="repair_button" type="submit" value="'.__("Restore selected files from quarantine records",'gotmls').'" class="button-primary" onclick="if (confirm(\''.__("Are you sure you want to overwrite the previously cleaned files with the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(1); loadIframe(\'File Restoration Results\'); } else return false;" /><input id="delete_button" type="submit" class="button-primary" value="'.__("Delete selected quarantine records",'gotmls').'" onclick="if (confirm(\''.__("Are you sure you want to permanently delete the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(2); loadIframe(\'File Deletion Results\'); } else return false;" /></p><p><b>'.__("The following items highlighted in yellow had been found to contain malicious code, they have been cleaned and the malicious contents have been removed. A record of the infection has been saved here in the Quarantine for your review and could help with any future investigations. The code is safe here and you do not need to do anything further with these files.",'gotmls').'</b></p>
		<p id="reclean_buttons" style="display: none;"><input id="reclean_button" type="submit" value="'.__("Re-clean re-infected files",'gotmls').'" class="button-primary" onclick="checkAllFiles(false); setvalAllFiles(1); loadIframe(\'Reinfected File Recleaning Results\');" /><b>'.__("The items highlighted in red have been found to be re-infected. The malicious code has returned and needs to be cleaned again.",'gotmls').'</b></p>
		<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3 style="margin: 8px 12px;">'.($my_query->post_count>1?'<input type="checkbox" onchange="checkAllFiles(this.checked); document.getElementById(\'quarantine_buttons\').style.display = \'block\';"> '.sprintf(__("Check all %d",'gotmls'),$my_query->post_count):"").__(" Items in Quarantine",'gotmls').'<span class="GOTMLS_date">'.__("Quarantined",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Infected",'gotmls').((isset($_REQUEST["GOTMLS_debug"]))?'</span><span class="GOTMLS_date">'.__("Date Modified",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Changed",'gotmls').'</span><span class="GOTMLS_date">'.__("File Size",'gotmls').'</span><span class="GOTMLS_date">'.__("Threat Found",'gotmls'):"").'</span></h3>';
		$root_path = implode(GOTMLS_slash(), array_slice(GOTMLS_explode_dir(__FILE__), 0, (2 + intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"])) * -1));
		while ($my_query->have_posts()) {
			$my_query->the_post();
			$gif = 'blocked.gif';
			$threat = 'potential';
			$action = $post->ID.'" id="check_'.$post->ID.'" onchange="document.getElementById(\'quarantine_buttons\').style.display = \'block\';';
			$link = GOTMLS_error_link(__("The current/live file is missing or deleted",'gotmls'), $post->ID, $threat);
			$fa = GOTMLS_threats_found_meta(GOTMLS_object_to_array($post));
			if (is_file($post->post_title)) {
				GOTMLS_scanfile($post->post_title);
				if (count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$gif = 'threat.gif" onload="document.getElementById(\'reclean_buttons\').style.display = \'block\';';
					$threat = 'known';
					$action = GOTMLS_encode(realpath($post->post_title)).'" id="ilist_'.$post->ID.'" checked="true';
				}
				$link = GOTMLS_error_link(__("View current/live version",'gotmls'), $post->post_title, $threat);
			} elseif (is_array($postdb = explode(":", $post->post_title.":")) && count($postdb) > 3 && is_numeric($postdb[1])) {
				if ("options" == substr($postdb[0], -7)) {
					if ($opt_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_id` = %s",(INT) $postdb[1]), ARRAY_A))
						$link = GOTMLS_error_link(__("View Option Record: ",'gotmls').((INT) $postdb[1]), ((INT) $postdb[1]).'.1', $threat);
					elseif ($opt_row = $wpdb->get_row($SQL = $wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE %s", trim($postdb[2], '"')), ARRAY_A))
						$link = GOTMLS_error_link(__("View Option Record: ",'gotmls').htmlspecialchars($postdb[2]), $opt_row["option_id"].'.1', $threat);
					else
						$link = GOTMLS_error_link(__("View Quarantine Record",'gotmls'), $post->ID, $threat);
				} else {
					$link = '<a target="_blank" href="';
					if ("revision" == $postdb[0])
						$link .= admin_url('revision.php?revision='.rawurlencode($postdb[1]))."\" title=\"View this revision";
					else
						$link .= admin_url('post.php?action=edit&post='.rawurlencode((INT) $postdb[1]))."\" title=\"View current ".GOTMLS_htmlspecialchars($postdb[0]);
					$link .= "\" id=\"list_edit_".((INT) $postdb[1])."\" class=\"GOTMLS_plugin $threat\">";
				}
			}
			$Q_Page .= '
			<li id="GOTMLS_quarantine_'.((INT) $post->ID).'" class="GOTMLS_quarantine_item" onmouseover="this.style.fontWeight=\'bold\';" onmouseout="this.style.fontWeight=\'normal\';"><span class="GOTMLS_date">'.GOTMLS_error_link(__("View Quarantine Record",'gotmls'), $post->ID, $threat).$post->post_date.'</a></span><span title="modified: '.GOTMLS_htmlspecialchars($post->post_modified).'" class="GOTMLS_date">'.GOTMLS_htmlspecialchars($post->post_modified_gmt).((isset($_REQUEST["GOTMLS_debug"]) && is_file($post->post_title))?'</span><span class="GOTMLS_date">'.gmdate("Y-m-d H:i:s", filemtime($post->post_title)).'</span><span class="GOTMLS_date">'.gmdate("Y-m-d H:i:s", filectime($post->post_title)).'</span><span class="GOTMLS_date">('.filesize($post->post_title).' bytes)</span><span class="GOTMLS_date">( '.$fa.')':"").'</span><input type="checkbox" name="GOTMLS_fix[]" value="'.$action.'" /><img src="'.GOTMLS_images_path.$gif.'" height=16 width=16 alt="Q">'.$link.GOTMLS_htmlspecialchars(str_replace($root_path, "...", $post->post_title))."</a></li>\n";
		}
		$Q_Page = GOTMLS_get_pagination($my_query->max_num_pages, "$Q_Page\n</ul>\n</form>");
	} else
		$Q_Page = '<h3>'.__("No Items in Quarantine",'gotmls').'</h3>';
	wp_reset_query();
	return $Q_Page;
}

function GOTMLS_box($bTitle, $bContents, $bType = "postbox") {
	$md5 = md5($bTitle);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["$bType"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["$bType"]))
		$GLOBALS["GOTMLS"]["tmp"]["$bType"]["$md5"] = "$bTitle";
	else
		$GLOBALS["GOTMLS"]["tmp"]["$bType"] = array("$md5"=>"$bTitle");
	return '
	<div id="box_'.$md5.'" class="'.$bType.'"><h3 title="Click to toggle" onclick="if (typeof '.$bType.'_showhide == \'function\'){'.$bType.'_showhide(\'inside_'.$md5.'\');}else{showhide(\'inside_'.$md5.'\');}" style="cursor: pointer;" class="hndle"><span id="title_'.$md5.'">'.$bTitle.'</span></h3>
		<div id="inside_'.$md5.'" class="inside">
'.$bContents.'
		</div>
	</div>';
}

function GOTMLS_threats_ver($threats_name) {
	foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"] as $threat_level => $Threats)
		if (is_array($Threats) && isset($Threats["$threats_name"][0]) && GOTMLS_strlen($Threats["$threats_name"][0]) == 5)
			return $Threats["$threats_name"][0];
	return $threats_name;
}

function GOTMLS_threats_found_meta($Q_post = array()) {
	global $wpdb, $table_prefix;
	$gt = ">"; // This local variable never changes
	$lt = "<"; // This local variable never changes
	$SQL = "SELECT `meta_value` AS `Threat`, COUNT(*) AS `Found` FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = 'GOTMLS_threats_found'";
	if (isset($Q_post["ID"]) && is_numeric($pID = $Q_post["ID"]) && ($pID > 0))
		$SQL = $wpdb->prepare("$SQL AND post_id = %s", (INT) $pID);
	else
		$pID = 0;
	$my_query = $wpdb->get_results("$SQL GROUP BY `meta_value`", ARRAY_A);
	$fa = "";
	if (is_array($my_query) && count($my_query)) {
		$f = 1;
		foreach ($my_query as $rec) {
			if (isset($rec["Threat"]) && is_string($rec["Threat"]) && is_array($Threat = @GOTMLS_uckserialize($rec["Threat"])) && isset($Threat["DefVer"]) && isset($Threat["SubPos"])) {
				$ends = explode("-", $Threat["SubPos"]."--", 3);
				if (GOTMLS_strlen($ends[0]) > 0 && GOTMLS_strlen($ends[1]) > 0 && is_numeric($ends[1]) && is_numeric($ends[0])) {
					if ($ends[1] < $ends[0])
						$ends = array_reverse($ends);
					$fa .= $lt.'a title="'.GOTMLS_htmlspecialchars($Threat["DefVer"]).'" href="javascript:select_text_range(\'ta_file\', '.$ends[0].', '.$ends[1].');"'.$gt.'['.$f++."]$lt/a$gt ";
				}
			}
		}
	} else {
		if (isset($Q_post["post_excerpt"]) && GOTMLS_strlen($Q_post["post_excerpt"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"] = @GOTMLS_uckserialize(GOTMLS_decode($Q_post["post_excerpt"])))) {
			$f = 1;
			foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
				$ends = explode("-", "$threats_found--", 3);
				if (GOTMLS_strlen($ends[0]) > 0 && GOTMLS_strlen($ends[1]) > 0 && is_numeric($ends[1]) && is_numeric($ends[0])) {
					if ($ends[1] < $ends[0])
						$ends = array_reverse($ends);
					$fa .= $lt.'a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$ends[0].', '.$ends[1].');"'.$gt.'['.$f++."]$lt/a$gt ";
					if (function_exists("add_post_meta"))
						add_post_meta($pID, 'GOTMLS_threats_found', array("SubPos" => $ends[0]."-".$ends[1], "DefVer" => GOTMLS_threats_ver($threats_name)));
				} else {
					if (is_numeric($threats_found)) {
						$threats_found = $threats_name;
						$threats_name = $f;
					}
					$fpos = 0;
					$flen = 0;
					$potential_threat = GOTMLS_convert_r($threats_found);
					while (($fpos = strpos($GLOBALS["GOTMLS"]["tmp"]["file_contents"], ($potential_threat), $flen + $fpos)) !== false) {
						$flen = GOTMLS_strlen($potential_threat);
						$fa .= $lt.'a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.($fpos).', '.($fpos + $flen).');"'.$gt.'['.$f++."]$lt/a$gt ";
						if (function_exists("add_post_meta"))
							add_post_meta($pID, 'GOTMLS_threats_found', serialize(array("SubPos" => $fpos."-".($fpos + $flen), "DefVer" => GOTMLS_threats_ver($threats_name))));
					}
				}
			}
		} else
			$fa = GOTMLS_strlen($Q_post["post_excerpt"])."No Threats Found ";
	}
	return $fa;
}

function GOTMLS_view_details($Q_post, $pretext = "") {
	$title = __("View Details:",'gotmls');
	$clean_file = GOTMLS_htmlentities($Q_post["post_title"]);
	$encoded_file_contents = GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["encoding"])) {
		$en = $GLOBALS["GOTMLS"]["tmp"]["encoding"];
		@header("Content-type: text/html; charset=$en");
	} else
		$en = "Unknown";
	$fa = GOTMLS_threats_found_meta($Q_post);
	die(GOTMLS_html_tags(array(
		"html" => array(
			"head" => array(
				"title" => "$title $clean_file",
				"script" => GOTMLS_js_text_range()
				), 
			"body" => array(
				"table" => array(
					"tr" => array(
						"td" => "$pretext".
							GOTMLS_html_tags(array(
								"div" => array(
									"b" => "$title", 
									"br id='encoding' /" => "encoding: $en",
									"br id='size' /" => "size: ".GOTMLS_strlen("$encoded_file_contents")." Bytes",
									"br id='modified' /" => 'modified:'.$Q_post["post_modified"],
									"br id='changed' /" => 'changed:'.$Q_post["post_modified_gmt"],
									"br id='quarantined' /" => 'quarantined:'.$Q_post["post_date"]
									)
								), array(
									'div' => 'id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"'
								)
							).
							GOTMLS_html_tags(array(
								"div" => GOTMLS_html_tags(array('span' => $title), array('span' => 'onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';"'))."( $fa)"
								), array(
									'div' => 'style="overflow: auto;"'
								)
							)
						),
						GOTMLS_html_tags(array(
							"tr" => array(
								"td" => array(
									"textarea" => GOTMLS_htmlentities("$encoded_file_contents")
									)
								)
							), array(
								'td' => 'style="height: 100%; padding: 5px 5px 0 0;"',
								'textarea' => 'id="ta_file" style="width: 100%; height: 100%"'
							)
						)
					)
				)
			)
		), array(
			'script' => 'type="text/javascript"',
			'table' => 'style="top: 0px; left: 0px; width: 100%; height: 100%; position: absolute;"',
			'td' => 'style="width: 100%"'
		)
	));
}

function GOTMLS_js_text_range($posttext = "") {
	return '
function select_text_range(ta_id, start, end) {
	var textBox = document.getElementById(ta_id);
	var scrolledText = "";
	scrolledText = textBox.value.substring(0, end);
	textBox.focus();
	if (textBox.setSelectionRange) {
		scrolledText = textBox.value.substring(end);
		textBox.value = textBox.value.substring(0, end);
		textBox.scrollTop = textBox.scrollHeight;
		textBox.value = textBox.value + scrolledText;
		textBox.setSelectionRange(start, end);
	} else if (textBox.createTextRange) {
		var range = textBox.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end);
		range.select();
	} else
		alert("The highlighting function does not work in your browser");
}
if (typeof window.parent.showhide === "function") 
	window.parent.showhide("GOTMLS_iFrame", true);
'.$posttext;
}

if ((isset($_SERVER["DOCUMENT_ROOT"]) && ($SCRIPT_FILE = str_replace($_SERVER["DOCUMENT_ROOT"], "", (isset($_SERVER["SCRIPT_FILENAME"])?$_SERVER["SCRIPT_FILENAME"]:(isset($_SERVER["SCRIPT_NAME"])?$_SERVER["SCRIPT_NAME"]:"")))) && GOTMLS_strlen($SCRIPT_FILE) > GOTMLS_strlen("/".basename(__FILE__)) && substr(__FILE__, -1 * GOTMLS_strlen($SCRIPT_FILE)) == substr($SCRIPT_FILE, -1 * GOTMLS_strlen(__FILE__)))) {
	if (isset($_REQUEST["page"]) && str_replace('-', '_', $_REQUEST["page"]) == "GOTMLS_View_Quarantine" && isset($_REQUEST["GOTMLS_mt"]) && GOTMLS_strlen($GOTMLS_nonce = $_REQUEST["GOTMLS_mt"]) == 32 && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["context"]) && ($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]["context"] == GOTMLS_update_home)) {
		try {
			$wpdb->prefix = $table_prefix;
			GOTMLS_define("GOTMLS_Loading_LANGUAGE", __("Loading, Please Wait ...",'gotmls'));
			GOTMLS_define("GOTMLS_position_msg", __("Default position",'gotmls'));
			if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"])) {
				$my_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'GOTMLS_quarantine' AND `ID` = %s", (INT) $_REQUEST["id"]), ARRAY_A);
				if (is_array($my_query) && isset($my_query[0]["post_type"]) && strtolower($my_query[0]["post_type"]) == "gotmls_quarantine") {
					GOTMLS_load_contents(GOTMLS_decode($my_query[0]["post_content"]));
					GOTMLS_view_details($my_query[0], '<form style="margin: 0;" method="post" action="?GOTMLS_mt='.$GOTMLS_nonce.'&page=GOTMLS_View_Quarantine" onsubmit="return confirm(\''.GOTMLS_strip4java(__("Are you sure you want to restore this record from the quarantine?",'gotmls')).'\');"><input type="hidden" name="id[]" value="'.$my_query[0]["ID"].'"><input type="submit" value="Restore from Quarantine" style="display: none; background-color: #0C0; float: right;"></form>');
				} else
					die('<h3>Item NOT Found in Quarantine</h3>');
			} else {
				if (!isset($_REQUEST["not_in"]))
					$_REQUEST["not_in"] = "trash";
				$GLOBALS["GOTMLS"]["Quarantine"]["SQL"] = $wpdb->prepare("FROM `{$wpdb->prefix}posts` WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` != %s ORDER BY `post_date_gmt` DESC", $_REQUEST["not_in"]);
				$GLOBALS["GOTMLS"]["Quarantine"]["Count"] = $wpdb->get_var("SELECT COUNT(*) ".$GLOBALS["GOTMLS"]["Quarantine"]["SQL"]);
				if (isset($_REQUEST["posts_per_page"]) && is_numeric($_REQUEST["posts_per_page"]) && ($_REQUEST["posts_per_page"]))
					$GLOBALS["GOTMLS"]["Quarantine"]["posts_per_page"] = (INT) $_REQUEST["posts_per_page"];
				else
					$GLOBALS["GOTMLS"]["Quarantine"]["posts_per_page"] = 200;
				$paged = GOTMLS_get_pagination(ceil($GLOBALS["GOTMLS"]["Quarantine"]["Count"] / $GLOBALS["GOTMLS"]["Quarantine"]["posts_per_page"]));
				$GLOBALS["GOTMLS"]["Quarantine"]["SQL"] .= $wpdb->prepare(" LIMIT %d,%d", (INT) (($GLOBALS["GOTMLS"]["Quarantine"]["paged"] - 1) * $GLOBALS["GOTMLS"]["Quarantine"]["posts_per_page"]), (INT) $GLOBALS["GOTMLS"]["Quarantine"]["posts_per_page"]);
				$my_query = $wpdb->get_results("SELECT * ".$GLOBALS["GOTMLS"]["Quarantine"]["SQL"], ARRAY_A);
				if (is_array($my_query) && count($my_query)) {
					$Q_Page = $paged.'<form method="POST" action="?page=GOTMLS_View_Quarantine" id="GOTMLS_Form_clean" name="GOTMLS_Form_clean"><input type="hidden" name="GOTMLS_mt" value="'.$GOTMLS_nonce.'"><p id="quarantine_buttons" style="display: none;"><input id="repair_button" type="submit" value="Restore selected files" class="button-primary" style="background-color: #0C0;" onclick="return confirm(\'Are you sure you want to overwrite the previously cleaned files with the selected files in the Quarantine?\');" /></p><p><b>The following items have been found to contain malicious code, they have been cleaned, and the original infected file contents have been saved here in the Quarantine. The code is safe here and you do not need to do anything further with these files.</b></p>
					<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3 style="margin: 8px 12px;">'.(count($my_query)>1?'<input type="checkbox" onchange="checkAllFiles(this.checked); document.getElementById(\'quarantine_buttons\').style.display = \'block\';"> '.sprintf(__("Check all %d",'gotmls'),count($my_query)):"").__(" Items in Quarantine",'gotmls').'<span class="GOTMLS_date">'.__("Quarantined",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Infected",'gotmls').'</span></h3>';
					$root_path = implode(GOTMLS_slash(), array_slice(GOTMLS_explode_dir(__FILE__), 0, (2 + intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"])) * -1));
					foreach ($my_query as $post_a) {
						$restored = "";
						$image = "threat";
						$extra = "";
						if (isset($_REQUEST["id"]) && is_array($_REQUEST["id"]) && in_array($post_a["ID"], $_REQUEST["id"])) {
							if (GOTMLS_save_contents($post_a["post_title"], $apost_content = GOTMLS_decode($post_a["post_content"]))) {
								$post_a["post_date_gmt"] = gmdate("Y-m-d H:i:s");
								if ($wpdb->query($wpdb->prepare("UPDATE `{$wpdb->prefix}posts` SET `post_status` = 'pending', `post_name` = 'whitelist', `post_date_gmt` = '".$post_a["post_date_gmt"]."' WHERE `post_type` = 'GOTMLS_quarantine' AND `ID` = %s", (INT) $post_a["ID"])))
									$post_a["post_status"] = 'pending';
							}
						}
						if ($post_a["post_status"] == 'pending') {
							$restored = " read-only disabled";
							$image = "checked";
							$extra = " Whitelisted ".$post_a["post_date_gmt"];
						}
						if ($post_a["post_status"] == 'trash') {
							$restored = " read-only disabled";
							$image = "blocked";
							$extra = " Deleted ".$post_a["post_date_gmt"];
						}
						$Q_Page .= '
						<li id="GOTMLS_quarantine_'.$post_a["ID"].'" class="GOTMLS_quarantine_item"><span class="GOTMLS_date">'.$post_a["post_date"].'</span><span class="GOTMLS_date" title="modified: '.$post_a["post_modified"].'">'.$post_a["post_modified_gmt"].'</span><input'.$restored.' type="checkbox" name="id[]" value="'.$post_a["ID"].'" id="GOTMLS_quarantine_check_'.$post_a["ID"].'" onchange="document.getElementById(\'quarantine_buttons\').style.display = \'block\';" /><img src="'.$image.'.gif" height=16 width=16 alt="Q"><a class="GOTMLS_plugin '.$restored.($post_a["post_status"]=='pending'?'" title="View Whitelisted File':($post_a["post_status"]=='trash'?' potential" title="View Deleted File':' known" title="View Quarantined File')).'" target="_blank" href="?page=GOTMLS_View_Quarantine&id='.$post_a["ID"].'&GOTMLS_mt='.$GOTMLS_nonce.'">'.str_replace($root_path, "...", $post_a["post_title"])."$extra</a></li>\n";
					}
					$Q_Page .= "\n</ul>\n</form>$paged";
				} else
					$Q_Page = '<h3>'.__("No Items in Quarantine",'gotmls').'</h3>';
				die(GOTMLS_html_tags(array("html" => array("body" => GOTMLS_get_header().GOTMLS_box(__("View Quarantine",'gotmls'), $Q_Page)))));
			}
		} catch (Exception $e) {
			die('Caught exception: '.GOTMLS_htmlspecialchars($e->getMessage())."\n");
		}
	} else {
		header("Content-type: image/gif");
		die(GOTMLS_decode('R=lGODlhEAAQAIQYAAAAAAIAAAMAAAgAAAkAAAsAAAwAAHcAAHgAAKYAAK4AAK8AALUAALYAAMcAAMgAAM=AANkAANoAANwAAN=AAP4AAP8AANTU1P_______________________________yH5BAEKAB8ALAAAAAAQABAAAAWB4HddwGia5SWSAVBZMAwIKQkg7xtXCJAKCEukURgRIJbKQWCrSGw-QAJWiS4sjFHUAYNUFD7LpKilvC6DiaVUqZxipuQIFpfXSWLC5UWpFdQ-V=gWD1EjDBYLUToJUT4XEVUlAQddAyMGDRIWS1o3SW=6PI9aNKJJMykrNSckIx8hADs2'));

	}
}
$GOTMLS_image_alt = array("wait"=>"...", "checked"=>"&#x2714;", "blocked"=>"X", "question"=>"?", "threat"=>"!");
$GOTMLS_dir_at_depth = array();
$GOTMLS_dirs_at_depth = array();
$GLOBAL_STRING = array("REQUEST" => "&","SERVER" => "&","FILES" => "&");
if (isset($_GET) && is_array($_GET))
	foreach ($_GET as $req => $val)
		$GLOBAL_STRING["REQUEST"] .= "$req=".(is_array($val)?print_r($val,1):$val)."&";
if (isset($_POST) && is_array($_POST))
	foreach ($_POST as $req => $val)
		$GLOBAL_STRING["REQUEST"] .= "$req=".(is_array($val)?print_r($val,1):$val)."&";
if (isset($_SERVER) && is_array($_SERVER))
	foreach ($_SERVER as $req => $val)
		$GLOBAL_STRING["SERVER"] .= "$req=".(is_array($val)?print_r($val,1):$val)."&";
if (isset($_FILES) && is_array($_FILES))
	foreach ($_FILES as $req => $fila)
		foreach (array("tmp_name","name") as $val)
			if (isset($fila["$val"]))
				$GLOBAL_STRING["FILES"] .= "$req.$val=".(is_array($fila["$val"])?print_r($fila["$val"],1):$fila["$val"])."&";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]) && array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"])))
	$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"] = array(
		"RevSlider"=>array("CCIGG", "Revolution Slider Exploit Protection", "This protection is automatically activated because of the widespread attacks on WordPress that have affected so many sites. It is still recommended that you make sure to upgrade any older versions of the Revolution Slider plugin, especially those included in themes that will not update automatically. Even if you don't think you have Revolution Slider on your site it doen't hurt to have this protection enabled.", "SERVER", '/\/admin-ajax\.php/i', "REQUEST", '/\&img=[^\&]*(?<!\.'.implode(')(?<!\.', array_slice($GLOBALS["GOTMLS"]["tmp"]["skip_ext"], 0, 10)).')\&/i'),
		"Traversal"=>array("CCIGG", "Directory Traversal Protection", "This protection is automatically activated because this type of attack is quite common. This protection can prevent hackers from accessing secure files in parent directories (or user's folders outside the site_root).", "REQUEST", '/[\=\/](\.\.|etc)\//'),
		"UploadPHP"=>array("CCIGG", "Upload PHP File Protection", "This protection is automatically activated because this type of attack is extremely dangerous. This protection can prevent hackers from uploading malicious code via web scripts.", "FILES", '/name=[^\&]*\.php\&/'));
foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"] as $TP => $VA) {
	$V = 3;
	if (is_array($VA) && count($VA) > $V && is_array($VA[$V])) {
		foreach ($VA[$V] as $reg => $arr) {
			$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V++] = $arr;
			$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V++] = $reg;
		}
	}
	if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"])) {
		$GLOBALS["GOTMLS"]["detected_attacks"] = "&attack[]=FW_$TP";
		for ($V = 4; isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V]); $V+=2)
			if (!isset($GLOBAL_STRING[$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]]))
				die($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]." [$V] not in ".GOTMLS_html_tags(array('pre' => GOTMLS_htmlspecialchars(print_r($GLOBAL_STRING,1)))));
			elseif (!preg_match($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V], $GLOBAL_STRING[$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]], $matches))
				$GLOBALS["GOTMLS"]["detected_attacks"] = "";
		if ($GLOBALS["GOTMLS"]["detected_attacks"])
			include(dirname(dirname(__FILE__))."/safe-load/index.php");
	}
}
$GLOBALS["GOTMLS"]["detected_attacks"] = "";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) && count($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) == 4))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"] = $GLOBALS["GOTMLS"]["tmp"]["default"]["msg_position"];
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"] = 2;
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"] = -1;
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"])))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"] = $GLOBALS["GOTMLS"]["tmp"]["skip_ext"];
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"] = "";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"])))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"] = array();
$GOTMLS_total_percent = 0;

function GOTMLS_admin_notices() {
    if (!is_admin())
		return;
   	if (is_file(dirname(dirname(dirname(__FILE__)))."/yuzo-related-post/yuzo_related_post.php"))
		echo GOTMLS_error_div('It looks like you have <b>"Related Post" plugin By <i>Lenin Zapata</i></b> installed on your site.<br />This plugin was removed from the WordPress Plugin Repository because it contained a major vulnerability that was responsible for a fairly widespread breach to many WordPress sites that had it installed.<br />It is recommended that it be deactivated and deleted until a fix is released that solves this problem.');
   	if (!function_exists("mb_detect_encoding"))
		echo GOTMLS_error_div('It looks like you don\'t have <b>"mbstring" functions</b> enabled on your server.<br />This Anti-Malware plugin requires Multibyte String compatibility for best results. Please make sure that php-mbstring is installed and configured for the version of PHP running on your server.');
   	if ($GLOBALS["GOTMLS"]["tmp"]["HeadersError"])
		echo $GLOBALS["GOTMLS"]["tmp"]["HeadersError"];
}
add_action("admin_notices", "GOTMLS_admin_notices");

function GOTMLS_array_recurse($array1, $array2) {
	foreach ($array2 as $key => $value) {
		if (!isset($array1[$key]) || (isset($array1[$key]) && !is_array($array1[$key])))
			$array1[$key] = array();
		if (is_array($value))
			$value = GOTMLS_array_recurse($array1[$key], $value);
		$array1[$key] = $value;
	}
	return $array1;
}

function GOTMLS_array_replace($array1, $array2) {
	foreach ($array2 as $key => $value)
		$array1[$key] = $value;
	return $array1;
}

function GOTMLS_array_replace_recursive($array1 = array()) {
	$args = func_get_args();
	$array1 = $args[0];
	if (!is_array($array1))
		$array1 = array();
	for ($i = 1; $i < count($args); $i++)
		if (is_array($args[$i]))
			$array1 = GOTMLS_array_recurse($array1, $args[$i]);
	return $array1;
}

function GOTMLS_scanlog_title() {
	$units = array("seconds"=>60,"minutes"=>60,"hours"=>24,"days"=>365,"years"=>10);
	if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["type"]) && GOTMLS_strlen($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["type"]))
		$GLOBALS["GOTMLS"]["scan"]["title"] = GOTMLS_sanitize($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["type"]);
	else
		$GLOBALS["GOTMLS"]["scan"]["title"] = "Unknown scan type";
	$scan_only = "";
	if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"])) {
		if (is_array($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"])) {
			if (count($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"]) == 1 && isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"][0]))
				$scan_only = "/".$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"][0];
		} else
			$scan_only = "/".$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["scan_only"];
	}
	if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["dir"]) && @is_dir($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["dir"]))
		$GLOBALS["GOTMLS"]["scan"]["title"] .= " of ".basename($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["dir"].$scan_only);
	elseif ($scan_only)
		$GLOBALS["GOTMLS"]["scan"]["title"] .= " of ".basename($scan_only);
	if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"])) {
		$ukeys = array_keys($units);
		$GLOBALS["GOTMLS"]["scan"]["title"] .= " on ".date("Y-m-d", $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]);
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"]) && ($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"] >= $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"])) {
			$time = ($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"] - $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]);
			for ($unit = $ukeys[0], $key=0; (isset($units[$ukeys[$key]]) && $key < (count($ukeys) - 1) && $time >= $units[$ukeys[$key]]); $unit = $ukeys[++$key])
				$time = floor($time/$units[$ukeys[$key]]);
			if (1 == $time)
				$unit = substr($unit, 0, -1);
			if ($time)
				$GLOBALS["GOTMLS"]["scan"]["title"] .= " ran for $time $unit";
		} else
			$GLOBALS["GOTMLS"]["scan"]["title"] .= " was not finished!";
	} else
		$GLOBALS["GOTMLS"]["scan"]["title"] .= " failed to started!";
	return $GLOBALS["GOTMLS"]["scan"]["title"];
}

function GOTMLS_load_scanlog($scanlog_key) {
	global $wpdb;
	if (GOTMLS_strlen($scanlog_key = preg_replace('/[^0-9a-f]++]i/', "", $scanlog_key)) != 32)
		$scanlog_key = preg_replace('/[^0-9a-f]++]i/', "", $GLOBALS["GOTMLS"]["tmp"]["mt"]);
	if ((GOTMLS_strlen($scanlog_key) == 32) && ($prs = $wpdb->get_results($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}posts` WHERE post_type = %s AND post_name = %s", 'gotmls_results', $scanlog_key), ARRAY_A))) {
		$GLOBALS["GOTMLS"]["scan"]["key"] = $scanlog_key;
		if (!(isset($prs[0]["post_content"]) && (GOTMLS_strlen($prs[0]["post_content"])) && is_array($GLOBALS["GOTMLS"]["scan"]["log"] = json_decode($prs[0]["post_content"], true))))
			$GLOBALS["GOTMLS"]["scan"]["log"] = array();
		if (!isset($GLOBALS["GOTMLS"]["scan"]["title"]) && !(isset($prs[0]["post_title"]) && (GOTMLS_strlen($GLOBALS["GOTMLS"]["scan"]["title"] = $prs[0]["post_title"])))) {
			GOTMLS_scanlog_title();
		}
		return $scanlog_key;
	}
	return false;
}

function GOTMLS_update_scanlog($scan_log, $status = "") {
	global $wpdb;
	if (is_array($scan_log)) {
		if (isset($GLOBALS["GOTMLS"]["scan"]["key"]) && GOTMLS_strlen($scanlog_key = preg_replace('/[^0-9a-f]++]i/', "", $GLOBALS["GOTMLS"]["scan"]["key"])) == 32) {
			$GLOBALS["GOTMLS"]["scan"]["log"] = GOTMLS_array_replace_recursive($GLOBALS["GOTMLS"]["scan"]["log"], $scan_log);
			$values = array("post_modified" => date("Y-m-d H:i:s", (int) $GLOBALS["GOTMLS"]["MT"]));
			$where = array("post_type" => 'gotmls_results', "post_name" => $scanlog_key);
		} else {
			$where = false;
			$values = array("post_modified" => date("Y-m-d H:i:s", (int) $GLOBALS["GOTMLS"]["MT"]), "post_date_gmt" => date("Y-m-d H:i:s", (int) $GLOBALS["GOTMLS"]["MT"]), "post_type" => 'gotmls_results', "post_parent" => 0);
			if (($prs = $wpdb->get_results($wpdb->prepare("SELECT ID FROM `{$wpdb->prefix}posts` WHERE post_type = %s ORDER BY post_date DESC LIMIT 1", 'gotmls_results'), ARRAY_A)) && isset($prs[0]["ID"]))
				$values["post_parent"] = $prs[0]["ID"];
			$GLOBALS["GOTMLS"]["scan"]["log"] = $scan_log;
		}
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["percent"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["percent"]) && ($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["percent"] >= 100))
			$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"] = time();
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"])) {
			$values["post_modified_gmt"] = date("Y-m-d H:i:s", (int) $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"]);
			if (!isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]))
				$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"] = $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"];
		}
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["type"]) && !isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]))
			$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"] = (int) $GLOBALS["GOTMLS"]["MT"];
		$values["post_content"] = json_encode($GLOBALS["GOTMLS"]["scan"]["log"]);
		$values["post_author"] = GOTMLS_get_current_user_id(0);
		$values["post_modified"] = date("Y-m-d H:i:s", (int) microtime(true));
		if (!(isset($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]) && is_array($GLOBALS["GOTMLS"]["scan"]["log"]["settings"])) && isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]))
			$GLOBALS["GOTMLS"]["scan"]["log"]["settings"] = $GLOBALS["GOTMLS"]["tmp"]["settings_array"];
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]) && ($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"] > 0)) {
			$values["post_date"] = date("Y-m-d H:i:s", (int) $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]);
			$values["post_title"] = GOTMLS_scanlog_title();
			if ($where)
				$scan_log["updated" . $wpdb->update($wpdb->posts, $values, $where)] = $where;
			else {
				if (GOTMLS_strlen($GLOBALS["GOTMLS"]["scan"]["key"] = preg_replace('/[^0-9a-f]++]i/', "", $GLOBALS["GOTMLS"]["tmp"]["mt"])) != 32)
					$GLOBALS["GOTMLS"]["scan"]["key"] = md5($GLOBALS["GOTMLS"]["MT"]);
				$values["post_name"] = $GLOBALS["GOTMLS"]["scan"]["key"];
				$scan_log["inserted"] = $wpdb->insert($wpdb->posts, $values);
			}
		}
	}
}

function GOTMLS_loaded() {
	if (headers_sent($filename, $linenum)) {
		if (!$filename)
			$filename = __("an unknown file",'gotmls');
		if (!is_numeric($linenum))
			$linenum = __("unknown",'gotmls');
		$GLOBALS["GOTMLS"]["tmp"]["HeadersError"] = GOTMLS_error_div(sprintf(__('<b>Headers already sent</b> in %1$s on line %2$s.<br />This is not a good sign, it may just be a poorly written plugin but Headers should not have been sent at this point.<br />Check the code in the above mentioned file to fix this problem.','gotmls'), $filename, $linenum));
	} elseif (isset($_GET["SESSION"]) && !session_id()) {
		@session_start();
	}
	if (session_id() && isset($_GET["SESSION"]) && $_GET["SESSION"] == "GOTMLS_debug" && ((isset($_GET["GOTMLS_debug"]) && "SESSION" == $_GET["GOTMLS_debug"]) || !isset($_SESSION["GOTMLS_debug"])))
		$_SESSION["GOTMLS_debug"] = array("GOTMLS_loaded" => microtime(true));
}
add_action("plugins_loaded", "GOTMLS_loaded");

if (!function_exists("add_action")) {
	GOTMLS_loaded();
//	GOTMLS_admin_notices();
}

function GOTMLS_get_ext($filename) {
	$nameparts = explode(".", ".$filename");
	return strtolower($nameparts[(count($nameparts)-1)]);
}

function GOTMLS_preg_match_all($threat_definition, $threat_name, $not_serialized = true) {
	if ($match = @preg_match_all($threat_definition, $GLOBALS["GOTMLS"]["tmp"]["file_contents"], $threats_found)) {
		$start = -1;
		if (!@preg_match_all($threat_definition, $GLOBALS["GOTMLS"]["tmp"]["new_contents"], $threat_found)) {
			$new_contents = $GLOBALS["GOTMLS"]["tmp"]["new_contents"];
			$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $GLOBALS["GOTMLS"]["tmp"]["file_contents"];
		} else
			$new_contents = false;
		foreach ($threats_found[0] as $find) {
			$potential_threat = GOTMLS_convert_r($find);
			$flen = GOTMLS_strlen($potential_threat);
			while (($start = strpos(GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"]), $potential_threat, $start+1)) !== false) {
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"]["$start-".($flen+$start)] = "$threat_name";
				if ($not_serialized)
					$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = str_replace($find, "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]);
				else
					$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = substr($GLOBALS["GOTMLS"]["tmp"]["new_contents"], 0, $start).str_repeat(" ", $flen).substr($GLOBALS["GOTMLS"]["tmp"]["new_contents"], $start + $flen);
			}
		}
		if ($not_serialized && ($new_contents !== false) && GOTMLS_strlen($new_contents) < GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["new_contents"]))
			$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $new_contents;
		return count($GLOBALS["GOTMLS"]["tmp"]["threats_found"]);
	} else 
		return $match;
}

function GOTMLS_preg_last_pcre_error() {
	$DC = array('PREG_NO_ERROR', 'PREG_INTERNAL_ERROR', 'PREG_BACKTRACK_LIMIT_ERROR', 'PREG_RECURSION_LIMIT_ERROR', 'PREG_BAD_UTF8_ERROR', 'PREG_BAD_UTF8_OFFSET_ERROR');
	if (function_exists("preg_last_error") && ($key = (INT) preg_last_error()) && isset($DC[$key]))
		return $DC[$key];
	else
		return "";
}

function GOTMLS_check_threat($check_threats, $file='UNKNOWN') {
	$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
	$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
	$filekey = md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"])."O".GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
	if (is_array($check_threats)) {
		$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, GOTMLS_strlen(ABSPATH))));
		if (substr($file, 0, GOTMLS_strlen(ABSPATH)) == ABSPATH && isset($check_threats[GOTMLS_wp_version]["$path"])) {
			if (($check_threats[GOTMLS_wp_version]["$path"] != $filekey) && ($source = GOTMLS_get_URL(GOTMLS_get_corefile_URL("$path", $check_threats[GOTMLS_wp_version]["$path"]))) && ($check_threats[GOTMLS_wp_version]["$path"] == md5($source)."O".GOTMLS_strlen($source))) {
				$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
				$len = GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
				if (GOTMLS_strlen($source) < $len)
					$len = GOTMLS_strlen($source);
				for ($start = 0, $end = 0; ($start == 0 || $end == 0) && $len > 0; $len--){
					if ($start == 0 && substr($source, 0, $len) == substr($GLOBALS["GOTMLS"]["tmp"]["file_contents"], 0, $len))
						$start = $len;
					if ($end == 0 && substr($source, -1 * $len) == substr($GLOBALS["GOTMLS"]["tmp"]["file_contents"], -1 * $len))
						$end = $len;
				}
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"]["$start-".(GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])-$end)] = "Core File Modified";
			}
		} else {
			foreach ($check_threats as $threat_name=>$threat_definitions) {
				$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
				if (is_array($threat_definitions) && count($threat_definitions) > 1 && GOTMLS_strlen($def_ver = array_shift($threat_definitions)) == 5 && (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && in_array($threat_name, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"])))) {
					while ($threat_definition = array_shift($threat_definitions)) {
						$found = GOTMLS_preg_match_all($threat_definition, $threat_name);
						if ($found===false && ($err = GOTMLS_preg_last_pcre_error())) {
							$GLOBALS["GOTMLS"]["tmp"]["file_scan"]["errors"]["$threat_definition"] = $err;
							$GLOBALS["GOTMLS"]["tmp"]["errors"]["$def_ver"]["$filekey"] = $err;
						}
					}
					if (isset($_SESSION["GOTMLS_debug"])) {
						$_SESSION["GOTMLS_debug"]["threat_name"] = "$threat_name";// ($def_ver)";
						$file_time = sprintf('%f', (microtime(true) - $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"]));
						if (isset($_GET["GOTMLS_debug"]) && is_numeric($_GET["GOTMLS_debug"]) && $file_time > $_GET["GOTMLS_debug"])
							echo GOTMLS_htmlspecialchars("\n//GOTMLS_debug $file_time $threat_name $file\n");
						if (isset($GLOBALS["GOTMLS"]["tmp"]["errors"]["$def_ver"]["$filekey"]))
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["errors"]["$filekey"] = $GLOBALS["GOTMLS"]["tmp"]["errors"]["$def_ver"]["$filekey"];
						if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"]))
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"] = sprintf('%f', $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"] + $file_time);
						else
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"] = $file_time;
						if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"]))
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"]++;
						else
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"] = 1;
						if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"]) || $file_time < $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"])
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"] = $file_time;
						if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"]) || $file_time > $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"])
							$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"] = $file_time;
					}
				}
			}
		}
	} elseif (GOTMLS_strlen($check_threats) && isset($_GET['eli']) && GOTMLS_verify_regex($check_threats)) {
		$found = GOTMLS_preg_match_all($check_threats, $check_threats);
		if ($found===false && ($err = GOTMLS_preg_last_pcre_error()))
			$GLOBALS["GOTMLS"]["tmp"]["errors"]["$check_threats"]["$filekey"] = $err;
	}
	if (isset($_SESSION["GOTMLS_debug"])) {
		$file_time = sprintf('%f', (microtime(true) - $GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"]));
		if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"]))
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"] = sprintf('%f', $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"] + $file_time);
		else
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"] = $file_time;
		if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"]))
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"]++;
		else
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"] = 1;
		if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"]) || $file_time < $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"])
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"] = $file_time;
		if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"]) || $file_time > $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"])
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"] = $file_time;
	}
	return count($GLOBALS["GOTMLS"]["tmp"]["threats_found"]);
}

function GOTMLS_verify_regex($RegExp) {
	if (preg_match('/^(\/|\#|\|).+\1[is]*$/', $RegExp))
		return $RegExp;
	else
		return "";
}

function GOTMLS_is_whitelisted($MD5Ofile, $file = "") {
	if (!(isset($GLOBALS["GOTMLS"]["tmp"]["whitelist"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["whitelist"]))) {
		if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]))
			$GLOBALS["GOTMLS"]["tmp"]["whitelist"] = array_flip($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]);
		else
			$GLOBALS["GOTMLS"]["tmp"]["whitelist"] = array();
		if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"])) {
			foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"] as $whitelist_file=>$non_threats) {
				if (is_array($non_threats) && count($non_threats) > 1) {
					if (isset($non_threats[0]))
						unset($non_threats[0]);
					$GLOBALS["GOTMLS"]["tmp"]["whitelist"] = array_merge($GLOBALS["GOTMLS"]["tmp"]["whitelist"], $non_threats);
				}
			}
		}
	}
	if (isset($GLOBALS["GOTMLS"]["tmp"]["whitelist"][$MD5Ofile]))
		return true;
	else
		return false;
}

function GOTMLS_scanfile($file) {
	global $wpdb;
	$gt = ">"; // This local variable never changes
	$lt = "<"; // This local variable never changes
	$GLOBALS["GOTMLS"]["tmp"]["debug_fix"] = "Scanning...";
	$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
	$found = false;
	$threat_link = "";
	$MD5O = "O";
	$className = "scanned";
	$real_file = realpath($file);
	$clean_file = GOTMLS_encode($real_file);
	$GLOBALS["GOTMLS"]["tmp"]["file_scan"] = array("start" => microtime(true), "name" => $real_file, "size" => 0, "errors" => array());
	if (is_file($real_file) && ($filesize = filesize($real_file)) && GOTMLS_load_contents(@file_get_contents($real_file))) {
		$MD5O = md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'O';
		$GLOBALS["GOTMLS"]["tmp"]["file_scan"]["size"] = $filesize;
		if (GOTMLS_is_whitelisted($MD5O.$filesize))
			return GOTMLS_return_threat($className, "checked.gif?$className", $file, $threat_link);
		$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $GLOBALS["GOTMLS"]["tmp"]["file_contents"];
		if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check_custom"]) && GOTMLS_strlen($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check_custom"]) && isset($_GET['eli']) && GOTMLS_verify_regex($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check_custom"]) && ($found = GOTMLS_check_threat($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check_custom"])))
			$className = "known";
		else {
			$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, GOTMLS_strlen(ABSPATH))));
			if (isset($_SESSION["GOTMLS_debug"])) {
				$_SESSION["GOTMLS_debug"]["file"] = $file;
				$_SESSION["GOTMLS_debug"]["last"]["total"] = microtime(true);
			}
			if (isset($GLOBALS["GOTMLS"]["tmp"]["threat_levels"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threat_levels"])) {
				foreach ($GLOBALS["GOTMLS"]["tmp"]["threat_levels"] as $threat_level) {
					if ("db_scan" != $threat_level) {
						if (isset($_SESSION["GOTMLS_debug"])) {
							$_SESSION["GOTMLS_debug"]["threat_level"] = $threat_level;
							$_SESSION["GOTMLS_debug"]["last"]["threat_level"] = microtime(true);
						}
						if (in_array($threat_level, $GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check"]) && !$found && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level]) && ($threat_level != "wp_core" || (substr($file, 0, GOTMLS_strlen(ABSPATH)) == ABSPATH && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"]))) && (!isset($GLOBALS["GOTMLS"]["tmp"]["threat_files"]["$threat_level"]) || (substr($file."e", (-1 * GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["threat_files"][$threat_level]."e"))) == $GLOBALS["GOTMLS"]["tmp"]["threat_files"][$threat_level]."e")) && ($found = GOTMLS_check_threat($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level],$file)))
							$className = $threat_level;
					}
				}
			}
			if (isset($_SESSION["GOTMLS_debug"])) {
				$file_time = round(microtime(true) - $_SESSION["GOTMLS_debug"]["last"]["total"], 5);
				if (isset($_SESSION["GOTMLS_debug"]["total"]["total"]))
					$_SESSION["GOTMLS_debug"]["total"]["total"] += $file_time;
				else
					$_SESSION["GOTMLS_debug"]["total"]["total"] = $file_time;
				if (isset($_SESSION["GOTMLS_debug"]["total"]["count"]))
					$_SESSION["GOTMLS_debug"]["total"]["count"] ++;
				else
					$_SESSION["GOTMLS_debug"]["total"]["count"] = 1;
				if (!isset($_SESSION["GOTMLS_debug"]["total"]["least"]) || $file_time < $_SESSION["GOTMLS_debug"]["total"]["least"])
					$_SESSION["GOTMLS_debug"]["total"]["least"] = $file_time;
				if (!isset($_SESSION["GOTMLS_debug"]["total"]["most"]) || $file_time > $_SESSION["GOTMLS_debug"]["total"]["most"])
					$_SESSION["GOTMLS_debug"]["total"]["most"] = $file_time;
			}
		}
	} else {
		GOTMLS_load_contents((is_file($real_file)?(is_readable($real_file)?(filesize($real_file)?__("Failed to read file contents!",'gotmls'):__("Empty file!",'gotmls')):(isset($_GET["eli"])?(@chmod($real_file, GOTMLS_CHMOD_FILE)?__("Fixed file permissions! (try again)",'gotmls'):__("File permissions read-only!",'gotmls')):__("File not readable!",'gotmls'))):__("File does not exist!",'gotmls')));
		$className = "errors";
	}
	if (count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
		$threat_link = $lt.'a target="GOTMLS_iFrame" href="'.GOTMLS_admin_url('GOTMLS_scan', GOTMLS_set_nonce(__FUNCTION__."1275").'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$clean_file).'" id="list_'.$clean_file.'" onclick="loadIframe(\''.str_replace("\"", "&quot;", $lt.'div style="float: left; white-space: nowrap;"'.$gt.GOTMLS_strip4java(__("Examine File",'gotmls')).' ... '.$lt.'/div'.$gt.$lt.'div style="overflow: hidden; position: relative; height: 20px;"'.$gt.$lt.'div style="position: absolute; right: 0px; text-align: right; width: 9000px;"'.$gt.GOTMLS_htmlspecialchars(GOTMLS_strip4java($file), ENT_NOQUOTES))."$lt/div$gt$lt/div$gt');\" class=\"GOTMLS_plugin\"$gt";
		if ($className == "errors") {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="errors";
			$threat_link = GOTMLS_error_link($GLOBALS["GOTMLS"]["tmp"]["file_contents"], $file);
			$imageFile = "/blocked";
		} elseif ($className != "potential") {
			if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && in_array($clean_file, $_REQUEST["GOTMLS_fix"])) {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="GOTMLS_fix";
				if (GOTMLS_get_nonce()) {
					if ($className == "timthumb") {
						if (($source = GOTMLS_get_URL("https://storage.googleapis.com/google-code-archive-downloads/v2/code.google.com/timthumb/timthumb.php")) && GOTMLS_strlen($source) > 500)
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
						else
							$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = "";
					} elseif ($className == 'wp_core') {
						$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, GOTMLS_strlen(ABSPATH))));
						if (substr($file, 0, GOTMLS_strlen(ABSPATH)) == ABSPATH && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"]) && ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"] != $MD5O.GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])) && ($source = GOTMLS_get_URL(GOTMLS_get_corefile_URL("$path", $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"]))) && ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"] == md5($source)."O".GOTMLS_strlen($source)))
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
						else
							$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = "";
					} else {
						$GOTMLS_no_contents = trim(preg_replace('/\/\*.*?\*\/\s*/s', "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]));
						$GOTMLS_no_contents = trim(preg_replace('/\n\s*\/\/.*/', "", $GOTMLS_no_contents));
						$GOTMLS_no_contents = trim(preg_replace('/'.$lt.'\?(php)?\s*(\?'.$gt.'|$)/is', "", $GOTMLS_no_contents));
						if (GOTMLS_strlen($GOTMLS_no_contents))
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = trim(preg_replace('/'.$lt.'\?(php)?\s*(\?'.$gt.'|$)/is', "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]));
						else
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = "";
					}
					if (GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]) > 0 && (($Q_post = GOTMLS_write_quarantine($file, $className)) !== false) && ((GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["new_contents"])==0 && isset($_GET["eli"]) && ($_GET["eli"] == "delete") && @unlink($file)) || (($Write_File = GOTMLS_save_contents($file, $GLOBALS["GOTMLS"]["tmp"]["new_contents"])) !== false))) {
						echo __("Success!",'gotmls');
						return "/*--{$gt}*"."/\nfixedFile('$clean_file');\n/*{$lt}!--*"."/";
					} else {
						echo __("Failed:",'gotmls').' '.(GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])?((is_writable(dirname($file)) && is_writable($file))?(($Q_post===false)?__("failed to quarantine!",'gotmls')." (".GOTMLS_htmlspecialchars($wpdb->last_error).")":((isset($Write_File)&&$Write_File)?"Q=$Q_post: ".__("reason unknown!",'gotmls'):"Q=$Q_post: ".__("failed to write!",'gotmls'))):__("file not writable!",'gotmls')):__("no file contents!",'gotmls'));
						if (isset($_GET["eli"]))
							echo get_current_user().$lt."br$gt{$lt}pre$gt file_stat".print_r(stat($file), true);
						return "/*--{$gt}*"."/\nfailedFile('$clean_file');\n/*{$lt}!--*"."/";
					}
				} else {
					echo GOTMLS_Invalid_Nonce(__("Failed: ",'gotmls'));
					return "/*--{$gt}*"."/\nfailedFile('$clean_file');\n/*{$lt}!--*"."/";
				}
			}
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]=isset($_POST["GOTMLS_fix"])?"GOTMLS_fix=".GOTMLS_htmlspecialchars(preg_replace('/[\r\n]+/', ' ', print_r($_POST["GOTMLS_fix"],1))):"!potential";
			$threat_link = $lt.'input type="checkbox" name="GOTMLS_fix[]" value="'.$clean_file.'" id="check_'.$clean_file.(($className != "wp_core||ifitis")?'" checked="'.$className:'').'" /'.$gt.$threat_link;
			$imageFile = "threat";
		} elseif (isset($_POST["GOTMLS_fix"]) && is_array($_POST["GOTMLS_fix"]) && in_array($clean_file, $_POST["GOTMLS_fix"])) {
			echo __("Already Fixed!",'gotmls');
			return "/*-->*"."/\nfixedFile('$clean_file');\n/*<!--*"."/";
		} else
			$imageFile = "question";
		return GOTMLS_return_threat($className, $imageFile, $file, str_replace("GOTMLS_plugin", "GOTMLS_plugin $className", $threat_link));
	} elseif (isset($_POST["GOTMLS_fix"]) && is_array($_POST["GOTMLS_fix"]) && in_array($clean_file, $_POST["GOTMLS_fix"])) {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="Already Fixed";
		echo __("Already Fixed!",'gotmls');
		return "/*--{$gt}*"."/\nfixedFile('$clean_file');\n/*{$lt}!--*"."/";
	} else {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="no threat";
		return GOTMLS_return_threat($className, ($className=="scanned"?"checked":"blocked").".gif?$className", $file, $threat_link);
	}
}

function GOTMLS_db_scan($id = 0) {
	global $wpdb;
	$li_js = "";
	if (isset($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check"]) && is_array($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check"]) && in_array("db_scan", $GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["check"]) && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && count($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"])) {
		if ($id) {
			$encoded_id = GOTMLS_encode($id);
			$ids = explode(".", $id.'.');
			if (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl1' && is_numeric($ids[0]) && ($Q_post = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_id` = %s", (INT) $ids[0]), ARRAY_A))) {
				$path = 'Option ID: '.$Q_post["option_id"];
				$clean_file = $Q_post["option_name"];
				$fa = "";
				GOTMLS_load_contents($Q_post["option_value"]);
				$not_serialized = !(is_array(GOTMLS_uckserialize($GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $Q_post["option_value"])));
				$found = 0;
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
				foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"] as $scan_sql => $scan_regex) {
					$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
					$threat_name = array_shift($scan_regex);
					while ($threat_definition = array_shift($scan_regex))
						$found += GOTMLS_preg_match_all($threat_definition, $threat_name, $not_serialized);
				}
				if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$f = 1;
					foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
						list($start, $end, $junk) = explode("-", "$threats_found--", 3);
						if ($start > $end)
							$fa .= 'ERROR['.($f++).']: Threat_size{'.$threats_found.'} Content_size{'.GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'}';
						else
							$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
					}
				} else
					$fa = " No Threats Found";
				if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && in_array($encoded_id, $_REQUEST["GOTMLS_fix"]) && isset($_REQUEST["GOTMLS_fixing"]) && $_REQUEST["GOTMLS_fixing"] > 0) {
					GOTMLS_write_quarantine($Q_post, "db_scan");
					if ($_REQUEST["GOTMLS_fixing"] > 1) {
						echo "<li>Removing $path ... ";
						if ($wpdb->query($wpdb->prepare("DELETE FROM `$wpdb->options` WHERE `option_id` = %s", (INT) $Q_post["option_id"]))) {
							echo __("Done!",'gotmls');
							$li_js .= "/*-->*"."/\nDeletedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Failed to delete!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scanlog(array("scan" => array("finish" => time(), "type" => "Removal of Option")));
					} else {
						echo "<li>Fixing $path ... ";
						if ($wpdb->update($wpdb->options, array("option_value" => $GLOBALS["GOTMLS"]["tmp"]["new_contents"]), array('option_id' => $Q_post["option_id"]))) {
							echo __("Success!",'gotmls');
							$li_js .= "/*-->*"."/\nfixedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Update Failed!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scanlog(array("scan" => array("finish" => time(), "type" => "Removal from Option")));
					}
					return $li_js;
				} else {
					return '<form style="margin: 0;" method="post" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."1394")).'" onsubmit="return confirm(\''.__("Are you sure you want to delete this option?",'gotmls').'\');"><input type="hidden" name="GOTMLS_fixing" value="2"><input type="hidden" name="action" value="GOTMLS_fix"><input type="submit" value="Delete this Option" style="float: right;"><input type="hidden" name="GOTMLS_fix[]" value="'.$encoded_id.'"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>Record Details</b><br />encoding: '.(isset($GLOBALS["GOTMLS"]["tmp"]["encoding"])?$GLOBALS["GOTMLS"]["tmp"]["encoding"]:"Unknown").'<br />size: '.GOTMLS_strlen(GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"])).' bytes</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("Record Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.GOTMLS_htmlentities(GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>';
				}
			} elseif (($Q_post = GOTMLS_get_quarantine($ids[0])) && isset($Q_post["post_content"])) {
				$path = $Q_post["post_type"].' ID: '.$Q_post["ID"];
				$clean_file = $Q_post["post_title"];
				$fa = "";
				GOTMLS_load_contents($Q_post["post_content"]);
				$not_serialized = !(is_array(GOTMLS_uckserialize($GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $Q_post["post_content"])));
				$found = 0;
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
				foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"] as $scan_sql => $scan_regex) {
					$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
					$threat_name = array_shift($scan_regex);
					while ($threat_definition = array_shift($scan_regex))
						$found += GOTMLS_preg_match_all($threat_definition, $threat_name, $not_serialized);
				}
				if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$f = 1;
					foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
						list($start, $end, $junk) = explode("-", "$threats_found--", 3);
						if ($start > $end)
							$fa .= 'ERROR['.($f++).']: Threat_size{'.$threats_found.'} Content_size{'.GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'}';
						else
							$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
					}
				} else
					$fa = " No Threats Found";
				if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && in_array($encoded_id, $_REQUEST["GOTMLS_fix"]) && isset($_REQUEST["GOTMLS_fixing"]) && $_REQUEST["GOTMLS_fixing"] > 0) {
					if ($_REQUEST["GOTMLS_fixing"] > 1) {
						echo "<li>Removing $path ... ";
						$Q_post["post_status"] = "trash";
						if (wp_update_post($Q_post)) {
							echo __("Done!",'gotmls');
							$li_js .= "/*-->*"."/\nDeletedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Failed to delete!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scanlog(array("scan" => array("finish" => time(), "type" => "Removal of Revision")));
					} else {
						echo "<li>Fixing $path ... ";
						GOTMLS_write_quarantine($Q_post, "db_scan");
						$Q_post["post_content"] = $GLOBALS["GOTMLS"]["tmp"]["new_contents"];
						if (wp_update_post($Q_post)) {
							echo __("Success!",'gotmls');
							$li_js .= "/*-->*"."/\nfixedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Update Failed!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scanlog(array("scan" => array("finish" => time(), "type" => "Removal from Content")));
					}
					return $li_js;
				} else {
					return '<form style="margin: 0;" method="post" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."1448")).($Q_post["post_type"]=="revision"?'" onsubmit="return confirm(\''.__("Are you sure you want to delete this revision?",'gotmls').'\');"><input type="hidden" name="GOTMLS_fixing" value="2"><input type="hidden" name="action" value="GOTMLS_fix"><input type="submit" value="Delete this revision" style="float: right;"><input type="hidden" name="GOTMLS_fix[]" value="'.$encoded_id:"").'"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>Record Details</b><br />encoding: '.(isset($GLOBALS["GOTMLS"]["tmp"]["encoding"])?$GLOBALS["GOTMLS"]["tmp"]["encoding"]:"Unknown").'<br />size: '.GOTMLS_strlen(GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"])).' bytes<br />last_modified:'.$Q_post["post_modified_gmt"].'<br />post_type:'.$Q_post["post_type"].'<br />author:'.$Q_post["post_author"].'<br />status:'.$Q_post["post_status"].'</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("Record Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.GOTMLS_htmlentities(GOTMLS_convert_r($GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>';
				}
			} else
				die(GOTMLS_html_tags(array("html" => array("body" => __("This record no longer exists.",'gotmls')."<br />\n<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\n</script>"))));
		} else {
			$threats_found = array();
			$and = "";
			if (!isset($_REQUEST["eli"]))
				$and .= " AND `post_status` != 'trash'";
			if (isset($_REQUEST["limit"]) && is_numeric($_REQUEST["limit"]))
				$and .= " LIMIT ".((INT) $_REQUEST["limit"]);
			if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"])) {
				if (isset($_GET["GOTMLS_scan"]) && GOTMLS_strlen($_GET["GOTMLS_scan"]) > 8 && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][substr($_GET["GOTMLS_scan"], 8)])) {
					$scan_replace = str_replace("db_scan", "Database for ", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"]));
					$db_scan_a = array(GOTMLS_sanitize(substr($_GET["GOTMLS_scan"], 8)) => $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][substr($_GET["GOTMLS_scan"], 8)]);
				} elseif (isset($_GET["GOTMLS_only_file"]) && GOTMLS_strlen($_GET["GOTMLS_only_file"]) && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][GOTMLS_decode($_GET["GOTMLS_only_file"])])) {
					$scan_replace = str_replace("db_scan", ("Database only $and for "), GOTMLS_htmlspecialchars("db_scan=".htmlspecialchars_decode(GOTMLS_decode($_GET["GOTMLS_only_file"]))));
					$_GET["GOTMLS_scan"] = "db_scan=".GOTMLS_decode($_GET["GOTMLS_only_file"]);
					$db_scan_a = array(GOTMLS_decode($_GET["GOTMLS_only_file"]) => $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][GOTMLS_decode($_GET["GOTMLS_only_file"])]);
				} else {
					$scan_replace = str_replace("db_scan", "Database", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"]));
					$db_scan_a = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"];
				}
				echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), $scan_replace));
				GOTMLS_flush();
				$li_js .= "/*<!--*"."/".GOTMLS_return_threat("dir", "checked", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"])).GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), $scan_replace));
			} else {
				echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("No Definitions for DB Injections!",'gotmls')));
				GOTMLS_flush();
				$li_js .= GOTMLS_return_threat("error", "question", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"]));
				$db_scan_a = GOTMLS_sanitize($_GET["GOTMLS_scan"]);
			}
			if (isset($db_scan_a) && is_array($db_scan_a)) {
				echo "\n//memory_limit=".@ini_get("memory_limit")."\n";
				foreach ($db_scan_a as $scan_sql => $scan_regex) {
					if (!in_array(GOTMLS_sanitize($scan_sql), $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"])) {
						$SQL = preg_replace('/\{[a-f0-9]{64}\}/', '%', $wpdb->prepare("SELECT * FROM `$wpdb->posts` WHERE `post_content` LIKE %s $and", $scan_sql));
						$threat_name = array_shift($scan_regex);
						if (($found_row = $wpdb->get_results($SQL, ARRAY_A)) && is_array($found_row) && count($found_row)) {
							$val = count($found_row);
							if (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
								echo GOTMLS_return_threat("db_scan", "question", (print_r(array("scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("$val Rows", 0));//debug
							foreach ($found_row as $frow) {
								$encoded_id = GOTMLS_encode($frow["ID"].'.0');
								$found = 0;
								if ($frow["post_type"] != "revision" || isset($_REQUEST["eli"])) {
									GOTMLS_load_contents($frow["post_content"]);
									$not_serialized = !(is_array(GOTMLS_uckserialize($frow["post_content"])));
									$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
									$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
									foreach ($scan_regex as $threat_definition)
										$found += GOTMLS_preg_match_all($threat_definition, $threat_name, $not_serialized);
									if ($found && !isset($threats_found['row_id_'.$encoded_id])) {
										echo str_replace($frow["ID"].'</a>', '</a><a target="_blank" title="Open '.$frow["post_type"].'" href="'.admin_url(($frow["post_type"]=="revision")?'revision.php?revision='.$frow["ID"].'">View Revision: ':'post.php?action=edit&post='.$frow["ID"].'">Edit '.$frow["post_type"].': ').$frow["ID"].'</a>', GOTMLS_return_threat("db_scan", "threat", "$found $threat_name \"".str_replace('%', '*', trim($scan_sql, "%")).'" in '.$frow["post_type"]."(".(($frow["post_status"]=='inherit')?$frow["post_parent"]:$frow["post_status"]).'):"'.GOTMLS_htmlspecialchars($frow["post_title"]).'":'.$frow["ID"], '<input type="checkbox" name="GOTMLS_fix[]" id="check_'.$encoded_id.'" value="'.$encoded_id.'" checked="true">'.GOTMLS_error_link(__("View DB Injection",'gotmls'), $frow["ID"].'.0', "db_scan")));
										$threats_found['row_id_'.$encoded_id] = $threat_name;
									} elseif (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
										echo GOTMLS_return_threat("db_scan", "question", (print_r(array("post_id"=>$frow["ID"], "scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("No preg_match", 0));//debug
								}
							}
						}
						if (($found_row = $wpdb->get_results(preg_replace('/\{[a-f0-9]{64}\}/', '%', $wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_value` LIKE %s", $scan_sql)), ARRAY_A)) && is_array($found_row) && count($found_row)) {
							$val = count($found_row);
							if (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
								echo GOTMLS_return_threat("db_scan", "question", (print_r(array("scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("$val Rows", 0));//debug
							foreach ($found_row as $frow) {
								$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["last_threat"] = microtime(true);
								$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
								$encoded_id = GOTMLS_encode($frow["option_id"].'.1');
								$found = 0;
								GOTMLS_load_contents($frow["option_value"]);
								$not_serialized = !(is_array(GOTMLS_uckserialize($frow["option_value"])));
								foreach ($scan_regex as $threat_definition)
									$found += GOTMLS_preg_match_all($threat_definition, $threat_name, $not_serialized);
								if ($found && !isset($threats_found['row_id_'.$encoded_id])) {
									echo GOTMLS_return_threat("db_scan", "threat", "$found $threat_name \"".str_replace('%', '*', trim($scan_sql, "%")).'" in '."$wpdb->options:".GOTMLS_htmlspecialchars($frow["option_name"]).'":'.$frow["option_id"].'.1', '<input type="checkbox" name="GOTMLS_fix[]" id="check_'.$encoded_id.'" value="'.$encoded_id.'" checked="true">'.GOTMLS_error_link(__("View DB Injection",'gotmls'), $frow["option_id"].'.1', "db_scan"));
									$threats_found['row_id_'.$encoded_id] = $threat_name;
								} elseif (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
									echo GOTMLS_return_threat("db_scan", "question", (print_r(array("post_id"=>$frow["ID"], "scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("No preg_match", 0));//debug
							}
						}
					}
				}
			}
			//
		}
	} else {
		$li_js .= "/*<!--*"."/".GOTMLS_return_threat("skipdirs", "blocked", "db_scan").GOTMLS_update_status(__("Skipped DB Scan",'gotmls'));
	}
	GOTMLS_update_scanlog(array("scan" => array("finish" => time())));
	return 	"$li_js/*-->*"."/\nscanNextDir(-1);\n/*<!--*"."/";
}

function GOTMLS_remove_dots($dir) {
	if ($dir != "." && $dir != "..")
		return $dir;
}

function GOTMLS_getfiles($dir) {
	$files = false;
	if (is_dir($dir)) {
		if (function_exists("scandir"))
			$files = @scandir($dir);
		if (is_array($files))
			$files = array_filter($files, "GOTMLS_remove_dots");
		elseif ($handle = @opendir($dir)) {
			$files = array();
			while (false !== ($entry = readdir($handle)))
				if ($entry != "." && $entry != "..")
					$files[] = "$entry";
			closedir($handle);
		} else
			$files = GOTMLS_read_error($dir);
	}
	return $files;
}

function GOTMLS_return_threat($className, $imageFile, $fileName, $link = "") {
	global $GOTMLS_image_alt;
	$fileNameJS = GOTMLS_strip4java(str_replace("db_scan", "Database", str_replace("db_scan=", "Database Query ", GOTMLS_replace_dirname(htmlspecialchars_decode($fileName)))));
	$fileName64 = GOTMLS_encode(htmlspecialchars_decode($fileName));
	$li_js = "/*-->*"."/";
	$imageF = explode(".", $imageFile.".");
	if ($className != "scanned")
		$li_js .= "\n$className++;\ndivx=document.getElementById('found_$className');\nif (divx) {\n\tvar newli = document.createElement('li');\n\tnewli.innerHTML='<img src=\"".GOTMLS_strip4java(GOTMLS_images_path.$imageFile).'.gif" height=16 width=16 alt="'.$GOTMLS_image_alt[$imageF[0]].'" style="float: left;" id="'.$imageFile."_$fileName64\">".GOTMLS_strip4java($link, true).$fileNameJS.($link?"</a>';\n\tdivx.display='block":"")."';\n\tdivx.appendChild(newli);\n}";
	elseif ($GLOBALS["GOTMLS"]["scan"]["log"]["settings"]["scan_depth"] == 1) {
		if (isset($GLOBALS["GOTMLS"]["tmp"]["file_scan"]["errors"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["file_scan"]["errors"]) && count($GLOBALS["GOTMLS"]["tmp"]["file_scan"]["errors"]))
			$text = preg_replace('/[\r\n]/', " ", print_r($GLOBALS["GOTMLS"]["tmp"]["file_scan"]["errors"],1));
		else
			$text = (isset($GLOBALS["GOTMLS"]["tmp"]["file_scan"]["size"]) ? $GLOBALS["GOTMLS"]["tmp"]["file_scan"]["size"]." bytes" : "Size Errors");
		$link = GOTMLS_error_link(htmlspecialchars($text), $fileName, substr($text, -1)=="s"?$className:"potential");
		$li_js .= "\ndivx=document.getElementById('found_$className');\nif (divx) {\n\tvar newli = document.createElement('li');\n\tnewli.innerHTML='<img src=\"".GOTMLS_strip4java(GOTMLS_images_path.$imageFile).'.gif" height=16 width=16 alt="'.$GOTMLS_image_alt[$imageF[0]].'" style="float: left;" id="'.$imageFile."_$fileName64\">".round(microtime(true)-$GLOBALS["GOTMLS"]["tmp"]["file_scan"]["start"], 4).GOTMLS_strip4java($link, true).$fileNameJS.($link?"</a>';\n\tdivx.display='block":"")."';\n\tdivx.appendChild(newli);\n}";
	}
	if ($className == "errors")
		$li_js .= "\ndivx=document.getElementById('wait_$fileName64');\nif (divx) {\n\tdivx.src='".GOTMLS_images_path."blocked.gif';\n\tdirerrors++;\n}";
	elseif (is_file($fileName))
	 	$li_js .= "\nscanned++;\n";
	if ($className == "dir")
		$li_js .= "\ndivx=document.getElementById('wait_$fileName64');\nif (divx)\n\tdivx.src='".GOTMLS_images_path."checked.gif';";
	return $li_js."\n/*<!--*"."/";
}

function GOTMLS_slash($dir = __FILE__) {
	if (substr($dir.'  ', 1, 1) == ':' || substr($dir.'  ', 0, 1) == "\\")
		return "\\";
	else
		return  '/';
}

function GOTMLS_trailingslashit($dir = "") {
	if (substr(' '.$dir, -1) != GOTMLS_slash($dir))
		$dir .= GOTMLS_slash($dir);
	return $dir;
}

function GOTMLS_explode_dir($dir, $pre = '') {
	if (GOTMLS_strlen($pre))
		$dir = GOTMLS_slash($dir).$pre.$dir;
	return explode(GOTMLS_slash($dir), $dir);
}

function GOTMLS_html_tags($tags, $inner = array()) {
	$html = "";
	$gt = ">"; // This local variable never changes
	if (!is_array($tags))
		$tags = array($tags => (is_array($inner)?(isset($inner["contents"])?$inner["contents"]:""):$inner));
	foreach ($tags as $tag => $contents) {
		if (!is_numeric($tag))
			$html .= ($tag=="html"?"<!DOCTYPE html$gt":"")."<$tag".(isset($inner[$tag])?" ".$inner[$tag]:"").$gt;
		if (is_array($contents))
			$html .= GOTMLS_html_tags($contents, $inner);
		else
			$html .= $contents;
		if ((!is_numeric($tag)) && substr($tag, -1) != '/')
			$html .= "</$tag$gt";
	}
	return $html;
}

function GOTMLS_write_quarantine($file, $className, $post_status = "private") {
	global $wpdb;
	$insert = array("post_author"=>GOTMLS_get_current_user_id(), "post_content"=>GOTMLS_encode($GLOBALS["GOTMLS"]["tmp"]["file_contents"]), "post_mime_type"=>md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"]), "post_name"=>$className, "post_status"=>$post_status, "post_type"=>"GOTMLS_quarantine", "post_content_filtered"=>GOTMLS_encode($GLOBALS["GOTMLS"]["tmp"]["new_contents"]), "guid"=>GOTMLS_Version);
	if (isset($file["ID"]) && is_numeric($file["ID"])) {
		$insert["post_modified"] = $file["post_modified"];
		$insert["post_modified_gmt"] = $file["post_modified_gmt"];
		$file = $file["post_type"].':'.$file["ID"].':"'.$file["post_title"].'"';
	} elseif (isset($file["option_id"]) && is_numeric($file["option_id"])) {
		$insert["post_modified"] = gmdate("Y-m-d H:i:s");
		$insert["post_modified_gmt"] = gmdate("Y-m-d H:i:s");
		$file = $wpdb->options.':'.$file["option_id"].':"'.$file["option_name"].'"';
	}
	$insert["comment_count"] = GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
	$insert["post_title"] = $file;
	$insert["post_date"] = gmdate("Y-m-d H:i:s");
	$insert["post_date_gmt"] = $insert["post_date"];
	if (is_file($file)) {
		if (@filemtime($file))
			$insert["post_modified"] = gmdate("Y-m-d H:i:s", filemtime($file));
		else
			$insert["post_modified"] = $insert["post_date"];
		if (@filectime($file))
			$insert["post_modified_gmt"] = gmdate("Y-m-d H:i:s", filectime($file));
		else
			$insert["post_modified_gmt"] = $insert["post_date"];
		if (!($insert["comment_count"] = @filesize($file)))
			$insert["comment_count"] = GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
	}
	if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
		$insert["post_excerpt"] = GOTMLS_encode(@serialize($GLOBALS["GOTMLS"]["tmp"]["threats_found"]));
		$pinged = array();
		foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $loc => $threat_name) {
			if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][0]) && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1]) && GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][0]) == 5 && GOTMLS_strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1]))
				$ping = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1];
			else
				$ping = $threat_name;
			if (isset($pinged[$ping]))
				$pinged[$ping]++;
			else
				$pinged[$ping] = 1;
		}
		$insert["pinged"] = GOTMLS_encode(@serialize($pinged));
	}
	if ($return = $wpdb->insert($wpdb->posts, $insert))
		return $return;
	else
		die(print_r(array('return'=>($return===false)?"FALSE":$return, 'last_error'=>$wpdb->last_error, 'insert'=>$insert),1));
}

function GOTMLS_update_status($status, $percent = -1) {
	if (!(isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]) && is_numeric($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"])))
		$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"] = time();
	$microtime = ceil(time()-$GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["start"]);
	if (($percent > 0) || isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["finish"]))
		GOTMLS_update_scanlog(array("scan" => array("microtime" => $microtime, "percent" => $percent)), $status);
	return "/*-->*"."/\nupdate_status('".GOTMLS_strip4java($status)."', $microtime, $percent);\n/*<!--*"."/";
}

function GOTMLS_flush($tag = "") {
	$output = "";
	if (($output = @ob_get_contents()) && GOTMLS_strlen(trim($output)) > 18) {
		@ob_clean();
		if (!(isset($_GET["eli"]) && $_GET["eli"] == "debug"))
			$output = preg_replace('/\/\*<\!--\*\/.*?\/\*-->\*\//s', "", "$output/*-->*"."/");
		echo "$output\n//flushed(".GOTMLS_strlen(trim($output)).")\n";
		if ($tag)
			echo "\n</$tag>\n";
		if (@ob_get_length())
			@ob_flush();
		if ($tag)
			echo "<$tag>\n";
		echo "/*<!--*"."/";
	}
}

function GOTMLS_replace_dirname($dir, $replace_with = "...") {
	return (isset($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["dir"]) ? str_replace(dirname($GLOBALS["GOTMLS"]["scan"]["log"]["scan"]["dir"]), $replace_with, $dir) : $dir);
}

function GOTMLS_readdir($dir, $current_depth = 1) {
	global $GOTMLS_dirs_at_depth, $GOTMLS_dir_at_depth, $GOTMLS_total_percent;
	if ($current_depth) {
		@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time']);
		$entries = GOTMLS_getfiles($dir);
		if (is_array($entries)) {
			echo GOTMLS_return_threat("dirs", "wait", $dir).GOTMLS_update_status(sprintf(__("Preparing %s",'gotmls'), GOTMLS_replace_dirname($dir)), $GOTMLS_total_percent);
			$files = array();
			$directories = array();
			foreach ($entries as $entry) {
				if (is_dir(GOTMLS_trailingslashit($dir).$entry))
					$directories[] = $entry;
				else
					$files[] = $entry;
			}
			if ($_REQUEST["scan_type"] == "Quick Scan") {
				$GOTMLS_dirs_at_depth[$current_depth] = count($directories);
				$GOTMLS_dir_at_depth[$current_depth] = 0;
			} else
				$GLOBALS["GOTMLS"]["tmp"]["scanfiles"][GOTMLS_encode($dir)] = GOTMLS_strip4java(GOTMLS_replace_dirname($dir));
			foreach ($directories as $directory) {
				$path = GOTMLS_trailingslashit($dir).$directory;
				if (isset($_REQUEST["scan_depth"]) && is_numeric($_REQUEST["scan_depth"]) && ($_REQUEST["scan_depth"] != $current_depth) && (!((($Recusive = strpos(GOTMLS_trailingslashit($dir), '/'.$directory.'/')) !== FALSE) && is_dir($dir.substr($dir, $Recusive).substr($dir, $Recusive).substr($dir, $Recusive)))) && !in_array($directory, $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"]) && !in_array($path, $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"])) {
					$current_depth++;
					$current_depth = GOTMLS_readdir($path, $current_depth);
				} else {
					if (!(isset($_REQUEST["scan_only"]) && is_array($_REQUEST["scan_only"])&& in_array($directory, $_REQUEST["scan_only"])))
						echo GOTMLS_return_threat("skipdirs", "blocked", $path, '<a title="'.(in_array($directory, $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"])||in_array($path, $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"])?"Directory is on Skip List":"Directory is below Scan Depth").'">');
					$GOTMLS_dir_at_depth[$current_depth] = (isset($GOTMLS_dir_at_depth[$current_depth])?$GOTMLS_dir_at_depth[$current_depth]:0) + 1;
				}
			}
			if ($_REQUEST["scan_type"] == "Quick Scan") {
				$echo = "";
				echo GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), GOTMLS_replace_dirname($dir)), $GOTMLS_total_percent);
				GOTMLS_flush("script");
				foreach ($files as $file)
					echo GOTMLS_check_file(GOTMLS_trailingslashit($dir).$file);
				echo GOTMLS_return_threat("dir", "checked", $dir);
			}
		} else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(GOTMLS_Failed_to_list_LANGUAGE.' readdir:'.($entries===false?'('.GOTMLS_fileperms($dir).')':$entries)));
		@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time']);
		if ($current_depth-- && $_REQUEST["scan_type"] == "Quick Scan") {
			$GOTMLS_dir_at_depth[$current_depth] = (isset($GOTMLS_dir_at_depth[$current_depth])?$GOTMLS_dir_at_depth[$current_depth]:0) + 1;
			for ($GOTMLS_total_percent = 0, $depth = $current_depth; $depth >= 0; $depth--) {
				if (!isset($GOTMLS_dir_at_depth[$depth]))
					$GOTMLS_dir_at_depth[$depth] = 0;
				echo "\n//(($GOTMLS_total_percent / $GOTMLS_dirs_at_depth[$depth]) + ($GOTMLS_dir_at_depth[$depth] / $GOTMLS_dirs_at_depth[$depth])) = ";
				$GOTMLS_total_percent = (($GOTMLS_dirs_at_depth[$depth]?($GOTMLS_total_percent / $GOTMLS_dirs_at_depth[$depth]):0) + ($GOTMLS_dir_at_depth[$depth] / ($GOTMLS_dirs_at_depth[$depth]+1)));
				echo "$GOTMLS_total_percent\n";
			}
			$GOTMLS_total_percent = floor($GOTMLS_total_percent * 100);
			echo GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), GOTMLS_replace_dirname($dir)), $GOTMLS_total_percent);
		}
		GOTMLS_flush("script");
	}
	return $current_depth;
}

function GOTMLS_sexagesimal($timestamp = 0) {
	if (!is_numeric($timestamp) && GOTMLS_strlen($timestamp) == 5) {
		$delim = array("=", "-", "-", " ", ":");
		foreach (str_split($timestamp) as $bit)
			$timestamp .= array_shift($delim).substr("00".(ord($bit)>96?ord($bit)-61:(ord($bit)>64?ord($bit)-55:ord($bit)-48)), -2);
		return "20".substr($timestamp, -14);
	} else {
		$match = '/^(20)?([0-5][0-9])[\-: \/]*(0*[1-9]|1[0-2])[\-: \/]*(0*[1-9]|[12][0-9]|3[01])[\-: \/]*([0-5][0-9])[\-: \/]*([0-5][0-9])$/';
		if (preg_match($match, $timestamp))
			$date = preg_replace($match, "\\2-\\3-\\4-\\5-\\6", $timestamp);
		elseif ($timestamp && strtotime($timestamp))
			$date = date("y-m-d-H-i", strtotime($timestamp));
		else
			$date = gmdate("y-m-d-H-i", time());
		foreach (explode("-", $date) as $bit)
			$date .= (intval($bit)>35?chr(ord("a")+intval($bit)-36):(intval($bit)>9?chr(ord("A")+intval($bit)-10):substr('0'.$bit, -1)));
		return substr($date, -5);
	}
}

if (!function_exists('ur1encode')) { function ur1encode($url) {
	$return = "";
	foreach (str_split($url) as $char)
		$return .= '%'.substr('00'.strtoupper(dechex(ord($char))),-2);
	return $return;
}}

function GOTMLS_strip4java($item, $htmlentities = false) {
	return preg_replace("/\\\\/", "\\\\\\\\", str_replace("'", "'+\"'\"+'", preg_replace('/\\+n|\\+r|\n|\r|\0/', "", ($htmlentities?$item:GOTMLS_htmlentities($item)))));
}

function GOTMLS_error_link($errorTXT, $file = "", $class = "errors") {
	global $post, $wpdb;
	$encoded_file = GOTMLS_encode($file);
	$ids = explode(".", $file.'.');
	if (isset($post->post_title))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($post->post_title, ENT_NOQUOTES));
	elseif (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl1' && is_numeric($ids[0]))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($wpdb->get_var($wpdb->prepare("SELECT CONCAT('option', `option_id`, ': ', `option_name`) FROM `$wpdb->options` WHERE `option_id` = %s", (INT) $ids[0])), ENT_NOQUOTES));
	elseif (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl0' && is_numeric($ids[0]))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($wpdb->get_var($wpdb->prepare("SELECT CONCAT(`post_type`, `ID`, ': ', `post_title`) FROM `$wpdb->posts` WHERE `ID` = %s", (INT) $ids[0])), ENT_NOQUOTES));
	else
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($file, ENT_NOQUOTES));
	$nonce_url = GOTMLS_set_nonce(__FUNCTION__."1823");
	if (count($ids) == 2 && is_numeric($ids[0])) {
		$encoded_file = (INT) $file;
		$onclick = 'loadIframe(\''.str_replace("\"", "&quot;", '<div style="float: left; white-space: nowrap;">'.GOTMLS_strip4java(__("Examine Quarantined Content",'gotmls')).' ... </div><div style="overflow: hidden; position: relative; height: 20px;"><div style="position: absolute; right: 0px; text-align: right; width: 9000px;">'.$js_file).'</div></div>\');" href="'.GOTMLS_admin_url('GOTMLS_scan', $nonce_url.'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$encoded_file);
	} elseif ($file)
		$onclick = 'loadIframe(\''.str_replace("\"", "&quot;", '<div style="float: left; white-space: nowrap;">'.GOTMLS_strip4java(__("Examine Current Content",'gotmls')).' ... </div><div style="overflow: hidden; position: relative; height: 20px;"><div style="position: absolute; right: 0px; text-align: right; width: 9000px;">'.$js_file).'</div></div>\');" href="'.GOTMLS_admin_url('GOTMLS_scan', $nonce_url.'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$encoded_file);
	else
		$onclick = 'return false;';
	return "<a id=\"list_$encoded_file\" title=\"$errorTXT\" target=\"GOTMLS_iFrame\" onclick=\"$onclick\" class=\"GOTMLS_plugin $class\">";
}

function GOTMLS_check_file($file) {
	$filesize = @filesize($file);
	$MD5O = @md5_file($file)."O";
	echo "/*-->*"."/\ndocument.getElementById('status_text').innerHTML='Checking ".GOTMLS_strip4java($file)." ($filesize bytes)';\n/*<!--*"."/";
	if ($filesize===false)
		echo GOTMLS_return_threat("errors", "blocked", $file, GOTMLS_error_link(__("Failed to determine file size!",'gotmls'), $file));
	elseif (GOTMLS_is_whitelisted($MD5O.$filesize, $file))
		echo GOTMLS_return_threat("scanned", "checked", $file, GOTMLS_error_link(__("CORE file was not modified!",'gotmls'), $file, ""));
	elseif (($filesize==0) || ($filesize>((isset($_REQUEST["oversize"])&&is_numeric($_REQUEST["oversize"]))?$_REQUEST["oversize"]:2934567)))
		echo GOTMLS_return_threat("skipped", "blocked", $file, GOTMLS_error_link(__("Skipped because of file size!",'gotmls')." ($filesize bytes)", $file, "potential"));
	elseif (in_array(GOTMLS_get_ext($file), $GLOBALS["GOTMLS"]["tmp"]["skip_ext"]) && !(preg_match('/(?:(?:shim|social\d*+)\.png|\/\.[^\/]++)$/i', $file)))
		echo GOTMLS_return_threat("skipped", "blocked", $file, GOTMLS_error_link(__("Skipped because of file extention!",'gotmls'), $file, "potential"));
	elseif (isset($GLOBALS["GOTMLS"]["tmp"]["custom_whitelist"]) && isset($GLOBALS["GOTMLS"]["tmp"]["custom_whitelist"]["$MD5O$filesize"]))
		echo GOTMLS_return_threat("skipped", "blocked", $file, GOTMLS_error_link(__("Skipped because file was Whitelisted!",'gotmls'), $file, "potential"));
	else {
		try {
			echo @GOTMLS_scanfile($file);
			echo "/*-->*"."/\n//debug_fix:".$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]."\n/*<!--*"."/";
		} catch (Exception $e) {
			die("//Exception:".GOTMLS_strip4java($e));
		}
	}
	echo "/*-->*"."/\ndocument.getElementById('status_text').innerHTML='Checked ".GOTMLS_strip4java($file)."';\n/*<!--*"."/";
}

function GOTMLS_read_error($path) {
	$error = error_get_last();
	if (!file_exists($path))
		return " (Path not found)";
	if (!is_readable($path) && isset($_GET["eli"]))
		$return = (@chmod($path, (is_dir($path)?GOTMLS_CHMOD_DIR:GOTMLS_CHMOD_FILE))?"Fixed permissions":"error: ".preg_replace('/[\r\n]/', ' ', print_r($error,1)));
	else
		$return = (is_array($error) && isset($error["message"])?preg_replace('/[\r\n]/', ' ', print_r($error["message"],1)):"readable?");
	return " [".GOTMLS_fileperms($path)."] ( ".filesize($path)." $return)";
}

function GOTMLS_scandir($dir) {
	echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), GOTMLS_replace_dirname(GOTMLS_htmlspecialchars($dir))));
	GOTMLS_flush();
	$li_js = "/*-->*"."/\nscanNextDir(-1);\n/*<!--*"."/";
	if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]))
		echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link("Directory Scan Depth set to 0, no files will be scanned!"));
	elseif (isset($_GET["GOTMLS_skip_dir"]) && $dir == GOTMLS_decode($_GET["GOTMLS_skip_dir"])) {
		if (isset($_GET["GOTMLS_only_file"]) && GOTMLS_strlen($_GET["GOTMLS_only_file"]))
			echo GOTMLS_return_threat("errors", "blocked", GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"]), GOTMLS_error_link("Failed to read this file!".GOTMLS_read_error(GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"])), GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"])));
		else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(__("Failed to read directory!",'gotmls')).GOTMLS_read_error($dir));
	} else {
		if (is_dir($dir) && is_array($files = GOTMLS_getfiles($dir))) {
			if (isset($_GET["GOTMLS_only_file"])) {
				if (GOTMLS_strlen($_GET["GOTMLS_only_file"])) {
					$path = GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"]);
					if (is_file($path)) {
						GOTMLS_check_file($path);
						echo GOTMLS_return_threat("dir", "checked", $path);
					}
				} else {
					foreach ($files as $file) {
						$path = GOTMLS_trailingslashit($dir).$file;
						if (is_file($path)) {
							$file_ext = GOTMLS_get_ext($file);
							$filesize = @filesize($path);
							if ((in_array($file_ext, $GLOBALS["GOTMLS"]["tmp"]["skip_ext"]) && !(preg_match('/(?:(?:shim|social\d*+)\.png|\/\.[^\/]++)$/i', $file))) || ($filesize==0) || ($filesize>((isset($_REQUEST["oversize"])&&is_numeric($_REQUEST["oversize"]))?$_REQUEST["oversize"]:2934567)))
								echo GOTMLS_return_threat("skipped", "blocked", $path, GOTMLS_error_link(sprintf(__('Skipped because of file size (%1$s bytes) or file extention (%2$s)!','gotmls'), $filesize, $file_ext), $file, "potential"));
							else
								echo "/*-->*"."/\nscanfilesArKeys.push('".GOTMLS_encode($dir)."&GOTMLS_only_file=".GOTMLS_encode($file, "D")."');\nscanfilesArNames.push('Re-Checking ".GOTMLS_strip4java($path)."');\n/*<!--*"."/".GOTMLS_return_threat("dirs", "wait", $path);
						} elseif (is_dir($path)) {
							echo "/*-->*"."/\n//sub-directory $path;\n/*<!--*"."/";
						}
					}
					echo GOTMLS_return_threat("dir", "question", $dir);
				}
			} else {
				foreach ($files as $file) {
					$path = GOTMLS_trailingslashit($dir).$file;
					if (is_file($path)) {
						if (isset($_GET["GOTMLS_skip_file"]) && is_array($_GET["GOTMLS_skip_file"]) && in_array($path, $_GET["GOTMLS_skip_file"])) {
							$li_js .= "/*-->*"."/\n//skipped $path;\n/*<!--*"."/";
							if ($_GET["GOTMLS_skip_file"][count($_GET["GOTMLS_skip_file"])-1] == $path)
								echo GOTMLS_return_threat("errors", "blocked", $path, GOTMLS_error_link(__("Failed to read file!",'gotmls'), $path));
						} else {
							GOTMLS_check_file($path);
						}
					} elseif (is_dir($path)) {
						$li_js .= "/*-->*"."/\n//sub-directory $path;\n/*<!--*"."/";
					}
				}
				echo GOTMLS_return_threat("dir", "checked", $dir);
			}
		} else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(GOTMLS_Failed_to_list_LANGUAGE.' scandir:'.($files===false?' (FALSE)':$files)));
	}
	echo GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), GOTMLS_replace_dirname($dir)));
	return $li_js;
}

function GOTMLS_safe_domain($domain) {
	return preg_replace('/[^a-z_0-9\-\~\+\.\?\#\/\:\@]/i', "", $domain);
}

function GOTMLS_safe_url($url, $allow = array(' ', '%20')) {
	$all = implode("", array_keys($allow));
	$url = preg_replace('/[^a-z_0-9\-\~\+\.\?\#\/\:\@\%\$\|\*\(\)\[\]\=\!\&\;'.$all.']/i', "", $url);
	foreach ($allow as $al => $low)
		$url = str_replace($al, $low, ltrim($url));
	return $url;
}

function GOTMLS_reset_settings($item, $key) {
	$key_parts = explode("_", $key."_");
	if (GOTMLS_strlen($key_parts[0]) != 4 && $key_parts[0] != "exclude")
		unset($GLOBALS["GOTMLS"]["tmp"]["settings_array"][$key]);
}

function GOTMLS_sanitize($unsanitized, $allow = 'a-zA-Z0-9\|\[\]\{\}<>\s\?\*\%\#\&\/=_\~\:;\.,\+-') {
	if (is_array($unsanitized)) {
		$sanitized = array();
		foreach ($unsanitized as $key => $val)
			$sanitized[preg_replace('/[^'.$allow.']/', '', $key)] = preg_replace('/[^'.$allow.']/', '', $val);
	} else
		$sanitized = preg_replace('/[^'.$allow.']/', '', $unsanitized);
	return $sanitized;
}

function GOTMLS_get_URL($URL) {
	$response = "";
	$GLOBALS["GOTMLS"]["get_URL"] = GOTMLS_get_option('get_URL', array());
	$min = round($GLOBALS["GOTMLS"]["MT"]/60);
	if (is_array($GLOBALS["GOTMLS"]["get_URL"]) && !preg_match('/\&dt=\d++/', $URL)) {
		foreach ($GLOBALS["GOTMLS"]["get_URL"] as $URI => $property)
			if (!(isset($property["time"]) && is_numeric($property["time"]) && ($property["time"] + 30) > $min))
				unset($GLOBALS["GOTMLS"]["get_URL"]["$URI"]);
	} else
		$GLOBALS["GOTMLS"]["get_URL"] = array();
	$URI = md5(preg_replace('/GOTMLS_mt[\[\]]*+=[a-f\d]*+/i', "", $URL));
	if (isset($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"]) && GOTMLS_strlen($response = ($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"])))
		$method = "cached";
	else {
		$GLOBALS["GOTMLS"]["get_URL"]["$URI"] = array("time" => $min);
		if (function_exists($method = "wp_remote_get")) {
			$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = wp_remote_get($URL, array("sslverify" => false));
			if (200 == wp_remote_retrieve_response_code($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method])) {
				$response = wp_remote_retrieve_body($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]);
				if (isset($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]["http_response"]))
					unset($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]["http_response"]);
				if (isset($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]["body"]))
					unset($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]["body"]);
			}
		}
		if (GOTMLS_strlen($response) == 0 && function_exists($method = "curl_exec")) {
			$curl_hndl = curl_init();
			curl_setopt($curl_hndl, CURLOPT_URL, $URL);
			curl_setopt($curl_hndl, CURLOPT_TIMEOUT, 30);
			if (isset($_SERVER['HTTP_REFERER']))
				$SERVER_HTTP_REFERER = GOTMLS_safe_url($_SERVER['HTTP_REFERER']);
			elseif (isset($_SERVER['HTTP_HOST']))
				$SERVER_HTTP_REFERER = 'HOST://'.GOTMLS_safe_domain($_SERVER['HTTP_HOST']);
			elseif (isset($_SERVER['SERVER_NAME']))
				$SERVER_HTTP_REFERER = 'NAME://'.GOTMLS_safe_domain($_SERVER['SERVER_NAME']);
			elseif (isset($_SERVER['SERVER_ADDR']))
				$SERVER_HTTP_REFERER = 'ADDR://'.GOTMLS_safe_ip($_SERVER['SERVER_ADDR']);
			else
				$SERVER_HTTP_REFERER = 'NULL://not.anything.com';
			curl_setopt($curl_hndl, CURLOPT_REFERER, $SERVER_HTTP_REFERER);
			if (isset($_SERVER['HTTP_USER_AGENT']))
				curl_setopt($curl_hndl, CURLOPT_USERAGENT, GOTMLS_safe_url($_SERVER['HTTP_USER_AGENT'], array(' ', ' ')));
			curl_setopt($curl_hndl, CURLOPT_HEADER, 0);
			curl_setopt($curl_hndl, CURLOPT_RETURNTRANSFER, TRUE);
			if (!($response = curl_exec($curl_hndl)))
				$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = curl_error($curl_hndl);
			curl_close($curl_hndl);
		}
		if (GOTMLS_strlen($response) == 0 && function_exists($method = "file_get_contents")) {
			try {
				$response = @file_get_contents($URL).'';
			} catch(Exception $e) {
				$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = $e->getTrace();
			}
		}
		$GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"] = ($response);
	}
	GOTMLS_update_option('get_URL', $GLOBALS["GOTMLS"]["get_URL"], false);
	if (isset($_GET["GOTMLS_debug"]) && (GOTMLS_strlen($response) == 0 || $_GET["GOTMLS_debug"] == "GOTMLS_get_URL"))
		print_r(array("$method $URI:".GOTMLS_strlen($response)=>htmlspecialchars($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["time"]." ~ $min: ".count($GLOBALS["GOTMLS"]["get_URL"]))));
	return $response;
}
