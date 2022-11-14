<?php

require_once get_template_directory() . '/widgets/fields.php';

// Adds widget: Title
class Title_Widget extends WP_Widget
{

  // Register widget with WordPress
  function __construct()
  {
    parent::__construct('title_widget', __('自定义小工具', 'imwtb'), ['description' => __('自定义小工具描述', 'imwtb')]);
    add_action('admin_footer', [$this, 'media_fields']);
    add_action('customize_controls_print_footer_scripts', [$this, 'media_fields']);
  }

  // Widget fields
  private function fields()
  {
    $fields = [
      // text
      // textarea
      // checkbox
      // media
      // email
      // url
      // password
      // number
      // tel
      // date
      [
        'label'   => __('文本', 'imwtb'),
        'id'      => 'text_id',
        'type'    => 'text',
      ],
      [
        'label'   => __('下拉选项', 'imwtb'),
        'id'      => 'select_id',
        'default' => '1',
        'type'    => 'select',
        'options' => [
          'one',
          'two',
          'other'
        ]
      ]
    ];
    return $fields;
  }

  // Frontend display of widget
  public function widget($args, $instance)
  {
    echo $args['before_widget'];

    // Output widget title
    if (!empty($instance['title'])) {
      echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
    }

    // Output generated fields

    echo $args['after_widget'];
  }

  // Media field backend
  public function media_fields()
  {
    $widget_fields = new widget_fields();
    return $widget_fields->footer_script();
  }

  // Back-end widget fields
  public function field_generator($instance)
  {
    $widget_fields = new Widget_fields();
    foreach ($this->fields() as $field) {
      $default = isset($field['default']) ? $field['default'] : '';
      $value   = !empty($instance[$field['id']]) ? $instance[$field['id']] : $default;
      switch ($field['type']) {

        case 'textarea':
          $output = $widget_fields->textarea(get_field_id($field), $value);
          break;

        case 'range':
        case 'number':
        case 'month':
        case 'date':
        case 'week':
        case 'time':
          $output = $widget_fields->text_minmax(get_field_id($field), $value);
          break;

        case 'checkbox':
          $output = $widget_fields->checkbox(get_field_id($field), $value);
          break;

        case 'pages':
          $output = $widget_fields->pages(get_field_id($field), $value);
          break;

        case 'users':
          $output = $widget_fields->users(get_field_id($field), $value);
          break;

        case 'categories':
          $output = $widget_fields->categories(get_field_id($field), $value);
          break;

        case 'select':
          $output = $widget_fields->selects(get_field_id($field), $value);
          break;

        case 'radio':
          $output = $widget_fields->radio(get_field_id($field), $value);
          break;

        case 'image':
          $output = $widget_fields->image(get_field_id($field), $value);
          break;

        default:
          $output = $widget_fields->text(get_field_id($field), $value);
      }
    }
    echo $output;
  }

  public function form($instance)
  {
    $title = !empty($instance['title']) ? $instance['title'] : __('标题', 'imwtb');
?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('标题:', 'imwtb'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
    </p>
<?php
    $this->field_generator($instance);
  }

  // Sanitize widget form values as they are saved
  public function update($new_instance, $old_instance)
  {
    $instance = [];
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    foreach ($this->fields() as $field) {
      switch ($field['type']) {
        default:
          $instance[$field['id']] = (!empty($new_instance[$field['id']])) ? strip_tags($new_instance[$field['id']]) : '';
      }
    }
    return $instance;
  }
}

function register_Title_widget()
{
  register_widget('Title_Widget');
}
add_action('widgets_init', 'register_Title_widget');
