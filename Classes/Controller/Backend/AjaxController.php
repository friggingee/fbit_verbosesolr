<?php
declare(strict_types=1);
namespace FBIT\VerboseSolr\Controller\Backend;

use ApacheSolrForTypo3\Solr\ConnectionManager;
use ApacheSolrForTypo3\Solr\Domain\Site\Site;
use ApacheSolrForTypo3\Solr\Domain\Site\SiteRepository;
use ApacheSolrForTypo3\Solr\IndexQueue\Indexer;
use ApacheSolrForTypo3\Solr\IndexQueue\Item;
use ApacheSolrForTypo3\Solr\IndexQueue\Queue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handling of Ajax requests
 */
class AjaxController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function reindexItem(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $itemUids = explode(',', $queryParams['item_uid']);

        $siteRepository = GeneralUtility::makeInstance(SiteRepository::class);

        $content = [];

        foreach ($itemUids as $itemUid) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_solr_indexqueue_item');

            $itemData = $queryBuilder->select('*')
                ->from('tx_solr_indexqueue_item')
                ->where(
                    $queryBuilder->expr()->eq('uid', $itemUid)
                )
                ->execute()
                ->fetchAll()[0];

            $item = GeneralUtility::makeInstance(
                Item::class,
                $itemData
            );

            $site = $siteRepository->getSiteByRootPageId($item->getRootPageUid());

            $configuration = $site->getSolrConfiguration();
            $indexingConfigurationName = $item->getIndexingConfigurationName();

            $indexerClass = $configuration->getIndexQueueIndexerByConfigurationName($indexingConfigurationName);
            $indexerConfiguration = $configuration->getIndexQueueIndexerConfigurationByConfigurationName($indexingConfigurationName);

            $indexer = GeneralUtility::makeInstance($indexerClass, /** @scrutinizer ignore-type */ $indexerConfiguration);

            try {
                $indexingResult = $indexer->index($item);
                GeneralUtility::makeInstance(Queue::class)->updateIndexTimeByItem($item);
            } catch (\Exception $exception) {
                $indexingResult = $exception->getMessage();
                GeneralUtility::makeInstance(Queue::class)->markItemAsFailed($item, $exception->getCode() . ':' . $exception->__toString());
            }

            $content[$itemUid] = $indexingResult;
        }

        $response->getBody()->write(json_encode($content));
        return $response;
    }
}
