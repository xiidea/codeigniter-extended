<?php

if (!isset($_SERVER['HTTP_HOST'])) {
    exit('This script cannot be run from the CLI. Run it from a browser.');
}

if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Forbidden');
    exit('This script is only accessible from localhost.');
}

require_once dirname(__FILE__).'/../src/libs/Xiidea/Installer/Services/CixRequirements.php';

$cixRequirements = new CixRequirements();

$majorProblems = $cixRequirements->getFailedRequirements();
$minorProblems = $cixRequirements->getFailedRecommendations();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="robots" content="noindex,nofollow" />
        <title>CIX Installation Checker</title>
        <link rel="stylesheet" href="assets/css/mini.php?files=structure,body,install.css" media="all" />
    </head>
    <body>
        <div id="content">
            <div class="header clear-fix">
                <div class="header-logo">
                    <img src="assets/images/cix_logo.png" alt="CIX" /><span>Puts your development on fast track</span>
                </div>
            </div>

            <div class="sf-reset">
                <div class="block">
                    <div class="symfony-block-content">
                        <h1 class="title">Welcome!</h1>
                        <p>Welcome to your new <strong>CIX</strong> project! powered by Codeigniter.</p>
                        <p>
                            This script will check installation status and your server configuration for the required settings and suggest you the fix.
                        </p>

                        <?php if (count($majorProblems)): ?>
                      <h2 class="ko">Major problems</h2>
                            <p>Major problems have been detected and <strong>must</strong> be fixed before continuing:</p>
                            <ol>
                                <?php foreach ($majorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if (count($minorProblems)): ?>
                            <h2>Recommendations</h2>
                            <p>
                                <?php if (count($majorProblems)): ?>Additionally, to<?php else: ?>To<?php endif; ?> enhance your Codeigniter experience,
                                it’s recommended that you fix the following:
                            </p>
                            <ol>
                                <?php foreach ($minorProblems as $problem): ?>
                                    <li><?php echo $problem->getHelpHtml() ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($cixRequirements->hasPhpIniConfigIssue()): ?>
                            <p id="phpini">*
                                <?php if ($cixRequirements->getPhpIniConfigPath()): ?>
                                    Changes to the <strong>php.ini</strong> file must be done in "<strong><?php echo $cixRequirements->getPhpIniConfigPath() ?></strong>".
                                <?php else: ?>
                                    To change settings, create a "<strong>php.ini</strong>".
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!count($majorProblems) && !count($minorProblems)): ?>
                            <p class="ok">Your configuration looks good to run CIX Distribution.</p>
                        <?php endif; ?>

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>                                
                                <li><a href="./">Take me to the Welcome page</a></li>
                            <?php endif; ?>
                            <?php if (count($majorProblems) || count($minorProblems)): ?>
                                <li><a href="config.php">Re-check configuration</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="version">Codeigniter Extended Edition</div>
        </div>
    </body>
</html>
