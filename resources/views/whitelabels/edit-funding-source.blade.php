@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf funding-source">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-bank-accounts') }}" title="Bank Accounts">Bank Accounts</a></li>
                        <li class="active-page">Edit Funding Source</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Edit Funding Source</h1>

                <span class="funding-src-status">Funding Source Status: {{ $fundingSource->status or '' }}</span>

                <p>Required fields are denoted by an asterisk (*).</p>

                <form id="funding-source-form" class="setting-form medium cf" action="{{ route('wl-edit-funding-src-upd', $bankAcct->bankAccountId) }}" autocomplete="off" method="post">
                    {{ csrf_field() }}

                    <input type="hidden" name="bankAccountId" value="{{ $bankAcct->bankAccountId }}">
                    
                    <div id="funding-source-info" class="cf">
                        <div id="account-info" class="lfloat pr25 rborder">
                            <h2>Account Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Account Name *</span>
                                <input type="text" name="accountName" id="accountName" value="@isset($bankAcct->accountName){{$bankAcct->accountName}}@endisset" maxlength="50" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Account Type *</span>
                                <select name="accountType" id="accountType" required @if (isset($fundingSource->status) && $fundingSource->status == 'verified') disabled @endif>
                                    <option value="">Select Account Type</option>
                                    <option value="checking" @isset($bankAcct->accountType) @if($bankAcct->accountType == "checking") selected @endif @endisset>Checking</option>
                                    <option value="savings" @isset($bankAcct->accountType) @if($bankAcct->accountType == "savings") selected @endif @endisset>Savings</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Account Number *</span>
                                <input type="text" name="accountNumber" id="accountNumber" value="@isset($bankAcct->accountNumber){{$bankAcct->accountNumber}}@endisset" maxlength="17" required @if (isset($fundingSource->status) && $fundingSource->status == 'verified') readonly @endif>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">ABA Routing Transit Number *</span>
                                <input type="text" name="abaRoutingTransitNumber" id="abaRoutingTransitNumber" value="@isset($bankAcct->abaRoutingTransitNumber){{$bankAcct->abaRoutingTransitNumber}}@endisset" maxlength="9" required @if (isset($fundingSource->status) && $fundingSource->status == 'verified') readonly @endif>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Wire Transfer Routing Number</span>
                                <input type="text" name="wireTransferRoutingNumber" id="wireTransferRoutingNumber" value="@isset($bankAcct->wireTransferRoutingNumber){{$bankAcct->wireTransferRoutingNumber}}@endisset" maxlength="9">
                            </div>
                        </div>
                        <div id="bank-info" class="rfloat pl25">
                            <h2>Bank Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Bank Name</span>
                                <input type="text" name="bankName" id="bankName" value="@isset($bankAcct->bankName){{$bankAcct->bankName}}@endisset">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Street Address (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="bankStreet" id="bankStreet" placeholder="" value="@isset($bankAcct->bankStreet){{$bankAcct->bankStreet}}@endisset">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City</span>
                                <input type="text" name="bankCity" id="bankCity" value="@isset($bankAcct->bankCity){{$bankAcct->bankCity}}@endisset">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State</span>
                                <select name="bankState" id="bankState">
                                    <option value="">Select a State</option>
                                    <option value="AL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AL") selected @endif @endisset>Alabama</option>
                                    <option value="AK" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AK") selected @endif @endisset>Alaska</option>
                                    <option value="AZ" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AZ") selected @endif @endisset>Arizona</option>
                                    <option value="AR" @isset($bankAcct->bankState) @if($bankAcct->bankState == "AR") selected @endif @endisset>Arkansas</option>
                                    <option value="CA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CA") selected @endif @endisset>California</option>
                                    <option value="CO" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CO") selected @endif @endisset>Colorado</option>
                                    <option value="CT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "CT") selected @endif @endisset>Connecticut</option>
                                    <option value="DE" @isset($bankAcct->bankState) @if($bankAcct->bankState == "DE") selected @endif @endisset>Delaware</option>
                                    <option value="DC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "DC") selected @endif @endisset>District Of Columbia</option>
                                    <option value="FL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "FL") selected @endif @endisset>Florida</option>
                                    <option value="GA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "GA") selected @endif @endisset>Georgia</option>
                                    <option value="HI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "HI") selected @endif @endisset>Hawaii</option>
                                    <option value="ID" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ID") selected @endif @endisset>Idaho</option>
                                    <option value="IL" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IL") selected @endif @endisset>Illinois</option>
                                    <option value="IN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IN") selected @endif @endisset>Indiana</option>
                                    <option value="IA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "IA") selected @endif @endisset>Iowa</option>
                                    <option value="KS" @isset($bankAcct->bankState) @if($bankAcct->bankState == "KS") selected @endif @endisset>Kansas</option>
                                    <option value="KY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "KY") selected @endif @endisset>Kentucky</option>
                                    <option value="LA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "LA") selected @endif @endisset>Louisiana</option>
                                    <option value="ME" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ME") selected @endif @endisset>Maine</option>
                                    <option value="MD" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MD") selected @endif @endisset>Maryland</option>
                                    <option value="MA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MA") selected @endif @endisset>Massachusetts</option>
                                    <option value="MI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MI") selected @endif @endisset>Michigan</option>
                                    <option value="MN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MN") selected @endif @endisset>Minnesota</option>
                                    <option value="MS" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MS") selected @endif @endisset>Mississippi</option>
                                    <option value="MO" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MO") selected @endif @endisset>Missouri</option>
                                    <option value="MT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "MT") selected @endif @endisset>Montana</option>
                                    <option value="NE" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NE") selected @endif @endisset>Nebraska</option>
                                    <option value="NV" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NV") selected @endif @endisset>Nevada</option>
                                    <option value="NH" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NH") selected @endif @endisset>New Hampshire</option>
                                    <option value="NJ" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NJ") selected @endif @endisset>New Jersey</option>
                                    <option value="NM" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NM") selected @endif @endisset>New Mexico</option>
                                    <option value="NY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NY") selected @endif @endisset>New York</option>
                                    <option value="NC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "NC") selected @endif @endisset>North Carolina</option>
                                    <option value="ND" @isset($bankAcct->bankState) @if($bankAcct->bankState == "ND") selected @endif @endisset>North Dakota</option>
                                    <option value="OH" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OH") selected @endif @endisset>Ohio</option>
                                    <option value="OK" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OK") selected @endif @endisset>Oklahoma</option>
                                    <option value="OR" @isset($bankAcct->bankState) @if($bankAcct->bankState == "OR") selected @endif @endisset>Oregon</option>
                                    <option value="PA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "PA") selected @endif @endisset>Pennsylvania</option>
                                    <option value="RI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "RI") selected @endif @endisset>Rhode Island</option>
                                    <option value="SC" @isset($bankAcct->bankState) @if($bankAcct->bankState == "SC") selected @endif @endisset>South Carolina</option>
                                    <option value="SD" @isset($bankAcct->bankState) @if($bankAcct->bankState == "SD") selected @endif @endisset>South Dakota</option>
                                    <option value="TN" @isset($bankAcct->bankState) @if($bankAcct->bankState == "TN") selected @endif @endisset>Tennessee</option>
                                    <option value="TX" @isset($bankAcct->bankState) @if($bankAcct->bankState == "TX") selected @endif @endisset>Texas</option>
                                    <option value="UT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "UT") selected @endif @endisset>Utah</option>
                                    <option value="VT" @isset($bankAcct->bankState) @if($bankAcct->bankState == "VT") selected @endif @endisset>Vermont</option>
                                    <option value="VA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "VA") selected @endif @endisset>Virginia</option>
                                    <option value="WA" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WA") selected @endif @endisset>Washington</option>
                                    <option value="WV" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WV") selected @endif @endisset>West Virginia</option>
                                    <option value="WI" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WI") selected @endif @endisset>Wisconsin</option>
                                    <option value="WY" @isset($bankAcct->bankState) @if($bankAcct->bankState == "WY") selected @endif @endisset>Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">ZIP Code</span>
                                <input type="text" name="bankZipCode" id="bankZipCode" value="@isset($bankAcct->bankZipCode){{$bankAcct->bankZipCode}}@endisset">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="funding-source-save" class="label-wrapper rfloat">
                        <button class="btn btn-success" type="button">Save</button>
                    </div>
                </div>
            </div>

            <div id="initiate-micro-deposits" @if (!isset($fundingSource->_links->{'initiate-micro-deposits'})) style="display:none;" @endif>
                <fieldset>
                    <legend>Initiate Micro-Deposits</legend>
                    <form id="initiate-deposits-form" class="setting-form medium cf" action="{{ route('wl-initiate-micro-deps', $bankAcct->bankAccountId) }}" autocomplete="off" method="get">
                        <p>In order to verify the funding source, Dwolla will deposit two random amounts of less than $0.10 to the above bank or credit union account and will post in 1-2 business days.</p>
                    </form>
                    <div class="button-wrapper cf">
                        <div id="initiate-deposits-submit" class="label-wrapper">
                            <button class="btn btn-primary" type="button">Submit</button>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div id="verify-micro-deposits" @if (!isset($fundingSource->_links->{'verify-micro-deposits'})) style="display:none;" @endif>
                <fieldset>
                    <legend>Verify Micro-Deposits</legend>
                    <p>To complete the funding source verification, enter the amounts of the two micro deposits that have posted to the above bank or credit union account.</p>
                    <form id="verify-deposits-form" class="setting-form medium cf" action="{{ route('wl-verify-micro-deps', $bankAcct->bankAccountId) }}" autocomplete="off" method="post">
                        {{ csrf_field() }}
                        
                        <div id="amount-info" class="cf">
                            <div class="lfloat pr16">
                                <div class="label-wrapper">
                                    <span class="label-name">Amount 1 *</span>
                                    <input type="text" name="amount1" id="amount1" placeholder="0.03" maxlength="4" required>
                                </div>
                            </div>
                            <div class="rfloat pl16">
                                <div class="label-wrapper">
                                    <span class="label-name">Amount 2 *</span>
                                    <input type="text" name="amount2" id="amount2" placeholder="0.09" maxlength="4" required>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="button-wrapper cf">
                        <div id="verify-deposits-submit" class="label-wrapper">
                            <button class="btn btn-primary" type="button">Submit</button>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        let form = $('#funding-source-form'),
            verifyDepsForm = $('#verify-deposits-form');

        form.validate({
            rules: {
                accountNumber: {
                    pattern: '[0-9]{4,17}'
                },
                abaRoutingTransitNumber: {
                    pattern: '[0-9]{9}'
                },
                wireTransferRoutingNumber: {
                    pattern: '[0-9]{9}'
                }
            },
            messages: {
                accountNumber: {
                    pattern: 'Format must be a numeric string of 4-17 digits.'
                },
                abaRoutingTransitNumber: {
                    pattern: 'Format must be a numeric string of 9 digits.'
                },
                wireTransferRoutingNumber: {
                    pattern: 'Format must be a numeric string of 9 digits.'
                }
            }
        });

        verifyDepsForm.validate({
            rules: {
                amount1: {
                    pattern: '0\\.[0-9]{2}'
                },
                amount2: {
                    pattern: '0\\.[0-9]{2}'
                }
            },
            messages: {
                amount1: {
                    pattern: 'Format must be 0.XX where X is 0-9.'
                },
                amount2: {
                    pattern: 'Format must be 0.XX where X is 0-9.'
                }
            }
        });

        $('#ach-wizard-cancel').click(function() {
            location.href = '{{ route('wl-bank-accounts') }}';
        });

        $('#funding-source-save').click(function() {
            if (form.valid())
                form.submit();
        });

        $('#initiate-deposits-submit').click(function() {
            $('#initiate-deposits-form').submit();
        });

        $('#verify-deposits-submit').click(function() {
            if (verifyDepsForm.valid())
                verifyDepsForm.submit();
        });

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

    jQuery.validator.setDefaults({
        success: 'valid',
        errorClass: 'validation-error-msg',
        errorPlacement: function(error, element) {
            var label = element.parent().find('span.label-name');
            label.addClass('validation-error');
            element.removeClass('validation-error-msg');
            element.addClass('input-validation-error');
            error.insertAfter(element);
        },
        unhighlight: function(element, errorClass, validClass) {
            var validElements = $(this.currentForm).find('.valid');
            for (var i=0; i < validElements.length; i++) {
                var elementId = validElements[i].control.id;
                var label = $('#' + elementId).parent().find('span.label-name');
                label.removeClass('validation-error');
                $('#' + elementId).removeClass('input-validation-error');
                $('label[for="' + elementId + '"]').remove();
            }
        }
    });
</script>
@endsection