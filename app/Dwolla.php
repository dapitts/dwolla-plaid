<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Dwolla
{
    private $env;
    private $sandbox_key;
    private $sandbox_secret;
    private $production_key;
    private $production_secret;
    private $webhook_url;
    private $webhook_secret;
    private $oauth_access_token;

    public function __construct() {
        $wlId = Auth::user()->wlId;

        // Set environment to sandbox for demo and demo24hr
        if ($wlId == 'WL923223063B847' || $wlId == 'WL353785118B889')
            $this->env = 'sandbox';
        else
            $this->env = env('DWOLLA_ENV', 'sandbox');

        $this->sandbox_key = env('DWOLLA_SANDBOX_KEY', null);
        $this->sandbox_secret = env('DWOLLA_SANDBOX_SECRET', null);
        $this->production_key = env('DWOLLA_PRODUCTION_KEY', null);
        $this->production_secret = env('DWOLLA_PRODUCTION_SECRET', null);
        $this->webhook_url = env('DWOLLA_WEBHOOK_URL', null);
        $this->webhook_secret = env('DWOLLA_WEBHOOK_SECRET', null);
    }

    /**
     * Get business classifications
     *
     * @return stdClass
     *
     */
    public function getBusinessClassifications() {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $this->getAPIHost() . '/business-classifications', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                return json_decode($response->result);
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respError = app()->make('stdClass');
                $embedded = app()->make('stdClass');

                $respError->_links = app()->make('stdClass');
                $respError->_embedded = $embedded;
                $respError->_embedded->{'business-classifications'} = array();
                $respError->total = 0;

                return $respError;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Get error message
     *
     * @param  stdClass $result
     * @param  string   $resourceType
     * @return string
     *
     */
    public function getErrorMessage($result, $resourceType = null) {
        switch ($result->code) {
            case 'ValidationError':
                $message = '';
                $individual_msg = '';
                $errors_len = count($result->_embedded->errors);

                for ($i = 0; $i < $errors_len; $i++) {
                    $individual_msg = $result->_embedded->errors[$i]->message;

                    if ($errors_len == 1 || ($errors_len > 1 && $i == $errors_len - 1))
                        $message .= $individual_msg;
                    else if ($errors_len > 1 && $i != $errors_len - 1) {
                        $message .= str_replace('.', ',', $individual_msg);
                        $message .= ' ';
                    }
                }

                return $message;
                break;
            case 'NotFound':
                if ($resourceType)
                    return 'The ' . $resourceType . ' was not found.';
                else
                    return $result->message;
                break;
            case 'InvalidResourceState':
                if ($resourceType)
                    return 'The ' . $resourceType . ' cannot be modified.';
                else
                    return $result->message;
                break;
            default:
                return $result->message;
                break;
        }
    }

    /**
     * Create verified business Customer
     *
     * @param  Request $request
     * @return stdClass
     *
     */
    public function createVerifiedBusinessCustomer($request) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $businessType = $this->getBusinessType($request->businessType);

        if ($businessType == 'soleProprietorship') {
            $postFields = app()->make('stdClass');
            $postFields->firstName = $request->firstName;
            $postFields->lastName = $request->lastName;
            $postFields->email = $request->email;
            $postFields->type = $request->type;
            $postFields->dateOfBirth = $request->dateOfBirth;
            $postFields->ssn = $request->ssn;
            $postFields->address1 = $request->address1;
            $postFields->city = $request->city;
            $postFields->state = $request->state;
            $postFields->postalCode = $request->postalCode;
            $postFields->businessName = $request->businessName;
            $postFields->businessType = $businessType;
            $postFields->businessClassification = $request->industrialClassification;

            // Optional fields: ipAddress, address2, doingBusinessAs, ein, website, phone
            $postFields->ipAddress = $this->getIPAddress();

            if (!empty($request->address2))
                $postFields->address2 = $request->address2;

            if (!empty($request->doingBusinessAs))
                $postFields->doingBusinessAs = $request->doingBusinessAs;

            if (!empty($request->ein))
                $postFields->ein = $request->ein;

            if (!empty($request->website))
                $postFields->website = $request->website;

            if (!empty($request->phone))
                $postFields->phone = $request->phone;
        } else {
            $cntlrAddress = app()->make('stdClass');
            $cntlrAddress->address1 = $request->cntlrAddress1;
            $cntlrAddress->city = $request->cntlrCity;
            $cntlrAddress->stateProvinceRegion = $request->cntlrStateProvinceRegion;
            $cntlrAddress->postalCode = $request->cntlrPostalCode;
            $cntlrAddress->country = $request->cntlrCountry;

            // Controller address optional fields: address2, address3
            if (!empty($request->cntlrAddress2))
                $cntlrAddress->address2 = $request->cntlrAddress2;

            if (!empty($request->cntlrAddress3))
                $cntlrAddress->address3 = $request->cntlrAddress3;

            if (empty($request->cntlrSsn)) {
                $cntlrPassport = app()->make('stdClass');
                $cntlrPassport->number = $request->cntlrPassportNumber;
                $cntlrPassport->country = $request->cntlrPassportCountry;
            }
            
            $controller = app()->make('stdClass');
            $controller->firstName = $request->cntlrFirstName;
            $controller->lastName = $request->cntlrLastName;
            $controller->title = $request->cntlrTitle;
            $controller->dateOfBirth = $request->cntlrDateOfBirth;

            if (!empty($request->cntlrSsn))
                $controller->ssn = $request->cntlrSsn;
            else
                $controller->passport = $cntlrPassport;

            $controller->address = $cntlrAddress;

            $postFields = app()->make('stdClass');
            $postFields->firstName = $request->firstName;
            $postFields->lastName = $request->lastName;
            $postFields->email = $request->email;
            $postFields->type = $request->type;
            $postFields->address1 = $request->address1;
            $postFields->city = $request->city;
            $postFields->state = $request->state;
            $postFields->postalCode = $request->postalCode;
            $postFields->businessName = $request->businessName;
            $postFields->businessType = $businessType;
            $postFields->businessClassification = $request->industrialClassification;
            $postFields->ein = $request->ein;
            $postFields->controller = $controller;

            // Optional fields: ipAddress, address2, doingBusinessAs, website, phone
            $postFields->ipAddress = $this->getIPAddress();

            if (!empty($request->address2))
                $postFields->address2 = $request->address2;

            if (!empty($request->doingBusinessAs))
                $postFields->doingBusinessAs = $request->doingBusinessAs;

            if (!empty($request->website))
                $postFields->website = $request->website;

            if (!empty($request->phone))
                $postFields->phone = $request->phone;
        }

        $curl = new Curl('POST', $this->getAPIHost() . '/customers', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Update verified business Customer
     *
     * @param  Request $request
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function updateVerifiedBusinessCustomer($request, $locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');

        if (!empty($request->email))
            $postFields->email = $request->email;

        // Optional fields: ipAddress, address2, doingBusinessAs, website, phone
        $postFields->ipAddress = $this->getIPAddress();

        if (!empty($request->address1))
            $postFields->address1 = $request->address1;

        if (!empty($request->address2))
            $postFields->address2 = $request->address2;
        else
            $postFields->address2 = '';  // To unset Address 2

        if (!empty($request->city))
            $postFields->city = $request->city;

        if (!empty($request->state))
            $postFields->state = $request->state;

        if (!empty($request->postalCode))
            $postFields->postalCode = $request->postalCode;

        if (!empty($request->doingBusinessAs))
            $postFields->doingBusinessAs = $request->doingBusinessAs;
        else
            $postFields->doingBusinessAs = '';  // To unset Doing Business As

        if (!empty($request->website))
            $postFields->website = $request->website;
        else
            $postFields->website = '';  // To unset Website

        if (!empty($request->phone))
            $postFields->phone = $request->phone;
        else
            $postFields->phone = '';  // To unset Phone

        $curl = new Curl('POST', $locationResource, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retry verified business Customer
     *
     * @param  Request $request
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function retryVerifiedBusinessCustomer($request, $locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $businessType = $this->getBusinessType($request->businessType);

        if ($businessType == 'soleProprietorship') {
            $postFields = app()->make('stdClass');
            $postFields->firstName = $request->firstName;
            $postFields->lastName = $request->lastName;
            $postFields->email = $request->email;
            $postFields->type = $request->type;
            $postFields->dateOfBirth = $request->dateOfBirth;
            $postFields->ssn = $request->ssn;
            $postFields->address1 = $request->address1;
            $postFields->city = $request->city;
            $postFields->state = $request->state;
            $postFields->postalCode = $request->postalCode;
            $postFields->businessName = $request->businessName;
            $postFields->businessType = $businessType;
            $postFields->businessClassification = $request->industrialClassification;

            // Optional fields: ipAddress, address2, doingBusinessAs, ein, website, phone
            $postFields->ipAddress = $this->getIPAddress();

            if (!empty($request->address2))
                $postFields->address2 = $request->address2;
            else
                $postFields->address2 = '';  // To unset Address 2

            if (!empty($request->doingBusinessAs))
                $postFields->doingBusinessAs = $request->doingBusinessAs;
            else
                $postFields->doingBusinessAs = '';  // To unset Doing Business As

            if (!empty($request->ein))
                $postFields->ein = $request->ein;
            else
                $postFields->ein = '';  // To unset EIN

            if (!empty($request->website))
                $postFields->website = $request->website;
            else
                $postFields->website = '';  // To unset Website

            if (!empty($request->phone))
                $postFields->phone = $request->phone;
            else
                $postFields->phone = '';  // To unset Phone
        } else {
            $cntlrAddress = app()->make('stdClass');
            $cntlrAddress->address1 = $request->cntlrAddress1;
            $cntlrAddress->city = $request->cntlrCity;
            $cntlrAddress->stateProvinceRegion = $request->cntlrStateProvinceRegion;
            $cntlrAddress->postalCode = $request->cntlrPostalCode;
            $cntlrAddress->country = $request->cntlrCountry;

            // Controller address optional fields: address2, address3
            if (!empty($request->cntlrAddress2))
                $cntlrAddress->address2 = $request->cntlrAddress2;
            else
                $cntlrAddress->address2 = '';  // To unset Address 2

            if (!empty($request->cntlrAddress3))
                $cntlrAddress->address3 = $request->cntlrAddress3;
            else
                $cntlrAddress->address3 = '';  // To unset Address 3

            if (empty($request->cntlrSsn)) {
                $cntlrPassport = app()->make('stdClass');
                $cntlrPassport->number = $request->cntlrPassportNumber;
                $cntlrPassport->country = $request->cntlrPassportCountry;
            }

            $controller = app()->make('stdClass');
            $controller->firstName = $request->cntlrFirstName;
            $controller->lastName = $request->cntlrLastName;
            $controller->title = $request->cntlrTitle;
            $controller->dateOfBirth = $request->cntlrDateOfBirth;

            if (!empty($request->cntlrSsn))
                $controller->ssn = $request->cntlrSsn;
            else
                $controller->passport = $cntlrPassport;
            
            $controller->address = $cntlrAddress;

            $postFields = app()->make('stdClass');
            $postFields->firstName = $request->firstName;
            $postFields->lastName = $request->lastName;
            $postFields->email = $request->email;
            $postFields->type = $request->type;
            $postFields->address1 = $request->address1;
            $postFields->city = $request->city;
            $postFields->state = $request->state;
            $postFields->postalCode = $request->postalCode;
            $postFields->businessName = $request->businessName;
            $postFields->businessType = $businessType;
            $postFields->businessClassification = $request->industrialClassification;
            $postFields->ein = $request->ein;
            $postFields->controller = $controller;

            // Optional fields: ipAddress, address2, doingBusinessAs, website, phone
            $postFields->ipAddress = $this->getIPAddress();

            if (!empty($request->address2))
                $postFields->address2 = $request->address2;
            else
                $postFields->address2 = '';  // To unset Address 2

            if (!empty($request->doingBusinessAs))
                $postFields->doingBusinessAs = $request->doingBusinessAs;
            else
                $postFields->doingBusinessAs = '';  // To unset Doing Business As

            if (!empty($request->website))
                $postFields->website = $request->website;
            else
                $postFields->website = '';  // To unset Website

            if (!empty($request->phone))
                $postFields->phone = $request->phone;
            else
                $postFields->phone = '';  // To unset Phone
        }

        $curl = new Curl('POST', $locationResource, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Upload Customer document
     *
     * @param  string  $documentType
     * @param  string  $realPath
     * @param  string  $mimeType
     * @param  string  $originalName
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function uploadCustomerDocument($documentType, $realPath, $mimeType, $originalName, $locationResource) {
        $headerFields = array(
            'Authorization: Bearer ' . $this->getOAuthAccessToken(),
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Cache-Control: no-cache'
        );

        $postFields = array(
            'documentType' => $documentType,
            'file' => new \CurlFile($realPath, $mimeType, $originalName)
        );

        $curl = new Curl('POST', $locationResource . '/documents', $headerFields, $postFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Upload Beneficial Owner document
     *
     * @param  string  $documentType
     * @param  string  $realPath
     * @param  string  $mimeType
     * @param  string  $originalName
     * @param  string  $beneficialOwnerId
     * @return stdClass
     *
     */
    public function uploadBeneficialOwnerDocument($documentType, $realPath, $mimeType, $originalName, $beneficialOwnerId) {
        $headerFields = array(
            'Authorization: Bearer ' . $this->getOAuthAccessToken(),
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Cache-Control: no-cache'
        );

        $postFields = array(
            'documentType' => $documentType,
            'file' => new \CurlFile($realPath, $mimeType, $originalName)
        );

        $curl = new Curl('POST', $this->getAPIHost() . '/beneficial-owners/' . $beneficialOwnerId . '/documents', $headerFields, $postFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List Customer documents
     *
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function listCustomerDocuments($locationResource) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $locationResource . '/documents', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List Beneficial Owner documents
     *
     * @param  string  $beneficialOwnerId
     * @return stdClass
     *
     */
    public function listBeneficialOwnerDocs($beneficialOwnerId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $this->getAPIHost() . '/beneficial-owners/' . $beneficialOwnerId . '/documents', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve document
     *
     * @param  string  $documentResource
     * @return stdClass
     *
     */
    public function retrieveDocument($documentResource) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $documentResource, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create receive-only User
     *
     * @param  Request $request
     * @return stdClass
     *
     */
    public function createReceiveOnlyUser($request) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->firstName = $request->firstName;
        $postFields->lastName = $request->lastName;
        $postFields->email = $request->email;
        $postFields->type = $request->type;

        // Optional fields: businessName, ipAddress
        if (!empty($request->businessName))
            $postFields->businessName = $request->businessName;

        $postFields->ipAddress = $this->getIPAddress();

        $curl = new Curl('POST', $this->getAPIHost() . '/customers', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Update receive-only User
     *
     * @param  Request $request
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function updateReceiveOnlyUser($request, $locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');

        if (!empty($request->firstName))
            $postFields->firstName = $request->firstName;

        if (!empty($request->lastName))
            $postFields->lastName = $request->lastName;

        if (!empty($request->email))
            $postFields->email = $request->email;

        if (!empty($request->businessName))
            $postFields->businessName = $request->businessName;
        else
            $postFields->businessName = '';  // To unset Business Name

        $curl = new Curl('POST', $locationResource, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Update customer
     *
     * @param  string $customerId
     * @param  string $status
     * @return stdClass
     *
     */
    public function updateCustomer($customerId, $status) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->status = $status;

        $curl = new Curl('POST', $this->getAPIHost() . '/customers/' . $customerId, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve customer
     *
     * @param  string $locationResource
     * @return stdClass
     *
     */
    public function retrieveCustomer($locationResource) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $locationResource, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List beneficial owners
     *
     * @param  string $locationResource
     * @return stdClass
     *
     */
    public function listBeneficialOwners($locationResource) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $locationResource . '/beneficial-owners', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                return json_decode($response->result);
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respError = app()->make('stdClass');
                $embedded = app()->make('stdClass');

                $respError->_links = app()->make('stdClass');
                $respError->_embedded = $embedded;
                $respError->_embedded->{'beneficial-owners'} = array();
                $respError->total = 0;

                return $respError;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create beneficial owner
     *
     * @param  Request $request
     * @param  string  $locationResource
     * @return stdClass
     *
     */
    public function createBeneficialOwner($request, $locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $address = app()->make('stdClass');
        $address->address1 = $request->address1;
        $address->city = $request->city;
        $address->stateProvinceRegion = $request->stateProvinceRegion;
        $address->postalCode = $request->postalCode;
        $address->country = $request->country;

        if (!empty($request->address2))
            $address->address2 = $request->address2;

        if (!empty($request->address3))
            $address->address3 = $request->address3;

        $postFields = app()->make('stdClass');
        $postFields->firstName = $request->firstName;
        $postFields->lastName = $request->lastName;
        $postFields->ssn = $request->ssn;
        $postFields->dateOfBirth = $request->dateOfBirth;
        $postFields->address = $address;

        $curl = new Curl('POST', $locationResource . '/beneficial-owners', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Update beneficial owner
     *
     * @param  Request $request
     * @param  string  $beneficialOwnerId
     * @return stdClass
     *
     */
    public function updateBeneficialOwner($request, $beneficialOwnerId) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $address = app()->make('stdClass');
        $address->address1 = $request->address1;
        $address->city = $request->city;
        $address->stateProvinceRegion = $request->stateProvinceRegion;
        $address->postalCode = $request->postalCode;
        $address->country = $request->country;

        if (!empty($request->address2))
            $address->address2 = $request->address2;

        if (!empty($request->address3))
            $address->address3 = $request->address3;

        $postFields = app()->make('stdClass');
        $postFields->firstName = $request->firstName;
        $postFields->lastName = $request->lastName;
        $postFields->ssn = $request->ssn;
        $postFields->dateOfBirth = $request->dateOfBirth;
        $postFields->address = $address;

        $curl = new Curl('POST', $this->getAPIHost() . '/beneficial-owners/' . $beneficialOwnerId, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve beneficial owner
     *
     * @param  string $beneficialOwnerId  Beneficial owner unique identifier
     * @return stdClass
     *
     */
    public function retrieveBeneficialOwner($beneficialOwnerId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $this->getAPIHost() . '/beneficial-owners/' . $beneficialOwnerId, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Delete beneficial owner
     *
     * @param  string $beneficialOwnerId  id of beneficial owner to delete
     * @return stdClass
     *
     */
    public function deleteBeneficialOwner($beneficialOwnerId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('DELETE', $this->getAPIHost() . '/beneficial-owners/' . $beneficialOwnerId, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Certify ownership
     *
     * @param  string $locationResource
     * @return stdClass
     *
     */
    public function certifyOwnership($locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->status = 'certified';

        $curl = new Curl('POST', $locationResource . '/beneficial-ownership', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Certify customer ownership
     *
     * @param  string $customerId
     * @return stdClass
     *
     */
    public function certifyCustomerOwnership($customerId) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->status = 'certified';

        $curl = new Curl('POST', $this->getAPIHost() . '/customers/' . $customerId . '/beneficial-ownership', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create funding source
     *
     * @param  string  $locationResource
     * @param  Request $request
     * @return stdClass 
     *
     */
    public function createFundingSource($locationResource, $request) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->routingNumber = $request->abaRoutingTransitNumber;
        $postFields->accountNumber = $request->accountNumber;
        $postFields->bankAccountType = $request->accountType;
        $postFields->name = $request->accountName;

        $curl = new Curl('POST', $locationResource . '/funding-sources', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create funding source with Plaid token
     *
     * @param  string $locationResource
     * @param  string $processorToken
     * @param  string $fundingSourceName
     * @return stdClass 
     *
     */
    public function createFundingSourceWithPlaidToken($locationResource, $processorToken, $fundingSourceName) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->plaidToken = $processorToken;
        $postFields->name = $fundingSourceName;

        $curl = new Curl('POST', $locationResource . '/funding-sources', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve funding source
     *
     * @param  string $fundingSourceURL
     * @return stdClass 
     *
     */
    public function retrieveFundingSource($fundingSourceURL) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $fundingSourceURL, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve funding source balance
     *
     * @param  string $fundingSourceURL
     * @return stdClass 
     *
     */
    public function retrieveFundingSourceBalance($fundingSourceURL) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $fundingSourceURL . '/balance', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List customer funding sources
     *
     * @param  string $locationResource
     * @return stdClass 
     *
     */
    public function listCustomerFundingSources($locationResource) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $locationResource . '/funding-sources?removed=false', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Update funding source
     *
     * @param  string   $fundingSourceURL
     * @param  stdClass $fundingSourceInfo
     * @return stdClass 
     *
     */
    public function updateFundingSource($fundingSourceURL, $fundingSourceInfo) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->name = $fundingSourceInfo->accountName;

        if (!empty($fundingSourceInfo->accountType))
            $postFields->bankAccountType = $fundingSourceInfo->accountType;

        if (!empty($fundingSourceInfo->abaRoutingTransitNumber))
            $postFields->routingNumber = $fundingSourceInfo->abaRoutingTransitNumber;

        if (!empty($fundingSourceInfo->accountNumber))
            $postFields->accountNumber = $fundingSourceInfo->accountNumber;

        $curl = new Curl('POST', $fundingSourceURL, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Remove funding source
     *
     * @param  string $fundingSourceURL
     * @return stdClass 
     *
     */
    public function removeFundingSource($fundingSourceURL) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->removed = true;

        $curl = new Curl('POST', $fundingSourceURL, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create funding sources token
     *
     * @param  string $locationResource
     * @return stdClass 
     *
     */
    public function createFundingSourcesToken($locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('POST', $locationResource . '/funding-sources-token', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create IAV token
     *
     * @param  string $locationResource
     * @return stdClass 
     *
     */
    public function createIAVToken($locationResource) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('POST', $locationResource . '/iav-token', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Initiate micro-deposits
     *
     * @param  string $fundingSourceURL
     * @return stdClass 
     *
     */
    public function initiateMicroDeposits($fundingSourceURL) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('POST', $fundingSourceURL . '/micro-deposits', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Verify micro-deposits
     *
     * @param  string $fundingSourceURL
     * @param  string $amount1
     * @param  string $amount2
     * @return stdClass 
     *
     */
    public function verifyMicroDeposits($fundingSourceURL, $amount1, $amount2) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $amt1 = app()->make('stdClass');
        $amt1->value = $amount1;
        $amt1->currency = 'USD';

        $amt2 = app()->make('stdClass');
        $amt2->value = $amount2;
        $amt2->currency = 'USD';

        $postFields = app()->make('stdClass');
        $postFields->amount1 = $amt1;
        $postFields->amount2 = $amt2;

        $curl = new Curl('POST', $fundingSourceURL . '/micro-deposits', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Initiate transfer
     *
     * @param  string $srcFundingSourceURL
     * @param  string $destFundingSourceURL
     * @param  string $amount
     * @param  string $idempotencyKey
     * @return stdClass 
     *
     */
    public function initiateTransfer($srcFundingSourceURL, $destFundingSourceURL, $amount, $idempotencyKey) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken(),
            'Idempotency-Key: ' . $idempotencyKey
        );

        $source = app()->make('stdClass');
        $source->href = $srcFundingSourceURL;

        $destination = app()->make('stdClass');
        $destination->href = $destFundingSourceURL;

        $links = app()->make('stdClass');
        $links->source = $source;
        $links->destination = $destination;

        $amt = app()->make('stdClass');
        $amt->currency = 'USD';
        $amt->value = $amount;

        $postFields = app()->make('stdClass');
        $postFields->_links = $links;
        $postFields->amount = $amt;

        $curl = new Curl('POST', $this->getAPIHost() . '/transfers', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve transfer
     *
     * @param  string $transferId
     * @return stdClass 
     *
     */
    public function retrieveTransfer($transferId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $this->getAPIHost() . '/transfers/' . $transferId, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List customer transfers
     *
     * @param  string $locationResource
     * @param  int    $limit
     * @param  int    $offset
     * @param  string $searchTerm
     * @return stdClass 
     *
     */
    public function listCustomerTransfers($locationResource, $limit, $offset, $searchTerm = null) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        if ($searchTerm)
            $queryStr = '?limit=' . $limit . '&offset=' . $offset . $this->buildSearchQueryStr($searchTerm);
        else
            $queryStr = '?limit=' . $limit . '&offset=' . $offset;

        $curl = new Curl('GET', $locationResource . '/transfers' . $queryStr, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * List investor transfers
     *
     * @param  string $locationResource
     * @param  string $offset
     * @return stdClass 
     *
     */
    public function listInvestorTransfers($locationResource, $offset) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $locationResource . '/transfers?limit=10&offset=' . $offset, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Retrieve transfer failure reason
     *
     * @param  string $transferId
     * @return stdClass 
     *
     */
    public function retrieveTransferFailure($transferId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('GET', $this->getAPIHost() . '/transfers/' . $transferId . '/failure', $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Cancel transfer
     *
     * @param  string $transferId
     * @return stdClass 
     *
     */
    public function cancelTransfer($transferId) {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->status = 'cancelled';

        $curl = new Curl('POST', $this->getAPIHost() . '/transfers/' . $transferId, $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Create webhook subscription
     *
     * @return stdClass 
     *
     */
    public function createWebhookSubscription() {
        $headerFields = array(
            'Content-Type: application/vnd.dwolla.v1.hal+json',
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $postFields = app()->make('stdClass');
        $postFields->url = $this->webhook_url;
        $postFields->secret = $this->webhook_secret;

        $curl = new Curl('POST', $this->getAPIHost() . '/webhook-subscriptions', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 201) {
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Delete webhook subscription
     * 
     * @param  string $webhookId
     * @return stdClass 
     *
     */
    public function deleteWebhookSubscription($webhookId) {
        $headerFields = array(
            'Accept: application/vnd.dwolla.v1.hal+json',
            'Authorization: Bearer ' . $this->getOAuthAccessToken()
        );

        $curl = new Curl('DELETE', $this->getAPIHost() . '/webhook-subscriptions/' . $webhookId, $headerFields);
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);
                return $response;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                return $response;
            }
        } else
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
    }

    /**
     * Get webhook resource
     * 
     * @param  string $webhookId
     * @return string
     *
     */
    public function getWebhookResource($webhookId) {
        return $this->getAPIHost() . '/webhook-subscriptions/' . $webhookId;
    }

    /**
     * Get business type name
     *
     * @param  int    $businessType
     * @return string 
     *
     */
    public function getBusinessTypeName($businessType) {
        switch ($businessType) {
            case 1:
                return "Sole proprietorships";
                break;
            case 2:
                return "Unincorporated association";
                break;
            case 3:
                return "Trust";
                break;
            case 4:
                return "Corporation";
                break;
            case 5:
                return "Public corporations";
                break;
            case 6:
                return "Non-profits";
                break;
            case 7:
                return "LLCs";
                break;
            case 8:
                return "Partnerships, LP's, LLP's";
                break;
        }
    }

    /**
     * Create idempotency key
     *
     * @return string Version 4 UUID
     *
     */
    public function createIdempotencyKey() {
        try {
            return Uuid::uuid4()->toString();
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }

    private function getOAuthAccessToken() {
        if (session()->has('dwolla_access_token'))
            $this->oauth_access_token = json_decode(session('dwolla_access_token'));
        
        if ($this->isAccessTokenExpired()) {
            $retryCount = 3;

            $headerFields = array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->getApplicationKey() . ':' . $this->getApplicationSecret())
            );

            $postFields = array(
                'grant_type' => 'client_credentials'
            );

            $curl = new Curl('POST', $this->getAPIHost() . '/token', $headerFields, http_build_query($postFields));

            for ($i = 0; $i < $retryCount; $i++) {
                $response = $curl->exec();

                if ($response->result !== false) {
                    if ($response->http_code == 200) {
                        $this->oauth_access_token = json_decode($response->result);
                        $this->oauth_access_token->created = time();
                        session(['dwolla_access_token' => json_encode($this->oauth_access_token)]);

                        break;
                    } else {
                        if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                            $response->result = json_decode($response->result);
        
                        $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);
        
                        if ($response->http_code == 401) {
                            if ($i == $retryCount - 1) {
                                session(['dwolla_access_token' => $this->invalidAccessToken()]);
                                break;
                            } else
                                continue;
                        } else {
                            session(['dwolla_access_token' => $this->invalidAccessToken()]);
                            break;
                        }
                    }
                } else {
                    $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                    session(['dwolla_access_token' => $this->invalidAccessToken()]);
                    break;
                }
            }
        }

        return json_decode(session('dwolla_access_token'))->access_token;
    }

    private function isAccessTokenExpired() {
        if (!$this->oauth_access_token)
            return true;

        $created = 0;
        if (isset($this->oauth_access_token->created))
            $created = $this->oauth_access_token->created;

        // If the OAuth access token is set to expire in the next 30 seconds.
        return ($created + $this->oauth_access_token->expires_in - 30) < time();
    }

    private function invalidAccessToken() {
        $invalid_access_token = app()->make('stdClass');
        $invalid_access_token->access_token = 'invalid_access_token';
        $invalid_access_token->token_type = 'Bearer';
        $invalid_access_token->expires_in = 0;

        return json_encode($invalid_access_token);
    }

    /**
     * Get IP address
     *
     * @return string
     *
     */
    private function getIPAddress() {
        return request()->ip();
    }

    private function getCustomerId($locationResource) {
        return substr($locationResource, strrpos($locationResource, '/') + 1);
    }

    private function getBusinessType($businessType) {
        switch ($businessType) {
            case '1':  // Sole proprietorships
            case '2':  // Unincorporated association
            case '3':  // Trust
                return 'soleProprietorship';
                break;
            case '4':  // Corporation
            case '5':  // Public corporations
            case '6':  // Non-profits
                return 'corporation';
                break;
            case '7':  // LLCs
                return 'llc';
                break;
            case '8':  // Partnerships, LP's, LLP's
                return 'partnership';
                break;
        }
    }

    private function buildSearchQueryStr($searchTerm) {
        $searchPhrases = explode(' ', $searchTerm);
        $len = count($searchPhrases);
        $searchQueryStr = '';

        for ($i = 0; $i < $len; $i++)
            $searchQueryStr .= '&search=' . $searchPhrases[$i];

        return $searchQueryStr;
    }

    private function logError($file, $line, $function, $response) {
        $msg = $file . '(' . $line . ') ' . $function . '()';

        if ($response->result !== false)
            Log::error($msg, ['user' => Auth::user()->email, 'result' => $response->result, 'http_code' => $response->http_code]);
        else
            Log::error($msg, ['user' => Auth::user()->email, 'errno' => $response->errno, 'error' => $response->error]);
    }

    private function getApplicationKey() {
        switch ($this->env) {
            case 'sandbox':
                return $this->sandbox_key;
                break;
            case 'production':
                return $this->production_key;
                break;
        }
    }
    
    private function getApplicationSecret() {
        switch ($this->env) {
            case 'sandbox':
                return $this->sandbox_secret;
                break;
            case 'production':
                return $this->production_secret;
                break;
        }
    }

    private function getAPIHost() {
        switch ($this->env) {
            case 'sandbox':
                return 'https://api-sandbox.dwolla.com';
                break;
            case 'production':
                return 'https://api.dwolla.com';
                break;
        }
    }
}