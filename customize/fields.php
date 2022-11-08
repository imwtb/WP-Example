<?php

class Theme_Fields
{

  // 多行文本
  function textarea($field, $value, $placeholder)
  {
    $input = sprintf(
      '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>',
      $field['id'],
      $placeholder,
      $value
    );
    return $input;
  }

  // 文本
  function text($field, $value, $placeholder)
  {
    $input = sprintf(
      '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" %5$s />',
      $field['id'],
      $field['type'],
      $placeholder,
      $value,
      $field['type'] == 'color' ? 'class="text-color-picker" style="display:none;"' : '',
    );
    return $input;
  }

  // 滑块
  function text_minmax($field, $value, $placeholder)
  {
    $input = sprintf(
      '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" %5$s %6$s %7$s />',
      $field['id'],
      $field['type'],
      $placeholder,
      $value,
      $field['max'] ? 'max="' . $field['max'] . '"' : '',
      $field['min'] ? 'min="' . $field['min'] . '"' : '',
      $field['step'] ? 'step="' . $field['step'] . '"' : '',
    );
    return $input;
  }

  // 选框
  function checkbox($field, $value)
  {
    $input = sprintf(
      '<label><input id="%1$s" name="%1$s" %2$s type="checkbox" value="1" />%3$s</label>',
      $field['id'],
      checked($value, 1, false),
      $field['label'],
    );
    return $input;
  }

  // 页面
  function pages($field, $value)
  {
    $input = wp_dropdown_pages([
      'show_option_none' => __('选择页面', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'echo'             => 0,
    ]);
    return $input;
  }

  // 用户
  function users($field, $value)
  {
    $input = wp_dropdown_users([
      'show_option_none' => __('选择用户', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'echo'             => 0,
    ]);
    return $input;
  }

  // 分类
  function categories($field, $value)
  {
    $input = wp_dropdown_categories([
      'show_option_none' => __('选择分类', 'imwtb'),
      'id'               => $field['id'],
      'name'             => $field['id'],
      'selected'         => $value,
      'taxonomy'         => isset($field['taxonomy']) ?: ['category'],
      'hide_empty'       => 0,
      'echo'             => 0,
    ]);
    return $input;
  }

  // 下拉框
  function selects($field, $value)
  {
    $input = sprintf(
      '<select name="%1$s" id="%1$s">',
      $field['id'],
    );
    foreach ($field['options'] as $key => $label) {
      $input .= sprintf(
        '<option value="%1$s" %2$s>%3$s</option>',
        $key,
        selected($value, $key, false),
        $label
      );
    }
    $input .= '</select>';
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
        '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s" type="%2$s" value="%3$s" %4$s /> %5$s</label>&nbsp;&nbsp;&nbsp;&nbsp;',
        $field['id'],
        $field['type'],
        $key,
        checked($value, $key, false),
        $label,
        $iterator
      );
    }
    $input .= '</fieldset>';
    return $input;
  }

  // 文件
  function file($field, $value, $placeholder)
  {
    $meta_url = '';
    if ($value) {
      if ($field['returnvalue'] == 'url') {
        $meta_url = $value;
      } else {
        $meta_url = wp_get_attachment_url($value);
      }
    }
    $input = sprintf(
      '<input name="%1$s" id="%1$s" type="text" placeholder="%3$s" value="%2$s" data-return="%4$s" />',
      $field['id'],
      $meta_url,
      $placeholder,
      $field['returnvalue'],
    );
    return $input;
  }

  // 图片
  function image($field, $value)
  {
    $meta_url = '';
    if ($value) {
      if ($field['returnvalue'] == 'url') {
        $meta_url = $value;
      } else {
        $meta_url = wp_get_attachment_url($value);
      }
    }
    $input = sprintf(
      '<input style="display:none;" id="%1$s" name="%1$s" type="text" value="%2$s" data-return="%3$s" /><div id="preview%1$s" style="margin-right:10px;border:1px solid #e2e4e7;background-color:#fafafa;display:inline-block;width: 100px;height:100px;background-image:url(%4$s);background-size:cover;background-repeat:no-repeat;background-position:center;"></div>',
      $field['id'],
      $value,
      $field['returnvalue'],
      $meta_url,
    );
    return $input;
  }

  // 添加删除按钮
  function button($field)
  {
    $input = sprintf(
      '<span><input style="margin:0px 5px;" class="button new-media" id="%1$s_button" name="%1$s_button" type="button" value="' . __('选择', 'imwtb') . '" /><input style="margin:0px 5px;" class="button remove-media" id="%1$s_buttonremove" name="%1$s_buttonremove" type="button" value="' . __('移除', 'imwtb') . '" /></span>',
      $field['id']
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

  // 文本编辑器
  function wysiwyg($field, $value)
  {
    ob_start();
    wp_editor($value, $field['id'], [
      'textarea_name' => $field['id'],
      'textarea_rows' => isset($field['rows']) ? $field['rows'] : 5,
      'media_buttons' => isset($field['media_buttons']) ? true : false,
      'quicktags'     => isset($field['quicktags']) ? true : false,
      'teeny'         => isset($field['teeny']) ? true : false,
    ]);
    $input = ob_get_contents();
    ob_end_clean();
    return $input;
  }
}
