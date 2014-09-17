<?php

class UploadController extends Controller
{

	public function actionIndex()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/

	/**
	 *
	 * Logging operation - to a file (upload_log.txt) and to the stdout
	 * @param string $str - the logging string
	 */
	function _log($str) {

	    // log to the output
	    $log_str = date('d.m.Y').": {$str}\r\n";
	    echo $log_str;

	    // log to file
	    if (($fp = fopen(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/upload_log.txt', 'a+')) !== false) {
	        fputs($fp, $log_str);
	        fclose($fp);
	    }
	}

	/**
	 * 
	 * Delete a directory RECURSIVELY
	 * @param string $dir - directory path
	 * @link http://php.net/manual/en/function.rmdir.php
	 */
	function rrmdir($dir) {
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (filetype($dir . "/" . $object) == "dir") {
	                    rrmdir($dir . "/" . $object); 
	                } else {
	                    unlink($dir . "/" . $object);
	                }
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	    }
	}

	/**
	 * Delete a file
	 * @param string $fileName - file path
	 */
	function rrmfile($fileName)
	{
		if (is_file($fileName)) {
			unlink($fileName);
		}
	}

	function ezip($fileName, $hedef = '')
    {
        
        $zip = zip_open($fileName);
        while($zip_icerik = zip_read($zip)):
            $zip_dosya = zip_entry_name($zip_icerik);
            if(strpos($zip_dosya, '.')):
                $hedef_yol = $hedef.$zip_dosya;
                touch($hedef_yol);
                $yeni_dosya = fopen($hedef_yol, 'w+');
                fwrite($yeni_dosya, zip_entry_read($zip_icerik));
                fclose($yeni_dosya); 
            else:
                @mkdir($hedef.$zip_dosya);
            endif;
        endwhile;
    }

	/**
	 *
	 * Check if all the parts exist, and 
	 * gather all the parts of the file together
	 * @param string $dir - the temporary directory holding all the parts of the file
	 * @param string $fileName - the original file name
	 * @param string $chunkSize - each chunk size (in bytes)
	 * @param string $totalSize - original file size (in bytes)
	 */
	function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize) {

	    // count all the parts of this file
	    $total_files = 0;
	    foreach(scandir($temp_dir) as $file) {
	        if (stripos($file, $fileName) !== false) {
	            $total_files++;
	        }
	    }

	    // check that all the parts are present
	    // the size of the last part is between chunkSize and 2*$chunkSize
	    if ($total_files * $chunkSize >=  ($totalSize - $chunkSize + 1)) {

	        // create the final destination file 
	        if (($fp = fopen(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/'.$fileName, 'w')) !== false) {
	            for ($i=1; $i<=$total_files; $i++) {
	                fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
	                $this->_log('writing chunk '.$i);
	            }
	            fclose($fp);

	            // 处理压缩文件
	            $originPath = dirname($temp_dir).'/'.$fileName;
	            if (file_exists($originPath) && pathinfo($originPath,PATHINFO_EXTENSION) == 'zip') {
	            	
	            	// 在解压之前先把上一次的videoSource.json备份
					$uploadDir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/');
					if (file_exists($uploadDir.'/data/videoSource.json')) {
						$bakDir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/bak/';
						if (!file_exists($bakDir)) {
							mkdir($bakDir, 0777, true);
							echo '第一版数据先创建 bak 目录\r\n';
						}

						// 读取 bak 目录下的文件数量，判断是否需要删除第一版数据源
						$files = $this->get_files($bakDir);
						echo '<br>file bak count : '.count($files)."<br>";
						if (count($files >= 2)) {
							if ($files[0]['filetime'] > $files[1]['filetime']) {
								// 处理掉时期最早的数据
								echo $files[1]['basefilename'].' filetime: '.date('Y:m:d H:i:s', $files[1]['filetime']).'<br>';
								$this->removeSourceBak($files[1]['basefilename']);

							}else{
								// 处理掉时期最早的数据
								echo $files[0]['basefilename'].'filetime: '.date('Y:m:d H:i:s', $files[0]['filetime']).'<br>';
								$this->removeSourceBak($files[0]['basefilename']);
							}
						}

						copy($uploadDir.'/data/videoSource.json', $uploadDir.'/data/bak/'.$this->createUuid().'.bak');
						echo 'create bak file : '.(count($files)+1)."<br>";
					}

	            	$zip = new ZipArchive;
	            	$res = $zip->open($originPath);

	            	if ($res === true) {
	            		// 解压到当前目录
	            		$zip->extractTo(dirname($temp_dir).'/');
	            		$zip->close();

	            		// 删除压缩文件
	            		$this->rrmfile($originPath);
	            		echo 'extract success';
	            	}else{
	            		echo 'failed code : '.$res;
	            	}

	            }

	        } else {
	            $this->_log('cannot create the destination file');
	            return false;
	        }

	        // rename the temporary directory (to avoid access from other 
	        // concurrent chunks uploads) and than delete it
	        if (rename($temp_dir, $temp_dir.'_UNUSED')) {
	            $this->rrmdir($temp_dir.'_UNUSED');
	        } else {
	            $this->rrmdir($temp_dir);
	        }
	    }

	}

	// 测试手动解压
	public function actionEzip($fileName)
	{
		$this->ezip(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/'.$fileName, realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/');
		echo 'success';
	}

	public function actionUpload()
	{
		//check if request is GET and the requested chunk exists or not. this makes testChunks work
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {

		    $temp_dir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/'.$_GET['resumableIdentifier'];
		    $chunk_file = $temp_dir.'/'.$_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];
		    if (file_exists($chunk_file)) {
		         header("HTTP/1.0 200 Ok");
		       } else
		       {
		         header("HTTP/1.0 404 Not Found");
		       }
		}

		// loop through files and move the chunks to a temporarily created directory
		if (!empty($_FILES)) foreach ($_FILES as $file) {

		    // check the error status
		    if ($file['error'] != 0) {
		        $this->_log('error '.$file['error'].' in file '.$_POST['resumableFilename']);
		        continue;
		    }

		    // init the destination file (format <filename.ext>.part<#chunk>
		    // the file is stored in a temporary directory
		    $temp_dir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/'.$_POST['resumableIdentifier'];
		    $dest_file = $temp_dir.'/'.$_POST['resumableFilename'].'.part'.$_POST['resumableChunkNumber'];

		    // create the temporary directory
		    if (!is_dir($temp_dir)) {
		        mkdir($temp_dir, 0777, true);
		    }

		    // move the temporary file
		    if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
		        $this->_log('Error saving (move_uploaded_file) chunk '.$_POST['resumableChunkNumber'].' for file '.$_POST['resumableFilename']);
		    } else {

		        // check if all the parts present, and create the final destination file
		        $this->createFileFromChunks($temp_dir, $_POST['resumableFilename'], 
		                $_POST['resumableChunkSize'], $_POST['resumableTotalSize']);

		    }
		}

	}

	// 读取 bak 目录下的文件
	public function get_files($dir) {
	    $files = array();

	    if (!is_dir($dir)) {
	        return $files;
	    }
	 	
	    $d = dir($dir);
	    while (false !== ($file = $d->read())) {
	        if ($file != '.' && $file != '..') {
	            $filename = $dir . "/"  . $file;
	 
	            if(is_file($filename)) {
	            	$extension = pathinfo($filename,PATHINFO_EXTENSION);
	            	$fileinfo = pathinfo($filename);
	            	$fname = $fileinfo['filename'];
	                $files[] = array('extension' => $extension, 
	                'filename' => $fname, 
	                'basefilename' =>$filename, 
	                'filetime' => fileatime($filename));//$filename;
	            }
	            // else {
	            //     $files = array_merge($files, get_files($filename));
	            // }
	        }
	    }

	    $d->close();
	    return $files;
	}

	// 删除备份的数据源
	public function removeSourceBak($filename)
	{
		$baseFilePath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../');
		if (file_exists($filename)) {
			$fileh = fopen($filename, 'r');
			$data = fread($fileh, filesize($filename));
			$source = json_decode($data);
			fclose($fileh);

			foreach ($source as $key => $value) {
				if (is_file($baseFilePath.$value->url)) {
					unlink($baseFilePath.$value->url);
				}

				if (is_file($baseFilePath.$value->imgUrl)) {
					unlink($baseFilePath.$value->imgUrl);
				}
			}

			unlink($filename);

			echo 'clear files success';
		}
	}

}