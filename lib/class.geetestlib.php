<?php
/**
 * GeeTest 4.0 验证码PHP SDK
 * 
 * 提供以下功能:
 * 1. 服务端二次验证
 */

class GeetestLib {
    const GT_SDK_VERSION = 'php_4.0.0'; // SDK版本号
    
    private $captcha_id;  // 验证码ID
    private $private_key; // 私钥
    private $timeout = 30;// 请求超时时间(秒)

    /**
     * 构造函数
     * @param string $captcha_id  验证码ID
     * @param string $private_key 私钥 
     */
    public function __construct($captcha_id, $private_key) {
        $this->captcha_id = $captcha_id;
        $this->private_key = $private_key;
    }

    /**
     * 服务端验证接口
     * 对前端验证结果进行二次验证
     * 
     * @param string $lot_number     验证流水号
     * @param string $captcha_output 验证输出信息 
     * @param string $pass_token     验证通过标识
     * @param string $gen_time      验证时间戳
     * @return array 验证结果
     *         ['result'=>'success'] 验证成功
     *         ['result'=>'fail'] 验证失败
     */
    public function validate($lot_number, $captcha_output, $pass_token, $gen_time) {
        // 组装验证参数
        $params = [
            'lot_number' => $lot_number,
            'captcha_output' => $captcha_output,
            'pass_token' => $pass_token,
            'gen_time' => $gen_time,
            'captcha_id' => $this->captcha_id
        ];
        
        // sign_token的计算方式：使用HMAC算法，使用lot_number作为消息，private_key作为密钥
        $sign_token = hash_hmac('sha256', $lot_number, $this->private_key);
        $params['sign_token'] = $sign_token;

        // 发送验证请求
        $response = $this->send_request("https://gcaptcha4.geetest.com/validate", $params);
        
        // 记录调试信息
        error_log("Geetest validate request params: " . print_r($params, true));
        error_log("Geetest validate response: " . print_r($response, true));
        
        // 处理响应结果
        if(!$response) {
            // 请求失败，验证失败
            error_log("Geetest validate failed: request failed");
            return ['result' => 'fail', 'reason' => 'request_failed'];
        }

        $result = json_decode($response, true);
        // 解析失败则验证失败
        if (!$result) {
            error_log("Geetest validate failed: json parse error");
            return ['result' => 'fail', 'reason' => 'parse_error'];
        }
        
        error_log("Geetest validate result: " . print_r($result, true));
        return $result;
    }

    /**
     * 发送HTTP请求
     * @param string $url    请求地址
     * @param array $params  请求参数
     * @return string|false  成功返回响应内容,失败返回false
     */
    private function send_request($url, $params = null) {
        $opts = [
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Geetest4.0 WP Plugin',
        ];

        if ($params) {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        
        // 检查是否有curl错误
        if (curl_errno($ch)) {
            error_log("Geetest curl error: " . curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        error_log("Geetest HTTP response code: " . $httpCode);
        
        curl_close($ch);

        return $response;
    }
    
}