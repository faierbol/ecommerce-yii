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
			<h1 class="page-header"><?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Items'); ?></h1>
		</div>-->
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<!--<div class="panel-heading"><?php echo Yii::t('admin','Approved Items').' '.Yii::t('admin','List'); ?> </div>-->
				<!-- /.panel-heading -->
				<div style="padding:10px;">
				<!--<div id="approve">Approve</div>
				<div id="pending">pending</div>-->
				<!--<div class=""><a href="<?php echo Yii::app()->createAbsoluteUrl("admin/item/pendingItems");?>"><?php echo Yii::t('admin','Pending').' '.Yii::t('admin','Items'); ?></a> </div>-->




					<?php //$form=$this->beginWidget('CActiveForm', array('id'=>'sitesettings1-form')); ?>
						<!--<div class="checkbox checkbox-custom">
							<?php //echo $form->checkBox($sitemodel,'approvedStatus', array('value'=>0, 'uncheckValue'=>1 ,'class'=>'ProductApproval')); ?>
							<?php //echo $form->labelEx($sitemodel,'approvedStatus'); ?>
						</div>-->
					<?php //$this->endWidget(); ?>
				<div class="panel-body">
				<div class="pending">
				<ul class="tab" style="width:310px;">

  					<li><a href="<?php echo Yii::app()->createAbsoluteUrl("admin/item/admin");?>" class="tablinks">Approved Items</a></li>
  					<li class="active2"><a href="javascript:void(0);" class="tablinks"><?php echo Yii::t('admin','Pending Items');?> </a></li>

				</ul>
				</div>		<?php
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
											jQuery(".userinfo").show();
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

				$this->widget('zii.widgets.grid.CGridView', array(
				'id'=>'products-grid',
				'dataProvider'=>$model->search2(),
				'filter'=>$model,
				'afterAjaxUpdate'=>"function(){jQuery('#".CHtml::activeId($model, 'createdDate')."').datepicker({dateFormat : 'dd-mm-yy'})}",
				'itemsCssClass' => 'table table-striped table-bordered table-hover',
				 'htmlOptions' => array('class' => 'table-responsive'),
				'columns'=>array(

				array('name' =>'productId','filterHtmlOptions' => array('class' => 'small-input','id'=>$data->productId)),
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
				/*
				array(
			    'class'=>'CButtonColumn',
				'header' => Yii::t('admin','Type'),
                 'template' => '{promote} {ad} {urgent} {sold}',
				'buttons'=>array
				(
				'promote' => array
				(
            'label'=> Yii::t('admin','PROMOTE'),
           'url'=> 'Yii::app()->createAbsoluteUrl("admin/item/manage",array("id"=>$data->productId))',
            'visible'=>'((($data->promotionType == 3 || $data->promotionType == "") && $data->soldItem == 0 ))',
			'options' => array(
                        'class' => "promotecls manage btn btn-sm btn-primary",
				        'id' => $data->productId,

				),
				//'click'   => $aa,
			//'onclick' => "js:removeCity(this, {$data->productId}); return false;",
				),
					'ad' => array
				(
             'label'=>Yii::t('admin','Ad'),
            // 'url'=>'Yii::app()->createAbsoluteUrl("admin/item/manage",array("status" => 0,"id"=>$data->productId))',
             'visible'=>'(($data->promotionType == 1 && $data->soldItem == 0))',
				'options' => array(
                        'class' => "btn btn-sm btn-success promotion",
						'id' => 'ad',
						'onclick' => 'return false;',

				),
						//'click'   => $disable_promotion,

				),
				'urgent' => array
				(
             'label'=>Yii::t('admin','URGENT'),
            // 'url'=>'Yii::app()->createAbsoluteUrl("admin/item/manage",array("status" => 0,"id"=>$data->productId))',
             'visible'=>'(($data->promotionType == 2 && $data->soldItem == 0))',
				'options' => array(
                        'class' => "btn btn-sm btn-warning promotion",
						'id' => 'urgent',
						'onclick' => 'return false;',

				),
						//'click'   => $disable_promotion,

				),
				'sold' => array
				(
             'label'=>Yii::t('admin','SOLD'),
            // 'url'=>'Yii::app()->createAbsoluteUrl("admin/item/manage",array("status" => 0,"id"=>$data->productId))',
             'visible'=>'(($data->soldItem == 1))',
				'options' => array(
                        'class' => "btn btn-sm btn-danger promotion",
						'id' => 'sold',

				),
						//'click'   => $disable_promotion,

				),
              	),
				),*/
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
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog" style="display:none;">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Choose promotion</h4>
        </div>
        <div class="modal-body">
          <p><input type="radio" class="promo_class" name="chs_promo" value="1" onchange="view_promo_con(this.value)">&nbsp;&nbsp;Advertisement&nbsp;&nbsp;
          <input type="radio" class="promo_class" name="chs_promo" value="2" onchange="view_promo_con(this.value)">&nbsp;&nbsp;Urgent</p>
          <span id="promo_choose_error" style="display:none;color:red;">Choose any one promotion.</span>
          <br>
          <span id="promo_con1" style="display:none">
          <?php $days = Promotions::model()->findAll();?>
        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

			  <select name="Products[promotion_id]" id="promotion_id" class="form-control select-box-down-arrow">
			   <option value="">Choose duration days</option>
			  <?php foreach($days as $pro_day) { ?>
			  	<option value="<?php echo $pro_day['id'];?>"><?php echo $pro_day['days'];?></option>
			  	<?php }?>
			 	</select>
			</div>

<span style="color:red" id="promo_error"></span>
          </span>
          <br>
           <span id="promo_con"></span>
           <input id="Products_myoffer2" type="hidden" name="promotionType" value="3" readonly=""><!-- currenct promotion type-->
           <input type="hidden" name="get_promotion_id" id="get_promotion_id" readonly=""><!-- currenct promotion id-->
           <input id="product_id" type="hidden" name="product_id" value="0" readonly=""><!-- currenct product id-->
        </div>
          <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="save_promo()">Save</button>
      </div>
      </div>

    </div>
  </div>
	<!-- model end -->

<style type="text/css">
	.button-column{
		width: 90px;
	}

</style>



<script type="text/javascript">
jQuery('a.promotecls').on('click',function(e) {//alert("dgdf");
	linkval = $(this).attr("href");//alert(linkval);
	if(linkval != "" && linkval != undefined)
	{
		$(this).html(Yii.t('admin','Please wait...'));
		$(this).attr("href",linkval);
		var fullData = $(this).attr('href').split('manage/');//alert(fullData);
		var orderId = fullData[1];
		//alert(orderId);
		$("#product_id").val(orderId);
		$("#myModal").modal("show");
		return false;
		}});
</script>
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