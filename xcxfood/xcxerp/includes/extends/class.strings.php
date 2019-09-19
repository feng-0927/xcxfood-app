<?php


/**
 * 字符串类
 */
class Strings
{
  //生成随机字符串的方法
  function randomStr($length = 10,$other=true) 
  {
    // 密码字符集，可任意添加你需要的字符 
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 

    'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 

    't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 

    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 

    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 

    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'); 

    
    //other参数值为true带有特殊字符
    if($other)
    {
      $otherArr = ['!','@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_','[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',','.', ';', ':', '/', '?', '|'];
      $chars = array_merge($chars,$otherArr);
    }

    

    // 在 $chars 中随机取 $length 个数组元素键名 

    $keys = array_rand($chars, $length); 
    $password = '';

    for($i = 0; $i < $length; $i++) 
    {
      // 将 $length 个数组元素连接成字符串 
      $password .= $chars[$keys[$i]];
    }

    return $password;
  }
}


?>