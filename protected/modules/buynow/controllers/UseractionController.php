<?php

class UseractionController extends Controller
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
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	protected function beforeAction($action)
	{
		if (!parent::beforeAction($action)) {
			return false;
		}
		$allowedActions = array('adaptiveipnprocess','review');
		$user = Yii::app()->user;
		if($user->isGuest && !in_array(Yii::app()->controller->action->id, $allowedActions)) {
			$this->redirect(array('/user/login'));
			return false;
		}

		return true;
	}

	public function actionOrders() {
		$userId = Yii::app()->user->id;
		$criteria = new CDbCriteria;
		$criteria->addCondition("userId = $userId");
		$criteria->order = "orderDate DESC";
		$count = Orders::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(10);
		$pages->applyLimit($criteria);
		$orders = Orders::model()->with('orderitems','invoices')->findAll($criteria);

		$user = Yii::app()->user;
		if($user->isGuest) {
			$this->redirect(array('/user/login'));
			return false;
		}
		$id = Yii::app()->user->id;

		$user = Users::model()->findByPk($id);

		$userid = Yii::app()->user->id;
		if(!empty($userid)){
		$followCriteria = new CDbCriteria;
		$followCriteria->addCondition("userId = $userid");
		$follower = Followers::model()->findAll($followCriteria);
		//echo "<pre>";print_r($follower);die;
		$followerIds = array();
		if(!empty($follower)) {
			foreach($follower as $follower):
			$followerIds[] = $follower->follow_userId;
			endforeach;
		}
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition("countryId != 0");
		$countryModel = Country::model()->findAll($criteria);

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}

		if(Yii::app()->request->isAjaxRequest) {
			$pages = new CPagination($count);
			$pages->setPageSize(10);
			$pages->applyLimit($criteria);
			$this->renderPartial('orders',array(
			'orders'=>$orders,'pages' => $pages,
			'user' => $user,'follower' => $follower,
			'followerIds' => $followerIds, 'countriesList' => $countriesList
			));
		} else {
			$this->render('orders',array(
			'orders'=>$orders,'pages' => $pages,
			'user' => $user,'follower' => $follower,
			'followerIds' => $followerIds, 'countriesList' => $countriesList
			));
		}
	}

	public function actionSales() {
		$userId = Yii::app()->user->id;
		$criteria = new CDbCriteria;
		$criteria->addCondition("sellerId = $userId");
		$criteria->order = "orderDate DESC";
		$count = Orders::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(10);
		$pages->applyLimit($criteria);
		$sales = Orders::model()->with('orderitems','user')->findAll($criteria);
		$tracking = new Trackingdetails;

		$user = Yii::app()->user;
		if($user->isGuest) {
			$this->redirect(array('/user/login'));
			return false;
		}
		$id = Yii::app()->user->id;

		$user = Users::model()->findByPk($id);

		$userid = Yii::app()->user->id;
		if(!empty($userid)){
		$followCriteria = new CDbCriteria;
		$followCriteria->addCondition("userId = $userid");
		$follower = Followers::model()->findAll($followCriteria);
		//echo "<pre>";print_r($follower);die;
		$followerIds = array();
		if(!empty($follower)) {
			foreach($follower as $follower):
			$followerIds[] = $follower->follow_userId;
			endforeach;
		}
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition("countryId != 0");
		$countryModel = Country::model()->findAll($criteria);

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}

		if(Yii::app()->request->isAjaxRequest) {
			$pages = new CPagination($count);
			$pages->setPageSize(10);
			$pages->applyLimit($criteria);
			$this->renderPartial('sales',array(
			'sales'=>$sales,'pages' => $pages,'tracking' => $tracking,'user' => $user,'follower' => $follower,'followerIds'=> $followerIds,
			'countriesList' => $countriesList
			));
		} else {
			$this->render('sales',array(
			'sales'=>$sales,'pages' => $pages,'tracking' => $tracking,'user' => $user,'follower' => $follower,'followerIds'=> $followerIds,
			'countriesList' => $countriesList
			));
		}
	}

	public function actionVieworders($id)
	{
		$dec = Myclass::safe_b64decode($id);
		$spl = explode('-',$dec);
		$id = $spl[0];
		if(count($spl) <= 1){
			$this->redirect(array('orders'));
		}
		$model = Orders::model()->with('user','orderitems')->findByPk($id);
		$trackingDetails = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		$shipping = Shippingaddresses::model()->findByPk($model->shippingAddress);
		$reviewModel = Reviews::model()->findByAttributes(array("sourceId" => $id, "reviewType" => "order"));
		$this->renderPartial('vieworders',array(
			'model'=>$model,'shipping' => $shipping,'trackingDetails' => $trackingDetails,'reviewModel' => $reviewModel
		));
	}

	public function actionViewsales($id)
	{
		$this->layout = "ajax";
		$dec = Myclass::safe_b64decode($id);
		$spl = explode('-',$dec);
		$id = $spl[0];
		if(count($spl) <= 1){
			$this->redirect(array('orders'));
		}
		$model = Orders::model()->with('user','orderitems')->findByPk($id);
		$trackingDetails = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		$tracking = new Trackingdetails;
		$shipping = Shippingaddresses::model()->findByPk($model->shippingAddress);
		$this->renderPartial('viewsales',array(
			'model'=>$model,'shipping' => $shipping,'trackingDetails' => $trackingDetails,'tracking' => $tracking
		), false, true);
	}

	public function actionMessage($id = ""){
		$userId = Yii::app()->user->id;
		$userDetails = Myclass::getUserDetails(Yii::app()->user->id);


		if(!empty($id)) {
			$dec = Myclass::safe_b64decode($id);
			$spl = explode('-',$dec);
			$id = $spl[0];
			$chatCriteria = new CDbCriteria;
			$chatCriteria->condition = "user1 = '$userId' AND user2 = '$id' OR user1 = '$id' AND user2 = '$userId'";
			$chat = Chats::model()->find($chatCriteria);
			if(empty($chat)) {
				$newChat = new Chats();
				$newChat->user1 = $userId;
				$newChat->user2 = $id;
				$newChat->lastContacted = time();
				$newChat->save(false);
				$messageChatId = $newChat->chatId;
			} else {
				$messageChatId = $chat->chatId;
			}
		}
		$criteria = new CDbCriteria;
		$criteria->condition = "user1 = '$userId' OR user2 = '$userId' order by lastContacted DESC";
		$chatedUsers = Chats::model()->findAll($criteria);

		$firstChat = "";
		$currentChatUser = $id;
		$firstLastReadCheck = 0;
		$chattingUsers = array();
		if (count($chatedUsers) > 0){
			$lastMessages = array();
			foreach ($chatedUsers as $chatedUser){
				$chatUserkkey = 0;
				$firstLastReadCheck = 0;
				if ($chatedUser->user1 != $userId){
					$chattingUsers[] = $chatedUser->user1;
					$lastMessages[$chatedUser->user1]['message'] = $chatedUser->lastMessage;
					$lastMessages[$chatedUser->user1]['time'] = $chatedUser->lastContacted;
					//$currentChatUser = $currentChatUser == '' ? $chatedUser->user1 : $currentChatUser;
					if ($currentChatUser == ""){
						$currentChatUser = $chatedUser->user1;
						$firstChat = $chatedUser->chatId;
						$firstLastReadCheck = 1;
					}elseif ($currentChatUser == $chatedUser->user1){
						$firstChat = $chatedUser->chatId;
						$firstLastReadCheck = 1;
					}
					$chatUserkkey = $chatedUser->user1;
				}elseif($chatedUser->user2 != $userId){
					$chattingUsers[] = $chatedUser->user2;
					$lastMessages[$chatedUser->user2]['message'] = $chatedUser->lastMessage;
					$lastMessages[$chatedUser->user2]['time'] = $chatedUser->lastContacted;
					//$currentChatUser = $currentChatUser == '' ? $chatedUser->user2 : $currentChatUser;
					if ($currentChatUser == ""){
						$currentChatUser = $chatedUser->user2;
						$firstChat = $chatedUser->chatId;
						$firstLastReadCheck = 1;
					}elseif ($currentChatUser == $chatedUser->user2){
						$firstChat = $chatedUser->chatId;
						$firstLastReadCheck = 1;
					}
					$chatUserkkey = $chatedUser->user2;
				}
				if($chatedUser->lastToRead == $userId && $firstLastReadCheck != 1){
					$lastMessages[$chatUserkkey]['messaggeMarker'] = '<div class="message-unread-count"></div>';//"<i class='fa fa-circle green-dot'></i>";
					$lastMessages[$chatUserkkey]['messaggeReplyMarker'] = "";
				}elseif($chatedUser->lastToRead != 0 && $firstLastReadCheck != 1){
					$lastMessages[$chatUserkkey]['messaggeReplyMarker'] = '<img alt="send-icon" src="'.Yii::app()->createAbsoluteUrl('images/reply.png').'">';//"<i class='fa fa-mail-reply'></i>";
					$lastMessages[$chatUserkkey]['messaggeMarker'] = "";
				}else{
					$lastMessages[$chatUserkkey]['messaggeMarker'] = "";
					$lastMessages[$chatUserkkey]['messaggeReplyMarker'] = "";
				}
				//$firstChat = $firstChat == '' ? $chatedUser->chatId : $firstChat;
			}
			$firstChatModel = Chats::model()->findByPk($firstChat);
			if($firstChatModel->lastToRead != 0 && $firstChatModel->lastToRead == $userId){
				$firstChatModel->lastToRead = 0;
				$firstChatModel->save(false);
			}

			$chatUserModel = Users::model()->findAllByAttributes(array('userId'=>$chattingUsers));
			foreach ($chatUserModel as $chatModel){
				$chatUser[$chatModel->userId] = $chatModel;
			}

			$criteria = new CDbCriteria;
			$criteria->condition = "chatId = '$firstChat' AND messageType NOT LIKE 'exchange'";
			$messageModel = Messages::model()->findAll($criteria);

			//$messageModel = Messages::model()->findAllByAttributes(array('chatId'=>$firstChat,
			//		'messageType NOTLIKE'=>'exchange'));
			$messageChatId = $firstChat;
			$currentChatUserImage = $chatUser[$currentChatUser]->userImage;
			if(!empty($currentChatUserImage)) {
				//$currentChatUserImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/'.$currentChatUserImage),$chatUser[$currentChatUser]->username);
				$currentChatUserImage = Yii::app()->createAbsoluteUrl('user/resized/75/'.$currentChatUserImage);
			} else {
				//$currentChatUserImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/default/'.Myclass::getDefaultUser()),$chatUser[$currentChatUser]->username);
				$currentChatUserImage = Yii::app()->createAbsoluteUrl('user/resized/75/default/'.Myclass::getDefaultUser());
			}
			if(!empty($userDetails->userImage)) {
				//$currentUserImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/'.$userDetails->userImage),$userDetails->username);
				$currentUserImage = Yii::app()->createAbsoluteUrl('user/resized/75/'.$userDetails->userImage);
			} else {
				//$currentUserImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/default/'.Myclass::getDefaultUser()),$userDetails->username);
				$currentUserImage = Yii::app()->createAbsoluteUrl('user/resized/75/default/'.Myclass::getDefaultUser());
			}
			//echo "<pre>";print_r($chattingUsers);print_r($messageModel);die;
			if(Yii::app()->request->isAjaxRequest) {
				$this->renderPartial('message',array('currentUserDetails'=>$userDetails, 'chattingUsers'=>$chattingUsers,
						'chatUser'=>$chatUser, 'messageModel'=>$messageModel, 'lastMessages'=>$lastMessages,
						'currentChatUser'=>$currentChatUser, 'currentChatUserImage'=>$currentChatUserImage,
						'currentUserImage'=>$currentUserImage,'messageChatId' => $messageChatId, 'ajaxChat' => 1));
			}else{
				$this->render('message',array('currentUserDetails'=>$userDetails, 'chattingUsers'=>$chattingUsers,
					'chatUser'=>$chatUser, 'messageModel'=>$messageModel, 'lastMessages'=>$lastMessages,
					'currentChatUser'=>$currentChatUser, 'currentChatUserImage'=>$currentChatUserImage,
					'currentUserImage'=>$currentUserImage,'messageChatId' => $messageChatId, 'ajaxChat' => 0));
			}
		}else{
			$this->render('message',array('currentUserDetails'=>$userDetails,
					'chattingUsers'=>$chattingUsers));
		}
	}

	public function actionPostmessage(){
		if (isset($_POST)){

			$message = Myclass::checkPostvalue($_POST['message']) ? $_POST['message'] : "";
			$senderId = Myclass::checkPostvalue($_POST['senderId']) ? $_POST['senderId'] : "";
			$messageType = Myclass::checkPostvalue($_POST['messageType']) ? $_POST['messageType'] : "";
			$sourceId = Myclass::checkPostvalue($_POST['sourceId']) ? $_POST['sourceId'] : "";
			$chatId = Myclass::checkPostvalue($_POST['chatId']) ? $_POST['chatId'] : "";

			$message = $_POST['message'];
			$senderId = $_POST['senderId'];
			$messageType = $_POST['messageType'];
			$sourceId = isset($_POST['sourceId']) && $_POST['sourceId'] != "" ? $_POST['sourceId'] : 0;
			$chatId = $_POST['chatId'];
			$timeUpdate = time();

			$p = new CHtmlPurifier();
			$message = $p->purify($message);

			if ($message != ""){

				$messageModel = new Messages();
				$messageModel->message = $message;
				$messageModel->messageType = $messageType;
				$messageModel->senderId = $senderId;
				$messageModel->sourceId = $sourceId;
				$messageModel->chatId = $chatId;
				$messageModel->createdDate = $timeUpdate;
				$messageModel->save();

				if($sourceId == 0){
					$chatModel = Chats::model()->findByPk($chatId);

					$chatModel->lastContacted = $timeUpdate;
					if ($chatModel->user1 == $senderId){
						$chatModel->lastToRead = $chatModel->user2;
					}else{
						$chatModel->lastToRead = $chatModel->user1;
					}
					$chatModel->lastMessage = $message;
					$chatModel->save();
				}

				$userImage = Myclass::getUserDetails($senderId);
				$outputData = array();
				if(!empty($userImage->userImage)) {
					//$userImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/'.$userImage->userImage),$userImage->username);
					$outputData['userName'] = $userImage->username;
					$outputData['userImage'] = Yii::app()->createAbsoluteUrl('user/resized/75/'.$userImage->userImage);
				} else {
					//$userImage = CHtml::image(Yii::app()->createAbsoluteUrl('user/resized/40x40/default/'.Myclass::getDefaultUser()),$userImage->username);
					$outputData['userName'] = $userImage->username;
					$outputData['userImage'] = Yii::app()->createAbsoluteUrl('user/resized/75/default/'.Myclass::getDefaultUser());
				}
				$outputData['chatURL'] = Yii::app()->baseUrl."/message/".Myclass::safe_b64encode($userImage->userId.'-0');
				$outputData['chatTime'] = date('M jS Y', $timeUpdate);
				$outputData['message'] = $message;
				/* echo '<div class="chat-grid-userimage">'.$userImage.'</div><div class="chat-grid-details">
				 <p class="chat-grid-msgs">'.$message.'</p><p class="chat-grid-time">'.date('M jS Y', $timeUpdate).'</p>
				 </div>'; */

				/*$userid = $chatModel->user2;
				$criteria = new CDbCriteria;
				$criteria->addCondition('user_id = "'.$userid.'"');
				$userdevicedet = Userdevices::model()->find($criteria);

				if(count($userdevicedet) > 0){
					$deviceToken = $userdevicedet->deviceToken;
					$badge = $userdevicedet->badge;
					$badge +=1;
					$userdevicedet->badge = $badge;
					$userdevicedet->deviceToken = $deviceToken;
					$userdevicedet->save(false);
					if(isset($deviceToken)){
						$messages = "You have message from ".$userImage->username." - ".$message;
						Myclass::pushnot($deviceToken,$messages,$badge);
					}
				}*/

				echo json_encode($outputData);

			}else{
				echo "";
			}
		}
	}

	public function actionCancelorder($id)
	{
		$id = Myclass::checkPostvalue($id) ? $id : "";
		$orderdata = Orders::model()->findByPk($id);
		$orderdata->status = "cancelled";
		$orderdata->trackPayment = "pending";
		$orderdata->save(false);

		$order = Orders::model()->with('orderitems')->findByPk($id);

		$productid = $order['orderitems'][0]['productId'];
		$productdata = Products::model()->findByPk($productid);
		$productdata->quantity = 1;
		$productdata->soldItem = 0;
		$productdata->save(false);

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
		$mail->setView('cancelledmail');
		$mail->setData(array('name' => $check->name,
							'siteSettings' => $siteSettings,
							'orderId'=>$id));
		$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
		$mail->setTo($check->email);
		$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Cancelled Mail'));
		$mail->send();

		$notifyMessage = 'your order has been cancelled Order Id :'.$order->orderId;
		Myclass::addLogs("order", $order->userId, $order->sellerId, $order->orderId, 0, $notifyMessage);
		$this->redirect($_SERVER['HTTP_REFERER']);
	}

	public function actionChangestatus() {
		if(isset($_POST['orderId'])) {

			$orderId = Myclass::checkPostvalue($_POST['orderId']) ? $_POST['orderId'] : "";
			$status = Myclass::checkPostvalue($_POST['status']) ? $_POST['status'] : "";

			$order = Orders::model()->findByPk($_POST['orderId']);

			if($order->status == 'shipped' && $_POST['status']  == 'processing') {
				Yii::app()->user->setFlash('warning',Yii::t('app','Product has already been Shipped.'));
				$this->redirect(array('sales'));
			}

			if(!empty($order)) {
				if(isset($_POST['status'])) {
					
					$order->status = $_POST['status'];
					if($_POST['status'] == 'delivered'){
						$order->statusDate = time();
						$order->reviewFlag = 1;
					}
					

					if($_POST['status'] == 'delivered')
					{
						$siteSettings = Sitesettings::model()->find();
						$check = Users::model()->findByPk($order->userId);
						$userName=$check->username;
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
						$mail->setView('deliveredmail');
						$mail->setData(array('name' => $check->name,
											'siteSettings' => $siteSettings,
											'orderId'=>$_POST['orderId']));
						$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
						$mail->setTo($check->email);
						$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Delivered Mail'));
						$mail->send();

						$notifyMessage = $userName.' has marked your order as delivered. Order Id :'.$order->orderId;
						$val=0;
						$text="order";
						Myclass::addLogs($text, $order->userId, $order->sellerId, $order->orderId, $val, $notifyMessage);
					
						$userid = $order->userId;
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
							$messages = $userName." ".Yii::t('app','has marked your order as delivered.')." ".Yii::t('app','Your orderid:')." ".$order->orderId;
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
						}
						}
					}
					
					//new process
							$orderid=$_POST['orderId'];
							$orderPreviousStatus= Orders::model()->findByPk($orderid);
							$status=$_POST['status'];
							
					if ($status == 'processing' && $orderPreviousStatus->status == "pending") {
						//echo "processing";exit;
				$criteria = new CDbCriteria;
				$criteria->addCondition("orderId=".$orderid);
				Orders::model()->updateAll(array('status' => "processing"),$criteria);

						$userid = $orderPreviousStatus->userId;
						$criteria = new CDbCriteria;
						$criteria->addCondition('user_id ='.$userid);
						$userdevicedet = Userdevices::model()->findAll($criteria);
				$val=0;$text="order";
				$notifyMessage = 'your order has been marked as processing Order Id :'.$orderPreviousStatus->orderId;
				Myclass::addLogs($text, $orderPreviousStatus->sellerId, $orderPreviousStatus->userId, $orderPreviousStatus->orderId, $val, $notifyMessage);

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

						$text = 'Your orderid:';
						$msg = Yii::t('app',$text);

						$text1 = 'has been marked as processing';
						$msg1 = Yii::t('app',$text1);


							$messages = $msg.' '.$orderid.' '.$msg1;
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
					}

				
			}
					$order->save(false);
					//new process

				}




			}
		}
	}

	public function actionEdit_tracking_details()
	{
		$id = Myclass::checkPostvalue($_POST['orderid']) ? $_POST['orderid'] : "";
		$id = $_POST['orderid'];
		$tracking = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		$trackingdetail['orderid'] = $tracking->orderid;
		$trackingdetail['shippingdate'] = date('m/d/Y',$tracking->shippingdate);
		$trackingdetail['couriername'] = $tracking->couriername;
		$trackingdetail['courierservice'] = $tracking->courierservice;
		$trackingdetail['trackingid'] = $tracking->trackingid;
		$trackingdetail['notes'] = $tracking->notes;
		print_r(json_encode($trackingdetail));
	}

	public function actionTracking($id) {

		$dec = Myclass::safe_b64decode($id);
		$spl = explode('-',$dec);
		$id = $spl[0];
		if(count($spl) <= 1){
			$this->redirect(array('sales'));
		}
		if($id == "")
		{
			$id = $_POST['Trackingdetails']['orderid'];
		}
		$model = Orders::model()->with('orderitems','user')->findByPk($id);
		if($model->status == 'delivered') {
			Yii::app()->user->setFlash('warning',Yii::t('app','Product has already been delivered.'));
			$this->redirect(array('sales'));
		}

		$seller = Myclass::getUserDetails($model->sellerId);
		$siteSettings = Sitesettings::model()->find();
		$userModel = $model['user'];
		$shipping = Shippingaddresses::model()->findByPk($model->shippingAddress);

		$check = Trackingdetails::model()->findAllByAttributes(array("orderid" => $id));

		if(empty($check)) {
			$tracking = new Trackingdetails;
		} else {
			$tracking = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		}
		if(isset($_POST['ajax']) && $_POST['ajax']==='trackingdetails-form')
		{
			echo CActiveForm::validate($tracking);
			Yii::app()->end();
		}

		if(isset($_POST['Trackingdetails']))
		{
			$tracking->attributes = $_POST['Trackingdetails'];
			$tracking->buyeraddress = $_POST['Trackingdetails']['buyeraddress'];
			$date = new DateTime($_POST['Trackingdetails']['shippingdate']);
			$tracking->shippingdate = $date->getTimeStamp();
			if($model->status != 'delivered') {
				if($tracking->validate()) {
					$tracking->save(false);
					$model->status = 'shipped';
					$siteSettings = Sitesettings::model()->find();
					if($model->save(false)) {
						$mail = new YiiMailer();
						if($siteSettings->smtpEnable == 1) {
							//$mail->IsSMTP();                         // Set mailer to use SMTP
							$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
							$mail->Host = $siteSettings->smtpHost;'smtp.gmail.com';  // Specify main and backup server
							$mail->SMTPAuth = true;                               // Enable SMTP authentication
							$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
							$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
							if($siteSettings->smtpSSL == 1)
								$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
							$mail->Port = $siteSettings->smtpPort; //465;
						}

						$mail->setView('trackdetailsmail');
						$mail->setData(array('siteSettings' => $siteSettings,
								'tempShippingModel' => $shipping,'userModel' => $userModel,
								'sellerName' => $seller->name,'tracking'=>$tracking,'model'=>$model));
						$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
						$mail->setTo($model['user']['email']);
						$mail->setSubject(Myclass::getSiteName().' '.Yii::t('app','Tracking Details Mail'));

			$notifyMessage = 'added tracking details for your order. Order Id : '.$id;
			Myclass::addLogs("order", $model->sellerId, $model->userId, 0, 0, $notifyMessage);


						$userid = $userModel->userId;
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
							$messages =  Yii::t('app','Your orderid:').' '.$id.' '.Yii::t('app','has been updated with Tracking details');
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
						}
					}


						if ($mail->send()) {
							$this->redirect(array('sales'));
						} else {
							echo $mail->getError();die;
						}
					}
				}
				$this->redirect($_SERVER['HTTP_REFERER']);
			} else {
				Yii::app()->user->setFlash('warning',Yii::t('app','Product has already been delivered.'));
				$this->redirect($_SERVER['HTTP_REFERER']);
			}
		}
		//$this->render('tracking',compact('model','shipping','tracking'));
	}

	public function actionClaimorder()
	{
		$orderid = Myclass::checkPostvalue($_POST['orderid']) ? $_POST['orderid'] : "";
		$orderid = $_POST['orderid'];
		$order = Orders::model()->findByPk($orderid);
		$order->status = "claimed";
		$order->save(false);
	}

	public function actionShippingaddress() {
		$user = Yii::app()->user;
		if($user->isGuest) {
			$this->redirect(array('/user/login'));
			return false;
		}
		$id = Yii::app()->user->id;

		$user = Users::model()->findByPk($id);

		$userid = Yii::app()->user->id;
		if(!empty($userid)){
		$followCriteria = new CDbCriteria;
		$followCriteria->addCondition("userId = $userid");
		$follower = Followers::model()->findAll($followCriteria);
		//echo "<pre>";print_r($follower);die;
		$followerIds = array();
		if(!empty($follower)) {
			foreach($follower as $follower):
			$followerIds[] = $follower->follow_userId;
			endforeach;
		}
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition("countryId != 0");
		$countryModel = Country::model()->findAll($criteria);

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}

		if(isset($_POST['Tempaddresses']))
		{
			if(isset($_POST['shippingId']))
			{
				$address = Tempaddresses::model()->findByAttributes(array('shippingaddressId'=>$addressId));
			}
			else
			{
				$address = new Tempaddresses();
			}
			$address->nickname = $_POST['Tempaddresses']['nickname'];
			$address->name = $_POST['Tempaddresses']['name'];
			$countryDetails = explode('-', $_POST['Tempaddresses']['country']);
			$address->countryCode = $countryDetails[0];
			$address->country = $countryDetails[1];
			$address->address1 = $_POST['Tempaddresses']['address1'];
			if(isset($_POST['Tempaddresses']['address2']))
				$address->address2 = $_POST['Tempaddresses']['address2'];
			$address->city = $_POST['Tempaddresses']['city'];
			$address->state = $_POST['Tempaddresses']['state'];
			$address->zipcode = $_POST['Tempaddresses']['zipcode'];
			$address->phone = $_POST['Tempaddresses']['phone'];echo "sdf";
			if($addressse->save(false))
				echo "sucess";
			else
				echo "error";
		}
		if(!empty($id)) {
			$defaultShipping = Myclass::getDefaultShippingAddress($id);
			$criteria = new CDbCriteria;
			$criteria->addCondition("userId = '$id'");
			$count = Tempaddresses::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->setPageSize(15);
			$pages->applyLimit($criteria);
			$criteria->order = 'shippingaddressId DESC';
			$address  = Tempaddresses::model()->findAll($criteria);
			$this->render('shippingaddress',compact('address','pages','defaultShipping','user','follower','followerIds','countriesList'));
		}
	}

	public function actionDefault($id,$userid)
	{
		$id = Myclass::checkPostvalue($id) ? $id : "";
		$userid = Myclass::checkPostvalue($userid) ? $userid : "";

		$default = Users::model()->findByPk($userid);
		$default->defaultshipping = $id;
		$default->save(false);
		$this->redirect($_SERVER['HTTP_REFERER']);
	}

	public function actionGetshipping()
	{
		$addressId = Myclass::checkPostvalue($_POST['id']) ? $_POST['id'] : "";
		$addressId = $_POST['id'];
		$address = Tempaddresses::model()->findByAttributes(array('shippingaddressId'=>$addressId));
		//print_r($address);
		$addresses['nickname'] = $address->nickname;
		$addresses['name'] = $address->name;
		$addresses['address1'] = $address->address1;
		$addresses['address2'] = $address->address2;
		$addresses['city'] = $address->city;
		$addresses['state'] = $address->state;
		$addresses['country'] = $address->country;
		$addresses['zipcode'] = $address->zipcode;
		$addresses['phone'] = $address->phone;
		$addresses['countryCode'] = $address->countryCode;
		$addresses['country'] = $address->country;
		$addresses['slug'] = $address->slug;
		print_r(json_encode($addresses));
	}

	public function actionAddshipping($id=null) {
		$id = Myclass::checkPostvalue($id) ? $id : "";
		if ($id === null){
			$id = Myclass::checkPostvalue($_POST['shippingId']) ? $_POST['shippingId'] : "";
			echo $id = $_POST['shippingId'];
		}
		if ($id === null || $id == 0){
			$model = new Tempaddresses();
			$model->setScenario('create');
			$model->slug = Myclass::getRandomString(8);
		}else{
			$model = Tempaddresses::model()->findByAttributes(array('slug'=>$id));
			$model->country = $model->countryCode.'-'.$model->country;
			$model->setScenario('update');
		}
		$criteria = new CDbCriteria;
		$criteria->addCondition("countryId != 0");
		$countryModel = Country::model()->findAll($criteria);

		foreach ($countryModel as $country){
			$combineCode = $country->countryId.'-'.$country->country;
			$countriesList[$combineCode] = $country->country;
		}

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

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
				//	$this->redirect(array('view','id'=>$model->productId));
				$this->redirect(array('shippingaddress'));
			}else{
				Yii::app()->user->setFlash('success',"Unable to save please try again");
			}

		}
		$this->redirect(array('shippingaddress'));
		//$this->render('addshipping', array('model'=>$model, 'countriesList'=>$countriesList));
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tempaddresses-addshipping-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

	}

	public function actionDelete($id)
	{
		$address = Tempaddresses::model()->findByAttributes(array('slug'=>$id));
		$userDefaultShipping = Myclass::getDefaultShippingAddress($address->userId);
		if($userDefaultShipping == $address->shippingaddressId) {
			$criteria = new CDbCriteria;
			$criteria->addCondition("userId = '$address->userId'");
			$criteria->addCondition("shippingaddressId != '$address->shippingaddressId'");
			$criteria->order = 'shippingaddressId DESC';
			$addressChange  = Tempaddresses::model()->find($criteria);

			//echo "<pre>";print_r($addressChange);die;
			$user = Users::model()->findByPk($address->userId);
			if(!empty($addressChange)){
				$user->defaultshipping = $addressChange->shippingaddressId;
			}else{
				$user->defaultshipping = 0;
			}
			$user->save(false);
		}
		$address->delete();
		if(!isset($_GET['ajax']))
		$this->redirect($_SERVER['HTTP_REFERER']);
	}

	public function actionViewinvoice()
	{
		$id = Myclass::checkPostvalue($_POST['orderId']) ? $_POST['orderId'] : "";
		$this->layout = "ajax";
		$id = $_POST['orderId'];

		$model = Orders::model()->with('user','orderitems','invoices')->findByPk($id);
		$trackingDetails = Trackingdetails::model()->findByAttributes(array("orderid" => $id));
		$tracking = new Trackingdetails;
		$shipping = Shippingaddresses::model()->findByPk($model->shippingAddress);
		$this->render('viewinvoice',array(
			'model'=>$model,'shipping' => $shipping,'trackingDetails' => $trackingDetails,'tracking' => $tracking
		), false, true);
	}

	public function actionReview($id) {
		if(!empty($id)) {
			$dec = Myclass::safe_b64decode($id);
			$spl = explode('-',$dec);
			$id = $spl[0];
		} else {
			$user = Yii::app()->user;
			/*if($user->isGuest) {
				$this->redirect(array('/user/login'));
				return false;
			}*/
			$id = Yii::app()->user->id;
		}
		//echo $id;die;
		$user = Users::model()->findByPk($id);
		$userid = Yii::app()->user->id;

		$criteria = new CDbCriteria;
		$criteria->addCondition("receiverId = $id");
		$criteria->addCondition("reviewType = 'order'");
		$criteria->order = "reviewId DESC";
		$count = Reviews::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->setPageSize(15);
		$pages->applyLimit($criteria);
		$reviewsModel = Reviews::model()->with('orders', 'user')->findAll($criteria);

		if(!empty($userid)){
		$followCriteria = new CDbCriteria;
		$followCriteria->addCondition("userId = $userid");
		$follower = Followers::model()->findAll($followCriteria);
		//echo "<pre>";print_r($follower);die;
		$followerIds = array();
		if(!empty($follower)) {
			foreach($follower as $follower):
			$followerIds[] = $follower->follow_userId;
			endforeach;
		}
		}
		//echo "<pre>";print_r($reviewsModel);die;
		if(Yii::app()->request->isAjaxRequest) {
			$pages = new CPagination($count);
			$pages->setPageSize(15);
			$pages->applyLimit($criteria);
			$this->renderPartial('review',array(
			'reviewsModel'=>$reviewsModel,'pages' => $pages,'user' => $user,'follower' => $follower,'followerIds'=> $followerIds
			));
		} else {
			$this->render('review',array(
			'reviewsModel'=>$reviewsModel,'pages' => $pages,'user' => $user,'follower' => $follower,'followerIds'=> $followerIds
			));
		}
	}

	public function actionUpdatereview() {

		$orderId = Myclass::checkPostvalue($_POST['reviewOrderId']) ? $_POST['reviewOrderId'] : "";
		$reviewId = Myclass::checkPostvalue($_POST['reviewId']) ? $_POST['reviewId'] : "";
		$reviewStars = Myclass::checkPostvalue($_POST['reviewStars']) ? $_POST['reviewStars'] : "";
		$reviewTitle = Myclass::checkPostvalue($_POST['reviewTitle']) ? $_POST['reviewTitle'] : "";
		$reviewDescription = Myclass::checkPostvalue($_POST['reviewDescription']) ? $_POST['reviewDescription'] : "";

		$orderId = $_POST['reviewOrderId'];
		$reviewId = $_POST['reviewId'];
		$reviewStars = $_POST['reviewStars'];
		$reviewTitle = $_POST['reviewTitle'];
		$reviewDescription = $_POST['reviewDescription'];

		$orderModel = Orders::model()->findByPk($orderId);

		if(!empty($reviewId)){
			$reviewModel = Reviews::model()->findByPk($reviewId);
		}else{
			$reviewModel = new Reviews();
		}
		$reviewModel->senderId = $orderModel->userId;
		$reviewModel->receiverId = $orderModel->sellerId;
		$reviewModel->reviewTitle = $reviewTitle;
		$reviewModel->review = $reviewDescription;
		$reviewModel->rating = $reviewStars;
		$reviewModel->reviewType = 'order';
		$reviewModel->sourceId = $orderId;
		$reviewModel->createdDate = time();
		$reviewModel->save(false);

		if(!empty($reviewId)){
			echo 1;
		}else{
			$reviewId = $reviewModel->reviewId;
			$reviewDetails = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="bold review-content-heading">'.$reviewTitle.'</div>
						<div class="review-content-description">'.$reviewDescription.'</div>
					<div class="review-date"><span>'.Yii::t('app','on').'</span> '.date('dS M Y', $reviewModel->createdDate).'</div>
					<div class="padding-top-10"><a class="g-color" href="" data-toggle="modal" data-target="#write-review-modal">
						'.Yii::t('app','Edit review').'</a>
					</div>
					<input type="hidden" class="review-id" value="'.$reviewId.'" />
				</div>';
			echo $reviewDetails;
		}

		$sellerModel = Users::model()->with('reviewRating')->findByPk($orderModel->sellerId);
		$averageRatting = $sellerModel->reviewRating;
		$sellerModel->averageRating = $averageRatting;
		$sellerModel->save(false);

	}
}