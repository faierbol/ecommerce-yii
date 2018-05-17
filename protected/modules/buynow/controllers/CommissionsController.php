<?php

class CommissionsController extends Controller
{
	const ACTIVE = 1;
	const INACTIVE = 0;
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
		//'postOnly + delete', // we only allow deletion via POST request
		);
	}
	protected function beforeAction($action)
	{
		if (!parent::beforeAction($action)) {
			return false;
		}

		$user = Yii::app()->adminUser;
		if($user->isGuest) {
			$this->redirect(Yii::app()->adminUser->loginUrl);
			return false;
		}

		return true;
	}
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */


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
		$model=new Commissions;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Commissions']))
		{
			$model->attributes=$_POST['Commissions'];
			if($model->save()) {
				Yii::app()->user->setFlash('success',Yii::t('admin','Commission created successfully.'));
				$this->redirect(array('index'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
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

		if(isset($_POST['Commissions']))
		{
			$model->attributes=$_POST['Commissions'];
			if($model->save()) {
				Yii::app()->user->setFlash('success',Yii::t('admin','Commission updated successfully.'));
				$this->redirect(array('index'));
			}
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
		if(!isset($_GET['ajax'])) {
			Yii::app()->user->setFlash('success',Yii::t('admin','Commission deleted successfully.'));
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria;
		$count = Commissions::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(10);
		$pages->applyLimit($criteria);
		$criteria->order = 'date DESC';
		$commissions = Commissions::model()->findAll($criteria);
		$commissionSetting = Sitesettings::model()->findByPk(1)->commission_status;
		$this->render('index',compact('commissions','pages','commissionSetting'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Commissions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Commissions']))
		$model->attributes=$_GET['Commissions'];
		$commissionSetting = Sitesettings::model()->findByPk(1)->commission_status;
		$this->render('admin',array(
			'model'=>$model,'commissionSetting'=> $commissionSetting,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Commissions the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Commissions::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Commissions $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='commissions-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionChangeStatus($id){
		$model = $this->loadModel($id);
		if($model->status == self::ACTIVE) {
			$model->status = self::INACTIVE;
		} else {
			$model->status = self::ACTIVE;
		}
		$model->save(false);
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	public function actionStatus(){
		$model = Sitesettings::model()->findByPk(1);
		if($model->commission_status == self::ACTIVE) {
			$model->commission_status = self::INACTIVE;
			Yii::app()->user->setFlash('success',Yii::t('admin','All Commissions are disabled'));
		} else {
			$model->commission_status = self::ACTIVE;
			Yii::app()->user->setFlash('success',Yii::t('admin','All Commissions are Enabled'));
		}
		$model->save(false);
		
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
}
