<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 15:45
     */
    namespace GB;

    class GB_Setup
    {

        public $core_pages = [];

        /**
         * GB_Setup constructor.
         */
        public function __construct()
        {
            $core_pages       = [
                'user'     => ['title' => 'User'],
                'login'    => ['title' => 'Login'],
                'register' => ['title' => 'Register'],
                'logout'   => ['title' => 'Logout'],
                'account'  => ['title' => 'Account'],
                'password' => ['title' => 'Password Reset']
            ];
            $this->core_pages = (array)apply_filters('gb_core_pages', $core_pages);
            add_action('init', [$this, 'installCorePages']);
        }

        public function installCorePages()
        {
            if (current_user_can('manage_options')/* && !get_option('gb_is_installed')*/) {
                update_option('gb_is_installed', 1);
                foreach ($this->core_pages as $slug => $array) {
                    $page_exists = GB_Query::find_post_id('page', '_gb_core', $slug);
                    if ( ! $page_exists) {
                        $content   = "[guestbook_{$slug}]";
                        $user_page = [
                            'post_title'     => $array['title'],
                            'post_content'   => $content,
                            'post_name'      => $slug,
                            'post_type'      => 'post',
                            'post_status'    => 'publish',
                            'post_author'    => get_current_user_id(),
                            'comment_status' => 'closed',
                        ];
                        $post_id   = wp_insert_post($user_page);
                        wp_update_post(['ID' => $post_id, 'post_type' => 'page']);
                        update_post_meta($post_id, '_gb_core', $slug);
                        $core_pages[$slug] = $post_id;
                    }
                }
                if (isset($core_pages)) {
                    update_option('gb_core_pages', $core_pages);
                    $options = get_option('gb_options');
                    foreach ($core_pages as $o_slug => $page_id) {
                        $options['core_' . $o_slug] = $page_id;
                    }
                    if (isset($options)) {
                        update_option('gb_options', $options);
                    }
                }
            }

        }
    }