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
 * FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult
 *
 * Properties:
 * <ul>
 *
 * <li>PackageNumber: int</li>
 * <li>TrackingNumber: string</li>
 * <li>CarrierCode: string</li>
 * <li>CarrierPhoneNumber: string</li>
 * <li>CarrierURL: string</li>
 * <li>ShipDate: string</li>
 * <li>EstimatedArrivalDate: string</li>
 * <li>ShipToAddress: FBAOutboundServiceMWS_Model_TrackingAddress</li>
 * <li>CurrentStatus: string</li>
 * <li>SignedForBy: string</li>
 * <li>AdditionalLocationInfo: string</li>
 * <li>TrackingEvents: FBAOutboundServiceMWS_Model_TrackingEventList</li>
 *
 * </ul>
 */
class Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult extends Mws_FBAOutboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>PackageNumber: int</li>
     * <li>TrackingNumber: string</li>
     * <li>CarrierCode: string</li>
     * <li>CarrierPhoneNumber: string</li>
     * <li>CarrierURL: string</li>
     * <li>ShipDate: string</li>
     * <li>EstimatedArrivalDate: string</li>
     * <li>ShipToAddress: FBAOutboundServiceMWS_Model_TrackingAddress</li>
     * <li>CurrentStatus: string</li>
     * <li>SignedForBy: string</li>
     * <li>AdditionalLocationInfo: string</li>
     * <li>TrackingEvents: FBAOutboundServiceMWS_Model_TrackingEventList</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'PackageNumber' => array('FieldValue' => null, 'FieldType' => 'int'),
        'TrackingNumber' => array('FieldValue' => null, 'FieldType' => 'string'),
        'CarrierCode' => array('FieldValue' => null, 'FieldType' => 'string'),
        'CarrierPhoneNumber' => array('FieldValue' => null, 'FieldType' => 'string'),
        'CarrierURL' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'EstimatedArrivalDate' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipToAddress' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_TrackingAddress'),
        'CurrentStatus' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SignedForBy' => array('FieldValue' => null, 'FieldType' => 'string'),
        'AdditionalLocationInfo' => array('FieldValue' => null, 'FieldType' => 'string'),
        'TrackingEvents' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAOutboundServiceMWS_Model_TrackingEventList'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the PackageNumber property.
     *
     * @return int PackageNumber
     */
    public function getPackageNumber()
    {
        return $this->_fields['PackageNumber']['FieldValue'];
    }

    /**
     * Sets the value of the PackageNumber property.
     *
     * @param int PackageNumber
     * @return this instance
     */
    public function setPackageNumber($value)
    {
        $this->_fields['PackageNumber']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the PackageNumber and returns this instance
     *
     * @param int $value PackageNumber
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withPackageNumber($value)
    {
        $this->setPackageNumber($value);
        return $this;
    }


    /**
     * Checks if PackageNumber is set
     *
     * @return bool true if PackageNumber  is set
     */
    public function isSetPackageNumber()
    {
        return !is_null($this->_fields['PackageNumber']['FieldValue']);
    }

    /**
     * Gets the value of the TrackingNumber property.
     *
     * @return string TrackingNumber
     */
    public function getTrackingNumber()
    {
        return $this->_fields['TrackingNumber']['FieldValue'];
    }

    /**
     * Sets the value of the TrackingNumber property.
     *
     * @param string TrackingNumber
     * @return this instance
     */
    public function setTrackingNumber($value)
    {
        $this->_fields['TrackingNumber']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the TrackingNumber and returns this instance
     *
     * @param string $value TrackingNumber
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withTrackingNumber($value)
    {
        $this->setTrackingNumber($value);
        return $this;
    }


    /**
     * Checks if TrackingNumber is set
     *
     * @return bool true if TrackingNumber  is set
     */
    public function isSetTrackingNumber()
    {
        return !is_null($this->_fields['TrackingNumber']['FieldValue']);
    }

    /**
     * Gets the value of the CarrierCode property.
     *
     * @return string CarrierCode
     */
    public function getCarrierCode()
    {
        return $this->_fields['CarrierCode']['FieldValue'];
    }

    /**
     * Sets the value of the CarrierCode property.
     *
     * @param string CarrierCode
     * @return this instance
     */
    public function setCarrierCode($value)
    {
        $this->_fields['CarrierCode']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the CarrierCode and returns this instance
     *
     * @param string $value CarrierCode
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withCarrierCode($value)
    {
        $this->setCarrierCode($value);
        return $this;
    }


    /**
     * Checks if CarrierCode is set
     *
     * @return bool true if CarrierCode  is set
     */
    public function isSetCarrierCode()
    {
        return !is_null($this->_fields['CarrierCode']['FieldValue']);
    }

    /**
     * Gets the value of the CarrierPhoneNumber property.
     *
     * @return string CarrierPhoneNumber
     */
    public function getCarrierPhoneNumber()
    {
        return $this->_fields['CarrierPhoneNumber']['FieldValue'];
    }

    /**
     * Sets the value of the CarrierPhoneNumber property.
     *
     * @param string CarrierPhoneNumber
     * @return this instance
     */
    public function setCarrierPhoneNumber($value)
    {
        $this->_fields['CarrierPhoneNumber']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the CarrierPhoneNumber and returns this instance
     *
     * @param string $value CarrierPhoneNumber
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withCarrierPhoneNumber($value)
    {
        $this->setCarrierPhoneNumber($value);
        return $this;
    }


    /**
     * Checks if CarrierPhoneNumber is set
     *
     * @return bool true if CarrierPhoneNumber  is set
     */
    public function isSetCarrierPhoneNumber()
    {
        return !is_null($this->_fields['CarrierPhoneNumber']['FieldValue']);
    }

    /**
     * Gets the value of the CarrierURL property.
     *
     * @return string CarrierURL
     */
    public function getCarrierURL()
    {
        return $this->_fields['CarrierURL']['FieldValue'];
    }

    /**
     * Sets the value of the CarrierURL property.
     *
     * @param string CarrierURL
     * @return this instance
     */
    public function setCarrierURL($value)
    {
        $this->_fields['CarrierURL']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the CarrierURL and returns this instance
     *
     * @param string $value CarrierURL
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withCarrierURL($value)
    {
        $this->setCarrierURL($value);
        return $this;
    }


    /**
     * Checks if CarrierURL is set
     *
     * @return bool true if CarrierURL  is set
     */
    public function isSetCarrierURL()
    {
        return !is_null($this->_fields['CarrierURL']['FieldValue']);
    }

    /**
     * Gets the value of the ShipDate property.
     *
     * @return string ShipDate
     */
    public function getShipDate()
    {
        return $this->_fields['ShipDate']['FieldValue'];
    }

    /**
     * Sets the value of the ShipDate property.
     *
     * @param string ShipDate
     * @return this instance
     */
    public function setShipDate($value)
    {
        $this->_fields['ShipDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ShipDate and returns this instance
     *
     * @param string $value ShipDate
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withShipDate($value)
    {
        $this->setShipDate($value);
        return $this;
    }


    /**
     * Checks if ShipDate is set
     *
     * @return bool true if ShipDate  is set
     */
    public function isSetShipDate()
    {
        return !is_null($this->_fields['ShipDate']['FieldValue']);
    }

    /**
     * Gets the value of the EstimatedArrivalDate property.
     *
     * @return string EstimatedArrivalDate
     */
    public function getEstimatedArrivalDate()
    {
        return $this->_fields['EstimatedArrivalDate']['FieldValue'];
    }

    /**
     * Sets the value of the EstimatedArrivalDate property.
     *
     * @param string EstimatedArrivalDate
     * @return this instance
     */
    public function setEstimatedArrivalDate($value)
    {
        $this->_fields['EstimatedArrivalDate']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the EstimatedArrivalDate and returns this instance
     *
     * @param string $value EstimatedArrivalDate
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withEstimatedArrivalDate($value)
    {
        $this->setEstimatedArrivalDate($value);
        return $this;
    }


    /**
     * Checks if EstimatedArrivalDate is set
     *
     * @return bool true if EstimatedArrivalDate  is set
     */
    public function isSetEstimatedArrivalDate()
    {
        return !is_null($this->_fields['EstimatedArrivalDate']['FieldValue']);
    }

    /**
     * Gets the value of the ShipToAddress.
     *
     * @return TrackingAddress ShipToAddress
     */
    public function getShipToAddress()
    {
        return $this->_fields['ShipToAddress']['FieldValue'];
    }

    /**
     * Sets the value of the ShipToAddress.
     *
     * @param TrackingAddress ShipToAddress
     * @return void
     */
    public function setShipToAddress($value)
    {
        $this->_fields['ShipToAddress']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ShipToAddress  and returns this instance
     *
     * @param TrackingAddress $value ShipToAddress
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withShipToAddress($value)
    {
        $this->setShipToAddress($value);
        return $this;
    }


    /**
     * Checks if ShipToAddress  is set
     *
     * @return bool true if ShipToAddress property is set
     */
    public function isSetShipToAddress()
    {
        return !is_null($this->_fields['ShipToAddress']['FieldValue']);

    }

    /**
     * Gets the value of the CurrentStatus property.
     *
     * @return string CurrentStatus
     */
    public function getCurrentStatus()
    {
        return $this->_fields['CurrentStatus']['FieldValue'];
    }

    /**
     * Sets the value of the CurrentStatus property.
     *
     * @param string CurrentStatus
     * @return this instance
     */
    public function setCurrentStatus($value)
    {
        $this->_fields['CurrentStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the CurrentStatus and returns this instance
     *
     * @param string $value CurrentStatus
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withCurrentStatus($value)
    {
        $this->setCurrentStatus($value);
        return $this;
    }


    /**
     * Checks if CurrentStatus is set
     *
     * @return bool true if CurrentStatus  is set
     */
    public function isSetCurrentStatus()
    {
        return !is_null($this->_fields['CurrentStatus']['FieldValue']);
    }

    /**
     * Gets the value of the SignedForBy property.
     *
     * @return string SignedForBy
     */
    public function getSignedForBy()
    {
        return $this->_fields['SignedForBy']['FieldValue'];
    }

    /**
     * Sets the value of the SignedForBy property.
     *
     * @param string SignedForBy
     * @return this instance
     */
    public function setSignedForBy($value)
    {
        $this->_fields['SignedForBy']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SignedForBy and returns this instance
     *
     * @param string $value SignedForBy
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withSignedForBy($value)
    {
        $this->setSignedForBy($value);
        return $this;
    }


    /**
     * Checks if SignedForBy is set
     *
     * @return bool true if SignedForBy  is set
     */
    public function isSetSignedForBy()
    {
        return !is_null($this->_fields['SignedForBy']['FieldValue']);
    }

    /**
     * Gets the value of the AdditionalLocationInfo property.
     *
     * @return string AdditionalLocationInfo
     */
    public function getAdditionalLocationInfo()
    {
        return $this->_fields['AdditionalLocationInfo']['FieldValue'];
    }

    /**
     * Sets the value of the AdditionalLocationInfo property.
     *
     * @param string AdditionalLocationInfo
     * @return this instance
     */
    public function setAdditionalLocationInfo($value)
    {
        $this->_fields['AdditionalLocationInfo']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the AdditionalLocationInfo and returns this instance
     *
     * @param string $value AdditionalLocationInfo
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withAdditionalLocationInfo($value)
    {
        $this->setAdditionalLocationInfo($value);
        return $this;
    }


    /**
     * Checks if AdditionalLocationInfo is set
     *
     * @return bool true if AdditionalLocationInfo  is set
     */
    public function isSetAdditionalLocationInfo()
    {
        return !is_null($this->_fields['AdditionalLocationInfo']['FieldValue']);
    }

    /**
     * Gets the value of the TrackingEvents.
     *
     * @return TrackingEventList TrackingEvents
     */
    public function getTrackingEvents()
    {
        return $this->_fields['TrackingEvents']['FieldValue'];
    }

    /**
     * Sets the value of the TrackingEvents.
     *
     * @param TrackingEventList TrackingEvents
     * @return void
     */
    public function setTrackingEvents($value)
    {
        $this->_fields['TrackingEvents']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the TrackingEvents  and returns this instance
     *
     * @param TrackingEventList $value TrackingEvents
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResult instance
     */
    public function withTrackingEvents($value)
    {
        $this->setTrackingEvents($value);
        return $this;
    }


    /**
     * Checks if TrackingEvents  is set
     *
     * @return bool true if TrackingEvents property is set
     */
    public function isSetTrackingEvents()
    {
        return !is_null($this->_fields['TrackingEvents']['FieldValue']);

    }




}
