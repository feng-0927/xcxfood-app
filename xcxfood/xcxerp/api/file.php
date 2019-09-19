<?php
include_once("../includes/init.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";

$json = array("msg"=>null,"result"=>false,"data"=>null);

if($action == "updateAvatar")
{
  //更新头像
  if($_POST)
  {
    $userid = isset($_POST['userid']) ? trim($_POST['userid']) : 0;

    //判断用户是否存在
    $user = $db->select()->from("user")->where(["id"=>$userid])->find();

    if(!$user)
    {
      $json['msg'] = '用户不存在';
      $json['result'] = false;
      return json($json);
    }

    $data = [];

    //判断是否有文件上传
    if($uploads->isFile())
    {
      //判断文件是否上传成功
      if($uploads->upload())
      {
        //获取上传的文件名
        $data['avatar'] = $uploads->savefile();
        $affect = $db->update("user",$data,"id = $userid");
        if($affect)
        {
          $user['avatar'] = $data['avatar'];
          $json['result'] = true;
          $json['msg'] = '更新头像成功';
          $json['data'] = $user;
          return json($json);
        }
      }
    }
    
    return json($json);
  }
}

?>