
 <?php
 $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'orders-grid',
	'dataProvider'=>$model->search($status),
	'filter'=>$model,
	'afterAjaxUpdate'=>"function(){jQuery('#".CHtml::activeId($model, 'orderDate')."').datepicker({dateFormat : 'dd-mm-yy'})}",
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
 //'htmlOptions' => array('class' => 'table-responsive'),
	'columns'=>array(
 array('name' => 'orderId','filterHtmlOptions' => array('class' => 'small-input')),
 array(
    'name'=>'userId',
    'type' => 'raw',
    'value'=> '$data->userName','filterHtmlOptions' => array('class'=>'userfilter'),'filter'=>false
 ),
 array('name' => 'sellerId','value'=>'$data->sellerName','filter'=>false),
				
 array('name' => 'totalCost','filterHtmlOptions' => array('class' => 'mid-input')),
			
 array('name' => 'totalShipping','filterHtmlOptions' => array('class' => 'mid-input')),
			
 array('name'=>'orderDate','value' => '$data->createdDate','filter'=>$this->widget(
 		'zii.widgets.jui.CJuiDatePicker', array('model'=>$model, 'attribute'=>'orderDate',
 		'options'=> array('dateFormat'=>'dd-mm-yy')), true),'filterHtmlOptions' => array('class' => 'small-input')),
array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','View'),
                 'template' => '{vieworder}', 
				'buttons'=>array
				(
				'vieworder' => array
				(
            'label'=>Yii::t('admin','View').' '.Yii::t('admin','Order'),
            'url'=> 'Yii::app()->createAbsoluteUrl("admin/orders/view",array("id"=>$data->orderId))',
				'options' => array(
                        'class' => "manage btn btn-sm btn-success",
				),
				),
				),
				),
 ),
 )); ?>
