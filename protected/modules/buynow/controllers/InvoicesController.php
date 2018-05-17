<?php

class InvoicesController extends Controller
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
		if(isset($id)) {
			$invoiceData = Orders::model()->with('orderitems','user','invoices')->findByPk($id);
			$shipping = Shippingaddresses::model()->findByPk($invoiceData->shippingAddress);
			$this->renderPartial('view',compact('invoiceData','shipping'),false,true);
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Invoices;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Invoices']))
		{
			$model->attributes=$_POST['Invoices'];
			if($model->save())
			$this->redirect(array('view','id'=>$model->orderId));
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

		if(isset($_POST['Invoices']))
		{
			$model->attributes=$_POST['Invoices'];
			if($model->save())
			$this->redirect(array('view','id'=>$model->orderId));
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
	public function actionIndex($status = null)
	{
		$criteria = new CDbCriteria;
		if(isset($_POST['invoiceNo'])){
			$invoiceNo = Myclass::checkPostvalue($_POST['invoiceNo']) ? $_POST['invoiceNo'] : "";
			$criteria->addCondition("invoiceNo LIKE '%{$_POST['invoiceNo']}%'");
		}
		$criteria->order = "invoiceDate DESC";
		$count = Invoices::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(10);
		$pages->applyLimit($criteria);
		$invoices = Invoices::model()->findAll($criteria);
		if(Yii::app()->request->isAjaxRequest) {
			$pages = new CPagination($count);
			$pages->setPageSize(10);
			$pages->applyLimit($criteria);
			$this->renderPartial('_view',array(
			'invoices'=>$invoices,'pages' => $pages
			));
		} else {
			$this->render('index',array(
			'invoices'=>$invoices,'pages' => $pages
			));
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Invoices('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Invoices']))
		$model->attributes=$_GET['Invoices'];
		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Invoices the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Invoices::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Invoices $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='invoices-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
