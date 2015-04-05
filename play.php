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

    //function boundsMap(value, )
    //{
    //
    //}

	window.onload = function() {
        var player = document.getElementById('player');
        var canvasElement = document.getElementById('canvas');
        var context = canvasElement.getContext("2d");
        var loader = new SoundcloudLoader(player);
        var audioSource = new SoundCloudAudioSource(player);
        
        // Assign choice
        var choice = 0;
        
        // Scene set-up
        
        // Configuration variables
        var defaultCameraPosition = { x: 0, y: 0, z: 10 };
        var maxDisplacement = { width: 30, height: 0, length: 0 };
        var displacementScalar = 0.02;
        var lightStartPosition = { x: 0, y: 0, z: 20 };
        var numberMovingLights = 3;
        var lightDirections = [];
        var lightSpeeds = [];
        var defaultLightSpeed = 5;
        
        var startingCubePosition = { x: 0, y: 0, z: 0 };
        
        // Initialize the scene
        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        var renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);
        camera.position.z = defaultCameraPosition.z;
        for (var i = 0; i < numberMovingLights; i++)
        {
            lightDirections.push((Math.random() < 0.5) ? 1 : -1);
            lightSpeeds.push(defaultLightSpeed + Math.random() * 2);
        }
        
        // Set up lighting
        var directionalLight = new THREE.DirectionalLight(0xFFFFFF, 0.5);
        directionalLight.position.set(0, 1, 0);
        var ambientLight = new THREE.AmbientLight(0x808080);
        var movingLights = [];
        for (var i = 0; i < numberMovingLights; i++)
        {
            var movingLight = new THREE.DirectionalLight(0xb9d1f1, 2.5);
            movingLight.position.set(lightStartPosition.x + (-maxDisplacement.width + maxDisplacement.width * Math.random() * 2), lightStartPosition.y, lightStartPosition.z);
            scene.add(movingLight);
            movingLights.push(movingLight);
        }
        
        scene.add(directionalLight);
        scene.add(ambientLight);
        
        for (var i = 0; i < audioSource.streamData.length; i++)
        {
            // Initialize scenes
            switch (choice)
            {
                case 0: // cube geometry
                    // Set up cubes
                    var geometry = new THREE.BoxGeometry(1, 1, 1);
                    var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
                    var cube = new THREE.Mesh(geometry, material);
                    
                    cube.position.x = startingCubePosition.x + (-maxDisplacement.width + Math.random() * 2 * maxDisplacement.width);
                    cube.position.y = startingCubePosition.y;
                    cube.position.z = startingCubePosition.z;
                    
                    // Add cube to the scene
                    scene.add(cube);
                    
                    // Keep reference to cube
                    cubes.push(cube);
                    break;
                case 1: // snowflakes
                    var geometry = new THREE.Geometry();
                    var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
                    
                    var sprites = [
                        THREE.ImageUtils.loadTexture("./assets/snowflake1.png"),
                        THREE.ImageUtils.loadTexture("./assets/snowflake2.png"),
                        THREE.ImageUtils.loadTexture("./assets/snowflake3.png"),
                        THREE.ImageUtils.loadTexture("./assets/snowflake4.png"),
                        THREE.ImageUtils.loadTexture("./assets/snowflake5.png"),
                    ];
                    
                    var parameters = [
                        [ [1.0, 0.2, 0.5], sprites[0], 20 ],
                        [ [0.95, 0.1, 0.5], sprites[1], 15 ],
                        [ [0.90, 0.05, 0.5], sprites[2], 10 ],
                        [ [0.85, 0.05, 0.5], sprites[3], 8 ],
                        [ [0.8, 0, 0.5], sprites[4], 5 ]
                    ];
                    
                    var materials = [];
                    
                    for (var j = 0; j < 5; j++)
                    {
                        var color = parameters[j][0];
                        var sprite = parameters[j][1];
                        var size = parameters[j][2];
                        
                        materials.push(new THREE.PointCloudMaterial({ size: size, map: sprite, blending: THREE.AdditiveBlending, depthTest: false, transparent: true }));
                        materials[j].color.setHSL(color[0], color[1], color[2]);
                        
                        var particles = new THREE.PointCloud(geometry, materials[i]);
                        
                        particles.rotation.x = Math.random() * 6;
                        particles.rotation.y = Math.random() * 6;
                        particles.rotation.z = Math.random() * 6;
                        
                        scene.add(particles);
                    }
                    break;
                default:
                    console.log("Impossible to reach here");
                    break;
            }
        }
        
        var draw = function() {
            // Move the moving lights
            for (var i = 0; i < numberMovingLights; i++)
            {
                var direction = lightDirections[i];
                var speed = lightSpeeds[i];
                var light = movingLights[i];
                
                light.position.x += direction * speed;
                if (light.position.x < -maxDisplacement.width || light.position.x > maxDisplacement.width)
                {
                    direction *= -1;
                }
            }
            
            // you can then access all the frequency and volume data
            // and use it to draw whatever you like on your canvas
            for (var i = 0; i < audioSource.streamData.length; i++)
            {
                // Render next frames
                switch (choice)
                {
                    case 0: // cube geometry
                        var cube = cubes[i % cubes.length % audioSource.streamData.length]; // subject to change
                        
                        // do something with each value. Here's a simple example
                        var val = audioSource.streamData[i];
                        //var red = val;
                        //var green = 255 - val;
                        //var blue = val / 2;
                        //context.fillStyle = 'rgb(' + red + ', ' + green + ', ' + blue + ')';
                        //context.fillRect(i * 2, 0, 2, 200);
                        // use lines and shapes to draw to the canvas is various ways. Use your imagination!
                        
                        // Displace cubes
                        var displacement = val * displacementScalar;
                        cube.position.y = startingCubePosition.y + (Math.random() < 0.5) ? displacement : -displacement; // randomize direction of displacement
                        
                        // Rotate cubes
                        cube.rotation.x += 0.2 * Math.random(); // randomize rotation speed
                        cube.rotation.y += 0.2 * Math.random();
                        
                        // Render scene
                        renderer.render(scene, camera);
                        break;
                    case 1: // snowflakes
                        camera.position.x += 0.05;
                        camera.position.y += 0.05;
                        camera.lookAt(scene.position);
                        
                        for (var j = 0; j < scene.children.length; j++)
                        {
                            var object = scene.children[j];
                            
                            if (object instanceof THREE.PointCloud)
                            {
                                object.rotation.y = 0.1 * + 0.03 * Math.random();
                            }
                        }
                        
                        for (var j = 0; i < materials.length; j++)
                        {
                            color = parameters[j][0];
                            
                            if (Math.random() * 100 < 5)
                            {
                                color += -1 + Math.random() * 2;
                            }
                        }
                        break;
                    default:
                        console.log("Impossible to reach here");
                        break;
                }
            }
            
            // Schedule next animation frame
            requestAnimationFrame(draw);
        };
        
        // Begin stream and drawing runs
        audioSource.playStream(loader.streamUrl);
        draw();
	}
	</script>
</head>
<body>
	<canvas id="canvas" width="1600" height="408" style="height:80vh; width:100vw">
	<audio id="player" controls="" autoplay="" preload autobuffer></audio>
	
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
