@extends('layouts.investor')
@section('content')

<section class="full-page bg-color fullpage cf bank-acct">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

			<!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('inv-settings') }}" title="Settings">Settings</a></li>
                        @if ($action == 'add')
                        <li class="active-page">Add Bank Account</li>
                        @elseif ($action == 'edit')
                        <li class="active-page">Edit Bank Account</li>
                        @endif
                    </ul>
                </div>

                @if ($action == 'add')
                <h1 class="color h1-border">Add Bank Account</h1>

                <form id="bank-acct-form" class="setting-form medium cf" action="" autocomplete="off" method="post">
                @elseif ($action == 'edit')
                <h1 class="color h1-border">Edit Bank Account</h1>

                <form id="bank-acct-form" class="setting-form medium cf" action="{{ route('inv-edit-bank-acct-upd', $bankAcct->bankAccountId) }}" autocomplete="off" method="post">
                @endif
                    {{ csrf_field() }}
                    
                    <!-- Account Owners Information -->
                    <div id="project_accounting_details">
                        <input type="hidden" name="bankAccountId" value="{{ $bankAcct->bankAccountId or '' }}">
                        <input type="hidden" name="mode" value="{{ $action }}">

                        @if ($action == 'edit')
                        <input type="hidden" name="currentAcctName" value="{{ $bankAcct->accountName }}">
                        @endif

                        @if ($action == 'add')
                        <p>Click the "Link Account" button to select a bank account to add.</p>
                        @elseif ($action == 'edit')
                        <p>Click the "Link Account" button to select a bank account to update.</p>
                        @endif

                        <div class="button-wrapper cf">
                            <div class="label-wrapper half cf">
                                <div id="link-btn-container" class="btn submit qtr cf">
                                    <a href="javascript:void(0)" id="link-btn">Link Account</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-section new-account-information-wrapper">
                            <div class="label-wrapper cf">
                                <span class="label-name">Account Name</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->accountName){{$bankAcct->accountName}}@endisset" placeholder="" name="accountName" maxlength="64" id="accountName">
                            </div>
                            <div class="label-wrapper cf">
                                <span class="label-name">Account Type</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->accountType){{$bankAcct->accountType}}@endisset" placeholder="" name="accountType" maxlength="64" id="accountType" readonly>
                            </div>
                        </div>

                        <div class="form-section new-account-information-wrapper">
                            <div class="label-wrapper half cf">
                                <span class="label-name">Account Number</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->accountNumber){{$bankAcct->accountNumber}}@endisset" placeholder="" name="accountNumber" maxlength="256" id="accountNumber" readonly>
                            </div>
                            <div class="label-wrapper half cf">
                                <span class="label-name">ABA Routing Transit Number</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->abaRoutingTransitNumber){{$bankAcct->abaRoutingTransitNumber}}@endisset" placeholder="" name="abaRoutingTransitNumber" maxlength="9" id="abaRoutingTransitNumber" readonly>
                            </div>
                            <div class="label-wrapper half cf">
                                <span class="label-name">Wire Transfer Routing Number</span><span class="error_message"></span>
                                <input type="text" class="input" value="@isset($bankAcct->wireTransferRoutingNumber){{$bankAcct->wireTransferRoutingNumber}}@endisset" placeholder="" name="wireTransferRoutingNumber" maxlength="9" id="wireTransferRoutingNumber" readonly>
                            </div>
                        </div>

                        <!-- New Account Owner's Street Address -->
                        <div id="project_address" class="form-section property-type-wrapper">
                            <div class="label-wrapper half cf">
                                <span class="label-name">Bank Name</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->bankName){{$bankAcct->bankName}}@endisset" placeholder="" name="bankName" id="bankName" readonly>
                            </div>
                            <p>Enter the address of the financial institution: (start typing in the Street Address field and choose the address from the dropdown)</p>
                            <div class="label-wrapper half cf">
                                <span class="label-name">Street Address</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->bankStreet){{$bankAcct->bankStreet}}@endisset" placeholder="" name="bankStreet" id="bankStreet">
                            </div>
                        </div>

                        <!-- New Account Owner's City, State, Zip -->
                        <div id="project_city_state_zip" class="form-section city-state-zip-wrapper">

                            <div class="label-wrapper half cf">
                                <span class="label-name">City</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->bankCity){{$bankAcct->bankCity}}@endisset" placeholder="" name="bankCity" id="bankCity">
                            </div>

                            <div class="label-wrapper qtr cf">
                                <span class="label-name">State</span><span class="error_message"></span>
                                <select class="input required" name="bankState" id="bankState">
                                    <option value="" @isset($bankAcct->bankState) @if($bankAcct->bankState == "") selected @endif @endisset >Select a State</option>
                                    <option value="AL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AL") selected @endif @endisset >Alabama</option>
                                    <option value="AK" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AK") selected @endif @endisset >Alaska</option>
                                    <option value="AZ" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AZ") selected @endif @endisset >Arizona</option>
                                    <option value="AR" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AR") selected @endif @endisset >Arkansas</option>
                                    <option value="CA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CA") selected @endif @endisset >California</option>
                                    <option value="CO" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CO") selected @endif @endisset >Colorado</option>
                                    <option value="CT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CT") selected @endif @endisset >Connecticut</option>
                                    <option value="DE" @isset($bankAcct->bankState) @if($bankAcct->bankState == "DE") selected @endif @endisset >Delaware</option>
                                    <option value="DC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "DC") selected @endif @endisset >District Of Columbia</option>
                                    <option value="FL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "FL") selected @endif @endisset >Florida</option>
                                    <option value="GA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "GA") selected @endif @endisset >Georgia</option>
                                    <option value="HI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "HI") selected @endif @endisset >Hawaii</option>
                                    <option value="ID" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ID") selected @endif @endisset >Idaho</option>
                                    <option value="IL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IL") selected @endif @endisset >Illinois</option>
                                    <option value="IN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IN") selected @endif @endisset >Indiana</option>
                                    <option value="IA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IA") selected @endif @endisset >Iowa</option>
                                    <option value="KS" @isset($bankAcct->bankState) @if($bankAcct->bankState == "KS") selected @endif @endisset >Kansas</option>
                                    <option value="KY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "KY") selected @endif @endisset >Kentucky</option>
                                    <option value="LA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "LA") selected @endif @endisset >Louisiana</option>
                                    <option value="ME" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ME") selected @endif @endisset >Maine</option>
                                    <option value="MD" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MD") selected @endif @endisset >Maryland</option>
                                    <option value="MA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MA") selected @endif @endisset >Massachusetts</option>
                                    <option value="MI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MI") selected @endif @endisset >Michigan</option>
                                    <option value="MN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MN") selected @endif @endisset >Minnesota</option>
                                    <option value="MS" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MS") selected @endif @endisset >Mississippi</option>
                                    <option value="MO" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MO") selected @endif @endisset >Missouri</option>
                                    <option value="MT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MT") selected @endif @endisset >Montana</option>
                                    <option value="NE" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NE") selected @endif @endisset >Nebraska</option>
                                    <option value="NV" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NV") selected @endif @endisset >Nevada</option>
                                    <option value="NH" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NH") selected @endif @endisset >New Hampshire</option>
                                    <option value="NJ" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NJ") selected @endif @endisset >New Jersey</option>
                                    <option value="NM" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NM") selected @endif @endisset >New Mexico</option>
                                    <option value="NY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NY") selected @endif @endisset >New York</option>
                                    <option value="NC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NC") selected @endif @endisset >North Carolina</option>
                                    <option value="ND" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ND") selected @endif @endisset >North Dakota</option>
                                    <option value="OH" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OH") selected @endif @endisset >Ohio</option>
                                    <option value="OK" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OK") selected @endif @endisset >Oklahoma</option>
                                    <option value="OR" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OR") selected @endif @endisset >Oregon</option>
                                    <option value="PA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "PA") selected @endif @endisset >Pennsylvania</option>
                                    <option value="RI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "RI") selected @endif @endisset >Rhode Island</option>
                                    <option value="SC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "SC") selected @endif @endisset >South Carolina</option>
                                    <option value="SD" @isset($bankAcct->bankState) @if($bankAcct->bankState == "SD") selected @endif @endisset >South Dakota</option>
                                    <option value="TN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "TN") selected @endif @endisset >Tennessee</option>
                                    <option value="TX" @isset($bankAcct->bankState) @if($bankAcct->bankState == "TX") selected @endif @endisset >Texas</option>
                                    <option value="UT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "UT") selected @endif @endisset >Utah</option>
                                    <option value="VT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "VT") selected @endif @endisset >Vermont</option>
                                    <option value="VA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "VA") selected @endif @endisset >Virginia</option>
                                    <option value="WA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WA") selected @endif @endisset >Washington</option>
                                    <option value="WV" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WV") selected @endif @endisset >West Virginia</option>
                                    <option value="WI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WI") selected @endif @endisset >Wisconsin</option>
                                    <option value="WY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WY") selected @endif @endisset >Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper qtr cf">
                                <span class="label-name">ZIP Code</span><span class="error_message"></span>
                                <input type="text" class="input required" value="@isset($bankAcct->bankZipCode){{$bankAcct->bankZipCode}}@endisset" placeholder="" name="bankZipCode" id="bankZipCode">
                            </div>
                        </div>
                    </div>

                    <!-- Button Wrapper -->
                    <div class="form-section comm-form cf">
                        <div class="button-wrapper cf">
                            <!-- div class="label-wrapper half cf">
                                <div class="btn primary outline full cf">
                                    <a href="" title="Clear Fields">Clear Fields</a>
                                </div>
                            </div -->

                            <div class="label-wrapper half cf">
                                <button class="btn @if($action == 'add') disabled @elseif($action == 'edit') submit @endif full cf" type="submit" @if ($action == 'add') disabled @endif>Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    (async function($) {
        const fetchLinkToken = async () => {
            let response = await fetch('/investor/settings/bank-account/link-token/create'),
                responseJSON = await response.json();

            return responseJSON.link_token;
        };

        let config = {
            token: await fetchLinkToken(),
            onSuccess: function(public_token, metadata) {
                $.ajax({
                    url: '/investor/settings/bank-account/auth',
                    type: 'POST',
                    data: {
                        public_token: public_token,
                        account_id: metadata.account_id,
                        bank_name: metadata.institution.name,
                        @if ($action == 'edit')
                        bankAccountId: '{{ $bankAcct->bankAccountId }}',
                        @endif
                        mode: '{{ $action }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    let auth_info = JSON.parse(data);

                    if (auth_info.status == 'success') {
                        @if ($action == 'add')
                        $('#bank-acct-form').attr('action', location.protocol + '//' + location.hostname + '/investor/settings/bank-account/edit/' + auth_info.bank_acct_id);
                        $('input[name="bankAccountId"]').val(auth_info.bank_acct_id);
                        $('#bank-acct-form button').removeAttr('disabled').removeClass('disabled').addClass('submit');
                        $('#link-btn-container').removeClass('submit');
                        $('#link-btn').addClass('disabled');
                        @endif
                        $('#accountName').val(metadata.account.name);
                        $('#accountType').val(auth_info.account_type);
                        $('#accountNumber').val(auth_info.account);
                        $('#abaRoutingTransitNumber').val(auth_info.routing);
                        $('#wireTransferRoutingNumber').val(auth_info.wire_routing);
                        $('#bankName').val(metadata.institution.name);
                    } else {
                        console.log(auth_info);
                    }
                }).fail(function(jqXHR, textStatus) {
                    console.log('Request failed: ' + textStatus);
                });
            },
            onExit: async function(err, metadata) {
                console.log(metadata);

                if (err != null) {
                    console.log(err);
                    // The user encountered a Plaid API error prior to exiting.
                    if (err.error_code === 'INVALID_LINK_TOKEN') {
                        // The link_token expired or the user entered too many
                        // invalid credentials. We want to destroy the old iframe
                        // and reinitialize Plaid Link with a new link_token.
                        handler.destroy();
                        handler = Plaid.create({
                            ...config,
                            token: await fetchLinkToken()
                        });
                    }
                }
            }
        };

        let handler = Plaid.create(config);

        $('#link-btn').on('click', function(e) {
            e.preventDefault();
            handler.open();
        });
    })(jQuery);

    $(document).ready(function() {
        let bankStreet = document.getElementById('bankStreet'),
            bankStreetSearchBox = new google.maps.places.SearchBox(bankStreet);

        bankStreetSearchBox.addListener('places_changed', function() {
            let place = this.getPlaces()[0],
                street_number,
                route;

            for (let address of place.address_components) {
                switch (address.types[0]) {
                    case 'street_number':
                        street_number = address.short_name;
                        break;
                    case 'route':
                        route = address.long_name;
                        break;
                    case 'locality':
                        document.getElementById('bankCity').value = address.long_name;
                        break;
                    case 'administrative_area_level_1':
                        document.getElementById('bankState').value = address.short_name;
                        break;
                    case 'postal_code':
                        document.getElementById('bankZipCode').value = address.short_name;
                        break;
                }
            }

            document.getElementById('bankStreet').value = street_number + ' ' + route;
        });
    });
</script>
@endsection