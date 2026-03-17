<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Exception</title>

    <!-- Error page styles -->
    <link rel="stylesheet"
        href="/assets/css/error.css?v=<?= filemtime(BASE_PATH . '/public/assets/css/error.css') ?>">
</head>

<body>

    <!-- =========================
TOP BAR
========================= -->
    <div class="topbar">
        <div class="topbar-inner">

            <div class="topbar-title">
                <i class="fas fa-triangle-exclamation"></i>
                Application Exception
            </div>

            <div class="topbar-location">
                <i class="fas fa-file-code"></i>
                <?= htmlspecialchars($file) ?> : <?= $line ?>
            </div>

        </div>
    </div>


    <!-- =========================
MAIN CONTAINER
========================= -->
    <div class="container">

        <div class="layout">

            <!-- =========================
        LEFT SIDE (65%)
        ========================= -->
            <div class="main">

                <!-- MESSAGE -->
                <div class="block">
                    <h3>
                        <i class="fas fa-bug"></i>
                        Error Message
                    </h3>

                    <div class="block-content">
                        <?= htmlspecialchars($message) ?>
                    </div>
                </div>


                <!-- CODE SNIPPET -->
                <div class="block">

                    <h3>
                        <i class="fas fa-code"></i>
                        Code
                    </h3>

                    <div class="code">

                        <?php foreach ($snippet as $num => $code): ?>

                            <span class="code-line <?= ($num + 1) == $line ? 'code-error' : '' ?>">

                                <?php
                                $codeColored = highlight_string($code, true);
                                $codeColored = str_replace(
                                    'style="color: #000000"',
                                    'style="color: #4fe934"',
                                    $codeColored
                                );
                                ?>

                                <?= $num + 1 ?> | <?= $codeColored ?>

                            </span>

                        <?php endforeach; ?>

                    </div>
                </div>


                <!-- STACK TRACE -->
                <div class="block">

                    <h3>
                        <i class="fas fa-layer-group"></i>
                        Stack Trace
                    </h3>

                    <div class="code">

                        <?php foreach ($traceArray as $i => $t): ?>

                            <div class="trace-item">

                                <b>#<?= $i ?></b>

                                <?php if (isset($t['file'])): ?>

                                    <a href="vscode://file/<?= $t['file'] ?>:<?= $t['line'] ?>"
                                        class="trace-file">

                                        <?= $t['file'] ?> : <?= $t['line'] ?>

                                    </a>

                                <?php endif; ?>

                                <?php if (isset($t['function'])): ?>

                                    <span class="trace-function">
                                        <?= $t['function'] ?>()
                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

            </div>


            <!-- =========================
        RIGHT SIDE (35%)
        ========================= -->
            <div class="sidebar">

                <!-- TABS -->
                <div class="tabs">

                    <button onclick="showTab('request')">
                        <i class="fas fa-network-wired"></i>
                        Request
                    </button>

                    <?php if (!empty($params)): ?>
                        <button onclick="showTab('sql')">
                            <i class="fas fa-database"></i>
                            SQL
                        </button>
                    <?php endif; ?>

                    <button onclick="showTab('php')">
                        <i class="fab fa-php"></i>
                        PHP
                    </button>

                </div>


                <!-- ======================
            REQUEST PANEL
            ====================== -->
                <div id="request" class="tab-panel">

                    <h3>Request Data</h3>

                    <div class="accordion">

                        <div class="acc-block">
                            <div class="acc-title" onclick="toggleAcc(this)">
                                GET
                            </div>

                            <pre><?php print_r($_GET ?? []); ?></pre>
                        </div>

                        <div class="acc-block">
                            <div class="acc-title" onclick="toggleAcc(this)">
                                POST
                            </div>

                            <pre><?php print_r($_POST ?? []); ?></pre>
                        </div>

                        <div class="acc-block">
                            <div class="acc-title" onclick="toggleAcc(this)">
                                SESSION
                            </div>

                            <pre><?php print_r($_SESSION ?? []); ?></pre>
                        </div>

                        <div class="acc-block">
                            <div class="acc-title" onclick="toggleAcc(this)">
                                SERVER
                            </div>

                            <pre><?php print_r($_SERVER ?? []); ?></pre>
                        </div>

                    </div>

                </div>


                <!-- ======================
            SQL PANEL
            ====================== -->
                <?php if (!empty($params)): ?>

                    <div id="sql" class="tab-panel">

                        <h3>SQL Query</h3>

                        <pre><?= htmlspecialchars($sql ?? '') ?></pre>

                        <h3>Params</h3>

                        <pre><?= htmlspecialchars(json_encode($params, JSON_PRETTY_PRINT)) ?></pre>

                        <h3>Builder Stack</h3>

                        <pre><?php print_r($builderStack ?? []); ?></pre>

                    </div>

                <?php endif; ?>


                <!-- ======================
            PHP INFO
            ====================== -->
                <div id="php" class="tab-panel">

                    <h3>PHP Info</h3>

                    <pre>
PHP Version: <?= phpversion() ?>

Memory usage: <?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB

Peak memory: <?= round(memory_get_peak_usage() / 1024 / 1024, 2) ?> MB

Request time:
<?= round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 3) ?> sec
                </pre>

                </div>


            </div>

        </div>

    </div>


    <script src="/assets/js/error.js?v=<?= filemtime(BASE_PATH . '/public/assets/js/error.js') ?>"></script>

</body>

</html>