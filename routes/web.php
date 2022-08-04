<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['whitelabel']], function () {
    Route::get('/whitelabel/settings/bank-accounts', 'WhitelabelController@settingsBankAccounts')->name('wl-bank-accounts');
    Route::get('/whitelabel/settings/bank-account/add', 'WhitelabelController@addBankAccount')->name('wl-add-bank-acct');
    Route::get('/whitelabel/settings/bank-account/delete/{bankAccountId}', 'WhitelabelController@deleteBankAccount')->name('wl-delete-bank-acct');
    Route::get('/whitelabel/settings/bank-account/edit/{bankAccountId}', 'WhitelabelController@editBankAccount')->name('wl-edit-bank-acct');
    Route::post('/whitelabel/settings/bank-account/edit/{bankAccountId}', 'WhitelabelController@editBankAccount')->name('wl-edit-bank-acct-upd');
    Route::post('/whitelabel/settings/bank-account/auth', 'WhitelabelController@authenticateBankAccount')->name('wl-auth-bank-acct');
    Route::get('/whitelabel/settings/bank-account/link-token/create', 'WhitelabelController@createLinkToken')->name('wl-create-link-token');
    Route::get('/whitelabel/settings/funding-source/add', 'WhitelabelController@addFundingSource')->name('wl-add-funding-src');
    Route::post('/whitelabel/settings/funding-source/add', 'WhitelabelController@addFundingSource')->name('wl-add-funding-src-save');
    Route::get('/whitelabel/settings/funding-source/edit/{bankAccountId}', 'WhitelabelController@editFundingSource')->name('wl-edit-funding-src');
    Route::post('/whitelabel/settings/funding-source/edit/{bankAccountId}', 'WhitelabelController@editFundingSource')->name('wl-edit-funding-src-upd');
    Route::get('/whitelabel/settings/funding-source/delete/{bankAccountId}', 'WhitelabelController@deleteFundingSource')->name('wl-delete-funding-src');
    Route::get('/whitelabel/settings/initiate-micro-deposits/{bankAccountId}', 'WhitelabelController@initiateMicroDeposits')->name('wl-initiate-micro-deps');
    Route::post('/whitelabel/settings/verify-micro-deposits/{bankAccountId}', 'WhitelabelController@verifyMicroDeposits')->name('wl-verify-micro-deps');
    Route::get('/whitelabel/settings/bank-account/manual/add', 'WhitelabelController@addBankAccountManual')->name('wl-add-bank-acct-manual');
    Route::post('/whitelabel/settings/bank-account/manual/add', 'WhitelabelController@addBankAccountManual')->name('wl-add-bank-acct-manual-save');
    Route::get('/whitelabel/settings/bank-account/manual/edit/{bankAccountId}', 'WhitelabelController@editBankAccountManual')->name('wl-edit-bank-acct-manual');
    Route::post('/whitelabel/settings/bank-account/manual/edit/{bankAccountId}', 'WhitelabelController@editBankAccountManual')->name('wl-edit-bank-acct-manual-upd');
    Route::get('/whitelabel/settings/bank-account/manual/delete/{bankAccountId}', 'WhitelabelController@deleteBankAccountManual')->name('wl-delete-bank-acct-manual');

    Route::get('/whitelabel/settings/payment-settings', 'WhitelabelController@paymentSettings')->name('wl-payment-settings');
    Route::get('/whitelabel/settings/payment-settings/transfer-funds', 'WhitelabelController@transferFunds')->name('wl-transfer-funds');
    Route::post('/whitelabel/settings/payment-settings/transfer-funds', 'WhitelabelController@transferFunds')->name('wl-transfer-funds-save');
    Route::get('/whitelabel/settings/payment-settings/transactions', 'WhitelabelController@transactions')->name('wl-transactions');
    Route::get('/whitelabel/settings/payment-settings/cancel-transfer/{transferId}', 'WhitelabelController@cancelTransfer')->name('wl-cancel-transfer');
    Route::post('/whitelabel/settings/payment-settings/transfer-failure', 'WhitelabelController@transferFailure');
    Route::post('/whitelabel/settings/payment-settings/funded-investors', 'WhitelabelController@getFundedInvestors');
    Route::post('/whitelabel/settings/payment-settings/investments', 'WhitelabelController@getInvestments');
    Route::get('/whitelabel/settings/create-customer', 'WhitelabelController@createCustomer')->name('wl-create-cust');
    Route::post('/whitelabel/settings/create-customer', 'WhitelabelController@createCustomer')->name('wl-create-cust-save');
    Route::get('/whitelabel/settings/update-customer', 'WhitelabelController@updateCustomer')->name('wl-update-cust');
    Route::post('/whitelabel/settings/update-customer', 'WhitelabelController@updateCustomer')->name('wl-update-cust-save');
    Route::get('/whitelabel/settings/retry-customer', 'WhitelabelController@retryCustomer')->name('wl-retry-cust');
    Route::post('/whitelabel/settings/retry-customer', 'WhitelabelController@retryCustomer')->name('wl-retry-cust-save');
    Route::get('/whitelabel/settings/customer-documents', 'WhitelabelController@customerDocuments')->name('wl-customer-documents');
    Route::get('/whitelabel/settings/upload-customer-docs', 'WhitelabelController@uploadCustomerDocuments')->name('wl-upload-cust-docs');
    Route::post('/whitelabel/settings/upload-customer-docs', 'WhitelabelController@uploadCustomerDocuments')->name('wl-upload-cust-docs-save');
    Route::get('/whitelabel/settings/funding-sources', 'WhitelabelController@fundingSources')->name('wl-funding-sources');
    Route::get('/whitelabel/settings/funding-source/create/{bankAccountId}', 'WhitelabelController@createFundingSource')->name('wl-create-funding-src');
    Route::get('/whitelabel/settings/funding-source/remove/{bankAccountId}', 'WhitelabelController@removeFundingSource')->name('wl-remove-funding-src');
    Route::get('/whitelabel/settings/beneficial-owners', 'WhitelabelController@beneficialOwners')->name('wl-beneficial-owners');
    Route::post('/whitelabel/settings/beneficial-owners/certify-ownership', 'WhitelabelController@certifyOwnership')->name('wl-certify-ownership');
    Route::get('/whitelabel/settings/beneficial-owners/certify-ownership/{customerId}', 'WhitelabelController@certifyCustomerOwnership');
    Route::get('/whitelabel/settings/create-beneficial-owner', 'WhitelabelController@createBeneficialOwner')->name('wl-create-beneficial');
    Route::post('/whitelabel/settings/create-beneficial-owner', 'WhitelabelController@createBeneficialOwner')->name('wl-create-beneficial-save');
    Route::get('/whitelabel/settings/beneficial-owner/edit/{beneficialOwnerId}', 'WhitelabelController@updateBeneficialOwner')->name('wl-update-beneficial');
    Route::post('/whitelabel/settings/beneficial-owner/edit/{beneficialOwnerId}', 'WhitelabelController@updateBeneficialOwner')->name('wl-update-beneficial-save');
    Route::get('/whitelabel/settings/beneficial-owner/delete/{beneficialOwnerId}', 'WhitelabelController@deleteBeneficialOwner')->name('wl-delete-beneficial');
    Route::get('/whitelabel/settings/beneficial-documents/{beneficialOwnerId}', 'WhitelabelController@beneficialOwnerDocs')->name('wl-beneficial-documents');
    Route::get('/whitelabel/settings/beneficial-owner/docs/{beneficialOwnerId}', 'WhitelabelController@uploadBeneficialOwnerDocs')->name('wl-upload-beneficial');
    Route::post('/whitelabel/settings/beneficial-owner/docs/{beneficialOwnerId}', 'WhitelabelController@uploadBeneficialOwnerDocs')->name('wl-upload-beneficial-save');
    Route::get('/whitelabel/payment-settings/create-webhook', 'WhitelabelController@createWebhook')->name('wl-create-webhook');
    Route::get('/whitelabel/payment-settings/delete-webhook/{webhookId}', 'WhitelabelController@deleteWebhook')->name('wl-delete-webhook');
    Route::get('/whitelabel/payment-settings/reactivate-customer/{customerId}', 'WhitelabelController@reactivateCustomer')->name('wl-reactivate-customer');
    Route::get('/whitelabel/payment-settings/deactivate-customer/{customerId}', 'WhitelabelController@deactivateCustomer')->name('wl-deactivate-customer');
    Route::get('/whitelabel/payment-settings/suspend-customer/{customerId}', 'WhitelabelController@suspendCustomer')->name('wl-suspend-customer');
    Route::get('/whitelabel/payment-settings/reset-whitelabel/{wlId}', 'WhitelabelController@resetWhitelabel')->name('wl-reset');
});

Route::group(['middleware' => ['investor']], function () {
    Route::post('/investor/settings/dwolla-setup', 'SubscriberController@dwollaSetup')->name('inv-dwolla-setup');
    Route::get('/investor/settings/bank-account/add', 'SubscriberController@addBankAccount')->name('inv-add-bank-acct');
    Route::post('/investor/settings/bank-account/auth', 'SubscriberController@authenticateBankAccount');
    Route::get('/investor/settings/bank-account/edit/{bankAccountId}', 'SubscriberController@editBankAccount')->name('inv-edit-bank-acct');
    Route::post('/investor/settings/bank-account/edit/{bankAccountId}', 'SubscriberController@editBankAccount')->name('inv-edit-bank-acct-upd');
    Route::get('/investor/settings/bank-account/delete/{bankAccountId}', 'SubscriberController@deleteBankAccount')->name('inv-delete-bank-acct');
    Route::get('/investor/settings/bank-account/link-token/create', 'SubscriberController@createLinkToken')->name('inv-create-link-token');
    Route::get('/investor/settings/funding-source/create/{bankAccountId}', 'SubscriberController@createFundingSource')->name('inv-create-funding-src');
    Route::get('/investor/settings/funding-source/remove/{bankAccountId}', 'SubscriberController@removeFundingSource')->name('inv-remove-funding-src');
    Route::get('/investor/settings/reset-subscriber/{subscriberId}', 'SubscriberController@resetSubscriber')->name('inv-reset');
    Route::get('/investor/transactions', 'SubscriberController@transactions')->name('inv-transactions');
    Route::get('/investor/settings/funding-source/add', 'SubscriberController@addFundingSource')->name('inv-add-funding-src');
    Route::post('/investor/settings/funding-source/add', 'SubscriberController@addFundingSource')->name('inv-add-funding-src-save');
    Route::get('/investor/settings/funding-source/edit/{bankAccountId}', 'SubscriberController@editFundingSource')->name('inv-edit-funding-src');
    Route::post('/investor/settings/funding-source/edit/{bankAccountId}', 'SubscriberController@editFundingSource')->name('inv-edit-funding-src-upd');
    Route::get('/investor/settings/funding-source/delete/{bankAccountId}', 'SubscriberController@deleteFundingSource')->name('inv-delete-funding-src');
});

Route::group(['middleware' => ['cors']], function () {
    Route::post('/whitelabel/payment-settings/webhook', 'WebhookController@handleRequest')->name('wl-handle-webhook');
});