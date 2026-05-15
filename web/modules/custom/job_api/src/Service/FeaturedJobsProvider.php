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
        $items[] = $this->normalizeJob($job);
      }
    }

    return $items;
  }

  /**
   * Converts a job node into the public API contract.
   *
   * @return array<string, mixed>
   *   Normalized public job data.
   */
  private function normalizeJob(NodeInterface $job): array {
    return [
      'id' => (int) $job->id(),
      'title' => $this->getFieldValue($job, 'field_title') ?: $job->label(),
      'company' => $this->getFieldValue($job, 'field_company'),
      'location' => $this->getFieldValue($job, 'field_location'),
      'job_type' => $this->getFieldValue($job, 'field_job_type'),
      'salary' => $this->getFieldValue($job, 'field_salary'),
      'expires_at' => $this->getFieldValue($job, 'field_expiration_date'),
      'created_at' => date(DATE_ATOM, (int) $job->getCreatedTime()),
    ];
  }

  /**
   * Safely reads a single-value field from a node.
   */
  private function getFieldValue(NodeInterface $job, string $field_name): ?string {
    if (!$job->hasField($field_name) || $job->get($field_name)->isEmpty()) {
      return NULL;
    }

    $value = $job->get($field_name)->value;

    return $value === NULL ? NULL : (string) $value;
  }

}
