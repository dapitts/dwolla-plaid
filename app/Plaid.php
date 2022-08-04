<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Plaid
{
    private $env;
    private $client_id;
    private $public_key;
    private $sandbox_secret;
    private $development_secret;
    private $production_secret;
    private $api_version;
    private $client_name;

    public function __construct() {
        $wlId = Auth::user()->wlId;

        // Set environment to sandbox for demo and demo24hr
        if ($wlId == 'WL923223063B847' || $wlId == 'WL353785118B889')
            $this->env = 'sandbox';
        else
            $this->env = env('PLAID_ENV', 'sandbox');

        $this->client_id = env('PLAID_CLIENT_ID', null);
        $this->public_key = env('PLAID_PUBLIC_KEY', null);
        $this->sandbox_secret = env('PLAID_SANDBOX_SECRET', null);
        $this->development_secret = env('PLAID_DEVELOPMENT_SECRET', null);
        $this->production_secret = env('PLAID_PRODUCTION_SECRET', null);
        $this->api_version = env('PLAID_API_VERSION', '2018-05-22');
        $this->client_name = env('PLAID_CLIENT_NAME', null);
    }

    /**
     * Get Plaid environment
     *
     * @return string $env
     *
     */
    public function getEnvironment() {
        return $this->env;
    }

    /**
     * Get Plaid public_key
     *
     * @return string $public_key
     *
     */
    public function getPublicKey() {
        return $this->public_key;
    }

    /**
     * Create a link token
     *
     * @return stdClass $respInfo
     *
     */
    public function createLinkToken() {
        $respInfo = app()->make('stdClass');

        if (($response = $this->checkPlaidKeys()) != 'success') {
            $respInfo->status = 'error';
            $respInfo->msg = $response;

            return $respInfo;
        }

        $headerFields = array(
            'Content-Type: application/json',
            'Plaid-Version: ' . $this->api_version
        );

        $clientUserId = app()->make('stdClass');
        $clientUserId->client_user_id = $this->getClientUserId();

        $postFields = app()->make('stdClass');
        $postFields->client_id = $this->client_id;
        $postFields->secret = $this->getSecret();
        $postFields->client_name = $this->client_name;
        $postFields->language = 'en';
        $postFields->country_codes = ['US'];
        $postFields->user = $clientUserId;
        $postFields->products = ['auth'];

        $curl = new Curl('POST', $this->getAPIHost() . '/link/token/create', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);

                $respInfo->status = 'success';
                $respInfo->msg = 'Link token was created.';
                $respInfo->link_token = $response->result->link_token;

                return $respInfo;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respInfo->status = 'error';
                $respInfo->msg = 'POST /link/token/create returned error.';
                $respInfo->error = $response->result;
                $respInfo->link_token = 'invalid_link_token';

                return $respInfo;
            }
        } else {
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

            $respInfo->status = 'error';
            $respInfo->msg = 'POST /link/token/create returned false.';
            $respInfo->link_token = 'invalid_link_token';

            return $respInfo;
        }
    }

    /**
     * Authenticate a bank account (AJAX).
     *
     * @param  Request  $request
     * @return stdClass $authInfo
     *
     */
    public function authenticateBankAccount(Request $request) {
        $authInfo = app()->make('stdClass');

        if (($response = $this->checkPlaidKeys()) != 'success') {
            $authInfo->status = 'error';
            $authInfo->msg = $response;

            return json_encode($authInfo);
        }

        $headerFields = array(
            'Content-Type: application/json',
            'Plaid-Version: ' . $this->api_version
        );

        $postFields = app()->make('stdClass');
        $postFields->public_token = $request->public_token;
        $postFields->client_id = $this->client_id;
        $postFields->secret = $this->getSecret();

        $curl = new Curl('POST', $this->getAPIHost() . '/item/public_token/exchange', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);

                $accessToken = $response->result->access_token;

                $options = app()->make('stdClass');
                $options->account_ids = [$request->account_id];

                $postFields = app()->make('stdClass');
                $postFields->access_token = $accessToken;
                $postFields->client_id = $this->client_id;
                $postFields->secret = $this->getSecret();
                $postFields->options = $options;

                $curl = new Curl('POST', $this->getAPIHost() . '/auth/get', $headerFields, json_encode($postFields));
                $response = $curl->exec();

                if ($response->result !== false) {
                    if ($response->http_code == 200) {
                        $response->result = json_decode($response->result);

                        $authInfo->status = 'success';
                        $authInfo->msg = 'Bank account was authenticated.';
                        $authInfo->access_token = $accessToken;
                        $authInfo->item_id = $response->result->item->item_id;
                        $authInfo->request_id = $response->result->request_id;
                        $authInfo->bank_name = $request->bank_name;

                        if (count($response->result->accounts) == 1) {
                            $authInfo->account_name = $response->result->accounts[0]->name;
                            $authInfo->account_type = $response->result->accounts[0]->subtype;
                        }

                        if (count($response->result->numbers->ach) == 1) {
                            $authInfo->account = $response->result->numbers->ach[0]->account;
                            $authInfo->account_id = $response->result->numbers->ach[0]->account_id;
                            $authInfo->routing = $response->result->numbers->ach[0]->routing;

                            if (!empty($response->result->numbers->ach[0]->wire_routing))
                                $authInfo->wire_routing = $response->result->numbers->ach[0]->wire_routing;
                            else
                                $authInfo->wire_routing = "";
                        }

                        $bankAcct = new BankAccount();

                        if ($request->mode == 'add') {
                            $result = $bankAcct->addBankAccount($authInfo);
                            $authInfo->bank_acct_id = $result[1];
                        } else {  // edit
                            $bank_account = $bankAcct->getBankAccount($request->bankAccountId);

                            if ($bank_account !== null && $bank_account->accountNumber != $authInfo->account) {
                                $response = $this->removeItem($bank_account->access_token);

                                if ($response->status == 'success' && $response->removed == true) {
                                    if (!empty($bank_account->funding_source_url)) {
                                        $dwolla = new Dwolla();
                                        $dwollaResp = $dwolla->removeFundingSource($bank_account->funding_source_url);

                                        if ($dwollaResp->http_code == 200 && $dwollaResp->result->removed == true) {
                                            $authInfo->bankAccountId = $request->bankAccountId;
                                            $bankAcct->updateBankAccount($authInfo);
                                            unset($authInfo->bankAccountId);
                                        } else {
                                            $respInfo = app()->make('stdClass');

                                            $respInfo->status = 'error';
                                            $respInfo->msg = 'Dwolla removeFundingSource() returned error.';
                                            $respInfo->error = $dwolla->getErrorMessage($dwollaResp->result, 'funding source');

                                            return json_encode($respInfo);
                                        }
                                    } else {
                                        $authInfo->bankAccountId = $request->bankAccountId;
                                        $bankAcct->updateBankAccount($authInfo);
                                        unset($authInfo->bankAccountId);
                                    }
                                } else
                                    return json_encode($response);
                            }
                        }

                        // Do not pass the access_token, item_id and account_id back to the client
                        unset($authInfo->access_token);
                        unset($authInfo->item_id);
                        unset($authInfo->account_id);

                        return json_encode($authInfo);
                    } else {
                        if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                            $response->result = json_decode($response->result);

                        $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                        $authInfo->status = 'error';
                        $authInfo->msg = 'POST /auth/get returned error.';
                        $authInfo->error = $response->result;

                        return json_encode($authInfo);
                    }
                } else {
                    $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                    $authInfo->status = 'error';
                    $authInfo->msg = 'POST /auth/get returned false.';

                    return json_encode($authInfo);
                }
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $authInfo->status = 'error';
                $authInfo->msg = 'POST /item/public_token/exchange returned error.';
                $authInfo->error = $response->result;

                return json_encode($authInfo);
            }
        } else {
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

            $authInfo->status = 'error';
            $authInfo->msg = 'POST /item/public_token/exchange returned false.';

            return json_encode($authInfo);
        }
    }

    /**
     * Create a public token
     *
     * @param  string   $access_token
     * @return stdClass $respInfo
     *
     */
    public function createPublicToken($access_token) {
        $respInfo = app()->make('stdClass');

        if (($response = $this->checkPlaidKeys()) != 'success') {
            $respInfo->status = 'error';
            $respInfo->msg = $response; 

            return $respInfo;
        }

        if ($access_token === null) {
            $respInfo->status = 'error';
            $respInfo->msg = 'Plaid access_token is null.';

            return $respInfo;
        }

        $headerFields = array(
            'Content-Type: application/json',
            'Plaid-Version: ' . $this->api_version
        );

        $postFields = app()->make('stdClass');
        $postFields->client_id = $this->client_id;
        $postFields->secret = $this->getSecret();
        $postFields->access_token = $access_token;

        $curl = new Curl('POST', $this->getAPIHost() . '/item/public_token/create', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);

                $respInfo->status = 'success';
                $respInfo->msg = 'Public token was created.';
                $respInfo->public_token = $response->result->public_token;

                return $respInfo;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respInfo->status = 'error';
                $respInfo->msg = 'POST /item/public_token/create returned error.';
                $respInfo->error = $response->result;

                return $respInfo;
            }
        } else {
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

            $respInfo->status = 'error';
            $respInfo->msg = 'POST /item/public_token/create returned false.';

            return $respInfo;
        }
    }

    /**
     * Remove an item
     *
     * @param  string   $access_token
     * @return stdClass $respInfo
     *
     */
    public function removeItem($access_token) {
        $respInfo = app()->make('stdClass');

        if (($response = $this->checkPlaidKeys()) != 'success') {
            $respInfo->status = 'error';
            $respInfo->msg = $response;

            return $respInfo;
        }

        if ($access_token === null) {
            $respInfo->status = 'error';
            $respInfo->msg = 'Plaid access_token is null.';

            return $respInfo;
        }

        $headerFields = array(
            'Content-Type: application/json',
            'Plaid-Version: ' . $this->api_version
        );

        $postFields = app()->make('stdClass');
        $postFields->client_id = $this->client_id;
        $postFields->secret = $this->getSecret();
        $postFields->access_token = $access_token;

        $curl = new Curl('POST', $this->getAPIHost() . '/item/remove', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);

                $respInfo->status = 'success';
                $respInfo->msg = 'Item was removed.';
                $respInfo->removed = $response->result->removed;

                return $respInfo;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respInfo->status = 'error';
                $respInfo->msg = 'POST /item/remove returned error.';
                $respInfo->error = $response->result;

                return $respInfo;
            }
        } else {
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

            $respInfo->status = 'error';
            $respInfo->msg = 'POST /item/remove returned false.';

            return $respInfo;
        }
    }

    /**
     * Create a Dwolla processor token
     *
     * @param  string  $access_token
     * @param  string  $account_id
     * @return stdClass $respInfo
     *
     */
    public function createDwollaProcessorToken($access_token, $account_id) {
        $respInfo = app()->make('stdClass');

        if (($response = $this->checkPlaidKeys()) != 'success') {
            $respInfo->status = 'error';
            $respInfo->msg = $response;

            return $respInfo;
        }

        if (($response = $this->checkBankAcctParameters($access_token, $account_id)) != 'success') {
            $respInfo->status = 'error';
            $respInfo->msg = $response;

            return $respInfo;
        }

        $headerFields = array(
            'Content-Type: application/json',
            'Plaid-Version: ' . $this->api_version
        );

        $postFields = app()->make('stdClass');
        $postFields->client_id = $this->client_id;
        $postFields->secret = $this->getSecret();
        $postFields->access_token = $access_token;
        $postFields->account_id = $account_id;

        $curl = new Curl('POST', $this->getAPIHost() . '/processor/dwolla/processor_token/create', $headerFields, json_encode($postFields));
        $response = $curl->exec();

        if ($response->result !== false) {
            if ($response->http_code == 200) {
                $response->result = json_decode($response->result);

                $respInfo->status = 'success';
                $respInfo->msg = 'Dwolla processor token was created.';
                $respInfo->processor_token = $response->result->processor_token;

                return $respInfo;
            } else {
                if (array_key_exists('content-length', $response->headers) && intval($response->headers['content-length'][0]))
                    $response->result = json_decode($response->result);

                $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

                $respInfo->status = 'error';
                $respInfo->msg = 'POST /processor/dwolla/processor_token/create returned error.';
                $respInfo->error = $response->result;

                return $respInfo;
            }
        } else {
            $this->logError(__FILE__, __LINE__, __FUNCTION__, $response);

            $respInfo->status = 'error';
            $respInfo->msg = 'POST /processor/dwolla/processor_token/create returned false.';

            return $respInfo;
        }
    }

    private function logError($file, $line, $function, $response) {
        $msg = $file . '(' . $line . ') ' . $function . '()';

        if ($response->result !== false)
            Log::error($msg, ['user' => Auth::user()->email, 'result' => $response->result, 'http_code' => $response->http_code]);
        else
            Log::error($msg, ['user' => Auth::user()->email, 'errno' => $response->errno, 'error' => $response->error]);
    }

    private function getSecret() {
        switch ($this->env) {
            case 'sandbox':
                return $this->sandbox_secret;
                break;
            case 'development':
                return $this->development_secret;
                break;
            case 'production':
                return $this->production_secret;
                break;
        }
    }

    private function getClientUserId() {
        return (empty(Auth::user()->subscriberId) ? 'wl-' : 'sub-') . Auth::user()->id;
    }

    private function checkPlaidKeys() {
        $secret = $this->getSecret();

        if (empty($this->client_id) && empty($secret))
            return 'Plaid client_id and ' . $this->env . ' secret are both not set.';
        else if (empty($this->client_id) && !empty($secret))
            return 'Plaid client_id is not set.';
        else if (!empty($this->client_id) && empty($secret))
            return 'Plaid ' . $this->env . ' secret is not set.';
        else
            return 'success';
    }

    private function checkBankAcctParameters($access_token, $account_id) {
        if ($access_token === null && $account_id === null)
            return 'Plaid access_token and account_id are both null.';
        else if ($access_token === null && $account_id !== null)
            return 'Plaid access_token is null.';
        else if ($access_token !== null && $account_id === null)
            return 'Plaid account_id is null.';
        else
            return 'success';
    }

    private function getAPIHost() {
        return 'https://' . $this->env . '.plaid.com';
    }
}