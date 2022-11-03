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

if (!class_exists('Post_Meta_Box')) {
  class Post_Meta_Box
  {
    protected $fields = [];

    /**
     * @param mixed $id             元框ID
     * @param string $title         元框名称
     * @param array $screen         文章类型
     * @param string $description   元框描述
     * @param string $context       上下文位置
     * @param string $priority      优先级
     * @return void
     */
    public function __construct($id, $title = 'Meta Box', $screen = 'post', $description = '', $context = 'advanced', $priority = 'high')
    {
      $this->id          = $id;
      $this->title       = $title;
      $this->screens     = is_array($screen) ? $screen : explode(',', $screen);
      $this->description = $description;
      $this->context     = $context;
      $this->priority    = $priority;

      add_action('add_meta_boxes', [$this, 'add_boxes']);
      add_action('admin_enqueue_scripts', [$this, 'add_scripts']);
      add_action('admin_footer', [$this, 'add_footer']);
      add_action('save_post', [$this, 'add_save']);
    }

    public function add_boxes()
    {
      foreach ($this->screens as $screen) {
        add_meta_box(
          $this->id,
          $this->title,
          [$this, 'add_callback'],
          $screen,
          $this->context,
          $this->priority,
        );
      }
    }

    public function fields($fields = [])
    {
      $this->fields = $fields;
    }

    public function add_scripts()
    {
      $typenow = get_current_screen();
      if (in_array($typenow->post_type, $this->screens)) {
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
      }
    }

    public function add_footer()
    {
      $typenow = get_current_screen();
      if (in_array($typenow->post_type, $this->screens)) { ?>
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

    public function add_save($post_id)
    {
      foreach ($this->fields as $field) {
        $flabel = $field['label'];
        $fid    = $field['id'];
        $farray = isset($field['array']);
        switch ($field['type']) {
          case 'checkbox':
            if (!empty($_POST[$fid])) {
              update_post_meta($post_id, $fid, isset($_POST[$fid]) ? $_POST[$fid] : '');
            }
            $this->add_delete($post_id, $fid);
            break;
          case 'editor':
            if (!empty($_POST[$fid])) {
              $metas = $farray ? serialize([sanitize_text_field($flabel), wp_filter_post_kses($_POST[$fid])]) : wp_filter_post_kses($_POST[$fid]);
              update_post_meta($post_id, $fid, $metas);
            }
            $this->add_delete($post_id, $fid);
            break;
          case 'email':
            if (!empty($_POST[$fid])) {
              $metas = $farray ? serialize([sanitize_text_field($flabel), sanitize_email($_POST[$fid])]) : sanitize_email($_POST[$fid]);
              update_post_meta($post_id, $fid, $metas);
            }
            $this->add_delete($post_id, $fid);
            break;
          case 'url':
            if (!empty($_POST[$fid])) {
              $metas = $farray ? serialize([sanitize_text_field($flabel), esc_url_raw($_POST[$fid])]) : esc_url_raw($_POST[$fid]);
              update_post_meta($post_id, $fid, $metas);
            }
            $this->add_delete($post_id, $fid);
            break;
          default:
            if (isset($_POST[$fid])) {
              $metas = $farray ? serialize([sanitize_text_field($flabel), sanitize_text_field($_POST[$fid])]) : sanitize_text_field($_POST[$fid]);
              update_post_meta($post_id, $fid, $metas);
            }
            $this->add_delete($post_id, $fid);
        }
      }
    }

    public function add_delete($post_id, $fid)
    {
      $value  = get_post_meta($post_id, $fid, true);
      if ($value == '') {
        delete_post_meta($post_id, $fid);
      }
    }

    public function add_callback()
    {
      echo '<div class="rwp-description">', $this->description ? $this->description : '', '</div>';
      ?>
      <table class="form-table" role="presentation">
        <tbody>
          <?php foreach ($this->fields as $field) { ?>
            <tr>
              <th scope="row"><?php $this->label($field); ?></th>
              <td><?php $this->field($field); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
<?php
    }

    private function label($field)
    {
      switch ($field['type']) {
        case 'editor':
        case 'radio':
          echo '<div class="">' . $field['label'] . '</div>';
          break;
        case 'media':
          printf(
            '<label class="" for="%s_button">%s</label>',
            $field['id'],
            $field['label'],
          );
          break;
        default:
          printf(
            '<label class="" for="%s">%s</label>',
            $field['id'],
            $field['label'],
          );
      }
    }

    private function field($field)
    {
      switch ($field['type']) {
        case 'checkbox':
          $this->checkbox($field);
          break;
        case 'date':
        case 'month':
        case 'number':
        case 'range':
        case 'time':
        case 'week':
          $this->input_minmax($field);
          break;
        case 'textarea':
          $this->textarea($field);
          break;
        case 'media':
          $this->input($field);
          $this->media_button($field);
          break;
        case 'editor':
          $this->editor($field);
          break;
        case 'radio':
          $this->radio($field);
          break;
        case 'select':
          $this->select($field);
          break;
        case 'posts':
          $this->posts($field);
          break;
        case 'categories':
          $this->categories($field);
          break;
        case 'pages':
          $this->pages($field);
          break;
        case 'users':
          $this->users($field);
          break;
        default:
          $this->input($field);
      }
    }

    // 复选框
    private function checkbox($field)
    {
      printf(
        '<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
        $this->checked($field),
        $field['id'],
        $field['id'],
        isset($field['description']) ? $field['description'] : '',
      );
    }

    // 文本
    private function input($field)
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
        $this->value($field),
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 数字范围
    private function input_minmax($field)
    {
      printf(
        '<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">%s',
        $field['id'],
        isset($field['max']) ? 'max="' . $field['max'] . '"' : '',
        isset($field['min']) ? 'min="' . $field['min'] . '"' : '',
        $field['id'],
        isset($field['step']) ? 'step="' . $field['step'] . '"' : '',
        $field['type'],
        $this->value($field),
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 多行文本
    private function textarea($field)
    {
      printf(
        '<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>%s',
        $field['id'],
        $field['id'],
        isset($field['rows']) ? $field['rows'] : 5,
        $this->value($field),
        isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '',
      );
    }

    // 媒体
    private function media_button($field)
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
    private function editor($field)
    {
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
      wp_editor($this->value($field), $field['id'], [
        'wpautop'       => isset($field['wpautop']) ? true : false,
        'media_buttons' => isset($field['media-buttons']) ? true : false,
        'textarea_name' => $field['id'],
        'textarea_rows' => isset($field['rows']) ? isset($field['rows']) : 20,
        'teeny'         => isset($field['teeny']) ? true : false,
      ]);
    }

    // 单选框
    private function radio($field)
    {
      $options = $field['options'];
      $output  = [];
      $i       = 0;
      foreach ($options as $option => $opt) {
        $output[] = sprintf(
          '<label><input %s id="%s-%d" name="%s" type="radio" value="%s">%s</label>',
          $this->value($field) == $option ? 'checked' : '',
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
    private function select($field)
    {
      $options = $field['options'];
      $output  = [];
      $i       = 0;
      foreach ($options as $option => $opt) {
        $output[] = sprintf(
          '<option %s value="%s">%s</option>',
          $this->value($field) == $option ? 'selected' : '',
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
    private function posts($field)
    {
      $output  = [];
      $options = get_posts([
        'post_type'           => isset($field['post_type']) ? $field['post_type'] : 'post',
        'fields'              => 'ids',
        'posts_per_page'      => 99,
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'suppress_filters'    => false,
      ]);
      $output[] = '<option value="">' . __('选择文章', 'example-text') . '</option>';
      foreach ($options as $option) {
        $output[] = sprintf(
          '<option %s value="%s"> %s</option>',
          $this->value($field) == $option ? 'selected' : '',
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
    private function categories($field)
    {
      wp_dropdown_categories([
        'show_option_none' => __('选择分类', 'example-text'),
        'taxonomy'         => isset($field['taxonomy']) ? $field['taxonomy'] : 'category',
        'show_count'       => isset($field['count']) ? $field['count'] : true,
        'selected'         => $this->value($field),
        'name'             => $field['id'],
        'id'               => $field['id'],
        'hierarchical'     => true,
        'hide_empty'       => false,
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 下拉页面
    private function pages($field)
    {
      wp_dropdown_pages([
        'show_option_none' => __('选择页面', 'example-text'),
        'selected'         => $this->value($field),
        'name'             => $field['id'],
        'id'               => $field['id'],
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 下拉用户
    private function users($field)
    {
      wp_dropdown_users([
        'show_option_none' => __('选择用户', 'example-text'),
        'selected'         => $this->value($field),
        'name'             => $field['id'],
        'id'               => $field['id'],
      ]);
      printf('%s', isset($field['description']) ? '<p>' . $field['description'] . '</p>' : '');
    }

    // 输出值
    private function value($field)
    {
      global $post;
      if (metadata_exists('post', $post->ID, $field['id'])) {
        $meta_value = get_post_meta($post->ID, $field['id'], true);
        if (isset($field['array'])) {
          $values = unserialize($meta_value);
          $value  = $values[1];
        } else {
          $value = $meta_value;
        }
      } else if (isset($field['default'])) {
        $value = $field['default'];
      } else {
        return '';
      }
      return str_replace('\u0027', "'", $value);
    }

    // 布尔值
    private function checked($field)
    {
      global $post;
      if (metadata_exists('post', $post->ID, $field['id'])) {
        $value = get_post_meta($post->ID, $field['id'], true);
        if ($value == 'on') {
          return 'checked';
        }
        return '';
      } else if (isset($field['checked'])) {
        return 'checked';
      }
      return '';
    }
  }
}
