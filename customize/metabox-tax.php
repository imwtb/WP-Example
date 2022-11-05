<?php

require_once get_template_directory() . '/customize/fields.php';

// Meta Box Class: MetaBoxTax
// Get the field value: $metavalue = get_term_meta( $post_id, $field_id, true );
class MetaBoxTax
{

  public function __construct()
  {
    if (is_admin()) {
      $terms = is_array($this->terms) ?: explode(',', $this->terms);
      foreach ($terms as $value) {
        add_action($value . '_add_form_fields', [$this, 'metabox_create_fields'], 10, 2);
        add_action($value . '_edit_form_fields', [$this, 'metabox_edit_fields'],  10, 2);
        add_action('created_' . $value, [$this, 'metabox_save_fields'], 10, 1);
        add_action('edited_' . $value,  [$this, 'metabox_save_fields'], 10, 1);
      }
      add_action('admin_footer', [$this, 'metabox_media_fields']);
      add_action('admin_enqueue_scripts', 'wp_enqueue_media');
    }
  }

  public function metabox_media_fields()
  {
    $theme_fields = new Theme_fields();
    return $theme_fields->media_script();
  }

  public function fields($fields = [])
  {
    $this->terms  = $fields['term'] ?: 'category';
    $this->fields = $fields['fields'];
  }

  public function metabox_create_fields($taxonomy)
  {
    $output      = '';
    $placeholder = '';
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      if (empty($value)) {
        if (isset($field['default'])) {
          $value = $field['default'];
        }
        if (isset($field['placeholder'])) {
          $placeholder = $field['placeholder'];
        }
      }
      switch ($field['type']) {
        default:
          $input = $theme_fields->text($field, $value, $placeholder);
      }
      $output .= '<div class="form-field">' . $this->format_rows($label, $input) . '</div>';
    }
    echo $output;
  }
  public function metabox_edit_fields($term, $taxonomy)
  {
    $output       = '';
    $placeholder  = '';
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label       = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      $value       = get_term_meta($term->term_id, $field['id'], true);
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
    echo '<div class="form-field">' . $output . '</div>';
  }
  public function format_rows($label, $input)
  {
    return '<tr class="form-field"><th>' . $label . '</th><td>' . $input . '</td></tr>';
  }
  public function metabox_save_fields($term_id)
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
      } else if ($field['type'] === 'checkbox') {
        update_term_meta($term_id, $field['id'], '0');
      }
    }
  }
}
