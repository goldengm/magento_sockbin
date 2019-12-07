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
 * FBAOutboundServiceMWS_Model_TrackingAddress
 *
 * Properties:
 * <ul>
 *
 * <li>City: string</li>
 * <li>State: string</li>
 * <li>Country: string</li>
 *
 * </ul>
 */
class Mws_FBAOutboundServiceMWS_Model_TrackingAddress extends Mws_FBAOutboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAOutboundServiceMWS_Model_TrackingAddress
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>City: string</li>
     * <li>State: string</li>
     * <li>Country: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'City' => array('FieldValue' => null, 'FieldType' => 'string'),
        'State' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Country' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the City property.
     *
     * @return string City
     */
    public function getCity()
    {
        return $this->_fields['City']['FieldValue'];
    }

    /**
     * Sets the value of the City property.
     *
     * @param string City
     * @return this instance
     */
    public function setCity($value)
    {
        $this->_fields['City']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the City and returns this instance
     *
     * @param string $value City
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingAddress instance
     */
    public function withCity($value)
    {
        $this->setCity($value);
        return $this;
    }


    /**
     * Checks if City is set
     *
     * @return bool true if City  is set
     */
    public function isSetCity()
    {
        return !is_null($this->_fields['City']['FieldValue']);
    }

    /**
     * Gets the value of the State property.
     *
     * @return string State
     */
    public function getState()
    {
        return $this->_fields['State']['FieldValue'];
    }

    /**
     * Sets the value of the State property.
     *
     * @param string State
     * @return this instance
     */
    public function setState($value)
    {
        $this->_fields['State']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the State and returns this instance
     *
     * @param string $value State
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingAddress instance
     */
    public function withState($value)
    {
        $this->setState($value);
        return $this;
    }


    /**
     * Checks if State is set
     *
     * @return bool true if State  is set
     */
    public function isSetState()
    {
        return !is_null($this->_fields['State']['FieldValue']);
    }

    /**
     * Gets the value of the Country property.
     *
     * @return string Country
     */
    public function getCountry()
    {
        return $this->_fields['Country']['FieldValue'];
    }

    /**
     * Sets the value of the Country property.
     *
     * @param string Country
     * @return this instance
     */
    public function setCountry($value)
    {
        $this->_fields['Country']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Country and returns this instance
     *
     * @param string $value Country
     * @return Mws_FBAOutboundServiceMWS_Model_TrackingAddress instance
     */
    public function withCountry($value)
    {
        $this->setCountry($value);
        return $this;
    }


    /**
     * Checks if Country is set
     *
     * @return bool true if Country  is set
     */
    public function isSetCountry()
    {
        return !is_null($this->_fields['Country']['FieldValue']);
    }




}
