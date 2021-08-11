<?php
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

defined('TYPO3_MODE') or die();

$bootstrap = function () {
    // Register event plugin
    $controllerActions = [
        \AawTeam\Verowa\Controller\EventController::class => 'list',
    ];
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Verowa',
        'Event',
        $controllerActions
    );
};
$bootstrap();
unset($bootstrap);
