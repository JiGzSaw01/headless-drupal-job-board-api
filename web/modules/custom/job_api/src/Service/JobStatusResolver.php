<?php

declare(strict_types=1);

namespace Drupal\job_api\Service;

use Drupal\node\NodeInterface;

/**
 * Class for resolving job statuses.
 */
final class JobStatusResolver implements JobStatusResolverInterface {

  private const STATUS_EXPIRED = 'expired';
  private const STATUS_LIVE = 'live';
  private const STATUS_DRAFT = 'draft';
  private const STATUS_UNDER_REVIEW = 'under_review';
  private const STATUS_UNPUBLISHED = 'unpublished';

  private const MODERATION_DRAFT = 'draft';
  private const MODERATION_NEEDS_REVIEW = 'pending_review';

  /**
   * {@inheritdoc}
   */
  public function resolve(NodeInterface $node): string {
    if ($this->isExpired($node)) {
      return self::STATUS_EXPIRED;
    }
    if ($node->isPublished()) {
      return self::STATUS_LIVE;
    }
    $moderation_state = $this->getFieldValue($node, 'moderation_state');
    if ($moderation_state === self::MODERATION_DRAFT) {
      return self::STATUS_DRAFT;
    }
    if ($moderation_state === self::MODERATION_NEEDS_REVIEW) {
      return self::STATUS_UNDER_REVIEW;
    }

    return self::STATUS_UNPUBLISHED;
  }

  /**
   * Determines whether the job has passed its expiration date.
   */
  private function isExpired(NodeInterface $node): bool {
    if (!$node->hasField('field_expiration_date') || $node->get('field_expiration_date')->isEmpty()) {
      return FALSE;
    }

    $expiration_date = $node->get('field_expiration_date')->value;
    $expiration_datetime = new \DateTimeImmutable($expiration_date);
    $now = new \DateTimeImmutable();

    return $expiration_datetime < $now;
  }

  /**
   * Safely reads a single-value field from a job node.
   */
  private function getFieldValue(NodeInterface $node, string $field_name): ?string {
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return NULL;
    }

    $value = $node->get($field_name)->value;

    return $value === NULL ? NULL : (string) $value;
  }

}
