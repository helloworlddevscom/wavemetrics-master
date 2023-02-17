<?php

namespace Drupal\Tests\comment\Functional\Views;

use Drupal\comment\Entity\Comment;
use Drupal\comment\Tests\CommentTestTrait;
use Drupal\entity_test\Entity\EntityTestMulChanged;
use Drupal\Tests\views\Functional\ViewTestBase;
use Drupal\user\Entity\Role;
use Drupal\views\Tests\ViewTestData;

/**
 * Tests the comment statistics handlers.
 *
 * @group comment
 */
class CommentStatisticsTest extends ViewTestBase {

  use CommentTestTrait;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['comment', 'comment_test_views', 'entity_test'];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_ces_entity_test_mul_changed'];

  public function testEntityMulChanged() {
    ViewTestData::createTestViews(get_class($this), ['comment_test_views']);

    $role = Role::load('anonymous')
      ->grantPermission('view test entity')
      ->grantPermission('access comments')
      ->save();

    $this->addDefaultCommentField('entity_test_mul_changed', 'entity_test_mul_changed');
    $entity = EntityTestMulChanged::create([
      'name' => $this->randomString(),
    ]);
    $entity->save();

    /** @var \Drupal\comment\Entity\Comment $comment */
    $comment = Comment::create([
      'subject' => $this->randomString(),
      'comment_type' => 'comment',
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'field_name' => 'comment',
      'changed' => \Drupal::time()->getRequestTime() + 3600,
    ]);
    $comment->setPublished()->save();

    $entity = EntityTestMulChanged::create([
      'name' => $this->randomString(),
      'changed' => \Drupal::time()->getRequestTime() + 7200,
    ]);
    $entity->save();

    $result = views_get_view_result('test_ces_entity_test_mul_changed');
    $this->assertEquals($result[0]->comment_entity_statistics_last_updated, $entity->getChangedTime());
    $this->assertEquals($result[1]->comment_entity_statistics_last_updated, $comment->getChangedTime());
  }

}
