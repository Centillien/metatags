<?php
/**
 * Elgg Metatags generator plugin
 * This plugin make the metatags for content.
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Gerard Kanters
 * Website: https://www.centillien.com
 */

require_once(dirname(__FILE__) . '/lib/functions.php');

elgg_register_event_handler('init', 'system', 'metatagsgen_init');

function metatagsgen_init()
{

    elgg_register_library('elgg:metatags', elgg_get_plugins_path() . 'metatags/lib/metadescription.php');

    elgg_extend_view('page/elements/head', 'metatagsgen/metatags');

    //Static caching of icons
    $cloudflare = elgg_get_plugin_setting("cloudflare", "metatags");
    if ($cloudflare == "yes") {
        elgg_register_plugin_hook_handler('entity:icon:url', 'user', 'metatags_user_icon_url_override');
    }

    //Unregister systemlog since it is not very usefull
    elgg_unregister_event_handler('log', 'systemlog', 'system_log_default_logger');
    elgg_register_plugin_hook_handler('view_vars', 'all', 'metatags_view_guid');
}

function metatags_user_icon_url_override($hook, $type, $returnvalue, $params)
{
    $user = $params['entity'];
    $size = $params['size'];

    if (isset($user->externalPhoto)) {
        // return thumbnail
        return $user->externalPhoto;
    } else {
        if (isset($user->icontime)) {
            return "avatar/view/$user->username/$size/$user->icontime.jpg";
        } else {
            return "_graphics/icons/user/default{$size}.gif";
        }
    }
}

function metatags_view_guid($hook, $type, $vars, $params)
{
    if (isset($vars['guid']) && get_input('guid', false) === false) {
        set_input('guid', $vars['guid']);
    }

    return $vars;
}
