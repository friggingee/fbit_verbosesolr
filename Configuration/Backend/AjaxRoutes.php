<?php

/**
 * Definitions for routes provided by EXT:solr
 */
return [
    'verboseSolr_reindexItem' => [
        'path' => '/verboseSolr/reindexItem',
        'target' => \FBIT\VerboseSolr\Controller\Backend\AjaxController::class . '::reindexItem'
    ],
];
