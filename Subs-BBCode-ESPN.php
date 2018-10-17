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
	if (empty($data))
		return ($tag['content'] = '');
	list($width, $height) = explode('|', $tag['content']);
	parse_str(parse_url(str_replace('&amp;', '&', $data), PHP_URL_QUERY), $out);
	$data = (isset($out['id']) ? $out['id'] : ((isset($out['externalId']) ? str_replace('espn:', '', $out['externalId']) : (int) $data)));
	$tag['content'] = (empty($data) ? '' : '<script src="http://player.espn.com/player.js?playerBrandingId=4ef8000cbaf34c1687a7d9a26fe0e89e&adSetCode=91cDU6NuXTGKz3OdjOxFdAgJVtQcKJnI&pcode=1kNG061cgaoolOncv54OAO1ceO-I&width=' . $width .' &height=' . $height . '&externalId=espn:' . $data . '&thruParam_espn-ui[autoPlay]=false&thruParam_espn-ui[playRelatedExternally]=true"></script>');
}

?>