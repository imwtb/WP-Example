<?php

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
  private function widget_fields()
  {
    $widget_fields = [
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
    return $widget_fields;
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
    echo '<p>' . $instance['text_id'] . '</p>';
    echo '<p>' . $instance['textarea_id'] . '</p>';
    echo '<p>' . $instance['checkbox_id'] . '</p>';
    echo '<p>' . $instance['select_id'] . '</p>';
    echo '<p>' . $instance['media_id'] . '</p>';
    echo '<p>' . $instance['email_id'] . '</p>';
    echo '<p>' . $instance['url_id'] . '</p>';
    echo '<p>' . $instance['password'] . '</p>';
    echo '<p>' . $instance['number_id'] . '</p>';
    echo '<p>' . $instance['tel_id'] . '</p>';
    echo '<p>' . $instance['date_id'] . '</p>';

    echo $args['after_widget'];
  }

  // Media field backend
  public function media_fields()
  {
?>
    <script>
      jQuery(document).ready(function($) {
        if (typeof wp.media !== 'undefined') {
          var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
          $(document).on('click', '.custommedia', function(e) {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(this);
            var id = button.attr('id');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment) {
              if (_custom_media) {
                $('input#' + id).val(attachment.id);
                $('span#preview' + id).css('background-image', 'url(' + attachment.url + ')');
                $('input#' + id).trigger('change');
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
          $(document).on('click', '.remove-media', function() {
            var parent = $(this).parents('p');
            parent.find('input[type="media"]').val('').trigger('change');
            parent.find('span').css('background-image', 'url()');
          });
        }
      });
    </script>
  <?php
  }

  // Back-end widget fields
  public function field_generator($instance)
  {
    $output = '';
    foreach ($this->widget_fields() as $widget_field) {
      $default      = '';
      $default      = isset($widget_field['default']) ? $widget_field['default'] : '';
      $widget_value = !empty($instance[$widget_field['id']]) ? $instance[$widget_field['id']] : __($default, 'imwtb');
      switch ($widget_field['type']) {
        case 'textarea':
          $output .= '<p>';
          $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'domtest') . ':</label> ';
          $output .= '<textarea class="widefat" id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" rows="6" cols="6" value="' . esc_attr($widget_value) . '">' . $widget_value . '</textarea>';
          $output .= '</p>';
          break;

        case 'checkbox':
          $output .= '<p>';
          $output .= '<input class="checkbox" type="checkbox" ' . checked($widget_value, true, false) . ' id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" value="1">';
          $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'domtest') . '</label>';
          $output .= '</p>';
          break;

        case 'select':
          $output .= '<p>';
          $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'textdomain') . ':</label> ';
          $output .= '<select id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '">';
          foreach ($widget_field['options'] as $option) {
            if ($widget_value == $option) {
              $output .= '<option value="' . $option . '" selected>' . $option . '</option>';
            } else {
              $output .= '<option value="' . $option . '">' . $option . '</option>';
            }
          }
          $output .= '</select>';
          $output .= '</p>';
          break;

        case 'image':
          $media_url = '';
          if ($widget_value) {
            $media_url = wp_get_attachment_url($widget_value);
          }
          $output .= '<p>';
          $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'domtest') . ':</label> ';
          $output .= '<input style="display:none;" class="widefat" id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" type="' . $widget_field['type'] . '" value="' . $widget_value . '">';
          $output .= '<span id="preview' . esc_attr($this->get_field_id($widget_field['id'])) . '" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url(' . $media_url . ');background-size:contain;background-repeat:no-repeat;"></span>';
          $output .= '<button id="' . $this->get_field_id($widget_field['id']) . '" class="button select-media custommedia">Add Media</button>';
          $output .= '<input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="Clear" />';
          $output .= '</p>';
          break;

        default:
          $output .= '<p>';
          $output .= '<label for="' . esc_attr($this->get_field_id($widget_field['id'])) . '">' . esc_attr($widget_field['label'], 'imwtb') . ':</label> ';
          $output .= '<input class="widefat" id="' . esc_attr($this->get_field_id($widget_field['id'])) . '" name="' . esc_attr($this->get_field_name($widget_field['id'])) . '" type="' . $widget_field['type'] . '" value="' . esc_attr($widget_value) . '">';
          $output .= '</p>';
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
    foreach ($this->widget_fields() as $widget_field) {
      switch ($widget_field['type']) {
        default:
          $instance[$widget_field['id']] = (!empty($new_instance[$widget_field['id']])) ? strip_tags($new_instance[$widget_field['id']]) : '';
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
