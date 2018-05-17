<?php

class CheckoutController extends Controller
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
	*/

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
		$allowedActions = array('adaptiveipnprocess', 'ipnprocess', 'canceled', 'success');
		$user = Yii::app()->user;
		if($user->isGuest && !in_array(Yii::app()->controller->action->id, $allowedActions)) {
			$this->redirect(array('/user/login'));
			return false;
		}

		return true;
	}



	/**
	 * Action to do the functionalities
	 * for the review order and place order
	 * in the buy now option
	 */
	public function actionRevieworder($details){
		$siteSettings = Sitesettings::model()->find();

		$paypalSettings = json_decode($siteSettings->paypal_settings, true);
		$userId = Yii::app()->user->id;

		$itemProperty = Myclass::cart_decrypt(urldecode($details), 'joy*ccart');
		$itemProperty = explode('-',$itemProperty);
		if(count($itemProperty) <= 1){
			$this->redirect(array('/'));
			//echo "<pre>";print_r($itemProperty);die;
		}
		$itemId = $itemProperty[0];
		$itemOption = $itemProperty[1];

		$itemId = Myclass::checkPostvalue($itemId) ? $itemId : "";
		$itemOption = Myclass::checkPostvalue($itemOption) ? $itemOption : "";

		$shippingAddressesModel = array();
		$shippingAddress = '';
		$shippingEnable = 0;
		$itemShippings = array();
		$itemShippingCost = array();
		$productModel = Products::model()->findByAttributes(array('productId'=>$itemId));

		if(isset($productModel->approvedStatus) && $productModel->approvedStatus == 0)
		{
			Yii::app()->user->setFlash('success','Product is waiting for admin approval');
			$this->redirect(array('/'));
		}

		$countrydata = Country::model()->findByAttributes(array('countryId'=>$productModel->shippingcountry));
		//echo "<pre>";print_r($productModel->shippings);die;
		$itemPrice = $productModel->price;
		$totalQuantity = $productModel->quantity;
		$reviewOrder = array();
		//echo "$itemOption <pre>";print_r($productModel);die;
		if ($itemOption != '0'){
			$options = json_decode($productModel->sizeOptions, true);
			$selectedOption = $options[$itemOption];
			if ($selectedOption['quantity'] < 1){
				Yii::app()->user->setFlash('success','Product has been soldout unexpectedly');
				$this->redirect($_SERVER['HTTP_REFERER']);
			}else{
				$totalQuantity = $selectedOption['quantity'];
			}
			if ($selectedOption['price'] != ''){
				$itemPrice = $selectedOption['price'];
			}
		}
		if($totalQuantity < 1 || $productModel->soldItem != 0){
			Yii::app()->user->setFlash('success','Product has been soldout unexpectedly');
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		foreach ($productModel->shippings as $itemShipping){
			$itemShippings[] = $itemShipping->countryId;
			$itemShippingCost[$itemShipping->countryId] = $itemShipping->shippingCost;
		}

		$shippingAddressesModel = Tempaddresses::model()->findAllByAttributes(array('userId'=>$userId,'countryCode'=>$productModel->shippingcountry));
		if (count($shippingAddressesModel) > 0){
			$default = Myclass::getDefaultShippingAddress($userId);
			if(empty($default)) {
				$shippingAddress = $shippingAddressesModel[0];
			} else {
				$shippingAddress = Tempaddresses::model()->findByPk($default);
			}
			$countryCode = $shippingAddress->countryCode;
			if(in_array($countryCode, $itemShippings)){
				$shippingEnable = 1;
				$shippingCost = $itemShippingCost[$countryCode];
			}

			if ($shippingEnable == 0){
				if(in_array(0, $itemShippings)){
					$shippingEnable = 1;
					$shippingCost = $itemShippingCost[0];
				} else {

				}
			}
		}
		$currency = explode('-',$productModel->currency);

		$reviewOrder['itemId'] = $productModel->productId;
		$reviewOrder['name'] = $productModel->name;
		$reviewOrder['sellername'] = $productModel->user->name;
		$reviewOrder['sellerId'] = $productModel->userId;
		$reviewOrder['price'] = $itemPrice;
		$reviewOrder['option'] = $itemOption;
		$reviewOrder['cartquantity'] = 1;
		$reviewOrder['totalquantity'] = $totalQuantity;
		$reviewOrder['shippingTime'] = $productModel->shippingTime;
		$shippingCost = $productModel->shippingCost;
		if(isset($currency[1])){
			$reviewOrder['currency'] = $currency[1];
		}else{
			$reviewOrder['currency'] = "USD";
		}
		if(!empty($shippingCost)) {
			$reviewOrder['shippingCost'] = $shippingCost;
		} else {
			$reviewOrder['shippingCost'] = 0;
		}

		$reviewOrder['productimage'] = $productModel->photos[0]->name;

		$criteria = new CDbCriteria;
		$criteria->addCondition("countryId != 0");
		$countryModel = Country::model()->findAll($criteria);

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}
		//echo "<pre>";print_r($merchantList);
		//echo "<pre>";print_r($merchantItemList);
		//echo "<pre>";print_r($cartModel);die;
		if (isset($_POST['selectedQty'])){
			$this->renderPartial('updateqtycart',array(
					'merchantList'=>$merchantList, 'merchantItemList'=>$merchantItemList,
					'cartCount'=>$cartCount,'shippingAddressesModel'=>$shippingAddressesModel,
					'shippingAddress'=>$shippingAddress,'countrydata'=>$countrydata
			));
		}else{
			$this->render('revieworder',array(
					'reviewOrder'=>$reviewOrder,'shippingAddressesModel'=>$shippingAddressesModel,
					'shippingAddress'=>$shippingAddress, 'shippingEnable'=>$shippingEnable,
					'itemShippings' => $itemShippings,'countriesList' => $countriesList,
					'itemShippingCost' => $itemShippingCost, 'paypalSettings'=>$paypalSettings,'countrydata' => $countrydata
			));
		}
	}

	public function actionCreditcardcheckout(){
		if (isset($_POST['productId'])){
			$productId = Myclass::checkPostvalue($_POST['productId']) ? $_POST['productId'] : "";
			$siteSettings = Sitesettings::model()->find();
			$userId = Yii::app()->user->id;
			$productId = $_POST['productId'];
			$productModel = Products::model()->findByAttributes(array('productId'=>$productId));
			$userModel = Users::model()->findByPk($userId);

			$productQuantity = $productModel->quantity;
			$productSoldout = $productModel->soldItem;
			if($productQuantity == 0 && $productQuantity >= $_POST['quantityChoosed'] && $productSoldout != 1){
				Yii::app()->user->setFlash('login','Sorry, Product you are looking is not available');
				echo "<script> window.location = '".Yii::app()->createAbsoluteUrl('/')."';</script>";
			}

			$shippingAddressesModel = Tempaddresses::model()->findByAttributes(array(
					'shippingaddressId'=>$_POST['shippingChoosed']));
			$countryCode = $shippingAddressesModel->countryCode;
			$shippingFlag = 0;
			$shippingPrice = 0;
			foreach ($productModel->shippings as $shippingModel){
				if ($shippingModel->countryId == $countryCode){
					$shippingPrice = $shippingModel->shippingCost;
					$shippingFlag = 1;
				}elseif ($shippingModel->countryId == 0 && $shippingFlag == 0){
					$shippingPrice = $shippingModel->shippingCost;
				}
			}

			if ($_POST['optionChoosed'] == ''){
				$itemPrice = $productModel->price;
			}else{
				$options = json_decode($productModel->sizeOptions, true);
				$optionDetails = $options[$_POST['optionChoosed']];
				if ($optionDetails['price'] != ''){
					$itemPrice = $optionDetails['price'];
				}else{
					$itemPrice = $productModel->price;
				}
			}
			$discount = 0;
			$productDetails['couponId'] = "";
			$finalPrice = $itemPrice * $_POST['quantityChoosed'];
			$finalUnitPrice = $itemPrice;
			$quantity = $_POST['quantityChoosed'];
			if (!empty($_POST['couponCode'])){
				$couponModel = Coupons::model()->findByAttributes(array('couponCode'=>$_POST['couponCode']));
				$couponType = $couponModel->couponType;
				$productDetails['couponId'] = $couponModel->id;
				if($couponType == "1") {
					$discount = $_POST['quantityChoosed'] * $couponModel->couponValue;
					$unitDiscount = $couponModel->couponValue;
					$couponModel->status = 0;
					$couponModel->save(false);
					$finalPrice = $finalPrice - $discount;
					$finalUnitPrice = $itemPrice -$unitDiscount;
				} else {
					$discount = ($itemPrice * $_POST['quantityChoosed']) * ($couponModel->couponValue / 100);
					$unitDiscount = $itemPrice * ($couponModel->couponValue / 100);
					if ($couponModel->maxAmount != "" && $couponModel->maxAmount != 0 && $couponModel->maxAmount < $discount){
						$discount = $couponModel->maxAmount;
						$quantity = 1;
						$finalPrice = $finalPrice - $discount;
						$finalUnitPrice = $finalPrice;
					}else{
						$finalPrice = $finalPrice - $discount;
						$finalUnitPrice = $itemPrice - $unitDiscount;
					}
				}
			}

			$currency = explode('-',$productModel->currency);

			/* $sellerItem[] = array(
			 "name" => $productModel->name,
			 "price" => round($sellerPrice,2),
			 "itemPrice" => $itemPrice,
			 "itemCount" => $_POST['quantityChoosed'],
			 "identifier" => $productModel->productId
				); */

			$sellerAmount = $finalPrice + $shippingPrice;

			$paypalSettings = json_decode($siteSettings->paypal_settings, true);
			$clientId = $paypalSettings['paypalCcClientId'];
			$clientSecret = $paypalSettings['paypalCcSecret'];

			Yii::setPathOfAlias('PayPal',Yii::getPathOfAlias('application.vendors.PayPal'));

			if($paypalSettings['paypalType'] == 2){
				$mode = 'sandbox';
			}elseif($paypalSettings['paypalType'] == 1){
				$mode = 'live';
			}
			$sdkConfig = array(
					"mode" => $mode
			);

			$oauthcred = new PayPal\Auth\OAuthTokenCredential($clientId, $clientSecret, $sdkConfig);

			/* $sdkConfig = array(
			 "mode" => "sandbox"
				);

			$cred = "Bearer A015iBcXpZtiq44QwZoj-BVupxYxoZDchIsNPEq22ShscJY"; */
			$cred = "Bearer ".$oauthcred->getAccessToken($sdkConfig);
			//echo "first call: $cred";
			$apiContext = new PayPal\Rest\ApiContext($oauthcred, 'Request' . time());
			$apiContext->setConfig($sdkConfig);
			$card = new PayPal\Api\CreditCard();
			/* $card->setType("visa");
				$card->setNumber("4446283280247004");
				$card->setExpireMonth("11");
				$card->setExpireYear("2018");
				$card->setFirstName("Joe");
				$card->setLastName("Shopper"); */

			/* $card->setType("mastercard");
				$card->setNumber("5500005555555559");
				$card->setExpireMonth("12");
				$card->setExpireYear("2018");
				$card->setCvv2("111");
				$card->setFirstName("Betsy");
				$card->setLastName("Buyer");*/

			$card->setType($_POST['cardType']);
			$card->setNumber($_POST['cardNumber']);
			$expiryDate = $_POST['expiryDate'];
			$expiryDate = explode('/', $expiryDate);
			$card->setExpireMonth($expiryDate[0]);
			$card->setExpireYear($expiryDate[1]);
			$card->setCvv2($_POST['cvv']);
			$card->setFirstName($_POST['firstname']);
			$card->setLastName($_POST['lastname']);

			$fi = new PayPal\Api\FundingInstrument();
			$fi->setCreditCard($card);

			$payer = new PayPal\Api\Payer();
			$payer->setPaymentMethod("credit_card");
			$payer->setFundingInstruments(array($fi));

			$item1 = new PayPal\Api\Item();
			$item1->setName($productModel->name);
			$item1->setDescription("Credit card purchase");
			$item1->setCurrency($currency[1]);
			$item1->setQuantity($quantity);
			//$item1->setTax(0.3);
			$item1->setPrice($finalUnitPrice);

			$itemList = new PayPal\Api\ItemList();
			$itemList->setItems(array($item1));

			$details = new PayPal\Api\Details();
			$details->setShipping($shippingPrice);
			//$details->setTax(1.3);
			$details->setSubtotal($finalPrice);

			$amount = new PayPal\Api\Amount();
			$amount->setCurrency($currency[1]);
			$amount->setTotal($sellerAmount);
			$amount->setDetails($details);

			$transaction = new PayPal\Api\Transaction();
			$transaction->setAmount($amount);
			$transaction->setItemList($itemList);
			//$transaction->setDescription("Payment description");
			//$transaction->setInvoiceNumber(uniqid());

			$payment = new PayPal\Api\Payment();
			$payment->setIntent("sale");
			$payment->setPayer($payer);
			$payment->setTransactions(array($transaction));

			$request = clone $payment;

			try {
				$payment->create($apiContext);
			} catch (Exception $ex) {
				//ResultPrinter::printError('Create Payment Using Credit Card. If 500 Exception, try creating a new Credit Card using <a href="https://ppmts.custhelp.com/app/answers/detail/a_id/750">Step 4, on this link</a>, and using it.', 'Payment', null, $request, $ex);
				$errorDetals = new PayPal\Api\ErrorDetails();
				//echo "<br><br>Unexpected End";
				//echo "<br><br>End {$errorDetals->getIssue()} <br><br>ex code: {$ex->getCode()} ex code: {$ex->getMessage()} ";
				//exit(1);
				//var_dump(json_decode($ex->getData()));
				echo "<script> window.location = '".Yii::app()->createAbsoluteUrl('/canceled')."';</script>";
				return;
			}

			//ResultPrinter::printResult('Create Payment Using Credit Card', 'Payment', $payment->getId(), $request, $payment);
			//echo "<br><br>End $request <br><br>Payment: $payment";
			$paymentInfo = json_decode($payment, true);
			$paymentStatus = $paymentInfo['transactions'][0]['related_resources'][0]['sale']['state'];
			if ($paymentInfo['state'] == 'approved' && $paymentStatus == 'completed'){

				$currencyCode = $currency[1];

				$itemId = $productId;

				$tempShippingModel = Tempaddresses::model()->findByPk($_POST['shippingChoosed']);

				$shippingaddressesModel = Shippingaddresses::model()->findByAttributes(array(
						'userId'=>$tempShippingModel->userId,
						'nickname'=>$tempShippingModel->nickname,
						'name'=>$tempShippingModel->name,
						'address1'=>$tempShippingModel->address1,
						'address2'=>$tempShippingModel->address2,
						'city'=>$tempShippingModel->city,
						'state'=>$tempShippingModel->state,
						'country'=>$tempShippingModel->country,
						'zipcode'=>$tempShippingModel->zipcode,
						'phone'=>$tempShippingModel->phone));
				if (!empty($shippingaddressesModel)){
					$shippingId = $shippingaddressesModel->shippingaddressId;
				}else{
					$newShippingEntry = new Shippingaddresses();
					$newShippingEntry->userId = $tempShippingModel->userId;
					$newShippingEntry->name = $tempShippingModel->name;
					$newShippingEntry->nickname = $tempShippingModel->nickname;
					$newShippingEntry->country = $tempShippingModel->country;
					$newShippingEntry->state = $tempShippingModel->state;
					$newShippingEntry->address1 = $tempShippingModel->address1;
					$newShippingEntry->address2 = $tempShippingModel->address2;
					$newShippingEntry->city = $tempShippingModel->city;
					$newShippingEntry->zipcode = $tempShippingModel->zipcode;
					$newShippingEntry->phone = $tempShippingModel->phone;
					$newShippingEntry->countryCode = $tempShippingModel->countryCode;

					$newShippingEntry->save(false);
					$shippingId =  $newShippingEntry->shippingaddressId;
				}

				$productModel = Products::model()->findByPk($itemId);

				$discountSource = $_POST['couponCode'];

				$ordersModel = new Orders();
				$ordersModel->userId = $userId;
				$ordersModel->sellerId = $productModel->userId;
				$ordersModel->totalCost = ($itemPrice * $_POST['quantityChoosed']) + $shippingPrice;
				$ordersModel->totalShipping = $shippingPrice;
				$ordersModel->discount = $discount;
				$ordersModel->discountSource = $discountSource;
				$ordersModel->orderDate = time();
				$ordersModel->shippingAddress = $shippingId;
				$ordersModel->currency = $currencyCode;
				$ordersModel->sellerPaypalId = $productModel->paypalid;
				$ordersModel->status = 'pending';
				$ordersModel->trackPayment = 'pending';

				$ordersModel->save(false);
				$orderId = $ordersModel->orderId;

				$orderItemTotalPrice = $itemPrice * $_POST['quantityChoosed'];
				$orderItemUnitPrice = $itemPrice;

				$orderItemsModel = new Orderitems();
				$orderItemsModel->orderId = $orderId;
				$orderItemsModel->productId = $itemId;
				$orderItemsModel->itemName = $productModel->name;
				$orderItemsModel->itemPrice = $orderItemTotalPrice;
				$orderItemsModel->itemSize = $_POST['optionChoosed'];
				$orderItemsModel->itemQuantity = $_POST['quantityChoosed'];
				$orderItemsModel->itemunitPrice = $orderItemUnitPrice;
				$orderItemsModel->shippingPrice = $shippingPrice;

				$orderItemsModel->save(false);

				$productModel->quantity = $productModel->quantity - $_POST['quantityChoosed'];
				if (!empty($_POST['optionChoosed']) && $_POST['optionChoosed'] != ''){
					$productOptions = json_decode($productModel->sizeOptions, true);
					$productOptions[$_POST['optionChoosed']]['quantity'] -= $_POST['quantityChoosed'];
					$productModel->sizeOptions = json_encode($productOptions);
				}

				$productModel->save(false);

				$invoiceModel = new Invoices();
				$invoiceModel->orderId = $orderId;
				$invoiceModel->invoiceNo = '';
				$invoiceModel->invoiceDate = time();
				$invoiceModel->invoiceStatus = $paymentStatus;
				$invoiceModel->paymentMethod = 'Paypal Credit Card Payment';
				$invoiceModel->paymentTranxid = $paymentInfo['id'];

				$invoiceModel->save(false);

				$invoiceNo = "INV".$invoiceModel->invoiceId.$userId;

				$invoiceModel->invoiceNo = $invoiceNo;
				$invoiceModel->save(false);

				$sellerEmail = $productModel->user->email;
				$sellerName = $productModel->user->name;

				$custom[2] = $_POST['optionChoosed'];

				$keyarray['item_name'] = $productModel->name;
				$keyarray['quantity'] = $_POST['quantityChoosed'];
				$keyarray['mc_gross'] = $finalPrice;
				$keyarray['mc_currency'] = $currencyCode;

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
				$mail->setView('sellerorderintimation');
				$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
						'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
						'tempShippingModel'=>$tempShippingModel));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($sellerEmail);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Seller Order Information'));

				$userid = $productModel->user->userId;
				$criteria = new CDbCriteria;
				$criteria->addCondition('user_id = "'.$userid.'"');
				$userdevicedet = Userdevices::model()->findAll($criteria);

				if(count($userdevicedet) > 0){
					foreach($userdevicedet as $userdevice){
						$deviceToken = $userdevice->deviceToken;
						$lang = $userdevice->lang_type;
						$badge = $userdevice->badge;
						$badge +=1;
						$userdevice->badge = $badge;
						$userdevice->deviceToken = $deviceToken;
						$userdevice->save(false);
						if(isset($deviceToken)){
							$msg = Myclass::push_lang($lang);
							$messages =  $userModel->username." ".Yii::t('app','placed an order in your shop, order id :')." ".$orderId;
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
				}


				if ($mail->send()) {
					//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
					//$this->redirect(array('user/login'));
					//echo "email sent";die;
				} else {
					//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
					//echo $mail->getError();die;
				}

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
				$mail->setView('buyerorderintimation');
				$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
						'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
						'tempShippingModel'=>$tempShippingModel));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($userModel->email);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Buyer Order Information'));
				if ($mail->send()) {
					//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
					//$this->redirect(array('user/login'));
					//echo "email sent";die;
				} else {
					//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
					//echo $mail->getError();die;
				}
				echo "<script> window.location = '".Yii::app()->createAbsoluteUrl('/success')."';</script>";

			}else{
				echo "<script> window.location = '".Yii::app()->createAbsoluteUrl('/canceled')."';</script>";
			}
		}else{
			echo "false";
		}
	}

	public function actionPlaceorder(){
		if (isset($_POST['productId'])){
			$productId = Myclass::checkPostvalue($_POST['productId']) ? $_POST['productId'] : "";
			$userId = Yii::app()->user->id;
			$productId = $_POST['productId'];
			$productModel = Products::model()->findByAttributes(array('productId'=>$productId));
			$userModel = Users::model()->findByPk($userId);

			$productQuantity = $productModel->quantity;
			$productSoldout = $productModel->soldItem;
			//echo "productQuantity: $productQuantity, ProductSoldout: $productSoldout";die;
			if($productQuantity == 0 || $productQuantity > $_POST['quantityChoosed'] || $productSoldout != 0){
				Yii::app()->user->setFlash('login','Sorry, Product you are looking is not available');
				echo "<script> window.location = '".Yii::app()->createAbsoluteUrl('/')."';</script>";
				//echo "productQuantity: $productQuantity, ProductSoldout: $productSoldout";
				return;
			}

			$shippingAddressesModel = Tempaddresses::model()->findByAttributes(array(
					'shippingaddressId'=>$_POST['shippingChoosed']));
			$countryCode = $shippingAddressesModel->countryCode;
			$shippingFlag = 0;
			$shippingPrice = 0;
			/* foreach ($productModel->shippings as $shippingModel){
				if ($shippingModel->countryId == $countryCode){
					$shippingPrice = $shippingModel->shippingCost;
					$shippingFlag = 1;
				}elseif ($shippingModel->countryId == 0 && $shippingFlag == 0){
					$shippingPrice = $shippingModel->shippingCost;
				}
			} */
			$shippingPrice = $productModel->shippingCost;

			$siteSettings = Sitesettings::model()->find();

			$commissionStatus = $siteSettings->commission_status;
			$paypalSettings = json_decode($siteSettings->paypal_settings, true);
			$sitePaymentMode = json_decode($siteSettings->sitepaymentmodes, true);
			if ($_POST['optionChoosed'] == ''){
				$itemPrice = $productModel->price;
			}else{
				$options = json_decode($productModel->sizeOptions, true);
				$optionDetails = $options[$_POST['optionChoosed']];
				if ($optionDetails['price'] != ''){
					$itemPrice = $optionDetails['price'];
				}else{
					$itemPrice = $productModel->price;
				}
			}
			$discount = 0;
			$productDetails['couponId'] = "";
			$finalPrice = $itemPrice * $_POST['quantityChoosed'];
			if (!empty($_POST['couponCode'])){
				$couponModel = Coupons::model()->findByAttributes(array('couponCode'=>$_POST['couponCode']));
				$couponType = $couponModel->couponType;
				$productDetails['couponId'] = $couponModel->id;
				if($couponType == "1") {
					$discount = $_POST['quantityChoosed'] * $couponModel->couponValue;
				} else {
					$discount = ($itemPrice * $_POST['quantityChoosed']) * ($couponModel->couponValue / 100);
					if ($couponModel->maxAmount != "" && $couponModel->maxAmount != 0 && $couponModel->maxAmount < $discount){
						$discount = $couponModel->maxAmount;
					}
				}
				$finalPrice = $finalPrice - $discount;
			}
			if ($commissionStatus == 1){
				$criteria = new CDbCriteria;
				$criteria->condition = "minRate <= $finalPrice AND maxRate >= $finalPrice AND status = '1'";
				$comissionModel = Commissions::model()->find($criteria);

				//echo "$itemPrice <pre>";print_r($comissionModel);die;

				if (empty($comissionModel)){
					$commissionStatus = 0;
				}else{
					$commissionPercentage = $comissionModel->percentage;
				}
			}

			if ($commissionStatus == 1 && $sitePaymentMode['scrowPaymentMode'] == 0){
				if($paypalSettings['paypalType'] == 2){
					$paypalurl = "https://sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=";
					$apiurl = "https://svcs.sandbox.paypal.com/AdaptivePayments/";
				}elseif($paypalSettings['paypalType'] == 1){
					$paypalurl = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=";
					$apiurl = "https://svcs.paypal.com/AdaptivePayments/";
				}

				$adminItem = array();
				$sellerItem = array();
				$adminCommission = 0;
				$sellerAmount = 0;

				$totalPrice = $itemPrice * $_POST['quantityChoosed'];
				if ($discount != 0){
					$totalPrice -= $discount;
				}

				$adminPrice = (floatval($totalPrice) * $commissionPercentage) / 100;
				$sellerPrice = floatval($totalPrice) - $adminPrice;
				$adminCommission = $adminPrice;
				$sellerAmount = $sellerPrice;
				$currency = explode('-',$productModel->currency);

				$adminItem[] = array(
						"name" => $productModel->name,
						"price" => round($adminPrice,2),
						"itemPrice" => $itemPrice,
						"itemCount" => $_POST['quantityChoosed'],
						"identifier" => $productModel->productId
				);
				$sellerItem[] = array(
						"name" => $productModel->name,
						"price" => round($sellerPrice,2),
						"itemPrice" => $itemPrice,
						"itemCount" => $_POST['quantityChoosed'],
						"identifier" => $productModel->productId
				);

				$sellerAmount += $shippingPrice;
				$requestEnvelope = array(
						'errorLanguage' =>"en_US",
						"detailLevel" => "ReturnAll"
				);

				$adminAmount = round($adminCommission,2);
				$sellerAmount = round($sellerAmount,2);
				$createPacket = array(
						"actionType" => "PAY",
						"currencyCode" => $currency[1],
						"receiverList" => array(
								"receiver" => array(
										array (
												"amount" => "$adminAmount",
												"email" => $paypalSettings['paypalEmailId'],
												'Primary' => 'false'
										),
										array(
												"amount" => "$sellerAmount",
												"email" => $productModel->paypalid,
												'Primary' => 'true'
										),
								),
						),
						"returnUrl" => Yii::app()->createAbsoluteUrl('/success'),
						"cancelUrl" => Yii::app()->createAbsoluteUrl('/canceled'),
						"ipnNotificationUrl" => Yii::app()->createAbsoluteUrl('/adaptiveipnprocess'),//'http://dev.hitasoft.com/new/success.php',
						"memo" => $userModel->email."-_-".$_POST['shippingChoosed']."-_-".$_POST['optionChoosed']."-_-".$productDetails['couponId'],
						"requestEnvelope" => $requestEnvelope
				);

				$result = $this->adaptiveCall($apiurl, $createPacket, "Pay", $paypalSettings);
				//echo json_encode($result)."<pre>".json_encode($createPacket);
				if ($result['responseEnvelope']['ack'] == 'Success') {
					$payKey =  $result['payKey'] ;
					$payDetails = array(
							"requestEnvelope" => $requestEnvelope,
							"payKey" => $payKey,
							"receiverOptions" => array(
									array(
											"receiver" => array("email"=>$paypalSettings['paypalEmailId']),
											"invoiceData" => array(
													"item" => $adminItem
											)
									),
									array(
											"receiver" => array("email"=>$productModel->paypalid),
											"invoiceData" => array(
													"item" => $sellerItem,
													"totalShipping" => $shippingPrice
											)
									)
							),

					);
					//echo "<pre>";print_r($payDetails);die;
					$result = $this->adaptiveCall($apiurl, $payDetails, "SetPaymentOptions", $paypalSettings);
					//echo "<pre>";print_r($result);
					if ($result['responseEnvelope']['ack'] == 'Success') {
						$packet = array(
								"requestEnvelope" => $requestEnvelope,
								"payKey" => $payKey
						);
						$result = $this->adaptiveCall($apiurl, $packet, "GetPaymentOptions", $paypalSettings);


						//echo "<pre>";print_r($result);die;
						if ($result['responseEnvelope']['ack'] == 'Success') {
							echo "<script> window.location = '".$paypalurl.$payKey."';</script>";
						}else{
							echo "get payment option  problem";
						}
					}else{
						echo "payment settings problem";
					}
				}else{
					echo "create packet problem";
				}
			} else {
				$productDetails['shippingId'] = $_POST['shippingChoosed'];
				$productDetails['quantity'] = $_POST['quantityChoosed'];
				$productDetails['options'] = $_POST['optionChoosed'];
				$productDetails['shippingPrice'] = $shippingPrice;
				$productDetails['discount'] = $discount;

				$this->renderPartial('placeorder',array('productModel'=>$productModel, 'productDetails'=>
						$productDetails, 'paypalSettings'=>$paypalSettings, 'userModel'=>$userModel,
						'sitePaymentMode'=>$sitePaymentMode));
			}
		}else{
			echo "false";
		}
	}

	public function actionSuccess() {
		$this->render('success', array());
	}

	public function actionCanceled() {
		$this->render('canceled', array());
	}

	public function adaptiveCall ($apiurl, $data, $action, $paypalAdaptive) {
		$data = json_encode($data);
		$header = array(
				"X-PAYPAL-SECURITY-USERID:".$paypalAdaptive['paypalApiUserId'],
				"X-PAYPAL-SECURITY-PASSWORD:".$paypalAdaptive['paypalApiPassword'],
				"X-PAYPAL-SECURITY-SIGNATURE:".$paypalAdaptive['paypalApiSignature'],
				"X-PAYPAL-APPLICATION-ID:".$paypalAdaptive['paypalAppId'],
				"X-PAYPAL-REQUEST-DATA-FORMAT:JSON",
				"X-PAYPAL-RESPONSE-DATA-FORMAT:JSON"
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiurl.$action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		/*curl_setopt($ch, CURLOPT_SSLVERSION, 4);
		 curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');*/
		curl_setopt ( $ch , CURLOPT_SSLVERSION , CURL_SSLVERSION_TLSv1 ) ;
		curl_setopt ( $ch , CURLOPT_SSL_CIPHER_LIST , ' TLSv1 ' ) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		//curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		/* $info = curl_getinfo($ch);

		echo "<pre>";print_r($info);
		if ($result){
		echo $result; */
		if(!curl_errno($ch))
		{
			return json_decode($result,true);
		}else{
			return curl_errno($ch)." - ".curl_error($ch);
		}
	}

	public function decodePayPalIPN($raw_post) {
		if (empty($raw_post)) {
			return array();
		} // else:
		$post = array();
		$pairs = explode('&', $raw_post);
		foreach ($pairs as $pair) {
			list($key, $value) = explode('=', $pair, 2);
			$key = urldecode($key);
			$value = urldecode($value);
			// This is look for a key as simple as 'return_url' or as complex as 'somekey[x].property'
			preg_match('/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts);
			switch (count($key_parts)) {
				case 4:
					// Original key format: somekey[x].property
					// Converting to $post[somekey][x][property]
					if (!isset($post[$key_parts[1]])) {
						$post[$key_parts[1]] = array($key_parts[2] => array($key_parts[3] => $value));
					} else if (!isset($post[$key_parts[1]][$key_parts[2]])) {
						$post[$key_parts[1]][$key_parts[2]] = array($key_parts[3] => $value);
					} else {
						$post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
					}
					break;
				case 3:
					// Original key format: somekey[x]
					// Converting to $post[somkey][x]
					if (!isset($post[$key_parts[1]])) {
						$post[$key_parts[1]] = array();
					}
					$post[$key_parts[1]][$key_parts[2]] = $value;
					break;
				default:
					// No special format
					$post[$key] = $value;
					break;
			}//switch
		}//foreach

		return $post;
	}

	public function actionAdaptiveipnprocess(){
		$postFields = 'cmd=_notify-validate';
		$siteSettings = Sitesettings::model()->find();

		$commissionStatus = $siteSettings->commission_status;
		$paypalSettings = json_decode($siteSettings->paypal_settings, true);

		if($paypalSettings['paypalType'] == 2){
			$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}elseif($paypalSettings['paypalType'] == 1){
			$url = 'https://www.paypal.com/cgi-bin/webscr';
		}

		$raw_post = file_get_contents("php://input");
		$raw_post = $this->decodePayPalIPN($raw_post);

		foreach($raw_post as $key => $value)
		{
			if ($key == 'transaction') {
				$transactionCount = count($raw_post['transaction']);
				foreach ($raw_post['transaction'] as $map => $transaction) {
					foreach ($transaction as $tranName => $tranValue){
						$postFields .= "&transaction[".$map."].".$tranName."=".urlencode($tranValue);
						$keyarray['transaction'][$map][urldecode($tranName)] = urldecode($tranValue);
					}
				}
			}else {
				$postFields .= "&$key=".urlencode($value);
				$keyarray[urldecode($key)] = urldecode($value);
			}
		}

		$ch = curl_init();

		curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postFields
		));

		$result = curl_exec($ch);
		curl_close($ch);

		$invoiceCheck = Invoices::model()->findByAttributes(array('paymentTranxid'=>$keyarray['pay_key']));

		if ($result == 'VERIFIED' && $keyarray['status'] == 'COMPLETED' && empty($invoiceCheck)) {
			if($paypalSettings['paypalType'] == 2){
				$paypalurl = "https://sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=";
				$apiurl = "https://svcs.sandbox.paypal.com/AdaptivePayments/";
			}elseif($paypalSettings['paypalType'] == 1){
				$paypalurl = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=";
				$apiurl = "https://svcs.paypal.com/AdaptivePayments/";
			}
			$requestEnvelope = array(
					'errorLanguage' =>"en_US",
					"detailLevel" => "ReturnAll"
			);
			$custom = explode('-_-', $keyarray['memo']);
			$userModel = Users::model()->findByAttributes(array('email'=>$custom[0]));
			$userId = $userModel->userId;
			$usernameforcust = $userModel->name;
			$payKey = $keyarray['pay_key'];
			$packet = array(
					"requestEnvelope" => $requestEnvelope,
					"payKey" => $payKey
			);

			while(1){
				$result = $this->adaptiveCall($apiurl, $packet, "GetPaymentOptions", $paypalSettings);
				$resultCount = count($result);
				if($resultCount > 0)
					break;
			}

			$currencyCode = explode(' ',$keyarray['transaction'][0]['amount']);

			$tempShippingModel = Tempaddresses::model()->findByPk($custom[1]);

			$shippingaddressesModel = Shippingaddresses::model()->findByAttributes(array(
					'userId'=>$tempShippingModel->userId,
					'nickname'=>$tempShippingModel->nickname,
					'name'=>$tempShippingModel->name,
					'address1'=>$tempShippingModel->address1,
					'address2'=>$tempShippingModel->address2,
					'city'=>$tempShippingModel->city,
					'state'=>$tempShippingModel->state,
					'country'=>$tempShippingModel->country,
					'zipcode'=>$tempShippingModel->zipcode,
					'phone'=>$tempShippingModel->phone));
			if (!empty($shippingaddressesModel)){
				$shippingId = $shippingaddressesModel->shippingaddressId;
			}else{
				$newShippingEntry = new Shippingaddresses();
				$newShippingEntry->userId = $tempShippingModel->userId;
				$newShippingEntry->name = $tempShippingModel->name;
				$newShippingEntry->nickname = $tempShippingModel->nickname;
				$newShippingEntry->country = $tempShippingModel->country;
				$newShippingEntry->state = $tempShippingModel->state;
				$newShippingEntry->address1 = $tempShippingModel->address1;
				$newShippingEntry->address2 = $tempShippingModel->address2;
				$newShippingEntry->city = $tempShippingModel->city;
				$newShippingEntry->zipcode = $tempShippingModel->zipcode;
				$newShippingEntry->phone = $tempShippingModel->phone;
				$newShippingEntry->countryCode = $tempShippingModel->countryCode;
				$newShippingEntry->save(false);
				/* if ($newShippingEntry->save()){
				 $siteSettings->currency_priority = "Save success";
				 $siteSettings->save();
				 }else{
				 $siteSettings->currency_priority = "Save error";
				 $siteSettings->save();
				 } */
				$shippingId =  $newShippingEntry->shippingaddressId;
			}
			$totalCost = 0;
			foreach ($result['receiverOptions']['1']['invoiceData']['item'] as $key => $item) {
				$totalCost += $item['itemPrice'] * $item['itemCount'];
				$cartItemPrice = $item['itemPrice'] * $item['itemCount'];
				$cartItemUnitPrice = $item['itemPrice'];
				$cartItemQuantity = $item['itemCount'];
				$cartItemId = $item['identifier'];
				$cartItemName = $item['name'];
				$keyarray['item_name'] = $item['name'];
				$keyarray['quantity'] = $item['itemCount'];
				$keyarray['mc_gross'] = $totalCost;
				$keyarray['mc_currency'] = $currencyCode[0];
			}
			$totShipping = $result['receiverOptions']['1']['invoiceData']['totalShipping'];
			$cartCount = count($result['receiverOptions']['1']['invoiceData']['item']);

			$totalCost += $totShipping;

			$productModel = Products::model()->findByPk($cartItemId);

			$discount = 0;
			$discountSource = "";
			if ($custom[3] != ""){
				$itemPrice = $cartItemUnitPrice;
				$couponModel = Coupons::model()->findByPk($custom[3]);
				$couponType = $couponModel->couponType;
				$discountSource = $couponModel->couponCode;
				if($couponType == "1") {
					$discount = $cartItemQuantity * $couponModel->couponValue;
					$couponModel->status = 0;
					$couponModel->save(false);
				} else {
					$discount = ($itemPrice * $cartItemQuantity) * ($couponModel->couponValue / 100);
					if ($couponModel->maxAmount != "" && $couponModel->maxAmount < $discount){
						$discount = $couponModel->maxAmount;
					}
				}
			}

			$ordersModel = new Orders();
			$ordersModel->userId = $userId;
			$ordersModel->sellerId = $productModel->userId;
			$ordersModel->totalCost = $totalCost;
			$ordersModel->totalShipping = $totShipping;
			$ordersModel->discount = $discount;
			$ordersModel->discountSource = $discountSource;
			$ordersModel->orderDate = time();
			$ordersModel->shippingAddress = $shippingId;
			$ordersModel->currency = $currencyCode[0];
			$ordersModel->sellerPaypalId = $productModel->paypalid;
			$ordersModel->status = 'pending';

			$ordersModel->save(false);
			$orderId = $ordersModel->orderId;

			$orderItemTotalPrice = $totalCost - $totShipping;
			$orderItemUnitPrice = $orderItemTotalPrice / $cartItemQuantity;

			$orderItemsModel = new Orderitems();
			$orderItemsModel->orderId = $orderId;
			$orderItemsModel->productId = $cartItemId;
			$orderItemsModel->itemName = $cartItemName;
			$orderItemsModel->itemPrice = $orderItemTotalPrice;
			$orderItemsModel->itemSize = $custom[2];
			$orderItemsModel->itemQuantity = $cartItemQuantity;
			$orderItemsModel->itemunitPrice = $orderItemUnitPrice;
			$orderItemsModel->shippingPrice = $totShipping;

			$orderItemsModel->save(false);

			$productModel->quantity = $productModel->quantity - $cartItemQuantity;
			if (!empty($custom[2]) && $custom[2] != ''){
				$productOptions = json_decode($productModel->sizeOptions, true);
				$productOptions[$custom[2]]['quantity'] -= $cartItemQuantity;
				$productModel->sizeOptions = json_encode($productOptions);
			}
			$productModel->soldItem = 1;
			if($productModel->promotionType != 3){
				$promotionCriteria = new CDbCriteria();
				$promotionCriteria->addCondition("productId = $productModel->productId");
				$promotionCriteria->addCondition("status LIKE 'live'");
				$promotionModel = Promotiontransaction::model()->find($promotionCriteria);

				if(!empty($promotionModel)){
					if($promotionModel->promotionName != 'urgent'){
						$previousCriteria = new CDbCriteria();
						$previousCriteria->addCondition("productId = $promotionModel->productId");
						$previousCriteria->addCondition("status LIKE 'Expired'");
						$previousPromotion = Promotiontransaction::model()->find($previousCriteria);
						if(!empty($previousPromotion)){
							$previousPromotion->status = "Canceled";
							$previousPromotion->save(false);
						}
					}
					$promotionModel->status = "Expired";
					$promotionModel->save(false);
				}
			}
			$productModel->promotionType = 3;
			$productModel->save(false);

			$invoiceModel = new Invoices();
			$invoiceModel->orderId = $orderId;
			$invoiceModel->invoiceNo = '';
			$invoiceModel->invoiceDate = time();
			$invoiceModel->invoiceStatus = $keyarray['status'];
			$invoiceModel->paymentMethod = 'Paypal Adaptive';
			$invoiceModel->paymentTranxid = $keyarray['pay_key'];

			$invoiceModel->save(false);

			$invoiceNo = "INV".$invoiceModel->invoiceId.$userId;

			$invoiceModel->invoiceNo = $invoiceNo;
			$invoiceModel->save(false);

			$sellerEmail = $productModel->user->email;
			$sellerName = $productModel->user->name;

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
			$mail->setView('sellerorderintimation');
			$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
					'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
					'tempShippingModel'=>$tempShippingModel));
			$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
			$mail->setTo($sellerEmail);
			$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Seller Order Information'));

			$userid = $productModel->user->userId;
			$criteria = new CDbCriteria;
			$criteria->addCondition('user_id = "'.$userid.'"');
			$userdevicedet = Userdevices::model()->findAll($criteria);

			if(count($userdevicedet) > 0){
				foreach($userdevicedet as $userdevice){
					$deviceToken = $userdevice->deviceToken;
					$lang = $userdevice->lang_type;
					$badge = $userdevice->badge;
					$badge +=1;
					$userdevice->badge = $badge;
					$userdevice->deviceToken = $deviceToken;
					$userdevice->save(false);
					if(isset($deviceToken)){
						$msg = Myclass::push_lang($lang);
						$messages =  $userModel->username." ".Yii::t('app','placed an order in your shop, order id :')." ".$orderId;
						Myclass::pushnot($deviceToken,$messages,$badge);
					}
				}
			}


			if ($mail->send()) {
				//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
				//$this->redirect(array('user/login'));
				//echo "email sent";die;
			} else {
				//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
				//echo $mail->getError();die;
			}

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
			$mail->setView('buyerorderintimation');
			$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
					'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
					'tempShippingModel'=>$tempShippingModel));
			$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
			$mail->setTo($userModel->email);
			$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Buyer Order Information'));
			if ($mail->send()) {
				//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
				//$this->redirect(array('user/login'));
				//echo "email sent";die;
			} else {
				//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
				//echo $mail->getError();die;
			}
		}
	}

	public function actionIpnprocess() {
		$postFields = 'cmd=_notify-validate';
		$siteSettings = Sitesettings::model()->find();

		$commissionStatus = $siteSettings->commission_status;
		$paypalSettings = json_decode($siteSettings->paypal_settings, true);

		if($paypalSettings['paypalType'] == 2){
			$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}elseif($paypalSettings['paypalType'] == 1){
			$url = 'https://www.paypal.com/cgi-bin/webscr';
		}

		foreach($_POST as $key => $value)
		{
			$postFields .= "&$key=".urlencode(stripslashes($value));
			$keyarray[urldecode($key)] = urldecode($value);
		}

		$ch = curl_init();

		curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postFields
		));

		$result = curl_exec($ch);
		curl_close($ch);

		$invoiceCheck = Invoices::model()->findByAttributes(array('paymentTranxid'=>$keyarray['txn_id']));

		if ($result == 'VERIFIED' && $keyarray['payment_status'] == 'Completed' && empty($invoiceCheck)) {
			if ($keyarray['custom'] != ''){
				$custom = explode('-_-', $keyarray['custom']);
			}else{
				$custom = explode('-_-', $keyarray['memo']);
			}

			$userModel = Users::model()->findByAttributes(array('email'=>$custom[0]));
			$userId = $userModel->userId;

			$currencyCode = $keyarray['mc_currency'];
			/* $forexrateModel = $this->Forexrates->find('first',
			 array('conditions'=>array('currency_code' => $currencyCode)));
			$forexRate = $forexrateModel['Forexrates']['price']; */

			if(isset($keyarray['item_number']) && $keyarray['item_number']!=''){
				$itemId = $keyarray['item_number'];
				$i = '';
			}else{
				$i = 1;
				$keyarray['iteration'] = 1;
				$mobi_value = explode('-_-', $keyarray['item_name'.$i]);
				$itemId = $mobi_value[1];
				$keyarray['item_name'.$i] = $mobi_value[0];
			}
			$tempShippingModel = Tempaddresses::model()->findByPk($custom[1]);

			$shippingaddressesModel = Shippingaddresses::model()->findByAttributes(array(
					'userId'=>$tempShippingModel->userId,
					'nickname'=>$tempShippingModel->nickname,
					'name'=>$tempShippingModel->name,
					'address1'=>$tempShippingModel->address1,
					'address2'=>$tempShippingModel->address2,
					'city'=>$tempShippingModel->city,
					'state'=>$tempShippingModel->state,
					'country'=>$tempShippingModel->country,
					'zipcode'=>$tempShippingModel->zipcode,
					'phone'=>$tempShippingModel->phone));

			if (!empty($shippingaddressesModel)){
				$shippingId = $shippingaddressesModel->shippingaddressId;
			}else{
				$newShippingEntry = new Shippingaddresses();
				$newShippingEntry->userId = $tempShippingModel->userId;
				$newShippingEntry->name = $tempShippingModel->name;
				$newShippingEntry->nickname = $tempShippingModel->nickname;
				$newShippingEntry->country = $tempShippingModel->country;
				$newShippingEntry->state = $tempShippingModel->state;
				$newShippingEntry->address1 = $tempShippingModel->address1;
				$newShippingEntry->address2 = $tempShippingModel->address2;
				$newShippingEntry->city = $tempShippingModel->city;
				$newShippingEntry->zipcode = $tempShippingModel->zipcode;
				$newShippingEntry->phone = $tempShippingModel->phone;
				$newShippingEntry->countryCode = $tempShippingModel->countryCode;

				$newShippingEntry->save(false);

				$shippingId = $newShippingEntry->shippingaddressId;
			}

			$productModel = Products::model()->findByPk($itemId);

			$discount = 0;
			$discountSource = "";
			if ($custom[3] != ""){
				$keyarray['mc_gross'] += $keyarray['discount'];
				$itemPrice = $keyarray['mc_gross'] - $keyarray['shipping'];
				$couponModel = Coupons::model()->findByPk($custom[3]);
				$couponType = $couponModel->couponType;
				$productDetails['couponId'] = $couponModel->id;
				$discountSource = $couponModel->couponCode;
				if($couponType == "1") {
					$discount = $keyarray['quantity'.$i] * $couponModel->couponValue;
					$couponModel->status = 0;
					$couponModel->save(false);
				} else {
					$discount = ($itemPrice * $keyarray['quantity'.$i]) * ($couponModel->couponValue / 100);
					if ($couponModel->maxAmount != "" && $couponModel->maxAmount < $discount){
						$discount = $couponModel->maxAmount;
					}
				}
			}

			$keyarray['shipping'] = isset($keyarray['shipping']) ? $keyarray['shipping'] : $keyarray['mc_shipping'];

			$ordersModel = new Orders();
			$ordersModel->userId = $userId;
			$ordersModel->sellerId = $productModel->userId;
			$ordersModel->totalCost = $keyarray['mc_gross'];
			$ordersModel->totalShipping = $keyarray['shipping'];
			$ordersModel->discount = $discount;
			$ordersModel->discountSource = $discountSource;
			$ordersModel->orderDate = time();
			$ordersModel->shippingAddress = $shippingId;
			$ordersModel->currency = $currencyCode;
			$ordersModel->sellerPaypalId = $productModel->paypalid;
			$ordersModel->status = 'pending';
			$ordersModel->trackPayment = 'pending';

			$ordersModel->save(false);
			$orderId = $ordersModel->orderId;

			$orderItemTotalPrice = $keyarray['mc_gross'] - $keyarray['shipping'];
			$orderItemUnitPrice = $orderItemTotalPrice / $keyarray['quantity'.$i];

			$orderItemsModel = new Orderitems();
			$orderItemsModel->orderId = $orderId;
			$orderItemsModel->productId = $itemId;
			$orderItemsModel->itemName = $productModel->name;
			$orderItemsModel->itemPrice = $orderItemTotalPrice;
			$orderItemsModel->itemSize = $custom[2];
			$orderItemsModel->itemQuantity = $keyarray['quantity'.$i];
			$orderItemsModel->itemunitPrice = $orderItemUnitPrice;
			$orderItemsModel->shippingPrice = $keyarray['shipping'];

			$orderItemsModel->save(false);

			$productModel->quantity = $productModel->quantity - $keyarray['quantity'.$i];
			if (!empty($custom[2]) && $custom[2] != ''){
				$productOptions = json_decode($productModel->sizeOptions, true);
				$productOptions[$custom[2]]['quantity'] -= $keyarray['quantity'.$i];
				$productModel->sizeOptions = json_encode($productOptions);
			}
			$productModel->soldItem = 1;
			if($productModel->promotionType != 3){
				$promotionCriteria = new CDbCriteria();
				$promotionCriteria->addCondition("productId = $productModel->productId");
				$promotionCriteria->addCondition("status LIKE 'live'");
				$promotionModel = Promotiontransaction::model()->find($promotionCriteria);

				if(!empty($promotionModel)){
					if($promotionModel->promotionName != 'urgent'){
						$previousCriteria = new CDbCriteria();
						$previousCriteria->addCondition("productId = $promotionModel->productId");
						$previousCriteria->addCondition("status LIKE 'Expired'");
						$previousPromotion = Promotiontransaction::model()->find($previousCriteria);
						if(!empty($previousPromotion)){
							$previousPromotion->status = "Canceled";
							$previousPromotion->save(false);
						}
					}
					$promotionModel->status = "Expired";
					$promotionModel->save(false);
				}
			}
			$productModel->promotionType = 3;
			$productModel->save(false);

			$invoiceModel = new Invoices();
			$invoiceModel->orderId = $orderId;
			$invoiceModel->invoiceNo = '';
			$invoiceModel->invoiceDate = time();
			$invoiceModel->invoiceStatus = $keyarray['payment_status'];
			$invoiceModel->paymentMethod = 'Paypal';
			$invoiceModel->paymentTranxid = $keyarray['txn_id'];

			$invoiceModel->save(false);

			$invoiceNo = "INV".$invoiceModel->invoiceId.$userId;

			$invoiceModel->invoiceNo = $invoiceNo;
			$invoiceModel->save(false);

			$sellerId = $productModel->userId;
			$sellerData = Users::model()->findByPk($sellerId);
			$sellerEmail = $sellerData->email;
			$sellerName = $sellerData->name;

			$notifyMessage = 'placed an order in your shop, Order Id : '.$orderId;
			Myclass::addLogs("order", $userModel->userId, $productModel->userId, $productModel->productId, $productModel->productId, $notifyMessage);

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
			$mail->setView('sellerorderintimation');
			$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
					'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
					'tempShippingModel'=>$tempShippingModel));
			$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
			$mail->setTo($sellerEmail);
			$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Seller Order Information'));
			$mail->send();

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
			$mail->setView('buyerorderintimation');
			$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
					'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
					'tempShippingModel'=>$tempShippingModel));
			$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
			$mail->setTo($userModel->email);
			$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Buyer Order Information'));
			$mail->send();


			$userdetail = Myclass::getcurrentUserdetail();

			$userid = $productModel->user->userId;
			$criteria = new CDbCriteria;
			$criteria->addCondition('user_id = "'.$userid.'"');
			$userdevicedet = Userdevices::model()->findAll($criteria);
			if(count($userdevicedet) > 0){
				foreach($userdevicedet as $userdevice){
					$deviceToken = $userdevice->deviceToken;
					$lang = $userdevice->lang_type;
					$badge = $userdevice->badge;
					$badge +=1;
					$userdevice->badge = $badge;
					$userdevice->deviceToken = $deviceToken;
					$userdevice->save(false);
					if(isset($deviceToken)){
						$msg = Myclass::push_lang($lang);
						$messages =  $userModel->username." ".Yii::t('app','placed an order in your shop, order id :')." ".$orderId;
						Myclass::pushnot($deviceToken,$messages,$badge);
					}
				}
			}

			/*if ($mail->send()) {
				//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
				//$this->redirect(array('user/login'));
				//echo "email sent";die;
			} else {
				//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
				//echo $mail->getError();die;
			}*/




			/*if ($mail->send()) {
				//Yii::app()->user->setFlash('login','Thank you for contacting us. We will respond to you as soon as possible.');
				//$this->redirect(array('user/login'));
				//echo "email sent";die;
			} else {
				//Yii::app()->user->setFlash('error','Error while sending email: '.$mail->getError());
				//echo $mail->getError();die;
			}*/
		}
	}

	public function actionBraintreepayment()
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

			$price = $_POST['totalcost'];
			$firstname = $_POST['card_name'];
			$card_number = $_POST['card_number'];
			$cvv         = $_POST['cvv'];
			$month        = $_POST['expiry_month'];
			$year         = $_POST['expiry_year'];
			$expirationDate =$month.'/'.$year;

			$result = Braintree\Transaction::sale([
					'amount' => $price,
					'creditCard' => array(
					'number' => $card_number,
					'cardholderName' => $firstname,
					'expirationDate' => $expirationDate,
					'cvv' => $cvv
					)
			]);
			$result1 = Braintree\Transaction::submitForSettlement($result->transaction->id);

			if ($result->success == '1' && $result1->success == '1') {
					$userId = $_POST['userId'];
					$userModel = Users::model()->findByPk($userId);
					$userId = $userModel->userId;

					$transaction = $result->transaction;

					$currencyCode = $_POST['currency'];
					$quantity = "1";

					$itemId = $_POST['productId'];
					$productModel = Products::model()->findByPk($itemId);
					$i = 1;
					$keyarray['iteration'] = 1;
					$keyarray['item_name'.$i] = $productModel->name;
					$shippingId = $_POST['shippingId'];

					$keyarray['quantity'.$i] = $quantity;
					$custom[2] = "";

					$tempShippingModel = Tempaddresses::model()->findByPk($shippingId);

					$shippingaddressesModel = Shippingaddresses::model()->findByAttributes(array(
							'userId'=>$tempShippingModel->userId,
							'nickname'=>$tempShippingModel->nickname,
							'name'=>$tempShippingModel->name,
							'address1'=>$tempShippingModel->address1,
							'address2'=>$tempShippingModel->address2,
							'city'=>$tempShippingModel->city,
							'state'=>$tempShippingModel->state,
							'country'=>$tempShippingModel->country,
							'zipcode'=>$tempShippingModel->zipcode,
							'phone'=>$tempShippingModel->phone));

					if (!empty($shippingaddressesModel)){
						$shippingId = $shippingaddressesModel->shippingaddressId;
					}else{
						$newShippingEntry = new Shippingaddresses();
						$newShippingEntry->userId = $tempShippingModel->userId;
						$newShippingEntry->name = $tempShippingModel->name;
						$newShippingEntry->nickname = $tempShippingModel->nickname;
						$newShippingEntry->country = $tempShippingModel->country;
						$newShippingEntry->state = $tempShippingModel->state;
						$newShippingEntry->address1 = $tempShippingModel->address1;
						$newShippingEntry->address2 = $tempShippingModel->address2;
						$newShippingEntry->city = $tempShippingModel->city;
						$newShippingEntry->zipcode = $tempShippingModel->zipcode;
						$newShippingEntry->phone = $tempShippingModel->phone;
						$newShippingEntry->countryCode = $tempShippingModel->countryCode;

						$newShippingEntry->save(false);

						$shippingId = $newShippingEntry->shippingaddressId;
					}

					$productModel = Products::model()->findByPk($itemId);



					$keyarray['shipping'] = isset($keyarray['shipping']) ? $keyarray['shipping'] : $keyarray['mc_shipping'];

					$ordersModel = new Orders();
					$ordersModel->userId = $userId;
					$ordersModel->sellerId = $productModel->userId;
					$ordersModel->totalCost = $price;
					$ordersModel->totalShipping = $productModel->shippingCost;
					$ordersModel->discount = $discount;
					$ordersModel->discountSource = $discountSource;
					$ordersModel->orderDate = time();
					$ordersModel->shippingAddress = $shippingId;
					$ordersModel->currency = $currencyCode;
					$ordersModel->sellerPaypalId = $productModel->paypalid;
					$ordersModel->status = 'pending';
					$ordersModel->trackPayment = 'pending';

					$ordersModel->save(false);
					$orderId = $ordersModel->orderId;

					$orderItemTotalPrice = $price - $productModel->shippingCost;
					$orderItemUnitPrice = $orderItemTotalPrice / $quantity;

					$orderItemsModel = new Orderitems();
					$orderItemsModel->orderId = $orderId;
					$orderItemsModel->productId = $itemId;
					$orderItemsModel->itemName = $productModel->name;
					$orderItemsModel->itemPrice = $orderItemTotalPrice;
					//$orderItemsModel->itemSize = $custom[2];
					$orderItemsModel->itemQuantity = $quantity;
					$orderItemsModel->itemunitPrice = $orderItemUnitPrice;
					$orderItemsModel->shippingPrice = $productModel->shippingCost;

					$orderItemsModel->save(false);

					$productModel->quantity = $productModel->quantity - $quantity;
					/*if (!empty($custom[2]) && $custom[2] != ''){
						$productOptions = json_decode($productModel->sizeOptions, true);
						$productOptions[$custom[2]]['quantity'] -= $keyarray['quantity'.$i];
						$productModel->sizeOptions = json_encode($productOptions);
					}*/
					$productModel->soldItem = 1;
					if($productModel->promotionType != 3){
						$promotionCriteria = new CDbCriteria();
						$promotionCriteria->addCondition("productId = $productModel->productId");
						$promotionCriteria->addCondition("status LIKE 'live'");
						$promotionModel = Promotiontransaction::model()->find($promotionCriteria);

						if(!empty($promotionModel)){
							if($promotionModel->promotionName != 'urgent'){
								$previousCriteria = new CDbCriteria();
								$previousCriteria->addCondition("productId = $promotionModel->productId");
								$previousCriteria->addCondition("status LIKE 'Expired'");
								$previousPromotion = Promotiontransaction::model()->find($previousCriteria);
								if(!empty($previousPromotion)){
									$previousPromotion->status = "Canceled";
									$previousPromotion->save(false);
								}
							}
							$promotionModel->status = "Expired";
							$promotionModel->save(false);
						}
					}
					$productModel->promotionType = 3;
					$productModel->save(false);

					$invoiceModel = new Invoices();
					$invoiceModel->orderId = $orderId;
					$invoiceModel->invoiceNo = '';
					$invoiceModel->invoiceDate = time();
					$invoiceModel->invoiceStatus = "Completed";
					$invoiceModel->paymentMethod = 'Braintree';
					$invoiceModel->paymentTranxid = $transaction->id;

					$invoiceModel->save(false);

					$invoiceNo = "INV".$invoiceModel->invoiceId.$userId;

					$invoiceModel->invoiceNo = $invoiceNo;
					$invoiceModel->save(false);

					$sellerId = $productModel->userId;
					$sellerData = Users::model()->findByPk($sellerId);
					$sellerEmail = $sellerData->email;
					$sellerName = $sellerData->name;

					$notifyMessage = 'made a purchase on your item';
					Myclass::addLogs("order", $userModel->userId, $productModel->userId, $productModel->productId, $productModel->productId, $notifyMessage);

					$mail = new YiiMailer();
					if($siteSettings->smtpEnable == 1) {
						//$mail->IsSMTP();                         // Set mailer to use SMTP
						$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
						$mail->Host = $siteSettings->smtpHost;  // Specify main and backup server
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
						$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
						$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
						$mail->Port = $siteSettings->smtpPort;
					}
					$mail->setView('sellerorderintimation');
					$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
							'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
							'tempShippingModel'=>$tempShippingModel));
					$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
					$mail->setTo($sellerEmail);
					$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Seller Order Information'));
					$mail->send();

					$mail = new YiiMailer();
					if($siteSettings->smtpEnable == 1) {
						//$mail->IsSMTP();                         // Set mailer to use SMTP
						$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
						$mail->Host = $siteSettings->smtpHost;  // Specify main and backup server
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
						$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
						$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
						$mail->Port = $siteSettings->smtpPort;
					}
					$mail->setView('buyerorderintimation');
					$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom'=>$custom,
							'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
							'tempShippingModel'=>$tempShippingModel));
					$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
					$mail->setTo($userModel->email);
					$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Buyer Order Information'));
					$mail->send();


					$userdetail = Myclass::getcurrentUserdetail();

					$userid = $productModel->user->userId;
					$criteria = new CDbCriteria;
					$criteria->addCondition('user_id = "'.$userid.'"');
					$userdevicedet = Userdevices::model()->findAll($criteria);
					if(count($userdevicedet) > 0){
						foreach($userdevicedet as $userdevice){
							$deviceToken = $userdevice->deviceToken;
							$badge = $userdevice->badge;
							$badge +=1;
							$userdevice->badge = $badge;
							$userdevice->deviceToken = $deviceToken;
							$userdevice->save(false);
							if(isset($deviceToken)){
								$messages =  $userModel->username." placed an order in your shop, order id : ".$orderId;
								Myclass::pushnot($deviceToken,$messages,$badge);
							}
						}
					}
					echo "success";
			}
			else
			{
				echo "failed";
			}
	}

	public function actionGetShippingAddress() {
		$nickname = Myclass::checkPostvalue($_POST['nickname']) ? $_POST['nickname'] : "";
		$shippingId = Myclass::checkPostvalue($_POST['shippingId']) ? $_POST['shippingId'] : "";
		$nickname = $_POST['nickname'];
		$shippingId = $_POST['shippingId'];
		$model = Tempaddresses::model()->findByAttributes(array("nickname" => $nickname,'shippingaddressId'=> $shippingId));
		$shippingArray = json_decode($_POST['shippingRange']);
		$shippingCost = json_decode($_POST['shipCost'],true);
		if(!empty($shippingArray)) {
			$countryId = Country::model()->find("country = '{$model->country}'")->countryId;

			if(in_array($countryId,$shippingArray)) {
				$shippingAmt = $shippingCost[$countryId];
			} else {
				if(in_array(0,$shippingArray)) {
					$shippingAmt = $shippingCost[0];
				} else {
					$shippingAmt = 0;
				}
			}
		}
		$return_array = array(
				'shippingId' => $model->shippingaddressId,
				'username' => $model->name,
				'address1' => $model->address1,
				'address2' => $model->address2,
				'city' => $model->city,
				'pincode' => $model->zipcode,
				'state' => $model->state,
				'country' => $model->country,
				'phone' => $model->phone,
				'shipPrice' => $shippingAmt
		);

		echo CJSON::encode($return_array, JSON_PRETTY_PRINT);
	}

	public function actionApplyCoupon(){
		if(isset($_POST['couponCode'])) {
			$sellerId = Myclass::checkPostvalue($_POST['sellerId']) ? $_POST['sellerId'] : "";
			$productId = Myclass::checkPostvalue($_POST['productId']) ? $_POST['productId'] : "";
			$couponCode = Myclass::checkPostvalue($_POST['couponCode']) ? $_POST['couponCode'] : "";

			$sellerId = $_POST['sellerId'];
			$productId = $_POST['productId'];
			$couponCode = $_POST['couponCode'];

			$criteria = new CDbCriteria;
			$criteria->addCondition("couponCode = '{$couponCode}'");
			$criteria->addCondition("sellerId = $sellerId");
			$criteria->addCondition("status = 1");
			$check = Coupons::model()->find($criteria);
			if(!empty($check))
			{
				if($check->productId == 0 || $check->productId == $productId) {
					$return_array = array(
							'couponCode' =>$check->couponCode,
							'couponValue' => $check->couponValue,
							'couponType' => $check->couponType,
							'startDate' => $check->startDate,
							'endDate' => $check->endDate,
							'maxAmount' => $check->maxAmount,
					);
					echo CJSON::encode($return_array, JSON_PRETTY_PRINT);
				}
			}
		}
	}

	public function actionCheckRange() {
		$country = Myclass::checkPostvalue($_POST['country']) ? $_POST['country'] : "";
		$country = $_POST['country'];
		$countryId = Country::model()->find("country = '{$country}'")->countryId;
		$shippingArray = json_decode($_POST['shippingRange']);
		if(!empty($shippingArray)) {
			if(in_array($countryId,$shippingArray)) {
				echo 'true';
				return true;
			} else {
				$check = in_array(0,$shippingArray);
				if($check == true) {
					echo 'true';
					return true;
				} else {
					echo 'false';
					return false;
				}

			}
		}
	}

	public function actionAddshipping($id=NULL) {
		$id = Myclass::checkPostvalue($id) ? $id : "";
		if ($id === null){
			$id = Myclass::checkPostvalue($_POST['shippingId']) ? $_POST['shippingId'] : "";
			$id = $_POST['shippingId'];
		}
		if ($id === null || $id == ""){
			$model = new Tempaddresses();
			$model->setScenario('create');
			$model->slug = Myclass::getRandomString(8);
		}else{
			$model = Tempaddresses::model()->findByAttributes(array('slug'=>$id));
			$model->country = $model->countryCode.'-'.$model->country;
			$model->setScenario('update');
		}


		$countryModel = Country::model()->findAll();

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}

		// Uncomment the following line if AJAX validation is needed
		if(isset($_POST['ajax']) && $_POST['ajax']==='tempaddresses-addshipping-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}


		if(isset($_POST['Tempaddresses']))
		{
			$addressData = $_POST['Tempaddresses'];
			$model->attributes=$_POST['Tempaddresses'];
			$model->userId = Yii::app()->user->id;
			if (isset($addressData['country'])){
				$countryDetails = explode('-', $addressData['country']);
				$model->countryCode = $countryDetails[0];
				$model->country = $countryDetails[1];
			}
			if($model->save(false)){
				$default = Users::model()->findByPk(Yii::app()->user->id);
				if($default->defaultshipping == 0){
					$default->defaultshipping = $model->shippingaddressId;
					$default->save(false);
				}
				$_SESSION['shipping_id'] = $model->shippingaddressId;
				//	$this->redirect(array('view','id'=>$model->productId));
				$this->redirect($_SERVER['HTTP_REFERER']);
			}else{
				echo "Not saved";
			}
		}
		//$this->render('addshipping', array('model'=>$model, 'countriesList'=>$countriesList));
	}
}