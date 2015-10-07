<?php
 
/*
 Wiki Feed Generator for MediaWiki
 Gregory Szorc <gregory.szorc@gmail.com>
 
 This library is free software; you can redistribute it and/or
 modify it under the terms of the GNU Lesser General Public
 License as published by the Free Software Foundation; either
 version 2.1 of the License, or (at your option) any later version.
 
 This library is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 Lesser General Public License for more details.
 
 You should have received a copy of the GNU Lesser General Public
 License along with this library; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 
 
 Directions:
 This script requires MediaWiki 1.5+ and PHP 5 to run.  It was developed against MediaWiki
 1.8.2 and PHP 5.1.6.  If it doesn't work, upgrade!
 
 To use this script, copy it to your extensions/ subdirectory inside the MediaWiki install.
 Add the following in LocalSettings.php:
 
 include_once('SpecialWikiFeeds.php');
 
 This script also needs my GenericXmlSyndicationFeed class, which can be found at
 http://opensource.case.edu/svn/MediaWikiHacks/classes/GenericXmlSyndicationFeed/GenericXmlSyndicationFeed.php
 
 You will need to manually include this file before the require/include SpecialWikiFeeds.php
 in LocalSettings.php or else when this file loads, you will get a big, fat error message.
 
 Example LocalSettings.php entry:
 
 require("$IP/extensions/GenericXmlSyndicationFeed.php");
 require("$IP/extensions/SpecialWikiFeeds.php");
 $wgWikiFeedsSettings['cacheEnable'] = true;
 
 Once WikiFeeds is enabled in LocalSettings.php,
 go to Special:WikiFeeds in your wiki.  Everything should be set!
 
 WikiFeeds can be slightly customized.  Settings which can be changed are located
 in the $wgWikiFeedsSettings array (defined and documentation below).  If you wish
 to change a setting, re-set it in LocalSettings.php, after including this file
 (see above example)
 
 If you encounter a bug, please file it at http://opensource.case.edu/projects/MediaWikiHacks
 or e-mail me.
 
 Other:
 The script supports ATOM 1.0 better than RSS 2.0.  ATOM is the future.  I'm not
 wasting my time adding full support for RSS.
 
 ToDo:
 Use MediaWiki language support through system messages (partially done)
 Better error checking
 Optimize SQL queries
 
 */
 
if (!defined('MEDIAWIKI')) die();
 
$wgExtensionCredits['specialpage'][] = array(
  'name'=>'Wiki Feeds',
  'author'=>'Gregory Szorc <gregory.szorc@gmail.com>',
  'url'=>'http://wiki.case.edu/User:Gregory.Szorc',
  'description'=>'Produces syndicated feeds for MediaWiki.',
  'version'=>'0.5'
);

$wgAutoloadClasses[ 'SpecialWikiFeeds' ] = __DIR__ . '/SpecialWikiFeeds.php'; # Location of the SpecialMyExtension class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles[ 'WikiFeeds' ] = __DIR__ . '/WikiFeeds.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgSpecialPages[ 'WikiFeeds' ] = 'SpecialWikiFeeds'; # Tell MediaWiki about the new special page and its class name
 
/**
 * Holds default settings for WikiFeeds
 *
 * Override values in LocalSettings.php after you include this file
 */
$wgWikiFeedsSettings = array(
	'cacheEnable' => false, //whether to enable the cache
	'cacheRoot'		=> '/nfsn/content/ds-x/public/tmp/', //cache directory, with trailing slash
	'cacheMaxAge'	=> 600, //max age of cached files, in seconds
	'cachePruneFactor' => 100, //prune stale cache entries 1 out of every this many requests
    'watchlistPrivate' => false, //when true, make per-user watchlists require special access token
);
 
