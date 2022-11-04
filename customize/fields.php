<?php

class Theme_Fields
{

  function text($field, $value, $placeholder)
  {
    $input = sprintf(
      '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
      $field['id'],
      $field['type'],
      $placeholder,
      $value
    );
    return $input;
  }

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

  function media($field, $value)
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
      '<input style="display:none;" id="%s" name="%s" type="text" value="%s" data-return="%s"><div id="preview%s" style="margin-right:10px;border:1px solid #e2e4e7;background-color:#fafafa;display:inline-block;width: 100px;height:100px;background-image:url(%s);background-size:cover;background-repeat:no-repeat;background-position:center;"></div><div><input style="width: 19%%;margin-right:5px;" class="button new-media" id="%s_button" name="%s_button" type="button" value="' . esc_html__('选择', 'example-text') . '" /><input style="width: 19%%;" class="button remove-media" id="%s_buttonremove" name="%s_buttonremove" type="button" value="' . esc_html__('移除', 'example-text') . '" /></div>',
      $field['id'],
      $field['id'],
      $value,
      $field['returnvalue'],
      $field['id'],
      $meta_url,
      $field['id'],
      $field['id'],
      $field['id'],
      $field['id']
    );
    return $input;
  }

  function categories($field, $value)
  {
    $input = wp_dropdown_categories([
      'selected'         => $value,
      'hide_empty'       => 0,
      'echo'             => 0,
      'name'             => $field['id'],
      'id'               => $field['id'],
      'show_option_none' => __('选择一个分类', 'example-text'),
      'taxonomy'         => $field['taxonomy'] ?: 'category',
    ]);
    return $input;
  }

  function pages($field, $value)
  {
    $input = wp_dropdown_pages([
      'selected'         => $value,
      'echo'             => 0,
      'name'             => $field['id'],
      'id'               => $field['id'],
      'show_option_none' => __('选择一个页面', 'example-text'),
    ]);
    return $input;
  }

  function users($field, $value)
  {
    $input = wp_dropdown_users([
      'selected'         => $value,
      'echo'             => 0,
      'name'             => $field['id'],
      'id'               => $field['id'],
      'show_option_none' => __('选择一个用户', 'example-text'),
    ]);
    return $input;
  }

  function checkbox($field, $value)
  {
    $input = sprintf(
      '<input %s id=" %s" name="%s" type="checkbox" value="1">',
      $value === '1' ? 'checked' : '',
      $field['id'],
      $field['id']
    );
    return $input;
  }

  function radio($field, $value)
  {
    $input = '<fieldset>';
    $iterator = 0;
    foreach ($field['options'] as $key => $label) {
      $iterator++;
      $input .= sprintf(
        '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
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

  function selects($field, $value)
  {
    if ($field['type'] === 'multiselect') {
      $attr = ' multiple="multiple" ';
    }
    $input = sprintf(
      '<select name="%1$s" id="%1$s" %2$s>',
      $field['id'],
      $attr,
    );
    foreach ($field['options'] as $key => $label) {
      $input .= sprintf(
        '<option value="%s" %s>%s</option>',
        $key,
        selected($value, $key, false),
        $label
      );
    }
    $input .= '</select>';
    return $input;
  }

  function wysiwyg($field, $value)
  {
    ob_start();
    wp_editor($value, $field['id'], [
      'textarea_name' => $field['id'],
      'textarea_rows' => $field['rows'] ? $field['rows'] : 5,
      'media_buttons' => $field['media_buttons'] ? true : false,
      'quicktags'     => $field['quicktags'] ? true : false,
      'teeny'         => $field['teeny'] ? true : false,
    ]);
    $input = ob_get_contents();
    ob_end_clean();
    return $input;
  }

  function media_script()
  {
?>
    <script>
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
      });
    </script>
<?php
  }
}
