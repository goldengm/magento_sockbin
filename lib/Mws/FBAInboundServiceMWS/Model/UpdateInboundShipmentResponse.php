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
 * FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
 *
 * Properties:
 * <ul>
 *
 * <li>UpdateInboundShipmentResult: FBAInboundServiceMWS_Model_UpdateInboundShipmentResult</li>
 * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>UpdateInboundShipmentResult: FBAInboundServiceMWS_Model_UpdateInboundShipmentResult</li>
     * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'UpdateInboundShipmentResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/');
        $response = $xpath->query('//a:UpdateInboundShipmentResponse');
        if ($response->length == 1) {
            return new Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse from provided XML.
                                  Make sure that UpdateInboundShipmentResponse is a root element");
        }

    }

    /**
     * Gets the value of the UpdateInboundShipmentResult.
     *
     * @return UpdateInboundShipmentResult UpdateInboundShipmentResult
     */
    public function getUpdateInboundShipmentResult()
    {
        return $this->_fields['UpdateInboundShipmentResult']['FieldValue'];
    }

    /**
     * Sets the value of the UpdateInboundShipmentResult.
     *
     * @param UpdateInboundShipmentResult UpdateInboundShipmentResult
     * @return void
     */
    public function setUpdateInboundShipmentResult($value)
    {
        $this->_fields['UpdateInboundShipmentResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the UpdateInboundShipmentResult  and returns this instance
     *
     * @param UpdateInboundShipmentResult $value UpdateInboundShipmentResult
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse instance
     */
    public function withUpdateInboundShipmentResult($value)
    {
        $this->setUpdateInboundShipmentResult($value);
        return $this;
    }


    /**
     * Checks if UpdateInboundShipmentResult  is set
     *
     * @return bool true if UpdateInboundShipmentResult property is set
     */
    public function isSetUpdateInboundShipmentResult()
    {
        return !is_null($this->_fields['UpdateInboundShipmentResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     *
     * @return ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata()
    {
        return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     *
     * @param ResponseMetadata ResponseMetadata
     * @return void
     */
    public function setResponseMetadata($value)
    {
        $this->_fields['ResponseMetadata']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ResponseMetadata  and returns this instance
     *
     * @param ResponseMetadata $value ResponseMetadata
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse instance
     */
    public function withResponseMetadata($value)
    {
        $this->setResponseMetadata($value);
        return $this;
    }


    /**
     * Checks if ResponseMetadata  is set
     *
     * @return bool true if ResponseMetadata property is set
     */
    public function isSetResponseMetadata()
    {
        return !is_null($this->_fields['ResponseMetadata']['FieldValue']);

    }



    /**
     * XML Representation for this object
     *
     * @return string XML for this object
     */
    public function toXML()
    {
        $xml = "";
        $xml .= "<UpdateInboundShipmentResponse xmlns=\"http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</UpdateInboundShipmentResponse>";
        return $xml;
    }

}
