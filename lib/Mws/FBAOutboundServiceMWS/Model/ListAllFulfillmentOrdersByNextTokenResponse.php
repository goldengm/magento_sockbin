<?php
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     FBAOutboundServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/*******************************************************************************
 *
 *  FBA Outbound Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:51:48 UTC 2010
 *
 */

/**
 *  @see Mws_FBAOutboundServiceMWS_Model
 */
require_once ('Mws/FBAOutboundServiceMWS/Model.php');



/**
 * FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse
 *
 * Properties:
 * <ul>
 *
 * <li>ListAllFulfillmentOrdersByNextTokenResult: FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult</li>
 * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse extends Mws_FBAOutboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ListAllFulfillmentOrdersByNextTokenResult: FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult</li>
     * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ListAllFulfillmentOrdersByNextTokenResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/');
        $response = $xpath->query('//a:ListAllFulfillmentOrdersByNextTokenResponse');
        if ($response->length == 1) {
            return new Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse from provided XML.
                                  Make sure that ListAllFulfillmentOrdersByNextTokenResponse is a root element");
        }

    }

    /**
     * Gets the value of the ListAllFulfillmentOrdersByNextTokenResult.
     *
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult ListAllFulfillmentOrdersByNextTokenResult
     */
    public function getListAllFulfillmentOrdersByNextTokenResult()
    {
        return $this->_fields['ListAllFulfillmentOrdersByNextTokenResult']['FieldValue'];
    }

    /**
     * Sets the value of the ListAllFulfillmentOrdersByNextTokenResult.
     *
     * @param ListAllFulfillmentOrdersByNextTokenResult Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult
     * @return void
     */
    public function setListAllFulfillmentOrdersByNextTokenResult($value)
    {
        $this->_fields['ListAllFulfillmentOrdersByNextTokenResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ListAllFulfillmentOrdersByNextTokenResult  and returns this instance
     *
     * @param Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResult $value ListAllFulfillmentOrdersByNextTokenResult
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse instance
     */
    public function withListAllFulfillmentOrdersByNextTokenResult($value)
    {
        $this->setListAllFulfillmentOrdersByNextTokenResult($value);
        return $this;
    }


    /**
     * Checks if ListAllFulfillmentOrdersByNextTokenResult  is set
     *
     * @return bool true if ListAllFulfillmentOrdersByNextTokenResult property is set
     */
    public function isSetListAllFulfillmentOrdersByNextTokenResult()
    {
        return !is_null($this->_fields['ListAllFulfillmentOrdersByNextTokenResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     *
     * @return Mws_FBAOutboundServiceMWS_Model_ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata()
    {
        return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     *
     * @param ResponseMetadata Mws_FBAOutboundServiceMWS_Model_ResponseMetadata
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
     * @param Mws_FBAOutboundServiceMWS_Model_ResponseMetadata $value ResponseMetadata
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse instance
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
        $xml .= "<ListAllFulfillmentOrdersByNextTokenResponse xmlns=\"http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</ListAllFulfillmentOrdersByNextTokenResponse>";
        return $xml;
    }

}
