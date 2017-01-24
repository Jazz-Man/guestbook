<?php
    namespace GB;

    /**
     * Class GB_Clean_UP_Theme
     *
     * @package GB
     */
    class GB_Clean_UP_Theme
    {

        /**
         * GB_Clean_UP_Theme constructor.
         */
        public function __construct()
        {
            add_action('init', [$this, 'head_cleanup']);
            add_filter('the_generator', '__return_false');
            add_filter('language_attributes', [$this, 'language_attributes']);
            add_filter('style_loader_tag', [$this, 'clean_style_tag']);
            add_filter('script_loader_tag', [$this, 'clean_script_tag']);
            add_filter('script_loader_src', [$this, 'remove_script_version'], 15);
            add_filter('style_loader_src', [$this, 'remove_script_version'], 15);
            add_filter('xmlrpc_methods', [$this, 'filter_xmlrpc_method']);
            add_filter('wp_headers', [$this, 'filter_headers']);
            add_filter('rewrite_rules_array', [$this, 'filter_rewrites']);
            add_filter('bloginfo_url', [$this, 'kill_pingback_url'], 10, 2);
            add_action('xmlrpc_call', [$this, 'kill_xmlrpc']);
        }

        public function head_cleanup()
        {
            remove_action('wp_head', 'feed_links_extra', 3);
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wp_shortlink_wp_head', 10);
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
            add_filter('use_default_gallery_style', '__return_false');
            global $wp_widget_factory;
            if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
                remove_action(
                    'wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']
                );
            }
        }

        public function rel_canonical()
        {
            global $wp_the_query;
            if ( ! is_singular()) {
                return;
            }
            if ( ! $id = $wp_the_query->get_queried_object_id()) {
                return;
            }
            $link = get_permalink($id);
            echo "\t<link rel=\"canonical\" href=\"$link\">\n";
        }

        /**
         * @return mixed|string|void
         */
        public function language_attributes()
        {
            $attributes = [];
            if (is_rtl()) {
                $attributes[] = 'dir="rtl"';
            }
            $lang = get_bloginfo('language');
            if ($lang) {
                $attributes[] = "lang=\"$lang\"";
            }
            $output = implode(' ', $attributes);
            $output = apply_filters('GB_Clean_UP_Theme/language_attributes', $output);

            return $output;
        }

        /**
         * @param $input
         *
         * @return string
         */
        public function clean_style_tag($input)
        {
            preg_match_all(
                "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input,
                $matches
            );
            if (empty($matches[2])) {
                return $input;
            }
            $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';

            return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
        }

        /**
         * @param $input
         *
         * @return mixed
         */
        public function clean_script_tag($input)
        {
            $input = str_replace("type='text/javascript' ", '', $input);

            return str_replace("'", '"', $input);
        }

        /**
         * @param $src
         *
         * @return bool|string
         */
        public function remove_script_version($src)
        {
            return $src ? esc_url(remove_query_arg('ver', $src)) : false;
        }

        /**
         * @param $methods
         *
         * @return mixed
         */
        public function filter_xmlrpc_method($methods)
        {
            unset($methods['pingback.ping']);

            return $methods;
        }

        /**
         * @param $headers
         *
         * @return mixed
         */
        public function filter_headers($headers)
        {
            if (isset($headers['X-Pingback'])) {
                unset($headers['X-Pingback']);
            }

            return $headers;
        }

        /**
         * @param array $rules
         *
         * @return array
         */
        public function filter_rewrites(array $rules)
        {
            foreach ($rules as $rule => $rewrite) {
                if (preg_match('/trackback\/\?\$$/i', $rule)) {
                    unset($rules[$rule]);
                }
            }

            return $rules;
        }

        /**
         * @param $output
         * @param $show
         *
         * @return string
         */
        public function kill_pingback_url($output, $show)
        {
            if ($show === 'pingback_url') {
                $output = '';
            }

            return $output;
        }

        /**
         * @param $action
         */
        public function kill_xmlrpc($action)
        {
            if ($action === 'pingback.ping') {
                wp_die('Pingbacks are not supported', 'Not Allowed!', ['response' => 403]);
            }
        }
    }
