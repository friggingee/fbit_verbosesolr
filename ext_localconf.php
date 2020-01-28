<?php

if (TYPO3_MODE === 'BE') {
    $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    $pageRenderer->loadRequireJsModule('TYPO3/CMS/VerboseSolr/VerboseSolr');
}
