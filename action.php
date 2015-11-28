<?php 
session_start();

?>


<form action="action.php?b=ch" method="post" align="right" >
CH：<input type="radio" name="language" id="ch"/>
<input type="submit" value="choose" />  
</form> 
<form action="action.php?b=en" method="post" align="right" >
EN：<input type="radio" name="language" id="en"/>
<input type="submit" value="choose" />
</form> 
<?php
	//注意数据库一定要匹配正确，即配置文件要修改相应的自己的密码，数据库，不然会出错。
	//单文件商城
	//路径方式index.php?a=del&id=3
	//加载数据库文件
	//display_header();//输出头部
	
 
if(isset($_GET["b"]))
	{$bc=$_GET["b"];
	$_SESSION["temp"]=$bc;}
		if($_SESSION["temp"]==en){
	require_once 'english.php';
	} 
	else  {
	require_once 'chinese.php';
	}
 
	?>
	

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
			<title>商城</title>
		</head>
		<body>
	<center>
				<h2> <?php echo Merchandise_management_system;?> </h2>
				<a href="action.php?a=display"><?php echo  add_goods;?> </a> |<a href="action.php?a=list"><?php echo see_the_detail;?></a>|
				<a href="action.php?a=mycar"><?php echo  i;?> </a> |<a href="action.php?a=cleanmycar"><?php echo j;?></a>
				<hr />
 
		 
	 </center>
		</body>
		 </html>

<?php	 
	require_once 'config.php';//加载数据库配置文件
	require_once 'upload.func.php';//加载上传函数
	
	//链接数据库
	$link=mysqli_connect($shop_db['host'],$shop_db['user'],$shop_db['pwd'],$shop_db['dbname']) or die('链接数据库出错');
	mysqli_set_charset($link,'utf8');//设置数据库字符集
	 
	//接收参数
	if(isset($_GET['a']))
	{$ac=$_GET['a'];}
	//echo $ac;
	//echo $link;

	//判断模式，如果是add就是添加商品，是list就是查看商品列表。是del就是删除商品。default就是显示首页添加商品页面。
	echo "<center>";
	switch($ac){
		case 'add':
			$result=add($link,$shop_db);
			break;
		case 'list':
			$result=list_goods($link,$shop_db);
			break;
		case 'del':
			$result=del($link,$shop_db);
			break;
		case 'modify':
			$result=modify($link,$shop_db);
			break;
		case 'desc':
			$result=display_desc($link,$shop_db);
			break;
		case 'addcar':
			$result=addcar($link,$shop_db);
			break;
		case 'mycar':
			$result=mycar($link,$shop_db);
			break;
		case 'cleanmycar':
			$result=cleanmycar($link,$shop_db);
			break;
		default:
			display_index();
	}
	echo "</center>";
	//var_dump($result);exit;
	
	mysqli_close($link);//输出底部
	?>
	 
	
 
	<?php	
	//显示输入表单函数
	function display_index(){
		echo '<form action="action.php?a=add" method="post"  enctype="multipart/form-data">';
			echo h ; echo'：<input type="text" name="name" />';
			echo b ; echo'：<input type="text" name="type" />';
			echo c ; echo'：<input type="text" name="price" />';
			echo d ; echo'：<input type="text" name="num" />';
			echo e ; echo'：<input type="file" name="file" />';
			echo' <input type="submit" value="提交" />　　<input type="reset" value="重置" />
		</form>';
	}
	//显示商品详细页面
	function display_desc($link,$shop_db){
		$id=$_GET['id'];
		$path='upload/';
		$sql="select * from goods where id={$id}";
		$result=mysqli_query($link,$sql);
		if($result&&$row=mysqli_fetch_assoc($result)){
			if($row['pic']){$image=$path.$row['pic'];}else{$image=null;}
			echo "<form action='action.php?a=modify&id={$row['id']}' method='post'  enctype='multipart/form-data'>
				编号：<input type='text' name='id' value='{$row['id']}' /> 
				名称：<input type='text' name='name' value='{$row['name']}' /> 
				型号：<input type='text' name='type' value='{$row['type']}' /> 
				单价：<input type='text' name='price' value='{$row['price']}' /> 
				数量：<input type='text' name='num' value='{$row['num']}' /> 
				数量：<input type='text' name='addtime' value='{$row['addtime']}' /> 
				图片：<input type='file' name='file' /><img src='' alt='图片'/>
				<input type='submit'  value='修改' /> <input type='reset'  value='重置' /> 
			</form>";
		}else{
			echo '获取信息失败';
			return false;
		}
	}
	//添加商品函数
	function add($link,$shop_db){
		$goods_name=$_POST['name'];
		$goods_type=$_POST['type'];
		$goods_price=$_POST['price'];
		$goods_num=$_POST['num'];
		if(count($_FILES['file'])){
			$goods_file=$_FILES['file'];
			$path='upload/';
			$file=upload($goods_file,$path);
			$image=$file['filename'];
		}else{
			$image=null;
		}
		$time=date('Y-m-d H:i:s',time()+8*3600);
		$sql="insert into goods(name,type,price,num,pic,addtime) values('{$goods_name}','{$goods_type}','{$goods_price}','{$goods_num}','{$image}',now())";
		//echo $sql;
		$result=mysqli_query($link,$sql);
		
		if($result&&mysqli_affected_rows($link)){
			echo '添加商品成功';
			return $result;
		}else{
			echo '添加商品失败';
			return $result;
		}
	}
	//显示商品列表函数
	function list_goods($link,$shop_db){
		$path='upload/';
		$sql="select * from goods order by addtime desc";
		$result=mysqli_query($link,$sql);
		if($result&&mysqli_num_rows($result)){
			echo "<table border='1'><tr><th>"; echo a ;echo "</th><th>"; echo h ; echo "</th><th>"; echo b; echo "</th><th>"; echo c  ; echo "</th><th>"; echo d ; echo "</th><th>"; echo e ;echo "  </th><th>"; echo f ;echo " </th><th>"; echo g ;echo "</th></tr>";
			while($row=mysqli_fetch_assoc($result)){
				echo '<tr>';
				foreach($row as $k=>$val){
					if($k=='pic'&&$val){
						echo "<td><img src='{$path}{$val}' alt='图片'/></td>";
					}else{
						echo "<td>{$val}</td>";
					}
				}
				echo '<td><a href="action.php?a=del&id='.$row['id'].'">删除</a><a href="action.php?a=desc&id='.$row['id'].'">修改</a><a href="action.php?a=addcar&id='.$row['id'].'">加入购物车</a></td></tr>';
			}
			echo '</table>';
			mysqli_free_result($result);
			return true;
		}else{
			echo '查询失败';
			return $result;
		}
	}
	//删除商品函数
	function del($link,$shop_db){
		$id=$_GET['id'];
		$sql="delete from goods where id={$id}";
		$result=mysqli_query($link,$sql);
		//var_dump($result);exit;
		if($result&&$num=mysqli_affected_rows($link)){
			echo "删除成功，有{$num}条数据被删除";
			return $result;
			
		}else{
			echo '删除失败';
			return $result;
		}
		
	}
	//修改商品内容函数
	function modify($link,$shop_db){
		$id=$_GET['id'];
		$goods_id=$_POST['id'];
		$goods_name=$_POST['name'];
		$goods_type=$_POST['type'];
		$goods_price=$_POST['price'];
		$goods_num=$_POST['num'];
		$goods_time=$_POST['addtime'];
		//echo $_FILES['file'];exit;
		if(count($_FILES['file'])){
			
			$goods_file=$_FILES['file'];
			$path='upload/';
			$file=upload($goods_file,$path);
			$image=$file['filename'];
		}else{
			$image=null;
		}
		//print_r($_POST);
		$sql="update goods set id={$goods_id},name='{$goods_name}',type='{$goods_type}',price={$goods_price},num={$goods_num},pic='{$image}',addtime='{$goods_time}' where id={$id}";
		//echo $sql;
		$result=mysqli_query($link,$sql);
		$num=mysqli_affected_rows($link);
		if($result){
			echo "{$num}条修改成功";
			return true;
		}else{
			echo '修改失败';
			return false;
		}
	}
	
	//添加商品到购物车
	
	function addcar($link,$shop_db)
	{
echo"<center>";
 echo add_success;
 echo"</center>";
$number= $_GET['id'];
 
 $sql="select * from goods where id ={$number}";

 $result=mysqli_query($link,$sql);
 if (!empty($result)){
 $_SESSION['goods'][]=mysqli_fetch_array($result);}
	}
	
	//查看购物车的商品
 function mycar($link,$shop_db)
	{
	$time1=date('Y-m-d H:i:s',time()+8*3600);
	echo "<center>";
	echo " <table border='1'><tr><th>"; echo a ;echo "</th><th>"; echo h ; echo "</th><th>"; echo b; echo "</th><th>"; echo c  ; echo "</th><th>"; echo d ; echo "</th><th>"; echo e ;echo "  </th><th>"; echo f ; echo "</th></tr>";
if(isset($_SESSION['goods'])){
	foreach($_SESSION['goods'] as $v ){
 echo "<center>";
 echo "<tr><th>";echo  $v['id']  ;echo "</th><th>"; echo  $v['name']; echo "</th><th>"; echo $v['type']; echo "</th><th>";echo  $v['price'] ; echo "</th><th>"; echo $v['num'] ; echo "</th><th>"; echo $v['pic'];echo "  </th><th>"; echo $time1 ;echo "</th></tr>";
 
 }
 }
	}
	
		//删除购物车的商品
		
    function cleanmycar($link,$shop_db){
		unset($_SESSION['goods']);
		header("location:action.php");
		}
	?>
