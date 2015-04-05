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
	<!--<script>SC.initialize({ client_id: '0a25f7c9ec955ced6294e9e5dcbbb532'});</script>-->
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
            if (Math.random() < 0.01)
            {
                console.log(self.streamData);
            }
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
			if(direction=='toggle'){
				this.player.play();
			} else {
				this.player.pause();
			}
		}
		this.player.setAttribute('src',this.streamUrl);
		this.player.play();
	}
	</script>
	<script>
	window.onload = function() {
		var player = document.getElementById('player');
		var loader = new SoundcloudLoader(player);
		var audioSource = new SoundCloudAudioSource(player);
		audioSource.playStream(loader.streamUrl);
	}
	</script>
</head>
<body>
	<canvas width="1600" height="408" style="height:80vh; width:100vw">
	<audio id="player" controls="" autoplay="" preload autobuffer></audio>
	
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
             
</body>
</html>
