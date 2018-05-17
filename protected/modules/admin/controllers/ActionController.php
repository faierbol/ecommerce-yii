<?php

class ActionController extends Controller
{
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
	public $layout = '//layouts/admin';

	protected function beforeAction($action)
	{
		if (!parent::beforeAction($action)) {
			return false;
		}

		$user = Yii::app()->adminUser;
		if($user->isGuest && Yii::app()->controller->action->id != 'index') {
			$this->redirect(Yii::app()->adminUser->loginUrl);
			return false;
		}elseif(isset(Yii::app()->adminUser->id) && Yii::app()->controller->action->id == 'index'){
			$this->redirect(array('/admin/action/dashboard'));
			return false;
		}

		return true;
	}

	public function actionIndex()
	{
		/* if(isset(Yii::app()->adminUser->id))
			$this->redirect(array('/admin/action/dashboard')); */

		$model=new AdminLoginForm;


		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='adminlogin-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['AdminLoginForm']))
		{
			$model->attributes=$_POST['AdminLoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(array('/admin/action/dashboard'));
		}
		// display the login form
		$this->render('index',array('model'=>$model));
	}

	public function actionDashboard(){
		/* if(!isset(Yii::app()->adminUser->id))
			$this->redirect(array('/admin')); */

		$this->layout = '//layouts/adminwithmenu';

				$this->render('dashboard');

	}

	public function actionSendpushnot(){

		if (isset($_POST['adminData']) && !empty($_POST['adminData'])){
			$message = $_POST['adminData'];

			/*$notifyTo = $userid;
			if($user_Id == $userid)
				$notifyTo = $senderid;*/
			$notifyMessage = 'sent message';
			Myclass::addLogs("admin", 0, 0, 0, 0, $notifyMessage, 0, $message);

			//echo $message; die;
			//$userDetail = Users::model()->findAll(array("condition"=>"userstatus =1 and activationStatus =1"));
			//foreach($userDetail as $userDet){
			//	$userid[] =  $userDet->userId;

			//	$criteria = new CDbCriteria;
			//	$criteria->addCondition('user_id = "'.$userid.'"');
			//	$userdevicedet = Userdevices::model()->findAll($criteria);
				$userdevicedet = Userdevices::model()->findAll();


				if(count($userdevicedet) > 0){
					foreach($userdevicedet as $userdevice){
						$deviceToken = $userdevice->deviceToken;
						$badge = $userdevice->badge;
						$badge +=1;
						$userdevice->badge = $badge;
						$userdevice->deviceToken = $deviceToken;
						$userdevice->save(false);
						if(isset($deviceToken)){
							$messages = $message;
							Myclass::pushnot($deviceToken,$messages,$badge,'admin');

						}
					}
				}
			//}
			echo $message;
		}else{
			echo "error";
		}

	}

	public function actionCleardevicetoken()
	{
		$type = $_POST['type'];
		if($type == "all")
		{
			Userdevices::model()->deleteAll();
		}
		else if($type == "ios")
		{
			Userdevices::model()->deleteAllByAttributes(array('type'=>'0'));
		}
		else if($type == "android")
		{
			Userdevices::model()->deleteAllByAttributes(array('type'=>'1'));
		}
	}

	public function actionStartfileupload()
	{
		$id = $_POST['id'];
			if ( 0 < $_FILES['file1']['error'] ) {
				//echo 'Error: ' . $_FILES['file']['error'] . '<br>';
			}
			$ftmp = $_FILES['file1']['tmp_name'];
			$ftmp1 = $_FILES['file2']['tmp_name'];
			$oname = $_FILES['file1']['name'];
			$fname = $_FILES['file1']['name'];
			$fsize = $_FILES['file1']['size'];
			$ftype = $_FILES['file1']['type'];
			$ext = strrchr($oname, '.');
			$imgpath = dirname(Yii::app()->request->scriptFile).'/certificate/';
			$devfile = "joysaleDevelopment.pem";
			$prodfile = "joysaleProduction.pem";
			chmod($imgpath.$devfile,777);
			chmod($imgpath.$prodfile,777);
			$result = move_uploaded_file($ftmp,$imgpath.$devfile);
			$result1 = move_uploaded_file($ftmp1,$imgpath.$prodfile);
			//echo $usrimg;
	}

	public function actionLogout(){

		Yii::app()->adminUser->logout(false);
		$this->redirect(array('/admin'));
	}

	public function actionSaveandroidkey()
	{
		$androidkey = $_POST['androidkey'];
		$siteSettingsModel = Sitesettings::model()->findByAttributes(array('id'=>1));
		echo $siteSettingsModel->androidkey = $androidkey;
		$siteSettingsModel->save(false);
	}
}
