<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Exception</title>

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto;
            background: #f3f4f6;
            color: #374151;
        }
        /* TOP BAR */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 55px;
            background: #dc2626;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-size: 20px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
            z-index: 1000;
        }
        /* MAIN */
        .container {
            margin-top: 80px;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
            padding: 20px;
        }
        .block {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .block h3 {
            margin: 0;
            padding: 14px 18px;
            background: #f3f4f6;
            font-size: 15px;
        }
        .block-content {
            padding: 18px;
        }
        /* CODE */

        .code {
            background: #13171c;
            color: #e5e7eb;
            padding: 16px;
            font-family: monospace;
            overflow: auto;
            font-size: 14px;
            line-height: 1.4;
            border: 4px solid white;
            box-shadow: 0px 0px 3px 2px black;
            margin: 5px;
        }
         .code-line { display: block; padding: 2px 10px; } 
         .code-error { background: #7f1d1d; }
        .error-line {
            background: #7f1d1d;
            display: block;
            padding-left: 5px;
        }
        /* DEBUG BUTTON */
        .debug-toggle {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background: white;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .25);
            cursor: pointer;
            font-size: 20px;
            z-index: 2000;
        }
        /* DEBUG BAR */
        .debugbar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            gap: 10px;
            padding: 8px 10px;
            background: #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .25);
            z-index: 1500;
        }
        /* BUTTONS */
        .dbg-btn {
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, .2);
            transition: .15s;
        }
        .dbg-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .3);
        }
        /* PANELS */
        .panel {
            position: fixed;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            max-height: 300px;
            overflow: auto;
            display: none;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .35);
            padding: 15px;
            z-index: 1400;
        }
        .panel pre {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            overflow: auto;
        }
    </style>
</head>

<body>
    <div class="topbar"> ⚠ Application Exception </div>
    <div class="container">
        <div class="section"> <b>Message</b><br> <?= htmlspecialchars($message) ?> </div>
        <div class="section"> <b>Location</b><br> <?= htmlspecialchars($file) ?> : <?= $line ?> </div>
        <div class="section">
            <b>Code</b> 
            <div class="code"> 
                <?php foreach ($snippet as $num => $code): ?> 
                    <span class="code-line <?= ($num + 1) == $line ? 'code-error' : '' ?>"> 
                        <?php $codeColored = highlight_string($code, true); ?>
                        <?php $codeColored = str_replace('style="color: #000000"', 'style="color: #4fe934"', $codeColored); ?>
                        <?= $num + 1 ?> | <?= $codeColored ?>
                    </span> 
                <?php endforeach; ?> 
            </div> 
        </div>

        <div class="block">
            <h3>Code to File</h3>
            <div class="code">
                <?php foreach ($traceArray as $i => $t): ?>
                    <div style="margin-bottom:6px">
                        <b>#<?= $i ?></b>
                        <?php if (isset($t['file'])): ?>
                            <a href="vscode://file/<?= $t['file'] ?>:<?= $t['line'] ?>" style="color:#2563eb;text-decoration:none">
                                <?= $t['file'] ?> : <?= $t['line'] ?>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($t['function'])): ?>
                            <span style="color:#6b7280">
                                <?= $t['function'] ?>()
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>



    <!-- DEBUG BUTTON -->

    <div class="debug-toggle" title="Открыть debug панель" onclick="toggleDebug()">ⓘ</div>



    <!-- DEBUG BAR -->

    <div class="debugbar" id="debugbar">
        <div class="dbg-btn" onclick="togglePanel('get')" title="GET">⬇</div>
        <div class="dbg-btn" onclick="togglePanel('post')" title="POST">⬆</div>
        <div class="dbg-btn" onclick="togglePanel('session')" title="SESSION">🔐</div>
        <div class="dbg-btn" onclick="togglePanel('server')" title="SERVER">🖥</div>
        <div class="dbg-btn" onclick="togglePanel('trace')" title="TRACE">🧵</div>
        <div class="dbg-btn" onclick="togglePanel('env')" title="ENV">⚙️</div>
    </div>

    <!-- PANELS -->

    <div id="get" class="panel">
        <h3>GET</h3>
        <pre><?php print_r($_GET ?? []); ?></pre>
    </div>

    <div id="post" class="panel">
        <h3>POST</h3>
        <pre><?php print_r($_POST ?? []); ?></pre>
    </div>

    <div id="session" class="panel">
        <h3>SESSION</h3>
        <pre><?php print_r($_SESSION ?? []); ?></pre>
    </div>

    <div id="server" class="panel">
        <h3>SERVER</h3>
        <pre><?php print_r($_SERVER ?? []); ?></pre>
    </div>

    <div id="trace" class="panel">
        <h3>Stack Trace</h3>
        <pre><?php print_r($trace ?? []); ?></pre>
    </div>
    <div id="env" class="panel">
        <h3>Environment</h3>
        <b>PHP</b>
        <pre><?= phpversion() ?></pre>
        <b>Memory usage</b>
        <pre><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</pre>
        <b>Peak memory</b>
        <pre><?= round(memory_get_peak_usage() / 1024 / 1024, 2) ?> MB</pre>
        <b>Request time</b>
        <pre><?= round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 3) ?> sec</pre>
        <b>Environment variables</b>
        <pre><?= htmlspecialchars(print_r($_ENV, true)) ?></pre>
    </div>
    <script>
        function toggleDebug() {
            const bar = document.getElementById('debugbar')
            bar.style.display = bar.style.display === "flex" ? "none" : "flex"
        }

        function togglePanel(id) {
            const panel = document.getElementById(id);

            if (panel.style.display === 'block') {
                panel.style.display = 'none';
                return;
            }

            document.querySelectorAll('.panel').forEach(p => p.style.display = 'none');
            panel.style.display = 'block';
        }
    </script>

</body>

</html>