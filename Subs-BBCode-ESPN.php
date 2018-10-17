<?php
/**********************************************************************************
* Subs-BBCode-ESPN.php
***********************************************************************************
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/

function BBCode_ESPN(&$bbc)
{
	// Format: [espn width=x height=x]{playlist ID}[/espn]
	$bbc[] = array(
		'tag' => 'espn',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('value' => ' width="$1"', 'match' => '(\d+)'),
			'height' => array('value' => ' height="$1"', 'match' => '(\d+)'),
		),
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '<script src="http://player.espn.com/player.js?playerBrandingId=4ef8000cbaf34c1687a7d9a26fe0e89e&adSetCode=91cDU6NuXTGKz3OdjOxFdAgJVtQcKJnI&pcode=1kNG061cgaoolOncv54OAO1ceO-I&width=576&height=324&externalId=espn:$1&thruParam_espn-ui[autoPlay]=false&thruParam_espn-ui[playRelatedExternally]=true"></script>',
		'disabled_content' => '$1',
	);

	// Format: [espn]{playlist ID}[/espn]
	$bbc[] = array(
		'tag' => 'espn',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_ESPN_Validate',
		'content' => '<script src="http://player.espn.com/player.js?playerBrandingId=4ef8000cbaf34c1687a7d9a26fe0e89e&adSetCode=91cDU6NuXTGKz3OdjOxFdAgJVtQcKJnI&pcode=1kNG061cgaoolOncv54OAO1ceO-I&width=576&height=324&externalId=espn:$1&thruParam_espn-ui[autoPlay]=false&thruParam_espn-ui[playRelatedExternally]=true"></script>',
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
	parse_str(parse_url(str_replace('&amp;', '&', $data), PHP_URL_QUERY), $out);
	$data = (isset($out['externalId']) ? str_replace('espn:', '', $out['externalId']) : (int) $data);
}

?>