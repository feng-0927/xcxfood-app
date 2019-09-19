<?php


function json($result)
{
  echo json_encode($result);
  exit;
}

//跳转提醒
function show($msg="",$url="")
{
  @header("Content-Type:text/html;charset=utf-8");
  if(empty($url))
  {
    echo "<script>alert('$msg');history.go(-1);</script>";
    exit;
  }else{
    echo "<script>alert('$msg');location.href='$url';</script>";
    exit;
  }
}

//验证管理员是否登录
//location 默认不跳转
function checkLogin($location = false,$url='')
{
  global $db;  //获取全局变量
  $adminid = isset($_SESSION['adminid']) ? $_SESSION['adminid'] : 0;
  $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';


  //两个只要有其中一个不存在
  if(!$adminid || empty($username))
  {
    session_unset();
    show('登录有误，请重新登录','login.php');
  }else{
    //两个都存在
    $where = [
      "id"=>$adminid,
      "username"=>$username
    ];

    $admin = $db->select()->from("admin")->where($where)->find();

    if(!$admin)
    {
      session_unset();
      show('登录有误,请重新登录','login');
    }

    if($location)
    {
      //location 等于true 说明要跳转
      if($url)
      {
        //给了地址就跳转指定地址
        header("Location:$url");
        exit;
      }else{
        //没有地址就跳转到默认首页
        header("Location:index.php");
        exit;
      }

    }
  }
}


//得到当前网址
function get_url(){
	$str = $_SERVER['PHP_SELF'].'?';
	if($_GET){
		foreach ($_GET as $k=>$v){  //$_GET['page']
			if($k!='page'){
				$str .= $k.'='.$v.'&';
			}
		}
	}
	return $str;
}



//分页函数
/**
 *@pargam $current	当前页
 *@pargam $count	记录总数
 *@pargam $limit	每页显示多少条
 *@pargam $size		中间显示多少条
 *@pargam $class	样式
*/
function page($current,$count,$limit,$size,$class='sabrosus'){
	$str='';
	if($count>$limit){
		$pages = ceil($count/$limit);//算出总页数
		$url = get_url();//获取当前页面的URL地址（包含参数）
		
		$str.='<div class="'.$class.'">';
		//开始
		if($current==1){
			$str.='<span class="disabled">首&nbsp;&nbsp;页</span>';
			$str.='<span class="disabled">  &lt;上一页 </span>';
		}else{
			$str.='<a href="'.$url.'page=1">首&nbsp;&nbsp;页 </a>';
			$str.='<a href="'.$url.'page='.($current-1).'">  &lt;上一页 </a>';
		}
		//中间
		//判断得出star与end
	    
		 if($current<=floor($size/2)){ //情况1
			$star=1;
			$end=$pages >$size ? $size : $pages; //看看他两谁小，取谁的
		 }else if($current>=$pages - floor($size/2)){ // 情况2
				 
			$star=$pages-$size+1<=0?1:$pages-$size+1; //避免出现负数
		
			$end=$pages;
		 }else{ //情况3
		 
			$d=floor($size/2);
			$star=$current-$d;
			$end=$current+$d;
		 }
	
		for($i=$star;$i<=$end;$i++){
			if($i==$current){
				$str.='<span class="current">'.$i.'</span>';	
			}else{
				$str.='<a href="'.$url.'page='.$i.'">'.$i.'</a>';
			}
		}
		//最后
		if($pages==$current){
			$str .='<span class="disabled">  下一页&gt; </span>';
			$str.='<span class="disabled">尾&nbsp;&nbsp;页  </span>';
		}else{
			$str.='<a href="'.$url.'page='.($current+1).'">下一页&gt; </a>';
			$str.='<a href="'.$url.'page='.$pages.'">尾&nbsp;&nbsp;页 </a>';
		}
		$str.='</div>';
	}
	
	return $str;
}


//调用第三方微信接口授权
function code2Session($js_code = null)
{
    if($js_code)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wxd9efc4772fb83b59&secret=53901ace5607b970114614e8ab292d15&js_code=$js_code&grant_type=authorization_code";

        $result = https_request($url);

        $resultArr = json_decode($result,true);

        return $resultArr;
    }else{
        return false;
    }
}


//发送一个http的请求服务
function https_request($url,$data = null)
{
    if(function_exists('curl_init')){
    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }else{
        return false;
    }
}


?>