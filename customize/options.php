<?php

require_once get_template_directory() . '/customize/fields.php';

// Settings Page: theme-options
// Retrieving values: get_option( 'your_field_id' )
class Theme_Options
{
  /**
   * @param string title        标题
   * @param string escription   描述
   * @param string capability   权限
   * @param string slug         唯一名称
   * @param string icon         图标
   * @param string position     位置 2仪表盘 | 4分隔符 | 5文章 10媒体 15链接 20页面 25评论 59分隔符 60外观 65插件 70用户 75工具 80设置 99分隔符
   * @return void
   * @link https://developer.wordpress.org/resource/dashicons
   * @link https://developer.wordpress.org/reference/functions/add_menu_page/
   */
  public function __construct()
  {
    add_action('admin_menu', [$this, 'option_create_settings']);
    add_action('admin_init', [$this, 'option_setup_sections']);
    add_action('admin_init', [$this, 'option_setup_fields']);

    add_action('admin_enqueue_scripts', [$this, 'meta_post_enqueue_scripts']);
    add_action('admin_footer', [$this, 'option_footer_scripts']);
  }

  public function meta_post_enqueue_scripts()
  {
    global $typenow;
    $slugs = is_array($this->slug) ?: explode(',', $this->slug);
    if (in_array($typenow, $slugs)) {
      wp_enqueue_media();
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style('wp-color-picker');
    }
  }

  public function option_footer_scripts()
  {
    global $typenow;
    $slugs = is_array($this->slug) ?: explode(',', $this->slug);
    if (in_array($typenow, $slugs)) {
      $theme_fields = new Theme_fields();
      return $theme_fields->footer_script();
    }
  }

  public function fields($fields = [])
  {
    $this->fields     = $fields['fields'];
    $this->title      = $fields['title'] ?: __('主题设置', 'example-text');
    $this->escription = $fields['escription'] ?: '';
    $this->capability = $fields['capability'] ?: 'manage_options';
    $this->slug       = $fields['slug'] ?: 'theme-options';
    $this->icon       = $fields['icon'] ?: 'dashicons-admin-settings';
    $this->position   = $fields['position'] ?: 99;
  }

  public function option_create_settings()
  {
    add_menu_page($this->title, $this->title, $this->capability, $this->slug, [$this, 'settings_content'], $this->icon, $this->position);
  }

  public function settings_content()
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

  public function option_setup_sections()
  {
    add_settings_section($this->slug, $this->escription, [], $this->slug);
  }

  public function option_setup_fields()
  {
    $fields = $this->fields;
    if ($fields) {
      foreach ($fields as $field) {
        add_settings_field($field['id'], $field['label'], [$this, 'field_callback'], $this->slug, $this->slug, $field);
        register_setting($this->slug, $field['id']);
      }
    }
  }

  public function field_callback($field)
  {
    $theme_fields = new Theme_fields();
    $placeholder  = '';
    $value        = get_option($field['id']);
    if (empty($value)) {
      if (isset($field['default'])) {
        $value = $field['default'];
      }
      if (isset($field['placeholder'])) {
        $placeholder = $field['placeholder'];
      }
    }
    switch ($field['type']) {

      case 'media':
        printf('%s', $theme_fields->media($field, $value));
        break;

      case 'categories':
        printf('%s', $theme_fields->categories($field, $value));
        break;

      case 'pages':
        printf('%s', $theme_fields->pages($field, $value));
        break;

      case 'users':
        printf('%s', $theme_fields->users($field, $value));
        break;

      case 'checkbox':
        printf('%s', $theme_fields->checkbox($field, $value));
        break;

      case 'select':
      case 'multiselect':
        printf('%s', $theme_fields->selects($field, $value));
        break;

      case 'radio':
        printf('%s', $theme_fields->radio($field, $value));
        break;

      case 'wysiwyg':
        printf('%s', $theme_fields->wysiwyg($field, $value));
        break;

      case 'textarea':
        printf('%s', $theme_fields->textarea($field, $value, $placeholder));
        break;

      default:
        printf('%s', $theme_fields->text($field, $value, $placeholder));
    }
    if (isset($field['desc'])) {
      if ($desc = $field['desc']) {
        printf('<p class="description">%s </p>', $desc);
      }
    }
  }
}
