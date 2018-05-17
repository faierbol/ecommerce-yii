
<?php
$columns[] = array('name' => 'orderId','filterHtmlOptions' => array('class' => 'small-input'));
$columns[] =  array(
    'name'=>'userId',
    'type' => 'raw',
    'value'=> '$data->userName','filterHtmlOptions' => array('class'=>'userfilter'),'filter'=>false
);
$columns[] = array('name' => 'sellerId','value'=>'$data->sellerName','filter'=>false);
$columns[] = array('name'=>'orderDate','value' => '$data->createdDate','filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array('model'=>$model, 'attribute'=>'orderDate','options'=> array('dateFormat'=>'dd-mm-yy')), true),'filterHtmlOptions' => array('class' => 'small-input'));
if($status != 'delivered' && $status != 'cancelled' && $status != 'claimed' && $status != 'approved') {
	$columns[] = array('name' => 'totalCost','filter'=>false,'header'=>'Item Cost','value' => '$data->totalCost - $data->totalShipping','filterHtmlOptions' => array('class' => 'small-input'));
	//$columns[] = array('name' => 'discount','value' => '$data->discount','filterHtmlOptions' => array('class' => 'small-input'));
	$columns[] = array('name' => 'totalShipping','value' => '$data->totalShipping','filterHtmlOptions' => array('class' => 'small-input'));

}
if($status == "")
{
	$columns[] = array('name' => 'totalCost','value' => '$data->totalCost','filterHtmlOptions' => array('class' => 'small-input'));
}
if($status == 'approved') {
	$columns[] = array('name' => 'totalCost','value' => '$data->totalCost','filterHtmlOptions' => array('class' => 'small-input'));
	//$columns[] = array('name' => 'discount','value' => '$data->discount','filterHtmlOptions' => array('class' => 'small-input'));
	$columns[] = array('name' => Yii::t('admin','Commission'),'value'=>'$data->commission','filter'=>false,'filterHtmlOptions' => array('class' => 'small-input'));

}
if($status != "")
{
	if($status == "pending" || $status == "shipped")
	{
		$columns[] = array('name' => Yii::t('admin','Buyer paid'),'value'=>'$data->totalAmount','filter'=>false);
	}
	else if($status == 'approved') {
		$columns[] = array('name' => Yii::t('admin','Paid To Seller'),'value'=>'$data->sellerAmount','filter'=>false);
	}
	else if($status == "delivered")
	{
		$columns[] = array('name' => Yii::t('admin','Pay To Seller'),'value'=>'$data->totalAmount','filter'=>false);
	}
	else
	{
		$columns[] = array('name' => Yii::t('admin','Amount Paid'),'value'=>'$data->totalAmount','filter'=>false);
	}
}
if($status == 'delivered') {
	$columns[] = array('name' => Yii::t('admin','Commission'),'value'=>'$data->commission','filter'=>false,'filterHtmlOptions' => array('class' => 'small-input'));
	$columns[] = array('name' => Yii::t('admin','Seller Amount'),'value'=>'$data->sellerAmount','filter'=>false,'filterHtmlOptions' => array('class' => ''));
	$columns[] = array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','Approve'),
                 'template' => '{approve}',
				'buttons'=>array
	(
				'approve' => array
	(
            'label'=>Yii::t('admin','Approve'),
            'url'=> 'Yii::app()->createAbsoluteUrl("buynow/orders/approve",array("id"=>$data->orderId))',
			'options' => array(
                        'class' => "callMobilePayment btn btn-sm btn-success",
						'confirm' => Yii::t('admin','Are you sure you want to proceed ?'),
	),
	),
	),
	);
}
if($status == 'cancelled') {
	//$columns[] = array('name' => 'invoices.paymentTranxid','value'=>'$model->paymentTranxid','filterHtmlOptions' => array('class' => 'small-input'));
	$columns[] = array('name' => Yii::t('admin','Buyer Amount'),'value'=>'$data->totalAmount','filter'=>false,'filterHtmlOptions' => array('class' => ''));
	$columns[] = array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','Refund'),
                 'template' => '{approve}',
				'buttons'=>array
	(
				'approve' => array
	(
            'label'=>Yii::t('admin','Refund'),
            'url'=> 'Yii::app()->createAbsoluteUrl("buynow/orders/cancelapprove",array("id"=>$data->orderId))',
			'options' => array(
                        'class' => "callMobilePayments btn btn-sm btn-success",
						'confirm' => Yii::t('admin','Are you sure you want to proceed ?'),
	),
	),
	),
	);
}
if($status == 'claimed') {
	$columns[] = array('name' => Yii::t('admin','Commission'),'value'=>'$data->commission','filter'=>false,'filterHtmlOptions' => array('class' => 'small-input'));
	$columns[] = array('name' => Yii::t('admin','Seller Amount'),'value'=>'$data->sellerAmount','filter'=>false,'filterHtmlOptions' => array('class' => ''));
	$columns[] = array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','Actions'),
                 'template' => '{approve}{decline}',
				'buttons'=>array
				(
					'approve' => array
					(
			            //'label'=>'<i class="fa fa-check-circle" style="color:#2FDAB8; font-size:20px;"></i>',
			            'label'=>Yii::t('admin','Approve'),
			            'url'=> 'Yii::app()->createAbsoluteUrl("buynow/orders/approve",array("id"=>$data->orderId))',
						'options' => array(
									'class' => "callMobilePayment btn btn-sm btn-success",
			                        //'class' => "callMobilePayment",
			                        'title'=>'Approve',
									'confirm' => Yii::t('admin','Are you sure you want to proceed ?'),
						),
					),
					'decline' => array
					(
			            //'label'=>'<i class="fa fa-times-circle" style="color:red; font-size:20px;"></i>',
			            'label'=>Yii::t('admin','Decline'),
			            'url'=> 'Yii::app()->createAbsoluteUrl("buynow/orders/decline",array("id"=>$data->orderId))',
						'options' => array(
									'class' => "margin_top10 callMobilePayments btn btn-sm btn-danger",
			                        //'class' => "",
			                        'title'=>'Decline',
									'confirm' => Yii::t('admin','Are you sure you want to proceed ?'),
						),
					),
				),
	);
}
$columns[] = array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','View'),
                 'template' => '{vieworder}',
				'buttons'=>array
(
				'vieworder' => array
(
            'label'=>Yii::t('admin','View').' '.Yii::t('admin','Order'),
            'url'=> 'Yii::app()->createAbsoluteUrl("buynow/orders/view",array("id"=>$data->orderId))',
				'options' => array(
                        'class' => "manage btn btn-sm btn-success",
),
),
),
);
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'mobileorders-grid',
	'dataProvider'=>$model->search($status),
	'filter'=>$model,
	'afterAjaxUpdate'=>"function(){jQuery('#".CHtml::activeId($model, 'orderDate')."').datepicker({dateFormat : 'dd-mm-yy'})}",
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
//'htmlOptions' => array('class' => 'table-responsive'),
	'columns'=> $columns,
)); ?>
<div class="payment-form"></div>
<script type='text/javascript'>
jQuery('#mobileorders-grid a.callMobilePayment').live('click',function(e) {
	linkval = $(this).attr("href");
	if(linkval != "" && linkval != undefined)
	{
		$('.callMobilePayment').html(Yii.t('admin','Approve'));
		$('.callMobilePayment').removeAttr("href");
		$(this).html(Yii.t('admin','Please wait...'));
		$(this).attr("href",linkval);
		var fullData = $(this).attr('href').split('id/');
		var orderId = fullData[1];
		var url = fullData[0];
		$.ajax({
			type : 'POST',
			url: url,
		    data : {orderId : orderId},
		    beforeSend : function() {
		    	//$(this).html(Yii.t('admin','Please wait...'));
				//$('.callMobilePayment').html(Yii.t('admin','Please wait...'));
			},
			success : function(responce) {
				var output = responce.trim();
				if (output != 'false') {
					$('.payment-form').html(output);
					document.getElementById('mobile-paypal-form').submit();
				} else {
					$('.callMobilePayment').html(Yii.t('admin','Try again!'));
					$('.callMobilePayment').css({
						"background-color" : "#fd2525"
					});
				}
			},
			failed : function() {
				$('.callMobilePayment').html(Yii.t('admin','Try again!'));
				$('.callMobilePayment').css({
					"background-color" : "#fd2525"
				});
			}
		});
	}
    return false;
});
</script>
