<?php
/* @var $this BannersController */
/* @var $model Banners */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'banners-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data','onsubmit'=> 'return validatebanner()'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>



	<div class="form-group" id="bannerImage">
	<?php echo $form->labelEx($model,'banner image for web (1920 x 400)',array('value'=>$model->bannerimage)); ?>
	<?php echo $form->hiddenField($model,'bannerimage',array('id'=>'hiddenwebImage','value'=>$model->bannerimage)); ?>
	
	
<?php  $this->widget('CMultiFileUpload',
  array(
       'model'=>$model,
       'attribute' => 'bannerimage',
       'accept'=>'jpg|gif|png|bmp|',
       'denied'=> Yii::t('admin','Only images are allowed'), 
       'max'=>1,       
       'remove'=>'[x]',
       'duplicate'=>Yii::t('admin','Already Selected'),
       'options' => array(
       		/*'onFileSelect' => 'function(e, v, m){ 
				var fsize = e.files[0].size;
				var ftype = e.files[0].type;
			    var file_data = e.prop("files")[0];   
			    var form_data = new FormData();                  
			    form_data.append("file", file_data);

				$.ajax({
					url: yii.urls.base + "/admin/banners/checkimage",
			                dataType: "text",  // what to expect back from the PHP script, if anything
			                cache: false,
			                contentType: false,
			                processData: false,
			                data: form_data,                         
			                type: "post",

			                success: function(response){
			                	if($.trim(response) == "error")
			                	{
					            	$("#bannerimageerr").html("Image size should be 1140 x 325");
											setTimeout(function() {
												$("#bannerimageerr").html("");
											}, 3000);            	
					            	$("#Banners_bannerimage_wrap_list").html("");
					            	$(".MultiFile").removeAttr("disabled");
					            	$("#bannercreatebtn").attr("disabled","true");
				            	}
				            	else
				            	{

				            	}
			                }
				}); 
       		 }',*/
       	),
       )
        );?>
	<?php if(!$model->isNewRecord && !empty($model->bannerimage)):
	echo CHtml::image(Yii::app()->createAbsoluteUrl('media/banners/'.$model->bannerimage),'',array('height' => 100,'width' => 300));
	endif;?>
	<?php echo $form->error($model,'bannerimage'); ?>
	<div id="bannerimageerr" class="errorMessage"></div>
	</div>							

	<div class="form-group" id="appbannerImage">
	<?php echo $form->labelEx($model,'banner image for app (1024 x 500)'); ?>
	<?php echo $form->hiddenField($model,'appbannerimage',array('id'=>'hiddenappImage','value'=>$model->appbannerimage)); ?>
	
	
<?php  $this->widget('CMultiFileUpload',
  array(
       'model'=>$model,
       'attribute' => 'appbannerimage',
       'accept'=>'jpg|gif|png|bmp|',
       'denied'=> Yii::t('admin','Only images are allowed'), 
       'max'=>1,       
       'remove'=>'[x]',
       'duplicate'=>Yii::t('admin','Already Selected'),
       )
        );?>
	<?php if(!$model->isNewRecord && !empty($model->appbannerimage)):
	echo CHtml::image(Yii::app()->createAbsoluteUrl('media/banners/'.$model->appbannerimage),'',array('height' => 150,'width' => 300));
	endif;?>
	<?php echo $form->error($model,'appbannerimage'); ?>
	<div id="appbannerimageerr" class="errorMessage"></div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bannerurl'); ?>
		<?php echo $form->textField($model,'bannerurl',array('size'=>60)); ?>
		<?php echo $form->error($model,'bannerurl'); ?>
		<div id="bannerurlerr" class="errorMessage"></div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('id'=>'bannercreatebtn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript">
	function checkfile()
	{
		/*newfile = $("#Banners_bannerimage").get(0);alert(newfile.files[0].temp_name);
		$(document).on('change','#Banners_bannerimage', function(){
		    var file, img;
		    if ((file = this.files[0])) {
		        img = new Image();
		        img.onload = function () {
		            if(this.width == "1140" && this.height == "325")
		            {
		            	$("#bannerimageerr").html("dfds");
		            	$("#bannercreatebtn").removeAttr("disabled");
		            	//$("#Banners_bannerimage_wrap_list").show();
		            }
		            else
		            {
		            	$("#bannerimageerr").html("Image size should be 1140 x 325");
								setTimeout(function() {
									$("#bannerimageerr").html("");
								}, 3000);            	
		            	$("#Banners_bannerimage_wrap_list").html("");
		            	$(".MultiFile").removeAttr("disabled");
		            	$("#bannercreatebtn").attr("disabled","true");
		            }
		        };
		    }
		});*/


		//var file = document.getElementById("Banners_bannerimage").value;
		var fsize = ($('#Banners_bannerimage')[0].files[0].size/1024)/1024;
		var ftype = $('#Banners_bannerimage')[0].files[0].type;
	    var file_data = $('#Banners_bannerimage').prop('files')[0];   
	    var form_data = new FormData();                  
	    form_data.append('file', file_data);

	    	
		$.ajax({
			url: yii.urls.base + '/admin/banners/checkimage',
	                dataType: 'text',  // what to expect back from the PHP script, if anything
	                cache: false,
	                contentType: false,
	                processData: false,
	                data: form_data,                         
	                type: 'post',

	                success: function(response){
	                	if($.trim(response) == "error")
	                	{
			            	$("#bannerimageerr").html("Image size should be 1140 x 325");
									setTimeout(function() {
										$("#bannerimageerr").html("");
									}, 3000);            	
			            	$("#Banners_bannerimage_wrap_list").html("");
			            	$(".MultiFile").removeAttr("disabled");
			            	$("#bannercreatebtn").attr("disabled","true");
		            	}
		            	else
		            	{

		            	}
	                }
		});                        
	}
</script>