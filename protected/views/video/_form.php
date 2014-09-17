<?php
/* @var $this VideoController */
/* @var $model Video */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'video-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mark'); ?>
		<?php echo $form->textField($model,'mark',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'mark'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'imgUrl'); ?>
		<?php echo $form->textField($model,'imgUrl',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'imgUrl'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'version'); ?>
		<?php echo $form->textField($model,'version'); ?>
		<?php echo $form->error($model,'version'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('size'=>60,'maxlength'=>500,'rows'=>6,'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<style type="text/css">
	/*video { display:block; margin:0 auto 30px auto; }*/
	button { display:block; width:480px; height:50px; font-size:24px; margin:0 auto; border:1px solid #0085ff; color:#FFF; background:-webkit-linear-gradient(rgba(80, 170, 255, .8), rgba(0, 132, 255, .8)); cursor:pointer; border-radius:5px; margin-bottom:30px; }
	button:hover { background:-webkit-linear-gradient(rgba(0, 132, 255, .8), rgba(80, 170, 255, .8)); border-color:#1988ff; }
</style>
<?php if (!$model->isNewRecord) {?>
<div>
	<video width="320" height="480" src="<?php echo $model->url.'?t='.time(); ?>">仅支持H264格式MP4</video>
	<canvas></canvas>
	<button class="screen">截图</button>
</div>

<script type="text/javascript">
	window.onload = function(){
		var button = document.querySelectorAll('.screen')[0];
		var video = document.querySelectorAll('video')[0];
		var canvas = document.querySelectorAll('canvas')[0];
		var ctx = canvas.getContext('2d');
		var width = 320;
		var height = 320;

		canvas.width = width;
  		canvas.height = height;
		button.onclick = function () {
			ctx.drawImage(video, 0, 0, width, height);  // 将video中的数据绘制到canvas里
			saveImage(canvas, 'screen_' + new Date().getTime() + '.png');  // 存储图片到本地
		};
  	
	}

	function saveImage (canvas, filename) {
	  var image = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');
	  saveFile(image, filename || 'file_' + new Date().getTime() + '.png');
	}

	function saveFile(data, filename) {
	  var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
	  save_link.href = data;
	  save_link.title = filename;
	  save_link.download = filename;

	  $.post("<?php echo $this->createUrl('video/upload'); ?>",{'data' : data},function(result){

	  	$("#Video_imgUrl").val(result.imgUrl);

	  }, 'json');
	   
	  // var event = document.createEvent('MouseEvents');
	  // event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
	  // save_link.dispatchEvent(event);
	}
</script>
<?php }?>