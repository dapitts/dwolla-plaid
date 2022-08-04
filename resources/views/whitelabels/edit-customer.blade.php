@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf edit-customer">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Edit Customer</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Edit Customer</h1>

                <form id="create-customer-form" class="setting-form medium cf" action="{{ route('wl-update-cust-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="customer-info" class="cf">
                        <div id="business" @if(empty($custInfo->controller)) class="m0a" @else class="lfloat pr25 rborder" @endif>
                            <h2>Business Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">First Name</span>
                                <input type="text" class="read-only" placeholder="John" name="firstName" id="firstName" value="@if(!empty($custInfo->firstName)){{$custInfo->firstName}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name</span>
                                <input type="text" class="read-only" placeholder="Smith" name="lastName" id="lastName" value="@if(!empty($custInfo->lastName)){{$custInfo->lastName}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Email</span>
                                <input type="email" placeholder="john.smith@acme.com" name="email" id="email" value="{{ old('email', $custInfo->email) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1 (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="address1" id="address1" placeholder="" value="{{ old('address1', $custInfo->address1) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" name="address2" id="address2" value="{{ old('address2', $custInfo->address2) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City</span>
                                <input type="text" name="city" id="city" value="{{ old('city', $custInfo->city) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State</span>
                                <select name="state" id="state">
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
                                <span class="label-name">Postal Code</span>
                                <input type="text" name="postalCode" id="postalCode" value="{{ old('postalCode', $custInfo->postalCode) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Business Name</span>
                                <input type="text" class="read-only" name="businessName" id="businessName" value="@if(!empty($custInfo->businessName)){{$custInfo->businessName}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Doing Business As</span>
                                <input type="text" name="doingBusinessAs" id="doingBusinessAs" value="{{ old('doingBusinessAs', $custInfo->doingBusinessAs) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Website</span>
                                <input type="text" name="website" id="website" value="{{ old('website', $custInfo->website) }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Phone</span>
                                <input type="text" name="phone" id="phone" placeholder="9045551212" value="{{ old('phone', $custInfo->phone) }}">
                            </div>
                        </div>
                        <div id="controller" class="rfloat pl25" @if(empty($custInfo->controller)) style="display:none;" @endif>
                            <h2>Controller Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">First Name</span>
                                <input type="text" class="read-only" placeholder="Jane" name="cntlrFirstName" id="cntlrFirstName" value="@if(!empty($custInfo->controller->firstName)){{$custInfo->controller->firstName}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name</span>
                                <input type="text" class="read-only" placeholder="Smith" name="cntlrLastName" id="cntlrLastName" value="@if(!empty($custInfo->controller->lastName)){{$custInfo->controller->lastName}}@endif" readonly>
                            </div>
        
                            <div class="label-wrapper">
                                <span class="label-name">Title</span>
                                <input type="text" class="read-only" placeholder="CEO" name="cntlrTitle" id="cntlrTitle" value="@if(!empty($custInfo->controller->title)){{$custInfo->controller->title}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Address 1</span>
                                <input type="text" class="read-only" name="cntlrAddress1" id="cntlrAddress1" placeholder="" value="@if(!empty($custInfo->controller->address->address1)){{$custInfo->controller->address->address1}}@endif" readonly>
                            </div>

                            @if (!empty($custInfo->controller->address->address2))
                            <div class="label-wrapper">
                                <span class="label-name">Address 2</span>
                                <input type="text" class="read-only" name="cntlrAddress2" id="cntlrAddress2" value="{{$custInfo->controller->address->address2}}" readonly>
                            </div>
                            @endif

                            @if (!empty($custInfo->controller->address->address3))
                            <div class="label-wrapper">
                                <span class="label-name">Address 3</span>
                                <input type="text" class="read-only" name="cntlrAddress3" id="cntlrAddress3" value="{{$custInfo->controller->address->address3}}" readonly>
                            </div>
                            @endif

                            <div class="label-wrapper">
                                <span class="label-name">City</span>
                                <input type="text" class="read-only" name="cntlrCity" id="cntlrCity" value="@if(!empty($custInfo->controller->address->city)){{$custInfo->controller->address->city}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State/Province/Region</span>
                                <select class="read-only" name="cntlrStateProvinceRegion" id="cntlrStateProvinceRegion">
                                    <option value="">Select a State/Province/Region</option>
                                    <option value="AL" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "WY") selected @endif>Wyoming</option>
                                    <option value="XR" @if(!empty($custInfo->controller->address->stateProvinceRegion) && $custInfo->controller->address->stateProvinceRegion == "XR") selected @endif>Redcar and Cleveland</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Postal Code</span>
                                <input type="text" class="read-only" name="cntlrPostalCode" id="cntlrPostalCode" value="@if(!empty($custInfo->controller->address->postalCode)){{$custInfo->controller->address->postalCode}}@endif" readonly>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Country</span>
                                <input type="text" class="read-only" name="cntlrCountry" id="cntlrCountry" value="@if(!empty($custInfo->controller->address->country)){{$custInfo->controller->address->country}}@endif" readonly>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="ach-wizard-save" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#ach-wizard-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings';
        });

        $('#ach-wizard-save').click(function() {
            $('#create-customer-form').submit();
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
    });
</script>
@endsection