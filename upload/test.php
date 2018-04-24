<style>
.fire {
  animation: animation 1s ease-in-out infinite alternate;
  -moz-animation: animation 1s ease-in-out infinite alternate;
  -webkit-animation: animation 1s ease-in-out infinite alternate;
  -o-animation: animation 1s ease-in-out infinite alternate;
}

.burn {
  animation: animation .65s ease-in-out infinite alternate;
  -moz-animation: animation .65s ease-in-out infinite alternate;
  -webkit-animation: animation .65s ease-in-out infinite alternate;
  -o-animation: animation .65s ease-in-out infinite alternate;
}

@keyframes animation
{
0% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #feec85,
  -20px -20px 40px #ffae34,
  20px -40px 50px #ec760c,
  -20px -60px 60px #cd4606,
  0 -80px 70px #973716,
  10px -90px 80px #451b0e;}
100% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #fefcc9,
  -20px -20px 40px #feec85,
  22px -42px 60px #ffae34,
  -22px -58px 50px #ec760c,
  0 -82px 80px #cd4606,
  10px -90px 80px  #973716;}
}

@-moz-keyframes animation
{
0% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #feec85,
  -20px -20px 40px #ffae34,
  20px -40px 50px #ec760c,
  -20px -60px 60px #cd4606,
  0 -80px 70px #973716,
  10px -90px 80px #451b0e;}
100% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #fefcc9,
  -20px -20px 40px #feec85,
  22px -42px 60px #ffae34,
  -22px -58px 50px #ec760c,
  0 -82px 80px #cd4606,
  10px -90px 80px  #973716;}
}

@-webkit-keyframes animation
{
0% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #feec85,
  -20px -20px 40px #ffae34,
  20px -40px 50px #ec760c,
  -20px -60px 60px #cd4606,
  0 -80px 70px #973716,
  10px -90px 80px #451b0e;}
100% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #fefcc9,
  -20px -20px 40px #feec85,
  22px -42px 60px #ffae34,
  -22px -58px 50px #ec760c,
  0 -82px 80px #cd4606,
  10px -90px 80px  #973716;}
}

@-o-keyframes animation
{
0% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #feec85,
  -20px -20px 40px #ffae34,
  20px -40px 50px #ec760c,
  -20px -60px 60px #cd4606,
  0 -80px 70px #973716,
  10px -90px 80px #451b0e;}
100% {text-shadow: 0 0 20px #fefcc9,
  10px -10px 30px #fefcc9,
  -20px -20px 40px #feec85,
  22px -42px 60px #ffae34,
  -22px -58px 50px #ec760c,
  0 -82px 80px #cd4606,
  10px -90px 80px  #973716;}
}
</style>
<?php
require('globals.php');
include('forms/gdpr.php');
echo "[<a href='#' data-toggle='modal' data-target='#data_collection'>Info</a>]";
$h->endpage();
?>
	<script type="text/javascript">
		$(window).on('load',function(){
			$('#data_collection').modal('show');
		});
	</script>