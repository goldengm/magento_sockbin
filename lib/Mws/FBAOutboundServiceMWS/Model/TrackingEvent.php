<?php
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     FBAOutboundServiceMWS
 *  @copyright   Copyright 2008-2009 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/*******************************************************************************
 *
 *  FBA Outbound Service MWS PHP5 Library
 *  Generated: Sun Apr 22 23:42:10 GMT 2012
 *
 */

/**
 *  @see Mws_FBAOutboundServiceMWS_Model
 */
require_once ('Mws/FBAOutboundServiceMWS/Model.php');



/**
 * FBAOutboundServiceMWS_Model_TrackingEvent
 *
 * Properties:
 * <ul>
 *
 * <li>EventDate: string</li>
 * <li>EventAddress: FBAOutboundServiceMWS_Model_TrackingAddress</li>
 * <li>EventCode: string</li>
 *
 * </ul>
 */
class Mws_FBAOutboundServiceMWS_Model_TrackingEvent extends Mws_FBAOutboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAOutboundServiceMWS_Model_TrackingEvent
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>EventDate: string</li>
     * <li>EventAddress: FBAOutboundServiceMWS_Model_TrackingAddress</li>
     * <li>EventCode: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'EventDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'EventAddress' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_TrackingAddress'),
        'EventCode' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the EventDate property.
     *
     * @return string EventDate
     */
    public function getEventDate()
    {
        return $this->_fields['EventDate']['FieldValue'];
    }

    /**
     * Sets the value of the EventDate property.
     *
     * @param string EventDate
     * @return this instance
     */
    public function setEventDate($value)
    {
        $this->_fields['EventDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the EventDate and returns this instance
     *
     * @param string $value EventDate
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingEvent instance
     */
    public function withEventDate($value)
    {
        $this->setEventDate($value);
        return $this;
    }


    /**
     * Checks if EventDate is set
     *
     * @return bool true if EventDate  is set
     */
    public function isSetEventDate()
    {
        return !is_null($this->_fields['EventDate']['FieldValue']);
    }

    /**
     * Gets the value of the EventAddress.
     *
     * @return TrackingAddress EventAddress
     */
    public function getEventAddress()
    {
        return $this->_fields['EventAddress']['FieldValue'];
    }

    /**
     * Sets the value of the EventAddress.
     *
     * @param TrackingAddress EventAddress
     * @return void
     */
    public function setEventAddress($value)
    {
        $this->_fields['EventAddress']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the EventAddress  and returns this instance
     *
     * @param TrackingAddress $value EventAddress
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingEvent instance
     */
    public function withEventAddress($value)
    {
        $this->setEventAddress($value);
        return $this;
    }


    /**
     * Checks if EventAddress  is set
     *
     * @return bool true if EventAddress property is set
     */
    public function isSetEventAddress()
    {
        return !is_null($this->_fields['EventAddress']['FieldValue']);

    }

    /**
     * Gets the value of the EventCode property.
     *
     * @return string EventCode
     */
    public function getEventCode()
    {
        return $this->_fields['EventCode']['FieldValue'];
    }

    /**
     * Sets the value of the EventCode property.
     *
     * @param string EventCode
     * @return this instance
     */
    public function setEventCode($value)
    {
        $this->_fields['EventCode']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the EventCode and returns this instance
     *
     * @param string $value EventCode
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingEvent instance
     */
    public function withEventCode($value)
    {
        $this->setEventCode($value);
        return $this;
    }


    /**
     * Checks if EventCode is set
     *
     * @return bool true if EventCode  is set
     */
    public function isSetEventCode()
    {
        return !is_null($this->_fields['EventCode']['FieldValue']);
    }




}
