<?php
    namespace GB;

    /**
     * Class GB_Form.
     */
    class GB_Form
    {
        public $predefined_fields;
        public $login_filds;
        public $register_filds;
        public $comment_edit_filds;
        public $comment_add_filds;
        public $form;
        public $form_attr;

        /**
         * GB_Form constructor.
         *
         * @param string $form
         * @param array  $form_attr
         */
        public function __construct(string $form, array $form_attr = [])
        {
            $this->setPredefinedFields();
            $this->setFormFields();
            $property = "{$form}_filds";
            if (property_exists($this, $property)) {
                $this->form_mode           = $form;
                $this->form                = (array)$this->$property;
                $_form_attr                = [
                    'name'         => 'gb_form',
                    'id'           => 'gb_form',
                    'nonce'        => 'gb_form',
                    'class'        => 'form inputs-underline',
                    'action'       => '',
                    'input_before' => '<div class="form-group">',
                    'input_after'  => '</div>',
                    'autocomplete' => 'off',
                    'method'       => 'post',
                    'redirect'     => '',
                    'submit'       => [
                        'title'         => __('Save'),
                        'id'            => 'gb-form-save',
                        'class'         => 'btn btn-primary width-100',
                        'submit_before' => '<div class="form-group center">',
                        'submit_after'  => '</div>',
                    ],
                ];
                $this->form_attr           = wp_parse_args($form_attr, $_form_attr);
                $this->form_attr['submit'] = wp_parse_args($form_attr['submit'], $_form_attr['submit']);
            } else {
                GB_Helper::notice('no_form', "Форму <strong>{$property}</strong> незнайдено!");
            }
        }

        public function setPredefinedFields()
        {
            $this->predefined_fields = [
                'user_login'           => [
                    'title'    => __('Username or Email Address'),
                    'type'     => 'text',
                    'label'    => __('Username or Email Address'),
                    'attr'     => [
                        'id'          => 'user_login',
                        'name'        => 'log',
                        'class'       => 'form-control',
                        'required'    => '',
                        'value'       => isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '',
                        'placeholder' => __('Username or Email Address'),
                        'size'        => 20,
                    ],
                    'validate' => 'unique_username',
                ],
                'user_password'        => [
                    'title' => __('Password'),
                    'id'    => 'user_password',
                    'type'  => 'password',
                    'label' => __('Password'),
                    'attr'  => [
                        'id'          => 'user_pass',
                        'name'        => 'pwd',
                        'class'       => 'form-control',
                        'placeholder' => __('Password'),
                        'required'    => '',
                        'size'        => 20,
                    ],
                ],
                'retype_password'      => [
                    'title' => __('Повторіть пароль', 'guestbook'),
                    'type'  => 'password',
                    'label' => __('Повторіть пароль', 'guestbook'),
                    'attr'  => [
                        'id'          => 'pass2',
                        'name'        => 'pass2',
                        'class'       => 'form-control',
                        'placeholder' => __('Повторіть пароль', 'guestbook'),
                        'required'    => '',
                        'size'        => 20,
                    ],
                ],
                'user_email'           => [
                    'title'    => __('Email'),
                    'label'    => __('Email'),
                    'type'     => 'email',
                    'attr'     => [
                        'id'          => 'user_email',
                        'name'        => 'user_email',
                        'class'       => 'form-control',
                        'required'    => '',
                        'placeholder' => __('Email'),
                        'size'        => 25,
                    ],
                    'validate' => 'unique_email',
                ],
                'anti_spam_field'      => [
                    'title' => __('Заповніть це поле і буде дуже цікаво :))'),
                    'type'  => 'text',
                    'label' => __('Заповніть це поле і буде дуже цікаво :))'),
                    'attr'  => [
                        'id'           => 'request',
                        'name'         => 'request',
                        'class'        => 'form-control',
                        'autocomplete' => 'off',
                        'placeholder'  => __('Заповніть це поле і буде дуже цікаво :))'),
                        'size'         => 25,
                        'value'        => '',
                    ],
                ],
                'rememberme'           => [
                    'title'    => __('Remember Me'),
                    'type'     => 'checkbox',
                    'label'    => __('Remember Me'),
                    'attr'     => [
                        'id'      => 'rememberme',
                        'name'    => 'rememberme',
                        'class'   => 'form-control',
                        'checked' => '',
                        'value'   => 'forever',
                    ],
                    'validate' => 'unique_username_or_email',
                ],
                'comment_area'         => [
                    'title'    => __('Коментар'),
                    'type'     => 'textarea',
                    'label'    => __('Коментар'),
                    'attr'     => [
                        'id'          => 'comment',
                        'name'        => 'comment',
                        'class'       => 'form-control',
                        'placeholder' => __('Leave a Reply'),
                        'cols'        => 45,
                        'rows'        => 8,
                        'required'    => '',
                    ],
                    'validate' => 'unique_username',
                ],
                'comment_author'       => [
                    'title'    => __('Name'),
                    'type'     => 'text',
                    'label'    => __('Name'),
                    'attr'     => [
                        'id'          => 'name',
                        'name'        => 'newcomment_author',
                        'class'       => 'form-control',
                        'placeholder' => __('Name'),
                        'size'        => 30,
                        'required'    => '',
                    ],
                    'validate' => 'unique_username',
                ],
                'comment_author_email' => [
                    'title'    => __('Email:'),
                    'type'     => 'email',
                    'label'    => __('Email:'),
                    'attr'     => [
                        'id'          => 'email',
                        'name'        => 'newcomment_author_email',
                        'class'       => 'form-control',
                        'placeholder' => __('Email:'),
                        'size'        => 30,
                        'required'    => '',
                    ],
                    'validate' => 'unique_username',
                ],
                'comment_author_url'   => [
                    'title'    => __('URL:'),
                    'type'     => 'url',
                    'label'    => __('URL:'),
                    'attr'     => [
                        'id'          => 'newcomment_author_url',
                        'name'        => 'newcomment_author_url',
                        'class'       => 'form-control',
                        'placeholder' => __('URL:'),
                        'size'        => 30,
                    ],
                    'validate' => 'unique_username',
                ],
                'user_registered'      => [
                    'title'          => __('Registration Date', 'ultimatemember'),
                    'metakey'        => 'user_registered',
                    'type'           => 'text',
                    'label'          => __('Registration Date', 'ultimatemember'),
                    'required'       => 0,
                    'public'         => 1,
                    'editable'       => 1,
                    'edit_forbidden' => 1,
                ],
                'secondary_user_email' => [
                    'title'    => __('Secondary E-mail Address', 'ultimatemember'),
                    'metakey'  => 'secondary_user_email',
                    'type'     => 'text',
                    'label'    => __('Secondary E-mail Address', 'ultimatemember'),
                    'required' => 0,
                    'public'   => 1,
                    'editable' => 1,
                    'validate' => 'unique_email',
                ],
                'description'          => [
                    'title'       => __('Biography', 'ultimatemember'),
                    'metakey'     => 'description',
                    'type'        => 'textarea',
                    'label'       => __('Biography', 'ultimatemember'),
                    'html'        => 0,
                    'required'    => 0,
                    'public'      => 1,
                    'editable'    => 1,
                    'max_words'   => 40,
                    'placeholder' => 'Enter a bit about yourself...',
                ],
                'password_reset_text'  => [
                    'title'       => __('Password Reset', 'ultimatemember'),
                    'type'        => 'block',
                    'content'     => '<div class="alert alert-info">' . __(
                            'To reset your password, please enter your email address or username below',
                            'ultimatemember'
                        ) . '</div>',
                    'private_use' => true,
                ],
            ];
            $this->predefined_fields = apply_filters('gb_predefined_fields', $this->predefined_fields);
        }

        public function setFormFields()
        {
            $this->login_filds        = [
                $this->predefined_fields['user_login'],
                $this->predefined_fields['user_password'],
                $this->predefined_fields['rememberme'],
            ];
            $this->register_filds     = [
                $this->predefined_fields['user_login'],
                $this->predefined_fields['user_email'],
                $this->predefined_fields['user_password'],
                $this->predefined_fields['retype_password'],
            ];
            $this->comment_edit_filds = [
                $this->predefined_fields['comment_author'],
                $this->predefined_fields['comment_author_email'],
                $this->predefined_fields['comment_author_url'],
                $this->predefined_fields['comment_area'],
            ];
            $this->comment_add_filds  = $this->comment_edit_filds;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            $output = '';
            if ( ! empty($this->form)) {
                ob_start();
                foreach ($this->form as $field => $item) {
                    if ($this->form_attr['input_before'] !== '') {
                        $output .= $this->form_attr['input_before'];
                    }
                    $output .= $this->getField($item);
                    if ($this->form_attr['input_after'] !== '') {
                        $output .= $this->form_attr['input_after'];
                    }
                }
                $output .= $this->getHiddenFields();
                $output .= $this->getSubmit();
                switch ($this->form_mode) {
                    case 'login':
                        $output .= $this->getLoginBottom();
                        break;

                }
                if ($this->isCommentMode()) {
                    $output .= $this->getCommentBottom();
                }
                unset($this->form_attr['nonce'], $this->form_attr['redirect'], $this->form_attr['comment'], $this->form_attr['submit'], $this->form_attr['input_before'], $this->form_attr['input_after']);
                echo new GB_Html('form', $this->form_attr, $output);
                $result = ob_get_contents();
                ob_end_clean();
                $output = $result;
            }

            return $output;
        }

        /**
         * @param array $key
         *
         * @return string|void
         */
        public function getField(array $key)
        {
            if ( ! isset($key['type']) && ! isset($key['attr'])) {
                return '';
            }
            $output = '';
            switch ($key['type']) {
                case 'text':
                case 'password':
                case 'url':
                case 'number':
                case 'date':
                case 'email':
                case 'time':
                    $output .= $this->getFieldLabel($key);
                    $attr = wp_parse_args($key['attr'], ['type' => $key['type']]);
                    if ($this->isCommentMode()) {
                        switch ($attr['name']) {
                            case 'newcomment_author':
                                $attr = wp_parse_args(
                                    [
                                        'value' => $this->form_attr['comment']['comment_author'] ?? '',
                                    ], $attr
                                );
                                break;
                            case 'newcomment_author_email':
                                $attr = wp_parse_args(
                                    [
                                        'value' => $this->form_attr['comment']['comment_author_email'] ?? '',
                                    ], $attr
                                );
                                break;
                            case 'newcomment_author_url':
                                $attr = wp_parse_args(
                                    [
                                        'value' => $this->form_attr['comment']['comment_author_url'] ?? '',
                                    ], $attr
                                );
                                break;

                        }
                    }
                    $output .= new GB_Html('input', $attr);
                    break;
                case 'checkbox':
                case 'radio':
                    $attr       = wp_parse_args($key['attr'], ['type' => $key['type']]);
                    $label_attr = [
                        'for'   => $attr['id'],
                        'class' => 'checkboxes',
                    ];
                    $label      = new GB_Html('input', $attr);
                    $label .= esc_html($key['label']);
                    $output .= new GB_Html('label', $label_attr, $label);
                    break;
                case 'textarea':
                    $output .= $this->getFieldLabel($key);
                    $attr    = wp_parse_args($key['attr'], ['type' => $key['type']]);
                    $content = '';
                    if (isset($this->form_attr['comment']['content'])) {
                        $content .= $this->form_attr['comment']['content'];
                    }
                    $output .= new GB_Html('textarea', $attr, $content);
                    break;
                case 'select':
                    $output .= $key['type'];
                    break;
                case 'multiselect':
                    $output .= $key['type'];
                    break;
            }

            return $output;
        }

        /**
         * @param array $key
         *
         * @return \GB\GB_Html|string
         */
        public function getFieldLabel(array $key)
        {
            $label = esc_html($key['label']);
            if (isset($key['attr']['required'])) {
                $label .= '<span>*</span>';
            }
            $output = new GB_Html(
                'label', [
                'for' => $key['attr']['id'],
            ], $label
            );

            return $output ?? '';
        }

        /**
         * @return mixed
         */
        public function getHiddenFields()
        {
            $_anti_spam = '<div style="display: none !important;">';
            $_anti_spam .= $this->getField($this->predefined_fields['anti_spam_field']);
            $_anti_spam .= '</div>';
            $_anti_spam .= wp_nonce_field($this->form_attr['id'], $this->form_attr['nonce'], true, false);
            if ($this->form_attr['redirect'] !== '') {
                $_anti_spam .= new GB_Html(
                    'input', [
                        'type'  => 'hidden',
                        'name'  => 'redirect_to',
                        'value' => $this->form_attr['redirect'],
                    ]
                );
            }

            return $_anti_spam;
        }

        /**
         * @return \GB\GB_Html
         */
        public function getLoginBottom()
        {
            $_options       = get_option('_guestbook-options_options');
            $register       = get_permalink($_options['register']) ?? '';
            $password_reset = get_permalink($_options['password']) ?? '';
            $link           = new GB_Html(
                'a', [
                'href'  => $register,
                'class' => 'btn btn-default',
            ], __('Register')
            );
            $link .= new GB_Html(
                'a', [
                'href'  => $password_reset,
                'class' => 'btn btn-default',
            ], __('Lost your password?')
            );
            $output = new GB_Html(
                'div', [
                'class' => 'btn-group btn-group-justified',
                'role'  => 'group',
            ], $link
            );

            return $output;
        }

        /**
         * @return bool
         */
        public function isCommentMode()
        {
            $comment_mode = ['comment_edit', 'comment_add'];

            return in_array($this->form_mode, $comment_mode);
        }

        /**
         * @return \GB\GB_Html|string
         */
        public function getCommentBottom()
        {
            if (isset($this->form_attr['comment'])) {
                $output = new GB_Html(
                    'input', [
                        'type'  => 'hidden',
                        'id'    => 'comment_post_ID',
                        'name'  => 'comment_post_ID',
                        'value' => $this->form_attr['comment']['comment_post_ID'],
                    ]
                );
                $output .= new GB_Html(
                    'input', [
                        'type'  => 'hidden',
                        'id'    => 'comment_ID',
                        'name'  => 'comment_ID',
                        'value' => $this->form_attr['comment']['comment_ID'],
                    ]
                );

                return $output;
            } else {
                return '';
            }
        }

        /**
         * @return string
         */
        public function getSubmit()
        {
            $_submit_attr = $this->form_attr['submit'];
            $submit_btn   = '';
            $before       = $_submit_attr['submit_before'];
            $after        = $_submit_attr['submit_after'];
            if ($before !== '') {
                $submit_btn .= $before;
            }
            unset($_submit_attr['submit_before'], $_submit_attr['submit_after']);
            $submit_btn .= new GB_Html('button', $_submit_attr, $_submit_attr['title']);
            if ($after !== '') {
                $submit_btn .= $after;
            }

            return $submit_btn;
        }
    }
