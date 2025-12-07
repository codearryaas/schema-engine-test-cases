<?php
/**
 * Dummy Data: Settings
 * 
 * Returns an array of global settings for the plugin.
 */

return array(
    'general' => array(
        'schema_type' => 'Organization', // Organization or Person
        'organization_name' => 'Global Corp',
        'organization_logo' => 'https://example.com/global-logo.png',
        'person_name' => '',
        'person_avatar' => '',
    ),
    'social' => array(
        'facebook' => 'https://facebook.com/globalcorp',
        'twitter' => 'https://twitter.com/globalcorp',
        'instagram' => 'https://instagram.com/globalcorp',
        'linkedin' => 'https://linkedin.com/company/globalcorp',
        'youtube' => 'https://youtube.com/globalcorp',
    ),
    'corporate_contact' => array(
        'phone' => '+1-800-555-0199',
        'contact_type' => 'customer support',
        'contact_option' => 'TollFree',
        'area_served' => 'US',
    ),
    'defaults' => array(
        'enable_breadcrumbs' => true,
        'enable_sitelinks_search' => true,
        'remove_hentry' => false,
        'pretty_print_json' => false,
    ),
    'advanced' => array(
        'minify_output' => true,
        'defragment_schema' => true,
    ),
);
