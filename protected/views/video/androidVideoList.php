<?php $this->layout = 'mobile_main';?>
<?php foreach ($model as $key => $value) :?>
	<section>
		<header><?php echo $value->title;?></header>
		<div class="item-video" data-paused="false" id = "<?php echo 'video'.$key;?>">
			<a class="fa fa-play-circle-o" data-video-url="<?php echo $value->url;?>"></a>
			<img src="<?php echo $value->imgUrl;?>"> <!-- class="lazy" data-original -->
		</div>
		<footer>
			<?php echo $value->description;?>
		</footer>
	</section>
<?php endforeach?>

<script type="text/javascript">
	$(function(){
		$(".item-video").find('a').click(function(){

			//window.Bus.playVideo($(this).parent().attr('id'));

			var img = $(this).parent().find('img');
			// var loadContainer = $(this).parent().find('.load-container');
			var pauseVideo = $(".item-video[data-paused=true]");
			pauseVideo.find("video").remove();
			pauseVideo.find("img").show();
			pauseVideo.find("a").show();
			pauseVideo.find('.load-container').remove();

			var video = $("<video>");
				video.attr('width', '320');
				video.attr('height', '320');
				video.attr('preload', 'none');
				video.attr('poster', img.attr('src'));
				video.attr('webkit-playsinline','webkit-playsinline');
				video.attr('src',this.dataset.videoUrl+'?t='+ (new Date()).getTime());
				$(this).parent().attr('data-paused', 'true');

				var loader;
				video[0].addEventListener("loadstart", function(){
					loader = $('<div class="load-container load1"><div class="loader">Loading...</div></div>');
					$(this).parent().append(loader);
					loader.show();
				},false);
				video[0].addEventListener("canplay", function(){
					img.hide();
					loader.hide();
					video.show();
					video.attr('controls', 'controls');
				}, false);

				$(this).parent().append(video);
				$(this).hide();
				var videoTime = setInterval(function(){
					if (video[0].paused) {
						video[0].play();
					}else{
						clear(videoTime);
					}
				},500);
		});
	})
</script>