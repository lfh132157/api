<?php
namespace app\api\controller;

use base\Grap\Grapimg;
use base\Sms\SmsSend;
use think\Controller;
use app\api\model\Report as R;
use app\api\model\Member as M;

class Index extends Controller
{
    /*用户登录*/
   public function login(){
      $m = new M();
      return $m->LoginUser();
   }

    /*添加上报信息*/
    public function index()
    {
       $r = new R();
       return $r->Repadd();
    }

    /*展示个人上报的信息*/
    public function AllReporting(){
        $r = new R();
        return $r->GetAllReport();
    }

    /*展示个人上报的单个信息详情*/
    public function OneReporting(){
        $r = new R();
        return $r->GetOneReport();
    }

    /*短信*/
  public function Smssend(){
    $m = new M();
    return $m->send();
  }
  /*发送通知短信*/
  public function NotSend(){
    $SmsSend = new SmsSend();
    $com = $SmsSend->send(18739175601,'车江山CJS','SMS_176532099',['address'=>'郑州市陇海路与城东路交叉口南500米路东','details'=>'五菱，宝马-严重损伤','phone'=>18039334627,'userphone'=>13721472821]);
    return jsonResponse(1,'',$com);
  }

  /* 外拓人员准备接单*/
  public function Receipt(){

  }


  /*图片上传*/
  public function GrapImg(){
    /*通过file上传图片*/
//    $post_data = $this->request->file('img');
//    $grapimg = new Grapimg();
//    var_dump($grapimg->uploads($post_data));
    /*通过base64上传图片*/
    $base64_img = input('post.base64Img');
    if (!$base64_img){return jsonResponse(-1,'','图片不能为空');}
    $grapimg = new Grapimg();
    return $grapimg->base64_image_content($base64_img,'./public/uploads/');
//    return $grapimg->DelOssImg('4bd3c71d9f2490152c4678aa6715d588.jpeg');//删除oss的图片

  }

    /*测试*/
  public function ceshi(){

      $num =date('Ymd');
			$b = db('order')->where('order_num','like',$num.'%')->select();
			$count = count($b);
			if (strlen($count)<2){
			  $count = '00000'.$count;
      }elseif (strlen($count)<3){
        $count = '0000'.$count;
      }elseif (strlen($count)<4){
        $count = '000'.$count;
      }elseif (strlen($count)<5){
        $count = '00'.$count;
      }elseif (strlen($count)<6){
        $count = '0'.$count;
      }
      $order_num = date('Ymd').rand(00000,99999).$count;
			$user_id = 1;
			$service_items = '小养护';
			$item = 210.00;
			$order_pay = $item;
			$data = array(
			  'user_id' => $user_id,
        'order_num' => $order_num,
        'order_pay' => $order_pay,
        'first_time' => date('Y-m-d h:i:s')
      );
			$data2 = array(
			  'order_num' => $order_num,
        'service_items' => $service_items,
        'item_monye' => $item
      );

			//$c = db('order')->where('user_id','1')->select();

		//return jsonResponse(1,$b,'查询成功');
  }
}
