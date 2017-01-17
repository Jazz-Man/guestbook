<?php
    define('THEME_DIR', get_template_directory());
    define('INC_DIR', THEME_DIR . '/inc');
    define('CLASS_DIR', INC_DIR . '/class');
    $setup_theme = new Plugin_API_Manager('after_setup_theme');
    $setup_theme->addCallback(
        function () {
            load_theme_textdomain('guestbook', get_template_directory() . '/languages');
            add_theme_support('title-tag');
            add_theme_support('post-thumbnails');
            register_nav_menus(
                [
                    'primary_menu' => esc_html__('Primary', 'guestbook'),
                ]
            );
            add_theme_support(
                'html5', [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                ]
            );
        }
    );
    $enqueue_scripts = new Plugin_API_Manager('wp_enqueue_scripts');
    $enqueue_scripts->addCallback(
        function () {
            $theme_dir      = get_template_directory_uri();
            $theme_dir_css  = $theme_dir . '/assets/css';
            $theme_dir_js   = $theme_dir . '/assets/js';
            $google_lib_url = '//ajax.googleapis.com/ajax/libs/';
            $bootstrapcdn   = 'https://maxcdn.bootstrapcdn.com/';
            if (WP_DEBUG) {
                $guestbook_scripts_url = 'http://localhost:8081/js/index.js';
                $guestbook_style_url   = 'http://localhost:8081/css/index.css';
            } else {
                $guestbook_scripts_url = $theme_dir_js . '/index.js';
                $guestbook_style_url   = $theme_dir_css . '/index.css';
            }
            $jquery_version = wp_scripts()->registered['jquery']->ver;
            wp_deregister_script('jquery');
            wp_deregister_script('jquery-core');
            $register_js  = [
                [
                    'handle'   => 'jquery',
                    'src'      => "{$google_lib_url}jquery/{$jquery_version}/jquery.min.js",
                    'deps'     => '',
                    'in_foter' => false,
                    'enqueue'  => true,
                ],
                [
                    'handle'   => 'guestbook-scripts',
                    'src'      => $guestbook_scripts_url,
                    'deps'     => [],
                    'in_foter' => true,
                    'enqueue'  => true,
                ],
            ];
            $register_css = [
                [
                    'handle' => 'guestbook-fonts',
                    'src'    => add_query_arg(
                        [
                            'family' => 'Roboto+Condensed:400,700',
                            'subset' => 'cyrillic',
                        ], 'https://fonts.googleapis.com/css'
                    ),
                    'deps'   => '',
                ],
                [
                    'handle' => 'font-awesome',
                    'src'    => $bootstrapcdn . 'font-awesome/4.7.0/css/font-awesome.min.css',
                    'deps'   => 'upages-fonts',
                ],
                [
                    'handle' => 'guestbook-style',
                    'src'    => $guestbook_style_url,
                    'deps'   => 'guestbook-fonts',
                ],
            ];
            foreach ($register_js as $file_js) {
                wp_register_script($file_js['handle'], $file_js['src'], $file_js['deps'], null, $file_js['in_foter']);
                if ($file_js['enqueue'] === true) {
                    wp_enqueue_script($file_js['handle']);
                }
            }
            foreach ($register_css as $file_css) {
                wp_enqueue_style($file_css['handle'], $file_css['src'], $file_css['deps'], null);
            }
            if (is_singular()) {
                wp_enqueue_script('comment-reply');
            }
        }
    );
    $body_class = new Plugin_API_Manager('body_class');
    $body_class->addCallback(
        function ($body_class) {
            $whitelist = [
                'home',
                'error404',
                'blog',
                'archive',
                'search',
                'single',
                'author',
                'category',
                'tag',
                'page',
                'logged-in'
            ];

            return array_intersect($body_class, $whitelist);
        }
    );
    require INC_DIR . '/template-tags.php';
    require INC_DIR . '/extras.php';
    spl_autoload_register(
        function ($class) {
            $class = ltrim($class, '\\');
            if (0 !== stripos($class, 'GB\\')) {
                return;
            }
            $parts = explode('\\', $class);
            array_shift($parts);
            $last    = array_shift($parts) . '.php';
            $parts[] = $last;
            $objects = CLASS_DIR . '/' . implode($parts, '/');
            if (file_exists($objects)) {
                require_once $objects;
            }
        }
    );
    new \GB\GB_Init();

