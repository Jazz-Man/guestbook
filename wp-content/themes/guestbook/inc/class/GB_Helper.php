<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 17.01.17
     * Time: 13:26
     */
    namespace GB;

    class GB_Helper
    {
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
    }