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
 * FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse
 *
 * Properties:
 * <ul>
 *
 * <li>GetPackageTrackingDetailsResult: FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult</li>
 * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
 *
 * </ul>
 */
class Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse extends Mws_FBAOutboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>GetPackageTrackingDetailsResult: FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult</li>
     * <li>ResponseMetadata: FBAOutboundServiceMWS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'GetPackageTrackingDetailsResult' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }


    /**
     * Construct FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse from XML string
     *
     * @param string $xml XML string to construct from
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/');
        $response = $xpath->query('//a:GetPackageTrackingDetailsResponse');
        if ($response->length == 1) {
            return new Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse(($response->item(0)));
        } else {
            throw new Exception ("Unable to construct Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse from provided XML.
                                  Make sure that GetPackageTrackingDetailsResponse is a root element");
        }

    }

    /**
     * Gets the value of the GetPackageTrackingDetailsResult.
     *
     * @return GetPackageTrackingDetailsResult GetPackageTrackingDetailsResult
     */
    public function getGetPackageTrackingDetailsResult()
    {
        return $this->_fields['GetPackageTrackingDetailsResult']['FieldValue'];
    }

    /**
     * Sets the value of the GetPackageTrackingDetailsResult.
     *
     * @param GetPackageTrackingDetailsResult GetPackageTrackingDetailsResult
     * @return void
     */
    public function setGetPackageTrackingDetailsResult($value)
    {
        $this->_fields['GetPackageTrackingDetailsResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the GetPackageTrackingDetailsResult  and returns this instance
     *
     * @param GetPackageTrackingDetailsResult $value GetPackageTrackingDetailsResult
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse instance
     */
    public function withGetPackageTrackingDetailsResult($value)
    {
        $this->setGetPackageTrackingDetailsResult($value);
        return $this;
    }


    /**
     * Checks if GetPackageTrackingDetailsResult  is set
     *
     * @return bool true if GetPackageTrackingDetailsResult property is set
     */
    public function isSetGetPackageTrackingDetailsResult()
    {
        return !is_null($this->_fields['GetPackageTrackingDetailsResult']['FieldValue']);

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
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse instance
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
        $xml .= "<GetPackageTrackingDetailsResponse xmlns=\"http://mws.amazonaws.com/FulfillmentOutboundShipment/2010-10-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</GetPackageTrackingDetailsResponse>";
        return $xml;
    }

}
