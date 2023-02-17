<?php

namespace Drupal\comment\Plugin\views\sort;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\sort\Date;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sort handler for the newer of last comment / entity updated.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("comment_ces_last_updated")
 */
class StatisticsLastUpdated extends Date {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  public function query() {
    $this->ensureMyTable();

    $entity_type = $this->entityTypeManager
      ->getDefinition($this->getEntityType());

    if ($entity_type->isSubclassOf('\Drupal\Core\Entity\EntityChangedInterface')) {
      // @todo Find proper column name.
      $entity_data_table = $this->query->ensureTable($entity_type->getDataTable(), $this->relationship);
      $this->field_alias = $this->query->addOrderBy(NULL, "GREATEST(" . $entity_data_table . ".changed, " . $this->tableAlias . ".last_comment_timestamp)", $this->options['order'], $this->tableAlias . '_' . $this->field);
    }
    else {
      // No changed field on entity so using own table.
      $this->field_alias = $this->query->addOrderBy(NULL, $this->tableAlias . ".last_comment_timestamp", $this->options['order'], $this->tableAlias . '_' . $this->field);
    }
  }

}
