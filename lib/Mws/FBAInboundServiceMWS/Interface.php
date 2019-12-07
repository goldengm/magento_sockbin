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

 */

interface  Mws_FBAInboundServiceMWS_Interface
{



    /**
     * Create Inbound Shipment Plan
     * Plans inbound shipments for a set of items.  Registers identifiers if needed,
     * and assigns ShipmentIds for planned shipments.
     * When all the items are not all in the same category (e.g. some sortable, some
     * non-sortable) it may be necessary to create multiple shipments (one for each
     * of the shipment groups returned).
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest request
     * or Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipmentPlan($request);



    /**
     * Get Service Status
     * Gets the status of the service.
     * Status is one of GREEN, RED representing:
     * GREEN: This API section of the service is operating normally.
     * RED: The service is disrupted.
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_GetServiceStatusRequest request
     * or Mws_FBAInboundServiceMWS_Model_GetServiceStatusRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_GetServiceStatusRequest
     * @return Mws_FBAInboundServiceMWS_Model_GetServiceStatusResponse FBAInboundServiceMWS_Model_GetServiceStatusResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function getServiceStatus($request);



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
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsRequest request
     * or Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsRequest
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResponse FBAInboundServiceMWS_Model_ListInboundShipmentsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipments($request);



    /**
     * List Inbound Shipments By Next Token
     * Gets the next set of inbound shipments created by a Seller with the
     * NextToken which can be used to iterate through the remaining inbound
     * shipments. If a NextToken is not returned, it indicates the
     * end-of-data.
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest request
     * or Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentsByNextToken($request);



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
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest request
     * or Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function updateInboundShipment($request);



    /**
     * Create Inbound Shipment
     * Creates an inbound shipment. It may include up to 200 items.
     * The initial status of a shipment will be set to 'Working'.
     * This operation will simply return a shipment Id upon success,
     * otherwise an explicit error will be returned.
     * More items may be added using the Update call.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_CreateInboundShipmentRequest request
     * or FBAInboundServiceMWS_Model_CreateInboundShipmentRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentRequest
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipment($request);



    /**
     * List Inbound Shipment Items By Next Token
     * Gets the next set of inbound shipment items with the NextToken
     * which can be used to iterate through the remaining inbound shipment
     * items. If a NextToken is not returned, it indicates the
     * end-of-data. You must first call ListInboundShipmentItems to get
     * a 'NextToken'.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItemsByNextToken($request);



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
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItems($request);

}
