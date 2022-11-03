<?php

if (!defined('ABSPATH'))
  exit;

new wtb_Customize_Register;

class wtb_Customize_Register
{

  public $postMessage    = 'postMessage';                     // 选择性刷新
  public $absint         = 'absint';                          // 清理 数字
  public $nohtml         = 'wp_filter_nohtml_kses';           // 清理 Html
  public $text           = 'sanitize_text_field';             // 清理 纯文本
  public $email          = 'sanitize_email';                  // 清理 Email
  public $url            = 'esc_url_raw';                     // 清理 Url
  public $strip_all_tags = 'wp_strip_all_tags';               // 清理 Html，包括脚本和样式
  public $hex_color      = 'sanitize_hex_color';              // 清理 3 或 6 位 十六进制颜色代码
  public $kses_post      = 'wp_kses_post';                    // 清理 Html 保留预设
  public $checkbox       = [__CLASS__, 'is_checkbox'];        // 清理 选框
  public $file           = [__CLASS__, 'is_file'];            // 清理 文件名
  public $range          = [__CLASS__, 'is_range'];           // 清理 范围滑块
  public $select_radio   = [__CLASS__, 'is_select_radio'];    // 清理 单选选项
  public $input_js_code  = [__CLASS__, 'is_input_js_code'];   // 清理 输入脚本
  public $output_js_code = [__CLASS__, 'is_output_js_code'];  // 清理 输出脚本

  public function __construct()
  {
    add_action('customize_register', [$this, 'wtb_panels']);
    add_action('customize_register', [$this, 'wtb_sections']);
    add_action('customize_register', [$this, 'wtb_settings_controls']);
    add_action('customize_preview_init', [$this, 'wtb_preview']);
  }

  /*----------------------------------------
    面板
  ----------------------------------------*/
  public function wtb_preview()
  {
    wp_enqueue_script(
      'previews',
      get_theme_file_uri('options/js/previews.js'),
      ['previews', 'jquery'],
      filemtime(get_theme_file_path('options/js/previews.js')),
      true
    );
  }

  /*----------------------------------------
    面板
  ----------------------------------------*/
  public function wtb_panels($wp_customize)
  {
    /* $wp_customize->wtb_panels('panel', [
      'title'       => 'panel',
      'description' => 'description',
      'priority'    => 160,
    ]);
    $wp_customize->remove_panel(); */
  }

  /*----------------------------------------
    部分
  ----------------------------------------*/
  public function wtb_sections($wp_customize)
  {
    /* $wp_customize->add_section('section', [
      'title'          => 'section',
      'description'    => 'section',
      'panel'          => '',                      // Not typically needed.
      'priority'       => 160,
      'capability'     => 'edit_theme_options',
      'theme_supports' => '',                      // Rarely needed.
    ]); */

    $wp_customize->add_section('base_section', ['title' => '基本设置']);

    //$wp_customize->remove_section('title_tagline');
    $wp_customize->remove_section('colors');
    $wp_customize->remove_section('header_image');
    $wp_customize->remove_section('background_image');
    $wp_customize->remove_section('static_front_page');
    $wp_customize->remove_section('custom_css');
  }

  /*----------------------------------------
    设置、创建
  ----------------------------------------*/
  public function wtb_settings_controls($wp_customize)
  {
    /* $wp_customize->add_setting('setting_id', [
      'type'                 => 'theme_mod',            // or 'option'
      'capability'           => 'edit_theme_options',
      'theme_supports'       => '',                     // Rarely needed.
      'default'              => '',
      'transport'            => 'refresh',              // or postMessage
      'validate_callback'    => '',
      'sanitize_callback'    => '',
      'sanitize_js_callback' => '',                     // Basically to_json.
      'dirty'                => '',
    ]);
    $wp_customize->remove_setting();

    $wp_customize->add_control('setting_id', [
      'setting'         => '',
      'capability'      => '',
      'priority'        => 10,
      'section'         => '',
      'label'           => 'label'),
      'description'     => 'description',
      'choices'         => [],                          // 'radio' or 'select'
      'input_attrs'     => [],                          // no 'checkbox', 'radio', 'select', 'textarea', 'dropdown-pages'
      'allow_addition'  => false,
      'type'            => 'text',                      // 'text', 'email', 'url', 'number', 'date', 'hidden', 'textarea', 'checkbox', 'select', 'radio', 'dropdown-pages'
      'active_callback' => '',
    ]);

    $wp_customize->remove_control(); */

    /*---------------------------------------- 自定义控制器 ----------------------------------------*/
    require_once get_theme_file_path('options/customizer-control.php');

    /*---------------------------------------- 默认部分 ----------------------------------------*/
    $wp_customize->get_setting('blogname')->transport        = $this->postMessage;
    $wp_customize->get_setting('blogdescription')->transport = $this->postMessage;
    $wp_customize->selective_refresh->add_partial('id', [
      'selector'        => '.class',
      'render_callback' => function () {
        return get_settings('id');
      },
    ]);

    /*---------------------------------------- 自定义部分 ----------------------------------------*/

    // Text
    $wp_customize->add_setting('wtb_text', [
      'default'           => '',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_text', [
      'section'     => 'base_section',
      'label'       => '文本',
      'description' => '文本描述',
      'choices'     => [],
      'input_attrs' => [],
      'type'        => 'text',
    ]);
    // 'choices'       'select', 'radio'
    // 'input_attrs'   'text', 'email', 'url', 'number', 'date', 'textarea'
    // 'type'          'text', 'email', 'url', 'number', 'date', 'hidden', 'textarea', 'checkbox', 'select', 'radio', 'dropdown-pages'

    // Dropdown categories
    $wp_customize->add_setting('wtb_dropdown_categories', [
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control('wtb_dropdown_categories', [
      'section'     => 'base_section',
      'label'       => '下拉分类',
      'description' => '下拉分类描述',
      'type'        => 'select',
      'choices'     => $this->dropdown_categories(),
    ]);

    // Range
    $wp_customize->add_setting('wtb_range', [
      'default'           => 1,
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control(new WP_Customize_Range_Control($wp_customize, 'wtb_range', [
      'section'     => 'base_section',
      'label'       => '滑块',
      'description' => '滑块描述',
      'min'         => 1,
      'max'         => 10,
      'step'        => 1,
    ]));

    // Color
    $wp_customize->add_setting('wtb_color', [
      'default'           => '#000',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->hex_color,
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'wtb_color', [
      'section'     => 'base_section',
      'label'       => '颜色',
      'description' => '颜色描述',
    ]));

    // Media Video
    $wp_customize->add_setting('wtb_media_video', [
      'default'           => '',
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'wtb_media_video', [
      'section'     => 'base_section',
      'label'       => '视频媒体',
      'description' => '视频媒体描述',
      'mime_type'   => 'video',
    ]));

    // Media Audio
    $wp_customize->add_setting('wtb_media_audio', [
      'default'           => '',
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'wtb_media_audio', [
      'section'     => 'base_section',
      'label'       => '音频媒体',
      'description' => '音频媒体描述',
      'mime_type'   => 'audio',
    ]));

    // Media Image
    $wp_customize->add_setting('wtb_media_image', [
      'default'           => '',
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'wtb_media_image', [
      'section'     => 'base_section',
      'label'       => '图片媒体',
      'description' => '图片媒体描述',
      'mime_type'   => 'image',
    ]));

    // File
    $wp_customize->add_setting('wtb_file', [
      'default'           => '',
      'sanitize_callback' => $this->file,
    ]);
    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'wtb_file', [
      'section'     => 'base_section',
      'label'       => '文件',
      'description' => '文件描述',
    ]));

    // Image
    $wp_customize->add_setting('wtb_image', [
      'default'           => '',
      'sanitize_callback' => $this->url,
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'wtb_image', [
      'section'     => 'base_section',
      'label'       => '图片',
      'description' => '图片描述',
    ]));

    // Cropped Image
    $wp_customize->add_setting('wtb_cropped_image', [
      'default'           => '',
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'wtb_cropped_image', [
      'section'     => 'base_section',
      'label'       => '图片裁剪',
      'description' => '图片裁剪描述',
      'height'      => '',
      'width'       => '',
      'flex_height' => true,
      'flex_width'  => true,
    ]));

    // Javascript
    $wp_customize->add_setting('wtb_javascript', [
      'default'              => '',
      'sanitize_callback'    => $this->input_js_code,
      'sanitize_js_callback' => $this->output_js_code,
    ]);
    $wp_customize->add_control('wtb_javascript', [
      'section'     => 'base_section',
      'label'       => '脚本',
      'description' => '脚本描述',
      'type'        => 'textarea',
    ]);
  }

  public function is_checkbox($checked)
  {
    return ((isset($checked) && true == $checked) ? true : false);
  }

  public function is_file($image, $setting)
  {
    $mimes = [
      'jpg|jpeg|jpe' => 'image/jpeg',
      'gif'          => 'image/gif',
      'png'          => 'image/png',
      'bmp'          => 'image/bmp',
      'tif|tiff'     => 'image/tiff',
      'ico'          => 'image/x-icon'
    ];
    $file = wp_check_filetype($image, $mimes);
    return ($file['ext'] ? $image : $setting->default);
  }

  public function is_range($number, $setting)
  {
    $number = absint($number);
    $atts   = $setting->manager->get_control($setting->id)->input_attrs;
    $min    = (isset($atts['min']) ? $atts['min'] : $number);
    $max    = (isset($atts['max']) ? $atts['max'] : $number);
    $step   = (isset($atts['step']) ? $atts['step'] : 1);
    return ($min <= $number && $number <= $max && is_int($number / $step) ? $number : $setting->default);
  }

  public function is_select_radio($input, $setting)
  {
    $input   = sanitize_key($input);
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
  }

  public function is_input_js_code($input)
  {
    return base64_encode($input);
  }

  public function is_output_js_code($input)
  {
    return esc_textarea(base64_decode($input));
  }

  public function dropdown_categories($taxonomys = 'category')
  {
    $categories = get_categories([
      'orderby'    => 'name',
      'order'      => 'ASC',
      'hide_empty' => 0,
      'taxonomy'   => is_array($taxonomys) ? $taxonomys : explode(',', $taxonomys),
    ]);
    $cat_ids = array_map(function ($el) {
      return $el->cat_ID;
    }, $categories);
    $cat_names = array_map(function ($el) {
      return $el->cat_name;
    }, $categories);
    $results  = [] + array_combine($cat_ids, $cat_names);
    return $results;
  }
}
