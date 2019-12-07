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
 * FBA Outbound Service MWS PHP5 Library
 *  Generated: Sun Apr 22 23:42:10 GMT 2012
 *
 */

/**
 *  @see Mws_FBAOutboundServiceMWS_Interface
 */
require_once ('Mws/FBAOutboundServiceMWS/Interface.php');

/**
 * Outbound fulfillment service
 *
 */
class Mws_FBAOutboundServiceMWS_Mock implements Mws_FBAOutboundServiceMWS_Interface
{
    // Public API ------------------------------------------------------------//


    /**
     * Get Package Tracking Details
     * Gets the tracking details for a shipment package.
     *
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_GetPackageTrackingDetails request or FBAOutboundServiceMWS_Model_GetPackageTrackingDetails object itself
     * @see Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetails
     * @return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function getPackageTrackingDetails($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/GetPackageTrackingDetailsResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_GetPackageTrackingDetailsResponse::fromXML($this->_invoke('GetPackageTrackingDetails'));
    }



    /**
     * List All Fulfillment Orders
     * Gets the first set of fulfillment orders that are currently being
     * fulfilled or that were being fulfilled at some time in the past
     * (as specified by the query parameters). Also returns a NextToken
     * which can be used iterate through the remaining fulfillment orders
     * (via the ListAllFulfillmentOrdersByNextToken operation).
     * If a NextToken is not returned, it indicates the end-of-data.
     *
     * If the QueryStartDateTime is set, the results will include all orders
     * currently being fulfilled, and all orders that were being fulfilled
     * since that date and time.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_ListAllFulfillmentOrders request or FBAOutboundServiceMWS_Model_ListAllFulfillmentOrders object itself
     * @see Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrders
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersResponse FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function listAllFulfillmentOrders($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/ListAllFulfillmentOrdersResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersResponse::fromXML($this->_invoke('ListAllFulfillmentOrders'));
    }



    /**
     * Get Fulfillment Preview
     * Get estimated shipping dates and fees for all
     * available shipping speed given a set of seller SKUs and quantities
     * If "ShippingSpeedCategories" are inputed, only previews for those options will be returned.
     *
     * If "ShippingSpeedCategories" are not inputed, then previews for all available options
     * are returned.
     * The service will return the fulfillment estimates for a set of Seller
     * SKUs and quantities.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_GetFulfillmentPreview request or FBAOutboundServiceMWS_Model_GetFulfillmentPreview object itself
     * @see Mws_FBAOutboundServiceMWS_Model_GetFulfillmentPreview
     * @return Mws_FBAOutboundServiceMWS_Model_GetFulfillmentPreviewResponse FBAOutboundServiceMWS_Model_GetFulfillmentPreviewResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function getFulfillmentPreview($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/GetFulfillmentPreviewResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_GetFulfillmentPreviewResponse::fromXML($this->_invoke('GetFulfillmentPreview'));
    }



    /**
     * Get Service Status
     * Request to poll the system for availability.
     * Status is one of GREEN, RED representing:
     * GREEN: This API section of the service is operating normally.
     * RED: The service is disrupted.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_GetServiceStatus request or FBAOutboundServiceMWS_Model_GetServiceStatus object itself
     * @see Mws_FBAOutboundServiceMWS_Model_GetServiceStatus
     * @return Mws_FBAOutboundServiceMWS_Model_GetServiceStatusResponse FBAOutboundServiceMWS_Model_GetServiceStatusResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function getServiceStatus($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/GetServiceStatusResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_GetServiceStatusResponse::fromXML($this->_invoke('GetServiceStatus'));
    }



    /**
     * List All Fulfillment Orders By Next Token
     * Gets the next set of fulfillment orders that are currently being
     * being fulfilled or that were being fulfilled at some time in the
     * past.
     * If a NextToken is not returned, it indicates the end-of-data.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextToken request or FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextToken object itself
     * @see Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextToken
     * @return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function listAllFulfillmentOrdersByNextToken($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/ListAllFulfillmentOrdersByNextTokenResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_ListAllFulfillmentOrdersByNextTokenResponse::fromXML($this->_invoke('ListAllFulfillmentOrdersByNextToken'));
    }



    /**
     * Get Fulfillment Order
     * Get detailed information about a FulfillmentOrder.  This includes the
     * original fulfillment order request, the status of the order and its
     * items in Amazon's fulfillment network, and the shipments that have been
     * generated to fulfill the order.
     *
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_GetFulfillmentOrder request or FBAOutboundServiceMWS_Model_GetFulfillmentOrder object itself
     * @see Mws_FBAOutboundServiceMWS_Model_GetFulfillmentOrder
     * @return Mws_FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function getFulfillmentOrder($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/GetFulfillmentOrderResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_GetFulfillmentOrderResponse::fromXML($this->_invoke('GetFulfillmentOrder'));
    }



    /**
     * Cancel Fulfillment Order
     * Request for Amazon to no longer attempt to fulfill an existing
     * fulfillment order. Amazon will attempt to stop fulfillment of all
     * items that haven't already shipped, but cannot guarantee success.
     * Note: Items that have already shipped cannot be cancelled.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_CancelFulfillmentOrder request or FBAOutboundServiceMWS_Model_CancelFulfillmentOrder object itself
     * @see Mws_FBAOutboundServiceMWS_Model_CancelFulfillmentOrder
     * @return Mws_FBAOutboundServiceMWS_Model_CancelFulfillmentOrderResponse FBAOutboundServiceMWS_Model_CancelFulfillmentOrderResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function cancelFulfillmentOrder($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/CancelFulfillmentOrderResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_CancelFulfillmentOrderResponse::fromXML($this->_invoke('CancelFulfillmentOrder'));
    }



    /**
     * Create Fulfillment Order
     * The SellerFulfillmentOrderId must be unique for all fulfillment
     * orders created by the seller. If your system already has a
     * unique order identifier, then that may be a good value to put in
     * this field.
     * This DisplayableOrderDateTime will appear as the "order date" in
     * recipient-facing materials such as the packing slip.  The format
     * must be timestamp.
     * The DisplayableOrderId will appear as the "order id" in those
     * materials, and the DisplayableOrderComment will appear as well.
     *
     * ShippingSpeedCategory is the Service Level Agreement for how long it
     * will take a shipment to be transported from the fulfillment center
     * to the recipient, once shipped. no default.
     * The following shipping speeds are available for US domestic:
     * * Standard, 3-5 business days
     * * Expedited, 2 business days
     * * Priority, 1 business day
     * Shipping speeds may vary elsewhere.  Please consult your manual for published SLAs.
     * DestinationAddress is the address the items will be shipped to.
     * FulfillmentPolicy indicates how unfulfillable items should be
     * handled. default is FillOrKill.
     * * FillOrKill if any item is determined to be unfulfillable
     * before any items have started shipping, the entire order is
     * considered unfulfillable.  Once any part of the order has
     * started shipping, as much of the order as possible will be
     * shipped.
     * * FillAll never consider any item unfulfillable.  Items must
     * either be fulfilled or merchant-cancelled.
     * * FillAllAvailable fulfill as much of the order as possible.
     *
     * FulfillmentMethod indicates the intended recipient channel for the
     * order whether it be a consumer order or inventory return.
     * default is Consumer.
     * The available methods to fulfill a given order:
     * * Consumer indicates a customer order, this is the default.
     * * Removal indicates that the inventory should be returned to the
     * specified destination address.
     *
     *
     * NotificationEmailList can be used to provide a list of e-mail
     * addresses to receive ship-complete e-mail notifications. These
     * e-mails are customer-facing e-mails sent by FBA on behalf of
     * the seller.
     *
     * @param mixed $request array of parameters for FBAOutboundServiceMWS_Model_CreateFulfillmentOrder request or FBAOutboundServiceMWS_Model_CreateFulfillmentOrder object itself
     * @see Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrder
     * @return Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderResponse FBAOutboundServiceMWS_Model_CreateFulfillmentOrderResponse
     *
     * @throws Mws_FBAOutboundServiceMWS_Exception
     */
    public function createFulfillmentOrder($request)
    {
        require_once ('Mws/FBAOutboundServiceMWS/Model/CreateFulfillmentOrderResponse.php');
        return Mws_FBAOutboundServiceMWS_Model_CreateFulfillmentOrderResponse::fromXML($this->_invoke('CreateFulfillmentOrder'));
    }

    // Private API ------------------------------------------------------------//

    private function _invoke($actionName)
    {
        return $xml = file_get_contents('Mws/FBAOutboundServiceMWS/Mock/' . $actionName . 'Response.xml', /** search include path */ TRUE);
    }
}
