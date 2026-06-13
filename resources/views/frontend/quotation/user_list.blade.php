@extends('frontend.layout.app')
@section('content')
<style type="text/css">
    #excel_image {
        width: 15%;
        height: 17px;
    }

    #edit_image {
        height: 17px;
    }

    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin: 18px 0 28px;
        font-family: Arial, Helvetica, sans-serif;
    }

    .custom-pagination a,
    .custom-pagination span {
        display: inline-block;
        min-width: 34px;
        padding: 6px 10px;
        text-align: center;
        border: 1px solid #e0e6e6;
        border-radius: 4px;
        background: #fff;
        color: #333;
        text-decoration: none;
        font-size: 13px;
    }

    .custom-pagination a:hover {
        background: #037d71;
        color: #fff;
        border-color: #037d71;
    }

    .custom-pagination .active {
        background: #037d71;
        color: #fff;
        font-weight: 600;
        border-color: #037d71;
    }

    .custom-pagination .disabled {
        color: #bfc9c9;
        border-color: #f0f3f3;
        cursor: default;
    }

    span.select2.select2-container.select2-container--default.select2-container--below.select2-container--focus {
        width: 76.25px !important;
    }
</style>

<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="quotationMidSection">
                <h2>Quotation List- User</h2>
                <div class="quotationSection">
                    <div class="tableResponsive">
                        <!-- <input type="text" id="customSearch" placeholder="Search quotations..." style="margin:10px 0; padding:6px; width:250px;"> -->
                        <input type="text" id="globalSearchInput" placeholder="Search quotations..." style="margin:10px 0; padding:6px; width:250px;" autocomplete="off">
                        <table id="quotationsTable" class="dataTable dataTables_info">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Quotation no</th>
                                    <th>Project Name</th>
                                    <th>Country</th>
                                    <th>Quotation Value</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Modification</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $index => $quotation)
                                @php
                                $quotation_no = $quotation->quotation_number;
                                $row = $quotationsData[$quotation_no] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration + $quotations->firstItem() - 1 }}</td>
                                    <td>{{ $quotation_no }} <input type="hidden" class="quotation-no" value="{{ $quotation_no }}"></td>
                                    <td>{{ $row['project_name'] ?? '' }}</td>
                                    <td>{{ $row['country'] ?? '' }}</td>
                                    <td>{{ isset($row['price_data']) ? round($row['price_data']) : 0 }}</td>
                                    <td>
                                        <select name="" class="status formInput">
                                            <option value="Open" {{ isset($row['status']) && $row['status'] == "Open" ? "selected" : "" }}>Open</option>
                                            <option value="Won" {{ isset($row['status']) && $row['status'] == "Won" ? "selected" : "" }}>Won</option>
                                            <option value="Lost" {{ isset($row['status']) && $row['status'] == "Lost" ? "selected" : "" }}>Lost</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="" class="reason formInput">
                                            <option value="">Select Reason</option>
                                            <option value="Price" {{ isset($row['reason']) && $row['reason'] == "Price" ? "selected" : "" }}>Price</option>
                                            <option value="Delivery" {{ isset($row['reason']) && $row['reason'] == "Delivery" ? "selected" : "" }}>Delivery</option>
                                            <option value="Vendor List" {{ isset($row['reason']) && $row['reason'] == "Vendor List" ? "selected" : "" }}>Vendor List</option>
                                            <option value="COO" {{ isset($row['reason']) && $row['reason'] == "COO" ? "selected" : "" }}>COO</option>
                                            <option value="Spec. not compliance" {{ isset($row['reason']) && $row['reason'] == "Spec. not compliance" ? "selected" : "" }}>Spec. not compliance</option>
                                        </select>
                                    </td>
                                    <td width="15%">
                                        <a href="{{ url('controlpanel/quotations/edit/' . $quotation_no) }}">Edit</a>
                                        <a href="{{ URL::to('controlpanel/quotations/pdf/' . $quotation_no )}}" target="_blank">
                                            <img src="{{ asset('fassets/images/viewIcon.png') }}" />
                                        </a>
                                        <a href="{{ URL::to('controlpanel/quotations/pdf/' . $quotation_no )}}" download>
                                            <img src="{{ asset('fassets/images/downloadIcon.png') }}" />
                                        </a>
                                        <a href="{{ URL::to('controlpanel/quotations/excel/' . $quotation_no ) }}">
                                            Excel
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">
                            @php
                            $onEachSide = 1;
                            @endphp
                            @if (is_object($quotations) && method_exists($quotations, 'lastPage') && method_exists($quotations, 'currentPage'))
                            @php
                            $last = $quotations->lastPage();
                            $current = $quotations->currentPage();
                            $start = max(1, $current - $onEachSide);
                            $end = min($last, $current + $onEachSide);
                            @endphp

                            @if ($quotations->lastPage() > 1)
                            <nav class="custom-pagination" aria-label="Pagination">
                                @if ($quotations->onFirstPage())
                                <span class="disabled">«</span>
                                @else
                                <a href="{{ $quotations->appends(['search' => request('search')])->previousPageUrl() }}" rel="prev">«</a>
                                @endif

                                @if ($start > 1)
                                <a href="{{ $quotations->appends(['search' => request('search')])->url(1) }}">1</a>
                                @if ($start > 2)
                                <span class="disabled">...</span>
                                @endif
                                @endif

                                @for ($page = $start; $page <= $end; $page++)
                                    @if ($page==$current)
                                    <span class="active">{{ $page }}</span>
                                    @else
                                    <a href="{{ $quotations->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a>
                                    @endif
                                    @endfor

                                    @if ($end < $last)
                                        @if ($end < $last - 1)
                                        <span class="disabled">...</span>
                                        @endif
                                        <a href="{{ $quotations->appends(['search' => request('search')])->url($last) }}">{{ $last }}</a>
                                        @endif

                                        @if ($quotations->hasMorePages())
                                        <a href="{{ $quotations->appends(['search' => request('search')])->nextPageUrl() }}" rel="next">»</a>
                                        @else
                                        <span class="disabled">»</span>
                                        @endif
                            </nav>
                            @endif

                            @else
                            <div class="custom-pagination">
                                <span class="disabled">Pagination unavailable</span>
                                <span class="disabled">Make sure controller uses paginate(50) not get()</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3"></div>
        <div class="d-flex cusPagination">
            <div class="">
                <a onclick="window.history.back()" href=""><img src="{{ asset('fassets/images/arrowLefticon.png') }}" /> Back</a>
            </div>
        </div>
        <div class="d-flex formPageFooter">
            <div class="left"></div>
            <div class="right">
                <ul>
                    <li><a href="{{ URL::to('/') }}" tooltip="Go to Home Page"><img src="{{ asset('fassets/images/homeIcon.png') }}" /></a></li>
                    <li><a href="{{ URL::to('controlpanel/cart/' . Auth::user()->id) }}" tooltip="Cart"><img src="{{ asset('fassets/images/addIcon.png') }}" /></a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->

<!-- Select2 CSS & JS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

<script>
    // Status update AJAX
    $(".status").on('change', function() {
        var quotation_no = $(this).closest('tr').find('.quotation-no').val();
        var status = $(this).find('option:selected').val();
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/quotations/status-update')}}",
            data: {
                quotation_no: quotation_no,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                alert(response.msg);
            },
            error: function(response) {}
        });
    });

    // Reason update AJAX
    $(".reason").on('change', function() {
        var quotation_no = $(this).closest('tr').find('.quotation-no').val();
        var reason = $(this).find('option:selected').val();
        $.ajax({
            type: "get",
            url: "{{url('controlpanel/quotations/reason-update')}}",
            data: {
                quotation_no: quotation_no,
                reason: reason
            },
            dataType: 'json',
            success: function(response) {
                alert(response.msg);
            },
            error: function(response) {}
        });
    });
</script>
<script>
    $('#globalSearchInput').on('keyup', function(e) {
        if (e.keyCode === 13 || e.keyCode === 32 || $(this).val().length === 0 || $(this).val().length > 2) {
            let search = $(this).val();
            let url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('page', 1); // always reset to page 1 on new search

            window.location.href = url.toString();
        }
    });

    // Optionally, prefill the search box if a search term is present
    $(document).ready(function() {
        $('#globalSearchInput').val("{{ request('search') }}");
    });
</script>
@stop