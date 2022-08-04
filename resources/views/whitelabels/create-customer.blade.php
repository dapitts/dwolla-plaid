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
                        <li class="active-page">Create Customer</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Create Customer</h1>

                <p id="required-fields" style="display:none;">Required fields are denoted by an asterisk (*).</p>

                <form id="create-customer-form" class="setting-form medium cf" action="{{ route('wl-create-cust-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="dwolla-terms-of-svc">
                        In order to use the payment functionality of this application, you must read the <a id="dwollaTOSLink" href="https://www.dwolla.com/legal/tos/" target="_blank">Dwolla Terms of Service</a> 
                        and accept them by clicking the "I Accept" checkbox. If you decline them, click the "Cancel" button.

                        <div class="cf">
                            <input type="checkbox" value="1" name="acceptDwollaTOS" id="dwolla-tos-cb">
                            <label for="dwolla-tos-cb">I Accept</label>
                        </div>
                    </div>
                    <div id="dwolla-privacy-policy" style="display:none;">
                        Additionally, you must read the <a id="dwollaPPLink" href="https://www.dwolla.com/legal/privacy/" target="_blank">Dwolla Privacy Policy</a> 
                        and accept it by clicking the "I Accept" checkbox. If you decline it, click the "Cancel" button.

                        <div class="cf">
                            <input type="checkbox" value="1" name="acceptDwollaPP" id="dwolla-pp-cb">
                            <label for="dwolla-pp-cb">I Accept</label>
                        </div>
                    </div>
                    <div id="customer-info" class="cf" style="display:none;">
                        <div id="business" @if(!empty(old('businessType')) && intval(old('businessType')) > 3) class="lfloat pr25 rborder" @else class="m0a" @endif>
                            <h2>Business Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Business Type *</span>
                                <select name="businessType" id="businessType" required>
                                    <option value="">Select a Business Type</option>
                                    <option value="1" @if(!empty(old('businessType')) && old('businessType') == "1") selected @endif>Sole proprietorships</option>
                                    <option value="2" @if(!empty(old('businessType')) && old('businessType') == "2") selected @endif>Unincorporated association</option>
                                    <option value="3" @if(!empty(old('businessType')) && old('businessType') == "3") selected @endif>Trust</option>
                                    <option value="4" @if(!empty(old('businessType')) && old('businessType') == "4") selected @endif>Corporation</option>
                                    <option value="5" @if(!empty(old('businessType')) && old('businessType') == "5") selected @endif>Public corporations</option>
                                    <option value="6" @if(!empty(old('businessType')) && old('businessType') == "6") selected @endif>Non-profits</option>
                                    <option value="7" @if(!empty(old('businessType')) && old('businessType') == "7") selected @endif>LLCs</option>
                                    <option value="8" @if(!empty(old('businessType')) && old('businessType') == "8") selected @endif>Partnerships, LP’s, LLP’s</option>
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
                                <input type="text" placeholder="John" name="firstName" id="firstName" value="{{ old('firstName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name *</span>
                                <input type="text" placeholder="Smith" name="lastName" id="lastName" value="{{ old('lastName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Email *</span>
                                <input type="email" placeholder="john.smith@acme.com" name="email" id="email" value="{{ old('email') }}" required>
                            </div>

                            <input type="hidden" name="type" value="business">

                            <div id="dobWrapper" class="label-wrapper" @if(!empty(old('businessType')) && intval(old('businessType')) > 3) style="display:none;" @endif>
                                <span class="label-name">Date of Birth *</span>
                                <input type="text" placeholder="YYYY-MM-DD" name="dateOfBirth" id="dateOfBirth" value="{{ old('dateOfBirth') }}" required>
                            </div>
   
                            <div id="ssnWrapper" class="label-wrapper" @if(!empty(old('businessType')) && intval(old('businessType')) > 3) style="display:none;" @endif>
                                <span class="label-name">SSN * (Last four digits of the business owner’s Social Security Number.)</span>
                                <input type="text" name="ssn" id="ssn" maxlength="4" value="{{ old('ssn') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1 * (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="address1" id="address1" placeholder="" value="{{ old('address1') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" name="address2" id="address2" value="{{ old('address2') }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City *</span>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State *</span>
                                <select name="state" id="state" required>
                                    <option value="">Select a State</option>
                                    <option value="AL" @if(!empty(old('state')) && old('state') == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty(old('state')) && old('state') == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty(old('state')) && old('state') == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty(old('state')) && old('state') == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty(old('state')) && old('state') == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty(old('state')) && old('state') == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty(old('state')) && old('state') == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty(old('state')) && old('state') == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty(old('state')) && old('state') == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty(old('state')) && old('state') == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty(old('state')) && old('state') == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty(old('state')) && old('state') == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty(old('state')) && old('state') == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty(old('state')) && old('state') == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty(old('state')) && old('state') == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty(old('state')) && old('state') == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty(old('state')) && old('state') == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty(old('state')) && old('state') == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty(old('state')) && old('state') == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty(old('state')) && old('state') == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty(old('state')) && old('state') == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty(old('state')) && old('state') == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty(old('state')) && old('state') == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty(old('state')) && old('state') == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty(old('state')) && old('state') == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty(old('state')) && old('state') == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty(old('state')) && old('state') == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty(old('state')) && old('state') == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty(old('state')) && old('state') == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty(old('state')) && old('state') == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty(old('state')) && old('state') == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty(old('state')) && old('state') == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty(old('state')) && old('state') == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty(old('state')) && old('state') == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty(old('state')) && old('state') == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty(old('state')) && old('state') == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty(old('state')) && old('state') == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty(old('state')) && old('state') == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty(old('state')) && old('state') == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty(old('state')) && old('state') == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty(old('state')) && old('state') == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty(old('state')) && old('state') == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty(old('state')) && old('state') == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty(old('state')) && old('state') == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty(old('state')) && old('state') == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty(old('state')) && old('state') == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty(old('state')) && old('state') == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty(old('state')) && old('state') == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty(old('state')) && old('state') == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty(old('state')) && old('state') == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty(old('state')) && old('state') == "WY") selected @endif>Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Postal Code *</span>
                                <input type="text" name="postalCode" id="postalCode" value="{{ old('postalCode') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Business Name *</span>
                                <input type="text" name="businessName" id="businessName" value="{{ old('businessName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Doing Business As</span>
                                <input type="text" name="doingBusinessAs" id="doingBusinessAs" value="{{ old('doingBusinessAs') }}">
                            </div>

                            <div id="einWrapper" class="label-wrapper">
                                @if (!empty(old('businessType')) && intval(old('businessType')) > 3)
                                <span class="label-name">EIN *</span>
                                <input type="text" name="ein" id="ein" placeholder="12-3456789" value="{{ old('ein') }}" required>
                                @else
                                <span class="label-name">EIN</span>
                                <input type="text" name="ein" id="ein" placeholder="12-3456789" value="{{ old('ein') }}">
                                @endif
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Website</span>
                                <input type="text" name="website" id="website" placeholder="https://www.domain.com" value="{{ old('website') }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Phone</span>
                                <input type="text" name="phone" id="phone" placeholder="9045551212" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div id="controller" class="rfloat pl25" @if(!empty(old('businessType')) && intval(old('businessType')) > 3) style="display:block;" @else style="display:none;" @endif>
                            <h2>Controller Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">First Name *</span>
                                <input type="text" placeholder="Jane" name="cntlrFirstName" id="cntlrFirstName" value="{{ old('cntlrFirstName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name *</span>
                                <input type="text" placeholder="Smith" name="cntlrLastName" id="cntlrLastName" value="{{ old('cntlrLastName') }}" required>
                            </div>
        
                            <div class="label-wrapper">
                                <span class="label-name">Title *</span>
                                <input type="text" placeholder="CEO" name="cntlrTitle" id="cntlrTitle" value="{{ old('cntlrTitle') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Date of Birth *</span>
                                <input type="text" placeholder="YYYY-MM-DD" name="cntlrDateOfBirth" id="cntlrDateOfBirth" value="{{ old('cntlrDateOfBirth') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Country *</span>
                                <select name="cntlrCountry" id="cntlrCountry" required>
                                    <option value="">Select a Country</option>
                                    <option value="US" @if(!empty(old('cntlrCountry')) && old('cntlrCountry') == "US") selected @endif>United States of America</option>
                                    <option value="GB" @if(!empty(old('cntlrCountry')) && old('cntlrCountry') == "GB") selected @endif>United Kingdom of Great Britain and Northern Ireland</option>
                                </select>
                            </div>

                            <div id="cntlrSsnWrapper" class="label-wrapper">
                                @if (!empty(old('cntlrCountry')) && old('cntlrCountry') != "US")
                                <span class="label-name">SSN (Last four digits of controller’s Social Security Number.)</span>
                                <input type="text" name="cntlrSsn" id="cntlrSsn" maxlength="4" value="{{ old('cntlrSsn') }}">
                                @else
                                <span class="label-name">SSN * (Last four digits of controller’s Social Security Number.)</span>
                                <input type="text" name="cntlrSsn" id="cntlrSsn" maxlength="4" value="{{ old('cntlrSsn') }}" required>
                                @endif
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1 * (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="cntlrAddress1" id="cntlrAddress1" placeholder="" value="{{ old('cntlrAddress1') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" name="cntlrAddress2" id="cntlrAddress2" value="{{ old('cntlrAddress2') }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 3</span>
                                <input type="text" name="cntlrAddress3" id="cntlrAddress3" value="{{ old('cntlrAddress3') }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City *</span>
                                <input type="text" name="cntlrCity" id="cntlrCity" value="{{ old('cntlrCity') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State/Province/Region *</span>
                                <select name="cntlrStateProvinceRegion" id="cntlrStateProvinceRegion" required>
                                    <option value="">Select a State/Province/Region</option>
                                    <option value="AL" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "WY") selected @endif>Wyoming</option>
                                    <option value="XR" @if(!empty(old('cntlrStateProvinceRegion')) && old('cntlrStateProvinceRegion') == "XR") selected @endif>Redcar and Cleveland</option>
                                </select>
                            </div>

                            <div id="cntlrPostalCodeWrapper" class="label-wrapper">
                                @if (!empty(old('cntlrCountry')) && old('cntlrCountry') != "US")
                                <span class="label-name">Postal Code</span>
                                <input type="text" name="cntlrPostalCode" id="cntlrPostalCode" value="{{ old('cntlrPostalCode') }}">
                                @else
                                <span class="label-name">Postal Code *</span>
                                <input type="text" name="cntlrPostalCode" id="cntlrPostalCode" value="{{ old('cntlrPostalCode') }}" required>
                                @endif
                            </div>

                            <div id="cntlrPassportNumberWrapper" class="label-wrapper" @if(!empty(old('cntlrCountry')) && old('cntlrCountry') != "US") style="display:block;" @else style="display:none;" @endif>
                                <span class="label-name">Passport Number</span>
                                <input type="text" name="cntlrPassportNumber" id="cntlrPassportNumber" value="{{ old('cntlrPassportNumber') }}">
                            </div>

                            <div id="cntlrPassportCountryWrapper" class="label-wrapper" @if(!empty(old('cntlrCountry')) && old('cntlrCountry') != "US") style="display:block;" @else style="display:none;" @endif>
                                <span class="label-name">Passport Country</span>
                                <select name="cntlrPassportCountry" id="cntlrPassportCountry">
                                    <option value="">Select a Country</option>
                                    <option value="GB" @if(!empty(old('cntlrPassportCountry')) && old('cntlrPassportCountry') == "GB") selected @endif>United Kingdom of Great Britain and Northern Ireland</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p class="certify-info" style="display:none;">
                        By clicking the Save button, I, {{ Auth::user()->wlFullName }}, hereby certify, to the best of my knowledge, that the information provided above is 
                        complete and correct.
                    </p>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="ach-wizard-save" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Save</button>
                    </div>
                    <div id="ach-wizard-next" class="label-wrapper rfloat next-btn-wrapper">
						<button class="btn btn-primary" type="button">Next</button>
                    </div>
                    <div id="ach-wizard-prev" class="label-wrapper rfloat">
						<button class="btn btn-primary" type="button">Previous</button>
					</div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        let step = 1,
            dwollaTOSLinkClicked = false,
            dwollaPPLinkClicked = false,
            form = $('#create-customer-form');

        form.validate({
            rules: {
                dateOfBirth: {
                    pattern: '[0-9]{4}-[0-9]{2}-[0-9]{2}'
                },
                ein: {
                    pattern: '[0-9]{2}-[0-9]{7}'
                },
                cntlrDateOfBirth: {
                    pattern: '[0-9]{4}-[0-9]{2}-[0-9]{2}'
                },
                cntlrCountry: {
                    pattern: '[A-Z]{2}'
                }
            },
            messages: {
                dateOfBirth: {
                    pattern: 'Format must be YYYY-MM-DD.'
                },
                ein: {
                    pattern: 'Format must be XX-XXXXXXX where X is 0 - 9.'
                },
                cntlrDateOfBirth: {
                    pattern: 'Format must be YYYY-MM-DD.'
                },
                cntlrCountry: {
                    pattern: 'ISO 3166-1 alpha-2 format is XX where X is A - Z.'
                }
            }
        });

        if (location.href.indexOf('#step-3') !== -1)
            displayStep3();
        else {
            location.hash = 'step-1';

            if ($('#dwolla-tos-cb').is(':checked'))
                $('#dwolla-tos-cb').prop('checked', false);

            if ($('#dwolla-pp-cb').is(':checked'))
                $('#dwolla-pp-cb').prop('checked', false);
        }

        $('#dwollaTOSLink').click(function() {
            dwollaTOSLinkClicked = true;

            if ($('#dwolla-tos-cb').is(':checked'))
                $('#ach-wizard-next').css('pointer-events', 'auto');
        });

        $('#dwollaPPLink').click(function() {
            dwollaPPLinkClicked = true;

            if ($('#dwolla-pp-cb').is(':checked'))
                $('#ach-wizard-next').css('pointer-events', 'auto');
        });

        $('#ach-wizard-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings';
        });
        
        $('#ach-wizard-prev').click(function() {
            wizardPrevious();
        });

        $('#ach-wizard-next').click(function() {
            wizardNext();
        });

        $('#ach-wizard-save').click(function() {
            if ($('#businessType').val() !== '') {
                if (form.valid())
                    $('#create-customer-form').submit();
            }
        });

        $('#dwolla-tos-cb').on('change', function() {
            if ($(this).is(':checked') && dwollaTOSLinkClicked)
                $('#ach-wizard-next').css('pointer-events', 'auto');
            else
                $('#ach-wizard-next').css('pointer-events', 'none');
        });

        $('#dwolla-pp-cb').on('change', function() {
            if ($(this).is(':checked') && dwollaPPLinkClicked)
                $('#ach-wizard-next').css('pointer-events', 'auto');
            else
                $('#ach-wizard-next').css('pointer-events', 'none');
        });

        function wizardPrevious() {
            step--;
            
            switch (step) {
                case 1:
                    location.hash = 'step-1';
                    $('#dwolla-terms-of-svc').show();
                    $('#dwolla-privacy-policy').hide();
                    $('#ach-wizard-prev').hide();
                    break;
                case 2:
                    location.hash = 'step-2';
                    $('#dwolla-privacy-policy').show();
                    $('#required-fields').hide();
                    $('#customer-info').hide();
                    $('p.certify-info').hide();
                    $('#ach-wizard-next').show();
                    $('#ach-wizard-save').hide();
                    break;
            }
        }

        function wizardNext() {
            step++;
            
            switch (step) {
                case 2:
                    location.hash = 'step-2';
                    $('#dwolla-terms-of-svc').hide();
                    $('#dwolla-privacy-policy').show();
                    $('#ach-wizard-prev').show();

                    if ($('#dwolla-pp-cb').is(':checked') && dwollaPPLinkClicked)
                        $('#ach-wizard-next').css('pointer-events', 'auto');
                    else
                        $('#ach-wizard-next').css('pointer-events', 'none');
                    break;
                case 3:
                    location.hash = 'step-3';
                    $('#dwolla-privacy-policy').hide();
                    $('#required-fields').show();
                    $('#customer-info').show();
                    $('p.certify-info').show();
                    $('#ach-wizard-next').hide();
                    $('#ach-wizard-save').show();
                    break;
            }
        }

        function displayStep3() {
            step = 3;
            dwollaTOSLinkClicked = true,
            $('#dwolla-tos-cb').prop('checked', true);
            dwollaPPLinkClicked = true;
            $('#dwolla-pp-cb').prop('checked', true);

            $('#dwolla-terms-of-svc').hide();
            $('#required-fields').show();
            $('#customer-info').show();
            $('p.certify-info').show();
            $('#ach-wizard-next').css('pointer-events', 'auto');
            $('#ach-wizard-next').hide();
            $('#ach-wizard-prev').show();
            $('#ach-wizard-save').show();
        }

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
                        $('#cntlrSsnWrapper .label-name').html('SSN (Last four digits of controller’s Social Security Number.)');
                        $('#cntlrSsn').removeAttr('required');
                        $('#cntlrPostalCodeWrapper .label-name').html('Postal Code');
                        $('#cntlrPostalCode').removeAttr('required');
                        $('#cntlrPassportNumberWrapper').show();
                        $('#cntlrPassportCountryWrapper').show();
                        break;
                    case 'US':
                        $('#cntlrSsnWrapper .label-name').html('SSN * (Last four digits of controller’s Social Security Number.)');
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