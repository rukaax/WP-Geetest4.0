<?php
/**
 * Plugin Name: Geetest4.0-WP
 * Plugin URI: https://blogs.rukaax.top/geetest4-wp
 * Description: 基于极验4.0的WordPress验证码插件
 * Version: 1.3
 * Author: RukaaX
 * Author URI: https://blogs.rukaax.top
 * License: CC-BY-NC-SA 4.0
 * 
 * 主要功能:
 * 1. 登录表单验证
 * 2. 评论表单验证  
 * 3. 三种验证形式:弹出、浮动、隐藏
 * 4. 支持界面自定义
 */

function add_captcha_env() {
    wp_enqueue_script('jquery.js', "//cdn.staticfile.org/jquery/3.3.1/jquery.min.js", array(), '3.3.1', false);
    wp_enqueue_script('gt4.js', "//static.geetest.com/v4/gt4.js", array(), '4.0.0', false);
    wp_enqueue_script('layer.js', "//cdn.staticfile.org/layer/2.3/layer.js", array(), '2.3', false);
}
add_action('login_enqueue_scripts','add_captcha_env');
add_action('register_enqueue_scripts', 'add_captcha_env');

function add_captcha_style() {
    echo '<div id="captcha"></div>';
}
add_action('login_form','add_captcha_style');
add_action('lostpassword_form','add_captcha_style');
add_action('resetpass_form','add_captcha_style');
add_action('register_form','add_captcha_style');

function add_login_captcha_API() {
    // 确保常量已定义
    if (!defined('CAPTCHA_ID') || !defined('PRIVATE_KEY') || !defined('GEETEST_PRODUCT') || !defined('GEETEST_LANG') || 
        !defined('GEETEST_TIMEOUT') || !defined('GEETEST_MASK_COLOR') || 
        !defined('GEETEST_REM_UNIT') || !defined('GEETEST_NATIVE_BUTTON_WIDTH')) {
        require_once dirname(__FILE__). '/config/config.php';
    }
    
    // 检查当前是否为注册页面，如果是则不执行登录验证码
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-login.php?action=register') !== false) {
        return;
    }
    
    echo '<script>
    var geetestObj = null;
    
    // 初始化GeeTest验证码
    initGeetest4({
        captchaId: "'. CAPTCHA_ID .'",
        product: "'. GEETEST_PRODUCT .'",
        language: "'. GEETEST_LANG .'",
        timeout: '. GEETEST_TIMEOUT .',
        mask: {
            bgColor: "'. GEETEST_MASK_COLOR .'"
        },
        remUnit: '. GEETEST_REM_UNIT .',
        nativeButton: {
            width: "'. GEETEST_NATIVE_BUTTON_WIDTH .'"
        }
    }, function (captcha) {
        geetestObj = captcha;
        captcha.appendTo("#captcha");
        
        captcha.onReady(function(){
            // 验证码准备就绪
        });

        captcha.onSuccess(function () {
            var result = captcha.getValidate();
            if(result) {
                var form = document.getElementById("loginform") || document.getElementById("registerform");
                if (!form) return;
                
                for(var key in result) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = key;
                    input.value = result[key];
                    form.appendChild(input);
                }
            }
        });
        
        captcha.onFail(function (failObj) {
            layer.msg("验证失败，请重试", {icon: 2});
        });

        captcha.onError(function(error) {
            layer.msg("验证出现错误,请刷新页面重试: " + error.msg, {icon: 2});
        });

        captcha.onClose(function(){
            if("'. GEETEST_PRODUCT .'" === "bind"){
                layer.msg("请完成验证后继续");
            }
        });
    });

    // 处理表单提交
    var submitButton = document.getElementById("wp-submit");
    if (submitButton) {
        submitButton.addEventListener("click", function(e) {
            if (!geetestObj) {
                e.preventDefault();
                layer.msg("验证码加载中，请稍后...");
                return;
            }
            
            var result = geetestObj.getValidate();
            if (!result) {
                e.preventDefault();
                layer.msg("请先完成验证");
                return;
            }
        });
    }
    </script>';
}
add_action('login_footer','add_login_captcha_API');

function add_register_captcha_API() {
    // 确保常量已定义
    if (!defined('CAPTCHA_ID') || !defined('PRIVATE_KEY') || !defined('GEETEST_PRODUCT') || !defined('GEETEST_LANG') || 
        !defined('GEETEST_TIMEOUT') || !defined('GEETEST_MASK_COLOR') || 
        !defined('GEETEST_REM_UNIT') || !defined('GEETEST_NATIVE_BUTTON_WIDTH')) {
        require_once dirname(__FILE__). '/config/config.php';
    }
    
    echo '<script>
    var geetestObj = null;
    
    // 初始化GeeTest验证码
    initGeetest4({
        captchaId: "'. CAPTCHA_ID .'",
        product: "'. GEETEST_PRODUCT .'",
        language: "'. GEETEST_LANG .'",
        timeout: '. GEETEST_TIMEOUT .',
        mask: {
            bgColor: "'. GEETEST_MASK_COLOR .'"
        },
        remUnit: '. GEETEST_REM_UNIT .',
        nativeButton: {
            width: "'. GEETEST_NATIVE_BUTTON_WIDTH .'"
        }
    }, function (captcha) {
        geetestObj = captcha;
        captcha.appendTo("#captcha");
        
        captcha.onReady(function(){
            // 验证码准备就绪
        });

        captcha.onSuccess(function () {
            var result = captcha.getValidate();
            if(result) {
                var form = document.getElementById("registerform");
                if (!form) return;
                
                for(var key in result) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = key;
                    input.value = result[key];
                    form.appendChild(input);
                }
            }
        });
        
        captcha.onFail(function (failObj) {
            layer.msg("验证失败，请重试", {icon: 2});
        });

        captcha.onError(function(error) {
            layer.msg("验证出现错误,请刷新页面重试: " + error.msg, {icon: 2});
        });

        captcha.onClose(function(){
            if("'. GEETEST_PRODUCT .'" === "bind"){
                layer.msg("请完成验证后继续");
            }
        });
    });

    // 处理表单提交
    var submitButton = document.getElementById("wp-submit");
    if (submitButton) {
        submitButton.addEventListener("click", function(e) {
            if (!geetestObj) {
                e.preventDefault();
                layer.msg("验证码加载中，请稍后...");
                return;
            }
            
            var result = geetestObj.getValidate();
            if (!result) {
                e.preventDefault();
                layer.msg("请先完成验证");
                return;
            }
        });
    }
    </script>';
}
add_action('register_form', 'add_register_captcha_API');

function add_comment_captcha_style() {
    echo '<p id="captcha"></p>';
}

function add_comment_captcha_API() {
    // 确保常量已定义
    if (!defined('CAPTCHA_ID') || !defined('GEETEST_PRODUCT') || !defined('GEETEST_LANG') || 
        !defined('GEETEST_TIMEOUT') || !defined('GEETEST_MASK_COLOR')) {
        require_once dirname(__FILE__). '/config/config.php';
    }
    
    echo '<script>
    var geetestObj = null;
    
    initGeetest4({
        captchaId: "'. CAPTCHA_ID .'",
        product: "'. GEETEST_PRODUCT .'",
        language: "'. GEETEST_LANG .'",
        timeout: '. GEETEST_TIMEOUT .',
        mask: {
            bgColor: "'. GEETEST_MASK_COLOR .'"
        }
    }, function (captcha) {
        geetestObj = captcha;
        captcha.appendTo("#captcha");

        captcha.onSuccess(function () {
            var result = captcha.getValidate();
            if(result) {
                var form = document.getElementById("commentform");
                if (!form) return;
                
                for(var key in result) {
                    var input = document.createElement("input");
                    input.type = "hidden"; 
                    input.name = key;
                    input.value = result[key];
                    form.appendChild(input);
                }
            }
        });
        
        captcha.onFail(function (failObj) {
            layer.msg("验证失败，请重试", {icon: 2});
        });
        
        captcha.onError(function(error) {
            layer.msg("验证出现错误,请刷新页面重试", {icon: 2});
        });
    });

    // 处理评论表单提交
    var commentSubmit = document.getElementById("submit");
    if (commentSubmit) {
        commentSubmit.addEventListener("click", function(e) {
            if (!geetestObj) {
                e.preventDefault();
                layer.msg("验证码加载中，请稍后...");
                return;
            }
            
            var result = geetestObj.getValidate();
            if (!result) {
                e.preventDefault();
                layer.msg("请先完成验证");
                return;
            }
        });
    }
    </script>';
}

if(!function_exists('is_user_logged_in')) {
    require (ABSPATH . WPINC . '/pluggable.php');
}
if (!is_user_logged_in()) {
    add_action('wp_enqueue_scripts', 'add_captcha_env');
    add_action('comment_form_after_fields', 'add_comment_captcha_style');
    add_action('comment_form_after', 'add_comment_captcha_API');
}

function add_captcha_validate($user) {
    require_once dirname(__FILE__). '/lib/class.geetestlib.php';
    require_once dirname(__FILE__). '/config/config.php';

    // 常规验证模式
    if(empty($_POST['lot_number']) || empty($_POST['captcha_output']) || 
       empty($_POST['pass_token']) || empty($_POST['gen_time'])) {
        return new WP_Error('broke', __("请完成验证"));
    }

    $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
    
    $result = $GtSdk->validate(
        $_POST['lot_number'],
        $_POST['captcha_output'], 
        $_POST['pass_token'],
        $_POST['gen_time']
    );

    if ($result['result'] === 'success') {
        return $user;
    }

    return new WP_Error('broke', __("验证未通过"));
}
add_filter('wp_authenticate_user', 'add_captcha_validate', 100, 1);

// 注册表单验证
function add_register_captcha_validate($errors, $sanitized_user_login, $user_email) {
    require_once dirname(__FILE__). '/lib/class.geetestlib.php';
    require_once dirname(__FILE__). '/config/config.php';

    // 常规验证模式
    if(empty($_POST['lot_number']) || empty($_POST['captcha_output']) || 
       empty($_POST['pass_token']) || empty($_POST['gen_time'])) {
        $errors->add('captcha_error', __("请完成验证"));
        return $errors;
    }

    $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
    
    $result = $GtSdk->validate(
        $_POST['lot_number'],
        $_POST['captcha_output'], 
        $_POST['pass_token'],
        $_POST['gen_time']
    );

    if ($result['result'] !== 'success') {
        $errors->add('captcha_error', __("验证未通过"));
    }
    
    return $errors;
}
add_filter('registration_errors', 'add_register_captcha_validate', 10, 3);
?>
