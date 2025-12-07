<?php
/**
 * Dummy Data: Conditions
 * 
 * Returns an array of condition configurations for testing rules.
 */

return array(
    'location_rules' => array(
        'basic_post' => array(
            'rule' => 'post_type',
            'operator' => '==',
            'value' => 'post',
        ),
        'specific_page' => array(
            'rule' => 'page',
            'operator' => '==',
            'value' => '123', // Page ID
        ),
        'category_archive' => array(
            'rule' => 'taxonomy',
            'taxonomy' => 'category',
            'operator' => '==',
            'value' => 'news', // Category slug or ID
        ),
        'front_page' => array(
            'rule' => 'general',
            'operator' => '==',
            'value' => 'is_front_page',
        ),
        'exclude_post' => array(
            'rule' => 'post',
            'operator' => '!=',
            'value' => '999',
        ),
    ),
    'user_rules' => array(
        'logged_in' => array(
            'rule' => 'user_status',
            'operator' => '==',
            'value' => 'logged_in',
        ),
        'administrator' => array(
            'rule' => 'user_role',
            'operator' => '==',
            'value' => 'administrator',
        ),
        'specific_user' => array(
            'rule' => 'user_id',
            'operator' => '==',
            'value' => '1',
        ),
    ),
    'mock_contexts' => array(
        // Contexts to simulate when testing rules
        'homepage' => array(
            'is_front_page' => true,
            'is_home' => true,
        ),
        'single_post' => array(
            'is_singular' => true,
            'post_type' => 'post',
            'post_id' => 10,
        ),
        'single_page' => array(
            'is_singular' => true,
            'post_type' => 'page',
            'post_id' => 123,
        ),
        'category_news' => array(
            'is_archive' => true,
            'taxonomy' => 'category',
            'term_id' => 5,
        ),
    ),
);
