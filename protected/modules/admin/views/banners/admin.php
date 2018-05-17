<?php
/* @var $this BannersController */
/* @var $model Banners */

?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo Yii::t('admin','Manage').' '.Yii::t('admin','Banners'); ?></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default col-xs-12 no-hor-padding">
				<div class="panel-heading"><?php echo Yii::t('admin','Banners').' '.Yii::t('admin','List'); ?></div>

				<div class="currencypromotion col-xs-12 no-hor-padding" style="">
<?php $models = Sitesettings::model()->findByPk(1);
		$oldBannerVideo = $models->bannervideo;
		?>
	<div class="panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
	<!-- banner status enable content start -->
		<div class="switch-box col-xs-6 col-sm-3 col-md-2 col-lg-10 no-hor-padding">
			<label class="Category-input-box-heading  col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">Banner Status Enable</label>

			<div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
			<?php
			if(isset($sitesettings->bannerstatus) && $sitesettings->bannerstatus == "1")
			{
			?>
			<input id="Sitesettings_bannerstatus" class="cmn-toggle cmn-toggle-round bannerapprove" checked="checked" type="checkbox" name="Sitesettings[bannerstatus]" value="1">
			<label for="Sitesettings_bannerstatus"></label>
			<?php
			}
			else
			{
			?>
			<input id="Sitesettings_bannerstatus" class="cmn-toggle cmn-toggle-round bannerapprove" type="checkbox" name="Sitesettings[bannerstatus]" value="0">
			<label for="Sitesettings_bannerstatus"></label>
			<?php
			}
			?>
			</div>
		</div>
		<!-- banner status enable content end -->


		<!-- video status enable,disable start-->
		<div class="switch-box col-xs-6 col-sm-3 col-md-2 col-lg-2 no-hor-padding" >
			<label class="Category-input-box-heading  col-xs-12 col-sm-6 col-md-12 col-lg-12 no-hor-padding" style="float:right;">Video Status Enable</label>

			<div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">

						<?php
			if(isset($sitesettings->bannervideoStatus) && $sitesettings->bannervideoStatus == "1")
			{
			?>
			<input id="Sitesettings_bannervideoStatus" class="cmn-toggle cmn-toggle-round videobannerapprove" checked="checked" type="checkbox" name="bannervideoStatus" value="1">
			<label for="Sitesettings_bannervideoStatus"></label>
			<?php
			}
			else
			{
			?>
			<input id="Sitesettings_bannervideoStatus" class="cmn-toggle cmn-toggle-round videobannerapprove" type="checkbox" name="bannervideoStatus" value="0">
			<label for="Sitesettings_bannervideoStatus"></label>
			<?php
			}
			?>

						
						</div>
		</div>
		<!-- video status enable,disable end-->




	</div>
	
				<div class="panel-body col-xs-12 hor-padding">



<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'banners-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'itemsCssClass' => 'table table-striped table-bordered table-hover',
	'htmlOptions' => array('class' => 'table-responsive'),
	'columns'=>array(
		'id',
		'bannerimage',
		'appbannerimage',
		'bannerurl',
		array(
			'class'=>'CButtonColumn',
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