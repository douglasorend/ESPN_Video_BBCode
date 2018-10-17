<?php
/**********************************************************************************
* Subs-BBCode-ESPN.php
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

function BBCode_ESPN(&$bbc)
{
	// Format: [espn width=x height=x]{playlist ID}[/espn]
	$bbc[] = array(
		'tag' => 'espn',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+)'),
			'height' => array('optional' => true, 'match' => '(\d+)'),
			'frameborder' => array('optional' => true, 'match' => '(\d+)'),
		),
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '{width}|{height}|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [espn]{playlist ID}[/espn]
	$bbc[] = array(
		'tag' => 'espn',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '0|0|0',
		'disabled_content' => '$1',
	);
}

function BBCode_ESPN_Button(&$buttons)
{
	$buttons[count($buttons) - 1][] = array(
		'image' => 'espn',
		'code' => 'espn',
		'description' => 'espn',
		'before' => '[espn]',
		'after' => '[/espn]',
	);
}

function BBCode_ESPN_Validate(&$tag, &$data, &$disabled)
{
	global $context, $modSettings, $txt;
	
	if (empty($data))
		return ($tag['content'] = $txt['espn_invalid']);
	list($width, $height, $frameborder) = explode('|', $tag['content']);
	if (empty($width) && !empty($modSettings['espn_default_width']))
		$width = $modSettings['espn_default_width'];
	if (empty($height) && !empty($modSettings['espn_default_height']))
		$height = $modSettings['espn_default_height'];
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
	if (!preg_match('#(http|https):\/\/(video\.espn\.com|espn\.go.com)/video/clip\?id=(|espn:)(\d+)#i', $data, $parts))
		return ($tag['content'] = $txt['espn_invalid']);
	$md5 = md5($data);
	if (($results = cache_get_data('espn_' . $md5, 86400)) == null)
	{
		$content = file_get_contents($data);
		$pattern = '#meta name="twitter:player" content="(.+?)"#i' . ($context['utf8'] ? 'u' : '');
		preg_match($pattern, $content, $codes);
		$results = (isset($codes[1]) ? $codes[1] : '');
		cache_put_data('espn_' . $md5, $results, 86400);
	}
	$tag['content'] = '<div' . ((empty($width) || empty($height)) ? '' : ' style="max-width: ' . $width . 'px; max-height: ' . $height . 'px;"') . '><div class="espn-wrapper"><iframe class="espn-player" type="text/html" src="' . $results .'" allowfullscreen frameborder="' . $frameborder . '"></iframe></div></div>';
}

function BBCode_ESPN_Settings(&$config_vars)
{
	$config_vars[] = array('int', 'espn_default_width');
	$config_vars[] = array('int', 'espn_default_height');
}

function BBCode_ESPN_Theme()
{
	global $context, $settings;
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/BBCode-ESPN.css" />';
	$context['allowed_html_tags'][] = '<iframe>';
}

function BBCode_ESPN_Embed(&$message)
{
	$pattern = '#(|\[espn(|.+?)\](([<br />]+)?))(http|https):\/\/(video\.espn\.com|espn\.go.com)/video/clip\?id=(|espn:)(\d+)(([<br />]+)?)(\[/espn\]|)#i';
	$message = preg_replace($pattern, '[espn$2]$5://$6/video/clip?id=$8[/espn]', $message);
	$pattern = '#(|\[espn(|.+?)\](([&lt;br /&gt;]+)?))&lt;script src=&quot;(http|https):\/\/player\.espn\.com/player\.js\?(.+?&amp;|)externalId=espn:(\d+)(|&amp;.+?)&quot;&gt;&lt;/script&gt;(([&lt;br /&gt;]+)?)(\[/espn\]|)#i';
	$message = preg_replace($pattern, '[espn$2]$5://espn.go.com/video/clip?id=$7[/espn]', $message);
	$pattern = '#\[code(|(.+?))\](|.+?)\[espn(|.+?)\](.+?)\[/espn\](|.+?)\[/code\]#i';
	$message = preg_replace($pattern, '[code$1]$3$5$6[/code]', $message);
}

?>