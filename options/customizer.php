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
    add_action('customize_register', [$this, 'wtb_sections']);
    add_action('customize_register', [$this, 'wtb_settings_controls']);
    add_action('customize_preview_init', [$this, 'wtb_customize_preview']);
  }

  /*----------------------------------------
    面板
  ----------------------------------------*/
  public function wtb_customize_preview()
  {
    wp_enqueue_script(
      'customizer-preview',
      get_theme_file_uri('options/js/customizer-preview.js'),
      ['jquery', 'customize-preview'],
      filemtime(get_theme_file_path('options/js/customizer-preview.js')),
      true
    );
  }

  /*----------------------------------------
    部分
  ----------------------------------------*/
  public function wtb_sections($wp_customize)
  {
    $wp_customize->add_section('base_section', ['title' => '基本设置']);
    $wp_customize->add_section('home_section', ['title' => '首页相关']);
    $wp_customize->add_section('post_section', ['title' => '文章相关']);
    $wp_customize->add_section('submit_section', ['title' => '认证/收录推送']);
    $wp_customize->add_section('optimize_section', ['title' => '简单优化']);
    $wp_customize->add_section('smtp_section', ['title' => 'SMTP']);

    $wp_customize->remove_section('custom_css');
  }

  /*----------------------------------------
    设置、创建
  ----------------------------------------*/
  public function wtb_settings_controls($wp_customize)
  {
    /*---------------------------------------- 默认部分 ----------------------------------------*/
    $wp_customize->get_setting('blogname')->transport        = $this->postMessage;
    $wp_customize->get_setting('blogdescription')->transport = $this->postMessage;

    $wp_customize->selective_refresh->add_partial('blogname', [
      'selector'        => '.cwhead-logo a span',
      'render_callback' => function () {
        return get_settings('blogname');
      },
    ]);

    // 显示标题
    $wp_customize->add_setting('wtb_sitename_switch', [
      'default'           => true,
      'sanitize_callback' => $this->checkbox,
    ]);
    $wp_customize->add_control('wtb_sitename_switch', [
      'priority' => 8,
      'section'  => 'title_tagline',
      'label'    => '显示标题',
      'type'     => 'checkbox',
    ]);

    // Keywords（关键词）
    $wp_customize->add_setting('wtb_keywords', [
      'default'           => get_bloginfo('name') . ',',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->nohtml,
    ]);
    $wp_customize->add_control('wtb_keywords', [
      'section'     => 'title_tagline',
      'label'       => 'Keywords（关键词）',
      'description' => '<p>分类：可独立设置文章：自动抓取（分类名+文章标签）</p><p>建议3-5个重要关键词，英文逗号“ , ”隔开与出现在网页文本中的核心内容相关</p>',
      'type'        => 'textarea',
    ]);

    // Description（网页简述）
    $wp_customize->add_setting('wtb_description', [
      'default'           => get_bloginfo('name') . '是一个简单友好的博客！',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->nohtml,
    ]);
    $wp_customize->add_control('wtb_description', [
      'section'     => 'title_tagline',
      'label'       => 'Description（网页简述）',
      'description' => '<p>分类：可独立设置文章：自动抓取（内容头部一段 或 文章摘要）</p><p>完整的一句话，一般160-320个字符，描述内容要和页面内容相关</p>',
      'type'        => 'textarea',
    ]);

    /*---------------------------------------- 基本设置 ----------------------------------------*/
    // 搜索占位符文字
    $wp_customize->add_setting('wtb_search_placeholder', [
      'default'           => '搜索词',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_search_placeholder', [
      'section' => 'base_section',
      'label'   => '搜索占位符文字',
    ]);

    // 建站日期
    $wp_customize->add_setting('wtb_sitebuild', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control('wtb_sitebuild', [
      'section'     => 'base_section',
      'label'       => '建站日期',
      'input_attrs' => [
        'placeholder' => date('Y'),
      ],
    ]);

    // 版权
    $wp_customize->add_setting('wtb_copyright', [
      'default'           => '版权所有.',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->nohtml,
    ]);
    $wp_customize->add_control('wtb_copyright', [
      'section' => 'base_section',
      'label'   => '版权',
      'type'    => 'textarea',
    ]);

    // 备案号
    $wp_customize->add_setting('wtb_record_number', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_record_number', [
      'section'     => 'base_section',
      'label'       => '备案号',
      'input_attrs' => [
        'placeholder' => '京ICP备' . date('Y') . 'XXXXXX号',
      ],
    ]);

    // JS脚本
    $wp_customize->add_setting('wtb_statistics', [
      'transport'            => $this->postMessage,
      'sanitize_callback'    => $this->input_js_code,
      'sanitize_js_callback' => $this->output_js_code,
    ]);
    $wp_customize->add_control('wtb_statistics', [
      'section'     => 'base_section',
      'label'       => 'JS脚本',
      'description' => '可以添加一些自定义脚本，如网站统计等',
      'input_attrs' => [
        'placeholder' => '<script>var _hmt = _hmt || [];(function() {var hm = document.createElement("script");hm.src = "https://hm.baidu.com/hm.........
        ',
      ],
      'type' => 'textarea',
    ]);

    /*---------------------------------------- 首页相关 ----------------------------------------*/

    /*---------------------------------------- 文章相关 ----------------------------------------*/
    $post_section = [
      ['cookie', false, '开启Cookie限制', '开启后每个IP在24小时内仅增加1个'],
      ['rand', false, '开启随机数'],
    ];

    foreach ($post_section as $value) {
      $wp_customize->add_setting("wtb_{$value[0]}_switch", [
        'default'           => $value[1],
        'transport'         => $this->postMessage,
        'sanitize_callback' => $this->checkbox,
      ]);
      $wp_customize->add_control("wtb_{$value[0]}_switch", [
        'section'     => 'post_section',
        'label'       => $value[2],
        'description' => isset($value[3]) ? $value[3] : '',
        'type'        => 'checkbox',
      ]);
    }

    // 随机数范围
    $wp_customize->add_setting('wtb_randnum', [
      'default'           => '64,128',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_randnum', [
      'section'     => 'post_section',
      'label'       => '随机数范围',
      'description' => '保存文章会自动添加一个范围之间的数字，英文逗号“ , ”隔开',
    ]);

    // 浏览量键值
    $wp_customize->add_setting('wtb_key', [
      'default'           => 'views',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_key', [
      'section'     => 'post_section',
      'label'       => '浏览量键值',
      'description' => '为了接替原先数据，一般主题均为 views',
    ]);

    /*---------------------------------------- 认证/收录推送 ----------------------------------------*/
    // 平台认证
    $wp_customize->add_setting('wtb_html_verify', [
      'transport'            => $this->postMessage,
      'sanitize_callback'    => $this->input_js_code,
      'sanitize_js_callback' => $this->output_js_code,
    ]);
    $wp_customize->add_control('wtb_html_verify', [
      'section'     => 'submit_section',
      'label'       => '平台认证',
      'description' => '填写完整 Meta 标签（建议使用最稳的（域名 或 文件）方式进行认证）',
      'input_attrs' => [
        'placeholder' => '<meta name="name" ...',
      ],
      'type' => 'textarea',
    ]);

    // 头条自动推送
    $wp_customize->add_setting('wtb_ttautosubmit_switch', [
      'default'           => false,
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_ttautosubmit_switch', [
      'section' => 'submit_section',
      'label'   => '头条自动推送',
      'type'    => 'checkbox',
    ]);

    // 百度文章提交
    $wp_customize->add_setting('wtb_baidu_submit', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->url,
    ]);
    $wp_customize->add_control('wtb_baidu_submit', [
      'section'     => 'submit_section',
      'label'       => '百度文章提交',
      'description' => '到平台获取完整的<a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank">接口调用地址</a>',
      'input_attrs' => ['placeholder' => 'http://data.zz.baidu.com/urls?site=https://example.com&token=KCpPorfs1HkCERyaLH3',],
      'type'        => 'textarea',
    ]);

    // 神马文章提交
    $wp_customize->add_setting('wtb_shenma_submit', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->url,
    ]);
    $wp_customize->add_control('wtb_shenma_submit', [
      'section'     => 'submit_section',
      'label'       => '神马文章提交',
      'description' => '到平台获取完整的<a href="https://zhanzhang.sm.cn/open/mip" target="_blank">接口调用地址</a>',
      'input_attrs' => ['placeholder' => 'https://data.zhanzhang.sm.cn/push?site=example.com&user_name=example@qq.com&resource_name=mip_add&token=TI_9b074f04562849kgd2518994a27846c',],
      'type'        => 'textarea',
    ]);

    // Bing/Yandex
    $wp_customize->add_setting('wtb_bingyandex_submit', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->nohtml,
    ]);
    $wp_customize->add_control('wtb_bingyandex_submit', [
      'label'       => 'Bing/Yandex',
      'description' => 'Bing官方工具<a href="https://www.bing.com/indexnow#implementation" target="_blank">生成KEY文件</a>放在网站根目录，复制文件名字填写至此处',
      'section'     => 'submit_section',
      'input_attrs' => [
        'placeholder' => '192dce42fb9c473499154c2b4e5827a0',
      ],
    ]);

    /*---------------------------------------- 简单优化 ----------------------------------------*/
    $optimize_section = [
      ['sab', true, '非管理员禁用工具栏'],
      ['aud', true, '完全禁用后台更新'],
      ['aup', true, '禁止插件自动更新'],
      ['aut', true, '禁止主题自动更新'],
      ['uwbe', true, '使用旧版小工具设置'],
      ['guwbe', true, '古腾堡移除旧版小部件'],
      ['ubefp', false, '使用旧版文章编辑器'],
      ['restapi', true, '完全关闭 REST API'],
    ];

    foreach ($optimize_section as $value) {
      $wp_customize->add_setting("wtb_{$value[0]}_switch", [
        'default'           => $value[1],
        'transport'         => $this->postMessage,
        'sanitize_callback' => $this->checkbox,
      ]);
      $wp_customize->add_control("wtb_{$value[0]}_switch", [
        'section'     => 'optimize_section',
        'label'       => $value[2],
        'description' => isset($value[3]) ? $value[3] : '',
        'type'        => 'checkbox',
      ]);
    }

    /*---------------------------------------- SMTP ----------------------------------------*/
    //启用SMTP
    $wp_customize->add_setting('wtb_smtp_switch', [
      'default'           => false,
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->checkbox,
    ]);
    $wp_customize->add_control('wtb_smtp_switch', [
      'section' => 'smtp_section',
      'label'   => '启用SMTP',
      'type'    => 'checkbox',
    ]);

    //发件人名称
    $wp_customize->add_setting('wtb_smtp_name', [
      'default'           => get_bloginfo('name'),
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_smtp_name', [
      'section' => 'smtp_section',
      'label'   => '发件人名称（默认站点标题）',
    ]);

    //邮箱
    $wp_customize->add_setting('wtb_smtp_email', [
      'default'           => get_bloginfo('admin_email'),
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->email,
    ]);
    $wp_customize->add_control('wtb_smtp_email', [
      'section' => 'smtp_section',
      'label'   => '发件人邮箱（默认管理员邮箱）',
      'type'    => 'email',
    ]);

    //密码
    $wp_customize->add_setting('wtb_smtp_pass', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_smtp_pass', [
      'section'     => 'smtp_section',
      'label'       => '密码',
      'description' => '腾讯为授权码',
    ]);

    //服务器
    $wp_customize->add_setting('wtb_smtp_host', [
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->text,
    ]);
    $wp_customize->add_control('wtb_smtp_host', [
      'section'     => 'smtp_section',
      'label'       => '服务器',
      'description' => '腾讯个人：smtp.qq.com <br> 腾讯企业：smtp.exmail.qq.com <br> 阿里云企业：smtp.mxhichina.com <br> 其他服务商自行查询',
    ]);

    //端口
    $wp_customize->add_setting('wtb_smtp_port', [
      'default'           => '25',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->absint,
    ]);
    $wp_customize->add_control('wtb_smtp_port', [
      'section' => 'smtp_section',
      'label'   => '端口',
      'choices' => [
        '25'  => '非加密 -> 25',
        '465' => '加密 -> 465',
      ],
      'type' => 'radio',
    ]);

    //加密
    $wp_customize->add_setting('wtb_smtp_pact', [
      'default'           => 'tls',
      'transport'         => $this->postMessage,
      'sanitize_callback' => $this->select_radio,
    ]);
    $wp_customize->add_control('wtb_smtp_pact', [
      'section' => 'smtp_section',
      'label'   => '协议',
      'choices' => [
        'tls' => '端口25 -> tls',
        'ssl' => '端口465 -> ssl',
      ],
      'type' => 'radio',
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
}
