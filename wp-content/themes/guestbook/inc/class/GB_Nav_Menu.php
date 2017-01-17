<?php
    namespace GB;

    /**
     * Class GB_Nav_Menu
     *
     * @package GB
     */
    class GB_Nav_Menu extends \Walker_Nav_Menu
    {

        public $options
            = [
                'template'       => '<li {ITEM_ATTRS}><a {LINK_ATTRS}>{TITLE}</a></li>',
                'secondary_menu' => '<a {LINK_ATTRS}>{TITLE}</a>'
            ];

        public $has_children;
        public $is_current_active_class;
        public $_current_item;
        public $_current_item_args;
        public $is_secondary;

        /**
         * @param string   $output
         * @param \WP_Post $item
         * @param int      $depth
         * @param array    $args
         * @param int      $id
         */
        public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
        {
            $this->setCurrentItem($item);
            $this->setCurrentItemArgs($args);
            $this->is_secondary = $this->_current_item_args['theme_location'] == 'secondary_menu';
            if ($this->_current_item['post_status'] !== 'publish') {
                return;
            }
            $indent = $depth ? str_repeat("\t", $depth) : '';
            $output .= $indent;
            $item_atr            = [];
            $item_atr['class'][] = $this->is_current_active_class ? 'active' : '';
            $link_atr            = [];
            $link_title          = apply_filters('the_title', $item->title, $item->ID);
            $link_atr['title']   = $item->attr_title ?: $item->title;
            $link_atr['target']  = $item->target ?: '_self';
            if ($item->type === 'post_type_archive') {
                $link_atr['href'] = get_post_type_archive_link($item->object);
            } else {
                $link_atr['href'] = $item->url;
            }
            if ($this->has_children) {
                $item_atr['class'][] = 'has-child';
                $link_atr['href']    = '#nav-wrapper-' . sanitize_title($this->_current_item['title']);
            }
            $link_atr = apply_filters('nav_menu_link_attributes', $link_atr, $item, $args, $depth);
            if ($this->is_secondary) {
                $template = str_replace('{TITLE}', $link_title, $this->options['secondary_menu']);
                $template = str_replace('{LINK_ATTRS}', GB_Helper::add_attr($link_atr), $template);
            } else {
                $template = $this->get_template_part('template');
                $template = str_replace('{TITLE}', $link_title, $template);
                $template = str_replace('{LINK_ATTRS}', GB_Helper::add_attr($link_atr), $template);
                $template = str_replace('{ITEM_ATTRS}', GB_Helper::add_attr($item_atr), $template);
            }
            $output .= apply_filters('walker_nav_menu_start_el', $template, $item, $depth, $args);
        }

        /**
         * @param $item
         */
        private function setCurrentItem($item)
        {
            $this->_current_item           = $this->_get_object_vars($item);
            $this->is_current_active_class = ! empty($this->_current_item) ? in_array(
                'current-menu-item', $this->_current_item['classes']
            ) : false;
        }

        /**
         * @param $object
         *
         * @return array
         */
        private function _get_object_vars($object)
        {
            return (array)get_object_vars($object);
        }

        /**
         * @param $args
         */
        public function setCurrentItemArgs($args)
        {
            $this->_current_item_args = $this->_get_object_vars($args);
            if ( ! empty($this->_current_item_args)) {
                if ( ! empty($this->_current_item_args['walker'])) {
                    $has_children       = $this->_get_object_vars($this->_current_item_args['walker']);
                    $this->has_children = $has_children['has_children'];
                } else {
                    $this->has_children = false;
                }
            }
        }

        /**
         * @param      $template
         * @param bool $start
         *
         * @return string
         */
        private function get_template_part($template, $start = true)
        {
            $template    = $this->options[$template];
            $parts       = explode('{SUB}', $template);
            $parts_count = count($parts);
            if ($parts_count === 2) {
                return $start ? $parts[0] : $parts[1];
            }
            $idx = strrpos($template, '</');
            if ($idx !== false) {
                return $start ? substr($template, 0, $idx) : substr($template, $idx);
            }
        }

        /**
         * @param string $output
         * @param int    $depth
         * @param array  $args
         */
        public function start_lvl(&$output, $depth = 0, $args = [])
        {
            $output .= '<div class="wrapper">';
            $output .= '<div id="nav-wrapper-' . sanitize_title($this->_current_item['title'])
                . '" class="nav-wrapper">';
            $output .= '<ul>';
        }

        /**
         * @param string   $output
         * @param \WP_Post $item
         * @param int      $depth
         * @param array    $args
         */
        public function end_el(&$output, $item, $depth = 0, $args = [])
        {
            $n      = isset($args->item_spacing) && 'discard' === $args->item_spacing ? '' : "\n";
            $end_el = $this->is_secondary ? '' : '</li>';
            $output .= "{$end_el}{$n}";
        }
    }