<?php

require_once get_template_directory() . '/customize/fields.php';

// Meta Box Class: MetaBoxPost
// Get the field value: $metavalue = get_post_meta( $post_id, $field_id, true );
class MetaBoxPost
{

  /**
   * @param string $id          唯一ID
   * @param string $title       标题
   * @param array $screen       显示在哪些页面：post | page | dashboard | link | afs | comment
   * @param string $context     上下文显示位置：normal | side | advanced
   * @param string $priority    上下文优先级：high | core | default | low
   * @return void
   * @link https://developer.wordpress.org/reference/functions/add_meta_box/
   */
  public function __construct()
  {
    add_action('add_meta_boxes', [$this, 'meta_post_add_boxes']);
    add_action('save_post', [$this, 'meta_post_save_fields']);

    add_action('admin_enqueue_scripts', [$this, 'meta_post_enqueue_scripts']);
    add_action('admin_footer', [$this, 'meta_post_footer_scripts']);
  }

  public function meta_post_enqueue_scripts()
  {
    global $typenow;
    $screens = is_array($this->screens) ?: explode(',', $this->screens);
    if (in_array($typenow, $screens)) {
      wp_enqueue_media();
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style('wp-color-picker');
    }
  }

  public function meta_post_footer_scripts()
  {
    global $typenow;
    $screens = is_array($this->screens) ? $this->screens : explode(',', $this->screens);
    if (in_array($typenow, $screens)) {
      $theme_fields = new Theme_fields();
      return $theme_fields->footer_script();
    }
  }

  public function fields($fields = [])
  {
    $this->menus    = $fields;
    $this->fields   = $fields['fields'];
    $this->id       = isset($this->menus['meta_id']) ? $this->menus['meta_id'] : 'metabox_id';
    $this->title    = isset($this->menus['title']) ? $this->menus['title'] : __('元框', 'imwtb');
    $this->screens  = isset($this->menus['screen']) ? $this->menus['screen'] : 'post';
    $this->context  = isset($this->menus['context']) ? $this->menus['context'] : 'advanced';
    $this->priority = isset($this->menus['priority']) ? $this->menus['priority'] : 'high';
  }

  public function meta_post_add_boxes()
  {
    $screens = is_array($this->screens) ? $this->screens : explode(',', $this->screens);
    foreach ($screens as $screen) {
      add_meta_box(
        $this->id,
        $this->title,
        [$this, 'meta_box_callback'],
        $screen,
        $this->context,
        $this->priority,
      );
    }
  }

  public function meta_box_callback($post)
  {
    wp_nonce_field('meta_post_data', 'meta_post_nonce');
    if (isset($this->menus['description'])) echo $this->menus['description'];
    $this->field_generator($post);
  }

  public function field_generator($post)
  {
    $output       = '';
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      $value = get_post_meta($post->ID, $field['id'], true);
      if (empty($value)) {
        $value = isset($field['default']) ? $field['default'] : '';
      }
      $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
      switch ($field['type']) {

        case 'notes':
          $export = $theme_fields->notes($field);
          break;

        case 'textarea':
          $export = $theme_fields->textarea($field, $value, $placeholder);
          break;

        case 'range':
        case 'number':
        case 'month':
        case 'date':
        case 'week':
        case 'time':
          $export = $theme_fields->text_minmax($field, $value, $placeholder);
          break;

        case 'checkbox':
          $export = $theme_fields->checkbox($field, $value);
          break;

        case 'pages':
          $export = $theme_fields->pages($field, $value);
          break;

        case 'users':
          $export = $theme_fields->users($field, $value);
          break;

        case 'categories':
          $export = $theme_fields->categories($field, $value);
          break;

        case 'select':
          $export = $theme_fields->selects($field, $value);
          break;

        case 'radio':
          $export = $theme_fields->radio($field, $value);
          break;

        case 'file':
          $export = $theme_fields->file($field, $value, $placeholder) . $theme_fields->button($field);
          break;

        case 'image':
          $export = $theme_fields->image($field, $value) . $theme_fields->button($field);
          break;

        case 'wysiwyg':
          $export = $theme_fields->wysiwyg($field, $value);
          break;

        default:
          $export = $theme_fields->text($field, $value, $placeholder);
      }
      $output .= $this->format_rows($label, $export);
    }
    echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
  }

  public function format_rows($label, $export)
  {
    return '<tr><th>' . $label . '</th><td>' . $export . '</td></tr>';
  }

  public function meta_post_save_fields($post_id)
  {
    if (!isset($_POST['meta_post_nonce']))
      return $post_id;
    $nonce = $_POST['meta_post_nonce'];
    if (!wp_verify_nonce($nonce, 'meta_post_data'))
      return $post_id;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return $post_id;
    foreach ($this->fields as $field) {
      if (!empty($_POST[$field['id']]) && $_POST[$field['id']] !== '' && $_POST[$field['id']] !== '-1') {
        switch ($field['type']) {
          case 'url':
            $_POST[$field['id']] = sanitize_url($_POST[$field['id']]);
            break;
          case 'email':
            $_POST[$field['id']] = sanitize_email($_POST[$field['id']]);
            break;
          default:
            $_POST[$field['id']] = sanitize_text_field($_POST[$field['id']]);
        }
        update_post_meta($post_id, $field['id'], $_POST[$field['id']]);
      } elseif ($field['type'] === 'checkbox' && $_POST[$field['id']] != 0) {
        update_post_meta($post_id, $field['id'], 0);
      } else {
        delete_post_meta($post_id, $field['id']);
      }
    }
  }
}
