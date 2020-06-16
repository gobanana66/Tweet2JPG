<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Convert tweet to image</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="html2canvas.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>

<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<form>
<div class="row">
    <input type="text" name="fancy-text" id="tweet_id"/>
    <label for="fancy-text">Tweet ID</label>
 </div>

</form>
<?php
require_once('TwitterAPIExchange.php');
require_once('secret.php');

$url = 'https://api.twitter.com/1.1/statuses/show.json';
$requestMethod = 'GET';

$idnum = $_GET['id'];
$id = '?tweet_mode=extended&id='.$idnum;

$twitter = new TwitterAPIExchange($settings);
$results = $twitter->setGetfield($id)
		    ->buildOauth($url, $requestMethod)
		    ->performRequest();

?>
<div class="inner"><img style="margin-left: 30px;" src="https://gobanana.ca/tweet2jpg/speechbubble.jpg"></div>
<div class="mag-wrap" id="mag-wrap">

	<div class="mag">

   <div class="permalink-header"> <div class="profpic"><img src="https://gobanana.ca/tweet2jpg/profpic_default.jpg"></div>
		<div class="user"><div class="name">Anna Hershenfeld</div>
    <div class="username">@gobanana66</div>
     </div>
    </div>
    <div class="tweet">Hi, my name is Anna!</div>
    <div class="time">9:16 AM - 29 Oct 2010</div>
	</div>
</div>
<form>
<button type="button" tabindex="0">Save Image</button>
</form>
<div id="destination"></div>

<?php 
$data = json_decode($results);
//Get the file
if($data->user) {
	$profpic_url = str_replace("_normal","",$data->user->profile_image_url);
	$content = file_get_contents($profpic_url);
	//Store in the filesystem.
	$fp = fopen("profpic.jpg", "w");
	fwrite($fp, $content);
	fclose($fp);
} 


?>
<script type="text/javascript">
// pass PHP variable declared above to JavaScript variable
let data = <?php echo $results ?>;
if(data.user) {
	let raw_date = moment(data.created_at).subtract(3, 'hours');
	let date = raw_date.format('h:mm A - D MMM YYYY');
	let profpic = data.user.profile_image_url.replace('_normal','');
	$(".name").text(data.user.name);
	$(".username").text("@"+data.user.screen_name);
	$(".tweet").text(data.full_text);
	$(".time").html(date);
	$(".profpic img").attr('src','profpic.jpg?'+new Date().getTime());
}

			
</script>
<script src="https://twemoji.maxcdn.com/2/twemoji.min.js"></script>
    <script>
      twemoji.parse(document.body);


	$('#tweet_id').keyup(
		function(){
			let value = $(this).val();
			window.location.href = "?id=" + value;
		}
	);

$('form').submit(function(e){
	e.preventDefault;
    return false;
});

$(document).ready(function(){
	
	$('button').on('click', function(){
		$('.mag').css('border-radius','0');
		let element = document.getElementById('mag-wrap');
		let destination = document.getElementById('destination');
		
		// With scale: 2 (dpi: 192).
		html2canvas(element, {
		  useCORS: true,
		  dpi: 144,
		  onrendered: function(canvas) {
	           /* let myImage = canvas.toDataURL("image/png");
						$('.lightbox').fadeIn(200);
						$('.image').attr('src', myImage).fadeIn(200);
				*/
				//destination.appendChild(canvas);
				let a = document.createElement('a');
				a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
		        a.download = 'tweet.jpg';
		        a.click();
		        $('.mag').css('border-radius','10px');
	        }
		});

   		
	});
	

	
});	
    </script>
     </body>
</html>
