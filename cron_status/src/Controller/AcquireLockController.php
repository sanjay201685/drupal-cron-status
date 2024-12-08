<?php

namespace Drupal\cron_status\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Lock\LockBackendInterface;

class AcquireLockController extends ControllerBase {

  /**
   * The lock backend service.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  protected $lockName;

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
  public function lock() {
    // Check if cron is locked.
    $lock_name = "cron";

    // Try to acquire the lock.
    if ($this->lock->acquire($lock_name, 30)) {
      try {
        // Perform the critical operation here.
        \Drupal::logger('Acquire Lock')->info('Lock acquired, processing...');
        sleep(5); // Simulate a time-consuming operation.
      }
      finally {
        // Release the lock after the operation is complete.
        $this->lock->release($lock_name);
        \Drupal::logger('Acquire Lock')->info('Lock released.');
      }
    }
    else {
      \Drupal::logger('Acquire Lock')->warning('Could not acquire the lock.');
    }

    // Return the output.
    return [
      '#markup' => $this->t('Drupal lock mechanisms running'),
    ];
  }

}
