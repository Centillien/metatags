<?php
/**
 * Elgg Metatags generator plugin
 * This plugin make the metatags for content.
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Gerard Kanters
 * Website: https://www.centillien.com
 */
$page = get_entity(get_input('guid'));
$site_name = elgg_get_site_entity()->name;
$context = elgg_get_context();
$title = str_replace(elgg_get_config('sitename'), '', str_replace(' : ', '', $vars['title']));
$user = elgg_get_page_owner_entity();
global $my_page_entity;
$offset = sanitise_int(get_input("offset", 0), false);
if (isset($my_page_entity->tags)) {
    $tags_array = (array)$my_page_entity->tags;
    if (!empty($tags_array)) {
        $tags = implode(",", $my_page_entity->tags);
    }
} else {
    if (($page->tags) && ($context != 'profile')) {
        $tags_array = (array)$my_page_entity->tags;
        if (!empty($tags_array)) {
            $tags = implode(",", $page->tags);
        }
    }
}


switch ($context) {
    case (empty($vars['title'])):
        $title = elgg_get_plugin_setting("mainpage_title", "metatags");
        $meta_description = elgg_get_plugin_setting("mainpage_description", "metatags");
        $meta_description = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9\_|+.-]/', ' ', urldecode(html_entity_decode(strip_tags($meta_description))))));
        break;
    case 'about':
        $title = "Over Elgg";
        $meta_description = "Elgg is een sociaal intranet applicatie. We bieden daarmee de kracht van social media aan in het eigen bedrijf.";
        break;
    case 'terms':
        $title = "NetCare algemene voorwaarden";
        $meta_description = "Het gebruik van de Elgg demo site is gratis.Deze site is een demonstratie omgeving die volledig functioneel is en u mag naar hartelust experimenteren.";
        break;
    case 'privacy':
        $title = "Privacy beleid";
        $meta_description = "Privacy is gewaarborgd voor zover de gebruiker informatie zelf niet publiek maakt.";
        break;
    case 'groups':
        $title = $title . " - " . $site_name . " - Netwerk groepen";
        break;
    case 'members':
        switch ($title) {
            case 'Meest populaire leden':
                $options['relationship'] = 'friend';
                $options['inverse_relationship'] = false;
                $options['offset'] = $offset;
                $options['limit'] = 10;
                $users = elgg_get_entities_from_relationship_count($options);
                $meta_description = "Positions " . $offset . " - " . (10 + $offset) . ": ";
                foreach ($users as $u) {
                    $u->name = str_replace(',', "", $u->name);
                    $meta_description = $meta_description . "" . $u->name . ", ";
                }
                $title = $title . " - " . $site_name . " - Werken met plezier";
                break;
        }
        break;
    case 'profile':
        switch ($user) {
            case (empty($user->name)):
                $meta_description = $title . " - " . elgg_get_plugin_setting("mainpage_description", "metatags");
                $title = $title . " - " . elgg_get_plugin_setting("mainpage_title", "metatags");
                break;
            case (empty($user->description)):
                $briefdescription = urldecode(html_entity_decode(strip_tags($user->briefdescription)));
                $briefdescription = str_replace(array(
                    "\r",
                    "\n"
                ), '', $briefdescription);
                $meta_description = substr($briefdescription, 0, 150) . " - " . $user->name . " is lid van  $site_name";
                $title = $user->name . " - " . $user->location;
                break;
            case (!empty($user->description)):
                $description = urldecode(html_entity_decode(strip_tags($user->description)));
                $description = str_replace(array(
                    "\r",
                    "\n"
                ), '', $description);
                $meta_description = substr($description, 0, 133) . " - " . $user->name . " is lid van $site_name";
                $title = $user->name . " - " . $user->location;
                break;
            case (!empty($user->briefdescription)):
                $briefdescription = urldecode(html_entity_decode(strip_tags($user->briefdescription)));
                $briefdescription = str_replace(array(
                    "\r",
                    "\n"
                ), '', $briefdescription);
                $meta_description = substr($briefdescription, 0, 133) . " - " . $user->name . " is lid van $site_name";
                break;
        }
        break;
    case 'blog':
    case 'file':
    case 'videos':
    case 'bookmarks':
    case 'pages':
        if (!empty($user->name)) {
	    $meta_description = elgg_get_excerpt($page->description);
	    $meta_description = urldecode(html_entity_decode(strip_tags($meta_description)));
            $meta_description = str_replace(array(
                "\r",
                "\n"
            ), '', $meta_description);
            $title = $title . " from " . $user->name . " - " . $user->location;
        } else {
            $title_context = substr($context, -1, 1) == 's' ? $context : $context . 's';
            $meta_description = ucfirst($context) . " lijst: " . $site_name . ". Deze pagina geeft een overzicht van alle {$title_context}";
            $title = $title . " - " . $site_name;
        }
        break;
   case 'demand':
   case 'jobs':
   case 'market':
        if (!empty($user->name)) {
            $meta_description = elgg_get_excerpt($page->description);
            $meta_description = urldecode(html_entity_decode(strip_tags($meta_description)));
            $meta_description = str_replace(array(
                "\r",
                "\n"
            ), '', $meta_description);
            $meta_description = substr($meta_description, 0, 200);
            $title = $title . " - " . $user->name . " - " . $user->location;
        } else {
            $meta_description = "$context op $site_name. ";
            $title = $title . " - " . $site_name . " - Werken met plezier";
        }
        break;
    case 'thewire':
        if (!empty($user->name)) {
            $title = $title . " - " . $user->location;
            $message = "these short messages";
            $meta_description = $user->name . " deeelt " . $message . " met je op $site_name";
        } else {
            $meta_description = "$site_name microblogs. Een lijst weergave van korte berichten";
            $title = $title . " - $site_name - Werken met plezier";
        }
        break;
    case 'group_profile':
        $meta_description = $user->name . " is een groep op $site_name. Als je lid wilt worden van deze groep, moet je eerst registreren op $site_name";
        break;
    case 'friends':
        if (!empty($user->name)) {
            $options = array(
                'relationship' => 'friend',
                'relationship_guid' => $user->getGUID(),
                'inverse_relationship' => FALSE,
                'limit' => false,
                'type' => 'user',
                'full_view' => FALSE
            );
            $num_friends = count(elgg_get_entities_from_relationship($options));
            $options['offset'] = $offset;
            $options['limit'] = 7;
            $friends = elgg_get_entities_from_relationship($options);
            if ($friends) {
                $meta_description = $user->name . " heeft " . $num_friends . "  connecties op  $site_name. Misschien ken je " . $user->name . " ook of ";
                foreach ($friends as $u) {
                    $u->name = str_replace(',', "", $u->name);
                    $meta_description = $meta_description . "" . $u->name . ", ";
                }
            } else {
                $meta_description = $user->name . " heeft nog geen connecties op  $site_name. Misschien ken je " . $user->name . ". Registreer op  $site_name en maak een connectie";
            }
            $title = $title . " - $site_name - Werken met plezier";
        }
        break;
    case (empty($user->name) || ($user->name== $site_name)):
        $meta_description = elgg_get_plugin_setting("mainpage_description", "metatags");
        $title = elgg_get_plugin_setting("mainpage_title", "metatags");;
        break;
    default:
        $clear = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9\_|+.-]/', ' ', urldecode(html_entity_decode(strip_tags($page->description))))));
        $meta_description = substr($clear, 0, 200);
        $meta_description = str_replace(array(
            "\r",
            "\n"
        ), '', $meta_description);
        if ($user->location) {
            $title = $title . " - " . $user->name . " - " . $user->location;
        } else {
            $title = $title . " - " . $user->name;
        }
        break;
}
$contexts = array(
    'front',
    'index',
    'main',
    'null',
    ''
);

//Replace double quotes to single quotes
$meta_description = str_replace('"', "'", $meta_description);
$title = str_replace('"', "'", $title);

if ($meta_description) {
    ?>
    <meta name="description" property="og:description" content="<?php
    if (strpos($title, ' - ') == 0) {
        $meta_description = str_replace(' - ', '', $meta_description);
    }
    echo $meta_description;
    ?>"/>
    <?php
}
?>
<title><?php
    echo $title;
    ?></title>
<meta name="author" content="<?php
if (empty($user->name)) {
    echo $site_name;
} else {
    echo $user->name;
}
?>"/>
<html prefix="og: http://ogp.me/ns#">
<meta property="og:type" content="article"/>
<meta property="og:site_name" content="<?php
echo $site_name;
?>"/>
<meta property="og:title" content="<?php
$title = trim($title);
if (strpos($title, '- ') == 0) {
    $title = str_replace('- ', '', $title);
}
echo $title;
?>"/>
<meta property="og:url" content="<?php
echo current_page_url();
?>"/>
<meta property="og:image" content="<?php
$mainpage_image = elgg_get_plugin_setting("mainpage_image", "metatags");
if (!empty($user->name) && !in_array($context, $contexts)) {
    echo $user->getIconURL('large');
} else if (strpos($mainpage_image, '://') !== false) {
    echo $mainpage_image;
} else {
    echo elgg_get_site_url() . "_graphics/" . $mainpage_image;
}
?>"/>
<link rel="author" href="<?php
echo $user->website;
?>"/>
<meta name="robots" content="index,follow"/>
<meta name="keywords" content="<?php
if ($tags && !in_array($context, $contexts)) {
    echo $context, ",", $tags, ",", $user->name, ",", $user->location;
} else {
    echo elgg_get_plugin_setting("mainpage_keywords", "metatags");
}
?>"/>
