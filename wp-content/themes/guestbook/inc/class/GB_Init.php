<?php
    namespace GB;

    /**
     * Class GB_Init
     *
     * @package GB
     */
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
            new GB_Account();
        }
    }