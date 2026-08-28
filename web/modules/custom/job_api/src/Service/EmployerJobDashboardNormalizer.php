<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\node\NodeInterface;

/**
 * Converts job nodes into the private API contract.
 */
final class EmployerJobDashboardNormalizer {

  /**
   * Constructs an EmployerJobDashboardNormalizer object.
   */
  public function __construct(
    private readonly JobStatusResolverInterface $jobStatusResolver,
  ) {
  }

  /**
   * Normalizes a job node for private API responses.
   *
   * @return array<string, mixed>
   *   Normalized private job data.
   */
  public function normalize(NodeInterface $job): array {
    return [
      'id' => (int) $job->id(),
      'title' => $this->getFieldValue($job, 'field_title') ?: $job->label(),
      'company' => $this->getFieldValue($job, 'field_company'),
      'location' => $this->getFieldValue($job, 'field_location'),
      'job_type' => $this->getFieldValue($job, 'field_job_type'),
      'salary' => $this->getFieldValue($job, 'field_salary'),
      'expires_at' => $this->getFieldValue($job, 'field_expiration_date'),
      'created_at' => date(DATE_ATOM, (int) $job->getCreatedTime()),
      'updated_at' => date(DATE_ATOM, (int) $job->getChangedTime()),
      'owner_id' => (int) $job->getOwnerId(),
      'published' => $job->isPublished(),
      'moderation_state' => $this->getFieldValue($job, 'moderation_state'),
      'status' => $this->jobStatusResolver->resolve($job),
      'featured' => (bool) $this->getFieldValue($job, 'field_featured'),
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
