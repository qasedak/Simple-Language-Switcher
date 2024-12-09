<?php

// Register display names of all users with posting capabilities for translation
function register_users_display_names_for_translation() {
    // Get plugin settings
    $options = get_option('sls_settings');
    
    // Check if username translation is enabled
    if (empty($options['translate_usernames'])) {
        return;
    }

    // Get all users with necessary fields
    $users = get_users([
        'fields' => ['display_name', 'ID', 'user_login'],
        'capability__in' => ['edit_posts', 'publish_posts']
    ]);

    // Register each user's display name for translation
    foreach ($users as $user) {
        pll_register_string(
            'User Display Name - ' . $user->user_login,
            $user->display_name,
            'simple-language-switcher'
        );
    }
}
add_action('admin_init', 'register_users_display_names_for_translation');

// Filter to replace author's name with translated display name
function replace_author_with_display_name($display_name) {
    // Get plugin settings
    $options = get_option('sls_settings');
    
    // Check if username translation is enabled
    if (empty($options['translate_usernames'])) {
        return $display_name;
    }

    global $post;
    
    // First check if we have a valid post and post_author
    if (!$post || !$post->post_author) {
        return $display_name;
    }
    
    // Get the post author's data
    $author = get_userdata($post->post_author);
    
    // Get the translated display name if available, otherwise fallback to original display name
    $translated_name = pll__($display_name, 'simple-language-switcher');
    return !empty($translated_name) ? $translated_name : $author->display_name;
}
add_filter('the_author', 'replace_author_with_display_name');
add_filter('get_the_author_display_name', 'replace_author_with_display_name');
