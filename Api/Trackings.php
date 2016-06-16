<?php

namespace AfterShip\Tracking\Api;

use AfterShip\Tracking\Api\TrackingsInterface;
use \Magento\Framework\AppInterface;
use \Magento\Framework\App\Http;

class Trackings extends Http implements TrackingsInterface, AppInterface
{
	/**
	* Return the sum of the two numbers.
	*
	* @api
	* @param int $store
	* @param int $from
	* @param int $to
	* @param int $max
	* @return \Magento\Framework\Controller\Result\Json
	*/
	public function retrieve($store, $from, $to, $max) {
		$connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
		$db = $connection->getConnection();
		$db->getProfiler()->setEnabled(true);
		$select = $db->select();
		$select->from(array('address' => 'sales_order_address'), array('country_id', 'telephone', 'postcode'));
		$select->join(array('sorder' => 'sales_order'), 'address.parent_id = sorder.entity_id', array('entity_id', 'customer_note', 'customer_firstname', 'customer_middlename', 'customer_lastname', 'customer_email'));
		$select->join(array('track' => 'sales_shipment_track'), 'sorder.entity_id = track.parent_id', array('track_number', 'title', 'carrier_code', 'CONVERT_TZ(`track`.`created_at`, @@session.time_zone, \'+00:00\') AS track_created_at'));
		$select->join(array('item' => 'sales_order_item'), 'sorder.entity_id = item.order_id', array('GROUP_CONCAT(item.name SEPARATOR \'<>\') AS order_items'));
		$select->where("CONVERT_TZ(`track`.`created_at`, @@session.time_zone, '+00:00') between FROM_UNIXTIME($from) and FROM_UNIXTIME($to)");
		if ($store >= 0) {
			$select->where("sorder.store_id = $store");
		}
		$select->group('track.track_number');
		$select->order('track_created_at ASC');
		$select->limit($max, 0);
		$data = $db->fetchAll($select);
		return $data;
	}
}
