@extends('frontend.layout.app')
@section('content')

    <!-- mid section start-->
    <section class="midContent" id="midContent">
        <div class="container">
            <div class="d-flex flex-center">
                <div class="addQuotationMidSection">
                    <h2>Manuals</h2>
                    <div class="quotationBottomSection">
                        <div class="tableResponsive">
                            <table>
                                <thead>
                                <tr>

                                    <th width="10%">Component</th>
                                    <th width="15%">Documents</th>

                                </tr>


                                </thead>
                                <tbody>

                                    {{--
                                    @if($cp['data']->isNotEmpty())
                                        <tr>
                                            <td>{{$cp['module_name']}} </td>
                                            <td>
                                                @if(empty($cp['data']))
                                                    <a href="javascript:void(0)">
                                                @else
                                                    @foreach($cp['data'] as $key=>$d)
                                                    <a href="{{ URL::to('public/manuals/'.$d->file_name )}}" target="_blank">
                                                        <img src="{{asset('public/assets/icons/file.svg')}}" /> {{$d->file_name}} <br>
                                                    @endforeach
                                                @endif
                                                    </a>
                                                </a>                                            
                                            </td>
                                        </tr>
                                    @endif
                                   
                                    @if($scp['data']->isNotEmpty())
                                    <tr>
                                        <td>{{$scp['module_name']}} </td>
                                        <td>
                                            @if(empty($scp['data']))
                                                <a href="javascript:void(0)">
                                                    @else
                                                        @foreach($scp['data'] as $key=>$d)
                                                            <a href="{{ URL::to('public/manuals/'.$d->file_name )}}" target="_blank">
                                                                <img src="{{asset('assets/icons/file.svg')}}" /> {{$d->file_name}} <br>
                                                                @endforeach
                                                                @endif
                                                            </a>
                                                </a>
                                        </td>

                                        </tr>
                                    @endif                                                                       

                                    @if($atmos['data']->isNotEmpty())
                                        <tr>
                                            <td>{{$atmos['module_name']}} </td>
                                            <td>
                                                @if(empty($atmos['data']))
                                                    <a href="javascript:void(0)">
                                                        @else
                                                            @foreach($atmos['data'] as $key=>$d)
                                                                <a href="{{ URL::to('public/manuals/'.$d->file_name )}}" target="_blank">
                                                                    <img src="{{asset('assets/icons/file.svg')}}" /> {{$d->file_name}} <br>
                                                                    @endforeach
                                                                    @endif
                                                                </a>
                                                    </a>
                                            </td>
                                        </tr>
                                    @endif

                                    @if($booster['data']->isNotEmpty())
                                        <tr>
                                            <td>{{$booster['module_name']}} </td>
                                            <td>
                                                @if(empty($booster['data']))
                                                    <a href="javascript:void(0)">
                                                        @else
                                                            @foreach($booster['data'] as $key=>$d)
                                                                <a href="{{ URL::to('public/manuals/'.$d->file_name )}}" target="_blank">
                                                                    <img src="{{asset('assets/icons/file.svg')}}" /> {{$d->file_name}} <br>
                                                                    @endforeach
                                                                    @endif
                                                                </a>
                                                    </a>
                                            </td>
                                        </tr>
                                    @endif

                                    --}} 

                                    <!-- A Code: 20-02-2026 Start -->
                                    @if(!empty($cp['data']) && $cp['data']->isNotEmpty())
                                        <tr>
                                            <td>{{ $cp['module_name'] }}</td>
                                            <td>
                                                @foreach($cp['data'] as $d)
                                                    <a href="{{ URL::to('public/manuals/' . $d->file_name) }}" target="_blank" class="d-block mb-1">
                                                        <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                        {{ $d->file_name }}
                                                    </a> <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

                                    @if(!empty($scp['data']) && $scp['data']->isNotEmpty())
                                        <tr>
                                            <td>{{ $scp['module_name'] }}</td>
                                            <td>
                                                @foreach($scp['data'] as $d)
                                                    <a href="{{ URL::to('public/manuals/' . $d->file_name) }}" target="_blank" class="d-block mb-1">
                                                        <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                        {{ $d->file_name }}
                                                    </a> <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                    
                                    @if(!empty($scpv['data']) && $scpv['data']->isNotEmpty())
                                        <tr>
                                            <td>{{ $scpv['module_name'] }}</td>
                                            <td>
                                                @foreach($scpv['data'] as $d)
                                                    <a href="{{ URL::to('public/manuals/' . $d->file_name) }}" target="_blank" class="d-block mb-1">
                                                        <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                        {{ $d->file_name }}
                                                    </a> <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

                                    @if(!empty($atmos['data']) && $atmos['data']->isNotEmpty())
                                        <tr>
                                            <td>{{ $atmos['module_name'] }}</td>
                                            <td>
                                                @foreach($atmos['data'] as $d)
                                                    <a href="{{ URL::to('public/manuals/' . $d->file_name) }}" target="_blank" class="d-block mb-1">
                                                        <img src="{{ asset('assets/icons/file.svg') }}" alt="file" />
                                                        {{ $d->file_name }}
                                                    </a> <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

                                    @if(!empty($booster['data']) && $booster['data']->isNotEmpty())
                                        <tr>
                                            <td>{{ $booster['module_name'] }}</td>
                                            <td>
                                                @foreach($booster['data'] as $d)
                                                    <a href="{{ URL::to('public/manuals/' . $d->file_name) }}" target="_blank" class="d-block mb-1">
                                                        <img src="{{ asset('assets/icons/file.svg') }}" alt="file">
                                                        {{ $d->file_name }}
                                                    </a> <br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                    <!-- A Code: 20-02-2026 Start -->

                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex cusPagination">
            <!--            <div class="">
            <?php $cpId = Request::get('cp_id'); ?>
                <a href="{{URL::to('controlpanel/customer-information/' . $customer->id ) }}"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
                        </div>-->
                <div class="">
                    <a  onclick="window.history.back()" href="javascript:void(0)"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
                </div>
            <!--            <div class="">
                            <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
                        </div>-->
            </div>
            <div class="d-flex formPageFooter">
                <div class="left">

                </div>
                <div class="right">
                    <ul>

                        <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>
                        <li><a href="{{URL::to('/')}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- mid section end -->


@endsection
