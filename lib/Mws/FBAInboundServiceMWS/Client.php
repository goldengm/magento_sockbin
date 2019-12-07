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
 *  Generated: Thu Oct 28 11:11:22 UTC 2010
 *
 */

/**
 *  @see Mws_FBAInboundServiceMWS_Interface
 */
require_once ('Mws/FBAInboundServiceMWS/Interface.php');

/**

 * FBAInboundServiceMWS_Client is an implementation of FBAInboundServiceMWS
 *
 */
class Mws_FBAInboundServiceMWS_Client implements Mws_FBAInboundServiceMWS_Interface
{

    /** @var string */
    private  $_awsAccessKeyId = null;

    /** @var string */
    private  $_awsSecretAccessKey = null;

    /** @var array */
    private  $_config = array ('ServiceURL' => 'http://localhost:8000/',
                               'UserAgent' => 'FBAInboundServiceMWS PHP5 Library',
                               'SignatureVersion' => 2,
                               'SignatureMethod' => 'HmacSHA256',
                               'ProxyHost' => null,
                               'ProxyPort' => -1,
                               'MaxErrorRetry' => 3
                               );

    private $_serviceVersion = null;

    public $lastUrl = null;
    public $lastQuery = null;

    const REQUEST_TYPE = "POST";

    const MWS_CLIENT_VERSION = "2012-09-28";

    /**
     * Construct new Client
     *
     * @param string $awsAccessKeyId AWS Access Key ID
     * @param string $awsSecretAccessKey AWS Secret Access Key
     * @param array $config configuration options.
     * @param array $attributes user-agent attributes
     * Valid configuration options are:
     * <ul>
     * <li>ServiceURL</li>
     * <li>UserAgent</li>
     * <li>SignatureVersion</li>
     * <li>TimesRetryOnError</li>
     * <li>ProxyHost</li>
     * <li>ProxyPort</li>
     * <li>MaxErrorRetry</li>
     * </ul>
     */

    public function __construct(
    $awsAccessKeyId, $awsSecretAccessKey, $config, $applicationName, $applicationVersion, $attributes = null)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->_awsAccessKeyId = $awsAccessKeyId;
        $this->_awsSecretAccessKey = $awsSecretAccessKey;
        $this->_serviceVersion = self::MWS_CLIENT_VERSION;
        if (!is_null($config)) $this->_config = array_merge($this->_config, $config);
        $this->setUserAgentHeader($applicationName, $applicationVersion, $attributes);
    }

  /**
   * Sets a MWS compliant HTTP User-Agent Header value.
   * $attributeNameValuePairs is an associative array.
   *
   * @param $applicationName
   * @param $applicationVersion
   * @param $attributes
   * @return unknown_type
   */
  public function setUserAgentHeader(
      $applicationName,
      $applicationVersion,
      $attributes = null) {

    if (is_null($attributes)) {
      $attributes = array ();
    }

    $this->_config['UserAgent'] =
        $this->constructUserAgentHeader($applicationName, $applicationVersion, $attributes);
  }

  /**
   * Construct a valid MWS compliant HTTP User-Agent Header. From the MWS Developer's Guide, this
   * entails:
   * "To meet the requirements, begin with the name of your application, followed by a forward
   * slash, followed by the version of the application, followed by a space, an opening
   * parenthesis, the Language name value pair, and a closing paranthesis. The Language parameter
   * is a required attribute, but you can add additional attributes separated by semi-colons."
   *
   * @param $applicationName
   * @param $applicationVersion
   * @param $additionalNameValuePairs
   * @return unknown_type
   */
  private function constructUserAgentHeader($applicationName, $applicationVersion, $attributes = null) {

    if (is_null($applicationName) || $applicationName === "") {
      throw new InvalidArguementException('$applicationName cannot be null.');
    }

    if (is_null($applicationVersion) || $applicationVersion === "") {
      throw new InvalidArguementException('$applicationVersion cannot be null.');
    }

    $userAgent =
    $this->quoteApplicationName($applicationName)
        . '/'
        . $this->quoteApplicationVersion($applicationVersion);

    $userAgent .= ' (';

    $userAgent .= 'Language=PHP/' . phpversion();
    $userAgent .= '; ';
    $userAgent .= 'Platform=' . php_uname('s') . '/' . php_uname('m') . '/' . php_uname('r');
    $userAgent .= '; ';
    $userAgent .= 'MWSClientVersion=' . self::MWS_CLIENT_VERSION;

    foreach ($attributes as $key => $value) {
      if (is_null($value) || $value === '') {
        throw new InvalidArgumentException("Value for $key cannot be null or empty.");
      }

      $userAgent .= '; '
        . $this->quoteAttributeName($key)
        . '='
        . $this->quoteAttributeValue($value);
    }
    $userAgent .= ')';

    return $userAgent;
  }

  /**
   * Collapse multiple whitespace characters into a single ' ' character.
   * @param $s
   * @return string
   */
  private function collapseWhitespace($s) {
    return preg_replace('/ {2,}|\s/', ' ', $s);
  }

  /**
   * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
   * and '/' characters from a string.
   * @param $s
   * @return string
   */
  private function quoteApplicationName($s) {
    $quotedString = $this->collapseWhitespace($s);
    $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
    $quotedString = preg_replace('/\//', '\\/', $quotedString);

    return $quotedString;
  }

  /**
   * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
   * and '(' characters from a string.
   *
   * @param $s
   * @return string
   */
  private function quoteApplicationVersion($s) {
    $quotedString = $this->collapseWhitespace($s);
    $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
    $quotedString = preg_replace('/\\(/', '\\(', $quotedString);

    return $quotedString;
  }

  /**
   * Collapse multiple whitespace characters into a single ' ' and backslash escape '\',
   * and '=' characters from a string.
   *
   * @param $s
   * @return unknown_type
   */
  private function quoteAttributeName($s) {
    $quotedString = $this->collapseWhitespace($s);
    $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
    $quotedString = preg_replace('/\\=/', '\\=', $quotedString);

    return $quotedString;
  }

  /**
   * Collapse multiple whitespace characters into a single ' ' and backslash escape ';', '\',
   * and ')' characters from a string.
   *
   * @param $s
   * @return unknown_type
   */
  private function quoteAttributeValue($s) {
    $quotedString = $this->collapseWhitespace($s);
    $quotedString = preg_replace('/\\\\/', '\\\\\\\\', $quotedString);
    $quotedString = preg_replace('/\\;/', '\\;', $quotedString);
    $quotedString = preg_replace('/\\)/', '\\)', $quotedString);

    return $quotedString;
    }

    // Public API ------------------------------------------------------------//



    /**
     * Create Inbound Shipment Plan
     * Plans inbound shipments for a set of items.  Registers identifiers if needed,
     * and assigns ShipmentIds for planned shipments.
     * When all the items are not all in the same category (e.g. some sortable, some
     * non-sortable) it may be necessary to create multiple shipments (one for each
     * of the shipment groups returned).
     * @param mixed $request array of parameters for Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest request
     * or Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlan
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipmentPlan($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentPlanRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentPlanResponse.php');
        return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentPlanResponse::fromXML($this->_invoke($this->_convertCreateInboundShipmentPlan($request)));
    }



    /**
     * Get Service Status
     * Gets the status of the service.
     * Status is one of GREEN, RED representing:
     * GREEN: This API section of the service is operating normally.
     * RED: The service is disrupted.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_GetServiceStatusRequest request
     * or FBAInboundServiceMWS_Model_GetServiceStatusRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_GetServiceStatus
     * @return Mws_FBAInboundServiceMWS_Model_GetServiceStatusResponse FBAInboundServiceMWS_Model_GetServiceStatusResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function getServiceStatus($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_GetServiceStatusRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/GetServiceStatusRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_GetServiceStatusRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/GetServiceStatusResponse.php');
        return Mws_FBAInboundServiceMWS_Model_GetServiceStatusResponse::fromXML($this->_invoke($this->_convertGetServiceStatus($request)));
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
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentsRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentsRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipments
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResponse FBAInboundServiceMWS_Model_ListInboundShipmentsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipments($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResponse::fromXML($this->_invoke($this->_convertListInboundShipments($request)));
    }



    /**
     * List Inbound Shipments By Next Token
     * Gets the next set of inbound shipments created by a Seller with the
     * NextToken which can be used to iterate through the remaining inbound
     * shipments. If a NextToken is not returned, it indicates the
     * end-of-data.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextToken
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentsByNextToken($request)
    {
        if (!$request instanceof FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsByNextTokenRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentsByNextTokenResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsByNextTokenResponse::fromXML($this->_invoke($this->_convertListInboundShipmentsByNextToken($request)));
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
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest request
     * or FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_UpdateInboundShipment
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function updateInboundShipment($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/UpdateInboundShipmentRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/UpdateInboundShipmentResponse.php');
        return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentResponse::fromXML($this->_invoke($this->_convertUpdateInboundShipment($request)));
    }



    /**
     * Create Inbound Shipment
     * Creates an inbound shipment. It may include up to 200 items.
     * The initial status of a shipment will be set to 'Working'.
     * This operation will simply return a shipment Id upon success,
     * otherwise an explicit error will be returned.
     * More items may be added using the Update call.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_CreateInboundShipmentRequest request
     * or FBAInboundServiceMWS_Model_CreateInboundShipmentRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_CreateInboundShipment
     * @return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse FBAInboundServiceMWS_Model_CreateInboundShipmentResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function createInboundShipment($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/CreateInboundShipmentResponse.php');
        return Mws_FBAInboundServiceMWS_Model_CreateInboundShipmentResponse::fromXML($this->_invoke($this->_convertCreateInboundShipment($request)));
    }



    /**
     * List Inbound Shipment Items By Next Token
     * Gets the next set of inbound shipment items with the NextToken
     * which can be used to iterate through the remaining inbound shipment
     * items. If a NextToken is not returned, it indicates the
     * end-of-data. You must first call ListInboundShipmentItems to get
     * a 'NextToken'.
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextToken
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItemsByNextToken($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsByNextTokenRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsByNextTokenResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsByNextTokenResponse::fromXML($this->_invoke($this->_convertListInboundShipmentItemsByNextToken($request)));
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
     * @param mixed $request array of parameters for FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest request
     * or FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest object itself
     * @see Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItems
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse
     *
     * @throws Mws_FBAInboundServiceMWS_Exception
     */
    public function listInboundShipmentItems($request)
    {
        if (!$request instanceof Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest) {
            require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsRequest.php');
            $request = new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest($request);
        }
        require_once ('Mws/FBAInboundServiceMWS/Model/ListInboundShipmentItemsResponse.php');
        return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsResponse::fromXML($this->_invoke($this->_convertListInboundShipmentItems($request)));
    }

        // Private API ------------------------------------------------------------//

    /**
     * Invoke request and return response
     */
    private function _invoke(array $parameters)
    {
        $actionName = $parameters["Action"];
        $response = array();
        $responseBody = null;
        $statusCode = 200;

        /* Submit the request and read response body */
        try {

            // Ensure the endpoint URL is set.
            if (empty($this->_config['ServiceURL'])) {
                throw new Mws_MarketplaceWebService_Exception(
                    array('ErrorCode' => 'InvalidServiceUrl',
                          'Message' => "Missing serviceUrl configuration value. You may obtain a list of valid MWS URLs by consulting the MWS Developer's Guide, or reviewing the sample code published along side this library."));
            }

            /* Add required request parameters */
            $parameters = $this->_addRequiredParameters($parameters);

            $shouldRetry = false;
            $retries = 0;
            do {
                try {
                        $response = $this->_httpPost($parameters);
                        $httpStatus = $response['Status'];
                        switch ($httpStatus)
                        {
                            case 200:
                                $shouldRetry = false;
                                break;

                            case 500:
                            case 503:
                                require_once('Mws/FBAInboundServiceMWS/Model/ErrorResponse.php');
                                $errorResponse = Mws_FBAInboundServiceMWS_Model_ErrorResponse::fromXML($response['ResponseBody']);

                                // We will not retry throttling errors since this would just add to the throttling problem.
                                $errors = $errorResponse->getError();
                                $shouldRetry = ($errors[0]->getCode() === 'RequestThrottled') ? false : true;

                                if ($shouldRetry && $retries <= $this->config['MaxErrorRetry'])
                                {
                                    $this->_pauseOnRetry(++$retries);
                                }
                                else
                                {
                                    throw $this->_reportAnyErrors($response['ResponseBody'], $response['Status']);
                                }
                                break;

                            default:
                                    $shouldRetry = false;
                                    throw $this->_reportAnyErrors($response['ResponseBody'], $response['Status']);
                                break;
                        }
                /* Rethrow on deserializer error */
                } catch (Exception $e) {
                    require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                    throw new Mws_FBAInboundServiceMWS_Exception(array('Exception' => $e, 'Message' => $e->getMessage()));
                }

            } while ($shouldRetry);

        } catch (Mws_FBAInboundServiceMWS_Exception $se) {
            throw $se;
        } catch (Exception $t) {
            throw new Mws_FBAInboundServiceMWS_Exception(array('Exception' => $t, 'Message' => $t->getMessage()));
        }

        return $response['ResponseBody'];
    }

    /**
     * Look for additional error strings in the response and return formatted exception
     */
    private function _reportAnyErrors($responseBody, $status, Exception $e =  null)
    {
        $ex = null;
        if (!is_null($responseBody) && strpos($responseBody, '<') === 0) {
            if (preg_match('@<RequestId>(.*)</RequestId>.*<Error><Code>(.*)</Code><Message>(.*)</Message></Error>.*(<Error>)?@mi',
                $responseBody, $errorMatcherOne)) {

                $requestId = $errorMatcherOne[1];
                $code = $errorMatcherOne[2];
                $message = $errorMatcherOne[3];

                require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                $ex = new Mws_FBAInboundServiceMWS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code,
                                                           'ErrorType' => 'Unknown', 'RequestId' => $requestId, 'XML' => $responseBody));

            } elseif (preg_match('@<Error><Code>(.*)</Code><Message>(.*)</Message></Error>.*(<Error>)?.*<RequestID>(.*)</RequestID>@mi',
                $responseBody, $errorMatcherTwo)) {

                $code = $errorMatcherTwo[1];
                $message = $errorMatcherTwo[2];
                $requestId = $errorMatcherTwo[4];
                require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                $ex = new Mws_FBAInboundServiceMWS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code,
                                                              'ErrorType' => 'Unknown', 'RequestId' => $requestId, 'XML' => $responseBody));
            } elseif (preg_match('@<Error><Type>(.*)</Type><Code>(.*)</Code><Message>(.*)</Message>.*</Error>.*(<Error>)?.*<RequestId>(.*)</RequestId>@mi',
                $responseBody, $errorMatcherThree)) {

                $type = $errorMatcherThree[1];
                $code = $errorMatcherThree[2];
                $message = $errorMatcherThree[3];
                $requestId = $errorMatcherThree[5];
                require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                $ex = new Mws_FBAInboundServiceMWS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code,
                                                              'ErrorType' => $type, 'RequestId' => $requestId, 'XML' => $responseBody));

            } elseif (preg_match('@<Error>\n.*<Type>(.*)</Type>\n.*<Code>(.*)</Code>\n.*<Message>(.*)</Message>\n.*</Error>\n?.*<RequestId>(.*)</RequestId>\n.*@mi',
                $responseBody, $errorMatcherFour)) {

                $type = $errorMatcherFour[1];
                $code = $errorMatcherFour[2];
                $message = $errorMatcherFour[3];
                $requestId = $errorMatcherFour[4];
                require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                $ex = new Mws_FBAInboundServiceMWS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code,
                                                              'ErrorType' => $type, 'RequestId' => $requestId, 'XML' => $responseBody));

            } else {
                require_once ('Mws/FBAInboundServiceMWS/Exception.php');
                $ex = new Mws_FBAInboundServiceMWS_Exception(array('Message' => 'Internal Error', 'StatusCode' => $status));
            }
        } else {
            require_once ('Mws/FBAInboundServiceMWS/Exception.php');
            $ex = new Mws_FBAInboundServiceMWS_Exception(array('Message' => 'Internal Error', 'StatusCode' => $status));
        }
        return $ex;
    }



    /**
     * Perform HTTP post with exponential retries on error 500 and 503
     *
     */
    private function _httpPost(array $parameters)
    {
        $query = $this->_getParametersAsString($parameters);
        $url = parse_url ($this->_config['ServiceURL']);
        $scheme = '';

        switch ($url['scheme']) {
            case 'https':
                $scheme = 'https://';
                $port = (!isset($url['port']) || ($url['port'] === null)) ? 443 : $url['port'];
                break;
            default:
                $scheme = '';
                $port = (!isset($url['port']) || ($url['port'] === null)) ? 80 : $url['port'];
        }

        $ch = curl_init();
        $this->lastQuery = $query;
        $this->lastUrl = $scheme . $url['host'] . $url['path'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
        curl_setopt($ch, CURLOPT_URL, $this->lastUrl);
        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_config['UserAgent']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->lastQuery);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->_config['ProxyHost'] != null && $this->_config['ProxyPort'] != -1)
        {
            curl_setopt($ch, CURLOPT_PROXY, $this->_config['ProxyHost'] . ':' . $this->_config['ProxyHost']);
        }

        $response = "";
        $response = curl_exec($ch);

        curl_close($ch);

        list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
        $other = preg_split("/\r\n|\n|\r/", $other);
        list($protocol, $code, $text) = explode(' ', trim(array_shift($other)), 3);

        return array ('Status' => (int)$code, 'ResponseBody' => $responseBody);
    }

    /**
     * Exponential sleep on failed request
     * @param retries current retry
     * @throws Mws_FBAInboundServiceMWS_Exception if maximum number of retries has been reached
     */
    private function _pauseOnRetry($retries)
    {
        $delay = (int) (pow(4, $retries) * 100000) ;
        usleep($delay);
    }

    /**
     * Add authentication related and version parameters
     */
    private function _addRequiredParameters(array $parameters)
    {
        $parameters['AWSAccessKeyId'] = $this->_awsAccessKeyId;
        $parameters['Timestamp'] = $this->_getFormattedTimestamp();
        //$parameters['Version'] = $this->_serviceVersion;
        $parameters['SignatureVersion'] = $this->_config['SignatureVersion'];
        if ($parameters['SignatureVersion'] > 1) {
            $parameters['SignatureMethod'] = $this->_config['SignatureMethod'];
        }
        $parameters['Signature'] = $this->_signParameters($parameters, $this->_awsSecretAccessKey);

        return $parameters;
    }

    /**
     * Convert paremeters to Url encoded query string
     */
    private function _getParametersAsString(array $parameters)
    {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            if (!is_null($key) && $key !=='' && !is_null($value) && $value!=='')
            {
                $queryParameters[] = $key . '=' . $this->_urlencode($value);
            }
        }
        return implode('&', $queryParameters);
    }


    /**
     * Computes RFC 2104-compliant HMAC signature for request parameters
     * Implements AWS Signature, as per following spec:
     *
     * Signature Version 0: This is not supported in the MWS.
     *
     * Signature Version 1: This is not supported in the MWS.
     *
     * Signature Version is 2, string to sign is based on following:
     *
     *    1. The HTTP Request Method followed by an ASCII newline (%0A)
     *    2. The HTTP Host header in the form of lowercase host, followed by an ASCII newline.
     *    3. The URL encoded HTTP absolute path component of the URI
     *       (up to but not including the query string parameters);
     *       if this is empty use a forward '/'. This parameter is followed by an ASCII newline.
     *    4. The concatenation of all query string components (names and values)
     *       as UTF-8 characters which are URL encoded as per RFC 3986
     *       (hex characters MUST be uppercase), sorted using lexicographic byte ordering.
     *       Parameter names are separated from their values by the '=' character
     *       (ASCII character 61), even if the value is empty.
     *       Pairs of parameter and values are separated by the '&' character (ASCII code 38).
     *
     */
    private function _signParameters(array $parameters, $key) {
        $signatureVersion = $parameters['SignatureVersion'];
        $algorithm = "HmacSHA1";
        $stringToSign = null;
        if (0 === $signatureVersion) {
            throw new InvalidArguementException(
                'Signature Version 0 is no longer supported. Only Signature Version 2 is supported.');
        } else if (1 === $signatureVersion) {
            throw new InvalidArguementException(
                'Signature Version 1 is no longer supported. Only Signature Version 2 is supported.');
        } else if (2 === $signatureVersion) {
            $algorithm = $this->_config['SignatureMethod'];
            $parameters['SignatureMethod'] = $algorithm;
            $stringToSign = $this->_calculateStringToSignV2($parameters);
        } else {
            throw new Exception("Invalid Signature Version specified");
        }
        return $this->_sign($stringToSign, $key, $algorithm);
    }

    /**
     * Calculate String to Sign for SignatureVersion 2
     * @param array $parameters request parameters
     * @return String to Sign
     */
    private function _calculateStringToSignV2(array $parameters) {
        $parsedUrl = parse_url($this->_config['ServiceURL']);
        $endpoint = $parsedUrl['host'];
        if (!is_null($parsedUrl['port'])) {
          $endpoint .= ':' . $parsedUrl['port'];
        }

        $data = 'POST';
        $data .= "\n";
        $endpoint = parse_url ($this->_config['ServiceURL']);
        $data .= $endpoint['host'];
        $data .= "\n";
        $uri = array_key_exists('path', $endpoint) ? $endpoint['path'] : null;
        if (!isset ($uri)) {
            $uri = "/";
        }
        $uriencoded = implode("/", array_map(array($this, "_urlencode"), explode("/", $uri)));
        $data .= $uriencoded;
        $data .= "\n";
        uksort($parameters, 'strcmp');
        $data .= $this->_getParametersAsString($parameters);
        return $data;
    }

    private function _urlencode($value) {
        return str_replace('%7E', '~', rawurlencode($value));
    }


    /**
     * Computes RFC 2104-compliant HMAC signature.
     */
    private function _sign($data, $key, $algorithm)
    {
        if ($algorithm === 'HmacSHA1') {
            $hash = 'sha1';
        } else if ($algorithm === 'HmacSHA256') {
            $hash = 'sha256';
        } else {
            throw new Exception ("Non-supported signing method specified");
        }
        return base64_encode(
            hash_hmac($hash, $data, $key, true)
        );
    }


    /**
     * Formats date as ISO 8601 timestamp
     */
    private function _getFormattedTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }



    /**
     * Convert CreateInboundShipmentPlanRequest to name value pairs
     */
    private function _convertCreateInboundShipmentPlan($request) {

        $parameters = array();
        $parameters['Action'] = 'CreateInboundShipmentPlan';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetShipFromAddress()) {
            $shipFromAddresscreateInboundShipmentPlanRequest = $request->getShipFromAddress();
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetName()) {
                $parameters['ShipFromAddress' . '.' . 'Name'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getName();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetAddressLine1()) {
                $parameters['ShipFromAddress' . '.' . 'AddressLine1'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getAddressLine1();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetAddressLine2()) {
                $parameters['ShipFromAddress' . '.' . 'AddressLine2'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getAddressLine2();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetDistrictOrCounty()) {
                $parameters['ShipFromAddress' . '.' . 'DistrictOrCounty'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getDistrictOrCounty();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetCity()) {
                $parameters['ShipFromAddress' . '.' . 'City'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getCity();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetStateOrProvinceCode()) {
                $parameters['ShipFromAddress' . '.' . 'StateOrProvinceCode'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getStateOrProvinceCode();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetCountryCode()) {
                $parameters['ShipFromAddress' . '.' . 'CountryCode'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getCountryCode();
            }
            if ($shipFromAddresscreateInboundShipmentPlanRequest->isSetPostalCode()) {
                $parameters['ShipFromAddress' . '.' . 'PostalCode'] =  $shipFromAddresscreateInboundShipmentPlanRequest->getPostalCode();
            }
        }
        if ($request->isSetLabelPrepPreference()) {
            $parameters['LabelPrepPreference'] =  $request->getLabelPrepPreference();
        }
        if ($request->isSetShipToCountryCode()) {
            $parameters['ShipToCountryCode'] =  $request->getShipToCountryCode();
        }
        if ($request->isSetInboundShipmentPlanRequestItems()) {
            $inboundShipmentPlanRequestItemscreateInboundShipmentPlanRequest = $request->getInboundShipmentPlanRequestItems();
            foreach ($inboundShipmentPlanRequestItemscreateInboundShipmentPlanRequest->getmember() as $memberinboundShipmentPlanRequestItemsIndex => $memberinboundShipmentPlanRequestItems) {
                if ($memberinboundShipmentPlanRequestItems->isSetSellerSKU()) {
                    $parameters['InboundShipmentPlanRequestItems' . '.' . 'member' . '.'  . ($memberinboundShipmentPlanRequestItemsIndex + 1) . '.' . 'SellerSKU'] =  $memberinboundShipmentPlanRequestItems->getSellerSKU();
                }
                if ($memberinboundShipmentPlanRequestItems->isSetASIN()) {
                    $parameters['InboundShipmentPlanRequestItems' . '.' . 'member' . '.'  . ($memberinboundShipmentPlanRequestItemsIndex + 1) . '.' . 'ASIN'] =  $memberinboundShipmentPlanRequestItems->getASIN();
                }
                if ($memberinboundShipmentPlanRequestItems->isSetCondition()) {
                    $parameters['InboundShipmentPlanRequestItems' . '.' . 'member' . '.'  . ($memberinboundShipmentPlanRequestItemsIndex + 1) . '.' . 'Condition'] =  $memberinboundShipmentPlanRequestItems->getCondition();
                }
                if ($memberinboundShipmentPlanRequestItems->isSetQuantity()) {
                    $parameters['InboundShipmentPlanRequestItems' . '.' . 'member' . '.'  . ($memberinboundShipmentPlanRequestItemsIndex + 1) . '.' . 'Quantity'] =  $memberinboundShipmentPlanRequestItems->getQuantity();
                }
                if ($memberinboundShipmentPlanRequestItems->isSetQuantityInCase()) {
                    $parameters['InboundShipmentPlanRequestItems' . '.' . 'member' . '.'  . ($memberinboundShipmentPlanRequestItemsIndex + 1) . '.' . 'QuantityInCase'] =  $memberinboundShipmentPlanRequestItems->getQuantityInCase();
                }

            }
        }

        return $parameters;
    }


    /**
     * Convert GetServiceStatusRequest to name value pairs
     */
    private function _convertGetServiceStatus($request) {

        $parameters = array();
        $parameters['Action'] = 'GetServiceStatus';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }

        return $parameters;
    }


    /**
     * Convert ListInboundShipmentsRequest to name value pairs
     */
    private function _convertListInboundShipments($request) {

        $parameters = array();
        $parameters['Action'] = 'ListInboundShipments';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetShipmentStatusList()) {
            $shipmentStatusListlistInboundShipmentsRequest = $request->getShipmentStatusList();
            foreach  ($shipmentStatusListlistInboundShipmentsRequest->getmember() as $membershipmentStatusListIndex => $membershipmentStatusList) {
                $parameters['ShipmentStatusList' . '.' . 'member' . '.'  . ($membershipmentStatusListIndex + 1)] =  $membershipmentStatusList;
            }
        }
        if ($request->isSetShipmentIdList()) {
            $shipmentIdListlistInboundShipmentsRequest = $request->getShipmentIdList();
            foreach  ($shipmentIdListlistInboundShipmentsRequest->getmember() as $membershipmentIdListIndex => $membershipmentIdList) {
                $parameters['ShipmentIdList' . '.' . 'member' . '.'  . ($membershipmentIdListIndex + 1)] =  $membershipmentIdList;
            }
        }
        if ($request->isSetLastUpdatedBefore()) {
            $parameters['LastUpdatedBefore'] =  $request->getLastUpdatedBefore();
        }
        if ($request->isSetLastUpdatedAfter()) {
            $parameters['LastUpdatedAfter'] =  $request->getLastUpdatedAfter();
        }

        return $parameters;
    }


    /**
     * Convert ListInboundShipmentsByNextTokenRequest to name value pairs
     */
    private function _convertListInboundShipmentsByNextToken($request) {

        $parameters = array();
        $parameters['Action'] = 'ListInboundShipmentsByNextToken';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetNextToken()) {
            $parameters['NextToken'] =  $request->getNextToken();
        }

        return $parameters;
    }


    /**
     * Convert UpdateInboundShipmentRequest to name value pairs
     */
    private function _convertUpdateInboundShipment($request) {

        $parameters = array();
        $parameters['Action'] = 'UpdateInboundShipment';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetShipmentId()) {
            $parameters['ShipmentId'] =  $request->getShipmentId();
        }
        if ($request->isSetInboundShipmentHeader()) {
            $inboundShipmentHeaderupdateInboundShipmentRequest = $request->getInboundShipmentHeader();
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetShipmentName()) {
                $parameters['InboundShipmentHeader' . '.' . 'ShipmentName'] =  $inboundShipmentHeaderupdateInboundShipmentRequest->getShipmentName();
            }
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetShipFromAddress()) {
                $shipFromAddressinboundShipmentHeader = $inboundShipmentHeaderupdateInboundShipmentRequest->getShipFromAddress();
                if ($shipFromAddressinboundShipmentHeader->isSetName()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'Name'] =  $shipFromAddressinboundShipmentHeader->getName();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetAddressLine1()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'AddressLine1'] =  $shipFromAddressinboundShipmentHeader->getAddressLine1();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetAddressLine2()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'AddressLine2'] =  $shipFromAddressinboundShipmentHeader->getAddressLine2();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetDistrictOrCounty()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'DistrictOrCounty'] =  $shipFromAddressinboundShipmentHeader->getDistrictOrCounty();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetCity()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'City'] =  $shipFromAddressinboundShipmentHeader->getCity();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetStateOrProvinceCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'StateOrProvinceCode'] =  $shipFromAddressinboundShipmentHeader->getStateOrProvinceCode();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetCountryCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'CountryCode'] =  $shipFromAddressinboundShipmentHeader->getCountryCode();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetPostalCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'PostalCode'] =  $shipFromAddressinboundShipmentHeader->getPostalCode();
                }
            }
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetDestinationFulfillmentCenterId()) {
                $parameters['InboundShipmentHeader' . '.' . 'DestinationFulfillmentCenterId'] =  $inboundShipmentHeaderupdateInboundShipmentRequest->getDestinationFulfillmentCenterId();
            }
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetAreCasesRequired()) {
                $parameters['InboundShipmentHeader' . '.' . 'AreCasesRequired'] =  $inboundShipmentHeaderupdateInboundShipmentRequest->getAreCasesRequired() ? "true" : "false";
            }
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetShipmentStatus()) {
                $parameters['InboundShipmentHeader' . '.' . 'ShipmentStatus'] =  $inboundShipmentHeaderupdateInboundShipmentRequest->getShipmentStatus();
            }
            if ($inboundShipmentHeaderupdateInboundShipmentRequest->isSetLabelPrepPreference()) {
                $parameters['InboundShipmentHeader' . '.' . 'LabelPrepPreference'] =  $inboundShipmentHeaderupdateInboundShipmentRequest->getLabelPrepPreference();
            }
        }
        if ($request->isSetInboundShipmentItems()) {
            $inboundShipmentItemsupdateInboundShipmentRequest = $request->getInboundShipmentItems();
            foreach ($inboundShipmentItemsupdateInboundShipmentRequest->getmember() as $memberinboundShipmentItemsIndex => $memberinboundShipmentItems) {
                if ($memberinboundShipmentItems->isSetShipmentId()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'ShipmentId'] =  $memberinboundShipmentItems->getShipmentId();
                }
                if ($memberinboundShipmentItems->isSetSellerSKU()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'SellerSKU'] =  $memberinboundShipmentItems->getSellerSKU();
                }
                if ($memberinboundShipmentItems->isSetFulfillmentNetworkSKU()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'FulfillmentNetworkSKU'] =  $memberinboundShipmentItems->getFulfillmentNetworkSKU();
                }
                if ($memberinboundShipmentItems->isSetQuantityShipped()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityShipped'] =  $memberinboundShipmentItems->getQuantityShipped();
                }
                if ($memberinboundShipmentItems->isSetQuantityReceived()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityReceived'] =  $memberinboundShipmentItems->getQuantityReceived();
                }
                if ($memberinboundShipmentItems->isSetQuantityInCase()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityInCase'] =  $memberinboundShipmentItems->getQuantityInCase();
                }

            }
        }

        return $parameters;
    }


    /**
     * Convert CreateInboundShipmentRequest to name value pairs
     */
    private function _convertCreateInboundShipment($request) {

        $parameters = array();
        $parameters['Action'] = 'CreateInboundShipment';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetShipmentId()) {
            $parameters['ShipmentId'] =  $request->getShipmentId();
        }
        if ($request->isSetInboundShipmentHeader()) {
            $inboundShipmentHeadercreateInboundShipmentRequest = $request->getInboundShipmentHeader();
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetShipmentName()) {
                $parameters['InboundShipmentHeader' . '.' . 'ShipmentName'] =  $inboundShipmentHeadercreateInboundShipmentRequest->getShipmentName();
            }
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetShipFromAddress()) {
                $shipFromAddressinboundShipmentHeader = $inboundShipmentHeadercreateInboundShipmentRequest->getShipFromAddress();
                if ($shipFromAddressinboundShipmentHeader->isSetName()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'Name'] =  $shipFromAddressinboundShipmentHeader->getName();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetAddressLine1()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'AddressLine1'] =  $shipFromAddressinboundShipmentHeader->getAddressLine1();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetAddressLine2()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'AddressLine2'] =  $shipFromAddressinboundShipmentHeader->getAddressLine2();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetDistrictOrCounty()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'DistrictOrCounty'] =  $shipFromAddressinboundShipmentHeader->getDistrictOrCounty();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetCity()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'City'] =  $shipFromAddressinboundShipmentHeader->getCity();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetStateOrProvinceCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'StateOrProvinceCode'] =  $shipFromAddressinboundShipmentHeader->getStateOrProvinceCode();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetCountryCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'CountryCode'] =  $shipFromAddressinboundShipmentHeader->getCountryCode();
                }
                if ($shipFromAddressinboundShipmentHeader->isSetPostalCode()) {
                    $parameters['InboundShipmentHeader' . '.' . 'ShipFromAddress' . '.' . 'PostalCode'] =  $shipFromAddressinboundShipmentHeader->getPostalCode();
                }
            }
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetDestinationFulfillmentCenterId()) {
                $parameters['InboundShipmentHeader' . '.' . 'DestinationFulfillmentCenterId'] =  $inboundShipmentHeadercreateInboundShipmentRequest->getDestinationFulfillmentCenterId();
            }
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetAreCasesRequired()) {
                $parameters['InboundShipmentHeader' . '.' . 'AreCasesRequired'] =  $inboundShipmentHeadercreateInboundShipmentRequest->getAreCasesRequired() ? "true" : "false";
            }
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetShipmentStatus()) {
                $parameters['InboundShipmentHeader' . '.' . 'ShipmentStatus'] =  $inboundShipmentHeadercreateInboundShipmentRequest->getShipmentStatus();
            }
            if ($inboundShipmentHeadercreateInboundShipmentRequest->isSetLabelPrepPreference()) {
                $parameters['InboundShipmentHeader' . '.' . 'LabelPrepPreference'] =  $inboundShipmentHeadercreateInboundShipmentRequest->getLabelPrepPreference();
            }
        }
        if ($request->isSetInboundShipmentItems()) {
            $inboundShipmentItemscreateInboundShipmentRequest = $request->getInboundShipmentItems();
            foreach ($inboundShipmentItemscreateInboundShipmentRequest->getmember() as $memberinboundShipmentItemsIndex => $memberinboundShipmentItems) {
                if ($memberinboundShipmentItems->isSetShipmentId()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'ShipmentId'] =  $memberinboundShipmentItems->getShipmentId();
                }
                if ($memberinboundShipmentItems->isSetSellerSKU()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'SellerSKU'] =  $memberinboundShipmentItems->getSellerSKU();
                }
                if ($memberinboundShipmentItems->isSetFulfillmentNetworkSKU()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'FulfillmentNetworkSKU'] =  $memberinboundShipmentItems->getFulfillmentNetworkSKU();
                }
                if ($memberinboundShipmentItems->isSetQuantityShipped()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityShipped'] =  $memberinboundShipmentItems->getQuantityShipped();
                }
                if ($memberinboundShipmentItems->isSetQuantityReceived()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityReceived'] =  $memberinboundShipmentItems->getQuantityReceived();
                }
                if ($memberinboundShipmentItems->isSetQuantityInCase()) {
                    $parameters['InboundShipmentItems' . '.' . 'member' . '.'  . ($memberinboundShipmentItemsIndex + 1) . '.' . 'QuantityInCase'] =  $memberinboundShipmentItems->getQuantityInCase();
                }

            }
        }

        return $parameters;
    }


    /**
     * Convert ListInboundShipmentItemsByNextTokenRequest to name value pairs
     */
    private function _convertListInboundShipmentItemsByNextToken($request) {

        $parameters = array();
        $parameters['Action'] = 'ListInboundShipmentItemsByNextToken';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetNextToken()) {
            $parameters['NextToken'] =  $request->getNextToken();
        }

        return $parameters;
    }


    /**
     * Convert ListInboundShipmentItemsRequest to name value pairs
     */
    private function _convertListInboundShipmentItems($request) {

        $parameters = array();
        $parameters['Action'] = 'ListInboundShipmentItems';
        if ($request->isSetSellerId()) {
            $parameters['SellerId'] =  $request->getSellerId();
        }
        if ($request->isSetMarketplace()) {
            $parameters['Marketplace'] =  $request->getMarketplace();
        }
        if ($request->isSetShipmentId()) {
            $parameters['ShipmentId'] =  $request->getShipmentId();
        }
        if ($request->isSetLastUpdatedBefore()) {
            $parameters['LastUpdatedBefore'] =  $request->getLastUpdatedBefore();
        }
        if ($request->isSetLastUpdatedAfter()) {
            $parameters['LastUpdatedAfter'] =  $request->getLastUpdatedAfter();
        }

        return $parameters;
    }


}
