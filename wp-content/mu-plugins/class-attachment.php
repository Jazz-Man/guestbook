<?php
    /**
     * Plugin Name: Class_Attachment
     * Version: 0.1-alpha
     * Description: PLUGIN DESCRIPTION HERE
     * Author: YOUR NAME HERE
     * Author URI: YOUR SITE HERE
     * Plugin URI: PLUGIN SITE HERE
     * Text Domain: class-attachment
     * Domain Path: /languages.
     */

namespace Upages_Objects;

/**
     * Class Attachment.
     */
class Attachment
{
    /**
         * @var string
         */
    protected static $post_type = 'attachment';

    /**
     * @param string $size
     * @param bool   $full_arg
     *
     * @return array|bool|false
     */
    public function getFeaturedImageUrl($size = 'full', $full_arg = false)
    {
        $attachment_id = $this->getThumbnailId();
        if ( ! $attachment_id) {
            return false;
        }
        $src = wp_get_attachment_image_src($attachment_id, $size);
        if ( ! $src) {
            return false;
        }

        return $full_arg === true ? $src : $src[0];
    }
    /**
     * @param string $size
     * @param array  $attr
     *
     * @return string
     */
    public function getFeaturedImage($size = 'full', array $attr = [])
    {
        $html          = '';
        $attachment_id = $this->getThumbnailId();
        $image         = $this->getFeaturedImageUrl($size, true);
        if ($image) {
            list($src, $width, $height) = $image;
            $hwstring   = image_hwstring($width, $height);
            $size_class = $size;
            if (is_array($size_class)) {
                $size_class = implode('x', $size_class);
            }
            $attachment   = get_post($attachment_id);
            $default_attr = [
                'src'   => $src,
                'class' => "attachment-$size_class size-$size_class",
                'alt'   => $this->getTitle(),
            ];
            $attr         = wp_parse_args($attr, $default_attr);
            if (empty($attr['srcset'])) {
                $image_meta = wp_get_attachment_metadata($attachment_id);
                if (is_array($image_meta)) {
                    $size_array = [absint($width), absint($height)];
                    $srcset     = wp_calculate_image_srcset($size_array, $src, $image_meta, $attachment_id);
                    $sizes      = wp_calculate_image_sizes($size_array, $src, $image_meta, $attachment_id);
                    if ($srcset && ($sizes || ! empty($attr['sizes']))) {
                        $attr['srcset'] = $srcset;
                        if (empty($attr['sizes'])) {
                            $attr['sizes'] = $sizes;
                        }
                    }
                }
            }
            $attr = apply_filters('wp_get_attachment_image_attributes', $attr, $attachment, $size);
            $attr = array_map('esc_attr', $attr);
            $html = rtrim("<img $hwstring");
            foreach ($attr as $name => $value) {
                $html .= " $name=" . '"' . $value . '"';
            }
            $html .= ' />';
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function getThumbnailId()
    {
        return $this->getMeta('_thumbnail_id');
    }
}
