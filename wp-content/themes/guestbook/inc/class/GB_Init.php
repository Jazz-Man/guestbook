<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 13.01.17
     * Time: 17:16
     */
    namespace GB;

    class GB_Init
    {

        /**
         * GB_Init constructor.
         */
        public function __construct()
        {
            new GB_Setup();
            new CB_Shortcodes();
            new GB_Filter();
            new GB_Clean_UP_Theme();
            GB_Admin::adminOptionINIT();
        }
    }