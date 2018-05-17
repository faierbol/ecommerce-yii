<?php
/* @var $this PromotionController */
/* @var $model Promotions */

?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Promotion'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default col-xs-12 no-hor-padding">
				<div class="panel-heading"><?php echo Yii::t('admin','Promotion').' '.Yii::t('admin','List'); ?></div>
				
				<div class="currencypromotion col-xs-12 no-hor-padding" style="">
				<?php $currency = Myclass::getCurrencyList();
				//print_r($selectedcurrency->currency_shortcode); echo count($currency);?>
				<div class="selectedpromotion col-xs-12 no-hor-padding">
				<?php if(!empty($selectedcurrency)) { ?>
				<?php $selected  = $selectedcurrency; ?>
				<?php }else {
					$selected  = '';?>
				
				
				<?php }  ?>
				<label for="Promotions_currency">
				<?php echo Yii::t('admin','Promotion').' '.Yii::t('app','Currency');?>
				</label>
				<select class="form-control col-sm-4 margin_left10 " id="selectedoption" style="width:auto;" name="promotion" onchange="selectpromotion();">
				<?php 
							echo ' <option value="0">Select currency</option>';?>
				<?php foreach($currency as $key => $currency)
					{
						
						//echo $key;
						if($selected == $currency)
					echo '<option value="'.$key.' - '.$currency.'" selected>'.$key.' - '.$currency.'</option>';
						
						else 
							echo '<option value="'.$key.' - '.$currency.'">'.$key.' - '.$currency.'</option>';
				 }?>
				</select>
				<div id="loading_img" class="promotionloader" style="display:none;text-align:center;float:left;">
								<img src="<?php echo Yii::app()->createAbsoluteUrl('images/loader.gif'); ?>" alt="Loading..." style="height: 20px; width: 20px; margin: 7px;">
								</div>
				<div class="promotion-error errorMessage"></div>
				<div class="promotion-success"></div>
								</div>
						<?php
						
						
						?>
                 
				<!-- /.panel-heading -->
				<?php
				/* $flashMessages = Yii::app()->user->getFlashes();
				 if ($flashMessages) {
					echo '<ul class="flashes">';
					foreach($flashMessages as $key => $message) {
					echo '<li><div class="flash-' . $key . '">' . $message . "</div></li>\n";
					}
					echo '</ul>';
					} */
				?>
				<div class="panel-body col-xs-12 hor-padding">


				<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'promotion-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
	'htmlOptions' => array('class' => 'table-responsive'),
	'columns'=>array(
				array('name' =>'name','filterHtmlOptions' => array('class' => 'medium-input')),
		
		 array('name' =>'days','filterHtmlOptions' => array('class' => 'medium-input')),
        array('name' =>'price','filterHtmlOptions' => array('class' => 'medium-input')),
				array(
			'class'=>'CButtonColumn', 
			'header' => Yii::t('admin','Action'),
			'template'=>'{update}{view}{delete}',
			'buttons' => array(
				'delete' => array(
	            	'visible'=>'true',
				),
				'view' => array(
		            'visible'=>'true',
	        	),
				'update' => array(
		            'visible'=>'true',
	        	),
        	),
			'afterDelete'=>'function(link,success,data){ if(success) {$(".userinfo").html(data); setTimeout(function() { $(".userinfo").fadeOut(); },3000); } }',
				),
				),
				)); ?>

				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->