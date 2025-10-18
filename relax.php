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

    .control-buttons {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 2;
      display: flex;
      gap: 10px;
    }

    .control-buttons button {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    #startBtn {
      background-color: #4CAF50;
      color: white;
    }

    #quitBtn {
      background-color: #f44336;
      color: white;
    }
  </style>
</head>
<body>

  <iframe id="dinoFrame" src="dino/index.html" class="iframe-fullscreen" allowfullscreen></iframe>

  <div class="control-buttons">
    <button id="startBtn">Start</button>
    <button id="quitBtn" onclick="window.close()">Quit</button>
  </div>

  <script>
    document.getElementById('startBtn').addEventListener('click', function() {
      const iframe = document.getElementById('dinoFrame');
      const iframeWindow = iframe.contentWindow;

      // Simulate spacebar keypress to start the game
      const event = new KeyboardEvent('keydown', {
        key: ' ',
        code: 'Space',
        keyCode: 32,
        which: 32,
        bubbles: true
      });

      iframeWindow.document.dispatchEvent(event);
    });
  </script>

</body>
</html>