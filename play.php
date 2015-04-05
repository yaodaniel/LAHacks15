<!DOCTYPE html>
<html>
<?php
$id = $_GET['song'];
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <!-- Soundcloud -->
	<script src="http://connect.soundcloud.com/sdk.js"></script>
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r71/three.js"></script>
	<script src="js/Detector.js"></script>
	<script src="js/stats.min.js"></script>
		
	<script>
	var SoundCloudAudioSource = function(player) {
		var self = this;
		var analyser;
		var audioCtx = new (window.AudioContext || window.webkitAudioContext);
		analyser = audioCtx.createAnalyser();
		analyser.fftSize = 256;
		var source = audioCtx.createMediaElementSource(player);
		source.connect(analyser);
		analyser.connect(audioCtx.destination);
		var sampleAudioStream = function() {
			analyser.getByteFrequencyData(self.streamData);
			// calculate an overall volume value
			var total = 0;
			for (var i = 0; i < 80; i++) { // get the volume from the first 80 bins, else it gets too loud with treble
				total += self.streamData[i];
			}
			self.volume = total;
		};
		setInterval(sampleAudioStream, 20);
		// public properties and methods
		this.volume = 0;
		this.streamData = new Uint8Array(128);
		this.playStream = function(streamUrl) {
			// get the input stream from the audio element
			player.addEventListener('ended', function(){
				self.directStream('coasting');
			});
			player.setAttribute('src', streamUrl);
			player.play();
		}
	};
	</script>
	<script>
	var SoundcloudLoader = function(player) {
		var self = this;
        var clientID = '0a25f7c9ec955ced6294e9e5dcbbb532';
		this.player = player;
		SC.initialize({ client_id: clientID });
		var songUrl = "<?php echo $id ?>";
		self.streamUrl = audioTagSrc = songUrl + '?client_id=' + clientID;
		
		this.directStream = function(direction){
		
			this.player.setAttribute('src',this.streamUrl);
			this.player.play();
			if(direction=='toggle'){
				this.player.play();
			} else {
				this.player.pause();
			}
		}
	}
	</script>
    <script src="./js/animations.js"></script>
	<script>
    // State globals
    var cubes = [];

    // Auxiliary functions
    function sum(numbers)
    {
        var sum = 0;
        
        for (var number in numbers)
        {
            sum += number;
        }
        
        return sum;
    }

    function average(numbers)
    {
        return sum(numbers) / numbers.length;
    }

    //
    // mapBounds()
    // Maps value from a scheme of 0 to limit1-1 to 0 to limit2-1
    //
    function mapBounds(value, limit1, limit2)
    {
        return Math.floor(value / limit1 * limit2);
    }

	window.onload = function() {
        //cubeAnimation();
	    //fireworks();
	    blackHole();
	}
	</script>
</head>
<nav class="navbar navbar-default" style="background:rgba(255,255,255,0.7)">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/index.html">Home</a>
			<a class="navbar-brand" href="">Sign in</a>
			<a class="navbar-brand" href="">Register</a>
			<a class="navbar-brand" href="">Share</a>
		</div>
	</div>
</nav>
<body>
	<audio id="player" controls="" autoplay="" preload autobuffer style="width:100%"></audio>
	<canvas id="canvas" width="100%" height="0%">
	
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<div style="height:350px; clear:both;"></div>           
</body>
<div id="footer" style="height:50px; width:100%; background-color: rgba(0,0,0,0.7); position:fixed; bottom:0">
	<div class="container">
		<div class="col-xs-8 col-xs-offset-3" id="form">
		<p class="muted credit" style="color:#337ab7; padding-top:5px">Website made and designed by Daniel Yao, Ian Cordero, & Paul Grad at LAHacks15</p>
		</div>
	</div>
</div>
</html>
