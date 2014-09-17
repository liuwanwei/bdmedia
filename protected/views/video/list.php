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
    	// $("img.lazy").lazyload({effect : "fadeIn"});
		/*$(".item-video").find('a').click(function(){
			
			var itemPaused = $(".item-video[data-paused=true]");
			itemPaused.each(function(){
				var pauseATag = $(this).find('a');
				var pauseImgTag = $(this).find('img');
				var pauseVideo = $(this).find('video');
				pauseVideo[0].pause();
				// pauseVideo[0].ended = true;
				pauseVideo.removeAttr('controls');
				// pauseVideo.removeAttr('src');
				pauseVideo.attr('src', pauseATag[0].dataset.videoUrl+'?t='+ (new Date()).getTime());
				pauseVideo.hide();

				pauseImgTag.show();
				pauseATag.show();
			});

			var img = $(this).parent().find('img');
			var video = $(this).parent().find('video');
			if ($(this).parent().attr('data-paused') == 'false')
			{
				$(this).parent().attr('data-paused', 'true');
				video.attr('src', this.dataset.videoUrl+'?t='+ (new Date()).getTime());
			}else{
				// video.attr('src', this.dataset.videoUrl+'?t='+ (new Date()).getTime());
			}
			// alert(this.dataset.videoUrl+'?t='+ (new Date()).getTime());
			$(this).hide();
			video[0].load();
			img.hide();
			video.show();
			video[0].play();
			
			video[0].addEventListener("canplay", function(){
				video.attr('controls', 'controls');
			}, false);
			
		});*/

		$(".item-video").find('a').click(function(){
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
				video[0].play();
		});
	
	// 创建记录

	$.post("<?php echo $this->createUrl('video/addUserRecord'); ?>");
	
	})
	
	function onPageFinished(videoId) {
	    $("#"+videoId).find('video').play();
	}
</script>