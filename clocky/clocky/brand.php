<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Ecosystem | Full Screen Branding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0a0a0a;
            --clocky-color: #00ffe1;
            --stocky-color: #ffcc00;
            --dartsy-color: #ff3333;
            --printy-color: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: radial-gradient(circle, #1a1a1a 0%, #050505 100%);
            height: 100vh; /* Pontosan a képernyő magassága */
            width: 100vw;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly; /* Egyenletes elosztás fentről lefelé */
            align-items: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* Nincs görgetés, minden ráfér a képernyőre */
            padding: 20px 0;
        }

        /* --- KÖZÖS STÍLUSOK --- */
        .brand-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 25px;
            user-select: none;
        }

        h1 {
            color: #fff;
            font-size: 7rem; /* Megemelt méret a kitöltéshez */
            font-weight: 900;
            letter-spacing: -5px;
            display: flex;
            align-items: center;
            background: linear-gradient(to bottom, #ffffff 40%, #bbbbbb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.8));
            position: relative;
        }

        .divider {
            width: 300px; /* Szélesebb vonal a nagyobb képernyőhöz */
            height: 5px;
            border-radius: 10px;
        }

        /* --- PRINTY (FEHÉR) --- */
        .printy-divider { 
            background-color: var(--printy-color); 
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }
        .paper-line {
            display: inline-block; width: 50px; height: 14px;
            background-color: var(--printy-color); margin-left: 15px;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
            border-radius: 2px; -webkit-text-fill-color: initial;
        }

        /* --- STOCKY (SÁRGA) --- */
        .stocky-divider { 
            background-color: var(--stocky-color); 
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.3);
        }
        .lines-stack { display: flex; flex-direction: column; gap: 6px; margin-left: 18px; -webkit-text-fill-color: initial; }
        .line { height: 7px; background-color: var(--stocky-color); border-radius: 2px; }
        .line-1 { width: 45px; } .line-2 { width: 60px; } .line-3 { width: 35px; }

        /* --- DARTSY (PIROS) --- */
        .dartsy-divider { 
            background-color: var(--dartsy-color); 
            box-shadow: 0 0 20px rgba(255, 51, 51, 0.3);
        }
        .arrow-head { 
            width: 0; height: 0; border-top: 15px solid transparent; border-bottom: 15px solid transparent;
            border-left: 25px solid var(--dartsy-color); margin-left: 18px; -webkit-text-fill-color: initial;
        }

        /* --- CLOCKY (TÜRKIZ) --- */
        .clocky-divider { 
            background-color: var(--clocky-color); 
            box-shadow: 0 0 20px rgba(0, 255, 225, 0.3);
        }
        .cube { 
            display: inline-block; width: 28px; height: 28px; 
            background-color: var(--clocky-color); margin-left: 15px; 
            transform: rotate(-5deg); -webkit-text-fill-color: initial; 
        }

        /* --- RESPONSIVE SZABÁLYOK --- */
        @media (max-height: 800px) {
            h1 { font-size: 5rem; }
            .divider { width: 200px; }
        }

        @media (max-width: 768px) { 
            h1 { font-size: 3rem; letter-spacing: -2px; } 
            .divider { width: 120px; }
            .paper-line { width: 25px; height: 8px; }
            .line { height: 4px; }
        }
    </style>
</head>
<body>

    <div class="brand-wrapper">
        <div class="brand-container printy">
             <h1>Printy<span class="paper-line"></span></h1>
        </div>
        <div class="divider printy-divider"></div>
    </div>

    <div class="brand-wrapper">
        <div class="brand-container stocky">
            <h1>Stocky
                <div class="lines-stack">
                    <div class="line line-1"></div>
                    <div class="line line-2"></div>
                    <div class="line line-3"></div>
                </div>
            </h1>
        </div>
        <div class="divider stocky-divider"></div>
    </div>

    <div class="brand-wrapper">
        <div class="brand-container dartsy">
            <h1>Dartsy<div class="arrow-head"></div></h1>
        </div>
        <div class="divider dartsy-divider"></div>
    </div>

    <div class="brand-wrapper">
        <div class="brand-container clocky">
            <h1>Clocky<span class="cube"></span></h1>
        </div>
        <div class="divider clocky-divider"></div>
    </div>

</body>
</html>