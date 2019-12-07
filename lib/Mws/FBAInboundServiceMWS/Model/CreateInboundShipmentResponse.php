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
 * FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
 *
 * Properties:
 * <ul>
 *
 * <li>CreateInboundShipmentResult: FBAInboundServiceMWS_Model_CreateInboundShipmentResult</li>
 * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>CreateInboundShipmentResult: FBAInboundServiceMWS_Model_CreateInboundShipmentResult</li>
     * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'CreateInboundShipmentResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAInboundServiceMWS_Model_CreateInboundShipmentResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/');
        $response = $xpath->query('//a:CreateInboundShipmentResponse');
        if ($response->length == 1) {
            return new Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse from provided XML.
                                  Make sure that CreateInboundShipmentResponse is a root element");
        }

    }

    /**
     * Gets the value of the CreateInboundShipmentResult.
     *
     * @return CreateInboundShipmentResult CreateInboundShipmentResult
     */
    public function getCreateInboundShipmentResult()
    {
        return $this->_fields['CreateInboundShipmentResult']['FieldValue'];
    }

    /**
     * Sets the value of the CreateInboundShipmentResult.
     *
     * @param CreateInboundShipmentResult CreateInboundShipmentResult
     * @return void
     */
    public function setCreateInboundShipmentResult($value)
    {
        $this->_fields['CreateInboundShipmentResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the CreateInboundShipmentResult  and returns this instance
     *
     * @param CreateInboundShipmentResult $value CreateInboundShipmentResult
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse instance
     */
    public function withCreateInboundShipmentResult($value)
    {
        $this->setCreateInboundShipmentResult($value);
        return $this;
    }


    /**
     * Checks if CreateInboundShipmentResult  is set
     *
     * @return bool true if CreateInboundShipmentResult property is set
     */
    public function isSetCreateInboundShipmentResult()
    {
        return !is_null($this->_fields['CreateInboundShipmentResult']['FieldValue']);

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
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse instance
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
        $xml .= "<CreateInboundShipmentResponse xmlns=\"http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</CreateInboundShipmentResponse>";
        return $xml;
    }

}
