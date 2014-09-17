<style type="text/css">
.row
{
	display: block;
	width: 1100px;
	min-height: 30px;
	clear: both;
}
.row form
{
	display: block;
}
.row form>div
{
	display: block;
	margin-right: 10px;
	float:left;
}
.item-index
{
	line-height: 20px;
	width: 30px;
}
#dialog
{
	display: none;
	position: fixed;
	top: 20%;
	left: 25%;
	width: 600px;
	height: 400px;
	border:1px solid #8c8c8c;
	border-radius: 10px;
	background: #FFF;

}
#dialog header
{
	position: relative;
	height: 30px;
	line-height: 30px;
}
#dialog header>a
{
	position: absolute;
	right: 10px;
	top: 5px;
	font-size: 1.2em;
	text-decoration: none;
}

#dialog .content
{
	display: -webkit-box;
	-webkit-box-orient:horizontal;
	width: 100%;
	height: 320px;
}
#dialog .content>div
{
	display: block;
	-webkit-box-flex:1;
}
#dialog .content .video
{
	width: 250px;
}
#dialog .content>div>video
{
	display: block;
}
#sidebar
{
	display: none!important;
}
</style>
<div style="clear:both;">
<?php foreach ($fileArr as $key => $value) {?>
	<div class="row">
		<form action="#" method="post" id="form<?php echo $key; ?>">
			<div class="item-index"><?php echo $key+1; ?></div>
			<div><input type="text" id="Video_version" style="width:30px;" placeholder="版本" name="Video_version" value="<?php echo $version;?>"></div>
			<div><input type="text" id="Video_title" name="Video_title" placeholder="标题" value="<?php echo $value['filename'];?>"></div>
			<div><input type="text" id="Video_url" style="width:200px;" placeholder="url" name="Video_url" value="/upload/data/video/<?php echo $value['basename'];?>"></div>
			<div><input type="text" id="Video_imgUrl" style="width:200px;" placeholder="封面" name="Video_imgUrl" value=""></div>
			<div><textarea id="Video_description" name="Video_description" placeholder="描述" rows=3 cols=20 value=""></textarea></div>
			<div><input type="button" value="确认" onclick="submitData('form<?php echo $key; ?>', this)"> <a href="javascript:loadVideo('/upload/data/video/<?php echo $value['basename'];?>', 'form<?php echo $key; ?>');" >封面</a></div>
			<div>
				<img src="" id="preview" width="50px" height="50px">
			</div>
		</form>
	</div>
<?php }?>
<div id="dialog">
	<header><a href="javascript:void(0);" class="fa fa-times"></a></header>
	<div class="content">
		<div class="video">
			<video>仅支持H264格式MP4</video>
		</div>
		<div>
			<canvas></canvas>
		</div>
	</div>
	<footer>
		<button id="btnqt">截图</button><button id="close">关闭</button>
	</footer>
</div>
</div>
<script type="text/javascript">
	
	$("#dialog header>a").click(function(){
		$("#dialog").hide();
	});

	$("#close").click(function(){
		$("#dialog").hide();
	});

	function loadVideo (url, formId) {
		$("#dialog").show();
		var form_item = $('#'+formId);
		var button = $("#btnqt")[0];
		var video = document.querySelectorAll('video')[0];
		var canvas = document.querySelectorAll('canvas')[0];
		var ctx = canvas.getContext('2d');
		var width = 320;
		var height = 320;
		video.src = url;
		canvas.width = width;
  		canvas.height = height;
  		video.width = 320;
		video.height = 320;

  		// $(".video").append(video);
		button.onclick = function () {
			ctx.drawImage(video, 0, 0, width, height);  // 将video中的数据绘制到canvas里
			saveImage(canvas, 'screen_' + new Date().getTime() + '.png', form_item);  // 存储图片到本地
		};
	}

	function saveImage (canvas, filename, form) {
	  var image = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');
	  saveFile(image, filename || 'file_' + new Date().getTime() + '.png', form);
	}

	function saveFile(data, filename, form) {
	  var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
	  save_link.href = data;
	  save_link.title = filename;
	  save_link.download = filename;

	  $.post("<?php echo $this->createUrl('video/upload'); ?>",{'data' : data},function(result){
	  	form.find("#Video_imgUrl").val(result.imgUrl);
	  	form.find("#preview").attr('src',result.imgUrl);

	  	
	  }, 'json');
	}

	function submitData(formId, button)
	{
		var from_item = $('#'+formId);

		$.post('<?php echo $this->createUrl("video/ajaxCreate");?>',{Video:{version:from_item.find("#Video_version").val(), title:from_item.find('#Video_title').val(), url:from_item.find('#Video_url').val(), imgUrl:from_item.find('#Video_imgUrl').val(), description:from_item.find('#Video_description').val()}}, function(result){

			// alert(result.isSuccess);
			if (result.isSuccess) {
				$(button).attr('disabled', 'false');
			}

		}, 'json');
	}

</script>