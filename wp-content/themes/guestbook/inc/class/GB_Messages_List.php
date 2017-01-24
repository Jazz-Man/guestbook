<?php
  namespace GB;

  if ( ! is_admin()) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    require_once ABSPATH . 'wp-admin/includes/screen.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
    require_once ABSPATH . 'wp-admin/includes/template.php';

    /**
     * Class GB_Messages_List
     *
     * @package GB
     */
    class GB_Messages_List extends \WP_List_Table
    {
      /**
       * GB_Messages_List constructor.
       */
      public function __construct()
      {
        $screen = 'profiletab=comments';
        $args   = [
          'singular' => 'comments',
          'plural'   => 'comments',
          'ajax'     => false,
          'screen'   => $screen,
        ];
        parent::__construct($args);
      }

      /**
       * @return array
       */
      public function get_columns()
      {
        $columns = [
          'comment_content' => __('Comments'),
          'action'          => __('Edit'),
          'comment_date'    => __('Published'),
        ];

        return $columns;
      }

      /**
       * @param $a
       * @param $b
       *
       * @return int
       */
      private function sort_data($a, $b)
      {
        $orderby = 'comment_post_ID';
        $order   = 'asc';
        if ( ! empty($_GET['orderby'])) {
          $orderby = $_GET['orderby'];
        }
        if ( ! empty($_GET['order'])) {
          $order = $_GET['order'];
        }
        $result = strcmp($a[$orderby], $b[$orderby]);
        if ($order === 'asc') {
          return $result;
        }

        return -$result;
      }

      /**
       * @param $comment
       *
       * @return string
       */
      public function _column_action($comment)
      {
        $comment_link        = admin_url('comment.php');
        $edit_comment_link   = add_query_arg(
          [
            'action'   => 'editcomment',
            'c'        => $comment['comment_ID'],
            '_wpnonce' => wp_create_nonce("editcomment_{$comment['comment_ID']}")
          ]
        );
        $edit_link_attr      = GB_Helper::add_attr(
          [
            'class' => 'link icon edit',
            'href'  => esc_url($edit_comment_link),
            'title' => __('Edit'),
          ]
        );
        $delete_comment_link = add_query_arg(
          [
            'action'   => 'trashcomment',
            'c'        => $comment['comment_ID'],
            '_wpnonce' => wp_create_nonce("delete-comment_{$comment['comment_ID']}")
          ], $comment_link
        );
        $delete_link_attr    = GB_Helper::add_attr(
          [
            'class' => 'link icon delete',
            'href'  => $delete_comment_link,
            'title' => __('Видалити'),
            'data-toggle'=>'modal'
          ]
        );
        $actions             = [];
        $actions['edit']
                             = "<a {$edit_link_attr}><i class='fa fa-pencil-square-o fa-2x' aria-hidden='true'></i></a>";
        $actions['delete']   = "<a {$delete_link_attr}><i class='fa fa-trash-o fa-2x' aria-hidden='true'></i></a>";
        $out                 = '<td class="options">';
        $out .= '<div class="edit-options">';
        foreach ($actions as $action => $link) {
          $out .= $link;
        }
        $out .= '</div>';
        $out .= '</td>';

        return $out;
      }

      /**
       * @param $comment
       *
       * @return string
       */
      public function _column_comment_content($comment)
      {
        ob_start(); ?>
        <td>
          <div class="info" id="my-comment-<?= $comment['comment_ID']?>">
            <p>
              <?= $comment['comment_content'] ?>
            </p>
          </div>
        </td>
        <?php
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
      }

      /**
       * @param int $post_id
       * @param int $pending_comments
       *
       * @return string
       */
      public function comments_bubble($post_id, $pending_comments)
      {
        $approved_comments        = get_comments_number($post_id);
        $approved_comments_number = number_format_i18n($approved_comments);
        $pending_comments_number  = number_format_i18n($pending_comments);
        $out                      = '';
        if ( ! $approved_comments && ! $pending_comments) {
          $out .= '<span aria-hidden="true">—</span>';
        } elseif ($approved_comments) {
          $out .= sprintf(
            '<span class="comment-count" aria-hidden="true">%s</span>', $approved_comments_number
          );
        } else {
          $out .= sprintf(
            '<span class="comment-count" aria-hidden="true">%s</span>', $approved_comments_number
          );
        }
        if ($pending_comments) {
          sprintf(
            '<span class="comment-count" aria-hidden="true">%s</span>', $pending_comments_number
          );
        } else {
          $out .= sprintf(
            '<span class="comment-count" aria-hidden="true">%s</span>', $pending_comments_number
          );
        }

        return $out;
      }

      public function prepare_items()
      {
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data     = $this->table_data();
        usort($data, [$this, 'sort_data']);
        $total_items = count($data);
        $per_page    = 10;
        $currentPage = $this->get_pagenum();
        $this->set_pagination_args(
          [
            'total_items' => $total_items,
            'per_page'    => $per_page,
          ]
        );
        $data                  = array_slice($data, ($currentPage - 1) * $per_page, $per_page);
        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items           = $data;
      }

      /**
       * @return array
       */
      public function get_hidden_columns()
      {
        return [];
      }

      /**
       * @param object $item
       * @param string $column_name
       *
       * @return mixed
       */
      public function column_default($item, $column_name)
      {
        switch ($column_name) {
          case 'comment_content':
          case 'action':
          case 'comment_date':
            return $item[$column_name];
          default:
            return print_r($item['comment_author'], true);
        }
      }

      /**
       * @return array
       */
      public function table_data()
      {
        $data  = [];
        $posts = get_comments(
          [
            'hierarchical' => 'flat',
            'user_id'      => get_current_user_id(),
          ]
        );
        foreach ($posts as $post) {
          $data[] = get_object_vars($post);
        }

        return $data;
      }

      /**
       * @param object $item
       */
      public function single_row($item)
      {
        echo '<tr class="my-item">';
        $this->single_row_columns($item);
        echo '</tr>';
      }

      /**
       * @param object $item
       */
      protected function single_row_columns($item)
      {
        list($columns, $hidden, $sortable, $primary) = $this->get_column_info();
        foreach ($columns as $column_name => $column_display_name) {
          $classes = [
            $column_name,
            "column-$column_name"
          ];
          if ($primary === $column_name) {
            $classes[] = 'has-row-actions column-primary';
          }
          if (in_array($column_name, $hidden)) {
            $classes[] = 'hidden';
          }
          $data       = 'data-colname="' . wp_strip_all_tags($column_display_name) . '"';
          $classes    = implode(' ', $classes);
          $attributes = "class='$classes' $data";
          if ('cb' === $column_name) {
            echo '<th scope="row" class="check-column">';
            echo $this->column_cb($item);
            echo '</th>';
          } elseif (method_exists($this, "_column_{$column_name}")) {
            echo call_user_func(
              [$this, "_column_{$column_name}"], $item, $column_name, $primary
            );
          } elseif (method_exists($this, "column_{$column_name}")) {
            echo "<td $attributes>";
            echo call_user_func([$this, "column_{$column_name}"], $item);
            echo $this->handle_row_actions($item, $column_name, $primary);
            echo '</td>';
          } else {
            echo "<td $attributes>";
            echo $this->column_default($item, $column_name);
            echo $this->handle_row_actions($item, $column_name, $primary);
            echo '</td>';
          }
        }
      }

      public function display_rows_or_placeholder()
      {
        if ($this->has_items()) {
          $this->display_rows();
        } else {
          echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
          $this->no_items();
          echo '</td></tr>';
        }
      }

      public function display()
      {
        $this->display_tablenav('top'); ?>
        <div class="my-items table-responsive">
          <?php $this->screen->render_screen_reader_content('heading_list'); ?>
          <table class="table <?php echo implode(' ', $this->get_table_classes()); ?>">
            <thead>
              <tr>
                <?php $this->print_column_headers(); ?>
              </tr>
            </thead>
            <tbody>
              <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
            <tfoot>
              <tr>
                <?php $this->print_column_headers(false); ?>
              </tr>
            </tfoot>
          </table>
        </div>
        <?php
        $this->display_tablenav('bottom');
      }

      /**
       * @param string $which
       */
      protected function display_tablenav($which)
      {
        ?>
        <div class="center <?php echo esc_attr($which); ?>">
          <?php $this->pagination($which); ?>
        </div>
        <?php

      }

      /**
       * @param string $which
       */
      protected function pagination($which)
      {
        if (empty($this->_pagination_args)) {
          return;
        }
        $total_pages          = $this->_pagination_args['total_pages'];
        $output               = '';
        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();
        $current_url          = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $current_url          = remove_query_arg($removable_query_args, $current_url);
        $page_links           = [];
        $disable_first        = $disable_last = $disable_prev = $disable_next = false;
        if ($current == 1) {
          $disable_first = true;
          $disable_prev  = true;
        }
        if ($current == 2) {
          $disable_first = true;
        }
        if ($current == $total_pages) {
          $disable_last = true;
          $disable_next = true;
        }
        if ($current == $total_pages - 1) {
          $disable_last = true;
        }
        if ($disable_first) {
          $page_links[] = sprintf(
            "<li class='disabled'><a class='first-page' href='#' title='%s'>1</a></li>", __('First page')
          );
        } else {
          $page_links[] = sprintf(
            "<li><a class='first-page' href='%s' title='%s'>1</a></li>",
            esc_url(remove_query_arg('paged', $current_url)), __('First page')
          );
        }
        if ($disable_prev) {
          $page_links[]
            = '<li class="disabled previous">
                    <a href="#" aria-label="Previous">
                      <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    </a>
                  </li>';
        } else {
          $page_links[] = sprintf(
            "<li class='previous'><a href='%s' title='%s'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></li>",
            esc_url(add_query_arg('paged', max(1, $current - 1), $current_url)), __('Previous page')
          );
        }
        $html_total_pages = sprintf('%s', number_format_i18n($total_pages));
        $current_page     = sprintf(
          _x('%1$s of %2$s', 'paging'), $current, $html_total_pages
        );
        $page_links[]     = sprintf(
          '<li class="active"><a href="#" title="%s">%s</a></li>', __('Current Page'), $current_page
        );
        if ($disable_next) {
          $page_links[]
            = '<li class="disabled next">
                    <a href="#" aria-label="Previous">
                      <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                    </a>
                  </li>';
        } else {
          $page_links[] = sprintf(
            "<li class='next'><a href='%s' title='%s'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></li>",
            esc_url(add_query_arg('paged', min($total_pages, $current + 1), $current_url)), __('Next page')
          );
        }
        if ($disable_last) {
          $page_links[] = sprintf(
            "<li class='disabled'><a class='last-page' href='#' title='%s'>%s</a></li>", __('Last page'), $total_pages
          );
        } else {
          $page_links[] = sprintf(
            "<li><a class='last-page' href='%s' title='%s'>%s</a></li>",
            esc_url(add_query_arg('paged', $total_pages, $current_url)), __('Last page'), $total_pages
          );
        }
        $output .= implode("\n", $page_links);
        echo "<nav aria-label='Page navigation'><ul class='pagination'>$output</ul></nav>";
      }
    }
  }