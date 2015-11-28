<?php
if(isset($_GET["b"]))
	{$bc=$_GET["b"];}
		if($bc==en){
	require_once 'english.php';
	} 
	else if($bc==ch)  {
	require_once 'chinese.php';
	}
echo Merchandise_management_system;
	?>