<?php

namespace Drupal\cron_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Lock\LockBackendInterface;

class CronStatusController extends ControllerBase {

  /**
   * The lock backend service.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * Constructs a CronStatusController object.
   *
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock backend service.
   */
  public function __construct(LockBackendInterface $lock) {
    $this->lock = $lock;
  }

  /**
   * Factory method for the controller.
   *
   * This is necessary because controllers are not automatically services,
   * and we need to pass dependencies.
   *
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock backend service.
   *
   * @return static
   */
  public static function create($container) {
    return new static(
      $container->get('lock')
    );
  }

  /**
   * Returns the page content.
   */
  public function status() {
    // Check if cron is locked.
    $isCronRunning = $this->lock->lockMayBeAvailable('cron') ? 'No' : 'Yes';

    // Return the output.
    return [
      '#markup' => $this->t('Is cron running? @status', ['@status' => $isCronRunning]),
    ];
  }

}
