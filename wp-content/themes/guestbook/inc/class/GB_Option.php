<?php
  namespace GB;

  /**
   * Class GB_Option
   *
   * @package GB
   */
  class GB_Option
  {
    private $pages = [];
    private $options;

    /**
     * GB_Option constructor.
     */
    public function __construct()
    {
      add_action('admin_menu', [$this, 'addPages']);
      add_action('admin_init', [$this, 'pageInit']);
    }

    /**
     * @param $function
     * @param $params
     *
     * @return array
     */
    public function __call($function, $params)
    {
      foreach ($this->pages as $page) {
        $page_menu_slug = $page['menu_slug']?? sanitize_title($page['page_title']);
        if ($function === "{$page_menu_slug}_callback") {
          $this->buildPage($page);

        } elseif ($function === "{$page_menu_slug}_sanitize") {
          return $this->sanitize($page, $params[0]);

        } elseif (isset($page['sections']) && count($page['sections']) > 0) {
          foreach ($page['sections'] as $section) {
            $_dufault_section = [
              'id' => sanitize_title($section['title']),
            ];
            $section = wp_parse_args($section, $_dufault_section);
            if ($function === "{$section['id']}_callback") {
              $this->buildSection($section);

            } elseif (isset($section['fields']) && count($section['fields']) > 0) {
              foreach ($section['fields'] as $field) {
                $_dufault_field = [
                  'id' => sanitize_title($field['title']),
                ];
                $field          = wp_parse_args($field, $_dufault_field);
                if ($function === $field['id'] . '_callback') {
                  $this->buildField($field, $page_menu_slug);
                } elseif (isset($page['subpages']) && count($page['subpages']) > 0) {
                  if (isset($subpage)) {
                    $subpage_menu_slug = $subpage['menu_slug']?? sanitize_title($subpage['page_title']);
                    if ($function === "{$subpage_menu_slug}_sanitize") {
                      return $this->sanitize($subpage, $params[0]);
                    } elseif ($function === "{$subpage_menu_slug}_callback") {
                      $this->buildPage($subpage);
                    }

                  } elseif (isset($subpage['sections']) && count($subpage['sections']) > 0) {
                    foreach ($subpage['sections'] as $subpage_section) {
                      if ($function === "{$subpage_section['id']}_callback") {
                        $this->buildSection($subpage_section);

                      } elseif (isset($subpage_section['fields']) && count($subpage_section['fields']) > 0) {
                        foreach ($subpage_section['fields'] as $subpage_field) {
                          if ($function === "{$subpage_field['id']}_callback") {
                            $this->buildField($subpage_field, $subpage['menu_slug']);
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        if (isset($page['subpages']) && count($page['subpages']) > 0) {
          foreach ($page['subpages'] as $subpage) {
            $subpage_menu_slug = $subpage['menu_slug']?? sanitize_title($subpage['page_title']);
            if ($function === "{$subpage_menu_slug}_callback") {
              $this->buildPage($subpage);

            } elseif ($function === "{$subpage_menu_slug}_sanitize") {
              return $this->sanitize($subpage, $params[0]);

            } elseif (isset($subpage['sections']) && count($subpage['sections']) > 0) {
              foreach ($subpage['sections'] as $subpage_section) {
                $_dufault_subpage_section = [
                  'id' => sanitize_title($subpage_section['title']),
                ];
                $subpage_section = wp_parse_args($subpage_section, $_dufault_subpage_section);
                if ($function === "{$subpage_section['id']}_callback") {
                  $this->buildSection($subpage_section);

                } elseif (isset($subpage_section['fields']) && count($subpage_section['fields']) > 0) {
                  foreach ($subpage_section['fields'] as $subpage_field) {
                    $_dufault_field = [
                      'id' => sanitize_title($subpage_field['title']),
                    ];
                    $subpage_field = wp_parse_args($subpage_field, $_dufault_field);
                    if ($function === "{$subpage_field['id']}_callback") {
                      $this->buildField($subpage_field, $subpage_menu_slug);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    /**
     * @param array $pages
     */
    public function addPage(array $pages = [])
    {
      if (isset($pages) && count($pages) > 0) {
        $this->pages[] = $pages;
        if ($this->_inArray('file', $this->pages)) {
          add_action('admin_print_scripts', [$this, 'admin_scripts']);
          add_action('admin_print_styles', [$this, 'admin_styles']);
        }
      }
    }

    /**
     * @param array $page
     */
    private function buildPage(array $page = [])
    {
      $page_menu_slug                 = $page['menu_slug']?? sanitize_title($page['page_title']);
      $this->options[$page_menu_slug] = get_option("_{$page_menu_slug}_options"); ?>
      <div class="wrap">
        <h2><?php echo $page['page_title']; ?></h2>
        <?php if (isset($page['description']) && ! empty($page['description'])) {
          echo $page['description'];
        }
          settings_errors();
          if (isset($page['sections']) && count($page['sections']) > 0) {
            ?>
            <form method="post" action="options.php">
              <?php settings_fields($page_menu_slug . '_group');
                do_settings_sections($page_menu_slug);
                submit_button(); ?>
            </form>
            <?php
            if (WP_DEBUG) {
              ?>
              <hr>
              <h2>WP_DEBUG Info (<?php echo $page['page_title']; ?>)</h2>
              <p class="description">
                Для того, щоб вибрати опцію із цієї сторінки налаштувань:
                <br>
                get_option('_<?= $page_menu_slug ?>_options');
              </p>
              <hr>
              <h3>Результат:</h3>
              <pre><?php print_r(get_option("_{$page_menu_slug}_options")); ?></pre>
              <?php
            }
          } ?>
      </div>
      <?php
    }

    /**
     * @param array $section
     */
    private function buildSection(array $section)
    {
      if (isset($section['description']) && ! empty($section['description'])) {
        echo $section['description'];
      }
    }

    /**
     * @param array $field
     * @param       $page
     */
    private function buildField(array $field, $page)
    {
      $_dufault_field = [
        'id' => sanitize_title($field['title'])
      ];
      $field          = wp_parse_args($field, $_dufault_field);
      switch ($field['type']) {
        case 'checkbox':
          $field_class       = $field['class']?? '';
          $field_value       = $field['value'] ?? '1';
          $field_checked     = checked($this->options[$page][$field['id']], true, false);
          $field_disabled    = disabled($field['args']['disabled']??false, true, false);
          $field_required    = $this->attrHelper($field['args']['required']??false, 'required');
          $field_description = $field['description'] ?? '';
          printf(
            '<label><input type="checkbox" class="%s" name="%s[%s]" id="%s" value="%s" %s %s %s> %s</label>',
            $field_class, '_' . $page . '_options', $field['id'], $field['id'], $field_value, $field_checked,
            $field_disabled, $field_required, $field_description
          );
          break;
        case 'text':
        case 'search':
        case 'url':
        case 'tel':
        case 'email':
        case 'password':
          $field_class        = $field['class'] ?? '';
          $field_value        = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_pattern      = isset($field['pattern']) ? "pattern='{$field['pattern']}'" : '';
          $field_placeholder  = isset($field['placeholder']) ? "placeholder='{$field['placeholder']}'" : '';
          $field_size         = isset($field['size']) ? "size='{$field['size']}'" : '';
          $field_maxlength    = isset($field['maxlength']) ? "maxlength='{$field['maxlength']}'" : '';
          $field_required     = $this->attrHelper($field['args']['required']??false, 'required');
          $field_autocomplete = (isset($field['args']['autocomplete'])
            && (bool)$field['args']['autocomplete'] === false) ? 'autocomplete="off"' : '';
          $field_readonly     = $this->attrHelper($field['args']['readonly']??false, 'readonly');
          $field_disabled     = disabled($field['args']['disabled'] ?? false, true, false);
          $field_multiple     = (isset($field['args']['multiple']) && $field['type'] === 'email'
            && (bool)$field['args']['multiple'] === true) ? 'multiple' : '';
          $field_description  = $field['description'] ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="%s" class="%s" name="%s[%s]" id="%s" value="%s" %s %s %s %s %s %s %s %s %s>%s</label>',
            $field['type'], $field_class, '_' . $page . '_options', $field['id'], $field['id'], $field_value,
            $field_pattern, $field_placeholder, $field_size, $field_maxlength, $field_required, $field_autocomplete,
            $field_readonly, $field_disabled, $field_multiple, $field_description
          );
          break;
        case 'date':
        case 'datetime':
        case 'datetime-local':
        case 'month':
        case 'time':
        case 'week':
          $field_class        = $field['class'] ?? '';
          $field_value        = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_min          = isset($field['min']) ? "min='{$field['min']}'" : '';
          $field_max          = isset($field['max']) ? "max='{$field['max']}'" : '';
          $field_step         = isset($field['step']) ? "step='{$field['step']}'" : '';
          $field_required     = $this->attrHelper($field['args']['required']??false, 'required');
          $field_autocomplete = (isset($field['args']['autocomplete'])
            && (bool)$field['args']['autocomplete'] === false) ? 'autocomplete="off"' : '';
          $field_readonly     = $this->attrHelper($field['args']['readonly']??false, 'readonly');
          $field_disabled     = disabled($field['args']['disabled'] ?? false, true, false);
          $field_description  = $field['description'] ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="%s" class="%s" name="%s[%s]" id="%s" value="%s" %s %s %s %s %s %s %s>%s</label>',
            $field['type'], $field_class, '_' . $page . '_options', $field['id'], $field['id'], $field_value,
            $field_min, $field_max, $field_step, $field_required, $field_autocomplete, $field_readonly, $field_disabled,
            $field_description
          );
          break;
        case 'range':
          $field_class       = $field['class'] ?? '';
          $field_value       = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_min         = isset($field['min']) ? "min='{$field['min']}'" : '';
          $field_max         = isset($field['max']) ? "max='{$field['max']}'" : '';
          $field_step        = isset($field['step']) ? "step='{$field['step']}'" : '';
          $field_disabled    = disabled($field['args']['disabled'] ?? false, true, false);
          $field_description = isset($field['description']) ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="range" class="%s" name="%s[%s]" id="%s" value="%s" %s %s %s %s>%s</label>',
            $field_class, '_' . $page . '_options', $field['id'], $field['id'], $field_value, $field_min, $field_max,
            $field_step, $field_disabled, $field_description
          );
          break;
        case 'color':
          $field_class       = $field['class'] ?? '';
          $field_value       = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_disabled    = disabled($field['args']['disabled'] ?? false, true, false);
          $field_description = isset($field['description']) ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="color" class="%s" name="%s[%s]" id="%s" value="%s" %s>%s</label>', $field_class,
            '_' . $page . '_options', $field['id'], $field['id'], $field_value, $field_disabled, $field_description
          );
          break;
        case 'number':
          $field_class       = $field['class'] ?? '';
          $field_value       = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_min         = isset($field['min']) ? "min='{$field['min']}'" : '';
          $field_max         = isset($field['max']) ? "max='{$field['max']}'" : '';
          $field_step        = isset($field['step']) ? "step='{$field['step']}'" : '';
          $field_disabled    = disabled($field['args']['disabled'] ?? false, true, false);
          $field_description = isset($field['description']) ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="number" class="%s" name="%s[%s]" id="%s" value="%s" %s %s %s %s>%s</label>',
            $field_class, '_' . $page . '_options', $field['id'], $field['id'], $field_value, $field_min, $field_max,
            $field_step, $field_disabled, $field_description
          );
          break;
        case 'file':
          $field_class       = $field['class'] ?? '';
          $field_value       = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_disabled    = disabled($field['args']['disabled'] ?? false, true, false);
          $field_required    = $this->attrHelper($field['args']['required']??false, 'required');
          $field_description = isset($field['description']) ? "<p class='description'>{$field['description']}</p>" : '';
          printf(
            '<label><input type="text" class="%s file-upload-text" name="%s[%s]" id="%s" value="%s" placeholder="None" %s %s> 
					<input class="button file-upload-button" type="button" value="Upload File" %s> %s</label>', $field_class,
            '_' . $page . '_options', $field['id'], $field['id'], $field_value, $field_disabled, $field_required,
            $field_disabled, $field_description
          );
          break;
        case 'radio':
          $field_class    = $field['class'] ?? '';
          $field_value    = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          $field_disabled = disabled($field['args']['disabled'] ?? false, true, false);
          if (isset($field['options']) && count($field['options']) > 0) {
            echo '<fieldset>';
            $i = 0;
            foreach ($field['options'] as $option_key => $option_value) {
              if (is_int($option_key)) {
                $option_key = $option_value;
              }
              $checked = checked($field_value, $option_key, false);
              printf(
                '<label><input type="radio" class="%s" name="%s[%s]" id="%s" value="%s" %s %s> %s</label>',
                $field_class, '_' . $page . '_options', $field['id'], $field['id'], $option_key, $checked,
                $field_disabled, $option_value
              );
              if ($i < count($field['options']) - 1) {
                echo '<br>';
              }
              ++$i;
            }
            echo '</fieldset>';
          } else {
            echo 'Attribute <code>options</code> required for type <code>radio</code>.';
          }
          break;
        case 'select':
          if (isset($field['options']) && count($field['options']) > 0) {
            $field_class       = $field['class'] ?? '';
            $field_disabled    = disabled($field['args']['disabled'] ?? false, true, false);
            $field_value       = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
            $field_description = isset($field['description']) ? "<p class='description'>{$field['description']}</p>"
              : '';
            printf(
              '<select class="%s" name="%s[%s]" id="%s" %s>', $field_class, '_' . $page . '_options', $field['id'],
              $field['id'], $field_disabled
            );
            foreach ($field['options'] as $option_value => $option_text) {
              $option_value_att = $option_value === '' ? '' : 'value="' . $option_value . '"';
//              $option_value_att = is_int($option_value) ? false : 'value="' . $option_value . '"';
              $selected         = selected($option_value, $field_value, false);
              printf(
                '<option %s %s>%s</option>', $option_value_att, $selected, $option_text
              );
            }
            echo '</select>' . $field_description;
          } else {
            echo 'Attribute <code>options</code> required for type <code>radio</code>.';
          }
          break;
        case 'textarea':
          $field_class        = $field['class'] ?? '';
          $field_placeholder  = isset($field['placeholder']) ? "placeholder='{$field['placeholder']}'" : '';
          $field_rows         = isset($field['rows']) ? "rows='{$field['rows']}'" : 'rows="10"';
          $field_cols         = isset($field['cols']) ? "cols='{$field['cols']}'" : '';
          $field_readonly     = $this->attrHelper($field['args']['readonly']??false, 'readonly');
          $field_disabled     = disabled($field['args']['disabled'] ?? false, true, false);
          $field_required     = $this->attrHelper($field['args']['required']??false, 'required');
          $field_autocomplete = (isset($field['args']['autocomplete'])
            && (bool)$field['args']['autocomplete'] === false) ? 'autocomplete="off"' : '';
          $field_value        = $this->options[$page][$field['id']] ?? ($field['value'] ?? '');
          printf(
            '<textarea class="%s" name="%s[%s]" id="%s" %s %s %s %s %s %s %s>%s</textarea>', $field_class,
            '_' . $page . '_options', $field['id'], $field['id'], $field_placeholder, $field_rows, $field_cols,
            $field_readonly, $field_disabled, $field_required, $field_autocomplete, $field_value
          );
          break;
      }
    }

    public function addPages()
    {
      foreach ($this->pages as $page) {
        $_dufault_page = [
          'page_title' => $page['page_title'],
          'menu_title' => $page['page_title'],
          'menu_slug'  => sanitize_title($page['page_title']),
          'capability' => 'manage_options'
        ];
        $page          = wp_parse_args($page, $_dufault_page);
        add_menu_page(
          $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'],
          [$this, "{$page['menu_slug']}_callback"], $page['icon_url'] ?? null, $page['position'] ?? null
        );
        if (isset($page['subpages']) && count($page['subpages']) > 0) {
          foreach ($page['subpages'] as $subpage) {
            $_dufault_subpage = [
              'page_title' => $subpage['page_title'],
              'menu_title' => $subpage['page_title'],
              'menu_slug'  => sanitize_title($subpage['page_title']),
              'capability' => 'manage_options'
            ];
            $subpage          = wp_parse_args($subpage, $_dufault_subpage);
            add_submenu_page(
              $page['menu_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'],
              $subpage['menu_slug'], [$this, "{$subpage['menu_slug']}_callback"]
            );
          }
        }
      }
    }

    public function pageInit()
    {
      foreach ($this->pages as $page) {
        if (isset($page['sections']) && count($page['sections']) > 0) {
          $_dufault_page = [
            'menu_slug' => sanitize_title($page['page_title']),
          ];
          $page          = wp_parse_args($page, $_dufault_page);
          register_setting(
            "{$page['menu_slug']}_group", "_{$page['menu_slug']}_options", [$this, "{$page['menu_slug']}_sanitize"]
          );
          $this->createSections($page['menu_slug'], $page['sections']);
        }
        if (isset($page['subpages']) && count($page['subpages']) > 0) {
          foreach ($page['subpages'] as $subpage) {
            if (isset($subpage['sections']) && count($subpage['sections']) > 0) {
              $_dufault_subpage = [
                'menu_slug' => sanitize_title($subpage['page_title']),
              ];
              $subpage          = wp_parse_args($subpage, $_dufault_subpage);
              register_setting(
                "{$subpage['menu_slug']}_group", "_{$subpage['menu_slug']}_options",
                [$this, "{$subpage['menu_slug']}_sanitize"]
              );
              $this->createSections($subpage['menu_slug'], $subpage['sections']);
            }
          }
        }
      }
    }

    /**
     * @param $helper
     * @param $type
     *
     * @return string
     */
    private function attrHelper($helper, $type)
    {
      $helper = (string)$helper;

      return __checked_selected_helper($helper, true, false, $type);
    }

    /**
     * @param $slug
     * @param $sections
     */
    private function createSections($slug, $sections)
    {
      foreach ($sections as $section) {
        $_dufault_section = [
          'id' => sanitize_title($section['title']),
        ];
        $section = wp_parse_args($section, $_dufault_section);
        add_settings_section(
          $section['id'], $section['title'], [$this, $section['id'] . '_callback'], $slug
        );
        if (isset($section['fields']) && count($section['fields']) > 0) {
          foreach ($section['fields'] as $field) {
            $_dufault_field = [
              'id' => sanitize_title($field['title']),
            ];
            $field = wp_parse_args($field, $_dufault_field);
            add_settings_field(
              $field['id'], $field['title'], [$this, "{$field['id']}_callback"], $slug, $section['id'],
              $field['args'] ?? ''
            );
          }
        }
      }
    }

    /**
     * @param $page
     * @param $input
     *
     * @return array
     */
    private function sanitize($page, $input)
    {
      $sanitary_values = [];
      if (isset($page['sections']) && count($page['sections']) > 0) {
        foreach ($page['sections'] as $section) {
          foreach ($section['fields'] as $field) {
            $_dufault_field = [
              'id' => sanitize_title($field['title'])
            ];
            $field          = wp_parse_args($field, $_dufault_field);
            if (isset($input[$field['id']])) {
              switch ($field['type']) {
                case 'text':
                case 'search':
                case 'tel':
                case 'password':
                case 'number':
                case 'textarea':
                  if ( ! isset($field['args']['html']) || (bool)($field['args']['html'] !== true)) {
                    $sanitary_values[$field['id']] = sanitize_text_field($input[$field['id']]);
                  } else {
                    $sanitary_values[$field['id']] = $input[$field['id']];
                  }
                  break;
                case 'url':
                  $sanitary_values[$field['id']] = esc_url($input[$field['id']]);
                  break;
                case 'email':
                  $sanitary_values[$field['id']] = sanitize_email($input[$field['id']]);
                  break;
                default:
                  $sanitary_values[$field['id']] = $input[$field['id']];
                  break;
              }
            } elseif ( ! isset($input[$field['id']]) && $field['type'] === 'checkbox') {
              $sanitary_values[$field['id']] = false;
            }
          }
        }
      }

      return $sanitary_values;
    }

    /**
     * @param      $needle
     * @param      $haystack
     * @param bool $strict
     *
     * @return bool
     */
    private function _inArray($needle, $haystack, $strict = false)
    {
      foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle)
          || (is_array($item)
            && $this->_inArray(
              $needle, $item, $strict
            ))
        ) {
          return true;
        }
      }

      return false;
    }

    public function admin_scripts()
    {
      wp_enqueue_script(
        'gb-admin', get_template_directory_uri() . '/assets/js/admin.js', ['media-upload', 'thickbox', 'jquery'], null,
        true
      );
    }

    public function admin_styles()
    {
      wp_enqueue_style('thickbox');
    }
  }
