function cubeAnimation()
{
    var player = document.getElementById('player');
    var canvasElement = document.getElementById('canvas');
    var context = canvasElement.getContext("2d");
    var loader = new SoundcloudLoader(player);
    var audioSource = new SoundCloudAudioSource(player);

    // Assign choice
    var choice = 1;

    // Scene set-up

    // Configuration variables
    var numSamples = audioSource.streamData.length;
    var defaultCameraPosition = { x: 0, y: 0, z: 10 };
    var maxDisplacement = { width: 30, height: 0, length: 0 };
    var displacementScalar = 0.02;
    var lightStartPosition = { x: 0, y: 0, z: 20 };
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

    for (var i = 0; i < numSamples; i++)
    {
        var parameters = [];
        var sprites = [];
        
        // Initialize scenes
        switch (choice)
        {
            case 0: // cube geometry
                // Set up cubes
                var geometry = new THREE.BoxGeometry(1, 1, 1);
                var material = new THREE.MeshLambertMaterial( { color: Math.random() * 0xFFFFFF } );
                var cube = new THREE.Mesh(geometry, material);
                
                cube.position.x = -maxDisplacement.width + cubeSpacing * i;
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
        
        // Schedule next animation frame
        requestAnimationFrame(draw);
    };

    // Begin stream and drawing runs
    audioSource.playStream(loader.streamUrl);
    draw();
}