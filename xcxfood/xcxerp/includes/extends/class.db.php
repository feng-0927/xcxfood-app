<?php


/**
 * 数据库类
 */
class DB
{
  //私有属性 在外面不能访问
  private $hostname = null;
  private $username = null;
  private $password = null;
  private $charset = "UTF8";
  private $pre = "pre_";
  private $dbname = null;
  private $link = null;  //套接字  连接标识符
  private $sql = null;

  //构造函数 在对象实例化的时候，自动调用的一个方法
  public function __construct($hostname = "localhost",$username ='root',$password='',$dbname='')
  {
    $this->hostname = $hostname;
    $this->username = $username;
    $this->password = $password;
    $this->dbname = $dbname;

    //连接数据库操作
    $this->link = mysqli_connect($this->hostname,$this->username,$this->password) or die("连接数据库失败");

    //选择数据库
    mysqli_select_db($this->link,$this->dbname);

    //设置数据库编码
    $res = mysqli_query($this->link,"SET NAMES {$this->charset}");
  }


  public function select($fields = "*")
  {
    $this->sql = "SELECT $fields ";
    return $this;
  }

  public function from($table)
  {
    $this->sql .= "FROM {$this->pre}$table AS $table ";
    return $this;
  }

  public function where($where = 1)
  {
    if(is_array($where))
    {
      $this->sql .= "WHERE ";
      foreach($where as $key=>$item)
      {
        $this->sql .= "$key = '$item' AND ";
      }
    }else {
      $this->sql .= "WHERE $where ";
    }

    $this->sql = trim($this->sql,"AND ");
    
    return $this;
  }

  public function orderby($fields=null,$by="asc")
  {
    if($fields)
    {
      $this->sql .= " ORDER BY $fields $by ";
      return $this;
    }else{
      return $this;
    }
  }

  public function limit($start=0,$limit=10)
  {
    $this->sql .= " LIMIT $start,$limit";
    return $this;
  }

  public function all()
  { 
    //会返回一个执行结果
    $res = mysqli_query($this->link,$this->sql);

    if(!$res)
    {
      $this->error();
      exit();
    }

    $data = array();
    while($row = mysqli_fetch_assoc($res))
    {
      $data[] = $row;
    }

    return $data;
  }

  public function find()
  {
    //会返回一个执行结果
    $res = mysqli_query($this->link,$this->sql);

    if(!$res)
    {
      $this->error();
      exit();
    }

    return mysqli_fetch_assoc($res);
  }

  //获取sql语句错误的信息
  public function error()
  {
    $error = mysqli_error($this->link);
    $message = "[".date("Y-m-d H:i")."] SQL错误：".$error."\r\n";
    
    $filename = APP_PATH."/includes/extends/mysql_error.log";
    file_put_contents($filename,$message,FILE_APPEND);
    echo "SQL语句执行失败";
    exit;
  }

  //插入多条数据
  public function addAll($table,$data)
  {
    //获取表字段
    $sql = "desc {$this->pre}$table";
    $res = mysqli_query($this->link,$sql);
    $tableFields = [];
    while($row = mysqli_fetch_assoc($res))
    {
      if($row['Key'] == "PRI")
      {
          continue;
      }else{
        $tableFields[] = $row['Field'];
      }
    }

    

    //组装好的字段部分
    sort($tableFields);
    $fields = "`".implode("`,`",$tableFields)."`";

    $dataArr = [];
    foreach($data as $item)
    {
      ksort($item);
      $dataArr[] = "('".implode("','",$item)."')";
    }

    if(count($dataArr) > 0)
    {
      $dataStr = implode(",",$dataArr);
    }else{
      $dataStr = "";
    }

    $this->sql = "INSERT INTO {$this->pre}$table($fields) VALUES $dataStr";
    $res = mysqli_query($this->link,$this->sql);

    if(!$res)
    {
      $this->error();
      return false;
    }

    return mysqli_affected_rows($this->link);
  }


  //连表查询
  public function join($table,$on,$by="LEFT")
  {
    $this->sql .= "$by JOIN {$this->pre}$table AS $table ON $on ";
    return $this;
  }

  public function delete($table,$where = 1)
  {
    $this->sql = "DELETE FROM {$this->pre}$table where $where";
    $res = mysqli_query($this->link,$this->sql);
    if(!$res)
    {
      $this->error();
      exit;
    }

    return mysqli_affected_rows($this->link);
  }

  //插入方法
  public function add($table,$data)
  {
    $dataKey = array_keys($data);
    $fields = "`".implode("`,`",$dataKey)."`";
    $value = "'".implode("','",$data)."'";

    $this->sql = "INSERT INTO {$this->pre}$table($fields)VALUES($value)";

    $res = mysqli_query($this->link,$this->sql);

    if(!$res)
    {
      $this->error();
      exit;
    }

    //返回插入id
    return mysqli_insert_id($this->link);
  }

  //更新方法

  //更新 和 插入方法写成一个
  public function update($table,$data,$where = 1)
  {
    $this->sql = "UPDATE {$this->pre}$table SET ";
    foreach($data as $key=>$item)
    {
      $this->sql .= "`$key`='$item',";
    }
    $this->sql = trim($this->sql,",");
    $this->sql .= " WHERE $where";
    $res = mysqli_query($this->link,$this->sql);
    if(!$res)
    {
      $this->error();
    }

    return mysqli_affected_rows($this->link);
  }

  //查询sql的方法
  public function getSQL()
  {
    return $this->sql;
  }

  //执行源生sql语句方法
  public function query($sql)
  {
    $this->sql = $sql;
    return mysqli_query($this->link,$this->sql);
  }
}

?>