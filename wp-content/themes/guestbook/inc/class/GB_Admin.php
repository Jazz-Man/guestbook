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
                    'page_title' => wp_get_theme()->get('Name') . ' Options',
                    'sections'   => [
                        [
                            'title'  => 'Core Pages',
                            'fields' => self::core_page_select(),
                        ],
                        [
                            'title'  => 'Redirect User',
                            'fields' => [
                                [
                                    'title'   => 'After Login',
                                    'type'    => 'select',
                                    'options' => self::page_select(),
                                ],
                                [
                                    'title'   => 'After Logout',
                                    'type'    => 'select',
                                    'options' => self::page_select(),
                                ],
                                [
                                    'title'   => 'After Register',
                                    'type'    => 'select',
                                    'options' => self::page_select(),
                                ],[
                                    'title'   => 'After Comment',
                                    'type'    => 'select',
                                    'options' => self::page_select(),
                                ]
                            ],
                        ],
                        [
                            'title'  => 'Select Main Profile Tab',
                            'fields' => [
                                [
                                    'title'   => 'Main Profile Tab',
                                    'type'    => 'select',
                                    'options' => GB_Account::mainProfileTab(),
                                ],
                            ],
                        ],
                    ],
                    'subpages'   => [
                        [
                            'page_title' => 'Sub Page One',
                            'sections'   => [
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

        public static function page_select()
        {
            $args    = [
                'echo' => false,
            ];
            $_pages  = wp_list_pluck(get_pages($args), 'post_title', 'ID');
            $_select = [];
            foreach ($_pages as $key => $val) {
                $_select[$key] = $val;
            }

            return $_select;
        }

        public static function core_page_select()
        {
            $_pages_fields = [];
            $args          = [
                'echo'    => false,
                'exclude' => get_option('page_on_front'),
            ];
            $_pages        = wp_list_pluck(get_pages($args), 'post_title', 'ID');
            $_select       = [];
            foreach ($_pages as $key => $val) {
                $_select[$key] = $val;
            }
            foreach ($_pages as $id => $name) {
                $_pages_fields[] = [
                    'title'   => $name,
                    'id'      => get_post_field('post_name', $id),
                    'type'    => 'select',
                    'options' => $_select,
                ];
            }

            return $_pages_fields;
        }
    }