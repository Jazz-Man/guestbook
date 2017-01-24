<?php
    namespace GB;

    /**
     * Class GB_Html
     *
     * @package GB
     */
    class GB_Html
    {
        private $_contents = '';
        private $_voidElements
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
        private $_allowed_tags
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

        /**
         * GB_Html constructor.
         *
         * @param string     $tag
         * @param array|null $attributes
         * @param string     $content
         */
        public function __construct(string $tag, array $attributes = null, string $content = '')
        {
            if ( ! $this->isAllowedTags($tag)) {
                GB_Helper::notice('tag_not_allowed', "Тег <strong>$tag</strong> не дозволено");
            }
            $this->_tag        = $tag;
            $this->_attributes = $attributes;
            $this->_contents   = $content;
        }

        /**
         * @return bool
         */
        private function isVoid()
        {
            return isset($this->_voidElements[$this->_tag]) ? true : false;
        }

        /**
         * @param $tag
         *
         * @return bool
         */
        private function isAllowedTags($tag)
        {
            return isset($this->_allowed_tags[$tag]) ? false : true;
        }

        /**
         * @return string
         */
        private function renderAttributes()
        {
            if ( ! $this->_attributes) {
                return '';
            }
            $attributes = '';
            foreach ($this->_attributes as $key => $value) {
                if (is_array($value)) {
                    $value = implode(' ', array_filter($value));
                }
                if ($key === 'class' && $value === '') {
                    continue;
                }
                if ($key === 0) {
                    $attributes .= ' ' . esc_attr($value) . '';
                } else {
                    $attributes .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                }
            }

            return $attributes;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            $_html = '';
            if ($this->_tag) {
                $void = $this->isVoid() ? '/' : '';
                $_html .= "<{$this->_tag} {$this->renderAttributes()}{$void}>";
            }
            $_html .= $this->_contents;
            if ($this->_tag && ( ! $this->isVoid())) {
                $_html .= "</{$this->_tag}>";
            }

            return $_html;
        }

    }