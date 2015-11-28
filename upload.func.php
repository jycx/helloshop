<?php
	/*	单文件上传函数
	*	$files为单文件
	*	$type要验证是否属于规定的类型
	*	默认$max_size大小为2M
	*/
	
	function upload($files,$path,$type=array(),$max_size=2097152){
		//判断路径是否存在,不存在就创建目录，创建失败，直接退出函数。
		if(!file_exists($path)||is_file($path)){
		//@的作用是屏蔽报错
			if(!@mkdir($path)){
				$result['msg']='创建目录失败';
				return $result;
			}
		}
		$path=rtrim($path,'/');

		//判断文件的大小是否超过大小限制，超过大小返回错误信息。
		if($files['size']>$max_size){
			$result['msg']='上传文件大小超出了PHP配置文件中upload_max_filesize限制';
			return $result;
		}
		
		//判断文件的error是否为0，只操作元素为0的数组。error不为的0，返回错误信息
		switch($files['error']){
			case 0:
				break;
			case 1:
				$result['msg']='上传文件大小超出了PHP配置文件中upload_max_filesize限制';
				return $result;
			case 2:
				$result['msg']='上传文件大小超出了表单中,MAX_FILE_SIZE指定的值';
				return $result;
			case 3:
				$result['msg']='文件上传不完整';
				return $result;
			case 4:
				$result['msg']='没有上传任何文件';
				return $result;
			case 6:
				$result['msg']='没有上传任何文件';
				return $result;
			default:
				$result['msg']='未知错误';
				return $result;
		}
		
		//判断文件是否通过HTTP POST上传的
		if(!is_uploaded_file($files['tmp_name'])){
			$result['msg']='文件不合法';
			return $result;
		}
		
		/*
		$pathinfo=pathinfo($files['name']);
		$ext=$pathinfo['extension'];
		*/
		
		//判断文件是否属于允许的类型，不属于这个类型就返回错误信息。
		$ext=array_pop((explode('.',$files['name'])));
		if(is_array($type)&&count($type)>0){
			if(!in_array($ext,$type)){
				$result['msg']='不允许此类型文件上传';
				return $result;
			}
		}
		
		//先生成新文件名
		$filename=uniqid().'.'.$ext;
		
		//移动文件，如果文件移动未成功就返回错误信息
		if(!move_uploaded_file($files['tmp_name'],$path.'/'.$filename)){
			$result['msg']='移动文件出错';
			return $result;
		}
		
		//返回成功信息，返回文件大小和原名、新名字
		$result['msg']='上传成功';
		$result['name']=$files['name'];
		$result['size']=$files['size'];
		$result['filename']=$filename;
		return $result;
	}