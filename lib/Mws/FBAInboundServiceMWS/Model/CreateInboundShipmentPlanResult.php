<?php
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     FBAInboundServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/*******************************************************************************
 *
 *  FBA Inbound Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:52:55 UTC 2010
 *
 */

/**
 *  @see Mws_FBAInboundServiceMWS_Model
 */
require_once ('Mws/FBAInboundServiceMWS/Model.php');



/**
 * FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResult
 *
 * Properties:
 * <ul>
 *
 * <li>InboundShipmentPlans: FBAInboundServiceMWS_Model_InboundShipmentPlanList</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResult extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResult
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>InboundShipmentPlans: FBAInboundServiceMWS_Model_InboundShipmentPlanList</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'InboundShipmentPlans' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanList'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the InboundShipmentPlans.
     *
     * @return InboundShipmentPlanList InboundShipmentPlans
     */
    public function getInboundShipmentPlans()
    {
        return $this->_fields['InboundShipmentPlans']['FieldValue'];
    }

    /**
     * Sets the value of the InboundShipmentPlans.
     *
     * @param InboundShipmentPlanList InboundShipmentPlans
     * @return void
     */
    public function setInboundShipmentPlans($value)
    {
        $this->_fields['InboundShipmentPlans']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the InboundShipmentPlans  and returns this instance
     *
     * @param InboundShipmentPlanList $value InboundShipmentPlans
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResult instance
     */
    public function withInboundShipmentPlans($value)
    {
        $this->setInboundShipmentPlans($value);
        return $this;
    }


    /**
     * Checks if InboundShipmentPlans  is set
     *
     * @return bool true if InboundShipmentPlans property is set
     */
    public function isSetInboundShipmentPlans()
    {
        return !is_null($this->_fields['InboundShipmentPlans']['FieldValue']);

    }




}
