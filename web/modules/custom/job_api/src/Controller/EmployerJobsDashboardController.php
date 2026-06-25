<?php

declare(strict_types=1);

namespace Drupal\job_api\Controller;

use Drupal\job_api\Service\EmployerJobDashboardProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lists employer-owned job posts.
 */
final class EmployerJobsDashboardController {

  /**
   * Constructs an EmployerJobsDashboardController object.
   */
  public function __construct(
    private readonly EmployerJobDashboardProviderInterface $dashboardProvider,
  ) {
  }

  /**
   * Lists employer-owned job posts.
   */
  public function list(Request $request): JsonResponse {
    $page = $request->query->getInt('page', 1);
    $limit = $request->query->getInt('limit', 10);

    return new JsonResponse(
      $this->dashboardProvider->getJobs($page, $limit)
    );
  }

}
