<?php
/* Customizing phpicalendar configuration:

phpicalendar 2.3 should work with no additional configuration. This file can be changed to customize the behavior of phpicalendar.
In version 2.3, there has been a change in the way configuration works in order to reduce the number of confusing global variables.  Unfortunately, this means that config.inc.php files from older installations will have to be translated to the new format.  The conversion is simple: use the old variable names as array keys for the $configs array below.

To change basic settings, uncomment (remove the first '#') from the desired line and adjust the value.  For setting blacklists, autmatic webcals, locked calendars, and HTTP authorized calendars modify the arrays below the basic configuration section.

The commented out lines below include alternatives to the default settings.  Additional settings that can be overridden are in default_config.php

For more info and help, go to http://phpicalendar.net or email phpicalendar@gmail.com
*/

$configs = array(

/*     ========= BASIC CONFIGURATION =========
       ** Server configuration **

As noted, phpicalendar should work without adjusting the default settings.  Change these if you are having problems or want to change where things are found.  For example, it is often useful to have calendar_path in a different location.

       calendar_path is a FILE path
       default_path, cookie_uri, and download_uri are a URL paths, e.g. http://www.example.com/phpicalendar; set these if you are having problems.

Note that the allow_webcals setting allows webcals to be passed as URLs.  You do NOT need to override the default setting to list specific webcals for inclusion in the SPECIAL CALENDARS section below.

The salt parameter is used to obfuscate things like webcal links that may have usernames and passwords.  This should be changed.
*/
#     'calendar_path'        => '/Library/WebServer/Documents/phpicalendar/calendars/recur_tests',
#     'default_path'         => '', 	
#     'save_parsed_cals'     => 'yes', 
#     'cookie_uri'           => '', 
#     'download_uri'         => '', 	
#     'allow_webcals'          => 'yes',
#     'recursive_path'          => 'yes',
#     'salt'                => 'SaLt4',

/*     ** Timezones **
If timezone is not set, all events show in the local time of the source calendar.  This isn't a problem if all your calendars are in the same timezone.  If you set a timezone for the server, events in other timezones are shown when they occur at the server's time.
*/
#     'timezone'             => 'US/Central',
#     'second_offset'        => $secs,

/*     ** Appearance **      
In this section you can set how phpicalendar will display calendar views.

phpicalendar currently supports about 30 language variants.  For a list of supported languages, see the languages folder.


*/

#     'language'             => 'Spanish',
#     'default_cal'          => 'US Holidays',	   // Exact filename of calendar without .ics.
#     'template'             => 'green',           // Template support: change this to have a different "skin" for your installation.     
#    'default_view'         => 'year',           // Default view for calendars'     => 'day', 'week', 'month', 'year'
#      'printview_default'    => 'yes',	           // Set print view as the default view. Uses'default_view (listed above).
#     'gridLength'           => 10,                // Grid size in day and week views. Allowed values are 1,2,3,4,10,12,15,20,30,60. Default is 15
#     'minical_view'         => 'current',	       // Where do the mini-calendars go when clicked?'     => 'day', 'week', 'month', 'current'
#     'allow_preferences'    => 'no', 
#     'month_locations'      => 'no',
#     'show_search'          => 'yes',
#     'show_todos'           => 'no',
#     'show_completed'       => 'no',
#     'allow_login'          => 'yes',	           // Set to yes to prompt for login to unlock calendars.
#     'week_start_day'       => 'Monday',          // Day of the week your week starts on
#     'week_length'          => '5',	           // Number of days to display in the week view
#     'day_start'            => '0600',	           // Start time for day grid
#     'day_end'              => '2000',	           // End time for day grid
#      'event_download' => 'yes',


/*     ========= CALENDAR PUBLISHING =========

	This section is not needed if your calendars directory is accessible via WebDAV or CalDAV.  These settings 
	control the publish.php script provided in the calendars directory.  For more information, please see that
	file.
*/
/*
// To set values, change the text between the single quotes
// Follow instructions to the right for detailed information
$num_years 				= '1';				// Number of years (up and back) to display in 'Jump to'
$month_event_lines 		= '1';				// Number of lines to wrap each event title in month view, 0 means display all lines.
$tomorrows_events_lines = '1';				// Number of lines to wrap each event title in the 'Tommorrow's events' box, 0 means display all lines.
$allday_week_lines 		= '1';				// Number of lines to wrap each event title in all-day events in week view, 0 means display all lines.
$week_events_lines 		= '1';				// Number of lines to wrap each event title in the 'Tommorrow's events' box, 0 means display all lines.
$bleed_time				= '-1';				// This allows events past midnight to just be displayed on the starting date, only good up to 24 hours. Range from '0000' to '2359', or '-1' for no bleed time.
$charset				= 'UTF-8';			// Character set your calendar is in, suggested UTF-8, or iso-8859-1 for most languages.
$this_months_events 	= 'yes';			// Display "This month's events" at the bottom off the month page.
$enable_rss				= 'yes';			// Enable RSS access to your calendars (good thing).
$login_cookies			= 'no';			// Set to yes to store authentication information via (unencrypted) cookies. Set to no to use sessions.
$support_ical			= 'no';			// Set to yes to support the Apple iCal calendar database structure.
$tmp_dir				= '/tmp';			// The temporary directory on your system (/tmp is fine for UNIXes including Mac OS X). Any php-writable folder works.
$webcal_hours			= '24';				// Number of hours to cache webcals. Setting to '0' will always re-parse webcals.
$calendar_path 			= __WEBDAV_PATH__."/".__WEBSITE_CODENAME__."/";					// Leave this blank on most installs, place your full FILE SYSTEM PATH to calendars if they are outside the phpicalendar folder.

*/

	'default_path'			=> '', 				// The HTTP URL to the PHP iCalendar directory, ie. http://www.example.com/phpicalendar
	'save_parsed_cals'		=> 'no',				// Saves a copy of the cal in /tmp after it's been parsed. Improves performance.
	'cookie_uri'			=> '', 				// The HTTP URL to the PHP iCalendar directory, ie. http://www.example.com/phpicalendar -- AUTO SETTING -- Only set if you are having cookie issues.
	'download_uri'			=> '', 				// The HTTP URL to your calendars directory, ie. http://www.example.com/phpicalendar/calendars -- AUTO SETTING -- Only set if you are having subscribe issues.
	'allow_webcals' 		=> 'no',				// Allow http:// and webcal:// prefixed URLs to be used as the $cal for remote viewing of "subscribe-able" calendars. This does not have to be enabled to allow specific ones below.
	'recursive_path'		=> 'no',			// Set to yes to recurse into subdirectories of the calendar path.
	'timezone '				=> '',				// Set timezone. Read TIMEZONES file for more information
	'second_offset'			=> '',				// The time in seconds between your time and your server's time.

	'language' 				=> 'French',		// Language support - 'English', 'Polish', 'German', 'French', 'Dutch', 'Danish', 'Italian', 'Japanese', 'Norwegian', 'Spanish', 'Swedish', 'Portuguese', 'Catalan', 'Traditional_Chinese', 'Esperanto', 'Korean'
	/*'default_cal'			=> $_SESSION["user"]->infos["login"],		// Exact filename of calendar without .ics. Or set to $ALL_CALENDARS_COMBINED to open all calenders combined into one.
*/	'template'				=> 'default',		// Template support
	'default_view'			=> 'week',			// Default view for calendars = 'day', 'week', 'month', 'year'
	'printview_default'		=> 'no',				// Set print view as the default view. day, week, and month only supported views for $default_view (listed well above).
	'gridLength' 			=> '15',				// Grid distance in minutes for day view, multiples of 15 preferred
	'minical_view' 			=> 'current',		// Where do the mini-calendars go when clicked? = 'day', 'week', 'month', 'current'
	'allow_preferences'		=> 'yes',			// Allow visitors to change various preferences via cookies.
	'show_search'			=> 'no',			// Show the search box in the sidebar.
	'show_todos'			=> 'no',			// Show your todo list on the side of day and week view.
	'show_completed	'		=> 'no',				// Show completed todos on your todo list.
	'allow_login'			=> 'no',				// Set to yes to prompt for login to unlock calendars.
	'week_start_day' 		=> 'Monday',			// Day of the week your week starts on
	'week_length'			=> '7',				// Number of days to display in the week view
	'day_start' 			=> '0700',			// Start time for day grid
	'day_end'				=> '2300',			// End time for day grid
	
	'phpicalendar_publishing'=> 1,





);
/*     ========= SPECIAL CALENDARS =========

these arrays provide extra calendar options.

       ** Blacklisted Calendars

       
*/
$blacklisted_cals = array(
''
);                          
/*     ========= SPECIAL CALENDARS =========

these arrays provide extra calendar options.

*/
$list_webcals = array(
#	'webcal://dimer.tamu.edu/calendars/seminars/Biochem.ics'
);
$more_webcals['recur_tests'] = array();
$locked_cals = array(

);
/*     ========= SPECIAL CALENDARS =========

these arrays provide extra calendar options.

*/
$locked_map['user1:pass'] = array('');             // Map username:password accounts to locked calendars that should be
$locked_map['user2:pass'] = array('');             // unlocked if logged in. Calendar names should be the same as what is
$locked_map['user3:pass'] = array('');             // listed in the $locked_cals, again without the .ics suffix.
$locked_map['user4:pass'] = array('');             // Example: $locked_map['username:password'] = array('Locked1', 'Locked2');
// add more lines as necessary

$apache_map['user1'] = array('');                  // Map HTTP authenticated users to specific calendars. Users listed here and
$apache_map['user2'] = array('');                  // authenticated via HTTP will not see the public calendars, and will not be
$apache_map['user3'] = array('');                  // given any login/logout options. Calendar names not include the .ics suffix.
$apache_map['user4'] = array('');                  // Example: $apache_map['username'] = array('Calendar1', 'Calendar2');
