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
 *  @see Mws_FBAInboundServiceMWS_Interface
 */
require_once ('Mws/FBAInboundServiceMWS/Interface.php');

/**

 */
class Mws_FBAInboundServiceMWS_Mock implements Mws_FBAInboundServiceMWS_Interface
{
    // Public API ------------------------------------------------------------//


    /**
     * Create Inbound Shipment Plan
     * Plans inbound shipments for a set of items.  Registers identifiers if needed,
     * and assigns ShipmentIds for planned shipments.
     * When all the items are not all in the same category (e.g. some sortable, some
     * non-sortable) it may be necessary to create multiple shipments (one for each
     * of the shipment groups returned).
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_CreateInboundShipmentPlan request or FBAInboundServiceMWS_Model_CreateInboundShipmentPlan object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlan
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipmentPlan($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentPlanResponse.php');
        return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse::fromXML($this->_invoke('CreateInboundShipmentPlan'));
    }



    /**
     * Get Service Status
     * Gets the status of the service.
     * Status is one of GREEN, RED representing:
     * GREEN: This API section of the service is operating normally.
     * RED: The service is disrupted.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_GetServiceStatus request or FBAInboundServiceMWS_Model_GetServiceStatus object itself
     * @see Mws_FBAInboundServiceMWS_Model_GetServiceStatus
     * @return Mws_FBAInboundServiceMWS_Model_GetServiceStatusResponse FBAInboundServiceMWS_Model_GetServiceStatusResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function getServiceStatus($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/GetServiceStatusResponse.php');
        return Mws_FBAInboundServiceMWS_Model_GetServiceStatusResponse::fromXML($this->_invoke('GetServiceStatus'));
    }



    /**
     * List Inbound Shipments
     * Get the first set of inbound shipments created by a Seller according to
     * the specified shipment status or the specified shipment Id. A NextToken
     * is also returned to further iterate through the Seller's remaining
     * shipments. If a NextToken is not returned, it indicates the
     * end-of-data.
     * At least one of ShipmentStatusList and ShipmentIdList must be passed in.
     * if both are passed in, then only shipments that match the specified
     * shipment Id and specified shipment status will be returned.
     * the LastUpdatedBefore and LastUpdatedAfter are optional, they are used
     * to filter results based on last update time of the shipment.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipments request or FBAInboundServiceMWS_Model_ListInboundShipments object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipments
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResponse FBAInboundServiceMWS_Model_ListInboundShipmentsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipments($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResponse::fromXML($this->_invoke('ListInboundShipments'));
    }



    /**
     * List Inbound Shipments By Next Token
     * Gets the next set of inbound shipments created by a Seller with the
     * NextToken which can be used to iterate through the remaining inbound
     * shipments. There is a special token value to indicate end-of-data.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentsByNextToken request or FBAInboundServiceMWS_Model_ListInboundShipmentsByNextToken object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextToken
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentsByNextToken($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsByNextTokenResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse::fromXML($this->_invoke('ListInboundShipmentsByNextToken'));
    }



    /**
     * Update Inbound Shipment
     * Updates an pre-existing inbound shipment specified by the
     * ShipmentId. It may include up to 200 items.
     * If InboundShipmentHeader is set. it replaces the header information
     * for the given shipment.
     * If InboundShipmentItems is set. it adds, replaces and removes
     * the line time to inbound shipment.
     * For non-existing item, it will add the item for new line item;
     * For existing line items, it will replace the QuantityShipped for the item.
     * For QuantityShipped = 0, it indicates the item should be removed from the shipment
     * This operation will simply return a shipment Id upon success,
     * otherwise an explicit error will be returned.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_UpdateInboundShipment request or FBAInboundServiceMWS_Model_UpdateInboundShipment object itself
     * @see Mws_FBAInboundServiceMWS_Model_UpdateInboundShipment
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function updateInboundShipment($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/UpdateInboundShipmentResponse.php');
        return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse::fromXML($this->_invoke('UpdateInboundShipment'));
    }



    /**
     * Create Inbound Shipment
     * Creates an inbound shipment. It may include up to 200 items.
     * The initial status of a shipment will be set to 'Working'.
     * This operation will simply return a shipment Id upon success,
     * otherwise an explicit error will be returned.
     * More items may be added using the Update call.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_CreateInboundShipment request or FBAInboundServiceMWS_Model_CreateInboundShipment object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipment
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipment($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentResponse.php');
        return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse::fromXML($this->_invoke('CreateInboundShipment'));
    }



    /**
     * List Inbound Shipment Items By Next Token
     * Gets the next set of inbound shipment items with the NextToken
     * which can be used to iterate through the remaining inbound shipment
     * items. If a NextToken is not returned, it indicates the
     * end-of-data. You must first call ListInboundShipmentItems to get
     * a 'NextToken'.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextToken request or FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextToken object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextToken
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItemsByNextToken($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsByNextTokenResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse::fromXML($this->_invoke('ListInboundShipmentItemsByNextToken'));
    }



    /**
     * List Inbound Shipment Items
     * Gets the first set of inbound shipment items for the given ShipmentId or
     * all inbound shipment items updated between the given date range.
     * A NextToken is also returned to further iterate through the Seller's
     * remaining inbound shipment items. To get the next set of inbound
     * shipment items, you must call ListInboundShipmentItemsByNextToken and
     * pass in the 'NextToken' this call returned. If a NextToken is not
     * returned, it indicates the end-of-data. Use LastUpdatedBefore
     * and LastUpdatedAfter to filter results based on last updated time.
     * Either the ShipmentId or a pair of LastUpdatedBefore and LastUpdatedAfter
     * must be passed in. if ShipmentId is set, the LastUpdatedBefore and
     * LastUpdatedAfter will be ignored.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItems request or FBAInboundServiceMWS_Model_ListInboundShipmentItems object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItems
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItems($request)
    {
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse::fromXML($this->_invoke('ListInboundShipmentItems'));
    }

    // Private API ------------------------------------------------------------//

    private function _invoke($actionName)
    {
        return $xml = file_get_contents('Mws/FBAInboundServiceMWS/Mock/' . $actionName . 'Response.xml', /** search include path */ TRUE);
    }
}
