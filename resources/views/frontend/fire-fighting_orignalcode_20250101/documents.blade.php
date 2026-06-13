@extends('frontend.layout.app')

@section('content')
    <section class="midContent" id="midContent">
        <div class="container">
            <div class="d-flex flex-center">
                <div class="addQuotationMidSection">
                    <h2>Fire Fighting Documents</h2>
                    <div class="row p-0 m-0 mb-2">
                        <div class="col-12 p-0 m-0 d-flex align-items-center">
                            @php
                                $dir_path = str_replace('assets/fire-fighting/documents', '', $main_path);
                                if ($dir_path == '') {
                                    $all_dir_path[url('fire-fighting-documents')] = 'Home';
                                } else {
                                    $dir_path = explode('/', $dir_path);
                                    $all_dir_path = [];
                                    $main_origin_path = 'assets/fire-fighting/documents';
                                    foreach ($dir_path as $dk => $dv) {
                                        if ($dv == '') {
                                            $all_dir_path[url('fire-fighting-documents')] = 'Home';
                                        } else {
                                            $main_origin_path .= '/'.$dv;
                                            $all_dir_path[url('fire-fighting-documents?s='.base64_encode($main_origin_path))] = $dv;
                                        }
                                    }
                                }
                                // dd($all_dir_path);
                            @endphp
                            @foreach($all_dir_path as $dir_path => $dir_folder)
                                @if(array_key_last($all_dir_path) == $dir_path)
                                    <span>{{ $dir_folder }}</span>
                                @else
                                    <a href="{{ $dir_path }}">{{ $dir_folder }}</a> <span class="text-sm text-gray"><img src="{{ asset('fassets/images/arrowDownIcon.png') }}" class="rotate-img px-1"></span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="quotationBottomSection">
                        <div class="tableResponsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="15%">Documents</th>
                                        <th width="10%">Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $fk => $fv)
                                        @php
                                            $file = public_path($main_path.'/'.$fv);
                                            $file_path = $main_path.'/'.$fv;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if(pathinfo($file, PATHINFO_EXTENSION))
                                                    <a class="text-body" href="{{ url(''.$main_path.'/'.$fv) }}" target="_blank"><span>{{ $fv }}</span></a>
                                                @else
                                                    <a class="text-body" href="{{ url('fire-fighting-documents?s='.base64_encode($file_path)) }}"><span>{{ $fv }}</span></a>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if(pathinfo($file, PATHINFO_EXTENSION))
                                                        <a class="text-body" href="{{ url(''.$main_path.'/'.$fv) }}" target="_blank" download><img src="{{ asset('fassets/images/downloadIcon.png') }}"></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex cusPagination">
                <div class="">
                    <a  onclick="window.history.back()" href="javascript:void(0)"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
                </div>
            </div>
            <div class="d-flex formPageFooter">
                <div class="left">
                </div>
                <div class="right">
                    <ul>
                        <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                        <li><a href="{{URL::to('/')}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
<style>
    a:hover {
        text-decoration: none;
    }
    .rotate-img {
        -webkit-transform:rotate(270deg); /* Chrome, Safari, Opera */
        -moz-transform: rotate(270deg);
        -ms-transform: rotate(270deg);
        -o-transform: rotate(270deg);
        transform: rotate(270deg);   /* Standard syntax */
    }
</style>
@endsection