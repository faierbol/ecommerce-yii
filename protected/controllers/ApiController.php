<?php
ini_set("soap.wsdl_cache_enabled", "0");
class ApiController extends Controller
{
	const PENDING = 0;
	const ACCEPT = 1;
	const DECLINE = 2;
	const CANCEL = 3;
	const SUCCESS = 4;
	const FAILED = 5;
	const SOLDOUT = 6;

	public $errorMessage;

	public function actions()
	{
		return array(
				'service'=>array(
						'class'=>'CWebServiceAction',
						),
				);
	}
	/* $api_username, $api_password, */
	//if ($this->authenticateAPI($api_username, $api_password)){

		// }else{
		// 	return '{"status":"false", "message":"Unauthorized Access to the API"}';
		// }
//$user_id ,$type  if ($this->authenticateAPI($api_username, $api_password)){

  //   public function actionLikedby($user_id = 4){
  //   	$criteria = new CDbCriteria;
		// $criteria->addCondition("userId = '$user_id'");
		// $favouriteModel = Favorites::model()->findAll($criteria);
		// $pcriteria = new CDbCriteria;
		// foreach ($favouriteModel as $favourite)
		// 	$likedproducts[] = $favourite->productId;
		// $pcriteria->addInCondition('productId', $likedproducts);
		// $favouriteModel = Products::model()->findAll($pcriteria);
		// //print_r($favouriteModel);
  //   }


	public function actionAccesssoap(){
		$client=new SoapClient('http://localhost:9002/joysale/api/service');
		echo '<pre>'; echo $client->payment("joySale","AJu8QFRw","1","2","95","1","L","JLC05gGM");
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @soap
	 */
	//public function checking($api_username, $api_password, $user_id){
	public function checking($user_id){

		$userId = $user_id;
		if (isset($userId) && $userId != ""){
			$userModel = Users::model()->findByAttributes(array('userId'=>$userId));
			if (empty($userModel)){

				$this->errorMessage = '{"status":"error","message":"User not registered yet"}';
				return false;

			}else{
				if ($userModel->userstatus == 0){

					$this->errorMessage = '{"status":"error","message":"Your account has been disabled by the Administrator"}';
					return false;

				}elseif(($userModel->userstatus == 0 || $userModel->activationStatus == 0)) {

					$this->errorMessage = '{"status":"error","message":"Please activate your account by the email sent to you"}';
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function checkItemstatus($api_username, $api_password,$item_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			if(isset($item_id))
			{
				$productId = $item_id;
				$productModel = Products::model()->findByPk($productId);
				if(isset($productModel->approvedStatus) && $productModel->approvedStatus == "1")
				{
					$item_approve = "1";
				}
				else
				{
					$item_approve = "0";
				}
				return '{"status":"true","item_approve":'.$item_approve.'}';
			}
			else
			{
				return '{"status":"false", "message":"Something went wrong"}';
			}
		}
		else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the shipping_id
	 * @param string the item_id
	 * @param string the quantity
	 * @param string the size
	 * @param string the coupon_id
	 * @return string the json data
	 * @soap
	 */
	public function payment($api_username, $api_password, $user_id,$shipping_id, $item_id,$quantity,$size,$coupon_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				$productId = $item_id;
				$productModel = Products::model()->findByAttributes(array('productId'=>$productId));
				if(!empty($productModel)) {
					$sellerModel = Myclass::getUserDetails($productModel->userId);
					$userModel = Users::model()->findByPk($userId);
					$shippingAddressesModel = Tempaddresses::model()->findByAttributes(array('shippingaddressId'=>$shipping_id));
					if(!empty($shippingAddressesModel)) {
						$countryCode = $shippingAddressesModel->countryCode;
						$shippingFlag = 0;
						foreach ($productModel->shippings as $shippingModel){
							if ($shippingModel->countryId == $countryCode){
								$shippingPrice = $shippingModel->shippingCost;
								$shippingFlag = 1;
							}elseif ($shippingModel->countryId == 0 && $shippingFlag == 0){
								$shippingPrice = $shippingModel->shippingCost;
							}
						}
						$siteSettings = Sitesettings::model()->find();
						$paypalSettings = json_decode($siteSettings->paypal_settings, true);

						if ($size == ''){
							$itemPrice = $productModel->price;
						}else{
							$options = json_decode($productModel->sizeOptions,true);
							$optionDetails = $options[$size];
							if ($optionDetails['price'] != ''){
								$itemPrice = $optionDetails['price'];
							}else{
								$itemPrice = $productModel->price;
							}
						}
						$productPrice = $itemPrice;

						$discount = 0;
						$productDetails['couponId'] = "";
						$finalPrice = $itemPrice * $quantity;
						if (!empty($coupon_id)){
							$couponModel = Coupons::model()->findByAttributes(array('id'=>$coupon_id));
							$couponType = $couponModel->couponType;
							$productDetails['couponId'] = $couponModel->id;
							if($couponType == "1") {
								$discount = $quantity * $couponModel->couponValue;
							} else {
								$discount = ($itemPrice * $quantity) * ($couponModel->couponValue / 100);
								if ($couponModel->maxAmount != 0 && $couponModel->maxAmount < $discount){
									$discount = $couponModel->maxAmount;
								}
							}
							$finalPrice = $finalPrice - $discount;
						}

						if(!empty($shippingPrice)) {
							$finalPrice = $finalPrice + $shippingPrice;
						}
						$productDetails['shippingId'] = $shipping_id;
						$productDetails['quantity'] = $quantity;
						$productDetails['options'] = $size;
						$productDetails['shippingPrice'] = $shippingPrice;
						$productDetails['discount'] = $discount;

						$result['ipnUrl'] = Yii::app()->createAbsoluteUrl('/ipnprocess');
						$result['memo'] = $userModel['email']."-_-".$shipping_id."-_-".$size."-_-".$coupon_id;

						$result['adminEmail'] = $paypalSettings['paypalEmailId'];

						$cur = explode("-",$productModel->currency);
						$result['currencyCode'] = $cur[0];
						$result['currencySymbol'] = $cur[1];

						$result['grandTotal'] = $finalPrice;

						$result['discountAmount'] = $discount;
						$result['itemName'] = $productModel->name;
						$result['itemPrice'] = $productPrice;
						$result['itemSize'] = $size;
						$result['itemShip'] = $shippingPrice;
						$result['itemCount'] = $quantity;
						$result['identifier'] = $productModel->productId;

						$final = json_encode($result);
						return '{"status": "true","result":'.$final.'}';
					} else {
						return '{"status":"false", "message":"There is no Shipping address defined for your account."}';
					}
				} else {
					return '{"status":"false", "message":"Item Not Found."}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	public function authenticateAPI($username, $password){
		if($username != "" && $password != ""){
			$id = 1;
			$sitesettingsModel = Sitesettings::model()->findByPk($id);
			$apiDetails = json_decode($sitesettingsModel->api_settings, true);
			$apiUsername = $apiDetails['apicredential']['current']['username'];
			$apiPassword = $apiDetails['apicredential']['current']['password'];

			if($username == $apiUsername && $password == $apiPassword){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the email
	 * @param string the password
	 * @return string the json data
	 * @soap
	 */
	public function login($api_username, $api_password, $email, $password)
	{
		if ($this->authenticateAPI($api_username, $api_password)){

			$userModel = Users::model()->findByAttributes(array('email'=>$email));
			if (!empty($userModel)){
				$encryptPassword = base64_encode($password);
				if ($encryptPassword == $userModel->password){
					if ($userModel->userstatus == 1){
						if ($userModel->activationStatus == 1){
							if(!empty($userModel->userImage)){
								$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
							}else{
								$userImage = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
							}
							return '{"status":"true","user_id":"'.$userModel->userId.'","email":"'.$userModel->email.'", "full_name":"'.$userModel->name.'","user_name":"'.
							$userModel->username.'","full_name":"'.$userModel->name.'",
									"email":"'.$userModel->email.'","photo":"'.$userImage.'","rating":"'.$userModel->averageRating.'"}';
						}else{
							return '{"status":"false","message":"Please activate your account by the email sent to you"}';
						}
					}else{
						return '{"status":"false","message":"Your account has been blocked by admin"}';
					}
				}else{
					return '{"status":"false","message":"Please enter correct email and password"}';
				}
			}else{
				return '{"status":"false","message":"User not registered yet"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_name
	 * @param string the full_name
	 * @param string the email
	 * @param string the password
	 * @return string the json data
	 * @soap
	 */
	public function signup($api_username, $api_password, $user_name, $full_name, $email, $password){
	/*public function actionSignup(){
		$user_name = $_POST['user_name'];
		$full_name = $_POST['full_name'];
		$email = $_POST['email'];
		$password= $_POST['password'];
		$api_username = $_POST['api_username'];
		$api_password = $_POST['api_password'];*/

		if ($this->authenticateAPI($api_username, $api_password)){
			$username = $user_name;
			$fullName = $full_name;
			$email = $email;
			$password = $password;

			$criteria = new CDbCriteria;
			$criteria->addCondition('email = "'.$email.'"');
			$criteria->addCondition('username = "'.$username.'"','OR');
			$userModel = Users::model()->find($criteria);
			if (empty($userModel)){
				$newUser = new Users();
				$newUser->username = $username;
				$newUser->name = $fullName;
				$newUser->password = base64_encode($password);
				$newUser->email = $email;
				$newUser->userstatus = 1;
			//	$newUser->activationStatus = 0;

				$emailTo = $newUser->email;
				$verifyLink = Yii::app()->createAbsoluteUrl('/verify/'.base64_encode($email));
				if ($newUser->save(false)){
					$siteSettings = Sitesettings::model()->find();
					if($siteSettings->signup_active=='yes')
					{

					$mail = new YiiMailer();
					if($siteSettings->smtpEnable == 1) {
						//$mail->IsSMTP();                         // Set mailer to use SMTP
						$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
						$mail->Host = $siteSettings->smtpHost; //'smtp.gmail.com';  // Specify main and backup server
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
						$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
						if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
						$mail->Port = $siteSettings->smtpPort; // 465;
					}
					$mail->setView('signup');
					$mail->setData(array('access_url' => $verifyLink, 'name' => $newUser->name,
							'siteSettings' => $siteSettings));
					$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
					$mail->setTo($emailTo);
					$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Welcome Mail'));
					$mail->send();
					return '{"status":"true","message":"An email was sent to your mail box, please activate your account and login."}';
					}
					else
					{
						$newUser->activationStatus = 1;
						$newUser->save(false);
						return '{"status":"true","message":"account has been created, Amazing products are waiting for you, kindly login."}';
					}

				}else{
					return '{"status":"false", "message":"Sorry, unable to create user, please try again later"}';
				}
			}else{
				if ($userModel->email == $email){
					return '{"status":"false","message":"Email already exists"}';
				}elseif($userModel->username == $username){
					return '{"status":"false","message":"Username already exists"}';
				}
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the type
	 * @param string the id
	 * @param string the first_name
	 * @param string the last_name
	 * @param string the email
	 * @param string the image_url
	 * @return string the json data
	 * @soap
	 */
	public function sociallogin($api_username, $api_password, $type, $id, $first_name, $last_name, $email,
	$image_url){
		if ($this->authenticateAPI($api_username, $api_password)){
			//$type = $_POST['type'];
			$socialId = $id;
			$fullName = $first_name." ".$last_name;
			$userName = $first_name.$last_name;
			//$email = $_POST['email'];
			//$image_url = $_POST['image_url'];

			if($email != "" && $type == 'twitter'){
				$userEmailCheck = Users::model()->findByAttributes(array('email'=>$email));
				if(!empty($userEmailCheck))
					return '{"status":"false", "message":"Email Already Exist"}';
			}

			$criteria = new CDbCriteria;
			if($email != "")
				$criteria->addCondition("email = '$email'");
			if ($type == 'facebook'){
				$criteria->addCondition("facebookId = ".$socialId,'OR');
			}elseif($type == 'twitter'){
				$criteria->addCondition("twitterId = ".$socialId,'OR');
			}else{
				$criteria->addCondition("googleId = ".$socialId,'OR');
			}
			$userModel = Users::model()->find($criteria);
			if (empty($userModel) && $email != ""){
				$imageName = Myclass::getImagefromURL($image_url);
				$imageUrl = Yii::app()->createAbsoluteUrl('/user/resized/40x40/'.$imageName);
				$userModel = new Users();
				$userModel->name = $fullName;
				$userModel->username = "";
				$userModel->password = "";
				$userModel->email = $email;
				$userModel->userstatus = 1;
				$userModel->activationStatus = 1;

				if($userModel->userImage == ''){
					$userModel->userImage = $imageName;
				}
				if ($type == 'facebook'){
					$userModel->facebookId = $socialId;
				}elseif($type == 'twitter'){
					$userModel->twitterId = $socialId;
				}else{
					$userModel->googleId = $socialId;
				}
				$userModel->save(false);
				$userModel->username = $userName.$userModel->userId;
				$userModel->save(false);

							if(!empty($userModel->userImage)){
								$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
							}else{
								$userImage = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
							}

				return '{"status":"true","user_id":"'.$userModel->userId.'", "user_name":"'.
						$userModel->username.'","full_name":"'.$userModel->name.'","email":"'.$userModel->email.'","photo":"'.
						$userImage.'","rating":"'.$userModel->averageRating.'"}';
			}elseif (!empty($userModel) && $userModel->userstatus == 1){
				if ($type == 'facebook'){
					$userModel->facebookId = $socialId;
					$userModel->userstatus = 1;
				}elseif($type == 'twitter'){
					$userModel->twitterId = $socialId;
					$userModel->userstatus = 1;
				}else{
					$userModel->googleId = $socialId;
					$userModel->userstatus = 1;
				}
				$userModel->save(false);

							if(!empty($userModel->userImage)){
								$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
							}else{
								$userImage = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
							}

				return '{"status":"true","user_id":"'.$userModel->userId.'",
						"user_name":"'.$userModel->username.'","full_name":"'.$userModel->name.'","email":"'.$userModel->email.'","photo":"'.
						$userImage.'","rating":"'.$userModel->averageRating.'"}';
			}elseif (empty($userModel) && $email == ""){
				return '{"status":"false", "message":"Account not found"}';
			}else{
				return '{"status":"false","message":"Your account has been blocked by admin"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the email
	 * @return string the json data
	 * @soap
	 */
	public function forgetpassword($api_username, $api_password, $email) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$criteria = new CDbCriteria;
			$criteria->addCondition("email='$email'");
			$criteria->addCondition("userstatus != 2");
			$userModel = Users::model()->find($criteria);
			if(empty($userModel)){
				return '{"status":"false","message":"User not found"}';
			}else{
				$resetPasswordCheck = Resetpassword::model()->findByAttributes(array(
										'userId'=>$userModel->userId));
				if($userModel->userstatus == 1 && $userModel->activationStatus == 1) {
					if (empty($resetPasswordCheck)){
						$createdDate = time();
						$randomValue = rand(10000, 100000);
						$resetPasswordData = base64_encode($userModel->userId."-".$createdDate."-".$randomValue);
						$resetPasswordModel = new Resetpassword();
						$resetPasswordModel->userId = $userModel->userId;
						$resetPasswordModel->resetData = $resetPasswordData;
						$resetPasswordModel->createdDate = $createdDate;

						$resetPasswordModel->save();
					}else{
						$resetPasswordData = $resetPasswordCheck->resetData;
					}
					if(!empty($resetPasswordData)) {
						$resetPasswordLink = Yii::app()->createAbsoluteUrl('/resetpassword?resetLink='.$resetPasswordData);
						$siteSettings = Sitesettings::model()->find();
						$mail = new YiiMailer();
						if($siteSettings->smtpEnable == 1) {
							//$mail->IsSMTP();                         // Set mailer to use SMTP
							$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
							$mail->Host = $siteSettings->smtpHost; //'smtp.gmail.com';  // Specify main and backup server
							$mail->SMTPAuth = true;                               // Enable SMTP authentication
							$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
							$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
							if($siteSettings->smtpSSL == 1)
								$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
							$mail->Port = $siteSettings->smtpPort; //465;
						}
						$mail->setView('forgetpassword');
						$mail->setData(array('uniquecode_pass' => $resetPasswordLink, 'name' => $userModel->name,
								'siteSettings' => $siteSettings));
						$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
						$mail->setTo($userModel->email);
						$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Forget Password Request'));
						$mail->send();

						return '{"status":"true","message":"Reset password link has been mailed to you"}';
					}
				}elseif($userModel->userstatus == 0 && $userModel->activationStatus == 0) {
					return '{"status":"error","message":"Your account has been disabled by the Administrator"}';
				}else {
					return '{"status":"true","message":"User not verified yet, activate the account from the email"}';
				}
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the old_password
	 * @param string the new_password
	 * @return string the json data
	 * @soap
	 */
	public function changepassword($api_username, $api_password, $user_id, $old_password, $new_password){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userModel = Users::model()->findByPk($user_id);
				$oldPassword = base64_decode($userModel->password);
				if($old_password == $new_password) {
					return '{"status":"false","message":"Old Password and new password are same, Please enter different one!"}';
				}
				if($oldPassword == $old_password){
					$newPassword = base64_encode($new_password);
					$userModel->password = $newPassword;
					$userModel->save();
					return '{"status":"true","message":"Password Changed Successfully"}';
				}else{
					return '{"status":"false","message":"Old Password Incorrect"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	public function actionGetimage(){
		$url = $_POST['urlimage'];
		$imageName = Myclass::getImagefromURL($url);

		echo Yii::app()->baseUrl."/user/resized/40x40/".$imageName;
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the lang_type
	 
	 * @return string the json data
	 * @soap
	 */
	public function getcategory($api_username, $api_password,$lang_type){
		if ($this->authenticateAPI($api_username, $api_password)){
			Myclass::push_lang($lang_type);
			$siteSettings = Sitesettings::model()->find();
			$maincategoryModel = Categories::model()->findAllByAttributes(array('parentCategory'=>'0'));
			$criteria = new CDbCriteria;
			$criteria->addCondition("parentCategory != 0");
			$subcategoryModel = Categories::model()->findAll($criteria);
			$categories = array();
			$subcategories = array();
			$maincategoryImage = array();
			foreach ($subcategoryModel as $subcategory){
				$subcategories[$subcategory->parentCategory][$subcategory->categoryId] = $subcategory->name;
			}

			foreach ($maincategoryModel as $catkey => $maincategory){
				$imageUrl = Yii::app()->createAbsoluteUrl('/admin/categories/resized/40/'.$maincategory->image);
				$categories['category'][$catkey]['category_id'] = $maincategory->categoryId;
				$categories['category'][$catkey]['category_name'] = Yii::t('app',$maincategory->name);
				$categories['category'][$catkey]['category_img'] = $imageUrl;
				$categories['category'][$catkey]['subcategory'] = array();
				if (isset($subcategories[$maincategory->categoryId])){
					$relatedSubcategory = $subcategories[$maincategory->categoryId];
					$relatedkey = 0;
					foreach ($relatedSubcategory as $relatedCategorykey => $relatedCategory){
						$categories['category'][$catkey]['subcategory'][$relatedkey]['sub_id'] = "$relatedCategorykey";
						$categories['category'][$catkey]['subcategory'][$relatedkey]['sub_name'] =Yii::t('app',$relatedCategory);
						$relatedkey++;
					}
				}
			}
			return '{"status": "true","result":'.json_encode($categories).'}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the type.
	 * @param string the price.
	 * @param string the search_key.
	 * @param string the category_id.
	 * @param string the subcategory_id.
	 * @param string the user_id.
	 * @param string the item_id.
	 * @param string the seller_id.
	 * @param string the sorting_id.
	 * @param string the offset.
	 * @param string the limit.
	 * @param string the lat.
	 * @param string the lon.
	 * @param string the posted_within.
	 * @param string the distance.
	 * @param string the distance_type.
	 * @param string the lang_type.
	 
	 * @return string the json data
	 * @soap
	 */
	public function getItems($api_username, $api_password,$type , $price = NULL, $search_key = NULL,
	$category_id = 0, $subcategory_id = NULL, $user_id = 0, $item_id = 0, $seller_id = NULL, $sorting_id = 0, $offset = 0,	$limit = 20, $lat = NULL, $lon = NULL, $posted_within = NULL, $distance = NULL, $distance_type, $lang_type){
	/*public function actionGetitems() {
		$type = $_POST['type'];
		$price = $_POST['price'];
		$search_key = $_POST['search_key'];
		$category_id = $_POST['category_id'];
		$user_id = $_POST['user_id'];
		$item_id = $_POST['item_id'];
		$seller_id = $_POST['seller_id'];
		$sorting_id = $_POST['sorting_id'];
		$offset = $_POST['offset'];
		$limit = $_POST['limit'];
		$lat = $_POST['lat'];
		$lon = $_POST['lon'];
		$distance = $_POST['distance'];
		$api_username = $_POST['api_username'];
		$api_password = $_POST['api_password'];**/
		if ($this->authenticateAPI($api_username, $api_password)){
			Myclass::push_lang($lang_type);
			if($this->checking($user_id)){
			//$limit = 20;
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				//$offset = 0;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				$criteria = new CDbCriteria();
				$criteria->order = 'productId DESC';
				$criteria->limit = $limit;
				$criteria->offset = $offset;
				if($type == 'liked'){
					$likecriteria = clone $criteria;
					$likecriteria->addCondition("userId = '$user_id'");
					$favouriteModel = Favorites::model()->findAll($likecriteria);
					foreach ($favouriteModel as $favourite) {
						$likedproducts[] = $favourite->productId;
					}
					$criteria->addInCondition('productId', $likedproducts);
				}
				if ($type == 'home' || $type == 'search'){
					if ($sorting_id != 0){
						switch ($sorting_id){
							case 1:
								$criteria->order = "productId DESC";
								break;
							case 2:
								$criteria->order = 'likes DESC';
								break;
							case 3:
								$criteria->order = 'price DESC';
								break;
							case 4:
								$criteria->order = 'price ASC';
								break;
							case 5:
								$criteria->addCondition('promotionType = 2');
								break;
						}

					}
					$criteria->addCondition('approvedStatus = 1');
				}
				if ($type == 'moreitems'){
					if(isset($user_id) && isset($seller_id) && $user_id != $seller_id)
					{
						$criteria->addCondition('approvedStatus = 1');
					}
					if (!empty($seller_id)){
						$sellerId = $seller_id;
						$criteria->addCondition('userId = '.$sellerId);
						if ($item_id != 0){
							$itemId = $item_id;
							$criteria->addCondition('productId != '.$itemId);
						}
					}else{

					}
				}elseif ($type == 'search'){
					/* if ($user_id != 0){
						$criteria->addCondition('userId = '.$user_id);
					} */
					if ($category_id != ''){
						if (strpos($category_id, ',') === false) {
						   $criteria->addCondition('category = '.$category_id);
						}else{
							$category_ids = explode(",",$category_id);
							$criteria->addInCondition('category', $category_ids);
						}

					}
					if ($subcategory_id != ''){
						if (strpos($subcategory_id, ',') === false) {
						   $criteria->addCondition('subCategory = '.$subcategory_id);
						}else{
							$subcategory_ids = explode(",",$subcategory_id);
							$criteria->addInCondition('subCategory', $subcategory_ids);
						}
					}

					if (!empty($search_key)){
						$criteria->addCondition('name LIKE "%'.$search_key.'%"');
					}
					if (!empty($price)){
						$priceRange = explode('-', $price);
						if(count($priceRange) > 1)
						$criteria->addBetweenCondition('price', $priceRange[0], $priceRange[1]);
						else
						$criteria->addCondition('price <= '.$priceRange[0]);
					}
					if(!empty($posted_within)){
						$date = date();
						if($posted_within == 'last24h'){
							$prev_date = strtotime($date .' -1 day');
							$criteria->addCondition('createdDate >= '.$prev_date);

						}elseif($posted_within == 'last7d'){
							$prev_week = strtotime($date .' -7 day');
							$criteria->addCondition('createdDate >= '.$prev_week);

						}elseif($posted_within == 'last30d'){
							$prev_month = strtotime($date .' -30 day');
							$criteria->addCondition('createdDate >= '.$prev_month);
						}
					}
					if( !empty($lat) && !empty($lon) ){
						
						/* convert km to mile calculation */
						
						if(!empty($distance_type))
						{ 
							if($distance_type=='mi')
								{
									$value=0.621371; // 1km = 0.621371 miles 
								} 
							else 
								{ 
									$value=1;
								}
						}
						else
						{
							$value=1;
						}
						/* convert km to mile calculation */

						if(!empty($distance)){
							$distance = ($distance * $value) * 0.1 / 11;
						}else{
							$default=25;// default 25 km
							$distance = ($default * $value) * 0.1 / 11;
						}

						$LatN = $lat + $distance;
						$LatS = $lat - $distance;
						$LonE = $lon + $distance;
						$LonW = $lon - $distance;
						//echo "North:".$LatN." Southlat:".$LatS." West:".$LonW." Eastlon:".$LonE;
						$criteria->addBetweenCondition('longitude', $LonW, $LonE);
						$criteria->addBetweenCondition('latitude', $LatS, $LatN);

					}
					$criteria->addCondition('approvedStatus = 1');
				}

				$itemModel = Products::model()->findAll($criteria);
				//print_r($criteria);
				if (!empty($itemModel)){
					if ($user_id != 0){
						$result = $this->convertJsonItems($itemModel, $user_id);
					}else{
						$result = $this->convertJsonItems($itemModel);
					}
					return '{"status": "true","result":'.$result.'}';
				}else{
					return '{"status":"false","message":"No item found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}

	}

	public function convertJsonItems($itemModel, $userId = 0){
		$items = array();
		foreach ($itemModel as $itemkey => $item){
			$productId = $item->productId;
			$likedornot = $this->checkuserlike($userId, $productId);
			$items['items'][$itemkey]['id'] = $item->productId;
			$items['items'][$itemkey]['item_title'] = $item->name;
			$items['items'][$itemkey]['item_description'] = html_entity_decode($item->description);
			$items['items'][$itemkey]['item_condition'] = Yii::t('app',$item->productCondition);
			$items['items'][$itemkey]['price'] = $item->price;
			$items['items'][$itemkey]['quantity'] = $item->quantity;
			if($item->quantity > 0 && $item->soldItem == 0){
				$items['items'][$itemkey]['item_status'] = "onsale";
			}else{
				$items['items'][$itemkey]['item_status'] = "sold";
			}
			$items['items'][$itemkey]['size'] = "";
			if($item->sizeOptions != ''){
				$sizeOptions = json_decode($item->sizeOptions, true);
				$size = array();
				$sizeKey = 0;
				foreach($sizeOptions as $sizeOption){
					$size[$sizeKey]['name'] = $sizeOption['option'];
					$size[$sizeKey]['qty'] = $sizeOption['quantity'];
					$size[$sizeKey]['price'] = $sizeOption['price'];
					$sizeKey++;
				}
				$items['items'][$itemkey]['size'] = $size;
			}
			$items['items'][$itemkey]['seller_name'] = $item->user->name;
			$items['items'][$itemkey]['seller_username'] = $item->user->username;
			$items['items'][$itemkey]['seller_id'] = $item->user->userId;
			if($item->user->userImage == "")
				$items['items'][$itemkey]['seller_img'] = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
			else
				$items['items'][$itemkey]['seller_img'] = $userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$item->user->userImage);
			$items['items'][$itemkey]['seller_rating'] = $item->user->averageRating;
			$items['items'][$itemkey]['mobile_no'] = $item->user->phone;
			if($item->user->phonevisible == "1")
			{
				$items['items'][$itemkey]['show_seller_mobile'] = "true";
			}
			else
			{
				$items['items'][$itemkey]['show_seller_mobile'] = "false";
			}

			if($item->user->facebookId == ''){
				$items['items'][$itemkey]['facebook_verification'] = 'false';
			}else{
				$items['items'][$itemkey]['facebook_verification'] = 'true';
			}
			if($item->user->mobile_status == '1'){
				$items['items'][$itemkey]['mobile_verification'] = 'true';
			}else{
				$items['items'][$itemkey]['mobile_verification'] = 'false';
			}

			$items['items'][$itemkey]['email_verification'] = 'true';

			$items['items'][$itemkey]['currency_code'] = $item->currency;
			$items['items'][$itemkey]['product_url'] = Yii::app()->createAbsoluteUrl("item/products/view",
					array('id' => Myclass::safe_b64encode($item->productId.'-'.rand(0,999)))).'/'.
					Myclass::productSlug($item->name);
			//Yii::app()->createAbsoluteUrl('/products/'.$item->productId);
			$items['items'][$itemkey]['likes_count'] = $item->likes;
			$items['items'][$itemkey]['comments_count'] = $item->commentCount;
			$items['items'][$itemkey]['views_count'] = $item->views;

			$items['items'][$itemkey]['liked'] = $likedornot;

			$items['items'][$itemkey]['report'] = "no";
			if ($item->reports != ''){
				$reports = json_decode($item->reports, true);
				if(in_array($userId, $reports)){
					$items['items'][$itemkey]['report'] = "yes";
				}
			}
//			$items['items'][$itemkey]['posted_time'] = Myclass::getElapsedTime($item->createdDate)." ago";
			$items['items'][$itemkey]['posted_time'] = $item->createdDate;
			$items['items'][$itemkey]['latitude'] = $item->latitude;
			$items['items'][$itemkey]['longitude'] = $item->longitude;
			$items['items'][$itemkey]['location'] = $item->location;
			$items['items'][$itemkey]['shipping_time'] = $item->shippingTime;
			$items['items'][$itemkey]['best_offer'] = "false";
			$buyType = "";
			if ($item->chatAndBuy){
				$buyType .= "contactme";
			}
			if($item->exchangeToBuy){
				$buyType .= $buyType == "" ? "swap" : ",swap";
			}
			if($item->instantBuy){
				$buyType .= $buyType == "" ? "sale" : ",sale";
			}
			$items['items'][$itemkey]['buy_type'] = $buyType;
			$items['items'][$itemkey]['paypal_id'] = $item->paypalid;
			//echo "<pre>";print_r($item->subCategory0);print_r($item->shippings);die;
			if(isset($item->category0)){
				$items['items'][$itemkey]['category_id'] = $item->category0->categoryId;
				$items['items'][$itemkey]['category_name'] = Yii::t('app',$item->category0->name);
			}else{
				$items['items'][$itemkey]['category_id'] = "";
				$items['items'][$itemkey]['category_name'] = "";
			}
			if(isset($item->subCategory0)){
				$items['items'][$itemkey]['subcat_id'] = $item->subCategory0->categoryId;
				$items['items'][$itemkey]['subcat_name'] = Yii::t('app',$item->subCategory0->name);
			}else{
				$items['items'][$itemkey]['subcat_id'] = "";
				$items['items'][$itemkey]['subcat_name'] = "";
			}

			$items['items'][$itemkey]['promotion_type'] = 'Normal';
			if($item->promotionType == '3'){
				$items['items'][$itemkey]['promotion_type'] = "Normal";
			}elseif ($item->promotionType == '1') {
				$items['items'][$itemkey]['promotion_type'] = "Ad";
			}elseif ($item->promotionType == '2') {
				$items['items'][$itemkey]['promotion_type'] = "Urgent";
			}

			if(isset($item->approvedStatus) && $item->approvedStatus == "1")
			{
				$items['items'][$itemkey]['item_approve'] = "1";
			}
			else
			{
				$items['items'][$itemkey]['item_approve'] = "0";
			}

			/*$productModels['items'][$itemkey]['exchange_buy'] = '';
			if($item->exchangeToBuy == '0'){
				$items['items'][$itemkey]['exchange_buy'] = "false";
			}else{
				$items['items'][$itemkey]['exchange_buy'] = "true";
			}

			$items['items'][$itemkey]['make_offer'] = '';
			if($item->myoffer == '1' || $item->myoffer == '2'){
				$items['items'][$itemkey]['make_offer'] = "true";
			}else{
				$items['items'][$itemkey]['make_offer'] = "false";
			}*/
			$items['items'][$itemkey]['exchange_buy'] = $item->exchangeToBuy;
			$items['items'][$itemkey]['make_offer'] = $item->myoffer;
			$items['items'][$itemkey]['instant_buy'] = $item->instantBuy;

			if($item->instantBuy == "1")
			{
				$items['items'][$itemkey]['country_id'] = $item->shippingcountry;
				$items['items'][$itemkey]['paypal_id'] = $item->paypalid;
				$items['items'][$itemkey]['shipping_cost'] = $item->shippingCost;
			}
			else
			{
				$items['items'][$itemkey]['country_id'] = "";
				$items['items'][$itemkey]['paypal_id'] = "";
				$items['items'][$itemkey]['shipping_cost'] = "";
			}

			/*$items['items'][$itemkey]['shipping_detail'] = array();
			if($item->instantBuy){
				$shipKey = 0;
				$shippingArray = array();
				foreach($item->shippings as $shipping){
					$shippingArray[$shipKey]['country_id'] = $shipping->countryId;
					$shippingArray[$shipKey]['country_name'] = $shipping->country->country;
					$shippingArray[$shipKey]['shipping_cost'] = $shipping->shippingCost;
					$shipKey++;
				}
				$items['items'][$itemkey]['shipping_detail'] = $shippingArray;
			}*/
			$items['items'][$itemkey]['photos'] = array();
			foreach ($item->photos as $photo){
				$photoName = $photo->name;

				 $photodetails['item_url_main_350'] = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId.'/'.$photoName);
       			 $photodetails['height'] = '350';
       			 $photodetails['width'] = '350';
				 $photodetails['item_url_main_original'] = Yii::app()->createAbsoluteUrl('media/item/'.$productId.'/'.$photoName);
				 $items['items'][$itemkey]['photos'][] = $photodetails;
			}
		}
		return json_encode($items);
	}

	public function checkuserlike($userId, $itemId){
		if($userId == '0'){
			return "no";
		}else{
			$favouriteModel = Favorites::model()->findAllByAttributes(array('userId'=>$userId,
					'productId'=>$itemId));
			if (empty($favouriteModel)){
				return "no";
			}else{
				return "yes";
			}
		}
	}



	public function liked($api_username, $api_password, $user_id, $offset = 0, $limit = 20){

		if ($this->authenticateAPI($api_username, $api_password)){
		$userId = $user_id;

		if (isset($userId) && $userId != ""){
			$criteria = new CDbCriteria;
			$criteria->limit = $limit;
			$criteria->offset = $offset;
			$criteria->addCondition("userId = '$userId'");
			$favouriteModel = Favorites::model()->findAll($criteria);
		//	echo "<pre>"; print_r($favouriteModel); die;
			if (!empty($favouriteModel)){

				foreach ($favouriteModel as $favourite ) {
					$productid = $favourite['productId'];

					$criteria = new CDbCriteria;

					$criteria->addCondition("productId = '$productid'");
					$_productModel = Products::model()->findAll($criteria);

					if (count($_productModel) > 0){
						$productModel[] = $_productModel;
					}

				}

				foreach ($productModel as $itemkey => $item){
				$productId = $item['0']['productId'];
				$productModels['items'][$itemkey]['id'] = $item['0']['productId'];
				$productModels['items'][$itemkey]['item_title'] = $item['0']['name'];
				$productModels['items'][$itemkey]['item_description'] = $item['0']['description'];
				$productModels['items'][$itemkey]['item_condition'] = $item['0']['productCondition'];
				if($item['0']['quantity'] > 0 && $item['0']['soldItem'] == 0){
					$items['items'][$itemkey]['item_status'] = "onsale";
				}else{
					$items['items'][$itemkey]['item_status'] = "sold";
				}
				$productModels['items'][$itemkey]['price'] = $item['0']['price'];
				$productModels['items'][$itemkey]['quantity'] = $item['0']['quantity'];
				$productModels['items'][$itemkey]['size'] = "";
				if($item['0']['sizeOptions'] != ''){
					$sizeOptions = json_decode($item['0']['sizeOptions'], true);
					$size = array();
					$sizeKey = 0;
					foreach($sizeOptions as $sizeOption){
						$size[$sizeKey]['name'] = $sizeOption['option'];
						$size[$sizeKey]['qty'] = $sizeOption['quantity'];
						$size[$sizeKey]['price'] = $sizeOption['price'];
						$sizeKey++;
					}
					$productModels['items'][$itemkey]['size'] = $size;
				}

				$userid = $item['0']['userId'];
				$productModels['items'][$itemkey]['seller_name'] = $item[0]->user->name;
				$productModels['items'][$itemkey]['seller_id'] = $item['0']['userId'];
				$productModels['items'][$itemkey]['seller_img'] = $item[0]->user->userImage;
				if($item[0]->user->facebookId == ''){
					$productModels['items'][$itemkey]['facebook_verification'] = 'false';
				}else{
					$productModels['items'][$itemkey]['facebook_verification'] = 'true';
				}
				if($item[0]->user->mobile_status == '1'){
					$productModels['items'][$itemkey]['mobile_verification'] = 'true';
				}else{
					$productModels['items'][$itemkey]['mobile_verification'] = 'false';
				}

				$productModels['items'][$itemkey]['email_verification'] = 'true';
				$currency = $item['0']['currency'];
				//$currency = explode("-", $currency);

				$productModels['items'][$itemkey]['currency_code'] = $currency;
				$productModels['items'][$itemkey]['product_url'] = Yii::app()->createAbsoluteUrl("item/products/view",
					array('id' => Myclass::safe_b64encode($item['0']['productId'].'-'.rand(0,999)))).'/'.
					Myclass::productSlug($item['0']['name']);
				$productModels['items'][$itemkey]['likes_count'] = $item['0']['likeCount'];
				$productModels['items'][$itemkey]['comments_count'] = $item['0']['commentCount'];
				$productModels['items'][$itemkey]['views_count'] = $item['0']['views'];

				$productModels['items'][$itemkey]['report'] = "no";
				if ($item['0']['reports'] != ''){
					$reports = json_decode($item['0']['reports'], true);
					if(in_array($userId, $reports)){
						$productModels['items'][$itemkey]['report'] = "yes";
					}
				}

				$productModels['items'][$itemkey]['posted_time'] = Myclass::getElapsedTime($item['0']['createdDate'])." ago";
				$productModels['items'][$itemkey]['latitude'] = $item['0']['latitude'];
				$productModels['items'][$itemkey]['longitude'] = $item['0']['longitude'];
				$productModels['items'][$itemkey]['location'] = $item['0']['location'];
				$productModels['items'][$itemkey]['shipping_time'] = $item['0']['shippingTime'];
				$productModels['items'][$itemkey]['best_offer'] = "false";
				$buyType = "";
				if ($item['0']['chatAndBuy']){
					$buyType .= "contactme";
				}
				if($item['0']['exchangeToBuy']){
					$buyType .= $buyType == "" ? "swap" : ",swap";
				}
				if($item['0']['instantBuy']){
					$buyType .= $buyType == "" ? "sale" : ",sale";
				}
				$productModels['items'][$itemkey]['buy_type'] = $buyType;

				if($item['0']['instantBuy']){
					$shipKey = 0;
					$shippingArray = array();
					foreach($item['0']['shippings'] as $shipping){
						$shippingArray[$shipKey]['country_id'] = $shipping->countryId;
						$shippingArray[$shipKey]['country_name'] = $shipping->country->country;
						$shippingArray[$shipKey]['shipping_cost'] = $shipping->shippingCost;
						$shipKey++;
					}
					$productModels['items'][$itemkey]['shipping_detail'] = $shippingArray;
				}

				$productModels['items'][$itemkey]['paypal_id'] = $item['0']['paypalid'];

				if(isset($item[0]->category0)){
					$productModels['items'][$itemkey]['category_id'] = $item[0]->category0->categoryId;
					$productModels['items'][$itemkey]['category_name'] = $item[0]->category0->name;
				}else{
					$productModels['items'][$itemkey]['category_id'] = "";
					$productModels['items'][$itemkey]['category_name'] = "";
				}
				if(isset($item[0]->subCategory0)){
					$productModels['items'][$itemkey]['subcat_id'] = $item[0]->subCategory0->categoryId;
					$productModels['items'][$itemkey]['subcat_name'] = $item[0]->subCategory0->name;
				}else{
					$productModels['items'][$itemkey]['subcat_id'] = "";
					$productModels['items'][$itemkey]['subcat_name'] = "";
				}

				if($item['0']['promotionType'] == '3'){
					$productModels['items'][$itemkey]['promotion_type'] = "Normal";
				}elseif ($item['0']['promotionType'] == '1') {
					$productModels['items'][$itemkey]['promotion_type'] = "Ad";
				}elseif ($item['0']['promotionType'] == '2') {
					$productModels['items'][$itemkey]['promotion_type'] = "Urgent";
				}

				if($item['0']['exchangeToBuy'] == '0'){
					$productModels['items'][$itemkey]['exchange_buy'] = "false";
				}else{
					$productModels['items'][$itemkey]['exchange_buy'] = "true";
				}
				if($item['0']['myoffer'] == '0'){
					$productModels['items'][$itemkey]['make_offer'] = "false";
				}else{
					$productModels['items'][$itemkey]['make_offer'] = "true";
				}

				if($item['0']['productId'] != '' && $item['0']['productId'] != '0'){
				foreach ($item[0]->photos as $singleidphoto){
					$photoName = $singleidphoto->name;
					 $photodetails['item_url_main_350'] = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId.'/'.$photoName);
           			 $photodetails['height'] = '350';
           			 $photodetails['width'] = '350';
					 $photodetails['item_url_main_original'] = Yii::app()->createAbsoluteUrl('media/item/'.$productId.'/'.$photoName);
					 $productModels['items'][$itemkey]['photos'][] = $photodetails;
				} }else{
					 $photodetails['item_url_main_350'] = Yii::app()->createAbsoluteUrl("/item/products/resized/350/default.jpeg");
           			 $photodetails['height'] = '350';
           			 $photodetails['width'] = '350';
					 $photodetails['item_url_main_original'] = Yii::app()->createAbsoluteUrl('media/item/default.jpeg');
					 $productModels['items'][$itemkey]['photos'][] = $photodetails;
				}

				}

				$final = json_encode($productModels);
				return  '{"status":"true","result":'.$final.'}';


				}else{
					return '{"status":"false","message":"No item found"}';
				}
			}else{
				return '{"status":"false","message":"No Valid Users"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}

	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function updateview($api_username, $api_password, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$productModel = Products::model()->findByPk($item_id);
			$productModel->saveCounters(array('views'=>1));
			return '{"status":"true","result":"Successfully added views"}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function getcomments($api_username, $api_password, $item_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			$productId = $item_id;
			$commentModel = Comments::model()->findAllByAttributes(array('productId'=>$productId));
			if (count($commentModel) > 0){
				$comments = array();
				foreach($commentModel as $commentKey => $comment){
					$comments['comments'][$commentKey]["comment_id"] = $comment->commentId;
					$comments['comments'][$commentKey]["comment"] = $comment->comment;
					$comments['comments'][$commentKey]["user_id"] = $comment->user->userId;
					$comments['comments'][$commentKey]["user_full_name"] = $comment->user->name;
					if($comment->user->userImage == "")
						$comments['comments'][$commentKey]["user_img"] = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					else
						$comments['comments'][$commentKey]["user_img"] = Yii::app()->createAbsoluteUrl('user/resized/150/'.$comment->user->userImage);
					$comments['comments'][$commentKey]["user_name"] = $comment->user->username;
					$comments['comments'][$commentKey]["comment_time"] = $comment->createdDate;
				}
				return '{"status": "true","result":'.json_encode($comments).'}';
			}else{
				return '{"status":"false","message":"No comment found"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @return string the json data
	 * @soap
	 */
	public function getcountrycurrency($api_username, $api_password) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$countryModel = Country::model()->findAll();
			$currencyModel = Currencies::model()->findAll();
			$result = array();

			foreach ($currencyModel as $currencykey => $currency){
				$result['currency'][$currencykey]['id'] = $currency->id;
				$result['currency'][$currencykey]['symbol'] = $currency->currency_shortcode.
				"-".$currency->currency_symbol;
			}

			foreach ($countryModel as $countrykey => $country){
				$result['country'][$countrykey]['country_id'] = $country->countryId;
				$result['country'][$countrykey]['country_name'] = $country->country;
			}
			return '{"status": "true","result":'.json_encode($result).'}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the comment
	 * @param string the user_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function postcomment($api_username, $api_password, $comment, $user_id, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				if(isset($comment) && $comment != ''){
					$newComment = new Comments();
					$newComment->userId = $user_id;
					$newComment->productId = $item_id;
					$newComment->comment = $comment;
					$newComment->createdDate = time();
					$newComment->save();


					$productModel = Products::model()->findByPk($item_id);
					$userModel = Users::model()->findByPk($user_id);
					$productModel->commentCount = $productModel->commentCount + 1;
					$productModel->save(false);
					if($user_id != $productModel->userId)
					{
						$notifyMessage = 'comment on your product';
						Myclass::addLogs("comment", $user_id, $productModel->userId, $newComment->commentId, $productModel->productId, $notifyMessage);
					}

					if(!empty($userModel->userImage)){
						$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
					}else{
						$userImage = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					}

					$userid = $productModel->userId;
					$criteria = new CDbCriteria;
					$criteria->addCondition('user_id = "'.$userid.'"');
					$userdevicedet = Userdevices::model()->findAll($criteria);

					$userModel = Users::model()->findByPk($user_id);
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
								 Myclass::push_lang($lang);

								$text = 'comment on your product';
								$msg = Yii::t('app',$text);

									$messages = $userModel->username." ".$msg." ".$productModel->name;
									if($user_id != $productModel->userId)
									{
										Myclass::pushnot($deviceToken,$messages,$badge);
									}
							}
						}
					}

					return '{"status":"true","comment_id":"'.$newComment->commentId.'","comment":"'.
					$newComment->comment.'","user_id":"'.$userModel->userId.'","user_img":"'.
					$userImage.'","user_name":"'.$userModel->username.'","comment_time": "'.$newComment->createdDate.'"}';
				}else{
					return '{"status":"false","message":"Comment Empty"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @return string the json data
	 * @soap
	 */
	public function getsettings($api_username, $api_password, $user_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				if(isset($user_id)){
					$userModel = Users::model()->findByPk($user_id);
					if(empty($userModel)){
						return '{"status":"false","message":"No user found"}';
					}else{
						return '{"status":"true","result":{"user_name":"'.$userModel->username.'","full_name":"'.
						$userModel->name.'","user_img":"'.$userModel->userImage.'","email":"'.
						$userModel->email.'"}}';
					}
				}else{
					return '{"status":"false","message":"No user found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	public function actionUploadimage($type){
		@$ftmp = $_FILES['images']['tmp_name'];
		@$oname = $_FILES['images']['name'];
		@$fname = $_FILES['images']['name'];
		@$fsize = $_FILES['images']['size'];
		@$ftype = $_FILES['images']['type'];

		if($type == 'item'){
			$user_image_path = "media/item/tmp/";
		}else{
			$user_image_path = "media/user/";
		}

		$ext = strrchr($oname, '.');
		if($ext){
			if(($ext != '.JPG' && $ext != '.PNG' && $ext != '.JPEG' && $ext != '.GIF' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg' && $ext != '.gif') || $fsize > 200*1024*1024){
				//echo 'error';
			}else{
				if(isset($ftmp)){
					$newname = time().$ext;
					$newimage = $user_image_path . $newname;

					$result = move_uploaded_file($ftmp,$newimage);
					chmod($newimage, 0666);
					if(empty($result)){
						$error["result"] = "There was an error moving the uploaded file.";
						echo '{"status":"false","message":"Image cannot be uploaded"}';
					}else{
						echo '{"status":"true",
									"Image":{
										"Message":"Image Upload Successfully",
										"Name" :"'.$newname.'",
										"View_url" :"'.Yii::app()->createAbsoluteUrl($newimage).'"
									}
								}';
					}
				}
			}
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the type
	 * @param string the name
	 * @return string the json data
	 * @soap
	 */
	public function removeimage($api_username, $api_password, $type, $name){
		if ($this->authenticateAPI($api_username, $api_password)){
			if($type == 'item'){
				$user_image_path = "media/item/tmp/";
				//Photos::model()->deleteAllByAttributes(array('productId'=>$_POST['item_id'],
				//		'name'=>$_POST['name']));
			}else{
				$user_image_path = "media/user/";
			}
			$user_image_path .= $name;
			if(unlink($user_image_path)){
				return '{"status":"true", "message":"Image deleted successfully"}';
			}else{
				return '{"status":"false","message":"Image cannot be deleted"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @param string the item_name
	 * @param string the item_des
	 * @param string the price
	 * @param string the size
	 * @param string the item_condition
	 * @param string the category
	 * @param string the subcategory
	 * @param string the chat_to_buy
	 * @param string the exchange_to_buy
	 * @param string the instant_buy
	 * @param string the paypal_id
	 * @param string the currency
	 * @param string the lat
	 * @param string the lon
	 * @param string the address
	 * @param string the shipping_time
	 * @param string the remove_img
	 * @param string the product_img
	 * @param string the shipping_detail
	 * @param string the make_offer
	 * @param string the shipping_cost
	 * @param string the country_id
	 * @return string the json data
	 * @soap
	 */
	public function addproduct($api_username, $api_password, $user_id, $item_id = 0, $item_name, $item_des, $price, $size, $category, $subcategory, $chat_to_buy, $exchange_to_buy,
	$currency, $lat, $lon, $address, $shipping_time, $remove_img = Null,
	$product_img, $shipping_detail, $item_condition = NULL, $make_offer,$instant_buy=NULL,$paypal_id=NULL,$shipping_cost=NULL,$country_id=NULL) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				if($item_id != 0){
					$productModel = Products::model()->findByPk($item_id);
				}else{
					$productModel = new Products();
				}
				$productModel->userId = $user_id;
				$productModel->name = $item_name;
				$productModel->description = htmlentities($item_des);
				$productModel->price = $price;
				if(empty($quantity)) {
					$quantity = 1;
				}
				$quantity = 1;
				$productModel->quantity = $quantity;
				$productModel->sizeOptions = $size;
				$productModel->productCondition = $item_condition;
				$productModel->category = $category;
				$productModel->subCategory = $subcategory;
				$productModel->createdDate = time();
				$productModel->chatAndBuy = $chat_to_buy;
				$productModel->exchangeToBuy = $exchange_to_buy;
				$productModel->instantBuy = $instant_buy;
				$productModel->myoffer = $make_offer;
				$productModel->paypalid = $paypal_id;
				$productModel->currency = $currency;
				$productModel->latitude = $lat;
				$productModel->longitude = $lon;
				$productModel->location = $address;
				$productModel->shippingTime = $shipping_time;
				if($instant_buy == "1"){
					$productModel->shippingCost = $shipping_cost;
					$productModel->shippingcountry = $country_id;
				}

				$siteSettings = Sitesettings::model()->find();
				if($siteSettings->product_autoapprove==1)
				{
					$productModel->approvedStatus = 1;
					$productModel->Initial_approve = 1;
				}
				else
				{
					$productModel->approvedStatus = 0;
					$productModel->Initial_approve = 0;

				}

				$productModel->save();

				if($item_id != 0){
					//Photos::model()->deleteAllByAttributes(array('productId'=>$_POST['item_id']));
					if(!empty($remove_img) && $remove_img != ''){
						$imagesToRemove = explode(',', $remove_img);
						$remove_image_path = "media/item/{$item_id}/";
						foreach ($imagesToRemove as $images){
							Photos::model()->deleteAllByAttributes(array('productId' => $item_id,
									'name' => $images));
							$imagePath = $remove_image_path.$images;
							unlink($imagePath);
						}
					}
				}
				$photos = explode(',', $product_img);
				$path = Yii::app( )->getBasePath( )."/../media/item/{$productModel->productId}/";
				if( !is_dir( $path ) ) {
					mkdir( $path );
					chmod( $path, 0777 );
				}
				foreach ($photos as $photo){
					$image["path"] = Yii::app( )->getBasePath( )."/../media/item/tmp/".$photo;
					if( is_file( $image["path"] ) ) {
						if( rename( $image["path"], $path.$photo ) ) {
							chmod( $path.$photo, 0777 );
							$img = new Photos( );
							$img->name = $photo;
							$img->productId = $productModel->productId;
							$img->createdDate = time();
							if(!$img->save()){
								//Its always good to log something
								Yii::log( "Could not save Image:\n".CVarDumper::dumpAsString(
								$img->getErrors( ) ), CLogger::LEVEL_ERROR );
								//this exception will rollback the transaction
								throw new Exception( 'Could not save Image');
							}
						}
					}
				}

				if($instant_buy == 1){
					/*if(isset($item_id)){
						Shipping::model()->deleteAllByAttributes(array('productId'=>$item_id));
					}
					if(isset($shipping_detail)){
						$shippingDetails = json_decode($shipping_detail, true);
						foreach ($shippingDetails as $productShipping){
							$shippingModel = new Shipping();
							$shippingModel->productId = $productModel->productId;
							$shippingModel->countryId = $productShipping['country_id'];
							$shippingModel->shippingCost = $productShipping['shipping_cost'];
							$shippingModel->createdDate = time();
							$shippingModel->save(false);
						}
					}else{
						if(isset($_POST['everywhere_cost']) && $_POST['everywhere_cost'] != 0){
							$shippingModel = new Shipping();
							$shippingModel->productId = $productModel->productId;
							$shippingModel->countryId = 0;
							$shippingModel->shippingCost = $_POST['everywhere_cost'];
							$shippingModel->createdDate = time();
							$shippingModel->save();
						}
						if(isset($_POST['shipping_cost']) && $_POST['shipping_cost'] != 0){
							$shippingModel = new Shipping();
							$shippingModel->productId = $productModel->productId;
							$shippingModel->countryId = $_POST['country_id'];
							$shippingModel->shippingCost = $_POST['shipping_cost'];
							$shippingModel->createdDate = time();
							$shippingModel->save();
						}
					}*/
				}
				$productLink = Yii::app()->createAbsoluteUrl('item/products/view',array(
						'id' => Myclass::safe_b64encode($productModel->productId.'-'.
						rand(0,999)))).'/'.Myclass::productSlug($productModel->name);

				$promotion_type = 'Normal';
				if($productModel->promotionType == "2"){
					$promotion_type = 'Urgent';
				}elseif($productModel->promotionType == "1"){
					$promotion_type = 'Ad';
				}
				if($siteSettings->product_autoapprove==1)
				{
					$notifyMessage = 'added a product';
					$notifyvar = 0;
					Myclass::addLogs("add", $productModel->userId, $notifyvar, $productModel->productId, $productModel->productId, $notifyMessage);
					//return "add ".$productModel->userId." 0 ".$productModel->productId." ".$productModel->productId." ".$notifyMessage;
					$userid = $productModel->userId;
					$userdata = Users::model()->findByPk($userid);
					$currentusername = $userdata->name;
					$followCriteria = new CDbCriteria;
					$followCriteria->addCondition("follow_userId = $userid");
					$followers = Followers::model()->findAll($followCriteria);
					foreach ($followers as $follower) {
						$followuserid = $follower->userId;
						$criteria = new CDbCriteria;
						$criteria->addCondition('user_id = "'.$followuserid.'"');
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

									$text = 'added a product';
									$msg = Yii::t('app',$text);

									$messages =$currentusername.' '.$msg.' '.$productModel->name;
									Myclass::pushnot($deviceToken,$messages,$badge);
								}
							}
						}
					}
				}
				if($siteSettings->product_autoapprove==1)
				{
					return '{"status":"true", "message":"Product Posted Successfully", "product_url":"'.$productLink.'","item_id":"'.$productModel->productId.'","promotion_type":"'.$promotion_type.'"}';
				}
				else
				{
					return '{"status":"true", "message":"Product waiting for admin approval", "product_url":"'.$productLink.'","item_id":"'.$productModel->productId.'","promotion_type":"'.$promotion_type.'"}';
				}
				/*return '{"status":"true", "message":"Product Posted Successfully", "product_url":"'.$productLink.'","item_id":"'.$productModel->productId.'","promotion_type":"'.$promotion_type.'"}';*/
			}else{
				$errors = $this->errorMessage;
				return '{"status":"false","message":”Sorry, Your Product is not posted try after sometime", "Reason":"'.$errors.'"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function messages($api_username, $api_password, $user_id, $offset = 0, $limit = 20) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				//$limit = 20;
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				//$offset = 0;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				$criteria = new CDbCriteria;
				$criteria->limit = $limit;
				$criteria->offset = $offset;
				$criteria->condition = "user1 = '$userId' OR user2 = '$userId' order by lastContacted DESC";
				$chatedUsers = Chats::model()->findAll($criteria);
				if (!empty($chatedUsers)){
					$chatUserList = array();
					$chatKey = 0;
					foreach ($chatedUsers as $chatedUser){
						if ($chatedUser->user1 != $user_id){
							$chatedUserId = $chatedUser->user1;
						}else{
							$chatedUserId = $chatedUser->user2;
						}
						$userDetails = Myclass::getUserDetails($chatedUserId);
						$chatUserList[$chatKey]['message_id'] = $chatedUser->chatId;
						if(!empty($userDetails->userImage)){
							$chatUserList[$chatKey]['user_image'] = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userDetails->userImage);
						}else{
							$chatUserList[$chatKey]['user_image'] = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
						}
						$chatUserList[$chatKey]['user_name'] = $userDetails->username;
						$chatUserList[$chatKey]['full_name'] = $userDetails->name;
						$chatUserList[$chatKey]['user_id'] = $chatedUserId;
						$chatUserList[$chatKey]['message'] = urldecode($chatedUser->lastMessage);
						$chatUserList[$chatKey]['last_repliedto'] = $chatedUser->lastToRead;
						$chatUserList[$chatKey]['message_time'] = $chatedUser->lastContacted;
						$chatKey++;
					}
					return '{"status": "true","result": '.json_encode($chatUserList).'}';
				}else{
					return '{"status":"false","message":"No Message found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the type
	 * @return string the json data
	 * @soap
	 */
	public function myexchanges($api_username, $api_password, $user_id, $type) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				//$type = $_POST['type'];
				$criteria = new CDbCriteria;
				if($type == 'incoming') {
					$criteria->addCondition("status = 0");
					$criteria->addCondition("status = 1",'OR');
					$criteria->addCondition("requestTo = $userId");
				}
				if($type == 'outgoing') {
					$criteria->addCondition("status = 0");
					$criteria->addCondition("status = 1",'OR');
					$criteria->addCondition("requestFrom = $userId");
				}
				if($type == 'success') {
					$criteria->addCondition("requestTo = $userId");
					$criteria->addCondition("requestFrom = $userId",'OR');
					$criteria->addCondition("status = ".self::SUCCESS,'AND');
				}
				if($type == 'failed') {
					$criteria->addCondition("status = ".self::DECLINE);
					$criteria->addCondition("status = ".self::CANCEL,'OR');
					$criteria->addCondition("status = ".self::FAILED,'OR');
					$criteria->addCondition("status = ".self::SOLDOUT,'OR');
					$criteria->addCondition("requestTo = $userId OR requestFrom = $userId",'AND');
				}
				$criteria->order = 'date DESC';
				$exchanges = Exchanges::model()->findAll($criteria);
				$result = array();

				if(!empty($exchanges)){
					foreach($exchanges as $key => $exchange):
					$result["exchange"][$key]["type"] = $type;
					$result["exchange"][$key]["exchange_id"] = $exchange->id;
					if($exchange->status == self::PENDING)
					$status = 'Pending';
					elseif($exchange->status == self::ACCEPT)
					$status = 'Accepted';
					elseif($exchange->status == self::DECLINE)
					$status = 'Declined';
					elseif($exchange->status == self::CANCEL)
					$status = 'Cancelled';
					elseif($exchange->status == self::SUCCESS)
					$status = 'Success';
					elseif($exchange->status == self::FAILED)
					$status = 'Failed';
					elseif($exchange->status == self::SOLDOUT)
					$status = 'Sold Out';
					$result["exchange"][$key]["status"] = $status;

					if($exchange->requestFrom == $userId) {
						$result["exchange"][$key]["request_by_me"] = 'true';
						$result["exchange"][$key]["exchange_time"] = date("d-m-Y",$exchange->date);
						$exchangerDetails = Myclass::getUserDetails($exchange->requestTo);
						$result["exchange"][$key]["exchanger_name"] = $exchangerDetails->name;
						$result["exchange"][$key]["exchanger_username"] = $exchangerDetails->username;
						if(!empty($exchangerDetails->userImage)) {
							$userImageUrl = Yii::app()->createAbsoluteUrl('user/resized/150/'.$exchangerDetails->userImage);
						} else {
							$userImageUrl = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
						}
						$result["exchange"][$key]["exchanger_image"] = $userImageUrl;
					} else {
						$result["exchange"][$key]["request_by_me"] = 'false';
						$result["exchange"][$key]["exchange_time"] = date("d-m-Y",$exchange->date);
						$exchangerDetails = Myclass::getUserDetails($exchange->requestFrom);
						$result["exchange"][$key]["exchanger_name"] = $exchangerDetails->name;
						$result["exchange"][$key]["exchanger_username"] = $exchangerDetails->username;
						if(!empty($exchangerDetails->userImage)) {
							$userImageUrl = Yii::app()->createAbsoluteUrl('user/resized/150/'.$exchangerDetails->userImage);
						} else {
							$userImageUrl = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
						}
						$result["exchange"][$key]["exchanger_image"] = $userImageUrl;
					}
					$result["exchange"][$key]["exchanger_id"] = $exchangerDetails->userId;

					$productImage =Myclass::getProductImage($exchange->mainProductId);
					$productDetails = Myclass::getProductDetails($exchange->mainProductId);
					$proImageUrl = Yii::app()->createAbsoluteUrl('item/products/resized/100x100/'.$exchange->mainProductId.'/'.$productImage);
					$result["exchange"][$key]["my_product"]["item_id"] = $productDetails->productId;
					$result["exchange"][$key]["my_product"]["item_name"] = $productDetails->name;
					$result["exchange"][$key]["my_product"]["item_image"] = $proImageUrl;


					$exproductImage =Myclass::getProductImage($exchange->exchangeProductId);
					$exproductDetails = Myclass::getProductDetails($exchange->exchangeProductId);
					$exproImageUrl = Yii::app()->createAbsoluteUrl('item/products/resized/100x100/'.$exchange->exchangeProductId.'/'.$exproductImage);
					$result["exchange"][$key]["exchange_product"]["item_id"] = $exproductDetails->productId;
					$result["exchange"][$key]["exchange_product"]["item_name"] = $exproductDetails->name;
					$result["exchange"][$key]["exchange_product"]["item_image"] = $exproImageUrl;

					endforeach;
					return '{"status":"true","result":'.json_encode($result).'}';
				}else{
					return'{"status":"false","message":"No data found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the myitem_id
	 * @param string the exchangeitem_id
	 * @return string the json data
	 * @soap
	 */
	public function createexchange($api_username, $api_password, $user_id, $myitem_id, $exchangeitem_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$exchange = new Exchanges;
				$exchange->mainProductId = $myitem_id;
				$exchange->exchangeProductId = $exchangeitem_id;
				$exchange->requestFrom = $user_id;
				$product = Products::model()->findByPk($myitem_id);
				$exchange->requestTo = $product->userId;
				$exchange->date = time();
				$exchange->slug = Myclass::getRandomString(8);
				$exchange->status = self::PENDING;
				$mainProductModel = Myclass::getProductDetails($myitem_id);
				$exchangeProductModel = Myclass::getProductDetails($exchangeitem_id);
				if($mainProductModel->quantity < 1 || $mainProductModel->soldItem != 0){
					return'{"status":"false","message":"Product has been soldout unexpectedly"}';
				}elseif($exchangeProductModel->quantity < 1 || $exchangeProductModel->soldItem != 0){
					return'{"status":"false","message":"Your choosen Product has been soldout, choose a different one"}';
				}else{
					$check = Myclass::exchangeProductExist($exchange->mainProductId,$exchange->exchangeProductId,$exchange->requestFrom,$exchange->requestTo);
					if(!empty($check)) {
						if($check->blockExchange == 1) {
							return'{"status":"false","message":"Exchange Request for this product has been blocked"}';
						} else {
							if($check->status != 0 && $check->status != 1) {
								$check->requestFrom = $user_id;
								$check->requestTo = $product->userId;
								$check->status = self::PENDING;
								$check->date = time();
								$history = array();
								if(!empty($check->exchangeHistory)) {
									$history = json_decode($check->exchangeHistory,true);
								}
								$history[] = array('status' =>'created','date'=>$check->date,'user'=>$check->requestFrom);
								$check->exchangeHistory = json_encode($history);
								$check->save(false);


								$userid = $check->requestFrom;
								$senderid = $check->requestTo;
								$sellerDetails = Myclass::getUserDetails($userid);
								$receiverDetails = Myclass::getUserDetails($senderid);
								$criteria = new CDbCriteria;
								$criteria->addCondition('user_id = "'.$senderid.'"');
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

											$text = 'sent exchange request to your product';
											$msg = Yii::t('app',$text);

											$messages = $sellerDetails->name." ".$msg." ".$mainProductModel->name;
											Myclass::pushnot($deviceToken,$messages,$badge);
										}
									}
								}
								$notifyMessage = 'sent exchange request to your product';
								Myclass::addLogs("exchange", $user_id, $senderid, $check->id, $myitem_id, $notifyMessage);

								$siteSettings = Sitesettings::model()->find();
								$emailTo = $receiverDetails->email;
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

								$mail->setView('exchangecreated');
								$mail->setData(array('c_username' => $receiverDetails->name, 'r_username' => $sellerDetails->name, 'siteSettings' => $siteSettings));
								$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
								$mail->setTo($emailTo);
								$mail->setSubject($sellerDetails->name.' '.Yii::t('app','sent exchange request to your product'));
								$mail->send();

								return '{"status":"true","result":"Exchange created successfully"}';
							} else {
								return'{"status":"false","message":"Exchange Request exists.Please check Your Exchanges"}';
							}
						}
					} else {
						if($exchange->validate()) {
							$history = array();
							if(!empty($exchange->exchangeHistory)) {
								$history = json_decode($exchange->exchangeHistory,true);
							}
							$history[] = array('status' =>'created','date'=>$exchange->date,'user'=>$exchange->requestFrom);
							$exchange->exchangeHistory = json_encode($history);
							$exchange->save(false);

								$userid = $exchange->requestFrom;
								$senderid = $exchange->requestTo;
								$pushsender = $senderid;
								$pushuser = $userid;
								if($user_id == $userid){
										$pushuser = $senderid;
										$pushsender = $userid;
								}
								$sellerDetails = Myclass::getUserDetails($pushsender);
								$userid = $exchange->requestFrom;
								$senderid = $exchange->requestTo;
								$sellerDetails = Myclass::getUserDetails($userid);
								$receiverDetails = Myclass::getUserDetails($senderid);
								$criteria = new CDbCriteria;
								$criteria->addCondition('user_id = "'.$senderid.'"');
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

									$text = 'sent exchange request to your product';
									$msg = Yii::t('app',$text);

											$messages = $sellerDetails->name." ".$msg." ".$mainProductModel->name;
											Myclass::pushnot($deviceToken,$messages,$badge);
										}
									}
								}

								$siteSettings = Sitesettings::model()->find();
								$emailTo = $receiverDetails->email;
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

								$mail->setView('exchangecreated');
								$mail->setData(array('c_username' => $receiverDetails->name, 'r_username' => $sellerDetails->name, 'siteSettings' => $siteSettings));
								$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
								$mail->setTo($emailTo);
								$mail->setSubject($sellerDetails->name.' '.Yii::t('app','sent exchange Request with your product'));
								$mail->send();

								$notifyMessage = 'sent exchange request to your product';
								Myclass::addLogs("exchange", $user_id, $senderid, $exchange->id, $myitem_id, $notifyMessage);
							return '{"status":"true","result":"Exchange created successfully"}';
						}
					}
				}
			}else{
				return $this->errorMessage;
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the exchange_id
	 * @param string the status
	 * @return string the json data
	 * @soap
	 */
	public function exchangestatus($api_username, $api_password, $user_id, $exchange_id, $status) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				//$exchange_id = $_POST['exchange_id'];
				$Exstatus = $status;
				$status = Exchanges::model()->findByPk($exchange_id);
				$statusUpdate = "";

				if($Exstatus == "accept" && $status->status == 0){
					$status->status = self::ACCEPT;
				}elseif($Exstatus == "decline" && $status->status == 0){
					$status->status = self::DECLINE;
				}elseif($Exstatus == "cancel" && $status->status == 0){
					$status->status = self::CANCEL;
				}elseif($Exstatus == "success" && $status->status == 1){
					$status->status = self::SUCCESS;
				}elseif($Exstatus == "failed" && $status->status == 1){
					$status->status = self::FAILED;
				}else{
					return '{"status":"false", "message":"Status Already Updated"}';
				}


				$userid = $status->requestFrom;
				$senderid = $status->requestTo;
				$pushsender = $senderid;
				$pushuser = $userid;
				$productId = $status->exchangeProductId;
				if($user_id == $userid){
					$pushuser = $senderid;
					$pushsender = $userid;
					$productId = $status->mainProductId;
				}
				$sellerDetails = Myclass::getUserDetails($pushsender);
				$receiverDetails = Myclass::getUserDetails($pushuser);
				$criteria = new CDbCriteria;
				$criteria->addCondition('user_id = "'.$pushuser.'"');
				$userdevicedet = Userdevices::model()->findAll($criteria);



				/* if($Exstatus == "accept")
				$status->status = self::ACCEPT;
				elseif($Exstatus == "decline")
				$status->status = self::DECLINE;
				elseif($Exstatus == "cancel")
				$status->status = self::CANCEL;
				elseif($Exstatus == "success")
				$status->status = self::SUCCESS;
				elseif($Exstatus == "failed")
				$status->status = self::FAILED; */

				$status->save(false);

				$emailTo = $receiverDetails->email;
				$siteSettings = Sitesettings::model()->find();
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

				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);

				$mainProduct = Products::model()->findByPK($productId);
				if($Exstatus == "accept"){
					$notifyMessage = 'accepted your exchange request on';
//					$messages = $sellerDetails->name." accepted your exchange request on ".$mainProduct->name;
					Myclass::addLogs("exchange", $pushsender,  $pushuser, $status->id, $status->mainProductId, $notifyMessage);

					$mail->setView('exchangeaccept');
					$mail->setSubject($sellerDetails->name.' Exchange Request with your product was Accepted');
				}
				elseif($Exstatus == "decline"){
					$notifyMessage = 'declined your exchange request on';
//					$messages = $sellerDetails->name." declined your exchange request on ".$mainProduct->name;
					Myclass::addLogs("exchange", $pushsender,  $pushuser, $status->id, $status->mainProductId, $notifyMessage);

					$mail->setView('exchangedecline');
					$mail->setSubject($sellerDetails->name.' Exchange Request with your product was Declined');
				}
				elseif($Exstatus == "cancel"){
					$notifyMessage = 'canceled your exchange request on';
//					$messages = $sellerDetails->name." cancelled your exchange request on ".$mainProduct->name;
					Myclass::addLogs("exchange", $pushsender,  $pushuser, $status->id, $status->mainProductId, $notifyMessage);

					$mail->setView('exchangecancel');
					$mail->setSubject($sellerDetails->name.' cancelled Exchange Request with your product');
				}
				elseif($Exstatus == "success"){
					$notifyMessage = 'successed your exchange request on';
//					$messages = $sellerDetails->name." successed your exchange request on ".$mainProduct->name;
					Myclass::addLogs("exchange", $pushsender,  $pushuser, $status->id, $status->mainProductId, $notifyMessage);

					$mail->setView('exchangesuccess');
					$mail->setSubject($sellerDetails->name.' Exchange Request with your product was Successed');
				}
				elseif($Exstatus == "failed"){
					$notifyMessage = 'failed your exchange request on';
//					$messages = $sellerDetails->name." failed your exchange request on ".$mainProduct->name;
					Myclass::addLogs("exchange", $pushsender,  $pushuser, $status->id, $status->mainProductId, $notifyMessage);

					$mail->setView('exchangefailed');
					$mail->setSubject($sellerDetails->name.' '.Yii::t('app','Exchange Request with your product was Failed'));
				}

				$mail->setData(array('c_username' => $receiverDetails->name, 'r_username' => $sellerDetails->name, 'siteSettings' => $siteSettings));
				$mail->setTo($emailTo);
				$mail->send();

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
							//$messages = $Exstatus." Exchange Request from ".$sellerDetails->name;
								$msg = Myclass::push_lang($lang);


				if($Exstatus == "accept"){
					$text = 'accepted your exchange request on';
					$msg = Yii::t('app',$text);
					$messages = $sellerDetails->name." ".$msg." ".$mainProduct->name;
				}
				elseif($Exstatus == "decline"){
					$text = 'declined your exchange request on';
					$msg = Yii::t('app',$text);
					$messages = $sellerDetails->name." ".$msg." ".$mainProduct->name;
				}
				elseif($Exstatus == "cancel"){
					$text = 'canceled your exchange request on';
					$msg = Yii::t('app',$text);
					$messages = $sellerDetails->name." ".$msg." ".$mainProduct->name;
				}
				elseif($Exstatus == "success"){
					$text = 'successed your exchange request on';
					$msg = Yii::t('app',$text);
					$messages = $sellerDetails->name." ".$msg." ".$mainProduct->name;
				}
				elseif($Exstatus == "failed"){
					$text = 'failed your exchange request on';
					$msg = Yii::t('app',$text);
					$messages = $sellerDetails->name." ".$msg." ".$mainProduct->name;
				}

							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
				}


				if($Exstatus == "success") {
					$mainProduct = Products::model()->findByPK($status->mainProductId);
					$mainProduct->soldItem = 1;

					if($mainProduct->promotionType != 3){
						$promotionCriteria = new CDbCriteria();
						$promotionCriteria->addCondition("productId = $mainProduct->productId");
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
					$mainProduct->promotionType = 3;
					$mainProduct->quantity--;
					$mainProduct->save(false);

					$exProduct = Products::model()->findByPK($status->exchangeProductId);
					$exProduct->soldItem = 1;

					if($exProduct->promotionType != 3){
						$promotionCriteria = new CDbCriteria();
						$promotionCriteria->addCondition("productId = $exProduct->productId");
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
					$exProduct->promotionType = 3;
					$exProduct->quantity--;
					$exProduct->save(false);
				}
				return '{"status":"true","result":"Exchange updated successfully"}';
			}else{
				return $this->errorMessage;
			}
		} else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the shipping_id
	 * @param string the full_name
	 * @param string the nick_name
	 * @param string the country_id
	 * @param string the country_name
	 * @param string the state
	 * @param string the address1
	 * @param string the address2
	 * @param string the city
	 * @param string the zip_code
	 * @param string the phone_no
	 * @param string the default
	 * @return string the json data
	 * @soap
	 */
	public function addShipping($api_username, $api_password, $user_id, $full_name, $nick_name,
	$country_id, $country_name, $state, $address1, $address2, $city, $zip_code, $phone_no,
	$default, $shipping_id = 0) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$shippingId = 0;
				if ($shipping_id != 0){
					$shippingId = $shipping_id;
				}
				$userId = $user_id;
				$fullName = $full_name;
				$nickName = $nick_name;
				$countryId = $country_id;
				$countryName = $country_name;
				$state = $state;
				$address1 = $address1;
				$address2 = $address2;
				$city = $city;
				$zipCode = $zip_code;
				$phoneNo = $phone_no;
				$default = $default;

				if ($shippingId == 0) {
					$criteria = new CDbCriteria;
					$criteria->addCondition("nickname = '{$nickName}'");
					$criteria->addCondition("userId = $userId");
					$shippingModel = Tempaddresses::model()->findAll($criteria);
				}
				if (!empty($shippingModel)){
					return '{"status":"false","message":"Already a Shipping Address with this Nick Name Exist"}';
				} else {
					$outputValue = 'Added';
					if ($shippingId != 0){
						$tmpaddress = Tempaddresses::model()->findByPk($shippingId);
						$tmpaddress->shippingaddressId = $shippingId;
						$outputValue = 'Updated';
					} else {
						$tmpaddress = new Tempaddresses;
					}
					$tmpaddress->userId = $userId;
					$tmpaddress->name = $fullName;
					$tmpaddress->nickname = $nickName;
					$tmpaddress->country = $countryName;
					$tmpaddress->state = $state;
					$tmpaddress->address1 = $address1;
					$tmpaddress->address2 = $address2;
					$tmpaddress->city = $city;
					$tmpaddress->zipcode = $zipCode;
					$tmpaddress->phone = $phoneNo;
					$tmpaddress->slug = Myclass::getRandomString(8);
					$tmpaddress->countryCode = $countryId;
					if($tmpaddress->save()) {
						$tempaddress['Tempaddresses']['shippingid'] = $tmpaddress->shippingaddressId;
						$tempaddress['Tempaddresses'] = $tmpaddress->attributes;

						if($default == 1) {
							$user = Users::model()->findByPk($userId);
							$user->defaultshipping = $tmpaddress->shippingaddressId;
							$user->save(false);
							$output = json_encode($tempaddress['Tempaddresses']);
						}
						else
						{
							$userModel = Users::model()->findByPk($userId);
							$defaultAddress = $userModel->defaultshipping;
							$shipping = Tempaddresses::model()->findByPk($tmpaddress->shippingaddressId);
							$shippingAddress['shippingid'] = $shipping->shippingaddressId;
							$shippingAddress['nickname'] = $shipping->nickname;
							$shippingAddress['name'] = $shipping->name;
							$shippingAddress['country'] = $shipping->country;
							$shippingAddress['state'] = $shipping->state;
							$shippingAddress['address1'] = $shipping->address1;
							$shippingAddress['address2'] = $shipping->address2;
							$shippingAddress['city'] = $shipping->city;
							$shippingAddress['zipcode'] = $shipping->zipcode;
							$shippingAddress['phone'] = $shipping->phone;
							$shippingAddress['countrycode'] = $shipping->countryCode;
							$output = json_encode($shippingAddress);
						}
						return '{"status":"true","result":'.$output.'}';
					} else {
						return '{"status":"false","result":"Not Saved.Something went wrong.Try again Later."}';
					}
				}
			}else{
				return $this->errorMessage;
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function getShippingAddress($api_username, $api_password, $user_id, $item_id){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				$userModel = Myclass::getUserDetails($userId);
				$defaultShipping = $userModel->defaultshipping;

				if($item_id != 0)
				{
					$productModel = Products::model()->findByPk($item_id);
					$countryId = $productModel->shippingcountry;
					$criteria = new CDbCriteria;
					$criteria->addCondition('userId = "'.$userId.'"');
					$criteria->addCondition('countryCode = "'.$countryId.'"');
					$criteria->order = 'shippingaddressId DESC';
					$shippingModel = Tempaddresses::model()->findAll($criteria);
				}
				else
				{
					$criteria = new CDbCriteria;
					$criteria->addCondition('userId = "'.$userId.'"');
					$criteria->order = 'shippingaddressId DESC';
					$shippingModel = Tempaddresses::model()->findAll($criteria);
				}

				if (!empty($shippingModel)){
					$shippingAddress = array();
					foreach ($shippingModel as $skey => $shipping){
						$shippingAddress[$skey]['shippingid'] = $shipping->shippingaddressId;
						$shippingAddress[$skey]['nickname'] = $shipping->nickname;
						$shippingAddress[$skey]['name'] = $shipping->name;
						$shippingAddress[$skey]['country'] = $shipping->country;
						$shippingAddress[$skey]['state'] = $shipping->state;
						$shippingAddress[$skey]['address1'] = $shipping->address1;
						$shippingAddress[$skey]['address2'] = $shipping->address2;
						$shippingAddress[$skey]['city'] = $shipping->city;
						$shippingAddress[$skey]['zipcode'] = $shipping->zipcode;
						$shippingAddress[$skey]['phone'] = $shipping->phone;
						$shippingAddress[$skey]['countrycode'] = $shipping->countryCode;
						$shippingAddress[$skey]['defaultshipping'] = 0;
						if ($defaultShipping == $shipping->shippingaddressId){
							$shippingAddress[$skey]['defaultshipping'] = 1;
						}
					}
					$resultArray = json_encode($shippingAddress);
					return '{"status":"true","result":'.$resultArray.'}';
				}else{
					return '{"status":"false","message":"Yet no shipping address added"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the shipping_id
	 * @return string the json data
	 * @soap
	 */
	 public function setdefaultshipping($api_username, $api_password, $user_id, $shipping_id)
	 {
	 	if ($this->authenticateAPI($api_username, $api_password)){
			if($this->checking($user_id)){
				$default = Users::model()->findByPk($user_id);
				$default->defaultshipping = $shipping_id;
				if($default->save(false))
				{
					return '{"status":"true","message":"Your default Address changed"}';
				}
				else
				{
					return '{"status":"false","message":"Something went wrong"}';
				}
			}
			else
			{
				return $this->errorMessage;
			}
		}
		else
		{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	 }

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the shipping_id
	 * @return string the json data
	 * @soap
	 */
	 public function removeshipping($api_username, $api_password, $user_id,$shipping_id)
	 {
	 	if ($this->authenticateAPI($api_username, $api_password)){
			if($this->checking($user_id)){
				$address = Tempaddresses::model()->findByAttributes(array('shippingaddressId'=>$shipping_id));
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
				if($address->delete())
				{
					return '{"status":"true","message":"Address deleted successfully"}';
				}
				else
				{
					return '{"status":"false","message":"Something went wrong"}';
				}
			}
			else
			{
				return $this->errorMessage;
			}
		}
		else
		{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	 }

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the order_id
	 * @return string the json data
	 * @soap
	 */
	public function gettrackdetails($api_username, $api_password, $order_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			$orderid = $order_id;

			$trackingModel = Trackingdetails::model()->find("orderid = $orderid");

			if (!empty($trackingModel)){
				$Trackingdetails['id'] = $trackingModel->id;
				$Trackingdetails['shippingdate'] = $trackingModel->shippingdate;
				$Trackingdetails['couriername'] = $trackingModel->couriername;
				$Trackingdetails['courierservice'] = $trackingModel->courierservice;
				$Trackingdetails['trackingid'] = $trackingModel->trackingid;
				$Trackingdetails['notes'] = $trackingModel->notes;

				$result = json_encode($Trackingdetails);
				return '{"status":"true","result":'.$result.'}';
			}else{
				return '{"status":"false","result":"No Tracking Details Found"}';
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the orderid
	 * @param string the chstatus
	 * @param string the subject
	 * @param string the message
	 * @param string the id
	 * @param string the shippingdate
	 * @param string the couriername
	 * @param string the courierservice
	 * @param string the trackid
	 * @param string the notes
	 * @return string the json data
	 * @soap
	 */
	public function orderstatus($api_username, $api_password, $orderid, $chstatus, $subject = NULL,
	$message = NULL, $id = 0, $shippingdate = NULL, $couriername = NULL, $courierservice = NULL,
	$trackid = NULL, $notes = NULL){
		if ($this->authenticateAPI($api_username, $api_password)){
			//$orderid = $_POST['orderid'];
			$orderPreviousStatus = Orders::model()->findByPk($orderid);
			$status = $chstatus;
			if ($status == 'Processing' && $orderPreviousStatus->status == "pending") {
				$criteria = new CDbCriteria;
				$criteria->addCondition("orderId=$orderid");
				Orders::model()->updateAll(array('status' => "processing"),$criteria);

						$userid = $orderPreviousStatus->userId;
						$criteria = new CDbCriteria;
						$criteria->addCondition('user_id = "'.$userid.'"');
						$userdevicedet = Userdevices::model()->findAll($criteria);

				$notifyMessage = 'your order has been marked as processing Order Id :'.$orderPreviousStatus->orderId;
				Myclass::addLogs("order", $orderPreviousStatus->sellerId, $orderPreviousStatus->userId, $orderPreviousStatus->orderId, 0, $notifyMessage);

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

				return '{"status":"true","result":"Status changed to Processing"}';
			}
			else if($status == "claim")
			{
				$order = Orders::model()->findByPk($orderid);
				$order->status = "claimed";
				$order->save(false);
				return '{"status":"true","result":"Status changed to claimed"}';
			}
			else if($status == "cancel")
			{
				$orderdata = Orders::model()->findByPk($orderid);
				$orderdata->status = "cancelled";
				$orderdata->trackPayment = "pending";
				$orderdata->save(false);

				$order = Orders::model()->with('orderitems')->findByPk($orderid);

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
									'orderId'=>$orderid));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($check->email);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Cancelled Mail'));
				$mail->send();
				$notifyMessage = 'your order has been cancelled Order Id :'.$order->orderId;
				Myclass::addLogs("order", $order->userId, $order->sellerId, $order->orderId, 0, $notifyMessage);

						$userid = $check->userId;
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

						$text = 'your order has been cancelled Order Id :';
						$msg = Yii::t('app',$text);

						$messages = $msg.' '.$order->orderId;
 	 					Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
				}

				return '{"status":"true","result":"Status changed to cancelled"}';



			}
			elseif($status == 'Delivered' && ($orderPreviousStatus->status == "shipped" || $orderPreviousStatus->status == "claimed")) {
				$statusDate = time();
				$criteria = new CDbCriteria;
				$criteria->addCondition("orderId=$orderid");
				Orders::model()->updateAll(array('status' => "delivered", 'statusDate' => "$statusDate"),$criteria);


				$siteSettings = Sitesettings::model()->find();
				$check = Users::model()->findByPk($orderPreviousStatus->userId);
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
									'orderId'=>$orderid));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($check->email);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Delivered Mail'));
				$mail->send();


				$notifyMessage = $userName.' your order has been marked as delivered Order Id :'.$orderPreviousStatus->orderId;
				Myclass::addLogs("order", $orderPreviousStatus->userId, $orderPreviousStatus->sellerId, $orderPreviousStatus->orderId, 0, $notifyMessage);

						$userid = $orderPreviousStatus->sellerId;
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

						/*$text = 'Your orderid:';
						$msg = Yii::t('app',$text);

						$text1 = 'has been marked as delivered';
						$msg1 = Yii::t('app',$text1);

							$messages = $msg.' '.$orderid.' '.$userName.' '.$msg1;*/
							$text = ' has marked the order as delivered, order id: ';
							$msg = Yii::t('app',$text);
							$messages = $userName.$msg.$orderid;
							Myclass::pushnot($deviceToken,$messages,$badge);
							
						}
						}
						}


				return '{"status":"true","result":"Status changed to Delivered"}';
			}elseif($status == 'Shipped' && $orderPreviousStatus->status == "processing"){
				//$subject = $_POST['subject'];
				//$message = $_POST['message'];
				$orderModel = Orders::model()->findByPk($orderid);
				$shipping = Shippingaddresses::model()->findByPk($orderModel->shippingAddress);
				$loguser = Myclass::getUserDetails($orderModel->sellerId);
				$buyerModel = Myclass::getUserDetails($orderModel->userId);
				$buyeremail = $buyerModel->email;
				$usernameforcust = $buyerModel->name;
				$orderitemModel = Orderitems::model()->find("orderid=$orderid");
				$itemmailids = $orderitemModel->productId;
				$itemname = $orderitemModel->itemName;
				$itemsize = $orderitemModel->itemSize;
				$totquantity = $orderitemModel->itemQuantity;
				$siteSettings = Sitesettings::model()->find();
				$mail = new YiiMailer();
				if($siteSettings->smtpEnable == 1) {
					//$mail->IsSMTP();                         // Set mailer to use SMTP
					$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
					$mail->Host = $siteSettings->smtpHost; //'smtp.gmail.com';  // Specify main and backup server
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = $siteSettings->smtpEmail;           // SMTP username
					$mail->Password = $siteSettings->smtpPassword;     // SMTP password
					if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';   // Enable encryption, 'ssl' also accepted
					$mail->Port = $siteSettings->smtpPort; //465;
				}
				$mail->setView('shippingintimation');
				$mail->setData(array('subject' => $subject,'siteSettings' => $siteSettings,
						'message' => $message,'tempShippingModel' => $shipping,'userModel' => $buyerModel,
						'orderId' => $orderid,'sellerName' => $loguser->name));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($buyeremail);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Shipping Confirmation Mail'));
				$mail->send();

				$criteria = new CDbCriteria;
				$criteria->addCondition("orderId=$orderid");
				Orders::model()->updateAll(array('status' => "shipped"),$criteria);

				$order = Orders::model()->with('orderitems')->findByPk($orderid);
				$notifyMessage = 'your order has been marked as shipped Order Id :'.$order->orderId;
				Myclass::addLogs("order", $order->sellerId, $order->userId, $order->orderId, 0, $notifyMessage);


						$userid = $orderModel->userId;
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

						$text = 'Your orderid:';
						$msg = Yii::t('app',$text);

						$text1 = 'has been marked as shipped';
						$msg1 = Yii::t('app',$text1);

							$messages = $msg.' '.$orderid.' '.$msg1;

							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
					}


				return '{"status":"true","result":"Status changed to Shipped"}';
			} elseif($status == 'Track' && ($orderPreviousStatus->status == "processing" || $orderPreviousStatus->status == "shipped") ) {
				$orderModel = Orders::model()->findByPk($orderid);
				$shipping = Shippingaddresses::model()->findByPk($orderModel->shippingAddress);
				$loguser = Myclass::getUserDetails($orderModel->sellerId);
				$buyerModel = Myclass::getUserDetails($orderModel->userId);
				$buyeremail = $buyerModel->email;
				$usernameforcust = $buyerModel->name;//$_POST['buyername'];
				$shipppingId = $orderModel->shippingAddress;
				$shippingModel = Shippingaddresses::model()->findByPk($shipppingId);
				$buyershipaddr = '';
				$buyershipaddr .= $shippingModel->address1.",</br>";
				$siteSettings = Sitesettings::model()->find();
				if (!empty($shippingModel->address2)){
					$buyershipaddr .= $shippingModel->address2.",</br>";
				}
				$buyershipaddr .= $shippingModel->city." - ".$shippingModel->zipcode.",</br>";
				$buyershipaddr .= $shippingModel->state.",</br>";
				$buyershipaddr .= $shippingModel->country.",</br>";
				$buyershipaddr .= "Ph.: ".$shippingModel->phone.".</br>";

				if ($id != 0){
					$track = Trackingdetails::model()->findByPk($id);
				} else {
					$track = new Trackingdetails;
				}

				$criteria = new CDbCriteria;
				$criteria->addCondition("orderId=$orderid");
				Orders::model()->updateAll(array('status' => "shipped"),$criteria);

				$order = Orders::model()->with('orderitems')->findByPk($orderid);
				$notifyMessage = 'your order has been marked as shipped Order Id :'.$order->orderId;
				Myclass::addLogs("order", $order->sellerId, $order->userId, $order->orderId, 0, $notifyMessage);


						$userid = $orderModel->userId;
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

						$text = 'Your orderid:';
						$msg = Yii::t('app',$text);

						$text1 = 'has been marked as shipped';
						$msg1 = Yii::t('app',$text1);

							$messages = $msg.' '.$orderid.' '.$msg1;
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
						}
						}

				$track->orderid = $orderid;
				$track->status = "shipped";
				$track->merchantid = $loguser->userId;
				$track->buyername = $usernameforcust;
				$track->buyeraddress = $buyershipaddr;
				$track->shippingdate = $shippingdate;
				$track->couriername = $couriername;
				$track->courierservice = $courierservice;
				$track->trackingid = $trackid;
				$track->notes = $notes;
				$track->save();

				$buyerModel = Myclass::getUserDetails($orderModel->userId);
				$buyeremail = $buyerModel->email;

				$mail = new YiiMailer();
				if($siteSettings->smtpEnable == 1) {
					//$mail->IsSMTP();                         // Set mailer to use SMTP
					$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
					$mail->Host = $siteSettings->smtpHost; //'smtp.gmail.com';  // Specify main and backup server
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
					$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
					if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
					$mail->Port = $siteSettings->smtpPort; //465;
				}

				$mail->setView('trackdetailsmail');
				$mail->setData(array('siteSettings' => $siteSettings,'tempShippingModel' => $shipping,
						'userModel' => $buyerModel,'sellerName' => $loguser->name,'tracking'=>$track,
						'model'=>$orderModel));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($buyeremail);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Tracking Details Mail'));
				$mail->send();

				return '{"status":"true","result":"Tracking Details Updated"}';
			}else{
				return '{"status":"false", "message":"Status already changed"}';
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function Itemlike($api_username, $api_password, $user_id, $item_id){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userid = $user_id;
				$itemid = $item_id;
				$product = Products::model()->findByPk($itemid);
				if(!empty($product)) {
					$favModel = Favorites::model()->findByAttributes(array('userId'=>$userid, 'productId'=>$itemid));
					if(empty($favModel)){
						$model = new Favorites();
						$model->userId = $userid;
						$model->productId = $itemid;
						if($model->save()) {
							$product->likes++;
							$product->save(false);

							$logsModel = new Logs();
							$logsModel->type = "like";
							$logsModel->userid = $userid;
							$logsModel->notifyto = $product->userId;
							$logsModel->itemid = $product->productId;
							$logsModel->notifymessage = 'liked your product';
							$logsModel->sourceid = $model->id;
							$logsModel->createddate = time();
							$logsModel->save(false);

							$userid = $product->userId;
							$criteria = new CDbCriteria;
							$criteria->addCondition('user_id = "'.$userid.'"');
							$userdevicedet = Userdevices::model()->findAll($criteria);

							$userModel = Users::model()->findByPk($model->userId);
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

								$text = 'liked your product';
								$msg = Yii::t('app',$text);

								$messages = $userModel->username." ".$msg." ".$product->name;
											Myclass::pushnot($deviceToken,$messages,$badge);
									}
								}
							}



							return '{"status":"true","result":"Item Liked Successfully"}';
						} else{
							return '{"status":"false","result":"Something went wrong."}';
						}
					}else {
						Favorites::model()->deleteByPk($favModel->id);
						$product->likes--;
						$product->save(false);

						$logCriteria = new CDbCriteria();
						$logCriteria->addCondition("type LIKE 'like'");
						$logCriteria->addCondition("sourceId = $favModel->id");
						$logsModel = Logs::model()->find($logCriteria);
						$logsModel->delete();

						return '{"status":"true","result":"Item Unliked Successfully"}';
					}
				} else {
					return '{"status":"false", "message":"Item Not Found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @param string the shipping_id
	 * @param string the merchant_id
	 * @param string the currency_code
	 * @param string the item_price
	 * @param string the shipping_fee
	 * @param string the order_total
	 * @param string the nonce
	 * @return string the json data
	 * @soap
	 */
	public function buynowPayment($api_username, $api_password, $user_id, $item_id, $shipping_id, $merchant_id, $currency_code, $nonce
		,$item_price, $shipping_fee, $order_total){
		if ($this->authenticateAPI($api_username, $api_password)){
			//$siteSettings = Sitesettings::model()->find();

			Yii::setPathOfAlias('Braintree',Yii::getPathOfAlias('application.vendors.Braintree.Braintree'));

			$siteSettings = Sitesettings::model()->find();
			$brainTreeSettings = json_decode($siteSettings->braintree_settings, true);

			$paymenttype = "sandbox";
			if($brainTreeSettings['brainTreeType'] == 1){
				$paymenttype = "production";
			}
			$merchantid = $brainTreeSettings['brainTreeMerchantId'];
			$publickey = $brainTreeSettings['brainTreePublicKey'];
			$privatekey = $brainTreeSettings['brainTreePrivateKey'];

			Braintree\Configuration::environment($paymenttype);
			Braintree\Configuration::merchantId($merchantid);
			Braintree\Configuration::publicKey($publickey);
			Braintree\Configuration::privateKey($privatekey);

			$result = Braintree\Transaction::sale([
					'amount' => $order_total,
					'paymentMethodNonce' => $nonce
			]);
			$result1 = Braintree\Transaction::submitForSettlement($result->transaction->id);

		//	if ($result->success || !is_null($result->transaction) && $result1->success == '1') {
				if ($result->success || !is_null($result->transaction->id) && $result1->success == '1') {
				$transaction = $result->transaction;
				$itemId = $item_id;

				$userModel = Users::model()->findByPk($user_id);
				$userId = $userModel->userId;

				$currencyCode = $currency_code;
				$quantity = 1;
				/* $forexrateModel = $this->Forexrates->find('first',
				 array('conditions'=>array('currency_code' => $currencyCode)));
				$forexRate = $forexrateModel['Forexrates']['price']; */

				/*if(isset($keyarray['item_number']) && $keyarray['item_number']!=''){
					$itemId = $keyarray['item_number'];
					$i = '';
				}else{
					$i = 1;
					$keyarray['iteration'] = 1;
					$mobi_value = explode('-_-', $keyarray['item_name'.$i]);
					$itemId = $mobi_value[1];
					$keyarray['item_name'.$i] = $mobi_value[0];
				}*/
				$itemId = $item_id;
				$tempShippingModel = Tempaddresses::model()->findByPk($shipping_id);

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




				$ordersModel = new Orders();
				$ordersModel->userId = $userId;
				$ordersModel->sellerId = $productModel->userId;
				$ordersModel->totalCost = $order_total;
				$ordersModel->totalShipping = $shipping_fee;
				$ordersModel->orderDate = time();
				$ordersModel->shippingAddress = $shippingId;
				$ordersModel->currency = $currencyCode;
				$ordersModel->sellerPaypalId = $productModel->paypalid;
				$ordersModel->status = 'pending';
				$ordersModel->trackPayment = 'pending';

				$ordersModel->save(false);
				$orderId = $ordersModel->orderId;

				$orderItemTotalPrice = $order_total - $shipping_fee;
				$orderItemUnitPrice = $orderItemTotalPrice / $quantity;

				$orderItemsModel = new Orderitems();
				$orderItemsModel->orderId = $orderId;
				$orderItemsModel->productId = $itemId;
				$orderItemsModel->itemName = $productModel->name;
				$orderItemsModel->itemPrice = $orderItemTotalPrice;
				$orderItemsModel->itemQuantity = $quantity;
				$orderItemsModel->itemunitPrice = $orderItemUnitPrice;
				$orderItemsModel->shippingPrice = $shipping_fee;

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

				$sellerEmail = $productModel->user->email;
				$sellerName = $productModel->user->name;

				$i = 1;
				$keyarray['iteration'] = "1";
				$keyarray['item_name'.$i] = $productModel->name;
				$keyarray['quantity'.$i] = $quantity;
				$custom[2] = "";

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
				$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom[2]'=>$custom[2],
						'userModel' => $userModel, 'sellerName' => $sellerName, 'keyarray'=>$keyarray,
						'tempShippingModel'=>$tempShippingModel));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($sellerEmail);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Seller Order Information'));

			$notifyMessage = 'placed an order in your shop, Order Id : '.$orderId;
			Myclass::addLogs("order", $userModel->userId, $productModel->user->userId, $productModel->productId, $productModel->productId, $notifyMessage);


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

								$text = 'placed an order in your shop, order id :';
								$msg = Yii::t('app',$text);

							$messages =  $userModel->username." ".$msg." ".$orderId;
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
				$mail->setData(array('orderId' => $orderId, 'siteSettings' => $siteSettings, 'custom[2]'=>$custom[2],
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

				return '{"status":"true","message":"Ordered Successfully"}';
			}else {
				return '{"status":"false","message":"Sorry, Something went wrong. Please try again later"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function myorders($api_username, $api_password, $user_id, $offset = 0, $limit = 10){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
			//$recent = $_POST['recent'];
				$userid = $user_id;
				//$offset = 0;
				//$limit = 10;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				$criteria = new CDbCriteria;
				$criteria->addCondition("userId = {$userid}");
				$criteria->order = 'orderId DESC';
				$criteria->offset = $offset;
				$criteria->limit = $limit;
				$timeline = strtotime('-1 month');
				/*if ($recent == 1){
					$criteria->addCondition("orderDate > {$timeline}");
				}else{
					$criteria->addCondition("orderDate < {$timeline}");
				}*/
				$ordersModel = Orders::model()->with('orderitems','trackingdetails')->findAll($criteria);
				foreach($ordersModel as $key => $order):
				$result[$key]['orderid'] = $order->orderId;
				$result[$key]['price'] = $order->totalCost;
				$result[$key]['saledate'] = $order->orderDate;
				$result[$key]['status'] = $order->status;

				$paymentmodes = Myclass::getSitePaymentModes();
				if(($paymentmodes['cancelEnableStatus'] == "processing" && $order->status == "pending") || ($paymentmodes['cancelEnableStatus'] == "shipped" && ($order->status == "pending" || $order->status == "processing"))) {
					$result[$key]['cancel'] = "true";
				}
				else
				{
					$result[$key]['cancel'] = "false";
				}

				if($order->status == "delivered" || $order->status == "paid")
					$result[$key]['deliverydate'] = $order->statusDate;
				else
					$result[$key]['deliverydate'] = "";

				$invoices = Invoices::model()->findByAttributes(array('orderId'=>$order->orderId));
				$result[$key]['transaction_id'] = $invoices->paymentTranxid;
				$result[$key]['payment_type'] = $invoices->paymentMethod;

				$reviewModel = Reviews::model()->findByAttributes(array('sourceId'=>$order->orderId, 'reviewType'=>'order'));
				$result[$key]['review_id'] = empty($reviewModel) ? "0" : $reviewModel->reviewId;
				$result[$key]['rating'] = empty($reviewModel) ? "0" : $reviewModel->rating;
				$result[$key]['review_title'] = empty($reviewModel) ? "" : $reviewModel->reviewTitle;
				$result[$key]['review_des'] = empty($reviewModel) ? "" : $reviewModel->review;
				$result[$key]['created_date'] = empty($reviewModel) ? "" : $reviewModel->createdDate;

				$userdata = Users::model()->findByPk($order->sellerId);
				$sellerId = $userdata->userId;
				$sellerName = $userdata->name;
				$sellerUsername = $userdata->username;

				if(!empty($userdata->userImage)){
					$sellerImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userdata->userImage);
				}else{
					$sellerImage = Yii::app()->createAbsoluteUrl('user/resized/150/default/'.Myclass::getDefaultUser());
				}

				$shipping = Shippingaddresses::model()->findByPk($order->shippingAddress);
				if(!empty($shipping)) {
					$id = $shipping->shippingaddressId;
					$result[$key]['shippingaddress']['name'] = $shipping->name;
					$result[$key]['shippingaddress']['nickname'] = $shipping->nickname;
					$result[$key]['shippingaddress']['country'] = $shipping->country;
					$result[$key]['shippingaddress']['state'] = $shipping->state;
					$result[$key]['shippingaddress']['address1'] = $shipping->address1;
					$result[$key]['shippingaddress']['address2'] = $shipping->address2;
					$result[$key]['shippingaddress']['city'] = $shipping->city;
					$result[$key]['shippingaddress']['zipcode'] = $shipping->zipcode;
					$result[$key]['shippingaddress']['phone'] = $shipping->phone;
					$result[$key]['shippingaddress']['countrycode'] = $shipping->countryCode;
				}
				if(!empty($order['trackingdetails'])){
					$result[$key]['trackingdetails']['id'] = $order['trackingdetails'][0]['id'];
					$result[$key]['trackingdetails']['shippingdate'] = $order['trackingdetails'][0]['shippingdate'];
					$result[$key]['trackingdetails']['couriername'] = $order['trackingdetails'][0]['couriername'];
					$result[$key]['trackingdetails']['courierservice'] = $order['trackingdetails'][0]['courierservice'];
					$result[$key]['trackingdetails']['trackingid'] = $order['trackingdetails'][0]['trackingid'];
					$result[$key]['trackingdetails']['notes'] = $order['trackingdetails'][0]['notes'];

				}
				if(!empty($order['orderitems'])) {
					$productId = $order['orderitems'][0]['productId'];
					$check = Products::model()->findByPk($productId);
					if(!empty($check)) {
						$productImage = $order['orderitems'][0]->product->photos[0]->name;
					}
					if(!empty($check)) {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId."/".$productImage);
					} else {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".'default.jpeg');
					}
					$result[$key]['orderitems']['itemid'] = $productId;
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					$result[$key]['orderitems']['seller_name'] = $sellerName;
					$result[$key]['orderitems']['seller_username'] = $sellerUsername;
					$result[$key]['orderitems']['seller_img'] = $sellerImage;
					$result[$key]['orderitems']['seller_id'] = $sellerId;
					$result[$key]['orderitems']['quantity'] = $order['orderitems'][0]['itemQuantity'];
					$result[$key]['orderitems']['price'] = $order['orderitems'][0]['itemPrice'];
					$result[$key]['orderitems']['unitprice'] = $order['orderitems'][0]['itemunitPrice'];
					$result[$key]['orderitems']['size'] = $order['orderitems'][0]['itemSize'];
					$currencysymbol = $order['orderitems'][0]->product->currency;
					$symbolval = explode("-", $currencysymbol);
					$cSymbol = $symbolval[0];
					$result[$key]['orderitems']['cSymbol'] = $cSymbol;
					$result[$key]['orderitems']['orderImage'] = $orderImageUrl;
					$result[$key]['orderitems']['shipping_cost'] = $order['orderitems'][0]['shippingPrice'];
					$result[$key]['orderitems']['total'] = $order['orderitems'][0]['shippingPrice'] + $order['orderitems'][0]['itemPrice'];
				}
				endforeach;
				if(!empty($result)) {
					$result = json_encode($result);
					return '{"status":"true","result":'.$result.'}';
				} else {
					return '{"status":"true","message":"No Purchase History Found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function mysales($api_username, $api_password, $user_id, $offset = 0, $limit = 10){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userid = $user_id;
				//$offset = 0;
				//$limit = 10;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				$criteria = new CDbCriteria;
				$criteria->addCondition("sellerId = {$userid}");
				$criteria->order = 'orderId DESC';
				$criteria->offset = $offset;
				$criteria->limit = $limit;

				$ordersModel = Orders::model()->with('orderitems','trackingdetails')->findAll($criteria);
				foreach($ordersModel as $key => $order):
				$result[$key]['orderid'] = $order->orderId;
				$result[$key]['price'] = $order->totalCost;
				$result[$key]['saledate'] = $order->orderDate;
				$result[$key]['status'] = $order->status;

				if($order->status == "delivered" || $order->status == "paid")
					$result[$key]['deliverydate'] = $order->statusDate;
				else
					$result[$key]['deliverydate'] = "";

				$invoices = Invoices::model()->findByAttributes(array('orderId'=>$order->orderId));
				$result[$key]['transaction_id'] = $invoices->paymentTranxid;
				$result[$key]['payment_type'] = $invoices->paymentMethod;
				$result[$key]['invoice'] = $invoices->invoiceNo;

				$reviewModel = Reviews::model()->findByAttributes(array('sourceId'=>$order->orderId, 'reviewType'=>'order'));
				$result[$key]['review_id'] = empty($reviewModel) ? "0" : $reviewModel->reviewId;
				$result[$key]['rating'] = empty($reviewModel) ? "0" : $reviewModel->rating;
				$result[$key]['review_title'] = empty($reviewModel) ? "" : $reviewModel->reviewTitle;
				$result[$key]['review_des'] = empty($reviewModel) ? "" : $reviewModel->review;
				$result[$key]['created_date'] = empty($reviewModel) ? "" : $reviewModel->createdDate;

				$paymentmodes = Myclass::getSitePaymentModes();
				$claimdays = $paymentmodes['sellerClimbEnableDays'];
				$trackingDetails = Trackingdetails::model()->findByAttributes(array("orderid" => $order->orderId));
				$shippingdate = $trackingDetails->shippingdate;
				$shipdate = strtotime("+$claimdays days",$shippingdate);
				$today = time();
				if($shipdate <= $today && $order->status == "shipped")
				{
					$result[$key]['claim'] = "true";
				}
				else
				{
					$result[$key]['claim'] = "false";
				}

				$userdata = Users::model()->findByPk($order->userId);
				$buyerId = $userdata->userId;
				$buyerName = $userdata->name;
				$buyerUsername = $userdata->username;
				$buyerEmail = $userdata->email;
				if(!empty($userdata->userImage)){
					$buyerImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userdata->userImage);
				}else{
					$buyerImage = Yii::app()->createAbsoluteUrl('user/resized/150/default/'.Myclass::getDefaultUser());
				}

				$shipping = Shippingaddresses::model()->findByPk($order->shippingAddress);
				if(!empty($shipping)) {
					$id = $shipping->shippingaddressId;
					$result[$key]['shippingaddress']['name'] = $shipping->name;
					$result[$key]['shippingaddress']['nickname'] = $shipping->nickname;
					$result[$key]['shippingaddress']['country'] = $shipping->country;
					$result[$key]['shippingaddress']['state'] = $shipping->state;
					$result[$key]['shippingaddress']['address1'] = $shipping->address1;
					$result[$key]['shippingaddress']['address2'] = $shipping->address2;
					$result[$key]['shippingaddress']['city'] = $shipping->city;
					$result[$key]['shippingaddress']['zipcode'] = $shipping->zipcode;
					$result[$key]['shippingaddress']['phone'] = $shipping->phone;
					$result[$key]['shippingaddress']['countrycode'] = $shipping->countryCode;
				}
				if(!empty($order['trackingdetails'])){
					$result[$key]['trackingdetails']['id'] = $order['trackingdetails'][0]['id'];
					$result[$key]['trackingdetails']['shippingdate'] = $order['trackingdetails'][0]['shippingdate'];
					$result[$key]['trackingdetails']['couriername'] = $order['trackingdetails'][0]['couriername'];
					$result[$key]['trackingdetails']['courierservice'] = $order['trackingdetails'][0]['courierservice'];
					$result[$key]['trackingdetails']['trackingid'] = $order['trackingdetails'][0]['trackingid'];
					$result[$key]['trackingdetails']['notes'] = $order['trackingdetails'][0]['notes'];

				}
				if(!empty($order['orderitems'])){
					$productId = $order['orderitems'][0]['productId'];
					$check = Products::model()->findByPk($productId);
					if(!empty($check)) {
						$productImage = $order['orderitems'][0]->product->photos[0]->name;
					}
					if(!empty($check)) {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId."/".$productImage);
					} else {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".'default.jpeg');
					}
					$result[$key]['orderitems']['itemid'] = $productId;
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					$result[$key]['orderitems']['buyer_name'] = $buyerName;
					$result[$key]['orderitems']['buyer_username'] = $buyerUsername;
					$result[$key]['orderitems']['buyer_email'] = $buyerEmail;
					$result[$key]['orderitems']['buyer_img'] = $buyerImage;
					$result[$key]['orderitems']['buyer_id'] = $buyerId;
					$result[$key]['orderitems']['quantity'] = $order['orderitems'][0]['itemQuantity'];
					$result[$key]['orderitems']['price'] = $order['orderitems'][0]['itemPrice'];
					$result[$key]['orderitems']['unitprice'] = $order['orderitems'][0]['itemunitPrice'];
					$result[$key]['orderitems']['size'] = $order['orderitems'][0]['itemSize'];
					$currencysymbol = $order['orderitems'][0]->product->currency;
					$symbolval = explode("-", $currencysymbol);
					$cSymbol = $symbolval[0];
					$result[$key]['orderitems']['cSymbol'] = $cSymbol;
					$result[$key]['orderitems']['orderImage'] = $orderImageUrl;
					$result[$key]['orderitems']['shipping_cost'] = $order['orderitems'][0]['shippingPrice'];
					$result[$key]['orderitems']['total'] = $order['orderitems'][0]['shippingPrice'] + $order['orderitems'][0]['itemPrice'];
				}
				endforeach;
				if(!empty($result)) {
					$result = json_encode($result);
					return '{"status":"true","result":'.$result.'}';
				} else {
					return '{"status":"true","message":"No Sales History Found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the order_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function orderdetails($api_username, $api_password, $user_id, $order_id, $offset = 0, $limit = 10){
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
			//$recent = $_POST['recent'];
				$userid = $user_id;
				//$offset = 0;
				//$limit = 10;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				$criteria = new CDbCriteria;
				$criteria->addCondition("userId = {$userid}");
				$criteria->addCondition("orderId = {$order_id}");
				/*if ($recent == 1){
					$criteria->addCondition("orderDate > {$timeline}");
				}else{
					$criteria->addCondition("orderDate < {$timeline}");
				}*/
				$ordersModel = Orders::model()->with('orderitems','trackingdetails')->findAll($criteria);
				foreach($ordersModel as $key => $order):
				$result[$key]['orderid'] = $order->orderId;
				$result[$key]['price'] = $order->totalCost;
				$result[$key]['saledate'] = $order->orderDate;
				$result[$key]['status'] = $order->status;
				if($order->status == "delivered")
					$result[$key]['deliverydate'] = $order->statusDate;
				else
					$result[$key]['deliverydate'] = "";

				$invoices = Invoices::model()->findByAttributes(array('orderId'=>$order->orderId));
				$result[$key]['transaction_id'] = $invoices->paymentTranxid;

				$sellerdata = Users::model()->findByPk($order->sellerId);
				$sellerId = $sellerdata->userId;
				$sellerName = $sellerdata->name;

				$userdata = Users::model()->findByPk($order->userId);
				$buyerId = $userdata->userId;
				$buyerName = $userdata->name;

				$shipping = Shippingaddresses::model()->findByPk($order->shippingAddress);
				if(!empty($shipping)) {
					$id = $shipping->shippingaddressId;
					$result[$key]['shippingaddress']['name'] = $shipping->name;
					$result[$key]['shippingaddress']['nickname'] = $shipping->nickname;
					$result[$key]['shippingaddress']['country'] = $shipping->country;
					$result[$key]['shippingaddress']['state'] = $shipping->state;
					$result[$key]['shippingaddress']['address1'] = $shipping->address1;
					$result[$key]['shippingaddress']['address2'] = $shipping->address2;
					$result[$key]['shippingaddress']['city'] = $shipping->city;
					$result[$key]['shippingaddress']['zipcode'] = $shipping->zipcode;
					$result[$key]['shippingaddress']['phone'] = $shipping->phone;
					$result[$key]['shippingaddress']['countrycode'] = $shipping->countryCode;
				}
				if(!empty($order['trackingdetails'])){
					$result[$key]['trackingdetails']['id'] = $order['trackingdetails'][0]['id'];
					$result[$key]['trackingdetails']['shippingdate'] = $order['trackingdetails'][0]['shippingdate'];
					$result[$key]['trackingdetails']['couriername'] = $order['trackingdetails'][0]['couriername'];
					$result[$key]['trackingdetails']['courierservice'] = $order['trackingdetails'][0]['courierservice'];
					$result[$key]['trackingdetails']['trackingid'] = $order['trackingdetails'][0]['trackingid'];
					$result[$key]['trackingdetails']['notes'] = $order['trackingdetails'][0]['notes'];

				}
				if(!empty($order['orderitems'])) {
					$productId = $order['orderitems'][0]['productId'];
					$check = Products::model()->findByPk($productId);
					if(!empty($check)) {
						$productImage = $order['orderitems'][0]->product->photos[0]->name;
					}
					if(!empty($check)) {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/100/".$productId."/".$productImage);
					} else {
						$orderImageUrl = Yii::app()->createAbsoluteUrl("/item/products/resized/100/".'default.jpeg');
					}
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					$result[$key]['orderitems']['itemname'] = $order['orderitems'][0]['itemName'];
					if($user_id == $order->userId)
					{
						$result[$key]['orderitems']['seller_name'] = $sellerName;
						$result[$key]['orderitems']['seller_id'] = $sellerId;
					}
					else if($user_id == $order->sellerId)
					{
						$result[$key]['orderitems']['buyer_name'] = $buyerName;
						$result[$key]['orderitems']['buyer_id'] = $buyerId;
					}
					$result[$key]['orderitems']['quantity'] = $order['orderitems'][0]['itemQuantity'];
					$result[$key]['orderitems']['price'] = $order['orderitems'][0]['itemPrice'];
					$result[$key]['orderitems']['unitprice'] = $order['orderitems'][0]['itemunitPrice'];
					$result[$key]['orderitems']['size'] = $order['orderitems'][0]['itemSize'];
					$result[$key]['orderitems']['cSymbol'] = $order->currency;
					$result[$key]['orderitems']['orderImage'] = $orderImageUrl;
					$result[$key]['orderitems']['shipping_cost'] = $order['orderitems'][0]['shippingPrice'];
					$result[$key]['orderitems']['total'] = $order['orderitems'][0]['shippingPrice'] + $order['orderitems'][0]['itemPrice'];
				}
				endforeach;
				if(!empty($result)) {
					$result = json_encode($result);
					return '{"status":"true","result":'.$result.'}';
				} else {
					return '{"status":"true","message":"No Purchase History Found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	   * @param string the api_username
	   * @param string the api_password
	   * @param string the user_id
	   * @param string the offset
	   * @param string the limit
       * @return string the json data
 	   * @soap
 	   */

	public function getreview($api_username, $api_password, $user_id, $offset = 0, $limit = 10){
		if ($this->authenticateAPI($api_username, $api_password)){
			$criteria = new CDbCriteria;
			$criteria->addCondition("reviewType LIKE 'order'");
			$criteria->addCondition("receiverId = $user_id");
			$criteria->order = "reviewId DESC";
			$criteria->limit = $limit;
			$criteria->offset = $offset;
			$reviewModel = Reviews::model()->with('orders', 'user')->findall($criteria);

			if(!empty($reviewModel)){
				foreach ($reviewModel as $key => $review) {
					$reviewDetails[$key]['review_id'] = $review->reviewId;
					$reviewDetails[$key]['rating'] = $review->rating;
					$reviewDetails[$key]['review_title'] = $review->reviewTitle;
					$reviewDetails[$key]['review_des'] = $review->review;
					$reviewDetails[$key]['created_date'] = $review->createdDate;
					$reviewDetails[$key]['user_id'] = $review['user']->userId;
					$reviewDetails[$key]['full_name'] = $review['user']->name;
					$reviewDetails[$key]['user_image'] = !empty($review['user']->userImage) ? Yii::app()->createAbsoluteUrl('user/resized/150/'.$review['user']->userImage) : Yii::app()->createAbsoluteUrl('user/resized/150/default/'.Myclass::getDefaultUser());
					$reviewDetails[$key]['item_id'] = $review['orders']['orderitems'][0]->productId;
					$reviewDetails[$key]['item_name'] = $review['orders']['orderitems'][0]->itemName;
				}
				$final = json_encode($reviewDetails);
				return  '{"status":"true","result":'.$final.'}';
			}else{
				return '{"status":"false", "message":"No reviews yet"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	   * @param string the api_username
	   * @param string the api_password
	   * @param string the user_id
	   * @param string the seller_id
	   * @param string the review_id
	   * @param string the rating
	   * @param string the review_title
	   * @param string the review_des
	   * @param string the order_id
       * @return string the json data
 	   * @soap
 	   */

	public function updatereview($api_username, $api_password, $user_id, $seller_id, $review_id, $rating, $review_title, $review_des, $order_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			if($review_id != 0){
				$reviewModel = Reviews::model()->findByPk($review_id);
			}else{
				$reviewModel = new Reviews();
			}
			$reviewModel->senderId = $user_id;
			$reviewModel->receiverId = $seller_id;
			$reviewModel->reviewTitle = $review_title;
			$reviewModel->review = $review_des;
			$reviewModel->rating = $rating;
			$reviewModel->reviewType = 'order';
			$reviewModel->sourceId = $order_id;
			$reviewModel->createdDate = time();
			$reviewModel->save(false);

			$orderModel = Orders::model()->findByPk($order_id);

			$sellerModel = Users::model()->with('reviewRating')->findByPk($orderModel->sellerId);
			$averageRatting = $sellerModel->reviewRating;
			$sellerModel->averageRating = $averageRatting;
			$sellerModel->save(false);

			return  '{"status":"true","result":"Review updated successfully"}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @return string the json data
	 * @soap
	 */
	public function getcoupon($api_username, $api_password, $user_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				$criteria = new CDbCriteria;
				$criteria->addCondition("sellerId = $userId");
				$coupons = Coupons::model()->findAll($criteria);
				if(!empty($coupons)) {
					foreach($coupons as $key => $value):
					$coupon[$key]['coupon_id'] = $value->id;
					$coupon[$key]['coupon_code'] = $value->couponCode;
					$coupon[$key]['coupon_value'] = $value->couponValue;
					$coupon[$key]['start_date'] = date("d-M-Y",strtotime($value->startDate));
					$coupon[$key]['end_date'] = date("d-M-Y",strtotime($value->endDate));
					$coupon[$key]['created_date'] = date("d-M-Y",strtotime($value->createdDate));
					$coupon[$key]['status'] = ($value->status == 1) ? 'Available' : 'Expired';
					endforeach;

					$coupon = json_encode($coupon);
					return '{"status":"true","coupons":'.$coupon.'}';
				} else {
					return '{"status":"true","message":"Coupon Not Found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the coupon_value
	 * @param string the type
	 * @param string the item_id
	 * @param string the start_date
	 * @param string the end_date
	 * @param string the status
	 * @param string the max_amount
	 * @return string the json data
	 * @soap
	 */
	public function createcoupon($api_username, $api_password, $user_id, $coupon_value, $type,
	$item_id, $start_date, $end_date, $status, $max_amount = 0) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$model = new Coupons();
				$model->sellerId = $user_id;
				$model->couponValue = $coupon_value;
				//$type = $_POST['type'];
				if($type == 'item') {
					$model->setScenario('itemView');
					$item = Products::model()->findByPk($item_id);
					$model->couponType = 1;
					$model->productId = $item_id;
					$currency = explode('-',$item->currency);
					$model->currency = $currency[0];
				} else {
					$model->setScenario('sellerProfile');
					$model->couponType = 2;
					$model->startDate = date("Y-m-d",strtotime($start_date));
					$model->endDate = date("Y-m-d",strtotime($end_date));
					if($max_amount != 0) {
						$model->maxAmount = $max_amount;
					}
				}
				//$status = $_POST['status'];
				if($status == 'enable') {
					$model->status = 1;
				} else {
					$model->status = 0;
				}
				$model->couponCode = Myclass::getRandomString(8);
				if($model->save(false)) {
					if($model->couponType == 1) {
						return '{"status":"true","result":'.$model->couponCode.'}';
					} else {
						$coupon['coupon_id'] = $model->id;
						$coupon['coupon_code'] = $model->couponCode;
						$coupon['coupon_value'] = $model->couponValue;
						$coupon['start_date'] = $model->startDate;
						$coupon['end_date'] = $model->endDate;
						$coupon['created_date'] = date("Y-m-d H:i:s");
						$coupon['status'] = $status;
						$coupon = json_encode($coupon);
						return '{"status":"true","result":'.$coupon.'}';
					}
				} else {
					return '{"status":"false","message":"Coupon cannot be created"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the coupon_id
	 * @return string the json data
	 * @soap
	 */
	public function couponstatus($api_username, $api_password, $user_id, $coupon_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$userId = $user_id;
				$couponId = $coupon_id;
				$criteria = new CDbCriteria;
				$criteria->addCondition("id = $couponId");
				$criteria->addCondition("sellerId = $userId");
				$findCoupon = Coupons::model()->find($criteria);
				if(!empty($findCoupon)) {
					$findCoupon->status = 0;
					$findCoupon->save(false);
					return '{"status":"true","result":"Coupon status changed successfully"}';
				} else {
					return '{"status":"false","result":"No Coupon found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the user_name
	 * @return string the json data
	 * @soap
	 */
	public function profile($api_username, $api_password, $user_id, $user_name = NULL) {
		if ($this->authenticateAPI($api_username, $api_password)){
		if($this->checking($user_id)){
				$proCriteria = new CDbCriteria;
				if ($user_id != 0){
					$userId = $user_id;
					$proCriteria->addCondition("userId = $userId");
				}
				if (!empty($user_name)){
					$userName = $user_name;
					$proCriteria->addCondition("username = $userName");
				}
				$model = Users::model()->find($proCriteria);

				if(!empty($model)) {

					$userDetails['user_id'] = $model->userId;
					$userDetails['user_name'] = $model->username;
					$userDetails['full_name'] = $model->name;


					if(!empty($model->userImage)) {
						$imageUrl = Yii::app()->createAbsoluteUrl('user/resized/150/'.$model->userImage);
					} else {
						$imageUrl = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					}
					$userDetails['user_img'] = $imageUrl;
					$userDetails['rating'] = $model->averageRating;
					$userDetails['email'] = $model->email;
					$userDetails['facebook_id'] = $model->facebookId;
					$userDetails['mobile_no'] = $model->phone;
					if($model->phonevisible == "1")
					{
						$userDetails['show_mobile_no'] = "true";
					}
					else
					{
						$userDetails['show_mobile_no'] = "false";
					}
					if($model->facebookId == ''){
						$userverdetails['facebook'] = 'false';
					}else{
						$userverdetails['facebook'] = 'true';
					}
					$userverdetails['email'] = 'true';
					if($model->mobile_status == '1'){
						$userverdetails['mob_no'] = 'true';
					}else{
						$userverdetails['mob_no'] = 'false';
					}

					$userDetails['verification'] = $userverdetails;
					$result = json_encode($userDetails);
					return '{"status":"true","result":'.$result.'}';
				} else {
					return '{"status":"false","result":"No user found"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function deleteproduct($api_username, $api_password, $user_id, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($user_id)){
				$itemId = $item_id;
				$userId = $user_id;
				$delCriteria = new CDbCriteria;
				$delCriteria->addCondition("productId = $itemId");
				$delCriteria->addCondition("userId = $userId");
				$productModel = Products::model()->find($delCriteria);

				Adspromotiondetails::model()->deleteAllByAttributes(array('productId'=>$itemId));
				Promotiontransaction::model()->deleteAllByAttributes(array('productId'=>$itemId));

				if(!empty($productModel)) {
					$productModel->delete();
					return '{"status":"true","message":"Product Deleted Successfully"}';
				} else {
					return '{"status":"false","message":"Sorry, Your Product is not deleted try after sometime"}';
				}
			}else{
				return $this->errorMessage;
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the item_id
	 * @param string the user_id
	 * @param string the lang_type
	 * @return string the json data
	 * @soap
	 */
	public function searchbyitem($api_username, $api_password, $item_id, $user_id = 0, $lang_type) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$item = Products::model()->with('user')->findByPk($item_id);
			Myclass::push_lang($lang_type);
			$userId = $user_id;
			if (!empty($item)){
				$itemkey = 0;
				$productId = $item->productId;
				$likedornot = $this->checkuserlike($userId, $productId);
				$items['items'][$itemkey]['id'] = $item->productId;
				$items['items'][$itemkey]['item_title'] = $item->name;
				$items['items'][$itemkey]['item_description'] = html_entity_decode($item->description);
				$items['items'][$itemkey]['item_condition'] = Yii::t('app',$item->productCondition);
				$items['items'][$itemkey]['price'] = $item->price;
				$items['items'][$itemkey]['quantity'] = $item->quantity;
				if($item->quantity > 0 && $item->soldItem == 0){
					$items['items'][$itemkey]['item_status'] = "onsale";
				}else{
					$items['items'][$itemkey]['item_status'] = "sold";
				}
				$items['items'][$itemkey]['size'] = "M";

				$items['items'][$itemkey]['seller_name'] = $item->user->name;
				$items['items'][$itemkey]['seller_username'] = $item->user->username;
				$items['items'][$itemkey]['seller_id'] = $item->user->userId;
				//$items['items'][$itemkey]['seller_img'] = $item->user->userImage;
				if($item->user->userImage == "")
					$items['items'][$itemkey]['seller_img'] = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
				else
					$items['items'][$itemkey]['seller_img'] = $userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$item->user->userImage);
				$items['items'][$itemkey]['seller_rating'] = $item->user->averageRating;
				$items['items'][$itemkey]['mobile_no'] = $item->user->phone;
				if($item->user->phonevisible == "1")
				{
					$items['items'][$itemkey]['show_seller_mobile'] = "true";
				}
				else
				{
					$items['items'][$itemkey]['show_seller_mobile'] = "false";
				}

				if($item->user->facebookId == ''){
					$items['items'][$itemkey]['facebook_verification'] = 'false';
				}else{
					$items['items'][$itemkey]['facebook_verification'] = 'true';
				}
				if($item->user->mobile_status == '1'){
					$items['items'][$itemkey]['mobile_verification'] = 'true';
				}else{
					$items['items'][$itemkey]['mobile_verification'] = 'false';
				}

				$items['items'][$itemkey]['email_verification'] = 'true';

				$items['items'][$itemkey]['currency_code'] = $item->currency;
				$items['items'][$itemkey]['product_url'] = Yii::app()->createAbsoluteUrl('/products/'.$item->productId);
				$items['items'][$itemkey]['likes_count'] = $item->likes;
				$items['items'][$itemkey]['comments_count'] = $item->commentCount;
				$items['items'][$itemkey]['views_count'] = $item->views;
				$items['items'][$itemkey]['liked'] = $likedornot;
//				$items['items'][$itemkey]['posted_time'] = Myclass::getElapsedTime($item->createdDate)." ago";
				$items['items'][$itemkey]['posted_time'] = $item->createdDate;
				$items['items'][$itemkey]['latitude'] = $item->latitude;
				$items['items'][$itemkey]['longitude'] = $item->longitude;
				$items['items'][$itemkey]['location'] = $item->location;
				$items['items'][$itemkey]['best_offer'] = "false";
				$buyType = "";
				if ($item->chatAndBuy){
					$buyType .= "contactme";
				}
				if($item->exchangeToBuy){
					$buyType .= $buyType == "" ? "swap" : ",swap";
				}
				if($item->instantBuy){
					$buyType .= $buyType == "" ? "sale" : ",sale";
				}
				$items['items'][$itemkey]['buy_type'] = $buyType;
				$items['items'][$itemkey]['paypal_id'] = $item->paypalid;

				$items['items'][$itemkey]['report'] = "no";
				if ($item->reports != ''){
					$reports = json_decode($item->reports, true);
					if(in_array($userId, $reports)){
						$items['items'][$itemkey]['report'] = "yes";
					}
				}

				if(isset($item->category0)){
				$items['items'][$itemkey]['category_id'] = $item->category0->categoryId;
				$items['items'][$itemkey]['category_name'] = Yii::t('app',$item->category0->name);
				}else{
					$items['items'][$itemkey]['category_id'] = "";
					$items['items'][$itemkey]['category_name'] = "";
				}
				if(isset($item->subCategory0)){
					$items['items'][$itemkey]['subcat_id'] = $item->subCategory0->categoryId;
					$items['items'][$itemkey]['subcat_name'] = Yii::t('app',$item->subCategory0->name);
				}else{
					$items['items'][$itemkey]['subcat_id'] = "";
					$items['items'][$itemkey]['subcat_name'] = "";
				}

				$items['items'][$itemkey]['promotion_type'] = 'Normal';
				if($item->promotionType == '3'){
					$items['items'][$itemkey]['promotion_type'] = "Normal";
				}elseif ($item->promotionType == '1') {
					$items['items'][$itemkey]['promotion_type'] = "Ad";
				}elseif ($item->promotionType == '2') {
					$items['items'][$itemkey]['promotion_type'] = "Urgent";
				}

				/*$items['items'][$itemkey]['exchange_buy'] = '';
				if($item->exchangeToBuy == '0'){
					$items['items'][$itemkey]['exchange_buy'] = "false";
				}else{
					$items['items'][$itemkey]['exchange_buy'] = "true";
				}

				$items['items'][$itemkey]['make_offer'] = '';
				if($item->myoffer == '1' || $item->myoffer == '2'){
					$items['items'][$itemkey]['make_offer'] = "true";
				}else{

					$items['items'][$itemkey]['make_offer'] = "false";
				}*/

				if($item->instantBuy == "1")
				{
					$items['items'][$itemkey]['country_id'] = $item->shippingcountry;
					$items['items'][$itemkey]['paypal_id'] = $item->paypalid;
					$items['items'][$itemkey]['shipping_cost'] = $item->shippingCost;
				}
				else
				{
					$items['items'][$itemkey]['country_id'] = "";
					$items['items'][$itemkey]['paypal_id'] = "";
					$items['items'][$itemkey]['shipping_cost'] = "";
				}

				$items['items'][$itemkey]['exchange_buy'] = $item->exchangeToBuy;
				$items['items'][$itemkey]['make_offer'] = $item->myoffer;
				$items['items'][$itemkey]['instant_buy'] = $item->instantBuy;

				if(isset($item->approvedStatus) && $item->approvedStatus == "1")
				{
					$items['items'][$itemkey]['item_approve'] = "1";
				}
				else
				{
					$items['items'][$itemkey]['item_approve'] = "0";
				}

				$items['items'][$itemkey]['shipping_detail'] = array();
					if($item->instantBuy){
						$shipKey = 0;
						$shippingArray = array();
						foreach($item->shippings as $shipping){
							$shippingArray[$shipKey]['country_id'] = $shipping->countryId;
							$shippingArray[$shipKey]['country_name'] = $shipping->country->country;
							$shippingArray[$shipKey]['shipping_cost'] = $shipping->shippingCost;
							$shipKey++;
						}
						$items['items'][$itemkey]['shipping_detail'] = $shippingArray;
					}


				$items['items'][$itemkey]['photos'] = array();
					foreach ($item->photos as $photo){
						$photoName = $photo->name;

						 $photodetails['item_url_main_350'] = Yii::app()->createAbsoluteUrl("/item/products/resized/350/".$productId.'/'.$photoName);
		       			 $photodetails['height'] = '350';
		       			 $photodetails['width'] = '350';
						 $photodetails['item_url_main_original'] = Yii::app()->createAbsoluteUrl('media/item/'.$productId.'/'.$photoName);
						 $items['items'][$itemkey]['photos'][] = $photodetails;
					}

				$result = json_encode($items);
				return '{"status": "true","result":'.$result.'}';
			}else{
				return '{"status":"false","message":"No item found"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the lang_type
	 * @return string the json data
	 * @soap
	 */
	public function productbeforeadd($api_username, $api_password,$lang_type) {
		if ($this->authenticateAPI($api_username, $api_password)){
			Myclass::push_lang($lang_type);
			$result = array();
			$maincategoryModel = Categories::model()->findAllByAttributes(array('parentCategory'=>'0'));
			$criteria = new CDbCriteria;
			$criteria->addCondition("parentCategory != 0");
			$subcategoryModel = Categories::model()->findAll($criteria);
			$categories = array();
			$subcategories = array();
			$maincategoryImage = array();
			foreach ($subcategoryModel as $subcategory){
				$subcategories[$subcategory->parentCategory][$subcategory->categoryId] = $subcategory->name;
			}

			foreach ($maincategoryModel as $catkey => $maincategory){
				$imageUrl = Yii::app()->createAbsoluteUrl('/admin/categories/resized/40/'.$maincategory->image);
				$result['category'][$catkey]['category_id'] = $maincategory->categoryId;
				$result['category'][$catkey]['category_name'] = Yii::t('app',$maincategory->name);
				$result['category'][$catkey]['category_img'] = $imageUrl;
				$categoryRules = json_decode($maincategory->categoryProperty, true);
				//{"itemCondition":"enable","exchangetoBuy":"enable","myOffer":"enable","contactSeller":"disable"}
				$result['category'][$catkey]['product_condition'] = $categoryRules['itemCondition'];
				$result['category'][$catkey]['exchange_buy'] = $categoryRules['exchangetoBuy'];
				$result['category'][$catkey]['make_offer'] = $categoryRules['myOffer'];
				$result['category'][$catkey]['instant_buy'] = $categoryRules['buyNow'];
				$result['category'][$catkey]['subcategory'] = array();
				if(isset($subcategories[$maincategory->categoryId])){
					$relatedSubcategory = $subcategories[$maincategory->categoryId];
					$relatedkey = 0;
					foreach ($relatedSubcategory as $relatedCategorykey => $relatedCategory){
						$result['category'][$catkey]['subcategory'][$relatedkey]['sub_id'] = "$relatedCategorykey";
						$result['category'][$catkey]['subcategory'][$relatedkey]['sub_name'] = Yii::t('app',$relatedCategory);
						$relatedkey++;
					}
				}

			}
			$countryModel = Country::model()->findAll();
			$currencyModel = Currencies::model()->findAll();
			$productConditionModel = Productconditions::model()->findAllByAttributes();

			foreach ($productConditionModel as $productConditionKey => $productCondition){
				$result['product_condition'][$productConditionKey]['name'] = Yii::t('app',$productCondition->condition);
			}

			foreach ($currencyModel as $currencykey => $currency){
				$result['currency'][$currencykey]['id'] = $currency->id;
				$result['currency'][$currencykey]['symbol'] = $currency->currency_shortcode.
				"-".$currency->currency_symbol;
			}

			foreach ($countryModel as $countrykey => $country){
				$result['country'][$countrykey]['country_id'] = $country->countryId;
				$result['country'][$countrykey]['country_code'] = $country->code;
				Myclass::push_lang($lang_type);
				$result['country'][$countrykey]['country_name'] = Yii::t('app',$country->country);
			}
			$result['shipDeliveryTime'][0]['id'] = '1 business day';
			$result['shipDeliveryTime'][0]['Time'] = '1 business day';
			$result['shipDeliveryTime'][1]['id'] = '1-2 business day';
			$result['shipDeliveryTime'][1]['Time'] = '1-2 business day';
			$result['shipDeliveryTime'][2]['id'] = '2-3 business day';
			$result['shipDeliveryTime'][2]['Time'] = '2-3 business day';
			$result['shipDeliveryTime'][3]['id'] = '3-5 business day';
			$result['shipDeliveryTime'][3]['Time'] = '3-5 business day';
			$result['shipDeliveryTime'][4]['id'] = '1-2 weeks';
			$result['shipDeliveryTime'][4]['Time'] = '1-2 weeks';
			$result['shipDeliveryTime'][5]['id'] = '2-4 weeks';
			$result['shipDeliveryTime'][5]['Time'] = '2-4 weeks';
			$result['shipDeliveryTime'][6]['id'] = '5-8 weeks';
			$result['shipDeliveryTime'][6]['Time'] = '5-8 weeks';

			if(!empty($result)) {
				return '{"status": "true","result":'.json_encode($result).'}';
			} else {
				return '{"status": "false","message":"No data found"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the lang_type
	 * @return string the json data
	 * @soap
	 */
	public function admindatas($api_username, $api_password, $lang_type){
		if ($this->authenticateAPI($api_username, $api_password)){
			Myclass::push_lang($lang_type);
			$siteSettings = Sitesettings::model()->find();

			$paymentModes = "";
			$resultarray = array();
			if(!empty($siteSettings->sitepaymentmodes))
			{
				$paymentModes = json_decode($siteSettings->sitepaymentmodes, true);
				if($paymentModes['exchangePaymentMode'] == "1")
					$resultarray['exchange'] = 'enable';
				else
					$resultarray['exchange'] = 'disable';
				if($paymentModes['buynowPaymentMode'] == "1")
					$resultarray['buynow'] = 'enable';
				else
					$resultarray['buynow'] = 'disable';
			}
			else
			{
				$resultarray['exchange'] = 'disable';
				$resultarray['buynow'] = 'disable';
			}
			if($siteSettings->promotionStatus == "1")
			{
				$resultarray['promotion'] = 'enable';
			}
			else
			{
				$resultarray['promotion'] = 'disable';
			}
			if($siteSettings->bannerstatus == "1")
			{
				$resultarray['banner'] = 'enable';
				$banners = Banners::model()->findAll();
				foreach ($banners as $key => $value) {
					$resultarray['bannerData'][$key]['bannerImage'] = Yii::app()->createAbsoluteUrl('/media/banners/'.$value->appbannerimage);
					$resultarray['bannerData'][$key]['bannerURL'] = $value->bannerurl;
				}
			}
			else
			{
				$resultarray['banner'] = 'disable';
				$resultarray['bannerData'] = "";
			}
			if($siteSettings->searchType=='miles') { $distanceType='mi'; }
			else { $distanceType='km'; }
			$resultarray['distance_type'] = $distanceType;
			$resultarray['block'] = "YES";
			//$result = array();
			$maincategoryModel = Categories::model()->findAllByAttributes(array('parentCategory'=>'0'));
			foreach ($maincategoryModel as $catkey => $maincategory)
				{
					$imageUrl = Yii::app()->createAbsoluteUrl('/admin/categories/resized/150/'.$maincategory->image);
					$resultarray['category'][$catkey]['category_id'] = $maincategory->categoryId;
					$resultarray['category'][$catkey]['category_name'] = Yii::t('app',$maincategory->name);
					$resultarray['category'][$catkey]['category_img'] = $imageUrl;
				}
			return '{"status": "true","result":'.json_encode($resultarray).'}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @return string the json data
	 * @soap
	 */
	public function getcountdetails($api_username, $api_password, $user_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			if(isset($user_id) && $user_id != "")
			{
				$messageCount = Myclass::getMessageCount($user_id);
				$notificationCount = Myclass::getNotificationCount($user_id);
				$resultarray['chatCount'] = $messageCount;
				$resultarray['notificationCount'] = $notificationCount;
				return '{"status":"true","result":'.json_encode($resultarray).'}';
			}
			else
			{
				return '{"status":"false", "message":"Something went wrong"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @param string the comment_id
	 * @return string the json data
	 * @soap
	 */
	public function deletecomment($api_username, $api_password, $user_id, $comment_id, $item_id){
		if ($this->authenticateAPI($api_username, $api_password)){
			if(isset($user_id) && $user_id != "" && isset($comment_id) && $comment_id != "")
			{
				$commentId = $comment_id;
				$condition = new CDbCriteria;
				$condition->addCondition('commentId = "'.$commentId.'"');
				$commentModel = Comments::model()->find($condition);
				$commentModel->delete();

				$logCriteria = new CDbCriteria();
				$logCriteria->addCondition("type LIKE 'comment'");
				$logCriteria->addCondition("sourceId = $commentId");
				$logsModel = Logs::model()->find($logCriteria);
				if(!empty($logsModel))
				$logsModel->delete();

				$productModel = Products::model()->findByPk($item_id);
				$productModel->commentCount = $productModel->commentCount - 1;
				$productModel->save();

				return '{"status":"true","message":"Comment deleted successfully"}';
			}
			else
			{
				return '{"status":"false", "message":"Something went wrong"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @return string the json data
	 * @soap
	 */
	public function getpromotion($api_username, $api_password){
		if ($this->authenticateAPI($api_username, $api_password)){
			$promotionDetails = Promotions::model()->findAll();

			$siteSettings = Sitesettings::model()->find();
			$urgentPrice = $siteSettings->urgentPrice;
			$promotionCurrency = $siteSettings->promotionCurrency;
			$promotionCurrency = explode('-', $promotionCurrency);

			$promotionData['urgent'] = $urgentPrice;
			$promotionData['currency_symbol'] = $promotionCurrency[1];
			str_replace(" ","",$promotionCurrency[0]);
			$promotionData['currency_code'] = $promotionCurrency[0];
			$promotionData['other_promotions'] = array();

			foreach($promotionDetails as $promotionDetailKey => $promotionDetail){
				$promotionData['other_promotions'][$promotionDetailKey]['id'] = $promotionDetail->id;
				$promotionData['other_promotions'][$promotionDetailKey]['name'] = $promotionDetail->name;
				$promotionData['other_promotions'][$promotionDetailKey]['price'] = $promotionDetail->price;
				$promotionData['other_promotions'][$promotionDetailKey]['days'] = $promotionDetail->days;
			}
			return '{"status": "true","result":'.json_encode($promotionData).'}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the type
	 * @return string the json data
	 * @soap
	 */
	public function mypromotions($api_username, $api_password, $user_id,$type){
		if ($this->authenticateAPI($api_username, $api_password)){
		if($this->checking($user_id)){
			if($type == 'urgent') {
				$criteria = new CDbCriteria;
				$criteria->addCondition("userId = $user_id");
				$criteria->addCondition("promotionType = '2'");
				$criteria->order = 'productId DESC';
				$products = Products::model()->findAll($criteria);
			}elseif($type == 'ad'){
				$criteria = new CDbCriteria;
				$criteria->addCondition("userId = $user_id");
				$criteria->addCondition("promotionType = '1'");
				$criteria->order = 'productId DESC';
				$products = Products::model()->findAll($criteria);
			}elseif ($type == 'expire'){
				$criteria = new CDbCriteria;
				$criteria->addCondition("userId = $user_id");
				$criteria->addCondition("promotionType = '3'");
				$criteria->order = 'productId DESC';
				$products = Products::model()->findAll($criteria);
			}
				//var_dump($products);
				if(!empty($products)){
					$key = 0;
					foreach($products as $product) {
						$productId = $product->productId;
						$product_criteria = new CDbCriteria;
						$product_criteria->addCondition("productId = $productId");
						if($type == 'ad'){
							$product_criteria->addCondition("status = 'Live'");
						}elseif ($type == 'expire'){
							$product_criteria->addCondition("status = 'Expired'");
						}
						$product_criteria->order = 'id DESC';
						$promot_detail = Promotiontransaction::model()->find($product_criteria);
						if(!empty($promot_detail)){
							$promotions[$key]['id'] = $promot_detail->id;
							$promotions[$key]['promotion_name'] = $promot_detail->promotionName;
							$promotions[$key]['paid_amount'] = $promot_detail->promotionPrice;
							$currency = '';
							$currency = explode('-',$product->currency);
							$promotions[$key]['currency_symbol'] = $currency[0];
							$promotions[$key]['currency_code'] = $currency[1];
							$start_date = date("M d Y",$promot_detail->createdDate);
							$end_date = date("M d Y",strtotime("+".$promot_detail->promotionTime."  days" , $promot_detail->createdDate));

							$promotions[$key]['upto'] = strtotime($start_date).' - '.strtotime($end_date);
							$promotions[$key]['transaction_id'] = $promot_detail->tranxId;
							$promotions[$key]['status'] = $promot_detail->status;
							$promotions[$key]['item_id'] = $product->productId;
							$promotions[$key]['item_name'] = $product->name;
							$promotions[$key]['item_image'] = $product->photos[0]->name;

							if(isset($product->approvedStatus) && $product->approvedStatus == "1")
							{
								$promotions[$key]['item_approve'] = "1";
							}
							else
							{
								$promotions[$key]['item_approve'] = "0";
							}

							$key++;
							/*foreach ($product->photos as $photo){
								$photoName = $photo->name;
								$promotions[$key]['item_image'] = $photoName;
								//break;
							}*/
						}
					}
					if(!empty($promotions)){
					$promotion_details = json_encode($promotions);
					return '{"status": "true", "result":'.$promotion_details.'}';
				}else{
					return "{'status':'false', 'message':'No data found'}";
				}
			}else{
				return "{'status':'false', 'message':'No data found'}";
			}

			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
		}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the userId
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function notification($api_username, $api_password, $userId, $offset = 0, $limit = 20){
		if ($this->authenticateAPI($api_username, $api_password)){
			$model = Users::model()->findByPk($userId);

			if($model->unreadNotification != 0){
				$model->unreadNotification = 0;
				$model->save(false);
			}

			$followersModel = Followers::model()->findAllByAttributes(array('userId'=>$userId));
			$followers = array();

			foreach ($followersModel as $follower){
				$followers[] = $follower->follow_userId;
			}

			$criteria = new CDbCriteria;
			$criteria->addInCondition('userid', $followers);
			$criteria->addCondition("type LIKE 'Add'", 'AND');
			$criteria->addCondition("notifyto = $userId", 'OR');
			$criteria->addCondition("type LIKE 'Admin'", 'OR');
			$criteria->addCondition('createddate > "'.$model->createdDate.'" ');
			$criteria->order = "id DESC";
			$criteria->limit = $limit;
			$criteria->offset = $offset;

			$logModel = Logs::model()->findAll($criteria);
			$notificationData = array();

			if(!empty($logModel)){
				foreach ($logModel as $logKey => $log){
					$productModel = array();
					if($log->itemid != 0){
						$productModel = Myclass::getProductDetails($log->itemid);
					}
					$userModel = Myclass::getUserDetails($log->userid);
					if(!empty($userModel->userImage)){
						$userImage = Yii::app()->createAbsoluteUrl('user/resized/150/'.$userModel->userImage);
					}else{
						$userImage = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					}
					//$createdDate = date('jS M Y', $log->createddate);

					$notificationData[$logKey]['type'] = $log->type;
					$notificationData[$logKey]['message'] = $log->notifymessage;
					$notificationData[$logKey]['event_time'] = $log->createddate;
					$notificationData[$logKey]['user_image'] = $userImage;
					if($log->type === 'admin'){
						$notificationData[$logKey]['message'] = $log->message;
					}
					if($log->type !== 'admin'){
						$notificationData[$logKey]['user_id'] = $log->userid;
						$notificationData[$logKey]['user_name'] = $userModel->name;
						if(!empty($productModel)){
							$notificationData[$logKey]['item_id'] = $productModel->productId;
							$notificationData[$logKey]['item_title'] = $productModel->name;

							if(isset($productModel->photos[0])){
								$productImage = Yii::app()->createAbsoluteUrl('item/products/resized/150/'.$productModel->productId.
										'/'.$productModel->photos[0]->name);
							}else{
								$productImage = Yii::app()->createAbsoluteUrl('item/products/resized/150/default.jpeg');
							}
							$notificationData[$logKey]['item_image'] = $productImage;
						}
					}

				}

				return '{"status": "true","result":'.json_encode($notificationData).'}';
			}else{
				return '{"status":"false","message":"No notifications found"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the sender_id
	 * @param string the receiver_id
	 * @return string the json data
	 * @soap
	 */
	public function getchatid($api_username, $api_password, $sender_id, $receiver_id) {
		if ($this->authenticateAPI($api_username, $api_password)){
			if($this->checking($sender_id)){
				$senderId = $sender_id;
				$receiverId = $receiver_id;

				$chatCriteria = new CDbCriteria;
				$chatCriteria->condition = "user1 = '$senderId' AND user2 = '$receiverId' OR user1 = '$receiverId' AND user2 = '$senderId'";
				$chat = Chats::model()->find($chatCriteria);
				if(empty($chat)) {
					$newChat = new Chats();
					$newChat->user1 = $senderId;
					$newChat->user2 = $receiverId;
					$newChat->lastContacted = time();
					$newChat->save(false);
					$chatId = $newChat->chatId;
				} else {
					$chatId = $chat->chatId;
				}
				if(!empty($chatId)) {
					return '{"status": "true","chat_id":'.$chatId.'}';
				}else {
					return '{"status": "false","message":"Chat id cannot be created"}';
				}
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the sender_id
	 * @param string the receiver_id
	 * @param string the type
	 * @param string the source_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */
	public function getchat($api_username, $api_password, $sender_id, $receiver_id, $type,
	$source_id, $offset = 0, $limit = 20) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($sender_id)){
				$senderId = $sender_id;
				$receiverId = $receiver_id;
				//$type = $_POST['type'];
				$sourceId = $source_id;

				//$limit = 20;
				if (isset($_POST['limit'])){
					$limit = $_POST['limit'];
				}
				//$offset = 0;
				if (isset($_POST['offset'])){
					$offset = $_POST['offset'];
				}
				$chatCriteria = new CDbCriteria;
				$chatCriteria->condition = "user1 = '$senderId' AND user2 = '$receiverId' OR user1 = '$receiverId' AND user2 = '$senderId'";
				$chat = Chats::model()->find($chatCriteria);
				if(!empty($chat)){
					if ($type == 'normal'){
						if($chat->lastToRead != 0 && $chat->lastToRead == $sender_id){
							$chat->lastToRead = 0;
							$chat->save(false);
						}
						$messageType[] = 'normal';
						$messageType[] = 'offer';
						$messageModel = Messages::model()->findAllByAttributes(array('chatId'=>$chat->chatId,
							'messageType'=>$messageType),array('order' => 'messageId DESC','limit' => $limit,'offset' => $offset));
					}else{
						$messageModel = Messages::model()->findAllByAttributes(array('chatId'=>$chat->chatId,'sourceId'=> $sourceId,
								'messageType'=>$type),array('order' => 'messageId DESC','limit' => $limit,'offset' => $offset));
					}
					$chats = array();
					if(!empty($messageModel)) {
						foreach($messageModel as $key => $message):
						$senderDetails = Myclass::getUserDetails($message->senderId);
						if($chat->user1 == $message->senderId) {
							$receiver = $chat->user2;
						} else {
							$receiver = $chat->user1;
						}
						$receiverDetails = Myclass::getUserDetails($receiver);
						$chats['chats'][$key]['receiver'] = $receiverDetails->username;
						$chats['chats'][$key]['sender'] = $senderDetails->username;
						if($message->sourceId != 0 && $message->messageType != 'exchange'){
							$chatSourceItem = Myclass::getProductDetails($message->sourceId);
							if($message->messageType == 'normal'){
								$chats['chats'][$key]['type'] = "about";
							}elseif ($message->messageType == 'offer'){
								$chats['chats'][$key]['type'] = "offer";
								$offerDetails = json_decode($message->message, true);
								$offerCurrency = explode('-', $offerDetails['currency']);
								$chats['chats'][$key]['offer_price'] = $offerCurrency[0].$offerDetails['price'];
							}
							$chats['chats'][$key]['item_id'] = $message->sourceId;
							$chats['chats'][$key]['item_title'] = $chatSourceItem->name;
							if(isset($chatSourceItem->photos[0])){
								$chats['chats'][$key]['item_image'] = Yii::app()->createAbsoluteUrl(
										'item/products/resized/80/'.$chatSourceItem->productId.
										'/'.$chatSourceItem->photos[0]->name);
							}else{
								$chats['chats'][$key]['item_image'] = Yii::app()->createAbsoluteUrl('item/products/resized/80/default.jpeg');
							}
						}else{
							$chats['chats'][$key]['type'] = "message";
						}
						$chats['chats'][$key]['message']['userName'] = $receiverDetails->username;
						if(!empty($receiverDetails->userImage)) {
							$currentChatUserImage = $receiverDetails->userImage;
						} else {
							$currentChatUserImage = Myclass::getDefaultUser();
						}
						$chats['chats'][$key]['message']['userImage'] = $currentChatUserImage;
						$chats['chats'][$key]['message']['chatTime'] = $message->createdDate;
						if($message->messageType == 'offer'){
							$chats['chats'][$key]['message']['message'] = $offerDetails['message'];
						}else{
							$chats['chats'][$key]['message']['message'] = urldecode($message->message);
						}
						endforeach;
						$chatURL = Yii::app()->createAbsoluteUrl("/message/".Myclass::safe_b64encode($senderDetails->userId.'-0'));
						//$chatURL = Yii::app()->baseUrl."/message/".Myclass::safe_b64encode($senderDetails->userId.'-0');
						return '{"status": "true","chat_id":'.$chat->chatId.',"chat_url":"'.$chatURL.'","chats":'.json_encode($chats).'}';
					} else {
						return '{"status": "false","message":"No Chat History Found"}';
					}
				}
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the sender_id
	 * @param string the chat_id
	 * @param string the type
	 * @param string the message
	 * @param string the created_date
	 * @param string the source_id
	 * @return string the json data
	 * @soap
	 */
	public function sendchat($api_username, $api_password, $sender_id, $chat_id, $type, $message,
	$created_date = 0, $source_id) {
		if ($this->authenticateAPI($api_username, $api_password)){

			if($this->checking($sender_id)){
				$senderId = $sender_id;
				$chatId = $chat_id;
				$messageType = $type;
				//$message = $_POST['message'];
				if($created_date != 0) {
					$createdDate = $created_date;
				} else {
					$createdDate = time();
				}
				$sourceId = $source_id;

				$messageModel = new Messages();
				$messageModel->message = $message;
				$messageModel->messageType = $messageType;
				$messageModel->senderId = $senderId;
				$messageModel->sourceId = $sourceId;
				$messageModel->chatId = $chatId;
				$messageModel->createdDate = $createdDate;
				$messageModel->save();

				$chatModel = Chats::model()->findByPk($chatId);

				$chatModel->lastContacted = $createdDate;
				if ($chatModel->user1 == $senderId){
					$chatModel->lastToRead = $chatModel->user2;
				}else{
					$chatModel->lastToRead = $chatModel->user1;
				}
				if ($messageType != 'exchange')
					$chatModel->lastMessage = $message;
				$chatModel->save();

		if($sourceId != 0 && $messageType == "normal"){
			$userid = $chatModel->user1;
			if($chatModel->user2 != $sender_id)
			{
			$userid = $chatModel->user2;
			}
			$sellerDetails = Myclass::getUserDetails($sender_id);
			$criteria = new CDbCriteria;
			$criteria->addCondition('user_id = "'.$userid.'"');
			$userdevicedet = Userdevices::model()->findAll($criteria);

			$notifyMessage = 'contacted you on your product';
			Myclass::addLogs("myoffer", $senderId, $userid, $sourceId, $sourceId, $notifyMessage);


		}
		if($messageType == "normal")
		{
			$userid = $chatModel->lastToRead;
			$criteria = new CDbCriteria;
			$criteria->addCondition('user_id = "'.$userid.'"');
			$userdevicedet = Userdevices::model()->findAll($criteria);
		$sellerDetails = Myclass::getUserDetails($sender_id);
			if(count($userdevicedet) > 0){
				foreach($userdevicedet as $userdevice){
				$deviceToken = $userdevice->deviceToken;
				$badge = $userdevice->badge;
				$badge +=1;
				$userdevice->badge = $badge;
				$userdevice->deviceToken = $deviceToken;
				$userdevice->save(false);
				if(isset($deviceToken)){
					$messages = $sellerDetails->name." : ".$message;
					Myclass::pushnot($deviceToken,$messages,$badge,"message");
				}
				}
			}
		}

				return '{"status": "true","message":"Message sent successfully"}';
			}
		} else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the coupon_code
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function applycoupon($api_username, $api_password, $coupon_code, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$couponCode = $coupon_code;
			$itemId = $item_id;
			$itemModel = Products::model()->findByPk($itemId);
			$couponCriteria = new CDbCriteria;
			$couponCriteria->addCondition("couponCode = '{$couponCode}'");
			$couponCriteria->addCondition("status = 1");
			$coupons = Coupons::model()->find($couponCriteria);
			if(!empty($coupons)) {
				if($coupons->couponType == 1) {
					if($coupons->productId == $itemId) {
						$result['type'] = 'itemcoupon';
						$result['coupon_id'] = $coupons->id;
						$result['discount_amount'] = $coupons->couponValue;

						return '{"status": "true","result":'.json_encode($result).'}';
					} else {
						return '{"status" : false, "message" : "Coupon Expired"}';
					}
				} elseif($coupons->couponType == 2) {
					if($coupons->sellerId != $itemModel->userId){
						return '{"status" : false, "message" : "Invalid Coupon"}';
					}
					$result['type'] = 'general';
					$result['coupon_id'] = $coupons->id;
					$result['discount_amount'] = $coupons->couponValue;
					$result['max_amount'] = $coupons->maxAmount;
					return '{"status": "true","result":'.json_encode($result).'}';
				} else {
					return '{"status" : false, "message" : "Coupon Expired"}';
				}
			}  else {
				return '{"status" : false, "message" : "Invalid Coupon"}';
			}
		} else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the shipping_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function shippingavail($api_username, $api_password, $shipping_id, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$shippingId = $shipping_id;
			$itemId = $item_id;
			$shipping = Tempaddresses::model()->findByPk($shippingId);
			if(!empty($shipping)) {
				$shippingCountry = $shipping->countryCode;
				$shippingArray = Shipping::model()->findAllByAttributes(array("productId" => $itemId));
				$shippingCost = 0;
				$everywhereCost = 0;
				$everyShipFlag = 0;
				$countryShipFlag = 0;
				foreach($shippingArray as $shipping){
					if($shippingCountry == $shipping->countryId){
						$shippingCost = $shipping->shippingCost;
						$countryShipFlag = 1;
					}elseif($shipping->countryId == 0){
						$everywhereCost = $shipping->shippingCost;
						$everyShipFlag = 1;
					}
				}
				if($countryShipFlag != 0) {
					return '{"status": "true","result":{"shipping_cost":"'.$shippingCost.'"}}';
				} else {
					if($everyShipFlag != 0) {
						return '{"status": "true","result":{"shipping_cost":"'.$everywhereCost.'"}}';
					} else {
						return '{"status": "false","message":"Shipping cannot be done"}';
					}
				}
			} else {
				return '{"status":"false", "message":"Record not found"}';
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}
	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @return string the json data
	 * @soap
	 */
	public function Getlikedid($api_username, $api_password,$user_id){
		$userId = $user_id;
		if ($this->authenticateAPI($api_username, $api_password)){
		$userModel = Favorites::model()->findAllByAttributes(array('userId'=>$userId));
		if(!empty($userModel)){
			foreach ($userModel as $user) {
				$userdetails[] = $user->productId;
			}
			$userdetails = json_encode($userdetails);
			return '{"status": "true","result":'.$userdetails.'}';
			}else{
				return "{'status':'false', 'message':'No data found’'}";
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @return string the json data
	 * @soap
	 */
	public function Getfollowerid($api_username, $api_password,$user_id){
		$userId = $user_id;
		if ($this->authenticateAPI($api_username, $api_password)){
		$userModel = Followers::model()->findAllByAttributes(array('userId'=>$userId));
		if(!empty($userModel)){
			foreach ($userModel as $user) {
				$userdetails[] = $user->follow_userId;
			}
			$userdetails = json_encode($userdetails);
			return '{"status": "true","result":'.$userdetails.'}';
		}else{
			return "{'status':'false', 'message':'No data found’'}";
		}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the value
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function solditem($api_username, $api_password, $value, $item_id) {
		if ($this->authenticateAPI($api_username, $api_password)){
			$id = $item_id;
			//$value = $_POST['value'];
			$product = Products::model()->findByPK($id);
			if(!empty($product)) {
				if($value == 1) {
					if($product->promotionType != 3){
						$promotionCriteria = new CDbCriteria();
						$promotionCriteria->addCondition("productId = $id");
						$promotionCriteria->addCondition("status LIKE 'live'");
						$promotionModel = Promotiontransaction::model()->find($promotionCriteria);
						if(!empty($promotionModel)){
							if($promotionModel->promotionName != 'urgent'){
								$previousCriteria = new CDbCriteria();
								$previousCriteria->addCondition("productId = $id");
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
						$product->promotionType = 3;
					}

					$product->soldItem = 1;
					$product->quantity = 0;

					$product->save(false);
					return '{"status": "true","message":"Item Status changed to Sold"}';
				} else {
					$product->soldItem = 0;
					$product->quantity = 1;
					$product->save(false);
					return '{"status": "true","message":"Item Status changed to Available"}';
				}
			} else {
				return '{"status": "false","message":"Item status cannot be changed"}';
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */
	public function reportitem($api_username, $api_password, $user_id, $item_id){
	/* public function actionReportitem(){
		$user_id = $_POST['user_id'];
		$item_id = $_POST['item_id']; */
		if ($this->authenticateAPI($api_username, $api_password)){
			$id = $item_id;
			$product = Products::model()->findByPK($id);
			if(!empty($product)) {
				if($product->reports == ""){
					$reports[] = $user_id;
					$product->reportCount += 1;
					$message = '{"status": "true","message":"Reported Successfully"}';
				}else{
					$reports = json_decode($product->reports, true);
					if(($key = array_search($user_id, $reports)) !== false){
						unset($reports[$key]);

						$product->reportCount -= 1;
						$message = '{"status": "true","message":"Unreported Successfully"}';
					}else{
						$reports[] = $user_id;
						$product->reportCount += 1;
						$message = '{"status": "true","message":"Reported Successfully"}';
					}
				}
				if(empty($reports)) {
					$product->reports = '';
				} else {
					$reportData = json_encode($reports);
					$product->reports = $reportData;
				}
				$product->save(false);
				return $message;
			} else {
				return '{"status": "false","message":"Item invalid"}';
			}
		}else {
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	 /**
     * @param string the api_username
     * @param string the api_password
     * @param string the deviceToken
     * @return string the json data
     * @soap
     */

	public function resetbadge($api_username, $api_password, $deviceToken){
                 $criteria = new CDbCriteria;
                 $criteria->addCondition('deviceToken = "'.$deviceToken.'"');
                 $userdevicedatas = Userdevices::model()->find($criteria);
                 $userdevicedatas->badge = '0';
                 $userdevicedatas->save(false);
                 if($userdevicedatas->save(false))
                 {
                 	return '{"status": "false","message":"Badge reset successfully"}';
                 }
                 else
                 {
                 	return '{"status": "false","message":"Something went wrong, Please try again"}';
                 }
	}

	 /**
     * @param string the api_username
     * @param string the api_password
	  * @param string the deviceId
     * @param string the userid
     * @param string the devicetype
     * @param string the deviceToken
     * @param string the devicemode
     * @param string the lang_type
     * @return string the json data
     * @soap
     */
    public function adddeviceid($api_username, $api_password, $deviceId, $userid, $devicetype, $deviceToken, $devicemode, $lang_type){
      /*public function actionAdddeviceid(){
	     $deviceId = $_POST['deviceId'];
        $userid = $_POST['userId'];
	     $deviceToken = $_POST['deviceToken'];
        $devicetype = $_POST['devicetype'];
        $api_username = $_POST['api_username'];
        $api_password = $_POST['api_password'];*/
        if ($this->authenticateAPI($api_username, $api_password)){

                 $criteria = new CDbCriteria;
                 $criteria->addCondition('deviceId = "'.$deviceId.'"');
                 $userdevicedatas = Userdevices::model()->find($criteria);
                 //print_r($userdevicedatas);exit;
                 if(isset($deviceId) && trim($deviceId)!=''){
                          if (isset($devicetype)){
                              		if(!empty($userdevicedatas)){
                                               	if (isset($devicetype)){
                                                            $userdevicedatas->deviceId = $deviceId;
                                                            $userdevicedatas->user_id = $userid;
                                                            $userdevicedatas->type =  $devicetype;
                                                            $userdevicedatas->mode = $devicemode;
                                                            $userdevicedatas->lang_type = $lang_type;
                                                            $userdevicedatas->save(false);
                                                 }
                                                 if (isset($deviceToken)){
                                                            $userdevicedatas->deviceId = $deviceId;
                                                            $userdevicedatas->user_id = $userid;
                                                            $userdevicedatas->deviceToken =  $deviceToken;
                                                            $userdevicedatas->mode = $devicemode;
                                                            $userdevicedatas->lang_type = $lang_type;
                                                            $userdevicedatas->save(false);
                                                }

                                    }else{
                                              $newdevice = new Userdevices();
                                              $newdevice->deviceId = $deviceId;
                                              $newdevice->user_id = $userid;
                                              $newdevice->deviceToken = $deviceToken;
                                              $newdevice->type =  $devicetype;
                                              $newdevice->mode = $devicemode;
                                              $newdevice->lang_type = $lang_type;
                                              $newdevice->cdate = time();
                                              $newdevice->save(false);
                              	   }
                            return '{"status":"true","result":"Registered successfully"}';
                         }
                     }else{
                                return '{"status":"false","result":"Something went wrong, please try again later"}';
                     }

                   	} else {
                        return '{"status":"false", "message":"Unauthorized Access to the API"}';
                     }
      }

      /**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the offset
	 * @param string the limit
	 * @return string the json data
	 * @soap
	 */

	public function Followersdetails($api_username, $api_password, $user_id, $offset = 0, $limit = 20){

		if ($this->authenticateAPI($api_username, $api_password)){
			$userId = $user_id;
			if (isset($userId) && $userId != ""){

				$criteria = new CDbCriteria;
				$criteria->limit = $limit;
				$criteria->offset = $offset;
				$criteria->addCondition("follow_userId = '$userId'");
				$FollowersModel = Followers::model()->findAll($criteria);
				foreach ($FollowersModel as $followkey => $Followers) {
					$follow_userId = $Followers->userId;
					$userModel = Users::model()->findAllByAttributes(array('userId'=>$follow_userId));
					$result[$followkey]['user_id'] = $userModel['0']['userId'];
					$result[$followkey]['user_name'] = $userModel['0']['username'];
					$result[$followkey]['full_name'] = $userModel['0']['name'];

					$_FollowersModel = Followers::model()->findAllByAttributes(array('userId'=>$userId,'follow_userId'=>$follow_userId));

					if (count($_FollowersModel) > 0){
						$result[$followkey]['status'] = "unfollow";
					}else{
						$result[$followkey]['status'] = "follow";
					}

					if(isset($userModel['0']['userImage'])){
						$imageUrl = Yii::app()->createAbsoluteUrl('/user/resized/150/'.$userModel['0']['userImage']);
					}else{
						$imageUrl = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					}

					$result[$followkey]['user_image'] = $imageUrl;

				}

				$final = json_encode($result);

				return '{"status": "true", "result":'.$final.'}';

			}else{

				return '{"status":"false","message":"No followers found"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}

	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the limit
	 * @param string the offset
	 * @return string the json data
	 * @soap
	 */

	public function Followingdetails($api_username, $api_password, $user_id, $limit = 20, $offset = 0){

		if ($this->authenticateAPI($api_username, $api_password)){
		$userId = $user_id;
		if (isset($userId) && $userId != ""){

			$criteria = new CDbCriteria;
			$criteria->limit = $limit;
			$criteria->offset = $offset;
			$criteria->addCondition("userId = '$userId'");
			$FollowersModel = Followers::model()->findAll($criteria);

			foreach ($FollowersModel as $followkey => $Followers) {
				$follow_userId = $Followers->follow_userId;
				$userModel = Users::model()->findAllByAttributes(array('userId'=>$follow_userId));
				$result[$followkey]['user_id'] = $userModel['0']['userId'];
				$result[$followkey]['user_name'] = $userModel['0']['username'];
				$result[$followkey]['full_name'] = $userModel['0']['name'];

				$result[$followkey]['status'] = "unfollow";

				if(isset($userModel['0']['userImage'])){
					$imageUrl = Yii::app()->createAbsoluteUrl('/user/resized/150/'.$userModel['0']['userImage']);
				}else{
					$imageUrl = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
				}

				$result[$followkey]['user_image'] = $imageUrl;

			}

			$final = json_encode($result);

			return '{"status": "true", "result":'.$final.'}';

		}else{

			return '{"status":"false","message":"No followings found"}';
		}
	}else{
		return '{"status":"false", "message":"Unauthorized Access to the API"}';
	}

	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the otp
	 * @param string the mob_no
	 * @return string the json data
	 * @soap
	 */

	public function Confirmotp($api_username, $api_password, $user_id, $otp, $mob_no){
		$userId = $user_id;
		if ($this->authenticateAPI($api_username, $api_password)){
		$user = Users::model()->findByPk($userId);
			if(!empty($user) && !empty($otp)){
				if(($otp == $user->mobile_verificationcode) && $user->userId == $user_id){
					$user->phone == $mob_no;
					$user->mobile_status = '1';
					$user->save(false);
					return ' {"status":"true","message":"Your mobile number verified successfully"}';
				}else{
					return '{"status":"false","message":"Sorry, Enter the correct verification code"}';
				}
			}else{
				return '{"status":"false","message":"Sorry, Something went to be wrong"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the follow_id
	 * @return string the json data
	 * @soap
	 */
	public function Followuser($api_username, $api_password, $user_id, $follow_id){

		if ($this->authenticateAPI($api_username, $api_password)){
			$userId = $user_id;
			$follow_user = $follow_id;
		$userdetails = Users::model()->findByPk($userId);
		$fusername = $userdetails->name;
		$followerdetail = Users::model()->findByPk($follow_user);
		$curentusername = $followerdetail->name;
		$emailTo = $followerdetail->email;
			if(!empty($follow_user)){
				$getfollowmodel = Followers::model()->findByAttributes(array('userId'=>$userId,'follow_userId'=>$follow_user));
				if(empty($getfollowmodel)){
					$model = new Followers();
					$model->userId = $userId;
					$model->follow_userId = $follow_user;
					$model->followedOn = date ("Y-m-d H:i:s");
					$model->save();

					$notifyMessage = 'start Following you';
					Myclass::addLogs("follow", $userId, $follow_user, $model->id, 0, $notifyMessage);

				$siteSettings = Sitesettings::model()->find();


				if($siteSettings->smtpEnable == 1) {
				$mail = new YiiMailer();
				//$mail->IsSMTP();
				                     // Set mailer to use SMTP
				$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
				$mail->Host = $siteSettings->smtpHost;  // Specify main and backup server
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = $siteSettings->smtpEmail;                            // SMTP username
				$mail->Password = $siteSettings->smtpPassword;                           // SMTP password
				if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
				$mail->Port = $siteSettings->smtpPort;


				$mail->setView('getfollow');
				$mail->setData(array('followername' => $fusername, 'username' => $curentusername,
						'siteSettings' => $siteSettings));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($emailTo);
				$mail->setSubject($fusername.' '.Yii::t('app','Now following you'));
				$mail->send();
				}

					$userid = $follow_user;
					$criteria = new CDbCriteria;
					$criteria->addCondition('user_id = "'.$userid.'"');
					$userdevicedet = Userdevices::model()->findAll($criteria);
					$followerdetail = Users::model()->findByPk($userId);
					$curentusername = $followerdetail->name;
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

								$text = 'start Following you';
								$msg = Yii::t('app',$text);
								$messages =$curentusername.' '.$msg;
								Myclass::pushnot($deviceToken,$messages,$badge);
							}
						}
					}

					return "{'status':'true','result':'Successfully Followed'}";
				}else{
					return "{'status':'false','message':'User Already Following'}";
				}
			}else{
				return "{'status':'false','message':'User Not Available for Following'}";
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the follow_id
	 * @return string the json data
	 * @soap
	 */
	public function Unfollowuser($api_username, $api_password, $user_id, $follow_id){

		if ($this->authenticateAPI($api_username, $api_password)){
			$userId = $user_id;
			$follow_user = $follow_id;
			if(!empty($follow_user)){
				$getfollowmodel = Followers::model()->findByAttributes(array('userId'=>$userId,'follow_userId'=>$follow_user));
				if(!empty($getfollowmodel)){
					$followId = $getfollowmodel->id;
					Followers::model()->deleteAllByAttributes(array('userId'=>$userId,'follow_userId'=>$follow_user));

					$logCriteria = new CDbCriteria();
					$logCriteria->addCondition("type LIKE 'follow'");
					$logCriteria->addCondition("sourceId = $followId");
					$logsModel = Logs::model()->find($logCriteria);
					$logsModel->delete();

					return "{'status':'true','result':'Successfully Unfollowed'}";
				}else{
					return "{'status':'false','message':'User Already Not Following'}";
				}
			}else{
				return "{'status':'false','message':'User Not Available for Following'}";
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the item_id
	 * @return string the json data
	 * @soap
	 */

	public function Checkpromotion($api_username, $api_password, $item_id){
    	if ($this->authenticateAPI($api_username, $api_password)){

    	$productModel = Products::model()->findByPk($item_id);
    	//print_r($product);
    	//$promotion_details = json_encode($product);

		//echo '{"status": " true", "result":'.$promotion_details.'}';
		$productId = $productModel->productId;
		$product_criteria = new CDbCriteria;
		$product_criteria->addCondition("productId = $productId");
		$product_criteria->order = 'id DESC';
		$promot_detail = Promotiontransaction::model()->find($product_criteria);
		if(!empty($promot_detail)){
		$promotions['id'] = $promot_detail->id;
		$promotions['promotion_name'] = $promot_detail->promotionName;
		$promotions['paid_amount'] = $promot_detail->promotionPrice;
		$currency = '';
		$currency = explode('-',$productModel->currency);
		$promotions['currency_symbol'] = $currency[0];
		$promotions['currency_code'] = $currency[1];
		$start_date = date("M d Y",$promot_detail->createdDate);
		$end_date = date("M d Y",strtotime("+".$promot_detail->promotionTime."  days" , $promot_detail->createdDate));
		$promotions['upto'] = strtotime($start_date).' - '.strtotime($end_date);
		$promotions['transaction_id'] = $promot_detail->tranxId;
		$promotions['status'] = $promot_detail->status;
		$promotions['item_id'] = $productModel->productId;
		$promotions['item_name'] = $productModel->name;
		$promotions['item_image'] = $productModel->photos[0]->name;

		if(isset($productModel->approvedStatus) && $productModel->approvedStatus == "1")
		{
			$promotions['item_approve'] = "1";
		}
		else
		{
			$promotions['item_approve'] = "0";
		}

			/*foreach ($productModel->photos as $photo){
				$photoName = $photo->name;
				$promotions['item_image'] = $photoName;
				//break;
			}*/
			$promotion_details = json_encode($promotions);
				return '{"status": "true", "result":'.$promotion_details.'}';
			}else{
				return '{"status":"false", "message":"Item Not Found for Promotions."}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
    }

	/**
	   * @param string the api_username
	   * @param string the api_password
       * @return string the json data
 	   * @soap
 	   */

	public function helppage($api_username, $api_password){
		if ($this->authenticateAPI($api_username, $api_password)){
		//$Helppages = Helppages::model()->findall();
		
		$criteria = new CDbCriteria;
		$criteria->addCondition('id != 2');
		$Helppages = Helppages::model()->findAll($criteria);
	//	print_r($Helppages);die;
		foreach ($Helppages as $key => $Helppage) {
			$pagetitle[$key]['page_name'] = $Helppage->page;
			$pagetitle[$key]['page_content'] = $Helppage->pageContent;
		}
		$final = json_encode($pagetitle);
		return  '{"status":"true","result":'.$final.'}';
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	 /**
	    * @param string the api_username
	    * @param string the api_password
 	    * @param string the user_id
       * @param string the mob_no
       * @param string the country_code
       * @return string the json data
 	    * @soap
 	    */
public function Getotp($api_username, $api_password, $user_id, $mob_no,$country_code){
		$userId = $user_id;
		if($user_id==0 && $api_username!="" && $api_password!="" && $mob_no!="" && $country_code!="")
		{
			$pass= rand(100000, 999999);
			// twilio mobile verification code
					Yii::import('application.vendors.*',true);
					spl_autoload_unregister(array('YiiBase','autoload'));
					require_once(Yii::app()->basePath."/vendors/twilio-php/Services/Twilio.php");
					spl_autoload_register(array('YiiBase', 'autoload'));

					$setting_id = 1;
					$checkCondition= new CDbCriteria;
					$checkCondition->addCondition('id = "'.$setting_id.'"');
					$sitedetails = Sitesettings::model()->find($checkCondition);
					$account_sid = $sitedetails->account_sid;
    				$auth_token = $sitedetails->auth_token;
    				$from_no=$sitedetails->sms_number;


    				$client = new Services_Twilio($account_sid, $auth_token);

    				try {
    						$message = $client->account->messages->create(array(
        					'To' => '+'.$country_code.$mob_no,

            				'From' => $from_no,
            				'Body' => 'Welcome to '.$sitedetails->sitename.' site.your mobile verification code is '.$pass,
            				'SmsUrl'=> 'https://demo.twilio.com/welcome/sms/reply/',
    						));

    						return '{"status":"true","message":"Mobile verification code sent successfully","otp":"'.$pass.'"}';
						} catch (Services_Twilio_RestException $e) {
   								$a=$e->getMessage();
 							return '{"status":"false","message":"'.$a.'"}';

						}

				// twilio mobile verification code end

		}
		else{
			if ($this->authenticateAPI($api_username, $api_password)){
				$pass= rand(100000, 999999);

				$user = Users::model()->findByPk($userId);
				if(!empty($user) && !empty($mob_no)){
					if($user->phone == $mob_no && $user->mobile_status == 1){
						return ' {"status":"false","message":"Mobile Number already Verified"}';
					}else{
						$user->mobile_verificationcode = $pass;
						//$user->phone = $mob_no;
						$user->save(false);
							// twilio mobile verification code
						Yii::import('application.vendors.*',true);
						spl_autoload_unregister(array('YiiBase','autoload'));
						require_once(Yii::app()->basePath."/vendors/twilio-php/Services/Twilio.php");
						spl_autoload_register(array('YiiBase', 'autoload'));

						$setting_id = 1;
						$checkCondition= new CDbCriteria;
						$checkCondition->addCondition('id = "'.$setting_id.'"');
						$sitedetails = Sitesettings::model()->find($checkCondition);
						$account_sid = $sitedetails->account_sid;
	    				$auth_token = $sitedetails->auth_token;
	    				$from_no=$sitedetails->sms_number;


	    				$client = new Services_Twilio($account_sid, $auth_token);

	    				try {
    						$message = $client->account->messages->create(array(
        					'To' => '+'.$country_code.$mob_no,

            				'From' => $from_no,
            				'Body' => 'Welcome to '.$sitedetails->sitename.' site.your mobile verification code is '.$pass,
            				'SmsUrl'=> 'https://demo.twilio.com/welcome/sms/reply/',
    						));

    						return '{"status":"true","message":"Mobile verification code sent successfully","otp":"'.$pass.'"}';
						} catch (Services_Twilio_RestException $e) {
   								$a=$e->getMessage();
 							return '{"status":"false","message":"'.$a.'"}';

						}

					// twilio mobile verification code end
					}
				}else{
					return ' {"status":"false","message":"Sorry, Something went to be wrong"}';
				}
			}else{
				return '{"status":"false", "message":"Unauthorized Access to the API"}';
			}
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @return string the json data
	 * @soap
	 */
	public function braintreeClientToken($api_username, $api_password){
		if ($this->authenticateAPI($api_username, $api_password)){
			Yii::setPathOfAlias('Braintree',Yii::getPathOfAlias('application.vendors.Braintree.Braintree'));

			$siteSettings = Sitesettings::model()->find();
			$brainTreeSettings = json_decode($siteSettings->braintree_settings, true);

			$paymenttype = "sandbox";
			if($brainTreeSettings['brainTreeType'] == 1){
				$paymenttype = "production";
			}
			$merchantid = $brainTreeSettings['brainTreeMerchantId'];
			$publickey = $brainTreeSettings['brainTreePublicKey'];
			$privatekey = $brainTreeSettings['brainTreePrivateKey'];

			Braintree\Configuration::environment($paymenttype);
			Braintree\Configuration::merchantId($merchantid);
			Braintree\Configuration::publicKey($publickey);
			Braintree\Configuration::privateKey($privatekey);
			$clientToken = Braintree\ClientToken::generate();
			if($clientToken && $clientToken!="")
			{
				return '{"status":"true","token":"'.$clientToken.'"}';
			}
			else
			{
				return '{"status":"false","message":"Token cannot be created now, Sorry!"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	/**
	 * @param string the api_username
	 * @param string the api_password
	 * @param string the user_id
	 * @param string the item_id
	 * @param string the promotion_id
	 * @param string the currency_code
	 * @param string the pay_nonce
	 * @return string the json data
	 * @soap
	 */
	public function processingPayment($api_username, $api_password, $user_id, $item_id, $promotion_id, $currency_code, $pay_nonce){
		if ($this->authenticateAPI($api_username, $api_password)){
			$siteSettings = Sitesettings::model()->find();
			if($promotion_id == 0){
				$promotionName = "urgent";
				$promotionPrice = $siteSettings->urgentPrice;
				$promotionTime = 0;
			}else{
				$promotionDetails = Promotions::model()->findByPk($promotion_id);
				$promotionName = "adds";
				$promotionPrice = $promotionDetails->price;
				$promotionTime = $promotionDetails->days;
			}

			Yii::setPathOfAlias('Braintree',Yii::getPathOfAlias('application.vendors.Braintree.Braintree'));

			$siteSettings = Sitesettings::model()->find();
			$brainTreeSettings = json_decode($siteSettings->braintree_settings, true);

			$paymenttype = "sandbox";
			if($brainTreeSettings['brainTreeType'] == 1){
				$paymenttype = "production";
			}
			$merchantid = $brainTreeSettings['brainTreeMerchantId'];
			$publickey = $brainTreeSettings['brainTreePublicKey'];
			$privatekey = $brainTreeSettings['brainTreePrivateKey'];

			Braintree\Configuration::environment($paymenttype);
			Braintree\Configuration::merchantId($merchantid);
			Braintree\Configuration::publicKey($publickey);
			Braintree\Configuration::privateKey($privatekey);

			$result = Braintree\Transaction::sale([
					'amount' => $promotionPrice,
					'paymentMethodNonce' => $pay_nonce
			]);
			$result1 = Braintree\Transaction::submitForSettlement($result->transaction->id);

			if ($result->success || !is_null($result->transaction->id) && $result1->success == '1') {
				$transaction = $result->transaction->id;
				$itemId = $item_id;
				$productModel = Products::model()->findByPk($itemId);

				$currencyCode = $currency_code;
				$createdDate = time();

				$promotionTranxModel = new Promotiontransaction();
				$promotionTranxModel->promotionName = $promotionName;
				$promotionTranxModel->promotionPrice = $promotionPrice;
				$promotionTranxModel->promotionTime = $promotionTime;
				$promotionTranxModel->productId = $itemId;
				$promotionTranxModel->status = 'Live';
				$promotionTranxModel->userId = $user_id;
				$promotionTranxModel->tranxId = $transaction;
				if($siteSettings->product_autoapprove==1)
				{
					$promotionTranxModel->approvedStatus = 1;
					$promotionTranxModel->initial_check = 1;
						$promotionTranxModel->createdDate = $createdDate;

				}
				else
				{
					$promotionTranxModel->approvedStatus = 0;
					$promotionTranxModel->initial_check = 0;
					$promotionTranxModel->createdDate = $createdDate;

				}

				$promotionTranxModel->save(false);
				$promotionTranxId = $promotionTranxModel->id;

				if($promotion_id != 0){
					$adsPromotionDetailsModel = new Adspromotiondetails();
					$adsPromotionDetailsModel->productId = $itemId;
					$adsPromotionDetailsModel->promotionTime = $promotionTime;
					$adsPromotionDetailsModel->promotionTranxId = $promotionTranxId;
					$adsPromotionDetailsModel->createdDate = $createdDate;

					$adsPromotionDetailsModel->save(false);
				}

				if($promotionName == "urgent"){
					$productModel->promotionType = 2;
				}else{
					$productModel->promotionType = 1;
				}
				// $notifyMessage = 'Sent Exchange request to your product';
				// Myclass::addLogs("exchange", $user_id, $senderid, $check->id, $exchangeitem_id, $notifyMessage);

				$productModel->save(false);

			$siteSettings = Sitesettings::model()->find();
			$userModel = Myclass::getUserDetails($productModel->userId);
			$sellerEmail = $userModel->email;
			$sellerName = $userModel->name;
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
			$mail->setView('promotionsuccessintimation');
			$mail->setData(array('siteSettings' => $siteSettings,
					'userModel' => $userModel,'productModel'=>$productModel,'productName'=>$productModel->name, 'sellerName' => $sellerName,
					));
			$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
			$mail->setTo($sellerEmail);
			$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Your product promoted'));
			$mail->send();

				$userid = $productModel->userId;
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

							if($promotionName == "urgent"){

								$text = 'You have promoted your product';
								$msg = Yii::t('app',$text);

								$text1 = 'by';
								$msg1 = Yii::t('app',$text1);


								$messages =  $msg." ".$productModel->name." ".$msg1." ".$currencyCode.$promotionPrice;
							}else{

								$text = 'You have promoted your product';
								$msg = Yii::t('app',$text);

								$text1 = 'by';
								$msg1 = Yii::t('app',$text1);

								$text2 = 'for';
								$msg2 = Yii::t('app',$text2);

								$text3 = 'days';
								$msg3 = Yii::t('app',$text3);


								$messages =  $msg." ".$productModel->name." ".$msg1." ".$currencyCode.$promotionPrice." ".$msg2." ".$promotionTime." ".$msg3;
							}
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
				}

				return '{"status":"true","message":"Your promotion was activated successfully"}';
			}else {
				return '{"status":"false","message":"Sorry, Something went wrong. Please try again later"}';
			}
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}

	 /**
	    * @param string the api_username
	    * @param string the api_password
 	    * @param string the user_id
        * @param string the full_name
        * @param string the user_img
        * @param string the facebook_id
        * @param string the mobile_no
        * @param string the fb_email
        * @param string the fb_firstname
        * @param string the fb_lastname
        * @param string the fb_phone
        * @param string the fb_profileurl
        * @param string the show_mobile_no
        * @param string the mobile_no
        * @return string the json data
 	    * @soap
 	    */

	public function Editprofile($api_username, $api_password, $user_id, $fb_email=NULL,$fb_firstname=NULL,$fb_lastname=NULL,$fb_phone=NULL,$fb_profileurl=NULL,$full_name = NULL, $user_img = NULL, $facebook_id = NULL, $mobile_no = NULL, $show_mobile_no=NULL)
	{
		if ($this->authenticateAPI($api_username, $api_password)){
			$userId = $user_id;
			$user = Users::model()->findByPk($userId);



			if(!empty($user)){
				if(!empty($full_name)){
					$user->name = $full_name;
				}
				if(!empty($facebook_id)){
					$user->facebookId = $facebook_id;
				}
				if(!empty($mobile_no)){
					$user->phone = $mobile_no;
				}
				if(!empty($user_img)){
					$user->userImage = $user_img;
				}
				if(!empty($show_mobile_no))
				{
					if($show_mobile_no == "true")
					{
						$user->phonevisible = "1";
					}
					else if($show_mobile_no == "false")
					{
						$user->phonevisible = "0";
					}
				}


				$socialids = json_decode($user->fbdetails,true);
				if(!empty($user->fbdetails))
				{
					if($socialids['email'] != "")
						$fbdetails['email'] = $socialids['email'];
					else
						$fbdetails['email'] = "";
					if($socialids['firstName'] != "")
						$fbdetails['firstName'] = $socialids['firstName'];
					else
						$fbdetails['firstName'] = "";
					if($socialids['lastName'] != "")
						$fbdetails['lastName'] = $socialids['lastName'];
					else
						$fbdetails['lastName'] = "";
					if($socialids['phone'] != "")
						$fbdetails['phone'] = $socialids['phone'];
					else
						$fbdetails['phone'] = "";
					if($socialids['profileURL'] != "")
						$fbdetails['profileURL'] = $socialids['profileURL'];
					else
						$fbdetails['profileURL'] = "";
				}
				else
				{
					if(!empty($fb_email))
						$fbdetails['email'] =  $fb_email;
					else
						$fbdetails['email'] = "";
					if(!empty($fb_firstname))
						$fbdetails['firstName'] =  $fb_firstname;
					else
						$fbdetails['firstName'] = "";
					if(!empty($fb_lastname))
						$fbdetails['lastName'] =  $fb_lastname;
					else
						$fbdetails['lastName'] = "";
					if(!empty($fb_phone))
						$fbdetails['phone'] =  $fb_phone;
					else
						$fbdetails['phone'] = "";
					if(!empty($fb_profileurl))
						$fbdetails['profileURL'] = $fb_profileurl;
					else
						$fbdetails['profileURL'] = "";
				}

				$user->fbdetails = json_encode($fbdetails);

				if(!empty($facebook_id) || !empty($full_name) || !empty($mobile_no) || !empty($user_img)){
					$user->save(false);
				}
					$user = Users::model()->findByPk($userId);

					$userDetails['user_id'] = $user->userId;
					$userDetails['user_name'] = $user->username;
					$userDetails['full_name'] = $user->name;
					if($user->userImage == "")
						$userDetails['user_img'] = Yii::app()->createAbsoluteUrl('media/user/default/'.Myclass::getDefaultUser());
					else
						$userDetails['user_img'] = Yii::app()->createAbsoluteUrl('user/resized/150/'.$user->userImage);
					$userDetails['email'] = $user->email;
					$userDetails['facebook_id'] = $user->facebookId;
					$userDetails['rating'] = $user->averageRating;
					$userDetails['mobile_no'] = $user->phone;

					if($user->facebookId == ''){
						$userverify['facebook'] = 'false';
					}else{
						$userverify['facebook'] = 'true';
					}

					$userverify['email'] = 'true';

					if($user->mobile_status == '1'){
						$userverify['mob_no'] = 'true';
					}else{
						$userverify['mob_no'] = 'false';
					}
					$userDetails['verification'] = $userverify;

					$final = json_encode($userDetails);
					return  '{"status": "true", "result": '.$final.'}';

			}else{
				return '{"status":"false","message":"Sorry, Something went to be wrong"}';
			}
			}else{
				return '{"status":"false", "message":"Unauthorized Access to the API"}';
			}

	}

	/**
     * @param string the api_username
     * @param string the api_password
	 * @param string the sender_id
     * @param string the source_id
     * @param string the chat_id
     * @param string the created_date
     * @param string the message
     * @param string the offer_price
     * @return string the json data
	 * @soap
	 */

	public function Sendofferreq($api_username, $api_password, $sender_id, $source_id, $chat_id, $created_date, $message, $offer_price){
		//
		$userDetails = Myclass::getUserDetails($sender_id);
		if ($this->authenticateAPI($api_username, $api_password)){
			if(!empty($userDetails)){

				$senderId = $sender_id;
				$name = $userDetails->name;
				$email = $userDetails->email;
				$phone = $userDetails->phone;
				$productId = $source_id;
				$productModel = Products::model()->findByPk($productId);
				$productURL = Yii::app()->createAbsoluteUrl('item/products/view',array(
					'id' => Myclass::safe_b64encode($productModel->productId.'-'.rand(0,999)))).'/'.
					Myclass::productSlug($productModel->name);
				$seller_id = $productModel->userId;
				$sellerDetails = Myclass::getUserDetails($seller_id);
				$receiverId = $seller_id;
				$sellerEmail = $sellerDetails->email;
				$sellerName = $sellerDetails->name;
				$offerRate = $offer_price;
				$siteSettings = Sitesettings::model()->find();
				$timeUpdate = $created_date;

				$chatModel = Chats::model()->findByAttributes(array('chatId'=>$chat_id));
					if ($chatModel->user1 == $senderId){
						$chatModel->lastToRead = $chatModel->user2;
					}else{
						$chatModel->lastToRead = $chatModel->user1;
					}
				$chatModel->lastMessage = $message;
				$chatModel->save();

				$offerMessage['message'] = $message;
				$offerMessage['price'] = $offerRate;
				$offerMessage['currency'] = $productModel->currency;
				$offerMessage = json_encode($offerMessage);

				$messageModel = new Messages();
				$messageModel->message = $offerMessage;
				$messageModel->messageType = "offer";
				$messageModel->senderId = $senderId;
				$messageModel->sourceId = $productId;
				$messageModel->chatId = $chat_id;
				$messageModel->createdDate = $timeUpdate;
				$messageModel->save();

				$notifyMessage = 'sent offer request '.$productModel->currency.$offerRate.' on your product';
				Myclass::addLogs("myoffer", $senderId, $receiverId, 0, $productId, $notifyMessage);



				$mail = new YiiMailer();
				if($siteSettings->smtpEnable == 1) {
					//$mail->IsSMTP();                               // Set mailer to use SMTP
					$mail->Mailer = 'smtp';                         // Set mailer to use SMTP
					$mail->Host = $siteSettings->smtpHost;         // Specify main and backup server
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = $siteSettings->smtpEmail;           // SMTP username
					$mail->Password = $siteSettings->smtpPassword;        // SMTP password
					if($siteSettings->smtpSSL == 1)
							$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
					$mail->Port = $siteSettings->smtpPort;
				}
				$mail->setView('myofferintimation');
				$mail->setData(array('name' => $name, 'email' => $email,'phone' => $phone,
						'offerRate' => $offerRate, 'message'=> $message, 'sellerName' => $sellerName,
					'siteSettings' => $siteSettings,'currency' => $productModel->currency, 'productURL'=>$productURL));
				$mail->setFrom($siteSettings->smtpEmail, $siteSettings->sitename);
				$mail->setTo($sellerEmail);
				$mail->setSubject($siteSettings->sitename.' '.Yii::t('app','Offer Intimation Mail'));
			 	$userid = $sender_id;
				$criteria = new CDbCriteria;
				$criteria->addCondition('user_id = "'.$receiverId.'"');
				$userdevicedet = Userdevices::model()->findAll($criteria);
				$userdata = Users::model()->findByPk($senderId);
				$currentusername = $userdata->name;
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

						$text = 'sent offer request';
						$msg = Yii::t('app',$text);

						$text1 = 'on your product';
						$msg1 = Yii::t('app',$text1);

							$messages = $currentusername." ".$msg." ".$productModel->currency.$offerRate." ".$msg1." ".$productModel->name;
							Myclass::pushnot($deviceToken,$messages,$badge);
						}
					}
				}

				 if ($mail->send()) {
				 	return '{"status":"true","message":"Message sent successfully"}';
				 } else {
				 	return '{"status":"false","message":"Message cannot be send"}';
				 }

			}else{
				return '{"status":"false","message":"Message cannot be send"}';
			}

		} else {
             return '{"status":"false", "message":"Unauthorized Access to the API"}';
        }


	}


	   /**
	    * @param string the api_username
	    * @param string the api_password
 	    * @param string the deviceId
       * @param string the deviceToken
       * @return string the json data
 	    * @soap
 	    */
	   public function pushsignout($api_username, $api_password, $deviceId){
 	       /*public function actionPushsignout(){
	              $deviceId = $_POST['deviceId'];
 	              $deviceToken = $_POST['deviceToken'];
	              $api_username = $_POST['api_username'];
                 $api_password = $_POST['api_password'];*/
           			if ($this->authenticateAPI($api_username, $api_password)){

                        	if(isset($deviceId) && trim($deviceId)!=''){
                               Userdevices::model()->deleteAllByAttributes(array('deviceId' => $deviceId));
                                       return '{"status":"true","result":"Unregistered successfully"}';
                            	 }
 	                            else
                               {
                                      return '{"status":"false","result":"Something went wrong, please try again later"}';
                               }
                  } else {
                       return '{"status":"false", "message":"Unauthorized Access to the API"}';
                  }

	       }


	       /* new changes */

	       /**
	   * @param string the api_username
	   * @param string the api_password
       * @return string the json data
 	   * @soap
 	   */

	public function tos($api_username, $api_password){
		if ($this->authenticateAPI($api_username, $api_password)){
			$id=2;
		$Helppages = Helppages::model()->findByAttributes(array('id'=>$id));
		if(!empty($Helppages))
		{
				$pagetitle= $Helppages->pageContent;
				$final = json_encode($pagetitle);
				return  '{"status":"true","message":'.$final.'}';
		}
		else
		{
				return '{"status":"false", "message":"Sorry, Something went to be wrong"}';
		}
		
		}else{
			return '{"status":"false", "message":"Unauthorized Access to the API"}';
		}
	}


}

