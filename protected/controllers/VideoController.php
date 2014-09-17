<?php

class VideoController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			// array('allow',  // allow all users to perform 'index' and 'view' actions
			// 	'actions'=>array('index','view','admin','delete','create','update'),
			// 	'users'=>array('*'),
			// ),
			// array('allow', // allow authenticated user to perform 'create' and 'update' actions
			// 	'actions'=>array('create','update'),
			// 	'users'=>array('@'),
			// ),
			// array('allow', // allow admin user to perform 'admin' and 'delete' actions
			// 	'actions'=>array('admin','delete'),
			// 	'users'=>array('admin'),
			// ),
			// array('deny',  // deny all users
			// 	'users'=>array('*'),
			// ),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Video;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Video']))
		{
			$model->attributes=$_POST['Video'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * With Ajax Creates a new model.
	 */
	public function actionAjaxCreate()
	{
		$model=new Video;

		$isSuccess = false;

		if(isset($_POST['Video']))
		{
			$model->attributes=$_POST['Video'];
			$isSuccess = $model->save();

		}

		echo json_encode(array('isSuccess' => $isSuccess));
		
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Video']))
		{
			$model->attributes=$_POST['Video'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Video');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Video('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Video']))
			$model->attributes=$_GET['Video'];

		$this->render('admin',array(
			'model'=>$model,
			'version' =>Video::model()->getGroupLastNum(),
		));
	}

	public function actionPlay()
	{
		$this->render('play');
	}

	public function actionUpload()
	{
		$imgData = $_POST['data'];
		//$image = base64_decode( str_replace('data:image/jpeg;base64,', '', $imgData)); 
		$data = explode(',', $imgData); 
		$filePath = 'upload/data/images/'. time() . '.png';
    	$fp = fopen($filePath, 'w');  
	    $result = fwrite($fp, base64_decode($data[1]));
	    fclose($fp);

	   echo json_encode(array('imgUrl' => '/'.$filePath));  
	}

	public function actionList()
	{
		$filename = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/data/').'/videoSource.json';
		
		$modelSource = array();
		if (file_exists($filename)) {
			$fileh = fopen($filename, 'r');
			$data = fread($fileh, filesize($filename));
			$modelSource = json_decode($data);
			fclose($fileh);
		}
		$this->render('list', array('model' => $modelSource));
	}

	public function actionAndroidVideoList()
	{
		$model = Video::model()->findAll();
		$this->render('androidVideoList', array('model' => $model));
	}

	public function get_files($dir) {
	    $files = array();

	 	$model = $this->queryVideo();

	    if (!is_dir($dir)) {
	        return $files;
	    }
	 	
	    $d = dir($dir);
	    while (false !== ($file = $d->read())) {
	        if ($file != '.' && $file != '..') {
	            $filename = $dir . "/"  . $file;
	 
	            if(is_file($filename) && pathinfo($filename,PATHINFO_EXTENSION) == 'mp4' && !in_array(pathinfo($filename)['basename'], $model) ) {
	                $files[] = array('basename' => pathinfo($filename)['basename'], 'filename' => pathinfo($filename)['filename'], 'basefilename' =>$filename);//$filename;
	            }
	            // else {
	            //     $files = array_merge($files, get_files($filename));
	            // }
	        }
	    }
	    // echo 'aaa';exit;

	    $d->close();
	    return $files;
	}

	// 加载一个列表
	public function actionCreateList()
	{
		$dirpath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/data/video/');
		$fileArr = $this->get_files($dirpath);
		$lastVersion = Video::model()->getGroupLastNum();

		$this->render('createList', array('fileArr' => $fileArr, 'version' => $lastVersion+1));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Video the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Video::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function queryVideo()
	{
		$model = Video::model()->findAll();
		$data = array();
		foreach ($model as $key => $value) {
			if (!empty($value->url)) {
				$expArr = explode('/', $value->url);
				$data[] = $expArr[count($expArr) - 1];
			}
			
		}
		return $data;
	}

	// 增加用户访问记录
	public function actionAddUserRecord()
	{
		$sessionId = session_id();
		$recordTime = date('Y-m-d H:i');

		$recordDir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/').'/data/record/';
		if (!file_exists($recordDir)) {
			mkdir($recordDir, 0777, true);
		}

		if(($fileh = fopen($recordDir.'user.log', 'a+')) !== false) {
			fputs($fileh, "{$sessionId} {$recordTime}\r\n");
			fclose($fileh);
		}
	}

	public function actionExportSource()
	{
		$version = 0;
		if (isset($_GET['version'])) {
			$version = $_GET['version'];
		}

		$model = Video::model()->findAll('version=:version', array(':version' => $version));
		$modelJSon = CJSON::encode($model);
		$dirpath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/data/');
		$file = fopen($dirpath . "/videoSource.json","w+");
		fwrite($file,$modelJSon);
		fclose($file);

		$file = $dirpath . "/videoSource.json";
 		
 		$filename = basename($file);
	   	header("Content-type: application/octet-stream");
 
	    //处理中文文件名
	    $ua = $_SERVER["HTTP_USER_AGENT"];
	    $encoded_filename = rawurlencode($filename);
	    if (preg_match("/MSIE/", $ua)) {
	     header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
	    } else if (preg_match("/Firefox/", $ua)) {
	     header("Content-Disposition: attachment; filename*=\"utf8''" . $filename . '"');
	    } else {
	     header('Content-Disposition: attachment; filename="' . $filename . '"');
	    }
	 
	    header("Content-Length: ". filesize($file));
	    readfile($file);
	}

	public function actionUploadVideoZip()
	{
		$this->render("uploadVideoZip");
	}

	public function actionUploadZip()
	{
		$uploadedFile = CUploadedFile::getInstanceByName('fileToUpload');
		if (!empty($uploadedFile)) 
		{
			$dirpath = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../upload/');
			$destFile = $dirpath .'/'. 'aaa.rar';
			$destPath = dirname($destFile);//echo $destPath;exit;
			// echo $destPath;exit;
			// 创建图片子目录。
			if (!file_exists($destPath)) {
				if(false === mkdir($destPath, 0755, true)){
					throw new CHttpException(403, '没有图片目录操作权限 ');
				}		
			}

			$uploadedFile->saveAs($destFile);

			echo CJSON::encode(array('lengthComputable' => 50));
		}
	}

	/**
	 * Performs the AJAX validation.
	 * @param Video $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='video-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
