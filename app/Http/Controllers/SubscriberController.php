<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\BankAccount;
use App\Plaid;
use App\Dwolla;
use App\Transaction;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller {

    public function dwollaSetup(Request $request) {
        $dwolla = new Dwolla();
        $subscribers = new Subscribers();

        if ($request->isMethod('post')) {
            if (empty(Auth::user()->dwollaLocationResource)) {  // Create
                $response = $dwolla->createReceiveOnlyUser($request);

                if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                    $updatedRows = $subscribers->saveDwollaInfo($response->headers['location'][0]);

                    if ($updatedRows)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully added a Receive-only User.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A new Receive-only User was unable to be created at this time.');
                } else {
                    return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($response->result));
                }
            } else {  // Update
                if ($request->status == 'document' || $request->status == 'suspended')
                    return redirect()->route('inv-settings')->with('status-error', 'Cannot update customer\'s information when in a status of Document or Suspended.');
                else {
                    $response = $dwolla->updateReceiveOnlyUser($request, Auth::user()->dwollaLocationResource);

                    if ($response->http_code == 200)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully updated a Receive-only User.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
                }
            }
        }
    }

    public function resetSubscriber($subscriberId) {
        $subscribers = new Subscribers();

        $updatedRows = $subscribers->resetSubscriber($subscriberId);

        if ($updatedRows)
            return redirect()->route('inv-settings')->with('status-success', 'You have successfully reset subscriber ID "' . $subscriberId . '".');
        else
            return redirect()->route('inv-settings')->with('status-error', 'The subscriber ID "' . $subscriberId . '" was unable to be reset at this time.');
    }

    public function addBankAccount(Request $request) {
        $settings = new Setting();
        $customization = $settings->getCustomizationSettingsByWlId(Auth::user()->wlId);

        return view('investors.bank-account', array(
            'action' => 'add',
            'bankAcct' => null,
            'customization' => $customization
        ));
    }

    public function editBankAccount(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();
        $settings = new Setting();
        $customization = $settings->getCustomizationSettingsByWlId(Auth::user()->wlId);

        if ($request->isMethod('post')) {
            if (empty($request->accountName))
                return redirect()->route('inv-edit-bank-acct', $bankAccountId)->with('status-error', 'Account Name is required.');

            if ($request['mode'] == 'add') {
                $updatedId = $bankAcct->editBankAccount($request);

                if ($updatedId)
                    return redirect()->route('inv-settings')->with('status-success', 'You have successfully added a bank account.');
                else
                    return redirect()->route('inv-settings')->with('status-error', 'A bank account was unable to be added at this time.');
            } else if ($request['mode'] == 'edit') {
                $bankAccount = $bankAcct->getBankAccount($bankAccountId);

                if (!empty($bankAccount->funding_source_url) && $request->accountName != $request->currentAcctName) {
                    $dwolla = new Dwolla();

                    $bankAcctInfo = app()->make('stdClass');
                    $bankAcctInfo->accountName = $request->accountName;

                    $response = $dwolla->updateFundingSource($bankAccount->funding_source_url, $bankAcctInfo);

                    if ($response->http_code == 200) {
                        $updatedId = $bankAcct->editBankAccount($request);

                        if ($updatedId)
                            return redirect()->route('inv-settings')->with('status-success', 'You have successfully edited a bank account.');
                        else
                            return redirect()->route('inv-settings')->with('status-error', 'A bank account was unable to be edited at this time.');
                    } else {
                        $errorMessage = $dwolla->getErrorMessage($response->result, 'funding source');

                        if ($response->result->code == 'ValidationError')
                            return redirect()->route('inv-edit-bank-acct', $bankAccountId)->with('status-error', $errorMessage);
                        else
                            return redirect()->route('inv-settings')->with('status-error', $errorMessage);
                    }
                } else {
                    $updatedId = $bankAcct->editBankAccount($request);

                    if ($updatedId)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully edited a bank account.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A bank account was unable to be edited at this time.');
                }
            }
        }

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) === null)
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');

        return view('investors.bank-account', array(
            'action' => 'edit',
            'bankAcct' => $bankAccount,
            'customization' => $customization
        ));
    }

    public function deleteBankAccount($bankAccountId) {
        $bankAcct = new BankAccount();
        $plaid = new Plaid();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            $response = $plaid->removeItem($bankAccount->access_token);

            if ($response->status == 'success' && $response->removed == true) {
                if (!empty($bankAccount->funding_source_url)) {
                    $dwollaResp = $dwolla->removeFundingSource($bankAccount->funding_source_url);

                    if ($dwollaResp->http_code == 200 && $dwollaResp->result->removed == true) {
                        $updatedRows = $bankAcct->deleteBankAccount($bankAccountId, true);

                        if ($updatedRows)
                            return redirect()->route('inv-settings')->with('status-success', 'You have successfully deleted a bank account.');
                        else
                            return redirect()->route('inv-settings')->with('status-error', 'A bank account was unable to be deleted at this time.');
                    } else
                        return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($dwollaResp->result, 'funding source'));
                } else {
                    $updatedRows = $bankAcct->deleteBankAccount($bankAccountId);

                    if ($updatedRows)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully deleted a bank account.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A bank account was unable to be deleted at this time.');
                }
            } else
                return redirect()->route('inv-settings')->with('status-error', 'removeItem() returned the following error: ' . $response->error->error_type . '.');
        } else
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function createFundingSource($bankAccountId) {
        $bankAcct = new BankAccount();
        $plaid = new Plaid();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            $response = $plaid->createDwollaProcessorToken($bankAccount->access_token, $bankAccount->account_id);

            if ($response->status == 'success') {
                $dwollaResp = $dwolla->createFundingSourceWithPlaidToken(Auth::user()->dwollaLocationResource, $response->processor_token, $bankAccount->accountName);

                if ($dwollaResp->http_code == 201 && array_key_exists('location', $dwollaResp->headers)) {
                    $updatedRowId = $bankAcct->setFundingSourceURL($bankAccount->id, $dwollaResp->headers['location'][0]);

                    if ($updatedRowId)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully created a new funding source.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A new funding source was unable to be created at this time.');
                } else {
                    return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($dwollaResp->result, 'funding source'));
                }
            } else
                return redirect()->route('inv-settings')->with('status-error', 'createDwollaProcessorToken() returned the following error: ' . $response->error->error_type . '.');
        } else
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function removeFundingSource($bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            if (!empty($bankAccount->funding_source_url)) {
                $response = $dwolla->removeFundingSource($bankAccount->funding_source_url);

                if ($response->http_code == 200 && $response->result->removed == true) {
                    $updatedRows = $bankAcct->deleteFundingSourceURL($bankAccountId);

                    if ($updatedRows)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully removed a funding source.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be removed at this time.');
                } else {
                    return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
                }
            } else
                return redirect()->route('inv-settings')->with('status-error', 'Funding source URL is empty.');
        } else
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function addFundingSource(Request $request) {
        $settings = new Setting();

        if (empty(Auth::user()->dwollaLocationResource))
            return redirect()->route('inv-settings')->with('status-error', 'In order to add a funding source, you must first create a Dwolla receive-only User.');

        if ($request->isMethod('post')) {
            $bankAcct = new BankAccount();
            $dwolla = new Dwolla();

            $response = $dwolla->createFundingSource(Auth::user()->dwollaLocationResource, $request);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                $request['fundingSourceURL'] = $response->headers['location'][0];

                $insertedRowId = $bankAcct->addFundingSource($request);

                if ($insertedRowId)
                    return redirect()->route('inv-settings')->with('status-success', 'You have successfully added a funding source.');
                else
                    return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be created at this time.');
            } else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'funding source');

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('inv-add-funding-src')->with('status-error', $errorMessage);
                else
                    return redirect()->route('inv-settings')->with('status-error', $errorMessage);
            }
        }

        $customization = $settings->getCustomizationSettingsByWlId(Auth::user()->wlId);

        return view('investors.create-funding-source', array(
            'customization' => $customization
        ));
    }

    public function editFundingSource(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();
        $settings = new Setting();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) === null)
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');

        if (!empty($bankAccount->funding_source_url)) {
            $response = $dwolla->retrieveFundingSource($bankAccount->funding_source_url);

            if ($response->http_code == 200)
                $fundingSource = $response->result;
            else
                return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
        } else
            $fundingSource = app()->make('stdClass');

        if ($request->isMethod('post')) {
            if (!empty($bankAccount->funding_source_url)) {
                $updatesMade = false;

                if ($fundingSource->status == 'verified' && $request->accountName != $bankAccount->accountName) {
                    $bankAcctInfo = app()->make('stdClass');
                    $bankAcctInfo->accountName = $request->accountName;

                    $updatesMade = true;
                } else if ($fundingSource->status == 'unverified') {
                    if ($request->accountName != $bankAccount->accountName || $request->accountType != $bankAccount->accountType ||
                    $request->abaRoutingTransitNumber != $bankAccount->abaRoutingTransitNumber || $request->accountNumber != $bankAccount->accountNumber) {
                        $bankAcctInfo = app()->make('stdClass');
                        $bankAcctInfo->accountName = $request->accountName;

                        if ($request->accountType != $bankAccount->accountType)
                            $bankAcctInfo->accountType = $request->accountType;

                        if ($request->abaRoutingTransitNumber != $bankAccount->abaRoutingTransitNumber)
                            $bankAcctInfo->abaRoutingTransitNumber = $request->abaRoutingTransitNumber;

                        if ($request->accountNumber != $bankAccount->accountNumber)
                            $bankAcctInfo->accountNumber = $request->accountNumber;

                        $updatesMade = true;
                    }
                }

                if ($updatesMade) {
                    $response = $dwolla->updateFundingSource($bankAccount->funding_source_url, $bankAcctInfo);

                    if ($response->http_code == 200) {
                        $updatedId = $bankAcct->editBankAccount($request);

                        if ($updatedId)
                            return redirect()->route('inv-settings')->with('status-success', 'You have successfully edited a funding source.');
                        else
                            return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be edited at this time.');
                    } else {
                        $errorMessage = $dwolla->getErrorMessage($response->result);

                        if ($response->result->code == 'ValidationError')
                            return redirect()->route('inv-edit-funding-src', $bankAccountId)->with('status-error', $errorMessage);
                        else
                            return redirect()->route('inv-settings')->with('status-error', $errorMessage);
                    }
                } else {
                    $updatedId = $bankAcct->editBankAccount($request);

                    if ($updatedId)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully edited a funding source.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be edited at this time.');
                }
            } else {
                $updatedId = $bankAcct->editBankAccount($request);

                if ($updatedId)
                    return redirect()->route('inv-settings')->with('status-success', 'You have successfully edited a funding source.');
                else
                    return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be edited at this time.');
            }
        }

        $customization = $settings->getCustomizationSettingsByWlId(Auth::user()->wlId);

        return view('investors.edit-funding-source', array(
            'bankAcct' => $bankAccount,
            'fundingSource' => $fundingSource,
            'customization' => $customization
        ));
    }

    public function deleteFundingSource($bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            if (!empty($bankAccount->funding_source_url)) {
                $response = $dwolla->removeFundingSource($bankAccount->funding_source_url);

                if ($response->http_code == 200 && $response->result->removed == true) {
                    $updatedRows = $bankAcct->deleteBankAccount($bankAccountId, true);

                    if ($updatedRows)
                        return redirect()->route('inv-settings')->with('status-success', 'You have successfully deleted a funding source.');
                    else
                        return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be deleted at this time.');
                } else
                    return redirect()->route('inv-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
            } else {
                $updatedRows = $bankAcct->deleteBankAccount($bankAccountId);

                if ($updatedRows)
                    return redirect()->route('inv-settings')->with('status-success', 'You have successfully deleted a funding source.');
                else
                    return redirect()->route('inv-settings')->with('status-error', 'A funding source was unable to be deleted at this time.');
            }
        } else
            return redirect()->route('inv-settings')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function transactions(Request $request) {
        $dwolla = new Dwolla();
        $transaction = new Transaction();
        $settings = new Setting();
        $pageNumber = intval($request->query('page'));
        $customization = $settings->getCustomizationSettingsByWlId(Auth::user()->wlId);

        if ($pageNumber == 0 || $pageNumber == 1)
            $offset = 0;
        else
            $offset = ($pageNumber - 1) * 10;

        $transactions = $transaction->getTransactionsBySubscriberId();

        if ($transactions->count()) {
            $response = $dwolla->listInvestorTransfers(Auth::user()->dwollaLocationResource, $offset);

            if ($response->http_code == 200 && !empty($response->result->_embedded->transfers)) {
                foreach ($transactions as $transaction) {
                    foreach ($response->result->_embedded->transfers as $transfer) {
                        if ($transaction->transferResource == $transfer->_links->self->href) {
                            $transaction->status = $transfer->status;

                            break;
                        }
                    }
                }
            } else {
                if (Auth::user()->remember_token === null)
                    $route = 'inv-settings';
                else
                    $route = 'inv-invest';

                return redirect()->route($route)->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
            }
        }

        return view('investors.transactions', array(
            'customization' => $customization,
            'transactions' => $transactions
        ));
    }

    public function authenticateBankAccount(Request $request) {
        $plaid = new Plaid();
        return $plaid->authenticateBankAccount($request);
    }

    public function createLinkToken() {
        $plaid = new Plaid();
        return json_encode($plaid->createLinkToken());
    }
}
