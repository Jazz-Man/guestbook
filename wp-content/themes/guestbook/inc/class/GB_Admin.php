<?php
    /**
     * Created by PhpStorm.
     * User: jazzman
     * Date: 17.01.17
     * Time: 13:09
     */
    namespace GB;

    class GB_Admin
    {

        public static function adminOptionINIT()
        {
            $options_page = new GB_Option();
            $options_page->addPage(
                [
                    'page_title'  => wp_get_theme()->get('Name').' Options',
                    'sections'    => [
                        [
                            'title'       => 'Sample Fields',
                            'description' => 'Some of the fields and settings supported by the class',
                            'fields'      => [
                                [
                                    'title'   => 'Sample Radio',
                                    'type'    => 'radio',
                                    'options' => [
                                        'radio-one' => 'Radio options are similar to those for selects',
                                        'radio-two' => 'Sequential or associative arrays work',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'subpages'    => [
                        [
                            'page_title' => 'Sub Page One',
                            'sections'    => [
                                [
                                    'title'       => 'Sample Fields new',
                                    'description' => 'Some of the fields and settings supported by the class',
                                    'fields'      => [
                                        [
                                            'title'       => 'Sample Text new',
                                            'type'        => 'text',
                                            'description' => 'Things like text, search, url, tel, email and password.',
                                            'placeholder' => 'Default value',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }
    }