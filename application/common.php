<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function jsonResponse($code = 0, $data = [], $msg = '')
{
    $return = jsonResponseReturn($code, $data, $msg);
    if (empty($return['data'])) {
        $json = json_encode($return, JSON_FORCE_OBJECT);
    } else {
        $json = json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    header('Content-type:application/json;charset=utf-8');
    echo $json;
    exit ();
}

function jsonResponseReturn($code = 0, $data = [], $msg = '') {
    $msg = empty ( $msg ) ? "UnknowError" : $msg;
    $return = [
        'code' => $code,
        'data' => $data,
        'msg' => $msg
    ];
    return $return;
}

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
if (!function_exists('curlRequest')) {
  function curlRequest($url, $post = '', $cookie = '', $returnCookie = 0)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
//    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if ($post) {
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookie) {
      curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
      return curl_error($curl);
    }
    curl_close($curl);
    if ($returnCookie) {
      list($header, $body) = explode("\r\n\r\n", $data, 2);
      preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
      $info['cookie'] = substr($matches[1][0], 1);
      $info['content'] = $body;
      return $info;
    } else {
      return $data;
    }
  }
}


