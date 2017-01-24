<?php
    namespace GB;

    /**
     * Class GB_Account
     *
     * @package GB
     */
    class GB_Account
    {
        public $core_tabs;

        /**
         * GB_Account constructor.
         */
        public function __construct()
        {
            $this->setCoreTabs();
            add_action('account_nav_tabs', [$this, 'accountNavTabs']);
            add_action('account_content_tabs', [$this, 'accountCcontentTabs']);
            add_action('gb_login_form', [$this, 'gb_login_form']);
            add_action('gb_register_form', [$this, 'gb_register_form']);
            add_action('comment_post_redirect', [$this, 'comment_post_redirect']);
        }

        public function setCoreTabs()
        {
            $tabs['comments'] = [
                'name' => __('My Comments', 'guestbook'),
                'icon' => 'fa fa-comment',
            ];
            $this->core_tabs  = apply_filters('account_core_nav_tabs', $tabs);
            update_option('account_core_nav_tabs', $this->core_tabs);
        }

        /**
         * @return array
         */
        public static function mainProfileTab()
        {
            $_res  = [];
            $_tabs = get_option('account_core_nav_tabs');
            if ($_tabs && is_array($_tabs)) {
                foreach ((array)$_tabs as $key => $val) {
                    $_res[$key] = $val['name'] ?? $key;
                }
            }

            return $_res;
        }

        public function accountNavTabs()
        {
            if ( ! empty($this->core_tabs)) {
                echo \Template_Loader::load('accounts/account_nav_tabs', $this->core_tabs);
            }
        }

        public function accountCcontentTabs()
        {
            if ( ! empty($this->core_tabs)) {
                echo \Template_Loader::load('accounts/account_content_tabs', $this->core_tabs);
            }
        }

        /**
         * @return \GB\GB_Form
         */
        public static function getLoginForm()
        {
            $_options = get_option('_guestbook-options_options');
            if ($_options && null !== $_options['after-login']) {
                $redirect = get_permalink($_options['after-login']);
            } else {
                $redirect = get_permalink();
            }
            $redirect = add_query_arg(['profiletab' => 'comments'], $redirect);
            $login    = new GB_Form(
                'login', [
                    'redirect' => $redirect,
                    'submit'   => [
                        'title' => __('Log In')
                    ]
                ]
            );
            do_action('gb_login_form', $_POST);

            return $login;
        }

        /**
         * @return \GB\GB_Form
         */
        public static function getRegisterForm()
        {
            $_options = get_option('_guestbook-options_options');
            if ($_options && null !== $_options['after-register']) {
                $redirect = get_permalink($_options['after-register']);
            } else {
                $redirect = get_permalink();
            }
            $redirect = add_query_arg(['profiletab' => 'comments'], $redirect);
            $register = new GB_Form(
                'register', [
                    'redirect' => $redirect,
                    'submit'   => [
                        'title' => __('Register')
                    ]
                ]
            );
            do_action('gb_register_form', $_POST);

            return $register;
        }

        public static function getCommentAddForm()
        {
            $_options = get_option('_guestbook-options_options');
            $_author  = new GB_User();
            echo $comment = new GB_Form(
                'comment_add', [
                    'comment' => [
                        'comment_author'       => $_author->getDisplayName(),
                        'comment_author_email' => $_author->getEmail(),
                        'comment_author_url'   => $_author->getField('user_url'),
                        'comment_post_ID'      => $_options['boock']
                    ],
                    'submit'=>[
                        'title'         => __( 'Leave a Reply' )
                    ]
                ]
            );

        }

        public static function getCommentEditForm()
        {
            $id       = $_POST['cid'];
            $_options = get_option('_guestbook-options_options');
            $_author  = new GB_User();
            $content  = get_comment_text($id);
            echo $comment = new GB_Form(
                'comment_edit', [
                    'comment' => [
                        'action'               => 'editedcomment',
                        'content'              => $content,
                        'comment_author'       => $_author->getDisplayName(),
                        'comment_author_email' => $_author->getEmail(),
                        'comment_author_url'   => $_author->getField('user_url'),
                        'comment_ID'           => $id,
                        'comment_post_ID'      => $_options['boock']
                    ]
                ]
            );
        }

        /**
         * @param $location
         *
         * @return false|string
         */
        public function comment_post_redirect($location)
        {
            $option = get_option('_guestbook-options_options');

            return get_permalink($option['after-comment']);
        }

        /**
         * @param $args
         *
         * @return int|void|\WP_Error
         */
        public function gb_register_form($args)
        {
            if (empty($args)) {
                return '';
            }
            if ($args['request'] !== '' || ! wp_verify_nonce($args['gb_form'], 'gb_form')) {
                echo GB_Helper::notice('spam', 'Привіт спам :))');
            }
            switch ($args) {
                case empty($args['log']) || empty($args['pwd']) || empty($args['user_email']):
                    echo GB_Helper::notice('field', 'Заповніть поля, позначені <strong>*</strong>');
                    break;
                case strlen($args['log']) < 4:
                    echo GB_Helper::notice(
                        'username_length',
                        "Ім'я користувача <strong>{$args['log']}</strong> є коротке. Потрібно мінімум 4-и символи"
                    );
                    break;
                case ! validate_username($args['log']):
                    echo GB_Helper::notice('username_invalid', 'В імені користувача використані неприпустимі символи!');
                    break;
                case strlen($args['pwd']) < 5:
                    echo GB_Helper::notice('password', 'Довжина пароля повинна бути більше 5');
                    break;
                case $args['pwd'] !== $args['pass2']:
                    echo GB_Helper::notice('password2', 'Повторний пароль невірний');
                    break;
                case ! is_email($args['user_email']):
                    echo GB_Helper::notice(
                        'email_invalid', "Електронна пошта <strong>{$args['user_email']}</strong> не коректний."
                    );
                    break;
                default:
                    break;

            }
            $new_user = wp_create_user($args['log'], $args['pwd'], $args['user_email']);
            if (is_wp_error($new_user)) {
                echo GB_Helper::notice($new_user->get_error_code(), $new_user->get_error_message());
            } else {
                $u                  = [];
                $u['user_login']    = $args['log'];
                $u['user_password'] = $args['pwd'];
                $u['remember']      = true;
                $auth               = wp_signon($u, false);
                if (is_wp_error($auth)) {
                    echo GB_Helper::notice($auth->get_error_code(), $auth->get_error_message());
                } elseif (isset($args['redirect_to'])) {
                    wp_safe_redirect(esc_url($args['redirect_to']));
                }
            }

            return $new_user;
        }

        /**
         * @param $args
         */
        public function gb_login_form($args)
        {
            if (empty($args)) {
                return;
            }
            if ( ! empty($args) && ($args['request'] !== '' || ! wp_verify_nonce($args['gb_form'], 'gb_form'))) {
                echo GB_Helper::notice('spam', 'Привіт спам :))');
            }
            $u                  = [];
            $u['user_login']    = $args['log'];
            $u['user_password'] = $args['pwd'];
            $u['remember']      = $args['rememberme'];
            $auth               = wp_signon($u, false);
            if (is_wp_error($auth)) {
                echo GB_Helper::notice('errors', $auth->get_error_message());
            } else {
                if (isset($args['redirect_to'])) {
                    wp_safe_redirect(esc_url($args['redirect_to']));
                }
            }

        }

    }
