function cubeOrSnowAnimation(whichAnimation)
{
    var player = document.getElementById('player');
    var canvasElement = document.getElementById('canvas');
    var context = canvasElement.getContext("2d");
    var loader = new SoundcloudLoader(player);
    var audioSource = new SoundCloudAudioSource(player);

    // Assign choice
    var choice = whichAnimation;

    // Scene set-up

    // Configuration variables
    var numSamples = audioSource.streamData.length;
    var defaultCameraPosition = { x: 0, y: 0, z: 1000 };
    var maxDisplacement = { width: 2500, height: 2500, length: 100 };
    var displacementScalar = 0.1;
    var lightStartPosition = { x: 0, y: 0, z: 200 };
    var numberMovingLights = 3;
    var lightDirections = [];
    var lightSpeeds = [];
    var defaultLightSpeed = 5;

    var startingCubePosition = { x: 0, y: 0, z: 0 };
    var cubeSpacing = maxDisplacement.width * 2 / numSamples;

    // Initialize the scene
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    var renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);
    camera.position.z = defaultCameraPosition.z;
    for (var i = 0; i < numberMovingLights; i++)
    {
        lightDirections.push((Math.random() < 0.05) ? 1 : -1);
        lightSpeeds.push(defaultLightSpeed + Math.random() * 2);
    }

    // Set up lighting
    var directionalLight = new THREE.DirectionalLight(0xFFFFFF, 5);
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
    
    var moreCubes = [];
    
    for (var i = 0; i < 1000; i++)
    {
        var boxSize = Math.random() * 10 + 10;
        var geometry = new THREE.BoxGeometry(boxSize, boxSize, boxSize);
        var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
        
        var cube = new THREE.Mesh(geometry, material);
        
        cube.position.x = startingCubePosition.x + (-maxDisplacement.width + maxDisplacement.width * 2 * Math.random());
        cube.position.y = startingCubePosition.y + (-maxDisplacement.height + maxDisplacement.height * 2 * Math.random());
        cube.position.z = startingCubePosition.z + (-maxDisplacement.length + maxDisplacement.length * 2 * Math.random());
        
        cube.velocity = Math.random() * 30 + 5;
        
        scene.add(cube);
        
        moreCubes.push(cube);
    }

    for (var i = 0; i < numSamples; i++)
    {
        var parameters = [];
        var sprites = [];
        
        // Initialize scenes
        switch (choice)
        {
            case 0: // cube geometry
                // Set up cubes
                var boxSize = Math.random() * 10 + 10;
                
                var geometry = new THREE.BoxGeometry(boxSize, boxSize, boxSize);
                var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
                var cube = new THREE.Mesh(geometry, material);
                
                cube.position.x = startingCubePosition.x + (-maxDisplacement.width + maxDisplacement.width * 2 * Math.random()); //-maxDisplacement.width + cubeSpacing * i;
                cube.position.y = startingCubePosition.y + (-maxDisplacement.height + maxDisplacement.height * 2 * Math.random());
                cube.position.z = startingCubePosition.z + (-maxDisplacement.length + maxDisplacement.length * 2 * Math.random());
                
                cube.rotation.x = Math.random() * 6;
                cube.rotation.y = Math.random() * 6;
                cube.rotation.z = Math.random() * 6;
                
                // Add cube to the scene
                scene.add(cube);
                
                // Keep reference to cube
                cubes.push(cube);
                break;
            case 1: // snowflakes
                var geometry = new THREE.Geometry();
                var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
                
                sprites = [
                               THREE.ImageUtils.loadTexture("./assets/snowflake1.png"),
                               THREE.ImageUtils.loadTexture("./assets/snowflake2.png"),
                               THREE.ImageUtils.loadTexture("./assets/snowflake3.png"),
                               THREE.ImageUtils.loadTexture("./assets/snowflake4.png"),
                               THREE.ImageUtils.loadTexture("./assets/snowflake5.png"),
                        ];
                
                parameters = [
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
                    
                    var particles = new THREE.PointCloud(geometry, materials[j]);
                    
                    particles.rotation.x = Math.random() * 6;
                    particles.rotation.y = Math.random() * 6;
                    particles.rotation.z = Math.random() * 6;
                    
                    scene.add(particles);
                }
                
                renderer.setPixelRatio(window.devicePixelRatio);
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
        
        for (var i = 0; i < moreCubes.length; i++)
        {
            var cube = moreCubes[i];
            
            var speed = cube.velocity;
            cube.position.x += speed;
            if (cube.position.x > maxDisplacement.width)
            {
                cube.position.x = -maxDisplacement.width;
            }
            
            cube.rotation.x += 0.2 + 0.5 * Math.random();
            cube.rotation.y += 0.2 + 0.5 * Math.random();
            
            // Displace cubes
            var val = audioSource.streamData[i % numSamples];
            var displacement = val * displacementScalar;
            
            cube.position.y += displacement;
            
            if (cube.position.y >= maxDisplacement.height)
            {
                cube.position.y = -maxDisplacement.height;
            }
        }
        
        // you can then access all the frequency and volume data
        // and use it to draw whatever you like on your canvas
        for (var i = 0; i < numSamples; i++)
        {
            // Render next frames
            switch (choice)
            {
                case 0: // cube geometry
                    var cube = cubes[i];
                    
                    // do something with each value. Here's a simple example
                    var val = audioSource.streamData[i];
                    //var red = val;
                    //var green = 255 - val;
                    //var blue = val / 2;
                    //context.fillStyle = 'rgb(' + red + ', ' + green + ', ' + blue + ')';
                    //context.fillRect(i * 2, 0, 2, 200);
                    // use lines and shapes to draw to the canvas is various ways. Use your imagination!
                    
                    // Rotate camera
                    camera.rotation.z += 0.00001;
                    
                    // Displace cubes
                    var displacement = val * displacementScalar * 10;
                    cube.position.z = startingCubePosition.z + (Math.random() < 0.5) ? displacement : -displacement; // randomize direction of displacement
                    
                    // Rotate cubes
                    cube.rotation.x += 0.2 * Math.random(); // randomize rotation speed
                    cube.rotation.y += 0.2 * Math.random();
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
                    
                    for (var j = 0; j < materials.length; j++)
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
        
        // Render scene
        renderer.render(scene, camera);
        
        // Schedule next animation frame
        requestAnimationFrame(draw);
    };

    // Begin stream and drawing runs
    audioSource.playStream(loader.streamUrl);
    draw();
}

function cubeAnimation()
{
    cubeOrSnowAnimation(0);
}

function snowAnimation()
{
    cubeOrSnowAnimation(1);
}

function fireworks() {
	if ( ! Detector.webgl ) {
		Detector.addGetWebGLMessage();
	}
	var player = document.getElementById('player');
    var canvasElement = document.getElementById('canvas');
    var context = canvasElement.getContext("2d");
    var loader = new SoundcloudLoader(player);
    var audioSource = new SoundCloudAudioSource(player);
				
	var SCREEN_WIDTH = window.innerWidth,
	SCREEN_HEIGHT = window.innerHeight,
    r = 450,
	mouseX = 0, mouseY = 0,
	windowHalfX = window.innerWidth / 2,
	windowHalfY = window.innerHeight / 2,
	camera, scene, renderer;
	
	init();
	animate();
	
	function init() {
	
    	var container;
    	
    	container = document.createElement( 'div' );
    	document.body.appendChild( container );
    	
    	camera = new THREE.PerspectiveCamera( 80, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 3000 );
    	camera.position.z = 1000;
    	
    	scene = new THREE.Scene();
    	
    	var i, line, vertex1, vertex2, material, p,
    	parameters = [ [ 0.25, 0xff7700 * Math.random(), 1, 2 ], [ 0.5, 0xff9900 * Math.random(), 1, 1 ], [ 0.75, 0xffaa00 * Math.random(), 0.75, 1 ], [ 1, 0xffaa00 * Math.random(), 0.5, 1 ], [ 1.25, 0x000833 * Math.random(), 0.8, 1 ],
    				[ 3.0, 0xaaaaaa * Math.random(), 0.75, 2 ], [ 3.5, 0xffffff * Math.random(), 0.5, 1 ], [ 4.5, 0xffffff * Math.random(), 0.25, 1 ], [ 5.5, 0xffffff * Math.random(), 0.125, 1 ] ],
    	
    	geometry = new THREE.Geometry();
    	
    	for ( i = 0; i < 1500; i ++ ) {
    	
    		var vertex1 = new THREE.Vector3();
    		vertex1.x = Math.random() * 2 - 1;
    		vertex1.y = Math.random() * 2 - 1;
    		vertex1.z = Math.random() * 2 - 1;
    		vertex1.normalize();
    		vertex1.multiplyScalar( r );
    	
    		vertex2 = vertex1.clone();
    		vertex2.multiplyScalar( Math.random() * 0.09 + 1 );
    	
    		geometry.vertices.push( vertex1 );
    		geometry.vertices.push( vertex2 );
    	
    	}
    	
    	for( i = 0; i < parameters.length; ++ i ) {
    	
    		p = parameters[ i ];
    	
    		material = new THREE.LineBasicMaterial( { color: p[ 1 ], opacity: p[ 2 ], linewidth: p[ 3 ] } );
    	
    		line = new THREE.Line( geometry, material, THREE.LinePieces );
    		line.scale.x = line.scale.y = line.scale.z = p[ 0 ];
    		line.originalScale = p[ 0 ];
    		line.rotation.y = Math.random() * Math.PI;
    		line.updateMatrix();
    		scene.add( line );
    	
    	}
    	
    	renderer = new THREE.WebGLRenderer( { antialias: true } );
    	renderer.setPixelRatio( window.devicePixelRatio );
    	renderer.setSize( SCREEN_WIDTH, SCREEN_HEIGHT );
    	container.appendChild( renderer.domElement );
    	
    	document.addEventListener( 'mousemove', onDocumentMouseMove, false );
    	document.addEventListener( 'touchstart', onDocumentTouchStart, false );
    	document.addEventListener( 'touchmove', onDocumentTouchMove, false );
    	
    	window.addEventListener( 'resize', onWindowResize, false );
	
	}
	
	function onWindowResize() {
	
		windowHalfX = window.innerWidth / 2;
		windowHalfY = window.innerHeight / 2;
	
		camera.aspect = window.innerWidth / window.innerHeight;
		camera.updateProjectionMatrix();
	
		renderer.setSize( window.innerWidth, window.innerHeight );
	
	}
	
	function onDocumentMouseMove( event ) {
	
		mouseX = event.clientX - windowHalfX;
		mouseY = event.clientY - windowHalfY;
	
	}
	
	function onDocumentTouchStart( event ) {
	
    	if ( event.touches.length > 1 ) {
    	
    		event.preventDefault();
    	
    		mouseX = event.touches[ 0 ].pageX - windowHalfX;
    		mouseY = event.touches[ 0 ].pageY - windowHalfY;
    	
    	}
	
	}
	
	function onDocumentTouchMove( event ) {
	
		if ( event.touches.length == 1 ) {
	
			event.preventDefault();
	
			mouseX = event.touches[ 0 ].pageX - windowHalfX;
			mouseY = event.touches[ 0 ].pageY - windowHalfY;
	
	    }
	
	}
	
	function animate() {
	
		requestAnimationFrame( animate );
	
		render1();
	
	}
	
	function render() {
		camera.position.y += ( - mouseY + 200 - camera.position.y ) * .05;
		camera.lookAt( scene.position );
	
		renderer.render( scene, camera );
	
		var time = Date.now() * 0.0001;
	
		for ( var i = 0; i < scene.children.length; i ++ ) {
			var val = audioSource.streamData[i];
			val /= 255;
			var object = scene.children[ i ];
			if ( object instanceof THREE.Line ) {
				object.rotation.y = time * ( i < 4 ? ( i + 1 ) : - ( i + 1 ) );
				if(val > 0.80) {
					object.scale.x = object.scale.y = object.scale.z = object.originalScale + val*((i/5+1) * (1 + 0.5 * Math.sin( 7*time ) ) * 3);
				}
				else if(val < 0.20) {
					object.scale.x = object.scale.y = object.scale.z = object.originalScale + (1/val)*((i/5+1) * (1 + 0.5 * Math.sin( 7*time ) ) * 3);
				}
				else {
					object.scale.x = object.scale.y = object.scale.z = object.originalScale + ((i/5+1) * (1 + 0.5 * Math.sin( 7*time ) ));
				}
			}
		}
	    // Schedule next animation frame
        requestAnimationFrame(draw);
	}
	
	function render1() {
		camera.position.y += ( - mouseY + 200 - camera.position.y ) * .05;
		camera.lookAt( scene.position );
	
		renderer.render( scene, camera );
	
		var time = Date.now() * 0.0001;
	
		for ( var i = 0; i < scene.children.length; i ++ ) {
			var val = audioSource.streamData[i];
			val /= 255;
			var object = scene.children[ i ];
			if ( object instanceof THREE.Line ) {
				object.rotation.y = time * ( i < 4 ? ( i + 1 ) : - ( i + 1 ) );
				if(val > 0.90) {
					object.scale.x = object.scale.y = object.scale.z = object.originalScale+(0.5*val)*Math.sin(time);
				}
				else if(val < 0.10) {
					object.scale.x = object.scale.y = object.scale.z = object.originalScale+(0.5*val)*Math.sin(time);
				}
                else
                    object.scale.x = object.scale.y = object.scale.z = object.originalScale-(0.05*val)*Math.sin(time);
			}
		}
	}
	
	// Begin stream and drawing runs
    audioSource.playStream(loader.streamUrl);
}

function blackHole() {
	if ( ! Detector.webgl ) Detector.addGetWebGLMessage();
	var container, stats;
	var camera, scene, renderer;
	var uniforms;
	var player = document.getElementById('player');
	var loader = new SoundcloudLoader(player);
	var audioSource = new SoundCloudAudioSource(player);

	init();
	animate();

	function init() {

		container = document.getElementById( 'canvas' );

		camera = new THREE.Camera();
		camera.position.z = 1;

		scene = new THREE.Scene();

		var geometry = new THREE.PlaneBufferGeometry( 2, 2 );

		uniforms = {
			time: { type: "f", value: 1.0 },
			resolution: { type: "v2", value: new THREE.Vector2() }
		};

		var material = new THREE.ShaderMaterial( {

			uniforms: uniforms,
			vertexShader: document.getElementById( 'vertexShader' ).textContent,
			fragmentShader: document.getElementById( 'fragmentShader' ).textContent

		} );

		var mesh = new THREE.Mesh( geometry, material );
		scene.add( mesh );
	
		renderer = new THREE.WebGLRenderer();
		renderer.setPixelRatio( window.devicePixelRatio );
		container.appendChild( renderer.domElement );
	
		stats = new Stats();
		stats.domElement.style.position = 'absolute';
		stats.domElement.style.top = '0px';
		container.appendChild( stats.domElement );
	
		onWindowResize();
	
		window.addEventListener( 'resize', onWindowResize, false );

	}

	function onWindowResize( event ) {

		renderer.setSize( window.innerWidth, window.innerHeight );

		uniforms.resolution.value.x = renderer.domElement.width;
		uniforms.resolution.value.y = renderer.domElement.height;

	}

	function animate() {

		requestAnimationFrame( animate );

		render();
		stats.update();

	}

	function render() {
		
		uniforms.time.value += 0.05;    //Here is where you can sync the animation with the music. audioSource hold the volume actually.
		renderer.render( scene, camera );
	}
	// Begin stream and drawing runs
    audioSource.playStream(loader.streamUrl);
}
