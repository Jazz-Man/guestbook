<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 17:20
     */
    namespace GB;

    class GB_Filter
    {

        /**
         * GB_Filter constructor.
         */
        public function __construct()
        {
            add_action('display_nav_menu', [$this, 'display_nav_menu']);
        }

        public function display_nav_menu($menus = [])
        {
            $defaults = [];
            if ( ! empty($menus)) {
                $defaults[] = $menus;
            }
            if ( ! empty($defaults) && is_array($defaults)) {
                foreach ($defaults as $menu) {
                    wp_nav_menu($menu);
                }
            }
        }
    }