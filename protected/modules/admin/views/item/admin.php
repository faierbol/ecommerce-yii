<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
		<?php
		$siteSettingsModel = Sitesettings::model()->findByAttributes(array('id'=>1));
		if($siteSettingsModel->product_autoapprove=='1'){$checked='checked';}else{$checked='';}?>
		<div class="switch-box" style="float:right;">
									<label for="Products_myoffer">Auto Approval</label>
									<input id="Products_myoffer" class="cmn-toggle cmn-toggle-round autoapprove" type="checkbox"  value="<?php echo $siteSettingsModel->product_autoapprove;?>" <?php echo $checked;?>>
									<label for="Products_myoffer"></label>
							</div>
		<h1 class="page-header" style="padding-top:10px;">
			<?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Items'); ?>
			</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>

	<div class="row">
		<!--<div class="col-lg-12">
			<h1 class="page-header"><?php// echo Yii::t('admin','Manage').' '.Yii::t('admin','Items'); ?></h1>
		</div>-->
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo Yii::t('admin','Items').' '.Yii::t('admin','List'); ?> </div>
				<!-- /.panel-heading -->
				<div class="panel-body">
				<div class="pending">
<ul class="tab" style="width:310px;">
  <li class="active2"><a href="javascript:void(0);" class="tablinks"><?php echo Yii::t('admin','Approved Items');?> </a>
  <li ><a href="<?php echo Yii::app()->createAbsoluteUrl("admin/item/pendingItems");?>" class="tablinks">Pending Items</a></li>

</ul>
</div>

	<?php
					$enableJs = 'js:function(__event)
					{
					    __event.preventDefault(); // disable default action

					    var $this = $(this), // link/buttonborder-box"
					        confirm_message = $this.data("confirm"), // read confirmation message from custom attribute
					        url = $this.attr("href"); // read AJAX URL with parameters from HREF attribute on the link
							//alert(url);

							//if(confirm(\' <?php echo Yii::t("admin","Are you sure you want to disable this item?"); ?>\')){
							if(confirm("Are you sure you want to disable this item?")){

								  $.ajax({
								            url : url ,
								            type : "POST",
								            success : function(data) {
								            	 jQuery("#products-grid").yiiGridView("update");
								            	 jQuery(".userinfo").show();
											jQuery(".userinfo").html(\'<ul class="flashes"><li><div class="flash-success">\'+Yii.t("app","Product Disabled Successfully")+\'</div></li></ul>\');
											setTimeout(function() {
														jQuery(".userinfo").fadeOut();
											}, 3000);

								             }
								        });
								        return false;
							}
					}';

					$disableJs = 'js:function(__event)
					{
					    __event.preventDefault(); // disable default action

					    var $this = $(this), // link/button
					        confirm_message = $this.data("confirm"), // read confirmation message from custom attribute
					        url = $this.attr("href"); // read AJAX URL with parameters from HREF attribute on the link
							//alert(url);

							//if(confirm(\' <?php echo Yii::t("admin","Are you sure you want to enable this item?"); ?>\')){
							if(confirm("Are you sure you want to enable this item?")){

								  $.ajax({
								            url : url ,
								            type : "POST",
								            success : function(data) {
											jQuery("#products-grid").yiiGridView("update");
											jQuery(".userinfo").html(\'<ul class="flashes"><li><div class="flash-success">\'+Yii.t("app","Product Enabled Successfully")+\'</div></li></ul>\');
											setTimeout(function() {
														jQuery(".userinfo").fadeOut();
											}, 3000);
								             }
								        });
								        return false;
							}
					}';

					/*$aa='js:function(){

												return false;}';*/
					$disable_promotion='function disable_promotion(){ alert("Not allow to diable product promotion");return false;}';
?>
				<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'products-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate'=>"function(){jQuery('#".CHtml::activeId($model, 'createdDate')."').datepicker({dateFormat : 'dd-mm-yy'})}",
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
	 'htmlOptions' => array('class' => 'table-responsive'),
	'columns'=>array(

				array('name' =>'productId','filterHtmlOptions' => array('class' => 'small-input')),
				array('name' =>'name','value' => 'html_entity_decode($data->name)','filterHtmlOptions' => array('class' => 'small-input')),
				array('name' =>'price','filterHtmlOptions' => array('class' => 'small-input')),

				//array('name' =>'productCondition','filterHtmlOptions' => array('style' => 'width:50px')),
				array('name'=>'createdDate','value' => '$data->modDate','filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array('model'=>$model, 'attribute'=>'createdDate','options'=> array('dateFormat'=>'dd-mm-yy')), true),'filterHtmlOptions' => array('class' => 'small-input')),
				array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','Manage'),
                 'template' => '{enable} {disable}',
				'buttons'=>array
				(
				'enable' => array
				(
            'label'=> Yii::t('admin','Enable'),
            'url'=> 'Yii::app()->createAbsoluteUrl("admin/item/manage",array("status" => 1,"id"=>$data->productId))',
            'visible'=>'(($data->approvedStatus == 0))',
			'options' => array(
                        'class' => "manage btn btn-sm btn-success",
				        'id' => 'enable',
				),
					'click'   => $disableJs,
				),
				'disable' => array
				(
             'label'=>Yii::t('admin','Disable'),
             'url'=>'Yii::app()->createAbsoluteUrl("admin/item/manage",array("status" => 0,"id"=>$data->productId))',
             'visible'=>'(($data->approvedStatus == 1))',
				'options' => array(
                        'class' => "manage btn btn-sm btn-warning",
						'id' => 'disable',

				),
						'click'   => $enableJs,
				),
              	),
				),
				array(
			'class'=>'CButtonColumn',
			'header' => Yii::t('admin','Action'),
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
</div>

<style type="text/css">
	.button-column{
		width: 90px;
	}
</style>
<style>


.pending ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
.pending ul.tab li {float: left;}

/* Style the links inside the list items */
.pending ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
.pending ul.tab li a:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
.pending ul.tab li a:focus,.active2{
    background-color: #ddd;
}

/* Style the tab content */
.pending .tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}
</style>