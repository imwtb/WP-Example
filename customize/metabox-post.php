<?php

require_once get_template_directory() . '/customize/fields.php';

// Meta Box Class: MetaBoxPost
// Get the field value: $metavalue = get_post_meta( $post_id, $field_id, true );
class MetaBoxPost
{

  /**
   * @param string $id          唯一ID
   * @param string $title       标题
   * @param array $screen       显示在哪些页面：post、page、dashboard、link、afs、comment
   * @param string $context     上下文显示位置：normal、side、advanced
   * @param string $priority    上下文优先级：high、core、default、low
   * @return void
   * @link https://developer.wordpress.org/reference/functions/add_meta_box/
   */
  public function __construct()
  {
    add_action('add_meta_boxes', [$this, 'metabox_add_boxes']);
    add_action('admin_footer', [$this, 'metabox_media_fields']);
    add_action('save_post', [$this, 'metabox_save_fields']);
  }

  public function metabox_media_fields()
  {
    $theme_fields = new Theme_fields();
    return $theme_fields->media_script();
  }

  public function fields($fields = [])
  {
    $this->menus    = $fields;
    $this->fields   = $fields['fields'];
    $this->id       = $this->menus['meta_id'] ?: 'metabox';
    $this->title    = $this->menus['title'] ?: __('元框', 'example-text');
    $this->screen   = $this->menus['screen'] ?: ['post'];
    $this->context  = $this->menus['context'] ?: 'advanced';
    $this->priority = $this->menus['priority'] ?: 'high';
  }

  public function metabox_add_boxes()
  {
    foreach ($this->screen as $single_screen) {
      add_meta_box(
        $this->id,
        $this->title,
        [$this, 'meta_box_callback'],
        $single_screen,
        $this->context,
        $this->priority,
      );
    }
  }

  public function meta_box_callback($post)
  {
    wp_nonce_field('Theme_data', 'Theme_nonce');
    echo $this->menus['description'];
    $this->field_generator($post);
  }

  public function field_generator($post)
  {
    $placeholder  = '';
    $output       = '';
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label       = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      $value       = get_post_meta($post->ID, $field['id'], true);
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
          $input = $theme_fields->media($field, $value);
          break;

        case 'categories':
          $input = $theme_fields->categories($field, $value);
          break;

        case 'pages':
          $input = $theme_fields->pages($field, $value);
          break;

        case 'users':
          $input = $theme_fields->users($field, $value);
          break;

        case 'checkbox':
          $input = $theme_fields->checkbox($field, $value);
          break;

        case 'select':
        case 'multiselect':
          $input = $theme_fields->selects($field, $value);
          break;

        case 'radio':
          $input = $theme_fields->radio($field, $value);
          break;

        case 'wysiwyg':
          $input = $theme_fields->wysiwyg($field, $value);
          break;

        case 'textarea':
          $input = $theme_fields->textarea($field, $value, $placeholder);
          break;

        default:
          $input = $theme_fields->text($field, $value, $placeholder);
      }
      $output .= $this->format_rows($label, $input);
    }
    echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
  }

  public function format_rows($label, $input)
  {
    return '<tr><th>' . $label . '</th><td>' . $input . '</td></tr>';
  }

  public function metabox_save_fields($post_id)
  {
    if (!isset($_POST['Theme_nonce']))
      return $post_id;
    $nonce = $_POST['Theme_nonce'];
    if (!wp_verify_nonce($nonce, 'Theme_data'))
      return $post_id;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return $post_id;
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
        update_post_meta($post_id, $field['id'], $_POST[$field['id']]);
      } else if ($field['type'] === 'checkbox') {
        update_post_meta($post_id, $field['id'], '0');
      }
    }
  }
}
