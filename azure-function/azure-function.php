<?php

/**
 * Plugin Name:       Azure Function Plugin
 * Plugin URI:        https://jmccurry.azurewebsites.net
 * Description:       Simple Wordpress to Azure Function Hook
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Josh McCurry
 * Author URI:        https://jmccurry.azurewebsites.net
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    die;
}

require 'settings.php';


function call_azure_function($post_id)
{
    
    $options = get_option('azure_function_options');
    $url = $options['azure_function_field_url'].'?code='.$options['azure_function_field_key'];
    
    $wppost = get_post($post_id);
    if(strcmp($wppost->post_status, $options['azure_function_field_trigger'])){
        $data = $wppost->to_array();
    
        $ch = curl_init($url);
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}

add_action('post_updated', 'call_azure_function');
