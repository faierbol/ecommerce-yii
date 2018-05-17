<?php
/* @var $this BannersController */
/* @var $model Banners */
?>



<div id="page-wrapper" style="min-height: 348px;">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Add').' '.Yii::t('admin','Banner').' '.Yii::t('admin','Video'); ?> </h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo Yii::t('admin','Add').' '.Yii::t('admin','Banner').' '.Yii::t('admin','Video'); ?></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
						
						

<div class="form">
<?php // echo Yii::app()->createUrl();?>
<form enctype="multipart/form-data" id="banners-form" action="<?php echo Yii::app()->createUrl("/admin/banners/bannervideo");?>" method="post" onsubmit="return validatebannervideo()" class="MultiFile-intercepted">
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	
<?php ;
$bannervideo=$model->bannervideo;
$bannervideoposter=$model->bannervideoposter;
$bannerText=$model->bannerText;
$extensionArray=explode(".",$bannervideo);

?>

	<div class="form-group" id="bannerVideo">
		<label value="" for="Sitesettings_banner_video"><?php echo Yii::t('admin','Banner').' '.Yii::t('admin','Video'); ?><span class="required">*</span></label>	
		<input id="hiddenBannerVideo" value="<?php echo $bannervideo?>" name="oldBannervideo" type="hidden">	
	
		<div class="MultiFile-wrap" id="Sitesettings_bannerimage_wrap">
			<input id="file" type="file" name="file" class="MultiFile-applied">
			<div class="MultiFile-list" id="Sitesettings_bannerimage_wrap_list"></div>
		</div>
		<div class="Message" id="bannervideomsg"><?php echo Yii::t('admin','Only allow mp4 type, video size maximum 50 MB');?></div>			
		<div id="bannervideoError" class="errorMessage"></div>
	</div>	

		<?php if($bannervideo!="") {?>
	<video width="220" height="140" controls loop="loop" muted="muted" autoplay="autoplay">
  		<source src="<?php echo Yii::app()->createAbsoluteUrl('/media/banners/videos/'.$bannervideo);?>" type="video/<?php echo $extensionArray[1];?>">
 		Your browser does not support the video tag
	</video>
	
	&nbsp;&nbsp;&nbsp;&nbsp;<a class="delete" title="Delete" href="<?php echo Yii::app()->createAbsoluteUrl('/admin/banners/deletevideo/'.base64_encode($bannervideo));//echo $baseUrl."/admin/banners/deletevideo"; ?>"><i class=" icon-trash">&nbsp;<?php echo Yii::t('admin','Remove video');?></i></a>
	<?php } ?>

	

	<div class="form-group">
	<label for="Users_name" class="required"><?php echo Yii::t('admin','Banner').' '.Yii::t('admin','Text');?><span class="required">*</span></label>	
	
	 <textarea class="form-control" id="bannertxt" rows="4" cols="50" maxlength="120" name="bannertxt"><?php echo $bannerText;?></textarea> 
	<div id="bannerText" class="errorMessage"></div>
		</div>


					
	

	<div class="form-group" id="beforeupload">
		<input class="btn btn-success" id="bannercreatebtn" type="submit" name="submit" value="<?php echo Yii::t('admin','Save');?>" onclick="checkfiles()">	</div>

		<div class="form-group" id="upload" style="display:none;">
		<input class="btn btn-success"  type="button" value="<?php echo Yii::t('admin','Uploading...');?>">	</div>

</form>
</div><!-- form -->


<script type="text/javascript">
function checkfiles()
	{
		
		document.getElementById("beforeupload").style.display = "none"; // to undisplay
   document.getElementById("upload").style.display = ""; // to display
 
		}
	
</script>
		</div>
					</div><!-- /.row (nested) -->
				</div><!-- /.panel-body -->
			</div><!-- /.panel -->
		</div><!-- /.col-lg-12 -->
	</div><!-- /.row -->
</div>