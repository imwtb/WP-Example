<?php

// textarea
// text
// - - email
// - - url
// - - tel
// - - password
// - - color
// - - range
// - - number
// - - month
// - - date
// - - week
// - - time
// - checkbox
// - pages
// - users
// - categories ['taxonomy' => ['category']]
// select
// radio
// image

class Widget_fields
{

  // 多行文本
  function textarea($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label> <textarea class="widefat" id="%1$s" name="%1$s" rows="6" cols="6" value="%3$s">%3$s</textarea></p>',
      $field['id'],
      $field['label'],
      $value
    );
    return $input;
  }

  // 文本
  function text($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%1$s" type="%3$s" value="%4$s" /></p>',
      $field['id'],
      $field['label'],
      $field['type'],
      $value,
    );
    return $input;
  }

  // 滑块
  function text_minmax($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label> <input class="widefat" id="%1$s" name="%1$s" type="%3$s" value="%4$s" %5$s %6$s %7$s /></p>',
      $field['id'],
      $field['label'],
      $field['type'],
      $value,
      isset($field['max']) ? 'max="' . $field['max'] . '"' : '',
      isset($field['min']) ? 'min="' . $field['min'] . '"' : '',
      isset($field['step']) ? 'step="' . $field['step'] . '"' : '',
    );
    return $input;
  }

  // 选框
  function checkbox($field, $value)
  {
    $input = sprintf(
      '<p><input class="checkbox" type="checkbox" %3$s id="%1$s" name="%1$s" value="1" /> <label for="%1$s">%2$s:</label></p>',
      $field['id'],
      $field['label'],
      checked($value, true, false),
    );
    return $input;
  }

  // 页面
  function pages($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label>',
      $field['id'],
      $field['label'],
    );
    $input .= wp_dropdown_pages([
      'show_option_none' => __('选择页面', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'echo'             => 0,
    ]);
    $input .= '</p>';
    return $input;
  }

  // 用户
  function users($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label>',
      $field['id'],
      $field['label'],
    );
    $input .= wp_dropdown_users([
      'show_option_none' => __('选择用户', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'echo'             => 0,
    ]);
    $input .= '</p>';
    return $input;
  }

  // 分类
  function categories($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label>',
      $field['id'],
      $field['label'],
    );
    $input .= wp_dropdown_categories([
      'show_option_none' => __('选择分类', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'taxonomy'         => isset($field['taxonomy']) ? $field['taxonomy'] : ['category'],
      'hide_empty'       => 0,
      'echo'             => 0,
    ]);
    $input .= '</p>';
    return $input;
  }

  // 下拉框
  function selects($field, $value)
  {
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label> <select name="%1$s" id="%1$s">',
      $field['id'],
      $field['label'],
    );
    foreach ($field['options'] as $key => $label) {
      $input .= sprintf(
        '<option value="%1$s" %2$s>%3$s</option>',
        $key,
        selected($value, $key, false),
        $label
      );
    }
    $input .= '</select></p>';
    return $input;
  }

  // 单选
  function radio($field, $value)
  {
    $input    = '<fieldset>';
    $iterator = 0;
    foreach ($field['options'] as $key => $label) {
      $iterator++;
      $input .= sprintf(
        '<p><label for="%1$s">%2$s:</label> <br><label for="%1$s_%7$s"><input id="%1$s_%7$s" name="%1$s" type="%3$s" value="%4$s" %5$s /> %6$s</label>&nbsp;&nbsp;&nbsp;&nbsp;',
        $field['id'],
        $field['label'],
        $field['type'],
        $key,
        checked($value, $key, false),
        $label,
        $iterator,
      );
    }
    $input .= '</fieldset>';
    return $input;
  }

  // 图片
  function image($field, $value)
  {
    $media_url = '';
    if ($value) {
      $media_url = wp_get_attachment_url($value);
    }
    $input = sprintf(
      '<p><label for="%1$s">%2$s:</label> <input style="display:none;" class="widefat" id="%1$s" name="%1$s" type="%3$s" value="%4$s"><span id="preview%1$s" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url(' . $media_url . ');background-size:contain;background-repeat:no-repeat;"></span><button id="%1$s" class="button select-media custommedia">%5$s</button><input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="%6$s" /></p>',
      $field['id'],
      $field['label'],
      $field['type'],
      $value,
      __('添加', 'imetb'),
      __('移除', 'imetb'),
    );
    return $input;
  }

  function footer_script()
  {
?>
    <script class="footer_scripts">
      jQuery(document).ready(function($) {
        if (typeof wp.media !== 'undefined') {
          var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
          $('.new-media').click(function(e) {
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

        $('.text-color-picker').wpColorPicker();
      });
    </script>
<?php
  }
}
