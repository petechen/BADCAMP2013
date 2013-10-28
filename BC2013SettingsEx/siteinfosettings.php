<?php
/******************************************************************
 * INCLUDED VARIABLES SET GLOBALLY FOR ENGINEERING DRUPAL SITES   *
 * DO NOT CHANGE ANYTHING BELOW                                   *
 *                                                                *
 * To use this soesettings.php files as an include to the regular *
 * settings.php file, define the following variables on the main  *
 * settings.php file: 
 * 
 * $siteinfo = array (
 *   'title'      => '', // default title for this site
 *   'dept'        => '', // department name (short)
 *   'dept_full'   => '', // department name (long)
 *   'enviro'      => '',  // environment name e.g. local, dev, stage, prod
 *   'version'     => '',  // This variable is optional. For internal control. Leave empty if not needed
 *   'site_prod'   => "",  // The production common url
 *   'admin_email' => '',  // drupal administrator email
 *   'drupalver'   => ,  // version of drupal
 *   'multisite'   => FALSE, // specify if a single site or multisite
 *   'id_format'   => '', // dept, eng, 
 *   'solrsearch'  => FALSE, // is there an apachesolr search config attached to this site? 
 * );
 * ****************************************************************/

if ($siteinfo['multisite'] == TRUE) {
  $site_common = $siteinfo['dept'].'-common';  //  10-18-13 going back to $siteinfo['dept'].'-common' less confusion.   
}
elseif ($siteinfo['multisite'] == FALSE) {
  $site_common = 'default';
}
// Setting these environments helps localize the sites/* folder to save files for that specific environment.
switch ($siteinfo['enviro']) {
  case 'local':
  case 'local7':
    $site_url = $siteinfo['dept'].'.'.$siteinfo['enviro']; // for a local environment
    break;
  case 'dev':
    $site_url = $siteinfo['dept'].'-'.$siteinfo['enviro'].'.yourdomain.com';  // replace "yourdomain.com" with your actual domain.  
    break;
  case 'stage':
    $site_url = $siteinfo['dept'].'-'.$siteinfo['enviro'].'.yourdomain.com';  // replace "yourdomain.com" with your actual domain.
    break;
  case 'prod':
    $site_url = $siteinfo['dept'].'-'.$siteinfo['enviro'].'.yourdomain.com';  // replace "yourdomain.com" with your actual domain.
    break;
  default:
    $site_url = "default";  //  Just use default folder if no environment specified
}
// The following two variables establish the two base locations where files can be saved.
// $site_dir contains files that will be rsynched between environments and thus can be 
// sourced either from the 'default' (single site) or 'production site url' (multi site) 
// directory as a way of keeping files common. n.b. in D7, there are multiple files
// folders. Make sure to define them all in drush aliases. 
// $enviro_dir is mostly BAM files specific to $siteinfo['enviro'] and don't get rsynched. 
$site_dir   = 'sites/'.$siteinfo['dept'].'/'.$site_common;
$enviro_dir = 'sites/'.$site_url.'/files_'.$siteinfo['enviro'];


// BADCAMP 2013 - THIS SECTION, based on $siteinfo['id_format'] is really only applies to
// themeing options.  If you use a "site-slogan" then this is useful.  
// This helps set variables for the site name and slogan in the Drupal header. 
switch ($siteinfo['id_format']) {
  case 'dept':
    $conf['site_name']    = $siteinfo['title'];
    # on the Open Framework derived themes, use $conf['site_slogan'] to specify 
    # the sub-name in the header, e.g. department. 
    if ($siteinfo['version']) {
      $conf['site_slogan']  = $siteinfo['dept_full'].' ('.strtoupper($siteinfo['enviro']).')'.' conf '.$siteinfo['version'];
    }
    else {
    if ($siteinfo['enviro'] !== 'prod') {
      $conf['site_slogan']  = $siteinfo['dept_full'].' ('.strtoupper($siteinfo['enviro']).')';
      }
      elseif ($siteinfo['enviro'] == 'prod') {
      $conf['site_slogan']    = $siteinfo['dept_full'];
    	}
		}
    break;
  default:  // to be safe, just give this a default setting.
    if ($siteinfo['enviro'] !== 'prod') {
      $conf['site_name']    = $siteinfo['dept_full'].' ('.strtoupper($siteinfo['enviro']).')';
    } elseif ($siteinfo['enviro'] == 'prod') {
      $conf['site_name']    = $siteinfo['dept_full'];
    }
    $conf['site_slogan']  = ''; // blank in this case
}

// Setting up paths for file directories including "files," "files_private" (7)
// and "tmp" directories based on single site (sites/default) or multisite (url)
// schema.  
//  n.b. in some circumstances, you may have to verify that your web server (apache/www)
// has the correct permissions to create and write to these directories under sites/. 
// 
// Verify on /admin/config/media/file-system  (or /admin/reports/status) that these
// directories are writable.  Otherwise, you may need to just go into your sites/ 
// folder and chmod/chown to get the folders established correctly. Easy fix, but a
// common trip up. 

if ($siteinfo['multisite'] == TRUE) {
  # DIRECTORY SETTINGS FOR MULTISITE ENVIRONMENT 
  $conf['file_directory_path']  = $site_dir.'/files';
    if ($siteinfo['drupalver'] == 6) {
    $conf['file_directory_temp'] = $enviro_dir.'/tmp';
  } elseif ($siteinfo['drupalver'] == 7) {
    $conf['file_temporary_path'] = $enviro_dir.'/tmp';
  }
  $conf['file_public_path']     = $site_dir.'/files';
  $conf['file_private_path']    = $site_dir.'/files_private';
} elseif ($siteinfo['multisite'] == FALSE) {
  # DIRECTORY SETTINGS FOR SINGLE-SITE ENVIRONMENT
  $conf['file_directory_path'] = 'sites/default/files';
  if ($siteinfo['drupalver'] == 6) {
    $conf['file_directory_temp'] = 'sites/default/files/tmp';
  } elseif ($siteinfo['drupalver'] == 7) {
    $conf['file_temporary_path'] = 'sites/default/files/tmp';
  }
  $conf['file_public_path'] = 'sites/default/files';
  $conf['file_private_path'] = 'sites/default/files_private';
}


// BACKUP AND MIGRATE SETTINGS ARRAY
// Using this array automatically sets up a set of backup and migrate
//  - destinations  (manual,  hourly, & daily folders)
//  - profiles     ( BAM_BACKUP)
//  - schedules ( hourly - keey 36, daily - keep 30 )
//  If you are having directory/folder writing issues see the n.b. on file 
// directories in the previous section (file directory paths). 


$conf['backup_migrate_destinations_defaults'][] = array(
	'type' 						=> 'file_manual',
	'destination_id' 	=> 'manual-'.$siteinfo['dept'].'-'.$siteinfo['enviro'],
	'name'						=> $site_url.' Manual Backups',
	'location'				=> $enviro_dir.'/backup_migrate/manual',
	'settings'				=> array (
		'chmod' 	=> '',
		'chown'		=> '',
		'chgrp'		=> '',
		),
	);
$conf['backup_migrate_destinations_defaults'][] = array(
	'type' 						=> 'file_scheduled',
	'destination_id' 	=> 'hourly-'.$siteinfo['dept'].'-'.$siteinfo['enviro'],
	'name'						=> $site_url.' Hourly Backups',
	'location'				=> $enviro_dir.'/backup_migrate/hourly',
	'settings'				=> array (
		'chmod' 	=> '',
		'chown'		=> '',
		'chgrp'		=> '',
		),
	);
$conf['backup_migrate_destinations_defaults'][] = array(
	'type' 						=> 'file_scheduled',
	'destination_id' 	=> 'daily-'.$siteinfo['dept'].'-'.$siteinfo['enviro'],
	'name'						=> $site_url.' Daily Backups',
	'location'				=> $enviro_dir.'/backup_migrate/daily',
	'settings'				=> array (
		'chmod' 	=> '',
		'chown'		=> '',
		'chgrp'		=> '',
		),
	);			
$conf['backup_migrate_profiles_defaults'][] = array(
  'profile_id' 			=> 'BAM_BACKUP',
  'name' 						=> 'My Backup',
  'exclude_tables' 	=> array(),
  'source_id' =>  'db',
#  'nodata_tables' => array(
#    'cache' => 'cache',
#    'cache_filter' => 'cache_filter',
#    'cache_menu' => 'cache_menu',
#    'cache_page' => 'cache_page',
#    'cache_views' => 'cache_views',
#    'devel_queries' => 'devel_queries',
#    'devel_times' => 'devel_times',
#    'sessions' => 'sessions',
#    'watchdog' => 'watchdog',
#  ),
  'filename'				 	=> $siteinfo['dept'].'-'.$siteinfo['enviro'],
  'append_timestamp' 	=> '1',
  'timestamp_format' 	=> 'Y-m-d\\TH-i-s',
  'filters'						=> array(
    'compression' 					=> 'gzip',
    'notify_success_enable' => '',
    'notify_success_email' 	=> '',
    'notify_failure_enable' => '1',
    'notify_failure_email' 	=> $siteinfo['admin_email'],
  ),
);	
$conf['backup_migrate_schedules_defaults'][] = array(
  'schedule_id' 		=> 'BACKUP_hourly',
  'name' 						=> 'Hourly '.$site_url,
  'destination_id' 	=> 'hourly-'.$siteinfo['dept'].'-'.$siteinfo['enviro'],
  'profile_id'	 		=> 'BAM_BACKUP',
  'keep' 						=> '36',
  'period' 					=> '3600',
  'last_run' 				=> '0',
  'enabled' 				=> '1',
  'cron' 						=> '0',
);
$conf['backup_migrate_schedules_defaults'][] = array(
  'schedule_id' 		=> 'BACKUP_daily',
  'name' 						=> 'Daily '.$site_url,
  'destination_id' 	=> 'daily-'.$siteinfo['dept'].'-'.$siteinfo['enviro'],
  'profile_id'	 		=> 'BAM_BACKUP',
  'keep' 						=> '30',
  'period' 					=> '86400',
  'last_run' 				=> '0',
  'enabled' 				=> '1',
  'cron' 						=> '0',
);



# Common General Settings
// Generally, who to contact
$conf['site_mail'] = $siteinfo['admin_email'];
if ($siteinfo['drupalver'] == 6) {
    $conf['date_default_timezone'] = -25200; // numerically stored in D6
  } elseif ($siteinfo['drupalver'] == 7) {
    $conf['date_default_timezone'] = 'America/Los_Angeles'; // stored as location in D7
  }

# If for some reason, you have a custom path for some of your theme resources, like a custom 
# logo and/or favicon that is not already specified in your theme folder, you can specify them
# here.  This is sometimes done thorugh the UI at /admin/appearance/settings/[theme_name] under
#  "Logo Image Settings" and "Shortcut Icon Settings"
#  Since "uploading" these icons/images via the UI typically places them in the */files folder
# the most straightforward example to do this specifes a path in the */files foler. 
  
  // BADCAMP 2013- commenting this theme setting section out as most people won't need to use
  // this but it is quite handy if you are not using a logo/favicon built in the theme package
  // folder itself. 
  
//  $conf['theme_[name-of-theme]_settings']= array (   // substitute [name-of-theme] with whatever 
//      // theme you're setting this for e.g. omega, adaptivetheme, zen, your custom theme. 
//      'default_logo'    => '0',
//      'logo_path'		=> $site_dir.'/files/[name-of-your-logo-file]',  // replace [name-of-your-logo-file] with the actual path/name of your file. 
//      'default_favicon' => '0',
//      'favicon_path'    => $site_dir.'/files/[name-of-your-facicon-file]',  // replace [name-of-your-favicon-file] with the actual path/name of your file. 
//      );

  
  
/* Performance overrider (6=Pressflow) 
 * These settings are useful for a local or dev system
 * so that a themer/develper does not need to keep
 * shutting off optimization at /admin/settings/performance 
 * */
if ($siteinfo['enviro'] == ('local' || 'dev')) {
$conf['cache'] = '0';  // '0' for off
$conf['preprocess_css'] = '0';  // '0' for off
$conf['preprocess_js'] = '0';  // '0' for off
}


/* Error handling settings /admin/config/development/logging
 * Set this up so that errors do not show on prod
 */
if ($siteinfo['enviro'] == 'prod') {
  $conf['error_level'] = '0';  // 0=none; 1=Errors&Warnings; 2=all; use 0 on prod
}

/* Hard configurations for update notifications concerning modules, core etc
 * when there are updates security or otherwise to be made.
 */
if ($siteinfo['enviro'] == 'prod' || 'local' || 'local7') {
$conf['update_notify_emails'] = array(
    $siteinfo['admin_email'],  // or stick in a list of your devs emails here. They'll love it.
    );
} else {
  $conf['update_notify_emails'] = array(
    'noone@example.com', // just a default nowhere address. Because this is  an array, you can put
      // multiple emails in this section. 
    );
}
$conf['update_check_frequency'] = '1'; // '1' for daily, '7' for weekly
$conf['update_notification_threshold'] = 'all'; //'all' for all version 'security'
// for security notifications only

