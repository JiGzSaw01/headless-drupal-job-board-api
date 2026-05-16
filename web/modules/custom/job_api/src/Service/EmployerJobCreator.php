<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Creates employer-owned job nodes.
 */
final class EmployerJobCreator implements EmployerJobCreatorInterface {

  private const ALLOWED_JOB_TYPES = [
    'full_time',
    'remote',
    'contract',
  ];

  /**
   * Constructs an EmployerJobCreator object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly AccountProxyInterface $currentUser,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function create(array $data): array {
    if ($this->currentUser->isAnonymous()) {
      throw new AccessDeniedHttpException('Authentication is required.');
    }

    $this->validate($data);

    $storage = $this->entityTypeManager->getStorage('node');
    $values = [
      'type' => 'job',
      'title' => $this->stringValue($data['title']),
      'uid' => (int) $this->currentUser->id(),
      'status' => NodeInterface::NOT_PUBLISHED,
      'field_title' => $this->stringValue($data['title']),
      'field_company' => $this->stringValue($data['company']),
      'field_description' => [
        'value' => $this->stringValue($data['description']),
        'format' => 'plain_text',
      ],
      'field_location' => $this->stringValue($data['location']),
      'field_job_type' => $this->stringValue($data['job_type']),
      'field_salary' => $this->stringValue($data['salary']),
      'field_expiration_date' => $this->stringValue($data['expiration_date']),
      'field_featured' => 0,
    ];

    $job = $storage->create($values);
    $job->save();

    return [
      'id' => (int) $job->id(),
      'title' => $job->label(),
      'status' => 'pending',
      'owner_id' => (int) $this->currentUser->id(),
    ];
  }

  /**
   * Validates required API input before creating a node.
   *
   * @param array<string, mixed> $data
   *   Decoded JSON request data.
   */
  private function validate(array $data): void {
    $required_fields = [
      'title',
      'company',
      'description',
      'location',
      'job_type',
      'salary',
      'expiration_date',
    ];

    foreach ($required_fields as $field) {
      if (!isset($data[$field]) || trim($this->stringValue($data[$field])) === '') {
        throw new BadRequestHttpException(sprintf('Missing required field: %s.', $field));
      }
    }

    if (!in_array($this->stringValue($data['job_type']), self::ALLOWED_JOB_TYPES, TRUE)) {
      throw new BadRequestHttpException('Invalid job_type. Allowed values: full_time, remote, contract.');
    }
  }

  /**
   * Converts scalar input values into strings.
   */
  private function stringValue(mixed $value): string {
    if (!is_scalar($value)) {
      return '';
    }

    return trim((string) $value);
  }

}
