@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf create-customer">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Create Customer (retry)</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Create Customer (retry)</h1>

                <p>Required fields are denoted by an asterisk (*).</p>

                <form id="create-customer-form" class="setting-form medium cf" action="{{ route('wl-retry-cust-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="customer-info" class="cf">
                        <div id="business" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && intval(old('businessType', Auth::user()->dwollaBusinessType)) > 3) class="lfloat pr25 rborder" @else class="m0a" @endif>
                            <h2>Business Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Business Type *</span>
                                <select name="businessType" id="businessType" required>
                                    <option value="">Select a Business Type</option>
                                    <option value="1" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "1") selected @endif>Sole proprietorships</option>
                                    <option value="2" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "2") selected @endif>Unincorporated association</option>
                                    <option value="3" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "3") selected @endif>Trust</option>
                                    <option value="4" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "4") selected @endif>Corporation</option>
                                    <option value="5" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "5") selected @endif>Public corporations</option>
                                    <option value="6" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "6") selected @endif>Non-profits</option>
                                    <option value="7" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "7") selected @endif>LLCs</option>
                                    <option value="8" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && old('businessType', Auth::user()->dwollaBusinessType) == "8") selected @endif>Partnerships, LP’s, LLP’s</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Business Classification *</span>
                                <select name="businessClassification" id="businessClassification" required>
                                    <option value="">Select a Business Classification</option>
                                    @foreach ($busClassifications->_embedded->{'business-classifications'} as $busClassification)
                                    <option value="{{ $busClassification->id }}">{{ $busClassification->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Industrial Classification * (must select Business Classification first)</span>
                                <select name="industrialClassification" id="industrialClassification" required></select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">First Name *</span>
                                <input type="text" placeholder="John" name="firstName" id="firstName" value="{{ old('firstName', $custInfo->firstName) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name *</span>
                                <input type="text" placeholder="Smith" name="lastName" id="lastName" value="{{ old('lastName', $custInfo->lastName) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Email *</span>
                                <input type="email" placeholder="john.smith@acme.com" name="email" id="email" value="{{ old('email', $custInfo->email) }}" required>
                            </div>

                            <input type="hidden" name="type" value="business">

                            <div id="dobWrapper" class="label-wrapper" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && intval(old('businessType', Auth::user()->dwollaBusinessType)) > 3) style="display:none;" @endif>
                                <span class="label-name">Date of Birth *</span>
                                <input type="text" placeholder="YYYY-MM-DD" name="dateOfBirth" id="dateOfBirth" value="{{ old('dateOfBirth') }}" required>
                            </div>
   
                            <div id="ssnWrapper" class="label-wrapper" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && intval(old('businessType', Auth::user()->dwollaBusinessType)) > 3) style="display:none;" @endif>
                                <span class="label-name">SSN * (Full nine digits of the business owner’s Social Security Number.)</span>
                                <input type="text" name="ssn" id="ssn" maxlength="11" placeholder="123-45-6789" value="{{ old('ssn') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1 * (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="address1" id="address1" placeholder="" value="{{ old('address1', $custInfo->address1) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" name="address2" id="address2" value="{{ old('address2', $custInfo->address2) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City *</span>
                                <input type="text" name="city" id="city" value="{{ old('city', $custInfo->city) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State *</span>
                                <select name="state" id="state" required>
                                    <option value="">Select a State</option>
                                    <option value="AL" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty(old('state', $custInfo->state)) && old('state', $custInfo->state) == "WY") selected @endif>Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Postal Code *</span>
                                <input type="text" name="postalCode" id="postalCode" value="{{ old('postalCode', $custInfo->postalCode) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Business Name *</span>
                                <input type="text" name="businessName" id="businessName" value="{{ old('businessName', $custInfo->businessName) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Doing Business As</span>
                                <input type="text" name="doingBusinessAs" id="doingBusinessAs" value="{{ old('doingBusinessAs', $custInfo->doingBusinessAs) }}">
                            </div>

                            <div id="einWrapper" class="label-wrapper">
                                @if (!empty(old('businessType', Auth::user()->dwollaBusinessType)) && intval(old('businessType', Auth::user()->dwollaBusinessType)) > 3)
                                <span class="label-name">EIN *</span>
                                <input type="text" name="ein" id="ein" placeholder="12-3456789" value="{{ old('ein') }}" required>
                                @else
                                <span class="label-name">EIN</span>
                                <input type="text" name="ein" id="ein" placeholder="12-3456789" value="{{ old('ein') }}">
                                @endif
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Website</span>
                                <input type="text" name="website" id="website" placeholder="https://www.domain.com" value="{{ old('website', $custInfo->website) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Phone</span>
                                <input type="text" name="phone" id="phone" placeholder="9045551212" value="{{ old('phone', $custInfo->phone) }}">
                            </div>
                        </div>
                        <div id="controller" class="rfloat pl25" @if(!empty(old('businessType', Auth::user()->dwollaBusinessType)) && intval(old('businessType', Auth::user()->dwollaBusinessType)) < 4) style="display:none;" @endif>
                            <h2>Controller Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">First Name *</span>
                                <input type="text" placeholder="Jane" name="cntlrFirstName" id="cntlrFirstName" value="{{ old('cntlrFirstName', $custInfo->controller->firstName) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name *</span>
                                <input type="text" placeholder="Smith" name="cntlrLastName" id="cntlrLastName" value="{{ old('cntlrLastName', $custInfo->controller->lastName) }}" required>
                            </div>
        
                            <div class="label-wrapper">
                                <span class="label-name">Title *</span>
                                <input type="text" placeholder="CEO" name="cntlrTitle" id="cntlrTitle" value="{{ old('cntlrTitle', $custInfo->controller->title) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Date of Birth *</span>
                                <input type="text" placeholder="YYYY-MM-DD" name="cntlrDateOfBirth" id="cntlrDateOfBirth" value="{{ old('cntlrDateOfBirth') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Country *</span>
                                <select name="cntlrCountry" id="cntlrCountry" required>
                                    <option value="">Select a Country</option>
                                    <option value="US" @if(!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) == "US") selected @endif>United States of America</option>
                                    <option value="GB" @if(!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) == "GB") selected @endif>United Kingdom of Great Britain and Northern Ireland</option>
                                </select>
                            </div>

                            <div id="cntlrSsnWrapper" class="label-wrapper">
                                @if (!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) != "US")
                                <span class="label-name">SSN (Full nine digits of controller’s Social Security Number.)</span>
                                <input type="text" name="cntlrSsn" id="cntlrSsn" maxlength="11" placeholder="123-45-6789" value="{{ old('cntlrSsn') }}">
                                @else
                                <span class="label-name">SSN * (Full nine digits of controller’s Social Security Number.)</span>
                                <input type="text" name="cntlrSsn" id="cntlrSsn" maxlength="11" placeholder="123-45-6789" value="{{ old('cntlrSsn') }}" required>
                                @endif
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1 * (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="cntlrAddress1" id="cntlrAddress1" placeholder="" value="{{ old('cntlrAddress1', $custInfo->controller->address->address1) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" name="cntlrAddress2" id="cntlrAddress2" value="{{ old('cntlrAddress2', $custInfo->controller->address->address2) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 3</span>
                                <input type="text" name="cntlrAddress3" id="cntlrAddress3" value="{{ old('cntlrAddress3', $custInfo->controller->address->address3) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City *</span>
                                <input type="text" name="cntlrCity" id="cntlrCity" value="{{ old('cntlrCity', $custInfo->controller->address->city) }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State/Province/Region *</span>
                                <select name="cntlrStateProvinceRegion" id="cntlrStateProvinceRegion" required>
                                    <option value="">Select a State/Province/Region</option>
                                    <option value="AL" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "WY") selected @endif>Wyoming</option>
                                    <option value="XR" @if(!empty(old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion)) && old('cntlrStateProvinceRegion', $custInfo->controller->address->stateProvinceRegion) == "XR") selected @endif>Redcar and Cleveland</option>
                                </select>
                            </div>

                            <div id="cntlrPostalCodeWrapper" class="label-wrapper">
                                @if (!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) != "US")
                                <span class="label-name">Postal Code</span>
                                <input type="text" name="cntlrPostalCode" id="cntlrPostalCode" value="{{ old('cntlrPostalCode', $custInfo->controller->address->postalCode) }}">
                                @else
                                <span class="label-name">Postal Code *</span>
                                <input type="text" name="cntlrPostalCode" id="cntlrPostalCode" value="{{ old('cntlrPostalCode', $custInfo->controller->address->postalCode) }}" required>
                                @endif
                            </div>

                            <div id="cntlrPassportNumberWrapper" class="label-wrapper" @if(!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) != "US") style="display:block;" @else style="display:none;" @endif>
                                <span class="label-name">Passport Number</span>
                                <input type="text" name="cntlrPassportNumber" id="cntlrPassportNumber" value="{{ old('cntlrPassportNumber') }}">
                            </div>

                            <div id="cntlrPassportCountryWrapper" class="label-wrapper" @if(!empty(old('cntlrCountry', $custInfo->controller->address->country)) && old('cntlrCountry', $custInfo->controller->address->country) != "US") style="display:block;" @else style="display:none;" @endif>
                                <span class="label-name">Passport Country</span>
                                <select name="cntlrPassportCountry" id="cntlrPassportCountry">
                                    <option value="">Select a Country</option>
                                    <option value="GB" @if(!empty(old('cntlrPassportCountry')) && old('cntlrPassportCountry') == "GB") selected @endif>United Kingdom of Great Britain and Northern Ireland</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="certify-info">
                        By clicking the Save button, I, {{ Auth::user()->wlFullName }}, hereby certify, to the best of my knowledge, that the information provided above is 
                        complete and correct.
                    </p>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="retry-customer-save" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        let form = $('#create-customer-form');

        form.validate({
            rules: {
                ein: {
                    pattern: '[0-9]{2}-[0-9]{7}'
                },
                cntlrDateOfBirth: {
                    pattern: '[0-9]{4}-[0-9]{2}-[0-9]{2}'
                },
                cntlrSsn: {
                    pattern: '[0-9]{3}-[0-9]{2}-[0-9]{4}'
                }
            },
            messages: {
                ein: {
                    pattern: 'Format must be XX-XXXXXXX where X is 0 - 9.'
                },
                cntlrDateOfBirth: {
                    pattern: 'Format must be YYYY-MM-DD.'
                },
                cntlrSsn: {
                    pattern: 'Format must be XXX-XX-XXXX where X is 0 - 9.'
                }
            }
        });

        $('#ach-wizard-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings';
        });

        $('#retry-customer-save').click(function() {
            if ($('#businessType').val() !== '') {
                if (form.valid())
                    $('#create-customer-form').submit();
            }
        });

        let address1 = document.getElementById('address1'),
            address1SearchBox = new google.maps.places.SearchBox(address1);

        address1SearchBox.addListener('places_changed', function() {
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
                        document.getElementById('city').value = address.long_name;
                        break;
                    case 'administrative_area_level_1':
                        document.getElementById('state').value = address.short_name;
                        break;
                    case 'postal_code':
                        document.getElementById('postalCode').value = address.short_name;
                        break;
                }
            }

            document.getElementById('address1').value = street_number + ' ' + route;
        });

        $('#businessType').on('change', function() {
            if ($(this).val() !== '') {
                if (parseInt($(this).val(), 10) > 3) {
                    $('#dobWrapper').hide();
                    $('#ssnWrapper').hide();
                    $('#einWrapper .label-name').html('EIN *');
                    $('#ein').attr('required', true);
                    $('#business').removeClass('m0a').addClass('lfloat pr25 rborder');
                    $('#controller').show();
                } else {
                    $('#controller').hide();
                    $('#dobWrapper').show();
                    $('#ssnWrapper').show();
                    $('#einWrapper .label-name').html('EIN');
                    $('#ein').removeAttr('required');
                    $('#business').removeClass('lfloat pr25 rborder').addClass('m0a');
                }
            }
        });

        $('#cntlrCountry').on('change', function() {
            if ($(this).val() !== '') {
                switch ($(this).val()) {
                    case 'GB':
                        $('#cntlrSsnWrapper .label-name').html('SSN (Full nine digits of controller’s Social Security Number.)');
                        $('#cntlrSsn').removeAttr('required');
                        $('#cntlrPostalCodeWrapper .label-name').html('Postal Code');
                        $('#cntlrPostalCode').removeAttr('required');
                        $('#cntlrPassportNumberWrapper').show();
                        $('#cntlrPassportCountryWrapper').show();
                        break;
                    case 'US':
                        $('#cntlrSsnWrapper .label-name').html('SSN * (Full nine digits of controller’s Social Security Number.)');
                        $('#cntlrSsn').attr('required', true);
                        $('#cntlrPostalCodeWrapper .label-name').html('Postal Code *');
                        $('#cntlrPostalCode').attr('required', true);
                        $('#cntlrPassportNumberWrapper').hide();
                        $('#cntlrPassportCountryWrapper').hide();
                        break;
                }
            }
        });

        let busClassifications = <?= json_encode($busClassifications) ?>,
        busClassificationId = '',
        indClassificationHtml = '';

        $('#businessClassification').on('change', function() {
            if ((busClassificationId = $(this).val()) !== '') {
                for (let i = 0; i < busClassifications._embedded['business-classifications'].length - 1; i++) {
                    if (busClassificationId == busClassifications._embedded['business-classifications'][i].id) {
                        indClassificationHtml = '';
                        for (let j = 0; j < busClassifications._embedded['business-classifications'][i]._embedded['industry-classifications'].length - 1; j++) {
                            indClassificationHtml += '<option value="' + busClassifications._embedded['business-classifications'][i]._embedded['industry-classifications'][j].id + '">' + busClassifications._embedded['business-classifications'][i]._embedded['industry-classifications'][j].name + '</option>';
                        }
                        $('#industrialClassification').html(indClassificationHtml);
                    }
                }
            }
        });

        @if (!empty(old('businessType')))
        $('#businessClassification').val('{{ old('businessClassification') }}').trigger('change');
        $('#industrialClassification').val('{{ old('industrialClassification') }}');
        @endif

        let cntlrAddress1 = document.getElementById('cntlrAddress1'),
        cntlrAddress1SearchBox = new google.maps.places.SearchBox(cntlrAddress1);

        cntlrAddress1SearchBox.addListener('places_changed', function() {
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
                        document.getElementById('cntlrCity').value = address.long_name;
                        break;
                    case 'administrative_area_level_1':
                        document.getElementById('cntlrStateProvinceRegion').value = address.short_name;
                        break;
                    case 'postal_code':
                        document.getElementById('cntlrPostalCode').value = address.short_name;
                        break;
                }
            }

            document.getElementById('cntlrAddress1').value = street_number + ' ' + route;
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