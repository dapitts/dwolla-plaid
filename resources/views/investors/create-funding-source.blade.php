@extends('layouts.investor')
@section('content')

<section class="full-page bg-color fullpage cf funding-source">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('inv-settings') }}" title="Settings">Settings</a></li>
                        <li class="active-page">Add Funding Source</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Add Funding Source</h1>

                <p>Required fields are denoted by an asterisk (*).</p>

                <form id="funding-source-form" class="setting-form medium cf" action="{{ route('inv-add-funding-src-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="funding-source-info" class="cf">
                        <div id="account-info" class="lfloat pr25 rborder">
                            <h2>Account Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Account Name *</span>
                                <input type="text" name="accountName" id="accountName" maxlength="50" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Account Type *</span>
                                <select name="accountType" id="accountType" required>
                                    <option value="">Select Account Type</option>
                                    <option value="checking">Checking</option>
                                    <option value="savings">Savings</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Account Number *</span>
                                <input type="text" name="accountNumber" id="accountNumber" maxlength="17" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">ABA Routing Transit Number *</span>
                                <input type="text" name="abaRoutingTransitNumber" id="abaRoutingTransitNumber" maxlength="9" required>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Wire Transfer Routing Number</span>
                                <input type="text" name="wireTransferRoutingNumber" id="wireTransferRoutingNumber" maxlength="9">
                            </div>
                        </div>
                        <div id="bank-info" class="rfloat pl25">
                            <h2>Bank Information</h2>

                            <div class="label-wrapper">
                                <span class="label-name">Bank Name</span>
                                <input type="text" name="bankName" id="bankName">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">Street Address (start typing in this field and choose the address from the dropdown)</span>
                                <input type="text" name="bankStreet" id="bankStreet" placeholder="">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">City</span>
                                <input type="text" name="bankCity" id="bankCity">
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">State</span>
                                <select name="bankState" id="bankState">
                                    <option value="">Select a State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District Of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div>

                            <div class="label-wrapper">
                                <span class="label-name">ZIP Code</span>
                                <input type="text" name="bankZipCode" id="bankZipCode">
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
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        let form = $('#funding-source-form');

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

        $('#ach-wizard-cancel').click(function() {
            location.href = '{{ route('inv-settings') }}';
        });

        $('#funding-source-save').click(function() {
            if (form.valid())
                form.submit();
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