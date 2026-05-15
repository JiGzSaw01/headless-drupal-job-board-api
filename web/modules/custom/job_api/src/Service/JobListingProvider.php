<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\node\NodeInterface;

/**
 * Loads and normalizes public job listings.
 */
final class JobListingProvider implements JobListingProviderInterface {

  /**
   * Constructs a JobListingProvider object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly JobNormalizer $jobNormalizer,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function getJobs(array $filters = [], int $page = 1, int $limit = 10): array {
    $page = max(1, $page);
    $limit = max(1, min($limit, 50));
    $offset = ($page - 1) * $limit;
    $storage = $this->entityTypeManager->getStorage('node');

    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'job')
      ->condition('status', NodeInterface::PUBLISHED)
      ->sort('created', 'DESC');

    $this->applyFilters($query, $filters);

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
        'location' => $filters['location'] ?? NULL,
        'job_type' => $filters['job_type'] ?? NULL,
      ],
    ];
  }

  /**
   * Applies supported public listing filters.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The entity query to modify.
   * @param array<string, string|null> $filters
   *   Supported listing filters.
   */
  private function applyFilters(QueryInterface $query, array $filters): void {
    if (!empty($filters['location'])) {
      $query->condition('field_location', $filters['location'], 'CONTAINS');
    }

    if (!empty($filters['job_type'])) {
      $query->condition('field_job_type', $filters['job_type']);
    }
  }

}
