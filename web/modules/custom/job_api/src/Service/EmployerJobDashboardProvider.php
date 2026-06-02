<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;

final class EmployerJobDashboardProvider implements EmployerJobDashboardProviderInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly AccountProxyInterface $currentUser,
    private readonly JobNormalizer $jobNormalizer,
  ) {}

  public function getJobs(int $page = 1, int $limit = 10): array {
    $page = max(1, $page);
    $limit = max(1, min($limit, 50));
    $offset = ($page - 1) * $limit;
    $storage = $this->entityTypeManager->getStorage('node');

    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'job')
      ->condition('uid', $this->currentUser->id())
      ->sort('created', 'DESC');

    $count_query = clone $query;
    $total = (int) $count_query->count()->execute();

    $nids = $query
      ->range($offset, $limit)
      ->execute();

    $items = [];

    if ($nids !== []) {
      $jobs = $storage->loadMultiple($nids);

      foreach ($jobs as $job) {
        if ($job instanceof NodeInterface) {
          $items[] = $this->jobNormalizer->normalize($job);
        }
      }
    }

    return [
      'data' => $items,
      'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => (int) ceil($total / $limit),
      ],
    ];
    

    


  
    
  }

}
