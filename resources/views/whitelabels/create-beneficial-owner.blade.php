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
                        <li><a href="{{ route('wl-beneficial-owners') }}" title="Beneficial Owners">Beneficial Owners</a></li>
                        <li class="active-page">Create Beneficial Owner</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Create Beneficial Owner</h1>

                <p>Required fields are denoted by an asterisk (*).</p>

                <form id="create-customer-form" class="setting-form medium cf" action="{{ route('wl-create-beneficial-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="customer-info" class="cf">
                        <div id="business" class="m0a">
                            <h2>Beneficial Owner Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">First Name *</span>
                                <input type="text" placeholder="John" name="firstName" id="firstName" value="{{ old('firstName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Last Name *</span>
                                <input type="text" placeholder="Smith" name="lastName" id="lastName" value="{{ old('lastName') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">SSN * (Full nine-digits of the beneficial owner’s social security number.)</span>
                                <input type="text" name="ssn" id="ssn" maxlength="11" placeholder="123-45-6789" value="{{ old('ssn') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Date of Birth *</span>
                                <input type="text" placeholder="YYYY-MM-DD" name="dateOfBirth" id="dateOfBirth" value="{{ old('dateOfBirth') }}" required>
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
                                <span class="label-name">Address 3</span>
                                <input type="text" name="address3" id="address3" value="{{ old('address3') }}">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City *</span>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State *</span>
                                <select name="stateProvinceRegion" id="stateProvinceRegion" required>
                                    <option value="">Select a State</option>
                                    <option value="AL" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "AL") selected @endif>Alabama</option>
                                    <option value="AK" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "AK") selected @endif>Alaska</option>
                                    <option value="AZ" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "AZ") selected @endif>Arizona</option>
                                    <option value="AR" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "AR") selected @endif>Arkansas</option>
                                    <option value="CA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "CA") selected @endif>California</option>
                                    <option value="CO" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "CO") selected @endif>Colorado</option>
                                    <option value="CT" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "CT") selected @endif>Connecticut</option>
                                    <option value="DE" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "DE") selected @endif>Delaware</option>
                                    <option value="DC" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "DC") selected @endif>District of Columbia</option>
                                    <option value="FL" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "FL") selected @endif>Florida</option>
                                    <option value="GA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "GA") selected @endif>Georgia</option>
                                    <option value="HI" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "HI") selected @endif>Hawaii</option>
                                    <option value="ID" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "ID") selected @endif>Idaho</option>
                                    <option value="IL" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "IL") selected @endif>Illinois</option>
                                    <option value="IN" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "IN") selected @endif>Indiana</option>
                                    <option value="IA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "IA") selected @endif>Iowa</option>
                                    <option value="KS" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "KS") selected @endif>Kansas</option>
                                    <option value="KY" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "KY") selected @endif>Kentucky</option>
                                    <option value="LA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "LA") selected @endif>Louisiana</option>
                                    <option value="ME" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "ME") selected @endif>Maine</option>
                                    <option value="MD" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MD") selected @endif>Maryland</option>
                                    <option value="MA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MA") selected @endif>Massachusetts</option>
                                    <option value="MI" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MI") selected @endif>Michigan</option>
                                    <option value="MN" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MN") selected @endif>Minnesota</option>
                                    <option value="MS" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MS") selected @endif>Mississippi</option>
                                    <option value="MO" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MO") selected @endif>Missouri</option>
                                    <option value="MT" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "MT") selected @endif>Montana</option>
                                    <option value="NE" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NE") selected @endif>Nebraska</option>
                                    <option value="NV" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NV") selected @endif>Nevada</option>
                                    <option value="NH" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NH") selected @endif>New Hampshire</option>
                                    <option value="NJ" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NJ") selected @endif>New Jersey</option>
                                    <option value="NM" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NM") selected @endif>New Mexico</option>
                                    <option value="NY" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NY") selected @endif>New York</option>
                                    <option value="NC" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "NC") selected @endif>North Carolina</option>
                                    <option value="ND" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "ND") selected @endif>North Dakota</option>
                                    <option value="OH" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "OH") selected @endif>Ohio</option>
                                    <option value="OK" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "OK") selected @endif>Oklahoma</option>
                                    <option value="OR" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "OR") selected @endif>Oregon</option>
                                    <option value="PA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "PA") selected @endif>Pennsylvania</option>
                                    <option value="RI" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "RI") selected @endif>Rhode Island</option>
                                    <option value="SC" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "SC") selected @endif>South Carolina</option>
                                    <option value="SD" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "SD") selected @endif>South Dakota</option>
                                    <option value="TN" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "TN") selected @endif>Tennessee</option>
                                    <option value="TX" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "TX") selected @endif>Texas</option>
                                    <option value="UT" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "UT") selected @endif>Utah</option>
                                    <option value="VT" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "VT") selected @endif>Vermont</option>
                                    <option value="VA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "VA") selected @endif>Virginia</option>
                                    <option value="WA" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "WA") selected @endif>Washington</option>
                                    <option value="WV" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "WV") selected @endif>West Virginia</option>
                                    <option value="WI" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "WI") selected @endif>Wisconsin</option>
                                    <option value="WY" @if(!empty(old('stateProvinceRegion')) && old('stateProvinceRegion') == "WY") selected @endif>Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Postal Code *</span>
                                <input type="text" name="postalCode" id="postalCode" value="{{ old('postalCode') }}" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Country * (Two letter country code.)</span>
                                <input type="text" name="country" id="country" placeholder="US" value="{{ old('country') }}" required>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="beneficial-owner-save" class="label-wrapper rfloat save-btn-wrapper">
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
                ssn: {
                    pattern: '[0-9]{3}-[0-9]{2}-[0-9]{4}'
                },
                dateOfBirth: {
                    pattern: '[0-9]{4}-[0-9]{2}-[0-9]{2}'
                },
                country: {
                    pattern: '[A-Z]{2}'
                }
            },
            messages: {
                ssn: {
                    pattern: 'Format must be XXX-XX-XXXX where X is 0 - 9.'
                },
                dateOfBirth: {
                    pattern: 'Format must be YYYY-MM-DD.'
                },
                country: {
                    pattern: 'ISO 3166-1 alpha-2 format is XX where X is A - Z.'
                }
            }
        });

        $('#ach-wizard-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/beneficial-owners';
        });

        $('#beneficial-owner-save').click(function() {
            if (form.valid())
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
                        document.getElementById('stateProvinceRegion').value = address.short_name;
                        break;
                    case 'postal_code':
                        document.getElementById('postalCode').value = address.short_name;
                        break;
                    case 'country':
                        document.getElementById('country').value = address.short_name;
                        break;
                }
            }

            document.getElementById('address1').value = street_number + ' ' + route;
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