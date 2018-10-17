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
			'height' => array('match' => '(\d+)'),
		),
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '{width}|{height}',
		'disabled_content' => '$1',
	);

	// Format: [espn]{playlist ID}[/espn]
	$bbc[] = array(
		'tag' => 'espn',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '576|324',
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
	global $txt;
	
	if (empty($data))
		return ($tag['content'] = $txt['espn_invalid']);
	list($width, $height, $id) = explode('|', $tag['content'] . '|' . ((int) $data));
	if (preg_match('#(http|https):\/\/espn\.go.com/video/clip\?id=(|espn:)(\d+)#i', $data, $parts))
		$id = $parts[3];
	$tag['content'] = (empty($id) ? $txt['espn_invalid'] : '<script src="http://player.espn.com/player.js?playerBrandingId=4ef8000cbaf34c1687a7d9a26fe0e89e&adSetCode=91cDU6NuXTGKz3OdjOxFdAgJVtQcKJnI&pcode=1kNG061cgaoolOncv54OAO1ceO-I&width=' . $width .'&height=' . $height . '&externalId=espn:' . $id . '&thruParam_espn-ui[autoPlay]=false&thruParam_espn-ui[playRelatedExternally]=true"></script>');
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