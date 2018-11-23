<?php
/*
 * MyBB: Rules Page
 *
 * File: rules_page.php
 * 
 * Authors: Chris Boulton, Samuel, Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 3.0
 * 
 */
 
// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Plugin add hooks

$plugins->add_hook('global_start', 'rules_page_toplink_start');
$plugins->add_hook('misc_start', 'rules_page');
$plugins->add_hook('fetch_wol_activity_end', 'rules_page_online_activity');
$plugins->add_hook('build_friendly_wol_location_end', 'rules_page_online_location');

// Plugin info

function rules_page_info()
{

    global $lang;

    $lang->load('rules_page');
    
    $lang->rules_page_Desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->rules_page_Desc;

    return Array(
        'name' => $lang->rules_page_Name,
        'description' => $lang->rules_page_Desc,
        'website' => $lang->rules_page_Web,
        'author' => $lang->rules_page_Auth,
        'authorsite' => $lang->rules_page_AuthSite,
        'version' => $lang->rules_page_Ver,
        'codename' => $lang->rules_page_CodeName,
        'compatibility' => $lang->rules_page_Compat
    );
}

// Plugin activation

function rules_page_activate()
{
	
	global $db, $lang;

// Plugin include language 

 $lang->load('rules_page');
	
	$rulespage = array(
		'name' => 'rules_page',
  'title'          => ''.$lang->rules_page_Setting_0_Title.'', 
  'description'    => ''.$lang->rules_page_Setting_0_Description.'',
		'disporder' => '403',
		'isdefault' => '0'
		);
		
	$group['gid'] = $db->insert_query('settinggroups', $rulespage);
	$gid = $db->insert_id();
	
	$ruleslist = array(
		'name' => 'rules_list',
  'title'          => ''.$lang->rules_page_Setting_1_Title.'', 
  'description'    => ''.$lang->rules_page_Setting_1_Description.'',
		'optionscode' => 'textarea',
		'value' =>       ''.$lang->rules_page_Setting_1_Value.'',
		'disporder' => '1',
		'gid' => intval($gid)
		);
		
	$db->insert_query('settings', $ruleslist);

	rebuild_settings();

	$insert_query[] = array("tid" => "0","title" => "misc_rules", "template" => $db->escape_string("<head>\n<title>{\$mybb->settings['bbname']} - {\$lang->rules_page_title}</title>\n{\$headerinclude}\n</head>\n<body>\n	{\$header}\n	<table border=\"0\" cellspacing=\"{\$theme['borderwidth']}\" cellpadding=\"{\$theme['tablespace']}\" class=\"tborder\" style=\"clear: both;\">\n	<tr>\n			<td class=\"thead\"><strong>{\$mybb->settings['bbname']} {\$lang->rules_page_title}</strong></td>\n		</tr>\n		<tr>\n			<td class=\"trow1\">{\$rules_list}</td>\n		</tr>\n	</table>\n	{\$footer}\n</body>\n</html>"), "sid" => "-1");

	$db->insert_query_multiple("templates", $insert_query);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('toplinks_help}</a></li>')."#i", 'toplinks_help}</a></li>
<li class="rules_link"><a href="{\$mybb->settings[\'bburl\']}/misc.php?action=rules" class="rules" style="background: url(images/toplinks/rules.png) no-repeat;" border="0" alt="" />{\$lang->rules_page_link}</a></li>');

}

// Plugin deactivation

function rules_page_deactivate()
{
	
	global $db;
	
	$db->delete_query("settinggroups", "name = 'rules_page'");

	$db->delete_query('templates', 'title = "misc_rules"');

	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'rules_list'
	)");

	rebuild_settings();

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("header", '#
<li class="rules_link">(.*)</li>#', '', 0);

}

// Plugin page

function rules_page()
{
	
	global $mybb, $templates, $theme, $header, $footer, $headerinclude, $rules, $lang;

// Plugin include language 

 $lang->load('rules_page');
  
	if($mybb->input['action'] != "rules")
	{
		return;
	}

	add_breadcrumb("{$lang->rules_page_breadcrumb}");

	$rules_list = nl2br($mybb->settings['rules_list']);

	eval("\$rules = \"".$templates->get("misc_rules")."\";");

	output_page($rules);

}

// Plugin online activity

function rules_page_online_activity($user_activity)
{
	
	global $parameters, $tid_list;
  	
	if($user_activity['activity'] == "misc" && $parameters['action'] == "rules")
	{
		
		$user_activity['activity'] = "misc_rules";
		
		return $user_activity;
	}

}

// Plugin online location

function rules_page_online_location($plugin_array)
{
	
	global $lang, $threads, $mybb, $rules;

// Plugin include language 

 $lang->load('rules_page');
 	
	if($plugin_array['user_activity']['activity'] == "misc_rules")
	{
		$plugin_array['location_name'] = "$lang->rules_page_online_viewing <a href=\"misc.php?action=rules\">$lang->rules_page_online_link</a>";		
		
		return $plugin_array;
	}
	
}
	
function rules_page_toplink_start()
{ 
	
	global $mybb, $templates, $theme, $header, $rules, $lang;

// Plugin include language 

 $lang->load('rules_page');
  
}

?>