<?php
include_once("../includes/init.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";

$json = array("msg"=>null,"result"=>false,"data"=>null);

if($action == "login")  //注册
{
  if($_POST)
  {
    $code = isset($_POST['code']) ? $_POST['code'] : false;

    if(!$code)
    {
      $json['msg'] = "授权失败";
      return json($json);
    }

    //获取openid
    $openid = code2Session($code);

    if(!$openid)
    {
      $json['msg'] = '授权失败';
      return json($json);
    }

    $openid = $openid['openid'];

    $user = $db->select()->from("user")->where("openid = '$openid'")->find();

    if($user)
    {
      $json['result'] = true;
      $json['data'] = $user;
      return json($json);
    }else{
      //注册
      $data = [
        "nickname"=>$_POST['nickname'],
        "gender"=>$_POST['gender'],
        "createtime"=>time(),
        "openid"=>$openid
      ];

      $userid = $db->add("user",$data);

      if($userid)
      {
        $json['data'] = $data;
        $json['result'] = true;
      }else{
        $json['msg'] = '注册失败';
        $json['result'] = false;
      }

      return json($json);
    }

  }
}else if($action == "updateUser")
{
  if($_POST)
  {
    $userid = isset($_POST['userid']) ? $_POST['userid'] : 0;

    $user = $db->select()->from("user")->where("id = '$userid'")->find();

    if(!$user)
    {
      $json['msg'] = '用户不存在';
      return json($json);
    }

    $data = [
      "nickname"=>$_POST['nickname'],
      "gender"=>$_POST['gender'],
      "mobile"=>$_POST['mobile'],
    ];
    
    $affect = $db->update("user",$data,"id = $userid");

    if($affect)
    {
      $user = $db->select()->from("user")->where("id = '$userid'")->find();
      $json['data'] = $user;
      $json['msg'] = '更新成功';
      $json['result'] = true;
    }else{
      $json['msg'] = '更新失败';
    }

    return json($json);
  }
}




?>