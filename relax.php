<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take a Break 🦖</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #f0f0f0;
      font-family: Arial, sans-serif;
      overflow: hidden;
    }

    .iframe-fullscreen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      border: none;
      z-index: 1;
    }

    #quitBtn {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 2;
      padding: 10px 20px;
      background-color: #f44336;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>

  <iframe src="dino/index.html" class="iframe-fullscreen" allowfullscreen></iframe>
  <button id="quitBtn" onclick="window.close()">Quit</button>

</body>
</html>