<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Invoices'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('admin','Invoices'); ?></div>
				<!-- /.panel-heading -->
				<div class="panel-body">

				<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'orders-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,	
	'afterAjaxUpdate'=>"function(){jQuery('#".CHtml::activeId($model, 'invoiceDate')."').datepicker({dateFormat : 'dd-mm-yy'})}",
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
	'columns'=>array(
		'invoiceNo',
		'orderId',
		array('name'=>'invoiceDate','value' => '$data->createdDate','filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array('model'=>$model, 'attribute'=>'invoiceDate','options'=> array('dateFormat'=>'dd-mm-yy')), true)),
		'invoiceStatus',
		'paymentMethod',

				array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','View'),
                'template' => '{viewinvoice}', 
				'buttons'=>array
				(
				'viewinvoice' => array
				(
              'label'=>Yii::t('admin','View').' '.Yii::t('admin','Invoice'),
              'url'=> 'Yii::app()->createAbsoluteUrl("buynow/invoices/view",array("id"=>$data->orderId))',
				'options' => array(
                'class' => "showinvoicepopup btn btn-sm btn-success",
				),
				),
				),
				),
				))); ?>
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
<div id="popup_container">
	<div id="show-exchange-popup"
		style="display: none; height: auto; width: 800px; overflow-y: hidden;margin-bottom:20px;"
		class="popup ly-title update show-invoice-popup"></div>
</div>
<script type='text/javascript'>

jQuery('#orders-grid a.showinvoicepopup').live('click',function() {
	var fullData = $(this).attr('href').split('id/');
	var invoiceId = fullData[1];
	var url = fullData[0];
	$.ajax({
		type : 'POST',
		url: url,
	    data : {invoiceId : invoiceId},
	    success : function(data) {
	    	$('#show-exchange-popup').html(data);
	    	$('#popup_container').show();
	    	$('#popup_container').css({
	    		"opacity" : "1"
	    	});
	    	$('#show-exchange-popup').show();
	    	$('body').css({
	    		"overflow" : "hidden"
	    	});
	    },
	    failure: function(){
			alert('oops something went wrong please try again');
	    }
	});
    return false;
});

</script>
