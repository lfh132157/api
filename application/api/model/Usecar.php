<?php
namespace app\api\model;

use think\Db;
use think\model;
use think\Request;

Class Usecar extends model{
  public function save($data = [], $where = [], $sequence = null)
  {
    return parent::save($data, $where, $sequence); // TODO: Change the autogenerated stub
  }

  public function orderadd() {
    $user_id = Member::GetUserId($post_data['user_id']);
    $a = db('order')->order('DESC')->find();
    if($a['id']<)
    $order_num = date('Y/m/d').
  }

}