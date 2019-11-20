<?php
namespace app\api\model;

use think\Db;
use think\Model;
use think\Request;


class Member extends Model
{
  public function save($data = [], $where = [], $sequence = null)
  {
    return parent::save($data, $where, $sequence); // TODO: Change the autogenerated stub
  }

    /*通过token查询用户ID*/
    public static function GetUserId($token){
      if (!$token){return jsonResponse(-1,'','用户信息不正确，请重新登录');}
      $user_Id = self::where(['auth_key'=>$token])->value('id');
      if (!$user_Id){return jsonResponse(-1,'','暂无信息,请登录...');}
      return $user_Id;
    }

  /*用户登录*/
  public function LoginUser(){
    try{
      $post_data = input('post.');
      $validate = new \think\Validate;
      $validate->rule([
        'mobile|用户手机号' => 'require|/^[1]([3-9])[0-9]{9}$/',
        'smscode|手机验证码' => 'require|min:6',
      ]);
      if ($validate->check($post_data)) {
        $phonecode = db('common_sms_log')->where(['mobile'=>$post_data['mobile'],'used'=>0])->order('id desc')->value('code');
        if ($phonecode != $post_data['smscode']){return jsonResponse(-1,'','验证码不正确，请查看');}
        /* 查看是否有该信息*/ 
        $user_One = $this->where(['mobile'=>$post_data['mobile']])->value('id');
        if ($user_One){
          $token = curlRequest(config('ExtUrl').'sulf/get-pass',['type'=>1]);
          $resd  = json_decode($token,true);
          if ($resd['code'] == 200 && $resd['message'] == 'OK') {
            db('common_sms_log')->where(['mobile' => $post_data['mobile']])->update(['used' => 1, 'use_time' => time()]);
            $this->save(['auth_key'=>$resd['data']['token']],['mobile' => $post_data['mobile']]);
            return jsonResponse(1,  $this->where(['mobile'=>$post_data['mobile']])->value('auth_key'), '登录成功');
          }else{
            return jsonResponse(-1, '', '登录_失败');
          }
        }else{
          $token = curlRequest(config('ExtUrl').'sulf/get-pass',['pass'=>123456]);
          $resd  = json_decode($token,true);
          if ($resd['code'] == 200 && $resd['message'] == 'OK') {
            $post_data['username'] = 'CheJS' . rand(1000, 9999);
            $post_data['merchant_id'] = 1;
            $post_data['password_hash'] = $resd['data']['pass'];
            $post_data['auth_key'] = $resd['data']['token'];
            $post_data['created_at'] = time();
            $post_data['updated_at'] = time();
            $bool = $this->allowField(true)->save($post_data);
            if ($bool) {
              db('common_sms_log')->where(['mobile' => $post_data['mobile']])->update(['used' => 1, 'use_time' => time()]);
              return jsonResponse(1, $this->where(['id' => $this->id])->value('auth_key'), '登录成功');
            } else {
              return jsonResponse(-1, '', '登录失败');
            }
          }else{
            return jsonResponse(-1, '', '登录_失败');
          }
        }
      }else{
        return jsonResponse(-1,'',$validate->getError());
      }
    }catch (\Exception $e){
      return jsonResponse(-1,'',$e->getMessage());
    }
  }

  //用户读取验证码
  public function send(){
    try{
      $post_data = input('post.');
      $validate = new \think\Validate;
      $validate->rule([
        'tel|用户手机号' => 'require|/^[1]([3-9])[0-9]{9}$/',
      ]);
      if ($validate->check($post_data)) {
        $phonecount = db('common_sms_log')->where(['mobile'=>$post_data['tel']])->whereTime('created_at', 'today')->count();
        if ($phonecount == 5) {return jsonResponse(-1,'','今日发送次数过多..');}
        $code = rand(100000,999999);
        $smsend = new \base\Sms\SmsSend;
        $content = $smsend->send($post_data['tel'],'车江山','SMS_176536326',['code'=>$code]);
        db('common_sms_log')->where(['mobile' => $post_data['tel'],'usage'=>'Login'])->update(['used' => 1, 'use_time' => time()]);
        $data = [$post_data['tel'],$code,'发送验证码','Login',time()+300,'',$content->Message,json_encode($content),0];
        $this->SmsLog($data);
        if ($content->Message == 'OK' && $content->Code == 'OK'){
          return jsonResponse(1,'','发送成功');
        }else{
          return jsonResponse(-1,$content,$content->Message);
//          return jsonResponse(-1,$content,$content->Message);
        }
      }else{
        return jsonResponse(-1,'',$validate->getError());
      }
    }catch (\Exception $e){
      return jsonResponse(-1,'',$e->getMessage());
    }
  }




  /* 短信日志*/
  public function SmsLog($d){
      $dat = [
        'merchant_id' => 1,
        'mobile' => $d['0'],
        'code' => $d['1'],
        'content' => $d['2'],
        'usage' => $d['3'],
        'used' => $d[8],
        'use_time' => $d['4'],
        'error_code' => $d['5'],
        'error_msg' => $d['6'],
        'error_data' => $d['7'],
        'ip' => \request()->ip(),
        'status' => 1,
        'created_at' => time(),
        'updated_at' => time(),
      ];
      db('common_sms_log')->insert($dat);
  }



}