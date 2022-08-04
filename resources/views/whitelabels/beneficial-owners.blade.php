@extends('layouts.whitelabel')
@section('content')

<section class="full-page bg-color fullpage cf beneficial-owners">
    <div class="full-pg-content-container top-padding cf">
        <div class="wrapper cf">

            <!-- Add A New Member -->
            <div class="community-tab-content-wrapper active cf">

                <div class="breadcrumb-wrapper cf">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('wl-settings') }}" title="Settings">Settings</a></li>
                        <li><a href="{{ route('wl-payment-settings') }}" title="Payment Settings">Payment Settings</a></li>
                        <li class="active-page">Beneficial Owners</li>
                    </ul>
                </div>

                <div class="r-side">
                    <div class="btn primary">
                        <a href="{{ route('wl-create-beneficial')}}" title="Add Beneficial Owner">Add Beneficial Owner</a>
                    </div>
                </div>

                <h1 class="color h1-border">Beneficial Owners</h1>

                <p>
                    Dwolla's financial institution partners require, per the federal government, that we collect and validate ownership information for companies that have 
                    individuals and/or controllers (not corporations or other legal entities) that own 25% or more of {{ Auth::user()->wlDba }}. Please add as many owners' 
                    information that meet this criteria. 
                    <span @if (empty($custInfo->_links->{'certify-beneficial-ownership'})) style="display:none;" @endif>
                        If no one owns at least 25% of {{ Auth::user()->wlDba }} please do not enter any information and click Submit.
                    </span>
                </p>
                    
                <table class="beneficial-owners-table">
                    <thead class="thead">
                        <tr>
                            <th class="col-1">First Name</th>
                            <th class="col-2">Last Name</th>
                            <th class="col-3">Status</th>
                            <th class="col-4" colspan="2">Options</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($beneficialOwners->_embedded->{'beneficial-owners'} as $beneficialOwner)
                        <tr>
                            <td class="col-1">{{ $beneficialOwner->firstName }}</td>
                            <td class="col-2">{{ $beneficialOwner->lastName }}</td>
                            <td class="col-3">{{ $beneficialOwner->verificationStatus }}</td>
                            @if ($beneficialOwner->verificationStatus != 'verified')
                            <td class="col-4">
                                <div class="edit-icon-wrapper">
                                    @if ($beneficialOwner->verificationStatus == 'incomplete')
                                    <a href="{{ route('wl-update-beneficial', $beneficialOwner->id) }}" class="edit-btn" title="Edit Beneficial Owner">edit</a>
                                    @elseif ($beneficialOwner->verificationStatus == 'document')
                                    <a href="{{ route('wl-beneficial-documents', $beneficialOwner->id) }}" class="upload-doc-btn" title="Beneficial Owner Documents">upload</a>
                                    @endif
                                </div>
                            </td>
                            @endif
                            @if ($beneficialOwner->verificationStatus == 'verified')
                            <td class="col-5 w10" colspan="2">
                            @else
                            <td class="col-5">
                            @endif
                                <div class="delete-icon-wrapper">
                                    <a href="{{ route('wl-delete-beneficial', $beneficialOwner->id) }}" class="delete" title="Delete Beneficial Owner">delete</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <form id="certify-ownership-form" class="setting-form medium cf" action="{{ route('wl-certify-ownership') }}" method="post" @if (empty($custInfo->_links->{'certify-beneficial-ownership'})) style="display:none;" @endif>
                    {{ csrf_field() }}
                     
                    <div class="certify-region cf">
                        <div class="certify-cb-wrapper">
                            <input type="checkbox" value="1" name="certifyOwnership" id="certify-ownership-cb">
                        </div>
                        <label for="certify-ownership-cb">
                            I, {{ Auth::user()->wlFullName }}, acknowledge that I have entered complete and truthful information for any individual owner that owns 25% or more of {{ Auth::user()->wlDba }}.
                        </label>
                    </div>

                    @if (!empty($custInfo->_links->{'verify-beneficial-owners'}))
                    <input type="hidden" name="beneficialOwnersVerified" value="0">
                    @else
                    <input type="hidden" name="beneficialOwnersVerified" value="1">
                    @endif

                    <input type="hidden" id="beneficial-owners-count" name="beneficialOwnersCount" value="">
                </form>

                <div class="button-wrapper cf" @if (empty($custInfo->_links->{'certify-beneficial-ownership'})) style="display:none;" @endif>
                    <div id="beneficial-owners-cancel" class="label-wrapper lfloat">
						<button class="btn btn-danger" type="button">Cancel</button>
                    </div>
                    <div id="beneficial-owners-certify" class="label-wrapper rfloat save-btn-wrapper">
                        <button class="btn btn-success" type="button">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#beneficial-owners-count').val($('.beneficial-owners-table .tbody tr').length);

        $('#beneficial-owners-cancel').click(function() {
            location = location.protocol + '//' + location.hostname + '/whitelabel/settings/payment-settings';
        });

        $('#beneficial-owners-certify').click(function() {
            $('#certify-ownership-form').submit();
        });
    });
</script>

@endsection