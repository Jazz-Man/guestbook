<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 15:56
     */
    namespace GB;

    class GB_Query
    {
        public $wp_pages = [];
        public $roles    = [];

        public static function make($args)
        {
            $defaults = [
                'post_type'   => 'post',
                'post_status' => ['publish'],
            ];
            $args     = wp_parse_args($args, $defaults);
            if (isset($args['post__in']) && empty($args['post__in'])) {
                return false;
            }
            $custom_posts = new \WP_Query();
            $custom_posts->query($args);

            return $custom_posts;
        }

        public function get_recent_users($number = 5)
        {
            global $wpdb;
            $args  = ['fields' => 'ID', 'number' => $number, 'orderby' => 'user_registered', 'order' => 'desc'];
            $users = new WP_User_Query($args);

            return $users->results;
        }

        public function count_users_by_status($status)
        {
            $args = ['fields' => 'ID', 'number' => 0];
            if ($status == 'unassigned') {
                $args['meta_query'][] = [['key' => 'account_status', 'compare' => 'NOT EXISTS']];
                $users                = new WP_User_Query($args);
                foreach ($users->results as $user) {
                    update_user_meta($user, 'account_status', 'approved');
                }
            } else {
                $args['meta_query'][] = [['key' => 'account_status', 'value' => $status, 'compare' => '=']];
            }
            $users = new WP_User_Query($args);

            return count($users->results);
        }

        public function get_users_by_status($status, $number = 5)
        {
            global $wpdb;
            $args                 = [
                'fields'  => 'ID',
                'number'  => $number,
                'orderby' => 'user_registered',
                'order'   => 'desc',
            ];
            $args['meta_query'][] = [
                [
                    'key'     => 'account_status',
                    'value'   => $status,
                    'compare' => '=',
                ],
            ];
            $users                = new WP_User_Query($args);

            return $users->results;
        }

        public function get_role_by_userid($user_id)
        {
            $role = get_user_meta($user_id, 'role', true);
            if ( ! $role) {
                $role = $user_id == get_current_user_id() && current_user_can('edit_users') ? 'admin' : 'member';
            }

            return $role;
        }

        public function count_users()
        {
            $result = count_users();

            return $result['total_users'];
        }

        public function count_users_by_role($role)
        {
            global $wpdb;
            $args['fields']     = 'ID';
            $args['meta_query'] = [
                [
                    'key'     => 'role',
                    'value'   => $role,
                    'compare' => '=',
                ],
            ];
            $users              = new WP_User_Query($args);

            return count($users->results);
        }

        public function update_attr($key, $post_id, $new_value)
        {
            update_post_meta($post_id, '_um_' . $key, $new_value);
        }

        public function get_attr($key, $post_id)
        {
            return get_post_meta($post_id, '_um_' . $key, true);
        }

        public function delete_attr($key, $post_id)
        {
            return delete_post_meta($post_id, '_um_' . $key);
        }

        public function has_post_meta($key, $value = null, $post_id = null)
        {
            if ( ! $post_id) {
                global $post;
                $post_id = $post->ID;
            }
            if ($value) {
                if (get_post_meta($post_id, $key, true) == $value) {
                    return true;
                }
            } else {
                if (get_post_meta($post_id, $key, true)) {
                    return true;
                }
            }

            return false;
        }

        public static function find_post_id($post_type, $key, $value)
        {
            $posts = get_posts(['post_type' => $post_type, 'meta_key' => $key, 'meta_value' => $value]);
            if (isset($posts[0]) && ! empty($posts)) {
                return $posts[0]->ID;
            }

            return false;
        }

        public function post_data($post_id)
        {
            $array['form_id'] = $post_id;
            $mode             = $this->get_attr('mode', $post_id);
            $meta             = get_post_custom($post_id);
            foreach ($meta as $k => $v) {
                if (strstr($k, '_um_' . $mode . '_')) {
                    $k         = str_replace('_um_' . $mode . '_', '', $k);
                    $array[$k] = $v[0];
                } elseif ($k == '_um_mode') {
                    $k         = str_replace('_um_', '', $k);
                    $array[$k] = $v[0];
                } elseif (strstr($k, '_um_')) {
                    $k         = str_replace('_um_', '', $k);
                    $array[$k] = $v[0];
                }
            }
            foreach ($array as $k => $v) {
                if (strstr($k, 'login_') || strstr($k, 'register_') || strstr($k, 'profile_')) {
                    if ($mode != 'directory') {
                        unset($array[$k]);
                    }
                }
            }

            return $array;
        }

        public function get_meta_value($key, $array_key = null, $fallback = null)
        {
            //            global $post;
            $post_id = get_the_ID();
            $try     = get_post_meta($post_id, $key, true);
            if (isset($try) && ! empty($try)) {
                if (is_array($try) && in_array($array_key, $try)) {
                    return $array_key;
                } elseif (is_array($try)) {
                    return '';
                } else {
                    return $try;
                }
            }
            if ($fallback == 'na') {
                $fallback = 0;
                $none     = '';
            } else {
                $none = 0;
            }

            return ( ! empty($fallback)) ? $fallback : $none;
        }

        public function is_core($post_id)
        {
            $is_core = get_post_meta($post_id, '_um_core', true);

            return $is_core != '' ? $is_core : false;
        }

        public function get_roles($add_default = false, $exclude = null)
        {
            $exclude_str = '';
            if (null !== $exclude && is_array($exclude)) {
                $exclude_str = implode('_', $exclude);
            }
            if (isset($this->roles['is_add_default_' . $add_default]['is_exclude_' . $exclude_str])) {
                return $this->roles['is_add_default_' . $add_default]['is_exclude_' . $exclude_str];
            }
            $roles = [];
            if ($add_default) {
                $roles[0] = $add_default;
            }
            $args  = [
                'post_type'      => 'um_role',
                'posts_per_page' => -1,
                'post_status'    => ['publish'],
            ];
            $posts = get_posts($args);
            if ($posts) {
                foreach ($posts as $post) {
                    if ($this->is_core($post->ID)) {
                        $roles[$this->is_core($post->ID)] = $post->post_title;
                    } else {
                        $roles[$post->post_name] = $post->post_title;
                    }
                }
            } else {
                $roles['member'] = 'Member';
                $roles['admin']  = 'Admin';
            }
            if ($exclude) {
                foreach ($exclude as $role) {
                    unset($roles[$role]);
                }
            }
            $this->roles['is_add_default_' . $add_default]['is_exclude_' . $exclude_str] = $roles;

            return $roles;
        }
    }