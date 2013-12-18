<?php
/**
 * Help text metatags description and title
 *
 * @package Metatags
 */

$english = array(
	//Mainpage settings
	'metatags:mainpage:title' => 'Provide a catching title for the index page',
	'metatags:mainpage:description' => 'Provide a catching description for your network. Tip, use existing words that are on the main page for better results',
	'metatags:mainpage:image' => 'Give the filename of an image that represents your site. This file must located be in the folder /_graphics',
	'metatags:cloudflare' => 'Do you want fixed avatar URLs ?',

	//setttings voor plugins
	'blog:message:description' => 'published a  news article',
	'blog:title:preformat' => 'Blog section - %s',
	'market:message:description' => 'wants to share this offering',
	'market:title:preformat' => '%s - advertising',
		
);

add_translation("en", $english);
