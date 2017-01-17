<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 21:36
     */
    namespace GB;

    class GB_Html_Core
    {

        const TARGET_BLANK = '_blank';

        const TARGET_SELF = '_self';

        const TARGET_PARENT = '_parent';

        const TARGET_TOP = '_top';

        const FLAG_OPENING = 'flag_opening';

        const FLAG_CLOSING = 'flag_closing';

        const REL_ALTERNATE = 'alternate';

        const REL_AUTHOR = 'author';

        const REL_BOOKMARK = 'bookmark';

        const REL_HELP = 'help';

        const REL_LICENSE = 'license';

        const REL_NEXT = 'next';

        const REL_NOFOLLOW = 'nofollow';

        const REL_NOREFERRER = 'noreferrer';

        const REL_PREFETCH = 'prefetch';

        const REL_PREV = 'prev';

        const REL_SEARCH = 'search';

        const REL_TAG = 'tag';

        public    $spase       = ' ';
        protected $_tag;
        private   $_attributes = [];
        private   $_contents   = [];
        private   $_html       = '';
        private   $_flags      = [];
        private   $_voidElements
                               = [
                'area',
                'base',
                'basefont',
                'br',
                'col',
                'command',
                'embed',
                'frame',
                'hr',
                'img',
                'input',
                'keygen',
                'link',
                'meta',
                'param',
                'source',
                'track',
                'wbr'
            ];
        private   $_allowed_tags
                               = [
                'a',
                'abbr',
                'address',
                'area',
                'article',
                'aside',
                'audio',
                'b',
                'blockquote',
                'br',
                'button',
                'canvas',
                'caption',
                'cite',
                'code',
                'col',
                'colgroup',
                'datalist',
                'dd',
                'del',
                'details',
                'dfn',
                'dialog',
                'div',
                'dl',
                'dt',
                'em',
                'embed',
                'fieldset',
                'figcaption',
                'figure',
                'footer',
                'form',
                'frameset',
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'head',
                'header',
                'hr',
                'i',
                'iframe',
                'img',
                'input',
                'ins',
                'kbd',
                'keygen',
                'label',
                'legend',
                'li',
                'main',
                'map',
                'mark',
                'menu',
                'meter',
                'nav',
                'noscript',
                'object',
                'ol',
                'optgroup',
                'option',
                'p',
                'param',
                'pre',
                'progress',
                'q',
                'rp',
                'rt',
                'ruby',
                's',
                'samp',
                'section',
                'select',
                'small',
                'source',
                'span',
                'strike',
                'strong',
                'sub',
                'summary',
                'sup',
                'table',
                'tbody',
                'td',
                'textarea',
                'tfoot',
                'th',
                'thead',
                'time',
                'tr',
                'track',
                'u',
                'ul',
                'var',
                'video',
                'wbr'
            ];

        public function __construct($tag, $attributes = null, $content = null)
        {
            if ( ! $this->isAllowedTags($tag)) {
                wp_die("Tag <b>$tag</b> is not allowed");
            }
            $this->_tag = $tag;
            $this->setAttributes($attributes);
            $this->setContent($content);
            $this->setHtml();
        }

        public function setContent($content)
        {
            if (null === $content) {
                $this->_contents = [];

                return $this;
            }
            if ( ! is_array($content)) {
                $content = [$content];
            }
            $this->_contents = $content;

            return $this;
        }

        protected function setHtml()
        {
            $return = '';
            if ($this->_tag) {
                $void = $this->isVoid() ? '/' : '';
                $return .= "<{$this->_tag}{$this->renderAttributes()}{$void}>";
            }
            $content = $this->_getContentForRender();
            $return .= $this->recursive($content);
            if ($this->_tag && ( ! $this->isVoid())) {
                $return .= "</{$this->_tag}>";
            }
            $this->_html = $return;
        }

        private function isVoid()
        {
            return $this->_tag && in_array($this->_tag, $this->_voidElements);
        }

        private function isAllowedTags($tag)
        {
            return in_array($tag, $this->_allowed_tags);
        }

        private function renderAttributes()
        {
            if ( ! $this->_attributes) {
                return '';
            }
            $return = [];
            foreach ($this->_attributes as $attribute => $value) {
                if (null === $value) {
                    $return[] = esc_attr($attribute);
                } else {
                    $return[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
                }
            }

            return $this->spase . implode($this->spase, $return);
        }

        protected function _getContentForRender()
        {
            return $this->getContent();
        }

        public function getContent()
        {
            return $this->_contents;
        }

        public function recursive($item)
        {
            if (is_array($item)) {
                $return = '';
                foreach ($item as $v) {
                    $return .= $this->recursive($v);
                }

                return $return;
            } else {
                return (string)$item;
            }
        }

        public function setRelationship($relationship)
        {
            $this->setAttribute('rel', $relationship);

            return $this;
        }

        public function setAttribute($attribute, $value = null)
        {
            $this->_attributes[$attribute] = $value;

            return $this;
        }

        public function appendContent($content)
        {
            if (is_array($content)) {
                $this->_contents = wp_parse_args($this->_contents, $content);
            } else {
                $this->_contents[] = $content;
            }

            return $this;
        }

        public function prependContent($content)
        {
            if (is_array($content)) {
                $this->_contents = wp_parse_args($content, $this->_contents);
            } else {
                array_unshift($this->_contents, $content);
            }

            return $this;
        }

        public function setAttributes($attributes)
        {
            if (null !== $attributes) {
                foreach ((array)$attributes as $k => $v) {
                    if ($k === 'class') {
                        $this->addClass($v);
                    } elseif (0 === $k) {
                        $this->setAttribute($v);
                    } else {
                        $this->setAttribute($k, $v);
                    }
                }
            }

            return $this;
        }

        public function removeAttributes(array $attributes)
        {
            foreach ($attributes as $attribute) {
                $this->removeAttribute($attribute);
            }

            return $this;
        }

        public function removeAttribute($attribute)
        {
            unset($this->_attributes[$attribute]);

            return $this;
        }

        public function removeClass($class)
        {
            $current = $this->getAttribute('class');
            $current = explode(' ', $current);
            $new     = array_diff($current, [$class]);
            if ($new) {
                $this->setAttribute('class', implode($this->spase, $new));
            } else {
                $this->removeAttribute('class');
            }

            return $this;
        }

        public function getAttribute($attribute)
        {
            if (array_key_exists($attribute, $this->_attributes)) {
                return $this->_attributes[$attribute];
            }

            return '';
        }

        public function __toString()
        {
            return $this->_html;
        }

        public static function getAndUnset($key, $array)
        {
            if (isset($array[$key])) {
                $value = $array[$key];
                unset($array[$key]);
            } else {
                $value = null;
            }

            return $value;
        }

        public function addClassToElement($arr, $str = '')
        {
            if (is_array($arr) || is_object($arr)) {
                foreach ($arr as $k => $v) {
                    $this->addClass($k . $str . $v);
                }
            } else {
                $this->addClass($arr . $str);
            }
        }

        public function addClass($class)
        {
            $current = $this->getAttribute('class');
            $current = $current ? explode($this->spase, $current) : [];
            $current = wp_parse_args($current, $class);
            $this->setAttribute('class', implode($this->spase, $current));

            return $this;
        }
    }