<?php
    function guestbook_body_classes($classes)
    {
        if (is_multi_author()) {
            $classes[] = 'group-blog';
        }
        if (!is_singular()) {
            $classes[] = 'hfeed';
        }

        return $classes;
    }

    add_filter('body_class', 'guestbook_body_classes');
    function guestbook_pingback_header()
    {
        if (is_singular() && pings_open()) {
            echo '<link rel="pingback" href="', esc_url(get_bloginfo('pingback_url')), '">';
        }
    }

    add_action('wp_head', 'guestbook_pingback_header');
