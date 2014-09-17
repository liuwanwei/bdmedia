<?php /* @var $this Controller */ 

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="language" content="en" />
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
	<meta content="yes" name="apple-mobile-web-app-capable" />
	<meta content="black" name="apple-mobile-web-app-status-bar-style" />
	<meta content="telephone=no" name="format-detection" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.11.1.min.js"></script>
	<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/font-awesome-4.1.0/css/font-awesome.min.css"> 
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.lazyload.min.js"></script>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<style type="text/css">
		body,section,header,div,video
		{
			margin: 0px;
			padding: 0px;
		}
		video
		{
			width: 320px;
			height: 320px;
			display: none;
		}
		body
		{
			background: rgb(242,242,242);
			-webkit-overflow-scrolling: touch;
		}
		section
		{
			margin-bottom: 15px;
			padding: 0px 0px 5px 0px;
			background: #FFFFFF;
			border-bottom: rgba(181,181,181);
		}
		section header
		{
			padding: 5px;
			line-height: 25px;
			font-size: 1em;
			color: #000;
			display: none;
		}
		section footer
		{
			margin:0px 0px;
			padding: 0px 5px;
			font-size: 0.8em;
			color: #333;
			line-height: 20px;
			min-height: 40px;
		}
		section .item-video
		{
			display: block;
			position: relative;
		}
		section .item-video a
		{
			position: absolute;
			font-size: 4em;
			color: #FFFFFF;
			top: 40%;
			left: 45%;
			text-decoration: none;
		}
		section .item-video a:-webkit-any-link:active 
		{
			color: rgba(242,242,242,1);/*-webkit-activelink;*/

		}

		.load-container{
			position: absolute;
			z-index: 1000;
			height: 2em;
			width: 4em;
			top: 40%;
			left: 45%;
			display: none;
		}
		.load1 .loader,
		.load1 .loader:before,
		.load1 .loader:after {
		  background: #FFF;
		  -webkit-animation: load1 1s infinite ease-in-out;
		  animation: load1 1s infinite ease-in-out;
		  width: 0.8em;
		  height: 2em;
		}
		.load1 .loader:before,
		.load1 .loader:after {
		  position: absolute;
		  top: 0;
		  content: '';
		}
		.load1 .loader:before {
		  left: -1.5em;
		  -webkit-animation-delay: -0.32s;
		  animation-delay: -0.32s;
		}
		.load1 .loader {
		  text-indent: -9999em;
		  margin: 2em auto;
		  position: relative;
		  font-size: 11px;
		  -webkit-animation-delay: -0.16s;
		  animation-delay: -0.16s;
		}
		.load1 .loader:after {
		  left: 1.5em;
		}
		@-webkit-keyframes load1 {
		  0%,
		  80%,
		  100% {
		    box-shadow: 0 0 #FFF;
		    height: 1em;
		  }
		  40% {
		    box-shadow: 0 -1em #ffffff;
		    height: 2em;
		  }
		}
		@keyframes load1 {
		  0%,
		  80%,
		  100% {
		    box-shadow: 0 0 #FFF;
		    height: 1em;
		  }
		  40% {
		    box-shadow: 0 -1em #ffffff;
		    height: 2em;
		  }
		}


	</style>
</head>

<body>
	<?php echo $content; ?>
</body>
</html>
