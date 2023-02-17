<?php

namespace Drupal\comment\Plugin\views\field;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views\Plugin\views\field\Date;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to display the newer of last comment / node updated.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("comment_ces_last_updated")
 */
class StatisticsLastUpdated extends Date {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a StatisticsLastUpdated object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatter $date_formatter, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $date_formatter,
      $entity_type_manager->getStorage('date_format')
    );

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
      $container->get('date.formatter'),
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
      $this->field_alias = $this->query->addField(NULL, "GREATEST(" . $entity_data_table . ".changed, " . $this->tableAlias . ".last_comment_timestamp)", $this->tableAlias . '_' . $this->field);
    }
    else {
      // No changed field on entity so using own table.
      $this->field_alias = $this->query->addField(NULL, $this->tableAlias . ".last_comment_timestamp", $this->tableAlias . '_' . $this->field);
    }
  }

}
