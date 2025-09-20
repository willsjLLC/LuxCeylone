<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Launch Page</title>
    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('assets/image/background.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: url('{{ asset('assets/image/background.png') }}') no-repeat center center/cover;
            color: white;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .launch-btn {
            margin-top: 600px;
            font-size: 3rem;
            padding: 40px 70px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #00C9FF, #92FE9D);
            color: #ffffff;
            font-weight: bolder;
            box-shadow: 0 0 20px rgba(0, 201, 255, 0.8), 0 0 30px rgba(146, 254, 157, 0.8);
            position: relative;
            cursor: pointer;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-shadow: 0 0 10px rgba(0, 255, 209, 0.8), 0 0 20px rgba(146, 254, 157, 0.8);
            animation: gradientBackground 3s infinite;
        }

        .launch-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(0, 201, 255, 1), 0 0 40px rgba(146, 254, 157, 1);
            text-shadow: 0 0 15px rgba(0, 255, 209, 1), 0 0 30px rgba(146, 254, 157, 1);
        }

        @keyframes gradientBackground {
            0% {
                background: linear-gradient(90deg, #00C9FF, #92FE9D);
            }

            50% {
                background: linear-gradient(90deg, #FF6F00, #FFD54F);
            }

            100% {
                background: linear-gradient(90deg, #00C9FF, #92FE9D);
            }
        }

        #sparkles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: none;
            z-index: 10;
            overflow: hidden;
        }

        .sparkle {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.9);
            animation: fall 5s linear infinite;
        }

        @keyframes fall {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translateY(100vh) scale(0.5);
            }
        }

        #loading-bar-container {
            width: 80%;
            height: 40px;
            margin-top: 20px;
            border: 2px solid white;
            border-radius: 10px;
            overflow: hidden;
            display: none;
        }

        #loading-bar {
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, #00C9FF, #92FE9D);
            animation: load 40s linear forwards;
        }

        #loading-text {
            margin-top: 10px;
            font-size: 2rem;
            color: #92FE9D;
            display: none;
            text-shadow: 0px 0px 5px rgba(0, 255, 200, 0.8);
        }

        @keyframes load {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <button class="launch-btn" onclick="startAnimation()">
        Launch Website
    </button>

    <div id="sparkles-container"></div>
    <div id="loading-bar-container">
        <div id="loading-bar"></div>
    </div>
    <div id="loading-text">Loading...</div>

    <!-- Optional Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loadingStages = [
            "Initializing System...",
            "Server Start...",
            "Fetching Configuration Files...",
            "Validating Environment Variables...",
            "Starting Database Services...",
            "Database Creating...",
            "Establishing Secure Connection...",
            "Database Connect...",
            "Loading Core Modules...",
            "Authenticating User Credentials...",
            "Migrating Services...",
            "Optimizing Assets...",
            "Compiling Codebase...",
            "Indexing Database...",
            "Caching Static Files...",
            "Validating API Endpoints...",
            "Establishing Communication Channels...",
            "Loading User Interface...",
            "Testing Network Stability...",
            "Finalizing Security Checks...",
            "Starting Background Services...",
            "Synchronizing Time Zones...",
            "Performing System Diagnostics...",
            "Deploying Resources...",
            "Finalizing Deployment..."
        ];

        function startAnimation() {
            document.querySelector('.launch-btn').style.display = 'none';
            const sparklesContainer = document.getElementById('sparkles-container');
            const loadingBarContainer = document.getElementById('loading-bar-container');
            const loadingBar = document.getElementById('loading-bar');
            const loadingText = document.getElementById('loading-text');

            sparklesContainer.style.display = 'block';
            loadingBarContainer.style.display = 'block';
            loadingText.style.display = 'block';

            const numberOfSparkles = 1000;
            for (let i = 0; i < numberOfSparkles; i++) {
                const sparkle = document.createElement('div');
                sparkle.classList.add('sparkle');
                sparkle.style.left = Math.random() * window.innerWidth + 'px';
                sparkle.style.animationDelay = Math.random() * 5 + 's';
                sparkle.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 75%)`;
                sparkle.style.boxShadow = `0 0 15px hsl(${Math.random() * 360}, 100%, 75%)`;
                sparklesContainer.appendChild(sparkle);
            }

            const totalDuration = 40000; // 40 seconds
            const stageDuration = totalDuration / loadingStages.length;

            let currentStage = 0;

            const interval = setInterval(() => {
                if (currentStage < loadingStages.length) {
                    loadingText.textContent = loadingStages[currentStage];
                    currentStage++;
                } else {
                    clearInterval(interval);
                }
            }, stageDuration);

            setTimeout(() => {
                window.location.href = "https://addciti.com";
            }, totalDuration);
        }
    </script>
</body>

</html>
