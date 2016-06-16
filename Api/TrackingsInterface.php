<?php

namespace AfterShip\Tracking\Api;

interface TrackingsInterface
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
	public function retrieve($store, $from, $to, $max);
}
