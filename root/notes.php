<?php
/**
*
* @package User's notes
* @version $Id: notes.php
* @copyright (c) 2010 Senky
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/notes');

// check if user s logged in, since this page can be used only after registration...
if (!$user->data['is_registered'])
{
  login_box( request_var('redirect', "notes.$phpEx") );
}

// ... and also this is not for bots (especially for bad ones :)
if ($user->data['is_bot'])
{
  redirect( append_sid("{$phpbb_root_path}index.$phpEx") );
}

add_form_key('users_notes');

if ( isset($_POST['snote']) )
{
  if(!check_form_key('users_notes'))
  {
    trigger_error($user->lang['FORM_INVALID']);
  }
  $note = utf8_normalize_nfc( request_var('note', '', true) );
  $note_ready = $db->sql_escape($note);
  
  // and finaly adding new notes into database
  $db->sql_query("UPDATE " . USERS_TABLE . " SET user_note = '" . $note_ready . "' WHERE user_id = " . $user->data['user_id']);

  trigger_error( sprintf($user->lang['NOTES_SAVED'], append_sid("{$phpbb_root_path}notes.$phpEx")) );
}

// create a template variables
$template->assign_vars(array(
      'NOTE'               => $user->data['user_note'],
)); 

page_header($user->lang['NOTES']);

$template->set_filenames(array(
    'body' => 'notes.html',
));

page_footer();
?>
