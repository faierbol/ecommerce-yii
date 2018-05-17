<?php

class BannersController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/adminwithmenu';

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
	protected function beforeAction($action)
		{
			if (!parent::beforeAction($action)) {
				return false;
			}

			$user = Yii::app()->adminUser;
			if($user->isGuest && Yii::app()->controller->action->id != 'index'&& Yii::app()->controller->action->id != 'resized') {
				$this->redirect(Yii::app()->adminUser->loginUrl);
				return false;
			}elseif(isset(Yii::app()->adminUser->id) && Yii::app()->controller->action->id == 'index'){
				$this->redirect(array('/admin/action/dashboard'));
				return false;
			}

			return true;
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
		$models=new Banners;
		$all_banners = Banners::model()->findAll();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Banners']))
		{
			if(count($all_banners)<5)
			{
				$models->attributes=$_POST['Banners'];
					/*$bannerUpload = CUploadedFile::getInstance($model,'bannerimage');
					print_r($bannerUpload);
					if(!is_null($bannerUpload)) {

						$model->bannerimage = rand(0000,9999).'_'.$bannerUpload;
					} else {
						$model->bannerimage = "";
					}
					if($model->save(false)) {
						if(!is_null($bannerUpload)){
							$bannerUpload->saveAs('media/banners/'.str_replace(" ","-",$model->bannerimage));
						}
						}	echo $model->bannerimage;die;*/

				$catImage = CUploadedFile::getInstances($models,'bannerimage');
				$appImage = CUploadedFile::getInstances($models,'appbannerimage');
				list($width,$height) = getimagesize($catImage[0]->tempName);
				list($width1,$height1) = getimagesize($appImage[0]->tempName);
				//if($width == "1140" && $height =="325" && $width1 == "1024" && $height1 == "500")
				if($width == "1920" && $height =="400" && $width1 == "1024" && $height1 == "500")
				{
					if(!empty($catImage)) {
						$imageName = explode(".",$catImage[0]->name);
						$models->bannerimage = rand(000,9999).'-'.Myclass::productSlug($imageName[0]).'.'.$catImage[0]->extensionName;
					}
						if(!empty($catImage)) {
							$catImage[0]->saveAs('media/banners/'. $models->bannerimage);
						}

					$appImage = CUploadedFile::getInstances($models,'appbannerimage');
					if(!empty($appImage)) {
						$imageName = explode(".",$appImage[0]->name);
						$models->appbannerimage = rand(000,9999).'-'.Myclass::productSlug($imageName[0]).'.'.$appImage[0]->extensionName;
					}
						if(!empty($appImage)) {
							$appImage[0]->saveAs('media/banners/'. $models->appbannerimage);
						}

					if($models->save())
						$this->redirect(array('view','id'=>$models->id));
				}
				else
				{
					Yii::app()->user->setFlash('success','Please upload the image with the specified size');
					$this->redirect(array('create'));
				}
			}
			else
			{
					Yii::app()->user->setFlash('success','You can upload maximum 5 banners only');
					$this->redirect(array('admin'));
			}
		}

		$this->render('create',array(
			'model'=>$models,
		));
	}


	public function actionBannervideo()
	{

		
		$models = Sitesettings::model()->findByPk(1);
		$oldBannerVideo = $models->bannervideo;
	//	$models->setScenario('videosettings');
		
			

		if(isset($_POST['submit']))
		{
			if($_POST['bannertxt']!="")
			{
				$bannertext=$_POST['bannertxt'];
				$models->bannerText =$bannertext;
      						$models->save(false);	
			}
			
			

			$fileDir="media/banners/videos/";
			$allowedExts = array("mp4");
			$allowedExts2 = array("jpg", "jpeg", "png");
			$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			$extension2 = pathinfo($_FILES['file2']['name'], PATHINFO_EXTENSION);
			/*$_FILES['file']['name']=rand(000,9999).'-'.$_FILES['file']['name'];
			$_FILES['file2']['name']=rand(000,9999).'-'.$_FILES['file2']['name'];*/

			$_FILES['file']['name']=rand(000,9999).'.'.$extension;
			$_FILES['file2']['name']=rand(000,9999).'.'.$extension2;

		
			$videoErrorMsg="";
			$videoPosterErrorMsg="";

if(!empty($extension))
{
	


	if (in_array($extension, $allowedExts))
 	{
 		
  		/*$imagedetails = getimagesize($_FILES['file']['tmp_name']);
		$width = $imagedetails[0];
		$height = $imagedetails[1];
		*/
		if($_FILES["file"]["size"] < 50643703)
		{ 
  			if ($_FILES["file"]["error"] > 0)
    		{
    				
    				$videoErrorMsg=Yii::t('admin','Error video file upload');
    		}
  			else
    		{
    		
      				if(move_uploaded_file($_FILES["file"]["tmp_name"],$fileDir . $_FILES["file"]["name"]))
      				{
      						
      						// Remove old File while uploading new video
      						if($oldBannerVideo != "")
      						{
								$videofileDir="media/banners/videos/".$oldBannerVideo;
      							if (file_exists($videofileDir))
      							{
      	 							unlink($videofileDir);//delete video
      							}
      						}
							// end remove script
							
      			 			$models->bannervideo =$_FILES["file"]["name"];
      						$models->save(false);
      						Yii::app()->user->setFlash('success',Yii::t('admin','File uploaded successfully'));

							
      				}
      				else
      				{		
      						$videoErrorMsg=Yii::t('admin','Error Move video File upload');

      				}
     
    		}
   	}
    	else
      	{				
      					$videoErrorMsg=Yii::t('admin','Maximum video upload size only 50 MB');

      	}

    }
    else
    {
    					$videoErrorMsg=Yii::t('admin','Invalid video file type');

      			
    }
}
// video file upload script end



		/*	if($videoPosterErrorMsg!="" && $videoErrorMsg!="")
			{
				$msg=$videoPosterErrorMsg.','.$videoErrorMsg;
			}
				elseif ($videoPosterErrorMsg!="") 
				{
					$msg=$videoPosterErrorMsg;
					// delete new videos
					$newVideo=$models->bannervideo;
					$videofileDir="media/banners/videos/".$newVideo;	
					if (file_exists($videofileDir))
      				{					
      					unlink($videofileDir);//delete video
      					

						$models->bannervideo=NULL;
						$models->save(false);
					}
				} */
					if ($videoErrorMsg!="") 
					{
						$msg=$videoErrorMsg;
					}
						else
						{
							$msg=Yii::t('admin','File upload successfully');


						}

	Yii::app()->user->setFlash('success',$msg);
	$videoErrorMsg="";
  	$this->redirect(array('bannervideo'));

		
}

		

		

		$this->render('bannervideo',array(
			'model'=>$models
		));
	}


	public function actionDeletevideo($details)
	{
		
			$models = Sitesettings::model()->findByPk(1);
		$videofileDir="media/banners/videos/".$models->bannervideo;
	
		
			
		if (file_exists($videofileDir))
      {
      	 unlink($videofileDir);//delete video
		 $models->bannervideo=NULL;
			if($models->save(false))
			{
				Yii::app()->user->setFlash('success','File Deleted successfully');
				$this->redirect(array('bannervideo'));
			}
     	
     }
   	 else
      {
      	Yii::app()->user->setFlash('success','Error delete file');
			$this->redirect(array('bannervideo'));
      }
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$models=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Banners']))
		{
			$models->attributes=$_POST['Banners'];
				/*$bannerUpload = CUploadedFile::getInstance($model,'bannerimage');
				print_r($bannerUpload);
				if(!is_null($bannerUpload)) {

					$model->bannerimage = rand(0000,9999).'_'.$bannerUpload;
				} else {
					$model->bannerimage = "";
				}
				if($model->save(false)) {
					if(!is_null($bannerUpload)){
						$bannerUpload->saveAs('media/banners/'.str_replace(" ","-",$model->bannerimage));
					}
					}	echo $model->bannerimage;die;*/

			$catImage = CUploadedFile::getInstances($models,'bannerimage');
			$appImage = CUploadedFile::getInstances($models,'appbannerimage');
			list($width,$height) = getimagesize($catImage[0]->tempName);
			list($width1,$height1) = getimagesize($appImage[0]->tempName);
			if(!empty($catImage))
			{
				if($width == "1140" && $height =="325")
				{
					if(!empty($catImage)) {
						$imageName = explode(".",$catImage[0]->name);
						$models->bannerimage = rand(000,9999).'-'.Myclass::productSlug($imageName[0]).'.'.$catImage[0]->extensionName;
					}
						if(!empty($catImage)) {
							$catImage[0]->saveAs('media/banners/'. $models->bannerimage);
						}
				}
				else
				{
					Yii::app()->user->setFlash('success','Please upload the image with the specified size');
					$this->redirect(array('update','id'=>$models->id));
				}
			}
			else if(!empty($appImage))
			{
				if($width1 == "1024" && $height1 == "500" || $width1 == "2048" && $height1 == "1000")
				{
					$appImage = CUploadedFile::getInstances($models,'appbannerimage');
					if(!empty($appImage)) {
						$imageName = explode(".",$appImage[0]->name);
						$models->appbannerimage = rand(000,9999).'-'.Myclass::productSlug($imageName[0]).'.'.$appImage[0]->extensionName;
					}
						if(!empty($appImage)) {
							$appImage[0]->saveAs('media/banners/'. $models->appbannerimage);
						}

				}
				else
				{
					Yii::app()->user->setFlash('success','Please upload the image with the specified size');
					$this->redirect(array('update','id'=>$models->id));
				}
			}
			if($models->save(false))
					$this->redirect(array('view','id'=>$models->id));
		}

		$this->render('update',array(
			'model'=>$models,
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
		$siteSettings = Sitesettings::model()->find();
		$dataProvider=new CActiveDataProvider('Banners');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'sitesettings' => $sitesettings,
		));
	}

	public function actionBannerenable()
	{
		$enablestatus = $_POST['enablestatus'];
		$sitesettings = Sitesettings::model()->findByPk(1);
		$sitesettings->bannerstatus = $enablestatus;
		$sitesettings->save(false);
		
	}

	public function actionBannervideoenable()
	{
		$enablestatus = $_POST['enablestatus'];
		$sitesettings = Sitesettings::model()->findByPk(1);
		$sitesettings->bannervideoStatus = $enablestatus;
		$sitesettings->save(false);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$sitesettings = Sitesettings::model()->find();
		$model=new Banners('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Banners']))
			$model->attributes=$_GET['Banners'];

		$this->render('admin',array(
			'model'=>$model,
			'sitesettings' => $sitesettings,
		));
	}

	public function actionCheckimage()
	{
			if ( 0 < $_FILES['file']['error'] ) {
				//echo 'Error: ' . $_FILES['file']['error'] . '<br>';
			}
			$ftmp = $_FILES['file']['tmp_name'];
			$oname = $_FILES['file']['name'];
			$fname = $_FILES['file']['name'];
			$fsize = $_FILES['file']['size'];
			$ftype = $_FILES['file']['type'];
			list($width,$height) = getimagesize($ftmp);
			if($width == "1140" && $height == "325")
			{
				echo "success";
			}
			else
			{
				echo "error";
			}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Banners the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Banners::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Banners $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='banners-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
