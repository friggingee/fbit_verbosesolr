<?php

namespace FBIT\VerboseSolr\ViewHelpers;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IndexQueueDataViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('pageUid', 'string', 'UID of the current root page', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $arguments = $this->arguments;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_solr_indexqueue_item');
        $queryBuilder->select('*', 'item.uid as item_uuid', 'item.root as item_root')
            ->from('tx_solr_indexqueue_item', 'item')
            ->leftJoin(
                'item',
                'tx_solr_indexqueue_indexing_property',
                'prop',
                $queryBuilder->expr()->eq(
                    'item.uid',
                    $queryBuilder->quoteIdentifier('prop.item_id')
                )
            )
            ->where($queryBuilder->expr()->eq('item.root', (int)$arguments['pageUid']))
            ->orderBy('item.indexed', 'desc');

        $indexQueueData = $queryBuilder->execute()->fetchAll();

        foreach ($indexQueueData as $index => $indexQueueDatum) {
            $indexQueueData[$indexQueueDatum['indexing_configuration']][] = $indexQueueDatum;
            unset($indexQueueData[$index]);
        }

        ksort($indexQueueData);

        return $indexQueueData;
    }
}
