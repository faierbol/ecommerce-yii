<?php

class OrdersController extends Controller
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
		$model = Orders::model()->with('user','orderitems')->findByPk($id);
		$shipping = Shippingaddresses::model()->findByPk($model->shippingAddress);
		$trackingDetails = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		$this->render('view',array(
			'model'=>$model,'shipping' => $shipping,'trackingDetails' => $trackingDetails
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Orders;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Orders']))
		{
			$model->attributes=$_POST['Orders'];
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

		if(isset($_POST['Orders']))
		{
			$model->attributes=$_POST['Orders'];
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
		if($status == 1) {
			$criteria->addCondition("status = 'delivered'");
		}
		if($status == 2) {
			$criteria->addCondition("status = 'shipped'");
		}
		if($status == 3) {
			$criteria->addCondition("status = 'pending'");
		}
		if(isset($_POST['search'])) {
			$search = Myclass::checkPostvalue($_POST['search']) ? $_POST['search'] : "";
			$criteria->addCondition("orderId LIKE '%{$_POST['search']}%'");
			$criteria->addCondition("currency LIKE '%{$_POST['search']}%'",'OR');
		}

		/*if(isset($_POST['startDate']) && isset($_POST['endDate']))
		 {
		 //	$criteria->condition="orderDate BETWEEN UNIX_TIMESTAMP(`$_POST['startDate']`) AND UNIX_TIMESTAMP(`$_POST['endDate']')";
		 }*/
		if(isset($_POST['startDate'])) {
			$startDate = Myclass::checkPostvalue($_POST['startDate']) ? $_POST['startDate'] : "";
		 $date = new DateTime($_POST['startDate']);
		 $start = $date->getTimestamp();
		} else {
		 $date = new DateTime("01/01/1970");
		 $start = $date->getTimestamp();
		}
		if(isset($_POST['endDate'])) {
			$endDate = Myclass::checkPostvalue($_POST['endDate']) ? $_POST['endDate'] : "";
		 $date = new DateTime($_POST['endDate']);
		 $end = $date->getTimestamp();
		} else {
			$end = time();
		}
		if(isset($_POST['startDate']) && isset($_POST['endDate'])) {
		 $criteria->addBetweenCondition('orderDate',$start,$end);
		}
		$criteria->order = "orderDate DESC";
		$count = Orders::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(10);
		$pages->applyLimit($criteria);
		//	echo '<pre>'; var_dump($criteria); exit;
		$orders = Orders::model()->with('user')->findAll($criteria);
		//var_dump($orders); exit;
		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('_view',array(
			'orders'=>$orders,'pages' => $pages
			));
		} else {
			$this->render('index',array(
			'orders'=>$orders,'pages' => $pages
			));
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin($status = null)
	{
		$model=new Orders('search');

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Orders']))
		$model->attributes=$_GET['Orders'];
		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('admin',array(
			'model'=>$model,'status' => $status,
			),false,false);
			Yii::app()->end();
		} else {
			$this->render('index',array(
			'model'=>$model,'status' => $status,
			));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Orders the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Orders::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Orders $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='orders-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionScroworders($status = null)
	{
		$model=new Orders('search');
		$model->unsetAttributes();  // clear any default values
		$siteSettings = Sitesettings::model()->find();
		$commissionStatus = $siteSettings->commission_status;

		if(isset($_GET['Orders']))
		$model->attributes=$_GET['Orders'];

		if($status == 'approved') {
			//	http://localhost/joysale/admin/orders/mobileorders/status/approved?tx=0MA61440J37479330&st=Completed&amt=100.00&cc=USD&cm=demo4%40happysale.com-_-2-_--_-vOBiFgPC&item_number=38
			if(Yii::app()->request->getParam('tx')) {
				$transactionId = Yii::app()->request->getParam('tx');
				$status = Yii::app()->request->getParam('st');
				$amount =  Yii::app()->request->getParam('amt');
				$currency = Yii::app()->request->getParam('cc');
				$memo = Yii::app()->request->getParam('cm');
				$details = explode('-_-',$memo);
				$buyerEmail = $details[0];
				$orderId = $details[1];
				$itemNumber = Yii::app()->request->getParam('item_number');

				$order = Orders::model()->findByPk($orderId);
				if($status == 'Completed') {
					$order->trackPayment = 'paid';
					$order->status = 'paid';
					$order->save(false);

					$siteSettings = Sitesettings::model()->find();
					$check = Users::model()->findByPk($order->sellerId);
					$mail = new YiiMailer();
					if($siteSettings->smtpEnable == 1) {
						//$mail->IsSMTP();                         // Set mailer to use SMTP
						$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
						$mail->Host = $siteSettings->smtpHost;  // Specify main and backup server
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
						$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
						if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
						$mail->Port = $siteSettings->smtpPort;
					}
					$mail->setView('approvedmail');
					$mail->setData(array('name' => $check->name,
										'siteSettings' => $siteSettings,
										'orderId'=>$orderId));
					$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
					$mail->setTo($check->email);
					$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Amount Paid Mail'));
					$mail->send();

					$notifyMessage = 'paid the amount for your order. Order Id :'.$order->orderId;
					Myclass::addLogs("adminpayment", 0, $order->sellerId, 0, 0, $notifyMessage);

					Yii::app()->user->setFlash('success',Yii::t('admin','Payment Successfull..!'));
					$this->redirect(array('scroworders','status' => 'approved'));
				} else {
					Yii::app()->user->setFlash('warning',Yii::t('admin','Error during transaction.Please try again..!'));
					$this->redirect(array('scroworders','status' => 'delivered'));
				}

			}
		}
		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('scroworders',array(
			'model'=>$model,'status' => $status,'commissionStatus' => $commissionStatus
			),false,false);
			Yii::app()->end();
		} else {
			$this->render('mobileindex',array(
			'model'=>$model,'status' => $status,'commissionStatus' => $commissionStatus,
			));
		}
	}
	public function actionApprove($id) {
		if(!empty($id)) {
			$orderId = $id;
			$orders = Orders::model()->with('orderitems','user','seller')->findByPk($orderId);
			$productId = $orders['orderitems'][0]['productId'];
			$productModel['name'] = $orders['orderitems'][0]['itemName'];
			$productModel['currency'] = $orders['currency'];
			$productModel['productId'] = $productId;
			$productModel['sellerEmail'] = $orders['seller']['email'];
			$productModel['sellerpaypalId'] = $orders['sellerPaypalId'];
			$unitPrice = $orders['orderitems'][0]['itemunitPrice'];
			$quantity =  $orders['orderitems'][0]['itemQuantity'];

			$shippingAddressesModel = Shippingaddresses::model()->findByAttributes(array(
					'shippingaddressId'=>$orders['shippingAddress']));
			$totalCost = $orders['totalCost'];
			$shipping = $orders['totalShipping'];
			$discount = $orders['discount'];

			$siteSettings = Sitesettings::model()->find();
			$commissionStatus = $siteSettings->commission_status;
			if(!empty($discount)) {
				$productPrice = $unitPrice * $quantity;
				$productPrice = $productPrice - $discount;
			}

			if($commissionStatus == 1) {
				$criteria = new CDbCriteria;
				$criteria->condition = "minRate <= $unitPrice AND maxRate >= $unitPrice AND status = '1'";
				$criteria->order = 'id DESC';
				$comissionModel = Commissions::model()->find($criteria);
				if(!empty($comissionModel)) {
					$percentage = $comissionModel->percentage;

					if(empty($discount)) {
						$adminCommission = ($unitPrice * ($percentage/100));
						$itemPrice = $unitPrice - $adminCommission;
						$adminCommission = $adminCommission * $quantity;
					} else {
						$adminCommission = ($productPrice * ($percentage/100));
						$itemPrice = $productPrice - $adminCommission;
					}

				} else {
					$adminCommission = 0;
				}
			} else {
				$adminCommission = 0;
			}

			if(empty($discount)) {
				$finalPrice = $itemPrice * $quantity;
				$finalPrice = $finalPrice - $discount;
			} else {
				$finalPrice = $itemPrice;
			}
			$price = $finalPrice + $shipping;

			$paypalSettings = json_decode($siteSettings->paypal_settings, true);
			$productModel['paypalId'] = $paypalSettings['paypalEmailId'];


		 $this->renderPartial('mobilepayment',array('orders'=>$orders,'paypalSettings'=>$paypalSettings,'userModel'=> $orders['seller'],'productModel' => $productModel,'price' => $price,'shippingAddresses' => $shippingAddressesModel));
		}
	}

	public function actionDecline($id) {
		$order = Orders::model()->findByPk($id);
		$order->status = "shipped";
		$order->trackPayment = "pending";
		$order->save(false);
		$this->redirect(['scroworders?status=claimed']);
	}

	public function actionIpnprocess() {

	}

	public function actionCancelapprove($id)
	{

		$invoiceData = Orders::model()->with('invoices')->findByPk($id);
		$tx = $invoiceData['invoices'][0]['paymentTranxid'];
		$paytype = $invoiceData['invoices'][0]['paymentMethod'];

		$curr = $invoiceData['currency'];//echo $curr;die;
		if($paytype == "Paypal Adaptive")
		{
			$this->canceladaptive($id);
			return;
		}
		else if($paytype == "Braintree")
		{
			$this->cancelbraintree($id);
			return;
		}
		$amt = $invoiceData->totalCost;
		$siteSettings = Sitesettings::model()->find();
		$paymentsettings = json_decode($siteSettings->paypal_settings,true);
		$paymenttype = $paymentsettings['paypalType'];
		$apiuserid = $paymentsettings['paypalApiUserId'];
		$apipassword = $paymentsettings['paypalApiPassword'];
		$apisignature = $paymentsettings['paypalApiSignature'];
		$apiappid = $paymentsettings['paypalAppId'];

			$info = array(
			'USER' => $apiuserid,
		    'PWD' => $apipassword,
		    'SIGNATURE' => $apisignature,
		    'Version' => '94',
		    'METHOD' => 'RefundTransaction',
		    'TransactionId' => $tx,
		    'REFUNDTYPE' => 'Partial',
			'AMT' => $amt,
			'CurrencyCode' => $curr
					);

					//$info = json_encode($info);

		if($paymenttype == '2') {

			$apipoint = 'https://api-3t.sandbox.paypal.com/nvp';

		}
		else
		{
			$apipoint = 'https://api-3t.paypal.com/nvp';
		}
		//$apipoint = 'https://api-3t.sandbox.paypal.com/nvp';
		$apiEndpoint = $apipoint;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint );
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query ($info));
		//curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_POST, true);

		$result = curl_exec($curl);
		//$result = json_decode($result);
		parse_str( $result, $parsed_result );
		//$result = json_decode($result);

		if ($parsed_result['ACK'] == 'Success') {

			Yii::app()->user->setFlash('success',Yii::t('admin','Refund Successfully credited.'));
			//$this->redirect('/');
			$order = Orders::model()->with('orderitems')->findByPk($id);
			$order->status = "cancelled";
			$order->trackPayment = "refunded";
			$order->save(false);

			$productid = $order['orderitems'][0]['productId'];
			$productdata = Products::model()->findByPk($productid);
			$productdata->quantity = 1;
			$productdata->soldItem = 0;
			$productdata->save(false);

					$siteSettings = Sitesettings::model()->find();
					$check = Users::model()->findByPk($order->userId);
					$mail = new YiiMailer();
					if($siteSettings->smtpEnable == 1) {
						//$mail->IsSMTP();                         // Set mailer to use SMTP
						$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
						$mail->Host = $siteSettings->smtpHost;  // Specify main and backup server
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
						$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
						if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
						$mail->Port = $siteSettings->smtpPort;
					}
					$mail->setView('refundedmail');
					$mail->setData(array('name' => $check->name,
										'siteSettings' => $siteSettings,
										'orderId'=>$id));
					$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
					$mail->setTo($check->email);
					$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Amount Refunded Mail'));
					$mail->send();

					$notifyMessage = 'refunded the amount for your order. Order Id :'.$order->orderId;
					Myclass::addLogs("adminpayment", 0, $order->userId, 0, 0, $notifyMessage);
					$this->redirect(array('scroworders','status' => 'refunded'));
		}
		else
		{
			Yii::app()->user->setFlash('success',Yii::t('admin','Unfortunately Refund is not credited.'));
			$this->redirect($_SERVER['HTTP_REFERER']);
		//	$this->redirect('/admin/merchant_payment_sub_cancelled/');
		}
		echo "<pre>";print_r($result);
		//$this->redirect($_SERVER['HTTP_REFERER']);
		//echo "<pre>";print_r($result);
	}

	public function cancelbraintree($id)
	{
			Yii::setPathOfAlias('Braintree',Yii::getPathOfAlias('application.vendors.Braintree.Braintree'));

			$siteSettings = Sitesettings::model()->find();
			$brainTreeSettings = json_decode($siteSettings->braintree_settings, true);

			$paymenttype = "sandbox";
			if($brainTreeSettings['brainTreeType'] == 1){
				$paymenttype = "live";
			}
			$paymenttype = "sandbox";
			$merchantid = "wd6v9yqp6syfxwnx";//$brainTreeSettings['brainTreeMerchantId'];
			$publickey = "zbv82z73szs82hyd";//$brainTreeSettings['brainTreePublicKey'];
			$privatekey = "a88e10291a97c6ce89512a698d4109d8";//$brainTreeSettings['brainTreePrivateKey'];

			$params = array(
				"testmode"   => $paymenttype,
				"merchantid" => $merchantid,
				"publickey"  => $publickey,
				"privatekey" => $privatekey,
			);

			Braintree\Configuration::environment($paymenttype);
			Braintree\Configuration::merchantId($merchantid);
			Braintree\Configuration::publicKey($publickey);
			Braintree\Configuration::privateKey($privatekey);

			$invoiceData = Orders::model()->with('invoices')->findByPk($id);
			$tx = $invoiceData['invoices'][0]['paymentTranxid'];
			$amt = $invoiceData->totalCost;
			$result = Braintree\Transaction::refund($tx);
			if($result->success)
			{

				$order = Orders::model()->with('orderitems')->findByPk($id);
				$order->status = "cancelled";
				$order->trackPayment = "refunded";
				$order->save(false);

				$productid = $order['orderitems'][0]['productId'];
				$productdata = Products::model()->findByPk($productid);
				$productdata->quantity = 1;
				$productdata->soldItem = 0;
				$productdata->save(false);
				Yii::app()->user->setFlash('success',Yii::t('admin','Refunded successfully'));
				$this->redirect(array('scroworders','status' => 'refunded'));
			}
			else
			{
				Yii::app()->user->setFlash('success',Yii::t('admin','Amount not credited. Please login into the braintree and check transaction status.'));
				$this->redirect(array('scroworders','status' => 'cancelled'));
			}

	}

	public function canceladaptive($id)
	{

		$invoiceData = Orders::model()->with('invoices')->findByPk($id);
		$tx = $invoiceData['invoices'][0]['paymentTranxid'];
		$amt = $invoiceData->totalCost;
		$siteSettings = Sitesettings::model()->find();
		$paymentsettings = json_decode($siteSettings->paypal_settings,true);
		$paymenttype = $paymentsettings['paypalType'];
		$apiuserid = $paymentsettings['paypalApiUserId'];
		$apipassword = $paymentsettings['paypalApiPassword'];
		$apisignature = $paymentsettings['paypalApiSignature'];
		$apiappid = $paymentsettings['paypalAppId'];

			$info = array(

					"X-PAYPAL-SECURITY-USERID:".$apiuserid."",
					"X-PAYPAL-SECURITY-PASSWORD:".$apipassword."",
					"X-PAYPAL-SECURITY-SIGNATURE:".$apisignature."",
					"X-PAYPAL-APPLICATION-ID:".$apiappid."",
					"X-PAYPAL-REQUEST-DATA-FORMAT:NV",
					"X-PAYPAL-RESPONSE-DATA-FORMAT:JSON"
					);

					//$info = json_encode($info);

			$requestEnvelope = array(
					'errorLanguage' =>"en_US",
					"detailLevel" => "ReturnAll"
			);

			$packet = array(
					"requestEnvelope" => $requestEnvelope,
					"payKey" => $tx
			);

		if($paymenttype == '2') {

			$apipoint = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Refund?payKey='.$tx.'&requestEnvelope.errorLanguage=en_US';

		}
		else
		{
			$apipoint = 'https://svcs.paypal.com/AdaptivePayments/Refund';
		}
		//$apipoint = 'https://svcs.sandbox.paypal.com/AdaptivePayments/Refund';
		$apiEndpoint = $apipoint;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint );
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($curl, CURLOPT_POSTFIELDS, $packet);
		curl_setopt ( $curl , CURLOPT_SSLVERSION , CURL_SSLVERSION_TLSv1 ) ;
		curl_setopt ( $curl , CURLOPT_SSL_CIPHER_LIST , ' TLSv1 ' ) ;
		curl_setopt($curl, CURLOPT_HTTPHEADER, $info);

		$result = curl_exec($curl);
		//$result = json_decode($result);
		//parse_str( $result, $parsed_result );
		$result = json_decode($result,true);
		//print_r($result);die;
		if ($result['responseEnvelope']['ack'] == 'Success') {

			Yii::app()->user->setFlash('success',Yii::t('admin','Refund Successfully credited.'));
			//$this->redirect('/');
			$order = Orders::model()->with('orderitems')->findByPk($id);
			$order->status = "cancelled";
			$order->trackPayment = "refunded";
			$order->save(false);

			$productid = $order['orderitems'][0]['productId'];
			$productdata = Products::model()->findByPk($productid);
			$productdata->quantity = 1;
			$productdata->soldItem = 0;
			$productdata->save(false);
			$this->redirect(array('scroworders','status' => 'refunded'));
		}
		else
		{
			Yii::app()->user->setFlash('success',Yii::t('admin','Unfortunately Refund is not credited.'));
			$this->redirect($_SERVER['HTTP_REFERER']);
		//	$this->redirect('/admin/merchant_payment_sub_cancelled/');
		}
		//echo "<pre>";print_r($result);
		//$this->redirect($_SERVER['HTTP_REFERER']);
	}
}
