<?php

require_once get_template_directory() . '/customize/fields.php';

// Meta Box Class: MetaBoxTax
// Get the field value: $metavalue = get_term_meta( $post_id, $field_id, true );
class MetaBoxTax
{

  public function __construct($terms = ['category'])
  {
    $this->terms = is_array($terms) ? $terms : explode(',', $terms);
    if (is_admin()) {
      foreach ($this->terms as $value) {
        add_action($value . '_add_form_fields', [$this, 'meta_tax_create_fields'], 10, 2);
        add_action($value . '_edit_form_fields', [$this, 'meta_tax_edit_fields'], 10, 2);
        add_action('created_' . $value, [$this, 'meta_tax_save_fields'], 10, 1);
        add_action('edited_' . $value, [$this, 'meta_tax_save_fields'], 10, 1);

        add_action('admin_enqueue_scripts', [$this, 'meta_post_enqueue_scripts']);
        add_action('admin_footer', [$this, 'metabox_footer_scripts']);
      }
    }
  }

  public function meta_post_enqueue_scripts()
  {
    foreach ($this->terms as $tax) {
      if (get_current_screen()->taxonomy == $tax) {
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
      }
    }
  }

  public function metabox_footer_scripts()
  {
    foreach ($this->terms as $tax) {
      if (get_current_screen()->taxonomy == $tax) {
        $theme_fields = new Theme_fields();
        return $theme_fields->footer_script();
      }
    }
  }

  public function fields($fields = [])
  {
    $this->fields = $fields['fields'];
  }

  public function meta_tax_create_fields($taxonomy)
  {
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label       = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      $value       = isset($field['default']) ? $field['default'] : '';
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
      $output = '<div class="form-field">' . $this->format_rows($label, $export) . '</div>';
    }
    echo $output;
  }

  public function meta_tax_edit_fields($term, $taxonomy)
  {
    $theme_fields = new Theme_fields();
    foreach ($this->fields as $field) {
      $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
      $value = get_term_meta($term->term_id, $field['id'], true);
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
      $output = $this->format_rows($label, $export);
    }
    echo '<div class="form-field">' . $output . '</div>';
  }

  public function format_rows($label, $export)
  {
    return '<tr class="form-field"><th>' . $label . '</th><td>' . $export . '</td></tr>';
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
