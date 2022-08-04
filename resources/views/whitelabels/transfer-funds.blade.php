@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf transfer-funds">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">
            <div class="community-tab-content-wrapper active cf">
                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Transfer Funds</li>
                    </ul>
                </div>

                <h1 class="color h1-border">Transfer Funds</h1>

                <ul class="xfer-progress-bar cf">
                    <li class="step-1 active">Choose Investor</li>
                    <li class="step-2">Enter Amount</li>
                    <li class="step-3">Review Transfer</li>
                </ul>

                <p id="required-fields" style="display:none;">Required fields are denoted by an asterisk (*).</p>

                <form id="transfer-funds-form" class="setting-form medium cf" action="{{ route('wl-transfer-funds-save') }}" autocomplete="off" method="post">
                    {{ csrf_field() }}
                    
                    <div id="investor-section">
                        <p>Required fields are denoted by an asterisk (*).</p>
                        <div class="label-wrapper">
                            <span class="label-name">Project *</span>
                            <select name="projectId" id="projectId" required>
                                <option value="">Select a Project</option>
                                @foreach ($projects as $project)
                                <option value="{{ $project->projectId }}">{{ $project->projectLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="label-wrapper">
                            <span class="label-name">Investor *</span>
                            <select name="subscriberId" id="subscriberId" required>
                                <option value="">Select an Investor</option>
                            </select>
                        </div>

                        <span class="label-name">Investment *</span>
                        <table class="transfer-funds-table">
                            <thead class="thead">
                                <tr>
                                    <th class="col-1">Select</th>
                                    <th class="col-2">Funded Amount</th>
                                    <th class="col-3">Funded Date</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                               
                            </tbody>
                        </table>
                    </div>
                    <div id="amount-section" style="display:none;">
                        <p>Required fields are denoted by an asterisk (*).</p>
                        <table class="transfer-info-table">
                            <tbody>
                                <tr>
                                    <th>Project</th>
                                    <td class="project-info"></td>
                                </tr>
                                <tr>
                                    <th>Investor</th>
                                    <td class="inv-info"></td>
                                </tr>
                                <tr>
                                    <th>Funded Amount</th>
                                    <td class="funded-amt-info"></td>
                                </tr>
                                <tr>
                                    <th>Funded Date</th>
                                    <td class="funded-date-info"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="label-wrapper">
                            <span class="label-name">Amount *</span>
                            <input type="text" placeholder="10.00" name="amount" id="amount" required>
                        </div>
                    </div>
                    <div id="review-section" style="display:none;">
                    <table class="transfer-info-table">
                            <tbody>
                                <tr>
                                    <th>Project</th>
                                    <td class="project-info"></td>
                                </tr>
                                <tr>
                                    <th>Investor</th>
                                    <td class="inv-info"></td>
                                </tr>
                                <tr>
                                    <th>Funded Amount</th>
                                    <td class="funded-amt-info"></td>
                                </tr>
                                <tr>
                                    <th>Funded Date</th>
                                    <td class="funded-date-info"></td>
                                </tr>
                                <tr>
                                    <th>Transfer Amount</th>
                                    <td class="transfer-amt-info"></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Review the above information for accuracy and confirm by clicking the Submit button.</p>
                    </div>
                </form>

                <div class="button-wrapper cf">
                    <div id="ach-wizard-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="ach-wizard-save" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Submit</button>
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
            form = $('#transfer-funds-form');

        form.validate({
            rules: {
                amount: {
                    pattern: '[0-9]+\\.[0-9]{2}'
                }
            },
            messages: {
                amount: {
                    pattern: 'Format must be dd.cc (d - Dollars, c - Cents).'
                }
            }
        });

        if ($('#projectId').val() !== '')
            $('#projectId').val('');

        if ($('#subscriberId').val() !== '')
            $('#subscriberId').val('');

        $('#ach-wizard-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings';
        });
        
        $('#ach-wizard-prev').click(function() {
            wizardPrevious();
        });

        $('#ach-wizard-next').click(function() {
            if (step == 1) {
                if (form.valid() && $('.transfer-funds-table .tbody input[name="investmentId"]:checked').length) {
                    let projectId = document.getElementById('projectId'),
                        subscriberId = document.getElementById('subscriberId');

                    $('#transfer-funds-form .project-info').text(projectId.options[projectId.selectedIndex].text);
                    $('#transfer-funds-form .inv-info').text(subscriberId.options[subscriberId.selectedIndex].text);
                    let selected_row = $('.transfer-funds-table .tbody input[name="investmentId"]:checked').parent().parent();
                    $('#transfer-funds-form .funded-amt-info').text(selected_row.find('td.col-2').text());
                    $('#transfer-funds-form .funded-date-info').text(selected_row.find('td.col-3').text());
                    wizardNext();
                }
            } else if (step == 2) {
                $('#transfer-funds-form .transfer-amt-info').text('$' + $('#amount').val());

                if (form.valid())
                    wizardNext();
            }
        });

        $('#ach-wizard-save').click(function() {
            if (form.valid())
                $('#transfer-funds-form').submit();
        });

        function wizardPrevious() {
            step--;
            
            switch (step) {
                case 1:
                    $('ul.xfer-progress-bar li.step-2').removeClass('active');
                    $('ul.xfer-progress-bar li.step-1').removeClass('finished').addClass('active');
                    $('#investor-section').show();
                    $('#amount-section').hide();
                    $('#ach-wizard-prev').hide();
                    break;
                case 2:
                    $('ul.xfer-progress-bar li.step-3').removeClass('active');
                    $('ul.xfer-progress-bar li.step-2').removeClass('finished').addClass('active');
                    $('#amount-section').show();
                    $('#review-section').hide();
                    $('#ach-wizard-next').show();
                    $('#ach-wizard-save').hide();
                    break;
            }
        }

        function wizardNext() {
            step++;
            
            switch (step) {
                case 2:
                    $('ul.xfer-progress-bar li.step-2').addClass('active');
                    $('ul.xfer-progress-bar li.step-1').removeClass('active').addClass('finished');
                    $('#investor-section').hide();
                    $('#amount-section').show();
                    $('#ach-wizard-prev').show();
                    break;
                case 3:
                    $('ul.xfer-progress-bar li.step-3').addClass('active');
                    $('ul.xfer-progress-bar li.step-2').removeClass('active').addClass('finished');
                    $('#amount-section').hide();
                    $('#review-section').show();
                    $('#ach-wizard-next').hide();
                    $('#ach-wizard-save').show();
                    break;
            }
        }

        $('#projectId').on('change', function() {
            let projectId = $(this).val(),
                html = '';

            if (projectId !== '') {
                $.ajax({
                    url: '/whitelabel/settings/payment-settings/funded-investors',
                    type: 'POST',
                    data: {
                        project_id: projectId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    let funded_invs = JSON.parse(data);

                    html = '<option value="">Select an Investor</option>';

                    for (let inv of funded_invs.investors) {
                        html += '<option value="' + inv.subscriberId + '">' + inv.subscriberFullName + '</option>';
                    }

                    $('#subscriberId').html(html);
                }).fail(function(jqXHR, textStatus) {
                    console.log('Request failed: ' + textStatus);
                });
            } else
                $('#subscriberId').html('<option value="">Select an Investor</option>');
        });

        $('#subscriberId').on('change', function() {
            let subscriberId = $(this).val(),
                html = '';

            if (subscriberId !== '') {
                $.ajax({
                    url: '/whitelabel/settings/payment-settings/investments',
                    type: 'POST',
                    data: {
                        project_id: $('#projectId').val(),
                        subscriber_id: subscriberId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(data) {
                    let investments = JSON.parse(data);
                    
                    for (let investment of investments.investments) {
                        html += '<tr><td class="col-1"><input type="radio" name="investmentId" value="' + investment.investmentId + '"></td>' +
                            '<td class="col-2">' + investment.pledge + '</td><td class="col-3">' + investment.funded_at + '</td></tr>';
                    }

                    $('table.transfer-funds-table .tbody').html(html);

                    if (investments.investments.length == 1)
                        $('.tbody input[name="investmentId"]').prop('checked', true);
                }).fail(function(jqXHR, textStatus) {
                    console.log('Request failed: ' + textStatus);
                });
            } else
                $('table.transfer-funds-table .tbody').html('');
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