<?php

class Theme_Fields
{
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
      '<input style="display:none;" id="%s" name="%s" type="text" value="%s"  data-return="%s"><div id="preview%s" style="margin-right:10px;border:1px solid #e2e4e7;background-color:#fafafa;display:inline-block;width: 100px;height:100px;background-image:url(%s);background-size:cover;background-repeat:no-repeat;background-position:center;"></div><input style="width: 19%%;margin-right:5px;" class="button new-media" id="%s_button" name="%s_button" type="button" value="Select" /><input style="width: 19%%;" class="button remove-media" id="%s_buttonremove" name="%s_buttonremove" type="button" value="Clear" />',
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
}
