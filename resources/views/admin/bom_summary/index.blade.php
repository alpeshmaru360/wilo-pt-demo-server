@extends('layouts.admin')

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr Month Select Plugin CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

<style>
    .flatDatepickr{background-color:transparent !important;}
</style>

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"> BOM summary </h1>
            </div>
            <div class="col-sm-6">
                @include('layouts.breadcrumbs')
            </div>
        </div>
    </div>
</div>
<!-- /.content-header -->
    
<!-- Main content -->
<section class="content">
@include('layouts.message')
    <form action="{{ url('admin/bom_summary/filter') }}" method="POST" id="filter_form">
        @csrf
        <div class="card">
            <div class="card-header">        
                <div class="pull-right">
                    <a class="btn btn-primary" href="javascript:;" onclick="download_csv()">
                        <i class="fas fa-download"></i> 
                        Export
                    </a>
                    <a class="btn btn-primary" href="{{ url('admin/bom_summary') }}">
                        <i class="fas fa-sync-alt"></i> 
                        Reset
                    </a>
                </div>
                <div class="pull-right mr-1">
                    <div class="form-group">
                        <input type="number" name="full_article_number" class="form-control" 
                        placeholder="Enter Article Number" value="{{ request('full_article_number') }}">
                    </div>
                </div>        
                <div class="pull-right mr-1">
                    <div class="form-group">
                       <input type="text" name="month" id="monthYearPicker" 
                        placeholder="Select Month & Year" class="form-control text-cente flatDatepickr">

                    </div>
                </div> 
                <div class="pull-right mr-1">
                    <div class="form-group">
                        <input type="text" name="date" id="simpleDatePicker" 
                               class="form-control text-center flatDatepickr" 
                               placeholder="Select Date" 
                               value="{{ request('date') }}">
                    </div>
                </div>
   
            </div>

            <div class="card-body project_bg">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th> SR No. </th>
                                    <th> Date </th>
                                    <th> Month </th>
                                    <th> Country </th> 
                                    <th> Customer Name </th>
                                    <th> Quotation Number </th> 
                                    <th> Project Name </th> 
                                    <th> Project Country </th> 
                                    <th> Full Article Number </th>
                                    <th> Product Description </th> 
                                    <th> Product Module </th>
                                    <th> Quantity </th> 
                                    <th> Unit Price </th> 
                                    <th> Total Price </th>
                                    <th> Intercompany Margin </th> 
                                    <th> MFC/Unit </th>
                                    <th> Overhead </th> 
                                    <th> Overhead / Unit </th> 
                                    <th> BOM Labour Charges </th> 
                                    <th> BOM Direct Material Cost</th> 
                                </tr>
                            </thead> 
                            <tbody>
                                @php
                                    $srNo = ($quotations->currentPage() - 1) * $quotations->perPage() + 1;
                                @endphp

                                @forelse ($response as $row)
                                    @php
                                        $date = $row['date'] !== '-' ? \DateTime::createFromFormat('d-m-Y', $row['date']) : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $srNo++ }}</td>
                                        <td>{{ $date ? $date->format('d M Y') : '-' }}</td>
                                        <td>{{ $date ? $date->format('F') : '-' }}</td>
                                        <td>{{ $row['country'] }}</td>
                                        <td>{{ $row['customer_name'] }}</td>
                                        <td>{{ $row['quotation_no'] }}</td>
                                        <td>{{ $row['project_name'] }}</td>
                                        <td>{{ $row['project_country'] }}</td>
                                       
                                        <td>
                                        @php

                                            // $url = match($row['Module']) {
                                            //     'controlpanel' => url('controlpanel/cart-item/' . $row['ItemWiseId']),
                                            //     'scp' => url('scp/cart-item/' . $row['ItemWiseId']),
                                            //     'scpv' => url('scpv/cart-item/' . $row['ItemWiseId']), // A Code: 23-02-2026
                                            //     'atmos' => url('atmos/cart-item/' . $row['ItemWiseId']),
                                            //     'booster' => url('booster-set/cart-item/' . $row['ItemWiseId']),
                                            //     'firefighting' => url('firefighting-set/cart-item/' . $row['ItemWiseId']),
                                            //     default => url('admin/dashboard'),
                                            // };
                                       
                                            // A Code: 27-06-2026 Start
                                            switch ($row['Module']) {
                                                case 'controlpanel':
                                                    $url = url('controlpanel/cart-item/' . $row['ItemWiseId']);
                                                    break;

                                                case 'scp':
                                                    $url = url('scp/cart-item/' . $row['ItemWiseId']);
                                                    break;

                                                case 'scpv':
                                                    $url = url('scpv/cart-item/' . $row['ItemWiseId']); // A Code: 23-02-2026
                                                    break;

                                                case 'atmos':
                                                    $url = url('atmos/cart-item/' . $row['ItemWiseId']);
                                                    break;

                                                case 'booster':
                                                    $url = url('booster-set/cart-item/' . $row['ItemWiseId']);
                                                    break;

                                                case 'firefighting':
                                                    $url = url('firefighting-set/cart-item/' . $row['ItemWiseId']);
                                                    break;

                                                default:
                                                    $url = url('admin/dashboard');
                                            }
                                            // A Code: 27-06-2026 End
                                        @endphp
                                        <a href="{{ $url }}" target = "_blank">{{ $row['article_no'] }}</a></td>

                                        <td>{{ $row['description'] }}</td>

                                        <td>{{ $row['Module'] }}</td>

                                        <td>{{ $row['qty'] }}</td>
                                        <td>{{ $row['unit_price'] }}</td>
                                        <td>{{ $row['total_price'] }}</td>
                                        <td>{{ $row['inter_company_margin_price'] }}</td>
                                        <td>{{ $row['MFC_per_unit'] }}</td>
                                        <td>{{ $row['overhead'] }}</td>
                                        <td>{{ $row['Overhead_per_unit'] }}</td>
                                        <td>{{ $row['BOM_labour_charges_and_costs'] }}</td>
                                        <td>{{ $row['BOM_direct_material_cost'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>


                        </table>

                    </div>
               

                <!-- Pagination links -->
                <div class="mt-2 float-right">
                    {{ $quotations->links() }}
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('scripts')
@parent
<script>
    function download_csv() {
        var $form = $('#filter_form');
        // Temporarily change action to export route
        $form.attr('action', "{{ url('admin/bom_summary/export-csv') }}");
        $form.attr('method', 'POST');

        // Add CSRF token (needed for POST)
        if ($form.find('input[name="_token"]').length === 0) {
            $form.prepend('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        }
        $form.submit();
        // Reset form back to normal filter action (so Apply Filter works again)
        $form.attr('action', "{{ url('admin/bom_summary/filter') }}");
        $form.attr('method', 'POST');
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form      = document.getElementById('filter_form');
        const articleEl = form.querySelector('input[name="full_article_number"]');

        if (articleEl) {
            articleEl.addEventListener('input', function () {
                form.submit();
            });
        }
    });
</script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Month Select Plugin JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        // Simple Date Picker
        flatpickr("#simpleDatePicker", {
            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d M Y",
            defaultDate: "{{ request('date') }}",
            theme: "light",
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('filter_form').submit(); // auto-submit
            }
        });

        // Month-Year Picker
        flatpickr("#monthYearPicker", {
            altInput: true,
            maxDate: "today",
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "M Y",
                    theme: "light"
                })
            ],
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('filter_form').submit(); // auto-submit
            },
            defaultDate: "{{ request('month') }}"
        });

    });
</script>
@endsection
