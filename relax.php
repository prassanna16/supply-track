<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Relax with Dino</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f0f0;
      text-align: center;
      margin: 0;
      padding: 20px;
    }

    h1 {
      color: #4CAF50;
      margin-bottom: 20px;
    }

    .iframe-container {
      position: relative;
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      padding-bottom: 25%;
      height: 0;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .iframe-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    #quitBtn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #f44336;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h1>Take a Break 🦖</h1>

  <div class="iframe-container">
    <iframe src="supply-track\dino\index.html" allowfullscreen></iframe>
  </div>

  <button id="quitBtn" onclick="window.close()">Quit</button>

</body>
</html>