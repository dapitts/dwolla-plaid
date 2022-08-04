<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\BankAccount;
use App\Plaid;
use App\Dwolla;
use App\CustomerDoc;
use App\BeneficialOwnerDoc;
use App\Transaction;
use App\Webhook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class WhitelabelController extends Controller {

    public function paymentSettings() {
        if (!empty(Auth::user()->dwollaLocationResource)) {
            $dwolla = new Dwolla();

            $response = $dwolla->retrieveCustomer(Auth::user()->dwollaLocationResource);

            if ($response->http_code == 200)
                $custInfo = $response->result;
            else
                return redirect()->route('wl-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
        } else
            $custInfo = app()->make('stdClass');

        if (!empty(Auth::user()->dwollaBusinessType))
            $businessTypeName = $dwolla->getBusinessTypeName(Auth::user()->dwollaBusinessType);
        else
            $businessTypeName = null;

        return view('whitelabels.payment-settings', array(
            'custInfo' => $custInfo,
            'businessTypeName' => $businessTypeName,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function createCustomer(Request $request) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = $dwolla->createVerifiedBusinessCustomer($request);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                $updatedRows = $this->whitelabelUtilities->saveDwollaInfo($response->headers['location'][0], $request);

                if ($updatedRows)
                    return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully added a business verified Customer.');
                else
                    return redirect('/whitelabel/settings/create-customer#step-3')->with('status-error', 'A new business verified Customer was unable to be created at this time.');
            } else {
                $errorMessage = $dwolla->getErrorMessage($response->result);

                if ($response->result->code == 'ValidationError')
                    return redirect('/whitelabel/settings/create-customer#step-3')->withInput()->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-payment-settings')->with('status-error', $errorMessage);
            }
        }

        $busClassifications = $dwolla->getBusinessClassifications();

        return view('whitelabels.create-customer', array(
            'busClassifications' => $busClassifications,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function updateCustomer(Request $request) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = $dwolla->updateVerifiedBusinessCustomer($request, Auth::user()->dwollaLocationResource);

            if ($response->http_code == 200)
                return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully updated a business verified Customer.');
            else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'customer');

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-update-cust')->withInput()->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-payment-settings')->with('status-error', $errorMessage);
            }
        }

        $response = $dwolla->retrieveCustomer(Auth::user()->dwollaLocationResource);

        if ($response->http_code == 200)
            $custInfo = $response->result;
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));

        return view('whitelabels.edit-customer', array(
            'custInfo' => $custInfo,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function reactivateCustomer($customerId) {
        $dwolla = new Dwolla();

        $response = $dwolla->updateCustomer($customerId, 'reactivated');

        if ($response->http_code == 200)
            return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully reactivated a customer.');
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
    }

    public function deactivateCustomer($customerId) {
        $dwolla = new Dwolla();

        $response = $dwolla->updateCustomer($customerId, 'deactivated');

        if ($response->http_code == 200)
            return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully deactivated a customer.');
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
    }

    public function suspendCustomer($customerId) {
        $dwolla = new Dwolla();

        $response = $dwolla->updateCustomer($customerId, 'suspended');

        if ($response->http_code == 200)
            return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully suspended a customer.');
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
    }

    public function resetWhitelabel($wlId) {
        $updatedRows = $this->whitelabelUtilities->resetWhitelabel($wlId);

        if ($updatedRows)
            return redirect()->route('wl-settings')->with('status-success', 'You have successfully reset whitelabel ID "' . $wlId . '".');
        else
            return redirect()->route('wl-settings')->with('status-error', 'The whitelabel ID "' . $wlId . '" was unable to be reset at this time.');
    }

    public function retryCustomer(Request $request) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = $dwolla->retryVerifiedBusinessCustomer($request, Auth::user()->dwollaLocationResource);

            if ($response->http_code == 200) {
                if (Auth::user()->dwollaBusinessType != intval($request->businessType)) {
                    $updatedRows = $this->whitelabelUtilities->updateDwollaBusType($request->businessType);

                    if ($updatedRow)
                        return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully created a business verified Customer (retry).');
                    else
                        return redirect()->route('wl-payment-settings')->with('status-error', 'A new business verified Customer (retry) was unable to be created at this time.');
                } else
                    return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully created a business verified Customer (retry).');
            } else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'customer');

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-retry-cust')->withInput()->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-payment-settings')->with('status-error', $errorMessage);
            }
        }

        $response = $dwolla->retrieveCustomer(Auth::user()->dwollaLocationResource);

        if ($response->http_code == 200)
            $custInfo = $response->result;
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));

        $busClassifications = $dwolla->getBusinessClassifications();

        return view('whitelabels.retry-customer', array(
            'busClassifications' => $busClassifications,
            'custInfo' => $custInfo,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function beneficialOwners() {
        $dwolla = new Dwolla();

        $beneficialOwners = $dwolla->listBeneficialOwners(Auth::user()->dwollaLocationResource);

        $response = $dwolla->retrieveCustomer(Auth::user()->dwollaLocationResource);

        if ($response->http_code == 200)
            $custInfo = $response->result;
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));

        return view('whitelabels.beneficial-owners', array(
            'custInfo' => $custInfo,
            'beneficialOwners' => $beneficialOwners,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function certifyOwnership(Request $request) {
        $dwolla = new Dwolla();

        $beneficialOwnersCount = intval($request->beneficialOwnersCount);
        $beneficialOwnersVerified = intval($request->beneficialOwnersVerified);
        $certifyOwnership = !empty($request->certifyOwnership) ? intval($request->certifyOwnership) : 0;

        // No beneficial owners but the certify ownership checkbox is checked
        if (!$beneficialOwnersCount && $certifyOwnership)
            return redirect()->route('wl-beneficial-owners')->with('status-error', 'In order to certify beneficial ownership, the certify ownership checkbox must not be checked when there are no beneficial owners.');

        // There are beneficial owners but the certify ownership checkbox is not checked
        if ($beneficialOwnersCount && !$certifyOwnership)
            return redirect()->route('wl-beneficial-owners')->with('status-error', 'In order to certify beneficial ownership, the certify ownership checkbox must be checked when there are beneficial owners.');

        // There are beneficial owners, the certify ownership checkbox is checked, but one or more of the beneficial owners have a status of either incomplete or document
        if ($beneficialOwnersCount && $certifyOwnership && !$beneficialOwnersVerified)
            return redirect()->route('wl-beneficial-owners')->with('status-error', 'In order to certify beneficial ownership, all of the beneficial owners must have a status of Verified.');

        if ((!$beneficialOwnersCount && !$certifyOwnership) || ($beneficialOwnersCount && $certifyOwnership && $beneficialOwnersVerified)) {
            $response = $dwolla->certifyOwnership(Auth::user()->dwollaLocationResource);

            if ($response->http_code == 200 && $response->result->status == 'certified')
                return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully certified beneficial ownership.');
            else
                return redirect()->route('wl-beneficial-owners')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
        }
    }

    public function certifyCustomerOwnership($customerId) {
        $dwolla = new Dwolla();

        $response = $dwolla->certifyCustomerOwnership($customerId);

        if ($response->http_code == 200 && $response->result->status == 'certified')
            return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully certified beneficial ownership.');
        else
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
    }

    public function createBeneficialOwner(Request $request) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = $dwolla->createBeneficialOwner($request, Auth::user()->dwollaLocationResource);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers))
                return redirect()->route('wl-beneficial-owners')->with('status-success', 'You have successfully added a beneficial owner.');
            else {
                $errorMessage = $dwolla->getErrorMessage($response->result);

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-create-beneficial')->withInput()->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-beneficial-owners')->with('status-error', $errorMessage);
            }
        }

        return view('whitelabels.create-beneficial-owner', array(
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function updateBeneficialOwner(Request $request, $beneficialOwnerId) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = $dwolla->updateBeneficialOwner($request, $beneficialOwnerId);

            if ($response->http_code == 200)
                return redirect()->route('wl-beneficial-owners')->with('status-success', 'You have successfully updated a beneficial owner.');
            else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'beneficial owner');

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-update-beneficial', $beneficialOwnerId)->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-beneficial-owners')->with('status-error', $errorMessage);
            }
        }

        $response = $dwolla->retrieveBeneficialOwner($beneficialOwnerId);

        if ($response->http_code == 200)
            $beneficialOwner = $response->result;
        else
            return redirect()->route('wl-beneficial-owners')->with('status-error', $dwolla->getErrorMessage($response->result, 'beneficial owner'));

        return view('whitelabels.update-beneficial-owner', array(
            'beneficialOwner' => $beneficialOwner,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function deleteBeneficialOwner($beneficialOwnerId) {
        $dwolla = new Dwolla();

        $response = $dwolla->deleteBeneficialOwner($beneficialOwnerId);

        if ($response->http_code == 200)
            return redirect()->route('wl-beneficial-owners')->with('status-success', 'You have successfully deleted a beneficial owner.');
        else
            return redirect()->route('wl-beneficial-owners')->with('status-error', $dwolla->getErrorMessage($response->result, 'beneficial owner'));
    }

    public function beneficialOwnerDocs($beneficialOwnerId) {
        $dwolla = new Dwolla();
        $beneficialOwnerDoc = new BeneficialOwnerDoc();

        $beneficial_docs = $beneficialOwnerDoc->getBeneficialOwnerDocs($beneficialOwnerId);

        if ($beneficial_docs->count()) {
            $response = $dwolla->listBeneficialOwnerDocs($beneficialOwnerId);

            if ($response->http_code == 200 && !empty($response->result->_embedded->documents)) {
                foreach ($beneficial_docs as $beneficial_doc) {
                    foreach ($response->result->_embedded->documents as $doc) {
                        if ($beneficial_doc->documentResource == $doc->_links->self->href) {
                            $beneficial_doc->status = $doc->status;

                            if (!empty($doc->failureReason))
                                $beneficial_doc->failureReason = $doc->failureReason;
                            break;
                        }
                    }
                }
            } else {
                return redirect()->route('wl-beneficial-owners')->with('status-error', $dwolla->getErrorMessage($response->result, 'beneficial owner'));
            }
        }

        return view('whitelabels.beneficial-documents', array(
            'beneficialOwnerId' => $beneficialOwnerId,
            'beneficialDocs' => $beneficial_docs,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function uploadBeneficialOwnerDocs(Request $request, $beneficialOwnerId) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $beneficialOwnerDoc = new BeneficialOwnerDoc();

            $realPath = $request->file('beneficialDoc')->getRealPath();
            $mimeType = $request->file('beneficialDoc')->getMimeType();
            $originalName = $request->file('beneficialDoc')->getClientOriginalName();

            $response = $dwolla->uploadBeneficialOwnerDocument($request->documentType, $realPath, $mimeType, $originalName, $beneficialOwnerId);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                $insertedRowId = $beneficialOwnerDoc->addBeneficialOwnerDoc($beneficialOwnerId, $response->headers['location'][0], $request->documentType, $originalName, $mimeType);

                if ($insertedRowId)
                    return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-success', 'You have successfully uploaded a beneficial owner document.');
                else
                    return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-error', 'A beneficial owner document was unable to be uploaded at this time.');
            } else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'beneficial owner');

                switch ($response->result->code) {
                    case 'ValidationError':
                    case 'FileTooLarge':
                        return redirect()->route('wl-upload-beneficial', $beneficialOwnerId)->with('status-error', $errorMessage);
                        break;
                    case 'InvalidResourceState':
                        return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-error', 'Resource cannot be modified. Document creation not allowed for already verified Customers or non-verified Customer types.');
                        break;
                    case 'NotAuthorized':
                        return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-error', 'Not authorized to create documents.');
                        break;
                    default:
                        return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-error', $errorMessage);
                        break;
                }
            }
        }

        $response = $dwolla->retrieveBeneficialOwner($beneficialOwnerId);

        if ($response->http_code == 200)
            $beneficialOwner = $response->result;
        else
            return redirect()->route('wl-beneficial-documents', $beneficialOwnerId)->with('status-error', $dwolla->getErrorMessage($response->result, 'beneficial owner'));

        return view('whitelabels.upload-beneficial-docs', array(
            'beneficialOwner' => $beneficialOwner,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function customerDocuments() {
        $dwolla = new Dwolla();
        $customerDoc = new CustomerDoc();

        $cust_docs = $customerDoc->getCustomerDocs();

        if ($cust_docs->count()) {
            $response = $dwolla->listCustomerDocuments(Auth::user()->dwollaLocationResource);

            if ($response->http_code == 200 && !empty($response->result->_embedded->documents)) {
                foreach ($cust_docs as $cust_doc) {
                    foreach ($response->result->_embedded->documents as $doc) {
                        if ($cust_doc->documentResource == $doc->_links->self->href) {
                            $cust_doc->status = $doc->status;

                            if (!empty($doc->failureReason))
                                $cust_doc->failureReason = $doc->failureReason;
                            break;
                        }
                    }
                }
            } else {
                return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
            }
        }

        return view('whitelabels.customer-documents', array(
            'customerDocs' => $cust_docs,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function uploadCustomerDocuments(Request $request) {
        $dwolla = new Dwolla();

        if ($request->isMethod('post')) {
            $response = null;

            switch ($request->uploadType) {
                case 'verify-controller':
                    $realPath = $request->file('controllerDoc')->getRealPath();
                    $mimeType = $request->file('controllerDoc')->getMimeType();
                    $originalName = $request->file('controllerDoc')->getClientOriginalName();

                    $response = $dwolla->uploadCustomerDocument($request->documentType, $realPath, $mimeType, $originalName, Auth::user()->dwollaLocationResource);

                    if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                        $customerDoc = new CustomerDoc();
                        $insertedRowId = $customerDoc->addCustomerDoc($response->headers['location'][0], $request->documentType, $originalName, $mimeType);

                        if ($insertedRowId)
                            return redirect()->route('wl-customer-documents')->with('status-success', 'You have successfully uploaded a customer document.');
                        else
                            return redirect()->route('wl-customer-documents')->with('status-error', 'A customer document was unable to be uploaded at this time.');
                    }

                    break;
                case 'verify-business':
                    $fileCount = count($request->file('businessDocs'));
                    $realPath = '';
                    $mimeType = '';
                    $originalName = '';
                    $failure = false;

                    for ($i = 0; $i < $fileCount; $i++) {
                        $realPath = $request->file('businessDocs')[$i]->getRealPath();
                        $mimeType = $request->file('businessDocs')[$i]->getMimeType();
                        $originalName = $request->file('businessDocs')[$i]->getClientOriginalName();

                        $response = $dwolla->uploadCustomerDocument($request->documentType, $realPath, $mimeType, $originalName, Auth::user()->dwollaLocationResource);

                        if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                            $customerDoc = new CustomerDoc();
                            $insertedRowId = $customerDoc->addCustomerDoc($response->headers['location'][0], $request->documentType, $originalName, $mimeType);

                            if ($insertedRowId)
                                continue;
                            else
                                return redirect()->route('wl-customer-documents')->with('status-error', 'A customer document was unable to be uploaded at this time.');
                        } else {
                            $failure = true;
                            break;
                        }
                    }

                    if (!$failure) {
                        if ($i == 1)
                            return redirect()->route('wl-customer-documents')->with('status-success', 'You have successfully uploaded a customer document.');
                        else
                            return redirect()->route('wl-customer-documents')->with('status-success', 'You have successfully uploaded ' . $i . ' customer documents.');
                    }

                    break;
                case 'verify-cntlr-and-business':
                    $realPath = '';
                    $mimeType = '';
                    $originalName = '';

                    $realPath = $request->file('controllerDoc')->getRealPath();
                    $mimeType = $request->file('controllerDoc')->getMimeType();
                    $originalName = $request->file('controllerDoc')->getClientOriginalName();

                    $response = $dwolla->uploadCustomerDocument($request->cntlrDocumentType, $realPath, $mimeType, $originalName, Auth::user()->dwollaLocationResource);

                    if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                        $customerDoc = new CustomerDoc();
                        $insertedRowId = $customerDoc->addCustomerDoc($response->headers['location'][0], $request->cntlrDocumentType, $originalName, $mimeType);

                        if ($insertedRowId) {
                            $fileCount = count($request->file('businessDocs'));
                            $failure = false;

                            for ($i = 0; $i < $fileCount; $i++) {
                                $realPath = $request->file('businessDocs')[$i]->getRealPath();
                                $mimeType = $request->file('businessDocs')[$i]->getMimeType();
                                $originalName = $request->file('businessDocs')[$i]->getClientOriginalName();

                                $response = $dwolla->uploadCustomerDocument($request->busDocumentType, $realPath, $mimeType, $originalName, Auth::user()->dwollaLocationResource);

                                if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                                    $customerDoc = new CustomerDoc();
                                    $insertedRowId = $customerDoc->addCustomerDoc($response->headers['location'][0], $request->busDocumentType, $originalName, $mimeType);

                                    if ($insertedRowId)
                                        continue;
                                    else
                                        return redirect()->route('wl-customer-documents')->with('status-error', 'A customer document was unable to be uploaded at this time.');
                                } else {
                                    $failure = true;
                                    break;
                                }
                            }

                            if (!$failure)
                                return redirect()->route('wl-customer-documents')->with('status-success', 'You have successfully uploaded ' . ($i + 1) . ' customer documents.');
                        } else
                            return redirect()->route('wl-customer-documents')->with('status-error', 'A customer document was unable to be uploaded at this time.');
                    }

                    break;
            }

            $errorMessage = $dwolla->getErrorMessage($response->result, 'customer');

            switch ($response->result->code) {
                case 'ValidationError':
                case 'FileTooLarge':
                    return redirect()->route('wl-upload-cust-docs')->with('status-error', $errorMessage);
                    break;
                case 'InvalidResourceState':
                    return redirect()->route('wl-customer-documents')->with('status-error', 'Resource cannot be modified. Document creation not allowed for already verified Customers or non-verified Customer types.');
                    break;
                case 'NotAuthorized':
                    return redirect()->route('wl-customer-documents')->with('status-error', 'Not authorized to create documents.');
                    break;
                default:
                    return redirect()->route('wl-customer-documents')->with('status-error', $errorMessage);
                    break;
            }
        }

        $response = $dwolla->retrieveCustomer(Auth::user()->dwollaLocationResource);

        if ($response->http_code == 200)
            $custInfo = $response->result;
        else
            return redirect()->route('wl-customer-documents')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));

        return view('whitelabels.upload-customer-docs', array(
            'custInfo' => $custInfo,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function fundingSources(Request $request) {
        $dwolla = new Dwolla();
        $bankAcct = new BankAccount();
        $bankAccts = $bankAcct->getFundingSrcBankAccts();
        $fundingSourceURL = null;
        $dwollaBalance = null;

        $response = $dwolla->listCustomerFundingSources(Auth::user()->dwollaLocationResource);

        if ($response->http_code == 200 && !empty($response->result->_embedded)) {
            foreach ($response->result->_embedded->{'funding-sources'} as $fundingSource) {
                if ($fundingSource->type == 'balance') {
                    $fundingSourceURL = $fundingSource->_links->self->href;
                    break;
                }
            }

            if (!empty($fundingSourceURL)) {
                $dwollaResp = $dwolla->retrieveFundingSourceBalance($fundingSourceURL);

                if ($dwollaResp->http_code == 200)
                    $dwollaBalance = $dwollaResp->result;
                else
                    return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($dwollaResp->result, 'funding source'));
            }
        } else {
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
        }

        return view('whitelabels.funding-sources', array(
            'bankAccts' => $bankAccts,
            'dwollaBalance' => $dwollaBalance,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
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
                        return redirect()->route('wl-funding-sources')->with('status-success', 'You have successfully created a new funding source.');
                    else
                        return redirect()->route('wl-funding-sources')->with('status-error', 'A new funding source was unable to be created at this time.');
                } else {
                    return redirect()->route('wl-funding-sources')->with('status-error', $dwolla->getErrorMessage($dwollaResp->result, 'funding source'));
                }
            } else
                return redirect()->route('wl-funding-sources')->with('status-error', 'createDwollaProcessorToken() returned the following error: ' . $response->error->error_type . '.');
        } else
            return redirect()->route('wl-funding-sources')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
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
                        return redirect()->route('wl-funding-sources')->with('status-success', 'You have successfully removed a funding source.');
                    else
                        return redirect()->route('wl-funding-sources')->with('status-error', 'A funding source was unable to be removed at this time.');
                } else {
                    return redirect()->route('wl-funding-sources')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
                }
            } else
                return redirect()->route('wl-funding-sources')->with('status-error', 'Funding source URL is empty.');
        } else
            return redirect()->route('wl-funding-sources')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function transferFunds(Request $request) {
        $dwolla = new Dwolla();
        $bankAcct = new BankAccount();
        $subscribers = new Subscribers();
        $custodian = new Custodian();
        $transaction = new Transaction();
        $transferInfo = app()->make('stdClass');

        if ($request->isMethod('post')) {
            if (($deal = $this->dealUtilities->getDealByPrjId($request->projectId)) !== null) {
                if (!empty($deal->bankAccountId)) {
                    if (($srcBankAccount = $bankAcct->getBankAccount($deal->bankAccountId)) !== null) {
                        if (!empty($srcBankAccount->funding_source_url)) {
                            if (($subscriber = $subscribers->getSubscriber($request->subscriberId)) !== null) {
                                if (!empty($subscriber->dwollaLocationResource)) {
                                    if (($investment = $this->investmentUtilities->getInvestmentByInvId($request->investmentId)) !== null) {
                                        if (!empty($investment->custodianId)) {
                                            if (($cust = $custodian->getCustodian($investment->custodianId)) !== null) {
                                                if (!empty($cust->bankAccountId)) {
                                                    if (($destBankAccount = $bankAcct->getBankAccount($cust->bankAccountId)) !== null) {
                                                        if (!empty($destBankAccount->funding_source_url)) {
                                                            if (!empty(floatval($request->amount))) {
                                                                $cache_key = hash('sha256', $request->projectId . $request->subscriberId . $request->investmentId);

                                                                if (Cache::has($cache_key))
                                                                    $idempotencyKey = Cache::get($cache_key);
                                                                else {
                                                                    $idempotencyKey = $dwolla->createIdempotencyKey();
                                                                    Cache::put($cache_key, $idempotencyKey, 60 * 24);  // last parameter is minutes (24hr)
                                                                }

                                                                $response = $dwolla->initiateTransfer($srcBankAccount->funding_source_url, $destBankAccount->funding_source_url, $request->amount, $idempotencyKey);

                                                                if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                                                                    Cache::forget($cache_key);

                                                                    $transferInfo->projectId = $request->projectId;
                                                                    $transferInfo->dealId = $deal->dealId;
                                                                    $transferInfo->subscriberId = $request->subscriberId;
                                                                    $transferInfo->custodianId = $investment->custodianId;
                                                                    $transferInfo->investmentId = $request->investmentId;
                                                                    $transferInfo->projectName = $deal->dealLabel;
                                                                    $transferInfo->subscriberFullName = $subscriber->subscriberFullName;
                                                                    $transferInfo->amount = $request->amount;
                                                                    $transferInfo->transferResource = $response->headers['location'][0];

                                                                    $insertedRowId = $transaction->addTransaction($transferInfo);

                                                                    if ($insertedRowId)
                                                                        return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully initiated a transfer to ' . $subscriber->subscriberFullName . '.');
                                                                    else
                                                                        return redirect()->route('wl-payment-settings')->with('status-error', 'A transfer was unable to be initiated at this time.');
                                                                } else {
                                                                    return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result));
                                                                }
                                                            } else
                                                                return redirect()->route('wl-transfer-funds')->with('status-error', 'The transfer amount cannot be zero.');
                                                        } else
                                                            return redirect()->route('wl-payment-settings')->with('status-error', 'The destination bank account funding source URL is empty.');
                                                    } else
                                                        return redirect()->route('wl-payment-settings')->with('status-error', 'The custodian bank account ID "' . $cust->bankAccountId . '" does not exist.');
                                                } else
                                                    return redirect()->route('wl-payment-settings')->with('status-error', 'The custodian bank account ID is not set.');
                                            } else
                                                return redirect()->route('wl-payment-settings')->with('status-error', 'The custodian for custodian ID "' . $investment->custodianId . '" does not exist.');
                                        } else
                                            return redirect()->route('wl-payment-settings')->with('status-error', 'The investment custodian ID is empty.');
                                    } else
                                        return redirect()->route('wl-payment-settings')->with('status-error', 'The investment for investment ID "' . $request->investmentId . '" does not exist.');
                                } else
                                    return redirect()->route('wl-payment-settings')->with('status-error', 'The subscriber Dwolla location resource is empty.');
                            } else
                                return redirect()->route('wl-payment-settings')->with('status-error', 'The investor for subscriber ID "' . $request->subscriberId . '" does not exist.');
                        } else
                            return redirect()->route('wl-payment-settings')->with('status-error', 'The source bank account funding source URL is empty.');
                    } else
                        return redirect()->route('wl-payment-settings')->with('status-error', 'The bank account ID "' . $deal->bankAccountId . '" does not exist.');
                } else
                    return redirect()->route('wl-payment-settings')->with('status-error', 'The payout bank account for project "' . $deal->dealLabel . '" is not set.');
            } else
                return redirect()->route('wl-payment-settings')->with('status-error', 'The deal for project ID "' . $request->projectId . '" does not exist.');
        }

        $projects = $this->projectUtilities->getProjectsWithFundedInvestments();

        foreach ($projects as $key => &$value) {
            if ($value->isLegacy == '1')
                $value->projectLabel = $value->projectLabel . ' (Legacy)';
        }

        return view('whitelabels.transfer-funds', array(
            'projects' => $projects,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function transactions(Request $request) {
        $dwolla = new Dwolla();
        $transaction = new Transaction();
        $pageNumber = intval($request->query('page'));
        $limit = 20;
        $searchTerm = $request->query('search');

        if ($pageNumber == 0 || $pageNumber == 1)
            $offset = 0;
        else {
            if (empty($searchTerm))
                $offset = ($pageNumber - 1) * 20;
            else {
                $limit = 10;
                $offset = ($pageNumber - 1) * 10;
            }
        }

        if (empty($searchTerm))
            $transactions = $transaction->getTransactionsByWlId();
        else
            $transactions = $transaction->getTransactionsByWlIdAndName($searchTerm);

        if ($transactions->count()) {
            $response = $dwolla->listCustomerTransfers(Auth::user()->dwollaLocationResource, $limit, $offset, $searchTerm);

            if ($response->http_code == 200 && !empty($response->result->_embedded->transfers)) {
                foreach ($transactions as $transaction) {
                    foreach ($response->result->_embedded->transfers as $transfer) {
                        if ($transaction->transferResource == $transfer->_links->self->href) {
                            $transaction->status = $transfer->status;

                            if (!empty($transfer->individualAchId))
                                $transaction->individualAchId = $transfer->individualAchId;

                            $transaction->transferId = substr($transaction->transferResource, strrpos($transaction->transferResource, '/') + 1);

                            $transaction->cancelLink = !empty($transfer->_links->cancel);
                            $transaction->failureLink = !empty($transfer->_links->failure);

                            break;
                        }
                    }
                }
            } else {
                return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result, 'customer'));
            }
        }

        return view('whitelabels.transactions', array(
            'transactions' => $transactions,
            'customization' => $this->settingUtilities->getCustomizationSettingsByWlId(Auth::user()->wlId)
        ));
    }

    public function cancelTransfer($transferId) {
        $dwolla = new Dwolla();

        $response = $dwolla->cancelTransfer($transferId);

        if ($response->http_code == 200 && $response->result->status == 'cancelled')
            return redirect()->route('wl-transactions')->with('status-success', 'You have successfully cancelled a transfer.');
        else
            return redirect()->route('wl-transactions')->with('status-error', $dwolla->getErrorMessage($response->result, 'transfer resource'));
    }

    public function createWebhook(Request $request) {
        $dwolla = new Dwolla();
        $webhook = new Webhook();

        $response = $dwolla->createWebhookSubscription();

        if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
            $insertedRowId = $webhook->addWebhook($response->headers['location'][0]);

            if ($insertedRowId)
                return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully created a new webhook subscription.');
            else
                return redirect()->route('wl-payment-settings')->with('status-error', 'A new webhook subscription was unable to be created at this time.');
        } else {
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result));
        }
    }

    public function deleteWebhook($webhookId) {
        $dwolla = new Dwolla();
        $webhook = new Webhook();

        $response = $dwolla->deleteWebhookSubscription($webhookId);

        if ($response->http_code == 200) {
            $updatedRows = $webhook->deleteWebhook($dwolla->getWebhookResource($webhookId));

            if ($updatedRows)
                return redirect()->route('wl-payment-settings')->with('status-success', 'You have successfully deleted a webhook subscription.');
            else
                return redirect()->route('wl-payment-settings')->with('status-error', 'A webhook subscription was unable to be deleted at this time.');
        } else {
            return redirect()->route('wl-payment-settings')->with('status-error', $dwolla->getErrorMessage($response->result));
        }
    }

    public function transferFailure(Request $request) {
        $dwolla = new Dwolla();
        $transferFailure = app()->make('stdClass');

        if (!empty($request->transfer_id)) {
            $response = $dwolla->retrieveTransferFailure($request->transfer_id);

            if ($response->http_code == 200) {
                $transferFailure->code = $response->result->code;
                $transferFailure->description = $response->result->description;
                $transferFailure->explanation = $response->result->explanation;
            } else {
                $transferFailure->code = '';
                $transferFailure->description = '';
                $transferFailure->explanation = '';
            }
        } else {
            $transferFailure->code = '';
            $transferFailure->description = '';
            $transferFailure->explanation = '';
        }

        return json_encode($transferFailure);
    }

    public function getFundedInvestors(Request $request) {
        $subscribers = new Subscribers();
        $invs = app()->make('stdClass');

        if (!empty($request->project_id))
            $invs->investors = $subscribers->getSubscribersByProjectId($request->project_id);
        else
            $invs->investors = [];

        return json_encode($invs);
    }

    public function getInvestments(Request $request) {
        $investments = app()->make('stdClass');

        if (!empty($request->project_id) && !empty($request->subscriber_id))
            $investments->investments = $this->investmentUtilities->getInvestmentsByPrjIdSubId($request->project_id, $request->subscriber_id);
        else
            $investments->investments = [];

        foreach ($investments->investments as $key => $value) {
            foreach ($value as $key1 => &$value1) {
                switch ($key1) {
                    case 'pledge':
                        $value1 = money_format('$%i', $value1);
                    break;
                    case 'funded_at':
                        $value1 = \Carbon\Carbon::parse($value1)->format('F j, Y');
                    break;
                }
            }
        }

        return json_encode($investments);
    }

    public function settingsBankAccounts(Request $request) {
        $settings = $this->settingUtilities->getGeneralSettingsByUser();

        if ($settings === null)
            return route('wl-logout');

        $bankAcct = new BankAccount();
        $bankAccts = $bankAcct->getBankAccountsByWlId();

        return view('whitelabels.preferences.bank-accounts', array (
            'bankAccts' => $bankAccts,
            'settings' => $settings,
            'customization' => $this->settingUtilities->getCustomizationSettingsByUser()
        ));
    }

    public function addBankAccount(Request $request) {
        $settings = $this->settingUtilities->getGeneralSettingsByUser();

        if ($settings === null)
            return route('wl-logout');

        return view('whitelabels.preferences.bank-account', array(
            'action' => 'add',
            'settings' => $settings,
            'bankAcct' => null,
            'customization' => $this->settingUtilities->getCustomizationSettingsByUser()
        ));
    }

    public function editBankAccount(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();
        $settings = $this->settingUtilities->getGeneralSettingsByUser();

        if ($settings === null)
            return route('wl-logout');

        if ($request->isMethod('post')) {
            if (empty($request->accountName))
                return redirect()->route('wl-edit-bank-acct', $bankAccountId)->with('status-error', 'Account Name is required.');

            if ($request['mode'] == 'add') {
                $updatedId = $bankAcct->editBankAccount($request);

                if ($updatedId)
                    return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully added a new bank account.');
                else
                    return redirect()->route('wl-bank-accounts')->with('status-error', 'A new bank account was unable to be created at this time.');
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
                            return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a bank account.');
                        else
                            return redirect()->route('wl-bank-accounts')->with('status-error', 'A bank account was unable to be edited at this time.');
                    } else {
                        $errorMessage = $dwolla->getErrorMessage($response->result, 'funding source');

                        if ($response->result->code == 'ValidationError')
                            return redirect()->route('wl-edit-bank-acct', $bankAccountId)->with('status-error', $errorMessage);
                        else
                            return redirect()->route('wl-bank-accounts')->with('status-error', $errorMessage);
                    }
                } else {
                    $updatedId = $bankAcct->editBankAccount($request);

                    if ($updatedId)
                        return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a bank account.');
                    else
                        return redirect()->route('wl-bank-accounts')->with('status-error', 'A bank account was unable to be edited at this time.');
                }
            }
        }

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) === null)
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');

        return view('whitelabels.preferences.bank-account', array(
            'action' => 'edit',
            'settings' => $settings,
            'bankAcct' => $bankAccount,
            'customization' => $this->settingUtilities->getCustomizationSettingsByUser()
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

                        if ($updatedRows) {
                            $this->dealUtilities->removeBankAccount($bankAccountId);

                            return redirect()->route('wl-bank-accounts')->with('status-success', 'The bank account has been deleted.');
                        } else
                            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account was not able to be deleted.');
                    } else {
                        return redirect()->route('wl-bank-accounts')->with('status-error', $dwolla->getErrorMessage($dwollaResp->result, 'funding source'));
                    }
                } else {
                    $updatedRows = $bankAcct->deleteBankAccount($bankAccountId);

                    if ($updatedRows) {
                        $this->dealUtilities->removeBankAccount($bankAccountId);

                        return redirect()->route('wl-bank-accounts')->with('status-success', 'The bank account has been deleted.');
                    } else
                        return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account was not able to be deleted.');
                }
            } else
                return redirect()->route('wl-bank-accounts')->with('status-error', 'removeItem() returned the following error: ' . $response->error->error_type . '.');
        } else
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function addFundingSource(Request $request) {
        if (empty(Auth::user()->dwollaLocationResource))
            return redirect()->route('wl-bank-accounts')->with('status-error', 'In order to add a funding source, you must first create a Dwolla verified business customer.');

        if ($request->isMethod('post')) {
            $bankAcct = new BankAccount();
            $dwolla = new Dwolla();

            $response = $dwolla->createFundingSource(Auth::user()->dwollaLocationResource, $request);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers)) {
                $request['fundingSourceURL'] = $response->headers['location'][0];

                $insertedRowId = $bankAcct->addFundingSource($request);

                if ($insertedRowId)
                    return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully added a funding source.');
                else
                    return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be created at this time.');
            } else {
                $errorMessage = $dwolla->getErrorMessage($response->result, 'funding source');

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-add-funding-src')->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-bank-accounts')->with('status-error', $errorMessage);
            }
        }

        $customization = $this->settingUtilities->getCustomizationSettingsByUser();

        return view('whitelabels.create-funding-source', array(
            'customization' => $customization
        ));
    }

    public function editFundingSource(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) === null)
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');

        if (!empty($bankAccount->funding_source_url)) {
            $response = $dwolla->retrieveFundingSource($bankAccount->funding_source_url);

            if ($response->http_code == 200)
                $fundingSource = $response->result;
            else
                return redirect()->route('wl-bank-accounts')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
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
                            return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a funding source.');
                        else
                            return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be edited at this time.');
                    } else {
                        $errorMessage = $dwolla->getErrorMessage($response->result);

                        if ($response->result->code == 'ValidationError')
                            return redirect()->route('wl-edit-funding-src', $bankAccountId)->with('status-error', $errorMessage);
                        else
                            return redirect()->route('wl-bank-accounts')->with('status-error', $errorMessage);
                    }
                } else {
                    $updatedId = $bankAcct->editBankAccount($request);

                    if ($updatedId)
                        return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a funding source.');
                    else
                        return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be edited at this time.');
                }
            } else {
                $updatedId = $bankAcct->editBankAccount($request);

                if ($updatedId)
                    return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a funding source.');
                else
                    return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be edited at this time.');
            }
        }

        $customization = $this->settingUtilities->getCustomizationSettingsByUser();

        return view('whitelabels.edit-funding-source', array(
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

                    if ($updatedRows) {
                        $this->dealUtilities->removeBankAccount($bankAccountId);

                        return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully deleted a funding source.');
                    } else
                        return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be deleted at this time.');
                } else
                    return redirect()->route('wl-bank-accounts')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
            } else {
                $updatedRows = $bankAcct->deleteBankAccount($bankAccountId);

                if ($updatedRows) {
                    $this->dealUtilities->removeBankAccount($bankAccountId);

                    return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully deleted a funding source.');
                } else
                    return redirect()->route('wl-bank-accounts')->with('status-error', 'A funding source was unable to be deleted at this time.');
            }
        } else
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function initiateMicroDeposits($bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            $response = $dwolla->initiateMicroDeposits($bankAccount->funding_source_url);

            if ($response->http_code == 201 && array_key_exists('location', $response->headers))
                return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully initiated micro-deposits for funding source verification.');
            else
                return redirect()->route('wl-bank-accounts')->with('status-error', $dwolla->getErrorMessage($response->result, 'funding source'));
        } else
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function verifyMicroDeposits(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();
        $dwolla = new Dwolla();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            $response = $dwolla->verifyMicroDeposits($bankAccount->funding_source_url, $request->amount1, $request->amount2);

            if ($response->http_code == 200)
                return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully verified micro-deposits for funding source verification.');
            else {
                $errorMessage = $dwolla->getErrorMessage($response->result);

                if ($response->result->code == 'ValidationError')
                    return redirect()->route('wl-edit-funding-src', $bankAccountId)->with('status-error', $errorMessage);
                else
                    return redirect()->route('wl-bank-accounts')->with('status-error', $errorMessage);
            }
        } else
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
    }

    public function addBankAccountManual(Request $request) {
        if ($request->isMethod('post')) {
            $bankAcct = new BankAccount();

            $insertedRowId = $bankAcct->addFundingSource($request, 3);

            if ($insertedRowId)
                return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully added a bank account.');
            else
                return redirect()->route('wl-bank-accounts')->with('status-error', 'A bank account was unable to be added at this time.');
        }

        $customization = $this->settingUtilities->getCustomizationSettingsByUser();

        return view('whitelabels.bank-account-manual', array(
            'action' => 'add',
            'bankAcct' => null,
            'customization' => $customization
        ));
    }

    public function editBankAccountManual(Request $request, $bankAccountId) {
        $bankAcct = new BankAccount();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) === null)
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');

        if ($request->isMethod('post')) {
            $updatedId = $bankAcct->editBankAccount($request);

            if ($updatedId)
                return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully edited a bank account.');
            else
                return redirect()->route('wl-bank-accounts')->with('status-error', 'A bank account was unable to be edited at this time.');
        }

        $customization = $this->settingUtilities->getCustomizationSettingsByUser();

        return view('whitelabels.bank-account-manual', array(
            'action' => 'edit',
            'bankAcct' => $bankAccount,
            'customization' => $customization
        ));
    }

    public function deleteBankAccountManual($bankAccountId) {
        $bankAcct = new BankAccount();

        if (($bankAccount = $bankAcct->getBankAccount($bankAccountId)) !== null) {
            $updatedRows = $bankAcct->deleteBankAccount($bankAccountId);

            if ($updatedRows) {
                $this->dealUtilities->removeBankAccount($bankAccountId);

                return redirect()->route('wl-bank-accounts')->with('status-success', 'You have successfully deleted a bank account.');
            } else
                return redirect()->route('wl-bank-accounts')->with('status-error', 'A bank account was unable to be deleted at this time.');
        } else
            return redirect()->route('wl-bank-accounts')->with('status-error', 'The bank account ID "' . $bankAccountId . '" does not exist.');
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
