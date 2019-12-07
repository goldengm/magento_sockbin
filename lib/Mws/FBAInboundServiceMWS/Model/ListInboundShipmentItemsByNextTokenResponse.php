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
 * FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
 *
 * Properties:
 * <ul>
 *
 * <li>ListInboundShipmentItemsByNextTokenResult: FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResult</li>
 * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ListInboundShipmentItemsByNextTokenResult: FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResult</li>
     * <li>ResponseMetadata: FBAInboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ListInboundShipmentItemsByNextTokenResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/');
        $response = $xpath->query('//a:ListInboundShipmentItemsByNextTokenResponse');
        if ($response->length == 1) {
            return new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse from provided XML.
                                  Make sure that ListInboundShipmentItemsByNextTokenResponse is a root element");
        }

    }

    /**
     * Gets the value of the ListInboundShipmentItemsByNextTokenResult.
     *
     * @return ListInboundShipmentItemsByNextTokenResult ListInboundShipmentItemsByNextTokenResult
     */
    public function getListInboundShipmentItemsByNextTokenResult()
    {
        return $this->_fields['ListInboundShipmentItemsByNextTokenResult']['FieldValue'];
    }

    /**
     * Sets the value of the ListInboundShipmentItemsByNextTokenResult.
     *
     * @param ListInboundShipmentItemsByNextTokenResult ListInboundShipmentItemsByNextTokenResult
     * @return void
     */
    public function setListInboundShipmentItemsByNextTokenResult($value)
    {
        $this->_fields['ListInboundShipmentItemsByNextTokenResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ListInboundShipmentItemsByNextTokenResult  and returns this instance
     *
     * @param ListInboundShipmentItemsByNextTokenResult $value ListInboundShipmentItemsByNextTokenResult
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse instance
     */
    public function withListInboundShipmentItemsByNextTokenResult($value)
    {
        $this->setListInboundShipmentItemsByNextTokenResult($value);
        return $this;
    }


    /**
     * Checks if ListInboundShipmentItemsByNextTokenResult  is set
     *
     * @return bool true if ListInboundShipmentItemsByNextTokenResult property is set
     */
    public function isSetListInboundShipmentItemsByNextTokenResult()
    {
        return !is_null($this->_fields['ListInboundShipmentItemsByNextTokenResult']['FieldValue']);

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
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse instance
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
        $xml .= "<ListInboundShipmentItemsByNextTokenResponse xmlns=\"http://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</ListInboundShipmentItemsByNextTokenResponse>";
        return $xml;
    }

}
