<?php
/* $fields = [
  [
    'type'        => 'checkbox',
    'id'          => "{$meta_get}checkbox",
    'label'       => '复选框',
    'description' => '复选框',
  ],
  [
    'type'  => 'textarea',
    'id'    => "{$meta_get}textarea",
    'label' => '多行文本',
  ],
  [
    'type'  => 'text',              // text url email tel password textarea
    'id'    => "{$meta_get}text",
    'label' => '文本',
    'array' => true,                // is array?  use unserialize font-end
  ],
  [
    'type'  => 'number',
    'id'    => "{$meta_get}number",
    'label' => '数字',
    'max'   => '100',
    'min'   => '1',
  ],
  [
    'type'         => 'color',
    'id'           => "{$meta_get}color",
    'label'        => '颜色',
    'default'      => '#c93131',
    'color-picker' => '1',
  ],
  [
    'type'    => 'date',
    'id'      => "{$meta_get}date",
    'label'   => '日期',
    'default' => '2022-05-12',
    'max'     => '2022-08-17',
    'min'     => '2022-05-28',
  ],
  [
    'type'    => 'month',
    'id'      => "{$meta_get}month",
    'label'   => '月份',
    'default' => '2022-03',
    'max'     => '2022-01',
    'min'     => '2022-12',
  ],
  [
    'type'    => 'week',
    'id'      => "{$meta_get}week",
    'label'   => '周',
    'default' => '2022-W20',
    'max'     => '2022-W22',
    'min'     => '2022-W18',
  ],
  [
    'type'    => 'time',
    'id'      => "{$meta_get}time",
    'label'   => '时间',
    'default' => '15:19',
    'max'     => '23:59',
    'min'     => '01:20',
  ],
  [
    'type'   => 'media',
    'id'     => "{$meta_get}media",
    'label'  => '媒体',
    'return' => 'url',                // url or id
  ],
  [
    'type'          => 'editor',
    'id'            => "{$meta_get}editor",
    'label'         => '编辑器',
    'wpautop'       => '1',
    'media-buttons' => '1',
    'teeny'         => '1',
  ],
  [
    'type'  => 'range',
    'id'    => "{$meta_get}range",
    'label' => '滑块',
    'max'   => '100',
    'min'   => '1',
  ],
  [
    'type'    => 'radio',              // radio or select
    'id'      => "{$meta_get}radio",
    'label'   => '单选',
    'default' => 'option-one',
    'options' => [
      'radio-one' => '第一',
      'radio-two' => '第二',
    ],
  ],
  [
    'type'     => 'categories',         // categories user page
    'id'       => "{$meta_get}categories",
    'label'    => '分类',
    'taxonomy' => '',
  ],
]; */

if (!defined('ABSPATH'))
  exit;

if (!class_exists('Term_Meta_Box')) {
  class Term_Meta_Box
  {

    /**
     * @param array $screen  需要加入的分类
     * @return void
     */
    public function __construct($screen = 'category')
    {

      $this->screens = is_array($screen) ? $screen : explode(',', $screen);

      foreach ($this->screens as $screen) {
        add_action($screen . '_add_form_fields', [$this, 'create_fields'], 10, 2);
        add_action($screen . '_edit_form_fields', [$this, 'edit_fields'],  10, 2);
        add_action('created_' . $screen, [$this, 'save_fields'], 10, 1);
        add_action('edited_' . $screen,  [$this, 'save_fields'], 10, 1);
        add_action('admin_enqueue_scripts', [$this, 'add_scripts']);
        add_action('admin_footer', [$this, 'add_footer']);
      }
    }

    public function fields($fields = [])
    {
      $this->fields = $fields;
    }

    public function add_scripts()
    {
      $typenow = get_current_screen();
      if (in_array($typenow->taxonomy, $this->screens)) {
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
      }
    }

    public function add_footer()
    {
      $typenow = get_current_screen();
      if (in_array($typenow->taxonomy, $this->screens)) { ?>
        <style>
          .wp-picker-input-wrap>label {
            display: none;
          }

          .wp-picker-input-wrap>input {
            margin-left: 0 !important;
          }
        </style>
        <script>
          jQuery.noConflict();
          (function($) {
            $(function() {
              $('body').on('click', '.rwp-media-toggle', function(e) {
                e.preventDefault();
                let button = $(this);
                let rwpMediaUploader = null;
                rwpMediaUploader = wp.media({
                  title: button.data('modal-title'),
                  button: {
                    text: button.data('modal-button')
                  },
                  multiple: false
                }).on('select', function() {
                  let attachment = rwpMediaUploader.state().get('selection').first().toJSON();
                  button.prev().val(attachment[button.data('return')]);
                }).open();
              });
              $('.rwp-color-picker').wpColorPicker();
            });
          })(jQuery);
        </script>
<?php
      }
    }

    public function save_fields($term_id)
    {
      foreach ($this->fields as $field) {
        if (isset($_POST[$field['id']])) {
          switch ($field['type']) {
            case 'email':
              $_POST[$field['id']] = sanitize_email($_POST[$field['id']]);
              break;
            case 'text':
              $_POST[$field['id']] = sanitize_text_field($_POST[$field['id']]);
              break;
          }
          update_term_meta($term_id, $field['id'], $_POST[$field['id']]);
        } else if ($field['type'] == 'checkbox') {
          update_term_meta($term_id, $field['id'], '0');
        }

        $value  = get_term_meta($term_id, $field['id'], true);
        if ($value == '') {
          delete_term_meta($term_id, $field['id']);
        }
      }
    }

    public function create_fields($taxonomy)
    {
      foreach ($this->fields as $field) {
        echo '<div class="form-field"><tr class="form-field"><th>';
        echo '<label for="' . $field['id'] . '">' . $field['label'] . '</label></th><td>';
        switch ($field['type']) {
          case 'checkbox':
            $this->checkbox($field, '');
            break;
          case 'date':
          case 'month':
          case 'number':
          case 'range':
          case 'time':
          case 'week':
            $this->input_minmax($field, '');
            break;
          case 'textarea':
            $this->textarea($field, '');
            break;
          case 'media':
            $this->input($field, '');
            $this->media_button($field, '');
            break;
          case 'editor':
            $this->editor($field, '');
            break;
          case 'radio':
            $this->radio($field, '');
            break;
          case 'select':
            $this->select($field, '');
            break;
          case 'posts':
            $this->posts($field, '');
            break;
          case 'categories':
            $this->categories($field, '');
            break;
          case 'pages':
            $this->pages($field, '');
            break;
          case 'users':
            $this->users($field, '');
            break;
          default:
            $this->input($field, '');
        }
        echo '</td></tr></div>';
      }
    }

    public function edit_fields($term, $taxonomy)
    {
      foreach ($this->fields as $field) {
        $value = get_term_meta($term->term_id, $field['id'], true);
        echo '<div class="form-field"><tr class="form-field"><th>';
        echo '<label for="' . $field['id'] . '">' . $field['label'] . '</label></th><td>';
        switch ($field['type']) {
          case 'checkbox':
            $this->checkbox($field, $value);
            break;
          case 'date':
          case 'month':
          case 'number':
          case 'range':
          case 'time':
          case 'week':
            $this->input_minmax($field, $value);
            break;
          case 'textarea':
            $this->textarea($field, $value);
            break;
          case 'media':
            $this->input($field, $value);
            $this->media_button($field, $value);
            break;
          case 'editor':
            $this->editor($field, $value);
            break;
          case 'radio':
            $this->radio($field, $value);
            break;
          case 'select':
            $this->select($field, $value);
            break;
          case 'posts':
            $this->posts($field, $value);
            break;
          case 'categories':
            $this->categories($field, $value);
            break;
          case 'pages':
            $this->pages($field, $value);
            break;
          case 'users':
            $this->users($field, $value);
            break;
          default:
            $this->input($field, $value);
        }
        echo '</td></tr></div>';
      }
    }

    // 复选框
    private function checkbox($field, $value)
    {
      printf(
        '<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
        $this->checked($field, $value),
        $field['id'],
        $field['id'],
        isset($field['description']) ? $field['description'] : '',
      );
    }

    // 文本
    private function input($field, $value)
    {
      if ($field['type'] == 'media') {
        $field['type'] = 'text';
      }
      if (isset($field['color-picker'])) {
        $field['class'] = 'rwp-color-picker';
      }
      printf(
        '<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">%s',
        isset($field['class']) ? $field['class'] : '',
        $field['id'],
        $field['id'],
        isset($field['pattern']) ? 'pattern="' . $field['pattern'] . '"' : '',
        $field['type'],
        $value,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 数字范围
    private function input_minmax($field, $value)
    {
      printf(
        '<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">%s',
        $field['id'],
        isset($field['max']) ? 'max="' . $field['max'] . '"' : '',
        isset($field['min']) ? 'min="' . $field['min'] . '"' : '',
        $field['id'],
        isset($field['step']) ? 'step="' . $field['step'] . '"' : '',
        $field['type'],
        $value,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 多行文本
    private function textarea($field, $value)
    {
      printf(
        '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>%s',
        $field['id'],
        $field['id'],
        isset($field['rows']) ? $field['rows'] : 5,
        $value,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 媒体
    private function media_button($field, $value)
    {
      printf(
        ' <button class="button rwp-media-toggle" data-modal-button="%s" data-modal-title="%s" data-return="%s" id="%s_button" name="%s_button" type="button">%s</button>%s',
        isset($field['modal-button']) ? $field['modal-button'] : __('选择这个文件', 'example-text'),
        isset($field['modal-title']) ? $field['modal-title'] : __('选择一个文件', 'example-text'),
        $field['return'],
        $field['id'],
        $field['id'],
        isset($field['button-text']) ? $field['button-text'] : __('上传', 'example-text'),
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 编辑器
    private function editor($field, $value)
    {
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
      wp_editor($value, $field['id'], [
        'wpautop'       => isset($field['wpautop']) ? true : false,
        'media_buttons' => isset($field['media-buttons']) ? true : false,
        'textarea_name' => $field['id'],
        'textarea_rows' => isset($field['rows']) ? isset($field['rows']) : 20,
        'teeny'         => isset($field['teeny']) ? true : false,
      ]);
    }

    // 单选框
    private function radio($field, $value)
    {
      $options = $field['options'];
      $output  = [];
      $i       = 0;
      foreach ($options as $option => $opt) {
        $output[] = sprintf(
          '<label><input %s id="%s-%d" name="%s" type="radio" value="%s">%s</label>',
          $value == $option ? 'checked' : '',
          $field['id'],
          $i,
          $field['id'],
          $option,
          $opt,
        );
        $i++;
      }
      $option_list = implode(' ', $output);
      printf(
        '<fieldset><legend class="screen-reader-text">%s</legend>%s</fieldset>%s',
        $field['label'],
        $option_list,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 下拉框
    private function select($field, $value)
    {
      $options = $field['options'];
      $output  = [];
      $i       = 0;
      foreach ($options as $option => $opt) {
        $output[] = sprintf(
          '<option %s value="%s">%s</option>',
          $value == $option ? 'selected' : '',
          $option,
          $opt,
        );
        $i++;
      }
      $option_list = implode('<br>', $output);
      printf(
        '<select id="%s" name="%s">%s</select>%s',
        $field['id'],
        $field['id'],
        $option_list,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 下拉文章
    private function posts($field, $value)
    {
      $output  = [];
      $options = get_posts([
        'post_type'        => isset($field['post_type']) ? $field['post_type'] : 'post',
        'fields'           => 'ids',
        'suppress_filters' => false,
      ]);
      $output[] = '<option value="">' . __('选择文章', 'example-text') . '</option>';
      foreach ($options as $option) {
        $output[] = sprintf(
          '<option %s value="%s"> %s</option>',
          $value == $option ? 'selected' : '',
          $option,
          get_the_title($option),
        );
      }
      $option_list = implode('<br>', $output);
      printf(
        '<select id="%s" name="%s">%s</select>%s',
        $field['id'],
        $field['id'],
        $option_list,
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 下拉分类
    private function categories($field, $value)
    {
      wp_dropdown_categories([
        'show_option_none' => __('选择分类', 'example-text'),
        'taxonomy'         => isset($field['taxonomy']) ? $field['taxonomy'] : 'category',
        'show_count'       => isset($field['count']) ? $field['count'] : true,
        'selected'         => $value,
        'name'             => $field['id'],
        'id'               => $field['id'],
        'hierarchical'     => true,
        'hide_empty'       => false,
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 下拉页面
    private function pages($field, $value)
    {
      wp_dropdown_pages([
        'show_option_none' => __('选择页面', 'example-text'),
        'selected'         => $value,
        'name'             => $field['id'],
        'id'               => $field['id'],
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 下拉用户
    private function users($field, $value)
    {
      wp_dropdown_users([
        'show_option_none' => __('选择用户', 'example-text'),
        'selected'         => $value,
        'name'             => $field['id'],
        'id'               => $field['id'],
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 布尔值
    private function checked($field, $value)
    {
      if ($value == 'on') {
        return 'checked';
      } else if (isset($field['checked'])) {
        return 'checked';
      }
      return '';
    }
  }
};
