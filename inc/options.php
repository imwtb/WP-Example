<?php

// Settings Page: options
// Retrieving values: get_option( 'your_field_id' )
class Theme_Settings_Pages
{
  /**
   * @param string title        标题
   * @param string escription   描述
   * @param string capability   权限
   * @param string slug         唯一名称
   * @param string icon         图标
   * @param string position     位置
   * @return void
   * @link https://developer.wordpress.org/reference/functions/add_menu_page/
   */
  public function __construct()
  {
    add_action('admin_menu', [$this, 'wph_create_settings']);
    add_action('admin_init', [$this, 'wph_setup_sections']);
    add_action('admin_init', [$this, 'wph_setup_fields']);
    add_action('admin_footer', [$this, 'media_fields']);
    add_action('admin_enqueue_scripts', 'wp_enqueue_media');
  }

  public function fields($fields = [])
  {
    $this->menus      = $fields;
    $this->fields     = $fields['fields'];
    $this->title      = $this->menus['title'] ?: __('主题设置', 'example-text');
    $this->escription = $this->menus['escription'] ?: '';
    $this->capability = $this->menus['capability'] ?: 'manage_options';
    $this->slug       = $this->menus['slug'] ?: 'theme-options';
    $this->icon       = $this->menus['icon'] ?: 'dashicons-admin-settings';
    $this->position   = $this->menus['position'] ?: 99;
  }

  public function wph_create_settings()
  {
    add_menu_page($this->title, $this->title, $this->capability, $this->slug, [$this, 'wph_settings_content'], $this->icon, $this->position);
  }

  public function wph_settings_content()
  {
?>
    <div class="wrap">
      <h1><?php echo $this->title; ?></h1>
      <?php settings_errors(); ?>
      <form method="POST" action="options.php">
        <?php
        settings_fields($this->slug);
        do_settings_sections($this->slug);
        submit_button();
        ?>
      </form>
    </div>
  <?php
  }

  public function wph_setup_sections()
  {
    add_settings_section($this->slug, $this->escription, [], $this->slug);
  }

  public function wph_setup_fields()
  {
    foreach ($this->fields as $field) {
      add_settings_field($field['id'], $field['label'], [$this, 'wph_field_callback'], $this->slug, $this->slug, $field);
      register_setting($this->slug, $field['id']);
    }
  }
  public function wph_field_callback($field)
  {
    $value = get_option($field['id']);
    $placeholder = '';
    if (isset($field['placeholder'])) {
      $placeholder = $field['placeholder'];
    }
    require_once get_template_directory() . '/inc/fields.php';
    $theme_fields = new Theme_fields();
    switch ($field['type']) {

      case 'media':
        printf('%s', $theme_fields->media($field, $value));
        break;

      case 'categories':
        printf('%s', wp_dropdown_categories([
          'selected'         => $value,
          'hide_empty'       => 0,
          'echo'             => 0,
          'name'             => $field['id'],
          'id'               => $field['id'],
          'show_option_none' => __('选择一个分类', 'example-text'),
          'taxonomy'         => $field['taxonomy'] ?: 'category',
        ]));
        break;

      case 'pages':
        printf('%s', wp_dropdown_pages([
          'selected'         => $value,
          'echo'             => 0,
          'name'             => $field['id'],
          'id'               => $field['id'],
          'show_option_none' => __('选择一个页面', 'example-text'),
        ]));
        break;

      case 'select':
      case 'multiselect':
        if (!empty($field['options']) && is_array($field['options'])) {
          $attr = '';
          $options = '';
          foreach ($field['options'] as $key => $label) {
            $options .= sprintf(
              '<option value="%s" %s>%s</option>',
              $key,
              selected($value, $key, false),
              $label
            );
          }
          if ($field['type'] === 'multiselect') {
            $attr = ' multiple="multiple" ';
          }
          printf(
            '<select name="%1$s" id="%1$s" %2$s>%3$s</select>',
            $field['id'],
            $attr,
            $options
          );
        }
        break;

      case 'radio':
        if (!empty($field['options']) && is_array($field['options'])) {
          $options_markup = '';
          $iterator = 0;
          foreach ($field['options'] as $key => $label) {
            $iterator++;
            $options_markup .= sprintf(
              '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
              $field['id'],
              $field['type'],
              $key,
              checked($value, $key, false),
              $label,
              $iterator
            );
          }
          printf(
            '<fieldset>%s</fieldset>',
            $options_markup
          );
        }
        break;

      case 'checkbox':
        printf(
          '<input %s id="%s" name="%s" type="checkbox" value="1">',
          $value === '1' ? 'checked' : '',
          $field['id'],
          $field['id']
        );
        break;

      case 'wysiwyg':
        wp_editor($value, $field['id']);
        break;

      case 'textarea':
        printf(
          '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
          $field['id'],
          $placeholder,
          $value
        );
        break;

      default:
        printf(
          '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
          $field['id'],
          $field['type'],
          $placeholder,
          $value
        );
    }
    if (isset($field['desc'])) {
      if ($desc = $field['desc']) {
        printf('<p class="description">%s </p>', $desc);
      }
    }
  }

  public function media_fields()
  {
  ?>
    <script>
      jQuery(document).ready(function($) {
        if (typeof wp.media !== 'undefined') {
          var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
          $('.menutitle-media').click(function(e) {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(this);
            var id = button.attr('id').replace('_button', '');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment) {
              if (_custom_media) {
                if ($('input#' + id).data('return') == 'url') {
                  $('input#' + id).val(attachment.url);
                } else {
                  $('input#' + id).val(attachment.id);
                }
                $('div#preview' + id).css('background-image', 'url(' + attachment.url + ')');
              } else {
                return _orig_send_attachment.apply(this, [props, attachment]);
              };
            }
            wp.media.editor.open(button);
            return false;
          });
          $('.add_media').on('click', function() {
            _custom_media = false;
          });
          $('.remove-media').on('click', function() {
            var parent = $(this).parents('td');
            parent.find('input[type="text"]').val('');
            parent.find('div').css('background-image', 'url()');
          });
        }
      });
    </script>
<?php
  }
}

if (class_exists('Theme_Settings_Pages')) {
  $option = new Theme_Settings_Pages();
  $option->fields([
    'fields'     => [
      // text /
      // email /
      // url /
      // number /
      // tel /
      // date /
      // time /
      // password /
      // textarea /
      // checkbox /
      // users /
      // pages /
      [
        'label'   => __('第一个', 'example-text'),
        'id'      => 'text_id',
        'type'    => 'text',
        'section' => 'one_section',
      ],
      [
        'label'   => __('第一个', 'example-text'),
        'id'      => 'pages_id',
        'type'    => 'pages',
        'section' => 'one_section',
      ],
      [
        'label'    => __('分类', 'example-text'),
        'id'       => 'categories_id',
        'type'     => 'categories',
        'section' => 'one_section',
        /* 'taxonomy' => ['videos'], */
      ],
      /* // radio
      // select
      [
        'label'   => __('单选', 'example-text'),
        'id'      => 'radio_id',
        'type'    => 'select',
        'default' => '2',
        'options' => [
          'one',
          'two',
          'other',
        ]
      ],
      [
        'label'       => __('媒体', 'example-text'),
        'id'          => 'media_id',
        'type'        => 'media',
        'returnvalue' => 'id', // 可选id或url模式
      ],
      [
        'label'         => 'wysiwyg',
        'id'            => 'wysiwyg_id',
        'type'          => 'wysiwyg',
        'media_buttons' => false,
        'textarea_rows' => 5,
        'quicktags'     => false,
        'teeny'         => false,
      ], */
    ]
  ]);
}
