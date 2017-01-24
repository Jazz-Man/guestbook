<?php
    namespace Upages_Objects;

    /**
     * Class Post
     *
     * @package Upages_Objects
     */
    class Post extends Attachment
    {

        public static $post_type = 'post';

        public $post;

        /**
         * Post constructor.
         *
         * @param null $p
         */
        public function __construct($p = null)
        {
            if (null !== $p) {
                switch ($p) {
                    case is_object($p):
                        $this->post = $p;
                        break;
                    case is_numeric($p):
                        $this->post = get_post($p);
                        break;

                }
            } else {
                global $post;
                $this->post = $post;
            }
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title)
        {
            $this->setField('post_title', $title);
        }

        /**
         * @param $key
         * @param $value
         */
        public function setField($key, $value)
        {
            global $wpdb;
            $wpdb->update($wpdb->posts, [$key => $value], ['ID' => $this->getId()]);
            clean_post_cache($this->getId());
            $this->post = get_post($this->getId());
        }

        /**
         * @return int
         */
        public function getId()
        {
            return (int)$this->getField('ID');
        }

        /**
         * @param $key
         *
         * @return mixed
         */
        public function getField($key)
        {
            return $this->post->$key;
        }

        /**
         * @param string $slug
         */
        public function setSlug(string $slug)
        {
            $this->setField('post_name', $slug);
        }

        /**
         * @param string $status
         */
        public function setStatus(string $status)
        {
            $this->setField('post_status', $status);
        }

        /**
         * @param string $excerpt
         */
        public function setExcerpt(string $excerpt)
        {
            $this->setField('post_excerpt', $excerpt);
        }

        /**
         * @param string $content
         */
        public function setContent(string $content)
        {
            $this->setField('post_content', $content);
        }

        /**
         * @param $author
         */
        public function setAuthor($author)
        {
            if (is_numeric($author)) {
                $author = new User($author);
            }
            $this->setField('post_author', $author::getId());
        }

        public function setDdate($post_date)
        {
            $this->setField('post_date', date('Y-m-d H:i:s', strtotime($post_date)));
        }

        /**
         * @param $post_date_gmt
         */
        public function setDateGmt($post_date_gmt)
        {
            $this->setField('post_date_gmt', date('Y-m-d H:i:s', strtotime($post_date_gmt)));
        }

        /**
         * @param $post_modified
         */
        public function setModified($post_modified)
        {
            $this->setField('post_modified', date('Y-m-d H:i:s', strtotime($post_modified)));
        }

        /**
         * @param $post_modified_gmt
         */
        public function setModifiedGmt($post_modified_gmt)
        {
            $this->setField('post_modified_gmt', date('Y-m-d H:i:s', strtotime($post_modified_gmt)));
        }

        /**
         * @param int $parent_id
         */
        public function setParent(int $parent_id)
        {
            $this->setField('post_parent', $parent_id);
        }

        /**
         * @param int $featured_image_id
         */
        public function setFeaturedImageId(int $featured_image_id)
        {
            $this->setMeta('_thumbnail_id', $featured_image_id);
        }

        /**
         * @param $key
         * @param $value
         */
        public function setMeta($key, $value)
        {
            update_post_meta($this->getId(), $key, $value);
        }

        public function setCategories($categories)
        {
            $this->setTaxonomyTerms('category', $categories);
        }

        /**
         * @param $taxonomy
         * @param $terms
         *
         * @return bool
         */
        public function setTaxonomyTerms($taxonomy, $terms)
        {
            if ( ! is_array($terms)) {
                return false;
            }
            $first_term = $terms[0];
            if (is_object($first_term)) {
                $terms = wp_list_pluck($terms, 'name');
            }
            foreach ($terms as $term) {
                if ( ! get_term_by('name', $term, $taxonomy)) {
                    wp_insert_term($term, $taxonomy);
                }
            }
            wp_set_object_terms($this->getId(), array_map('sanitize_title', $terms), $taxonomy);
        }

        /**
         * @param $tags
         */
        public function setTags($tags)
        {
            $this->setTaxonomyTerms('post_tag', $tags);
        }

        /**
         * @param $type
         */
        public function setPostType($type)
        {
            set_post_type($this->getId(), $type);
        }

        public function getEditLink()
        {
            return get_edit_post_link($this->getId());
        }

        /**
         * @param bool $force_delete
         *
         * @return string|void
         */
        public function getDeleteLink($force_delete = false)
        {
            return get_delete_post_link($this->getId(), '', $force_delete);
        }

        /**
         * @param bool   $in_same_cat
         * @param string $excluded_categories
         *
         * @return null|string|\WP_Post
         */
        public function getNextPost($in_same_cat = false, $excluded_categories = '')
        {
            return get_next_post($in_same_cat, $excluded_categories);
        }

        /**
         * @return mixed
         */
        public function getPostType()
        {
            return $this->getField('post_type');
        }

        /**
         * @return string
         */
        public function getCommentStatus()
        {
            return (string)$this->getField('comment_status');
        }

        /**
         * @param string $class
         *
         * @return string
         */
        public function getPostClass($class = '')
        {
            $post_id   = $this->getId();
            $post_type = $this->getPostType();
            $classes   = [];
            if ($class) {
                if ( ! is_array($class)) {
                    $class = preg_split('#\s+#', $class);
                }
                $classes = array_map('esc_attr', $class);
            } else {
                $class = [];
            }
            $classes[] = "post-{$post_id}";
            if ( ! is_admin()) {
                $classes[] = $post_type;
            }
            $classes[] = "type-{$post_type}";
            $classes[] = "status-{$this->getStatus()}";
            if (post_type_supports($post_type, 'post-formats')) {
                $post_format = get_post_format($post_id);
                if ($post_format && ! is_wp_error($post_format)) {
                    $classes[] = 'format-' . sanitize_html_class($post_format);
                } else {
                    $classes[] = 'format-standard';
                }
            }
            $post_password_required = post_password_required($post_id);
            if ($post_password_required) {
                $classes[] = 'post-password-required';
            } elseif ( ! empty($this->getPassword())) {
                $classes[] = 'post-password-protected';
            }
            if (current_theme_supports('post-thumbnails') && $this->hasThumbnail() && ! is_attachment($this->post)
                && ! $post_password_required
            ) {
                $classes[] = 'has-post-thumbnail';
            }
            if (is_sticky($post_id)) {
                if (is_home() && ! is_paged()) {
                    $classes[] = 'sticky';
                } elseif (is_admin()) {
                    $classes[] = 'status-sticky';
                }
            }
            $classes[]  = 'hentry';
            $taxonomies = get_taxonomies(['public' => true]);
            foreach ((array)$taxonomies as $taxonomy) {
                if (is_object_in_taxonomy($post_type, $taxonomy)) {
                    foreach ((array)get_the_terms($post_id, $taxonomy) as $term) {
                        if (empty($term->slug)) {
                            continue;
                        }
                        $term_class = sanitize_html_class($term->slug, $term->term_id);
                        if (is_numeric($term_class) || ! trim($term_class, '-')) {
                            $term_class = $term->term_id;
                        }
                        if ('post_tag' == $taxonomy) {
                            $classes[] = "tag-{$term_class}";
                        } else {
                            $classes[] = sanitize_html_class(
                                "{$taxonomy}-{$term_class}", "{$taxonomy}-{$term->term_id}"
                            );
                        }
                    }
                }
            }
            $classes = array_map('esc_attr', $classes);
            $classes = apply_filters('post_class', $classes, $class, $post_id);
            $classes = array_unique($classes);

            return implode(' ', $classes);
        }

        /**
         * @return mixed
         */
        public function getPingStatus()
        {
            return $this->getField('ping_status');
        }

        /**
         * @return mixed
         */
        public function getGuid()
        {
            return $this->getField('guid');
        }

        public function getMenuOrder()
        {
            return $this->getField('menu_order');
        }

        /**
         * @return mixed
         */
        public function getPassword()
        {
            return $this->getField('post_password');
        }

        /**
         * @return mixed
         */
        public function getToPing()
        {
            return $this->getField('to_ping');
        }

        /**
         * @return mixed
         */
        public function getPinged()
        {
            return $this->getField('pinged');
        }

        /**
         * @return mixed
         */
        public function getCommentCount()
        {
            return $this->getField('comment_count');
        }

        /**
         * @param $key
         */
        public function deleteMeta($key)
        {
            delete_post_meta($this->getId(), $key);
        }

        public function theTitle()
        {
            echo apply_filters('the_title', $this->getTitle());
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            $title = $this->getField('post_title') ?? '';
            if ( ! is_admin()) {
                if ( ! empty($this->getPassword())) {
                    $protected_title_format = apply_filters('protected_title_format', __('Protected: %s'), $this->post);
                    $title                  = sprintf($protected_title_format, $title);
                } elseif ('private' === $this->getStatus()) {
                    $private_title_format = apply_filters('private_title_format', __('Private: %s'), $this->post);
                    $title                = sprintf($private_title_format, $title);
                }
            }

            return (string)apply_filters('the_title', $title, $this->getId());
        }

        /**
         * @return string
         */
        public function getSlug()
        {
            return (string)$this->getField('post_name');
        }

        /**
         * @return string
         */
        public function getStatus()
        {
            return (string)$this->getField('post_status');
        }

        public function the_excerpt()
        {
            echo apply_filters('the_excerpt', $this->getExcerpt());
        }

        /**
         * @return string
         */
        public function getExcerpt()
        {
            return (string)$this->getField('post_excerpt');
        }


        public function theContent()
        {
            $content = $this->getContent();
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            echo $content;
        }

        /**
         * @return string
         */
        public function getContent()
        {

            if (post_password_required($this->post)) {
                return get_the_password_form($this->post);
            }

            return force_balance_tags($this->getField('post_content'));
        }

        /**
         * @return mixed
         */
        public function getAuthor()
        {
            return $this->getField('post_author');
        }

        /**
         * @return mixed|void
         */
        public function thePermalink()
        {
            return apply_filters('the_permalink', $this->getPermalink());
        }

        /**
         * @return false|string
         */
        public function getPermalink()
        {
            return get_permalink($this->getId());
        }

        /**
         * @return mixed
         */
        public function getDate()
        {
            return $this->getField('post_date');
        }

        /**
         * @return mixed
         */
        public function getDateGmt()
        {
            return $this->getField('post_date_gmt');
        }

        /**
         * @return mixed
         */
        public function getModified()
        {
            return $this->getField('post_modified');
        }

        /**
         * @return mixed
         */
        public function getModifiedGmt()
        {
            return $this->getField('post_modified_gmt');
        }

        /**
         * @return int
         */
        public function getParent()
        {
            return (int)$this->getField('post_parent');
        }

        /**
         * @param $key
         *
         * @return mixed
         */
        public function getMeta($key)
        {
            return get_post_meta($this->getId(), $key, true);
        }

        /**
         * @param string $size
         *
         * @return false|string
         */
        public function getThumbnailUrl($size = 'post-thumbnail')
        {
            return wp_get_attachment_image_url($this->getThumbnailId(), $size);
        }

        /**
         * @return bool
         */
        public function hasThumbnail()
        {
            return (bool)$this->getThumbnailId();
        }

        /**
         * @return mixed
         */
        public function getAllMeta()
        {
            return get_post_meta($this->getId(), '', true);
        }

        /**
         * @return array|false|\WP_Error
         */
        public function getCategories()
        {
            return $this->getTaxonomyTerms('category');
        }

        /**
         * @param $taxonomy
         *
         * @return array|bool|mixed|void|\WP_Error
         */
        public function getTaxonomyTerms($taxonomy)
        {
            $id    = $this->getId();
            $terms = get_object_term_cache($id, $taxonomy);
            if (false === $terms) {
                $terms = wp_get_object_terms($id, $taxonomy);
                if ( ! is_wp_error($terms)) {
                    $term_ids = wp_list_pluck($terms, 'term_id');
                    wp_cache_add($id, $term_ids, $taxonomy . '_relationships');
                }
            }
            $terms = apply_filters('get_the_terms', $terms, $id, $taxonomy);
            if (empty($terms)) {
                return false;
            }

            return $terms;
        }

        /**
         * @param string $output
         *
         * @return array
         */
        public function getTaxonomiNames($output = 'names')
        {
            return get_object_taxonomies($this->post, $output);
        }

        /**
         * @return array|bool|mixed|void|\WP_Error
         */
        public function getTags()
        {
            return $this->getTaxonomyTerms('post_tag');
        }

        /**
         * @param array|null $args
         *
         * @return bool
         */
        public static function create(array $args = null)
        {
            $defaults = [
                'post_type'   => static::$post_type,
                'post_status' => 'draft',
                'post_author' => User::getId(),
            ];
            $args     = array_merge($defaults, $args);
            add_filter('wp_insert_post_empty_content', '__return_false');
            $post_id = wp_insert_post($args);
            remove_filter('wp_insert_post_empty_content', '__return_false');
            if ( ! $post_id) {
                return false;
            }
            $class = get_called_class();

            return new $class($post_id);
        }
    }
