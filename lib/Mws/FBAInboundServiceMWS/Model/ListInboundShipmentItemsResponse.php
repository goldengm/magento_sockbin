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
 * FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
 *
 * Properties:
 * <ul>
 *
 * <li>ListInboundShipmentItemsResult: FBAInboundServiceMWS_Model_ListInboundShipmentItemsResult</li>
 * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ListInboundShipmentItemsResult: FBAInboundServiceMWS_Model_ListInboundShipmentItemsResult</li>
     * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ListInboundShipmentItemsResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/');
        $response = $xpath->query('//a:ListInboundShipmentItemsResponse');
        if ($response->length == 1) {
            return new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse from provided XML.
                                  Make sure that ListInboundShipmentItemsResponse is a root element");
        }

    }

    /**
     * Gets the value of the ListInboundShipmentItemsResult.
     *
     * @return ListInboundShipmentItemsResult ListInboundShipmentItemsResult
     */
    public function getListInboundShipmentItemsResult()
    {
        return $this->_fields['ListInboundShipmentItemsResult']['FieldValue'];
    }

    /**
     * Sets the value of the ListInboundShipmentItemsResult.
     *
     * @param ListInboundShipmentItemsResult ListInboundShipmentItemsResult
     * @return void
     */
    public function setListInboundShipmentItemsResult($value)
    {
        $this->_fields['ListInboundShipmentItemsResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ListInboundShipmentItemsResult  and returns this instance
     *
     * @param ListInboundShipmentItemsResult $value ListInboundShipmentItemsResult
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse instance
     */
    public function withListInboundShipmentItemsResult($value)
    {
        $this->setListInboundShipmentItemsResult($value);
        return $this;
    }


    /**
     * Checks if ListInboundShipmentItemsResult  is set
     *
     * @return bool true if ListInboundShipmentItemsResult property is set
     */
    public function isSetListInboundShipmentItemsResult()
    {
        return !is_null($this->_fields['ListInboundShipmentItemsResult']['FieldValue']);

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
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse instance
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
        $xml .= "<ListInboundShipmentItemsResponse xmlns=\"http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</ListInboundShipmentItemsResponse>";
        return $xml;
    }

}
