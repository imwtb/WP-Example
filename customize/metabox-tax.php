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
        add_action($value . '_add_form_fields', [$this, 'meta_tax_create_fields'], 10, 2);
        add_action($value . '_edit_form_fields', [$this, 'meta_tax_edit_fields'],  10, 2);
        add_action('created_' . $value, [$this, 'meta_tax_save_fields'], 10, 1);
        add_action('edited_' . $value,  [$this, 'meta_tax_save_fields'], 10, 1);
      }
      add_action('admin_enqueue_scripts', [$this, 'meta_post_enqueue_scripts']);
      add_action('admin_footer', [$this, 'metabox_footer_scripts']);
    }
  }

  public function meta_post_enqueue_scripts()
  {
    global $typenow;
    $terms = is_array($this->terms) ?: explode(',', $this->terms);
    if (in_array($typenow,  $terms)) {
      wp_enqueue_media();
      wp_enqueue_script('wp-color-picker');
      wp_enqueue_style('wp-color-picker');
    }
  }

  public function metabox_footer_scripts()
  {
    global $typenow;
    $terms = is_array($this->terms) ?: explode(',', $this->terms);
    if (in_array($typenow,  $terms)) {
      $theme_fields = new Theme_fields();
      return $theme_fields->footer_script();
    }
  }

  public function fields($fields = [])
  {
    $this->terms  = $fields['term'] ?: 'category';
    $this->fields = $fields['fields'];
  }

  public function meta_tax_create_fields($taxonomy)
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

  public function meta_tax_edit_fields($term, $taxonomy)
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

        case 'textarea':
          $input = $theme_fields->textarea($field, $value, $placeholder);
          break;

        case 'range':
        case 'number':
        case 'month':
        case 'date':
        case 'week':
        case 'time':
          $input = $theme_fields->text_minmax($field, $value, $placeholder);
          break;

        case 'checkbox':
          $input = $theme_fields->checkbox($field, $value);
          break;

        case 'pages':
          $input = $theme_fields->pages($field, $value);
          break;

        case 'users':
          $input = $theme_fields->users($field, $value);
          break;

        case 'categories':
          $input = $theme_fields->categories($field, $value);
          break;

        case 'select':
          $input = $theme_fields->selects($field, $value);
          break;

        case 'radio':
          $input = $theme_fields->radio($field, $value);
          break;

        case 'file':
          $input = $theme_fields->file($field, $value, $placeholder) . $theme_fields->button($field);
          break;

        case 'image':
          $input = $theme_fields->image($field, $value) . $theme_fields->button($field);
          break;

        case 'wysiwyg':
          $input = $theme_fields->wysiwyg($field, $value);
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

  public function meta_tax_save_fields($term_id)
  {
    foreach ($this->fields as $field) {
      if (isset($_POST[$field['id']]) && $_POST[$field['id']] !== '' && $_POST[$field['id']] !== '-1') {
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
        update_term_meta($term_id, $field['id'], $_POST[$field['id']]);
      } else if ($field['type'] === 'checkbox' && $_POST[$field['id']] != 0) {
        update_term_meta($term_id, $field['id'], '0');
      } else {
        delete_term_meta($term_id, $field['id']);
      }
    }
  }
}
