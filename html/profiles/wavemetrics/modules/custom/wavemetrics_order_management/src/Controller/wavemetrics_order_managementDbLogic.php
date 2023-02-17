<?php

namespace Drupal\wavemetrics_order_management\Controller;

use Drupal\Core\Database\Connection;

/**
 * Defines a storage handler class that handles the node grants system.
 *
 * This is used to build node query access.
 *
 * @ingroup wavemetrics_order_management
 */
class wavemetrics_order_managementDbLogic {

    /**
     * The database connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    protected $database;

    /**
     * Constructs a wavemetrics_order_managementDbLogic object.
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    // The $database variable came to us from the service argument.
    public function __construct(Connection $database) {
        $this->database = $database;
    }

    /**
     * Add new record in table wavemetrics_order_management.
     */
    public function add($orderid, $email, $name, $amountcents, $address1, $city, $state, $zip, $country, $phone, $ordertime, $stripecustomerid, $orderitems, $comment = "", $ordertype, $confirmation,$institution,$department,$degree,$urlhelper,$request,$course,$serials,$source,$position,$activation) {
        $query = $this->database->insert('weborders');
        $query->fields(array(
            'orderid' => $orderid,
            'email' => $email,
            'name' => $name,
            'amountcents' => $amountcents,
            'address1' => $address1,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
            'phone' => $phone,
            'ordertime' => $ordertime,
            'stripecustomerid' => $stripecustomerid,
            'orderitems' => $orderitems,
            'comment' => $comment,
            'ordertype' => $ordertype,
            'confirmation' => $confirmation,
            'institution' => $institution,
            'department' => $department,
            'degree' => $degree,
            'urlhelper' => $urlhelper,
            'request' => $request,
            'course' => $course,
            'serials' => $serials,
            'source' => $source,
            'position' => $position,
            'activation' => $activation,
        ));
        return $query->execute();
    }

  /**
   * Add new record in table wavemetrics_order_management.
   */
  public function updateField($orderid, array $fields) {
    $query = $this->database->update('weborders');
    $query->fields($fields);
    $query->condition('orderid',$orderid);
    return $query->execute();
  }

    /**
     * Get all records from table wavemetrics_order_management.
     */
    public function getAll() {
        return $this->getById();
    }

    /**
     * Get records by id from table wavemetrics_order_management.
     */
    public function getById($id = NULL, $reset = FALSE) {
        $query = $this->database->select('weborders');
        $query->fields('weborders', array('id', 'email', 'name'));
        if ($id) {
            $query->condition('id', $id);
        }
        $result = $query->execute()->fetchAll();
        if (count($result)) {
            if ($reset) {
                $result = reset($result);
            }
            return $result;
        }
        return FALSE;
    }

    /**
     * Get records by Entry ID that comes from WaveMetric's table
     */
    public function getByEntryID($id, $reset = FALSE) {
        $query = $this->database->select('weborders');
        $query->fields('weborders', array('id', 'email', 'name'));
        $query->condition('entryid', $id);
        $result = $query->execute()->fetchAll();
        if (count($result)) {
            if ($reset) {
                $result = reset($result);
            }
            return $result;
        }
        return FALSE;
    }

  /**
   * Get records by Order ID and Price.
   * This is primarily used as a sanity check on a ajax response from stripe.
   * That is why we check both values.
   */
  public function getByOidPrice($order_id, $price,$reset = FALSE, $source=null) {
    $query = $this->database->select('weborders');
    $query->fields('weborders', array('entryid', 'email', 'name','amountcents','orderid'));
    $query->condition('orderid', $order_id);
    $query->condition('amountcents', $price);
    if(!is_null($source)){
      $query->condition('source', $source);
    }
    $result = $query->execute()->fetchAll();
    if (count($result)) {
      if ($reset) {
        $result = reset($result);
      }
      return $result;
    }
    return FALSE;
  }

}
