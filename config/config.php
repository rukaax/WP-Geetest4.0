<?php
/**
 * GeeTest 4.0配置文件
 * 
 * 包含以下配置项:
 * 1. 验证码基础配置
 * 2. 验证码界面配置 
 * 3. UI自定义配置
 */

// ===== 验证码基础配置 =====
// 从极验后台获取 hhttps://console.geetest.com/sensbot/management
define("CAPTCHA_ID", "XXXXXX");    // 验证ID,必填
define("PRIVATE_KEY", "XXXXXX");   // 验证Key,必填

// ===== 验证码界面配置 =====
// 以下不填修改为默认
// 验证码展现形式 
// 参考https://docs.geetest.com/gt4/apirefer/api/web
define("GEETEST_PRODUCT", "popup");     // 可选值:
                                      // popup - 弹出式,点击验证按钮后弹出
                                      // float - 浮动式,直接嵌入表单 
                                      // bind - 隐藏式,点击登录按钮时显示

// 验证码语言 
define("GEETEST_LANG", "zho");        // 可选值:
                                      // zho - 简体中文
                                      // eng - 英文
                                      // zho-tw - 繁体中文
                                      // jpn - 日文
                                      // kor - 韩文等

// 超时配置                                    
define("GEETEST_TIMEOUT", 30000);     // 验证超时时间,单位毫秒
                                      // 超时后触发onError回调

// ===== UI自定义配置 =====
// 验证码遮罩层
define("GEETEST_MASK_COLOR", "");     // 弹出式遮罩颜色
                                      // 默认rgba(0,0,0,0.5)
                                      // 支持所有CSS颜色值

// 移动端适配                                    
define("GEETEST_REM_UNIT", 16);       // rem单位基准值
                                      // 用于移动端适配
                                      // 默认16px

// 按钮样式                                    
define("GEETEST_NATIVE_BUTTON_WIDTH", "100%"); // 验证按钮宽度
                                              // 支持px或百分比
                                              // 默认260px
?>