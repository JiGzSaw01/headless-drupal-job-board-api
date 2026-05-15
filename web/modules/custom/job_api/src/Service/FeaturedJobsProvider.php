<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Loads and normalizes published featured job nodes.
 */
final class FeaturedJobsProvider implements FeaturedJobsProviderInterface {

  /**
   * Constructs a FeaturedJobsProvider object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly JobNormalizer $jobNormalizer,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function getFeaturedJobs(int $limit = 6): array {
    $limit = max(1, min($limit, 20));
    $storage = $this->entityTypeManager->getStorage('node');

    $nids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'job')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('field_featured', 1)
      ->sort('created', 'DESC')
      ->range(0, $limit)
      ->execute();

    if ($nids === []) {
      return [];
    }

    $jobs = $storage->loadMultiple($nids);
    $items = [];

    foreach ($jobs as $job) {
      if ($job instanceof NodeInterface) {
        $items[] = $this->jobNormalizer->normalize($job);
      }
    }

    return $items;
  }

}
