<?php
if (current_user_can('manage_options')) {
  edit_post_link(esc_html__('编辑', 'example-text'), '', '', get_the_ID(), '');
  delete_post_link(esc_html__('删除', 'example-text'), '', '', get_the_ID(), '');
}
