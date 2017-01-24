<?php
    namespace GB;

    class CB_Shortcodes
    {

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
            echo \Template_Loader::load('accounts/user', $atts);
        }

        public function guestbook_login($atts)
        {
            echo \Template_Loader::load('accounts/login', $atts);
        }

        public function guestbook_register($atts)
        {
            echo \Template_Loader::load('accounts/register', $atts);
        }

        public function guestbook_logout($atts)
        {
            echo \Template_Loader::load('accounts/logout', $atts);
        }

        public function guestbook_account($atts)
        {
            echo \Template_Loader::load('accounts/account', $atts);
        }

        public function guestbook_password($atts)
        {
            echo \Template_Loader::load('accounts/password', $atts);
        }
    }