<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\BankAccount;
use App\Transaction;

class WebhookController extends Controller {

    private $event;

    public function __construct() {
        $this->event = new Event();
    }

    public function handleRequest(Request $request) {
        $headers = $request->header();
        $body = $request->getContent();

        if (array_key_exists('x-request-signature-sha-256', $headers)) {
            $proposedSignature = $headers['x-request-signature-sha-256'][0];
            $actualSignature = hash_hmac('sha256', $body, env('DWOLLA_WEBHOOK_SECRET', null));

            if ($proposedSignature == $actualSignature) {
                if (array_key_exists('content-type', $headers) && array_key_exists('content-length', $headers) && $headers['content-type'][0] == 'application/json' && intval($headers['content-length'][0])) {
                    $event = json_decode($body);

                    $this->handleEvent($event);
                    
                    return response('', 200);
                } else
                    return response('', 400);
            } else
                return response('', 400);
        } else
            return response('', 400);
    }

    public function handleEvent($event) {
        if ($this->event->doesntExist($event->id)) {
            if ($this->event->add($event)) {
                switch ($event->topic) {
                    case 'customer_funding_source_removed':
                        $bankAcct = new BankAccount();

                        $updatedRows = $bankAcct->removeFundingSource($event->_links->resource->href);
                        break;
                    case 'customer_transfer_cancelled':
                        $transaction = new Transaction();

                        $updatedRows = $transaction->updateStatus($event->_links->resource->href, 'cancelled');
                        break;
                    case 'customer_transfer_failed':
                        $transaction = new Transaction();

                        $updatedRows = $transaction->updateStatus($event->_links->resource->href, 'failed');
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
}