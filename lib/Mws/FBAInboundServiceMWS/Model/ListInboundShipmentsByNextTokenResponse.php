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
 * FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
 *
 * Properties:
 * <ul>
 *
 * <li>ListInboundShipmentsByNextTokenResult: FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResult</li>
 * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ListInboundShipmentsByNextTokenResult: FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResult</li>
     * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ListInboundShipmentsByNextTokenResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/');
        $response = $xpath->query('//a:ListInboundShipmentsByNextTokenResponse');
        if ($response->length == 1) {
            return new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse from provided XML.
                                  Make sure that ListInboundShipmentsByNextTokenResponse is a root element");
        }

    }

    /**
     * Gets the value of the ListInboundShipmentsByNextTokenResult.
     *
     * @return ListInboundShipmentsByNextTokenResult ListInboundShipmentsByNextTokenResult
     */
    public function getListInboundShipmentsByNextTokenResult()
    {
        return $this->_fields['ListInboundShipmentsByNextTokenResult']['FieldValue'];
    }

    /**
     * Sets the value of the ListInboundShipmentsByNextTokenResult.
     *
     * @param ListInboundShipmentsByNextTokenResult ListInboundShipmentsByNextTokenResult
     * @return void
     */
    public function setListInboundShipmentsByNextTokenResult($value)
    {
        $this->_fields['ListInboundShipmentsByNextTokenResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ListInboundShipmentsByNextTokenResult  and returns this instance
     *
     * @param ListInboundShipmentsByNextTokenResult $value ListInboundShipmentsByNextTokenResult
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse instance
     */
    public function withListInboundShipmentsByNextTokenResult($value)
    {
        $this->setListInboundShipmentsByNextTokenResult($value);
        return $this;
    }


    /**
     * Checks if ListInboundShipmentsByNextTokenResult  is set
     *
     * @return bool true if ListInboundShipmentsByNextTokenResult property is set
     */
    public function isSetListInboundShipmentsByNextTokenResult()
    {
        return !is_null($this->_fields['ListInboundShipmentsByNextTokenResult']['FieldValue']);

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
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse instance
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
        $xml .= "<ListInboundShipmentsByNextTokenResponse xmlns=\"http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</ListInboundShipmentsByNextTokenResponse>";
        return $xml;
    }

}
