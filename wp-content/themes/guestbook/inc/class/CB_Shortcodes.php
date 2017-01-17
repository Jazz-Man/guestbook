<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 16:46
     */
    namespace GB;

    class CB_Shortcodes
    {

        /**
         * CB_Shortcodes constructor.
         */
        public function __construct()
        {
            $core_page = get_option('gb_core_pages');
            if ($core_page && is_array($core_page)) {
                foreach ((array)$core_page as $slug => $id) {
                    if (method_exists($this, "guestbook_{$slug}")) {
                        add_shortcode("guestbook_{$slug}", [$this, "guestbook_{$slug}"]);
                    }
                }
            }
        }

        public function guestbook_user($atts)
        {
            echo 'foo = ' . __CLASS__;
        }

        public function guestbook_login($atts)
        {
            echo 'foo = ' . __CLASS__;
        }

        public function guestbook_register($atts)
        {
            echo 'foo = ' . __CLASS__;
        }

        public function guestbook_logout($atts)
        {
            echo 'foo = ' . __CLASS__;
        }

        public function guestbook_account($atts)
        {
            echo 'foo = ' . __CLASS__;
        }

        public function guestbook_password($atts)
        {
            echo 'foo = ' . __CLASS__;
        }
    }