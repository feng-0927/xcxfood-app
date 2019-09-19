<?php


/**
 * 文件上传类
 */
class Uploads
{
  private $path = null;
  private $size = 123123123123;  //大小
  private $ext = ['jpg','png','gif','xls'];
  private $message = "";  //提示信息
  private $savefile = null;  //生成文件

  public function __construct($path=UPLOAD_PATH)
  {
    $this->path = $path;
  }

  //判断文件是否有上传
  public function isFile($name = null)
  {
    $file = null;
    if($name)
    {
      $file = $_FILES[$name];
    }else{
      reset($_FILES);  //将数组的指针重置回第一位
      $file = current($_FILES); //取到数组的第一个元素
    }

    $size = $file['size'] > 0 ? true : false;

    return $size;
  }

  //单个文件
  public function upload($name=null)
  {
    $file = null;
    if($name)
    {
      $file = $_FILES[$name];
    }else{
      reset($_FILES);  //将数组的指针重置回第一位
      $file = current($_FILES); //取到数组的第一个元素
    }

    $error = $file['error'];

    //判断错误
    if($error > 0)
    {
      switch($error)
      {
        case 1:
          $this->message = "超过php环境配置大小";
          break;
        case 2:
          $this->message = "超过表单提交大小";
          break;
        case 3:
          $this->message = "网络中断";
          break;
        case 4:
          $this->message = "无文件上传";
          break;
        default:
          $this->message = "未知错误";
      }
      return false;
    }

    //判断大小
    if($file['size'] > $this->size)
    {
      $this->message = "上传文件大小超出限制";
      return false;
    }

    //判断文件的类型
    $ext = PATHINFO($file['name'],PATHINFO_EXTENSION);
    if(!in_array($ext,$this->ext))
    {
      $this->message = "未知文件类型";
      return false;
    }

    //组装一个新的文件名称
    global $Strings;
    $filename = date("YmdHis").$Strings->randomStr(15,false).".$ext";

    if(!is_dir($this->path))
    {
      mkdir($this->path,0777,true);
    }

    //判断文件是否是通过http post上传上来
    if(is_uploaded_file($file['tmp_name']))
    {
      $res = move_uploaded_file($file['tmp_name'],$this->path."/$filename");

      if($res)
      {
        $this->savefile = "/".basename($this->path)."/".$filename;
        return true;
      }else{
        $this->message = "上传文件失败";
        return false;
      }

    }else{
      $this->message = "非法途径";
      return false;
    }

  }

  //返回上传成功后的文件名
  public function savefile()
  {
    return $this->savefile;
  }

  //返回错误信息
  public function getMessage()
  {
    return $this->message;
  }

  //多个文件
}


?>