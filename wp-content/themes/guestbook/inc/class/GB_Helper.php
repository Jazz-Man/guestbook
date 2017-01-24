<?php
    namespace GB;

    /**
     * Class GB_Helper
     *
     * @package GB
     */
    class GB_Helper
    {
        /**
         * @param array $attr
         *
         * @return string
         */
        public static function add_attr(array $attr)
        {
            $attributes = '';
            foreach ($attr as $key => $value) {
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
         * @param        $code
         * @param string $message
         * @param string $type
         *
         * @return string
         */
        public static function notice($code, $message, $type = 'danger')
        {
            $_error  = new \WP_Error();
            $message = "<div class='alert alert-{$type}' role='alert'>{$message}</div>";
            $_error->add($code, $message);
            return $_error->get_error_message();

        }

        /**
         * @param string $key
         * @param array  $array
         *
         * @return mixed|null
         */
        public static function getAndUnset(string $key, array $array)
        {
            if (isset($array[$key])) {
                $value = $array[$key];
                unset($array[$key]);
            } else {
                $value = null;
            }

            return $value;
        }

    }