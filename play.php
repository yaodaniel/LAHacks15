<!DOCTYPE html>
<html>
<?php
$id = $_GET['id'];
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Bootstrap (and jQuery) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <!-- Soundcloud -->
	<script src="http://connect.soundcloud.com/sdk.js"></script>
    <!-- DSP.js -->
    <script src="./js/dsp.js"></script>
    <!-- Three.js -->
    <script src="./js/three.min.js"></script>
	<script>SC.initialize({ client_id: '0a25f7c9ec955ced6294e9e5dcbbb532'});</script>
	<script>
        // "Constants"
        var dontCare = -1;
        
	function searchTrack(query)
    {
        SC.get('/tracks', { q: query }, function name(tracks) {
            console.log(tracks);
               
           // Configuration
           var defaultNumSuggestions = 10;
        
			//var resultLen = tracks.length;
			$("#results").empty();
			$("#results").append("<br>");
            
           for (var i = 0; i < defaultNumSuggestions; i++)
           {
               if (i == 0)
               {
                   $("#results").append("<a href=\"#\" class=\"list-group-item active\"><b>"+tracks[0].title+"</b></a>");
               }
               else
               {
                   $("#results").append("<a href=\"#\" class=\"list-group-item\"><b>"+tracks[i].title+"</b></a>");
               }
           }
			
            // Note: Probably moving this to another page
            SC.stream("/tracks/" + tracks[0].id, function(sound) {
                      // Initialize canvas
                console.log("Initializing canvas");
                      
                // Configuration variables
                var numCubes = 100;
                var defaultCameraPosition = { x: 0, y: 0, z: 10 };
                  var defaultCubePosition = { x: -15, y: 0, z: 0 };
                  var startingWidthBound = 15;
                  var averageVelocity = 0.25;
                  var initialDirection = { x: 1, y: 0 };
                  var defaultCubeSize = 1;
                  
                  // State variables
                  var isPlaying = true;
                  var currentDirection = { x: 1, y: 0 }; // x, y := { -1, 0, 1 }

                // Initialize scene
                var scene = new THREE.Scene();
                var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
                var renderer = new THREE.WebGLRenderer();
                renderer.setSize(window.innerWidth, window.innerHeight);
                document.body.appendChild(renderer.domElement);
                      camera.position.z = defaultCameraPosition.z;

                // Set up the scene
                var cubes = [];
                      
                      // Set up lighting
                      var ambientLight = new THREE.AmbientLight(0x808080);
                      var directionalLight = new THREE.DirectionalLight(0xFFFFFF, 0.5);
                      
                      // Add lights to scene
                      scene.add(directionalLight);
                      scene.add(ambientLight);

                      // Add cubes to scene
                for (var i = 0; i < numCubes; i++)
                {
                      // Set up cube
                    var geometry = new THREE.BoxGeometry(defaultCubeSize, defaultCubeSize, defaultCubeSize);
                    var material = new THREE.MeshLambertMaterial({ color: 0xCC0000 });
                    var cube = new THREE.Mesh(geometry, material);
                      
                      cube.position.x = defaultCubePosition.x + (-startingWidthBound + Math.random() * startingWidthBound * 2);
                      cube.position.y = defaultCubePosition.y;
                      cube.position.z = defaultCubePosition.z;
                      
                      // Add cube to scene
                      scene.add(cube);
                      
                      // Add cube to array for future reference
                    cubes.push(cube);
                }
                      
                      // Configure render behavior
                function render()
                {
                      // Schedule next frame
                    if (isPlaying)
                    {
                      requestAnimationFrame(render);
                      renderer.render(scene, camera);
                    }
                      
                    // Update scene as appropriate using variables that whileplaying() updates
                    
                }
                      
                      // Call first render
                      render();

                // Play sound
                sound.play({
                           // Responsible for resetting animations and performing other clean-up.
                     onfinish: function() {
                        // Reset canvas
                         console.log("Resetting canvas");
                           
                           // Update state to reflect that sound is no longer playing
                           isPlaying = false;
                     }
                });
                     
                      // Configure update function for sound
                      
                      // Updates variable state to reflect the music signal's "spectrum" transform
                sound.whileplaying = function() {
                    // Manipulate canvas
                    console.log("Manipulating canvas");

                    // Reference for SM2 waveformdata
                    //for (var i=0; i<256; i++) {
                    //graphPixels[i].style.top = (gScale+Math.ceil(this.waveformData.left[i]*-gScale))+'px';
                    //}
                }

                // Reference for DSP.js
                //var fft = new FFT(/* bufferSize: */ 2048, /* sampleRate: */ 44100);
                //fft.forward(signal);
                //var spectrum = fft.spectrum;
            });
			SC.stream("/tracks/"+<?php echo $id?>, function(sound) {
			sound.play();});
        });
	}
	</script>
	<title>LivingSound</title>
</head>

<body style="background-image: url(assets/nature.jpg); height:100%;">
<nav class="navbar navbar-default" style="background:rgba(255,255,255,0.95)">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/index.html">Home</a>
		</div>
	</div>
</nav>
<div class="container">
	
	
	<div class="row" style="position: relative; top:30vh">
	<h1 class="text-center">Visualize your favourite song!</h1>
		<form name="query" action="javascript:searchTrack(document.getElementById('songSearch').value)" method="GET" role="search" autocomplete="off">
		  <div class="col-xs-8 col-xs-offset-2" id="form">
			<input type="text" name="q" id="songSearch" style="background-color: rgba(255,255,255,0.8); height:50px;
				border-radius:20px"
				placeholder="Search for a song..." class="form-control">
		  </div>
		  <!--<button type="submit" class="btn btn-default" style="col-xs-2; height:50px">Search</button>-->
		</form>
		<div class="col-xs-8 col-xs-offset-2" style="border-radius:20px">
		<div class="list-group" id="results" style="border-radius:20px"></div></div>
	</div>
	
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</div>
</body>
</html>
