<?php
include_once("../includes/init.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";

$json = array("msg"=>null,"result"=>false,"data"=>null);

if($action == "ordercart")
{
  if($_POST)
  {
    $userid = isset($_POST['userid']) ? trim($_POST['userid']) : 0;

    $cart = $db->select("cart.*,food.name,food.price")->from("cart")->join("food","cart.foodid = food.id")->where("cart.userid = $userid")->all();

    $cartBack = [
      "count"=>0,  //数量
      "price"=>0,  //价格
    ];

    //有数据
    foreach($cart as $item)
    {
      $cartBack['count'] += $item['foodnum'];
      $cartBack['price'] += ($item['price']*$item['foodnum']);
    }

    $result = [
      "cart"=>$cart,
      "cartBack"=>$cartBack
    ];

    $json['result'] = true;
    $json['data'] = $result;
    $json['msg'] = '返回购物车数据成功';

    return json($json);
  }
}else if($action == "orderadd")
{
  if($_POST)
  {
    //判断余额是否充足 更新用户表 订单表 订单产品 清空购物车
    $order = isset($_POST['order']) ? $_POST['order'] : false;
    $cart = isset($_POST['cart']) ? $_POST['cart'] : false;
    $userid = isset($_POST['userid']) ? $_POST['userid'] : 0;

    //判断用户是否存在
    $user = $db->select()->from("user")->where(["id"=>$userid])->find();

    if(!$user)
    {
      $json['msg'] = '该用户不存在';
      return json($json);
    }

    if(!$order)
    {
      $json['msg'] = '无订单数据';
      return json($json);
    }

    if(!$cart)
    {
      $json['msg'] = '无食品数据';
      return json($json);
    }

    //判断余额是否充足
    $money = $user['money'];
    $price = $order['price'];
    $updateMoney = $money-$price;
    if($updateMoney < 0)
    {
      $json['msg'] = '余额不足，请先充值';
      return json($json);
    }

    //更新用户表
    $userData = [
      "money"=>$updateMoney
    ];

    //开启事务
    $db->query("start transaction");

    $userRes = $db->update("user",$userData,"id = $userid");

    if(!$userRes)
    {
      $json['msg'] = '更新用户余额失败';
      return json($json);
    }

    //订单表
    $orderData = [
      "ordersn"=>$Strings->randomStr(20,false),
      "createtime"=>time(),
      "ordertype"=>$order['ordertype'],
      "price"=>$order['price'],
      "status"=>1,
      "content"=>$order["content"],
      "userid"=>$userid
    ];

    //预约
    if($order['ordertype'] == 1)
    {
      $orderData['ordertime'] = empty($order['ordertime']) ? 0 : strtotime($order['ordertime']);
    }else{
      $orderData['ordertime'] = time();
    }

    $orderRes = $db->add("order",$orderData);

    if(!$orderRes)
    {
      $db->query("ROLLBACK");
      $json['msg'] = '添加订单失败';
      return json($json);
    }

    $orderFood = [];
    foreach($cart as $item)
    { 
      $food = [
        "orderid"=>$orderRes,
        "foodid"=>$item['foodid'],
        "foodnum"=>$item['foodnum']
      ];
      $orderFood[] = $food;
    }

    $foodRes = $db->addAll("order_food",$orderFood);

    if(!$foodRes)
    {
      $db->query("ROLLBACK");
      $json['msg'] = '添加订单产品失败';
      return json($json);
    }

    //清空购物车
    $cartRes = $db->delete("cart","userid = $userid");

    if(!$cartRes)
    {
      $db->query("ROLLBACK");
      $json['msg'] = '清空购物车失败';
      return json($json);
    }

    //提交事务
    if($userRes && $orderRes && $foodRes && $cartRes)
    {
      $db->query("COMMIT"); //提交事务
      $json['msg'] = '下单成功';
      $json['result'] = true;
      return json($json);
    }
  }
}

?>