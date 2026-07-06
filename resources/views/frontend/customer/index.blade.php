@extends('frontend.layout.app')
@section('content')

<!-- mid section start-->
<section class="midContent" id="midContent">
    <div class="container">
        <div class="d-flex flex-center">
            <div class="formsMidSection">
                <div class="formWidget">
                    <form action="{{route('customer.save')}}" method="post">
                        @csrf
                        <input type ="hidden" value="{{ Request::get('cp_ids') }}" name="cp_ids">
                        <input type ="hidden" value="{{ Request::get('atmosIds') }}" name="atmos_ids">
                        <input type ="hidden" value="{{ Request::get('scpIds') }}" name="scp_ids">
                        <!-- A Code: 24-02-2026 Start -->
                        <input type ="hidden" value="{{ Request::get('scpvIds') }}" name="scpv_ids">
                        <!-- A Code: 24-02-2026 End -->
                        <input type ="hidden" value="{{ Request::get('boosterIds') }}" name="booster_ids">
						<input type ="hidden" value="{{ Request::get('firefightingIds') }}" name="firefighting_ids">
                        <input type ="hidden" value="{{ Request::get('removeCartIds') }}" name="removeCartIds">
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here popover content goes here.</div>
                            </div>
                            <input type="text" required class="formInput" name="name" id="" placeholder="Customer Name*">                            
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text" required class="formInput" name="project_name" id="" placeholder="Project Name*">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="country" id="" class="formInput" required>
                                <option value="">Project Country*</option>
                                @foreach($countries as $row)
                                <option value="{{ucfirst($row->country)}}">{{ucfirst($row->country)}}</option>

                                @endforeach
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="revision_number" id="" value="00" placeholder="Revision">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <span class="formArrowIcon"><img src="{{asset('fassets/images/arrowDownIcon.png')}}" /></span>
                            <select name="segment_category" id="" class="formInput">
                                <option>Segment Category</option>
                                <option value="Water Management & Industry">Water Management & Industry</option>
                                <option value="Building service residential">Building service residential</option>
                                <option value="Building service commercial">Building service commercial</option>
                                <option value="">OEM</option>
                            </select>
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text" required class="formInput" name="project_location" id="" placeholder="Project Location*">
                        </div>
                        <!-- <hr>
                        <h3>Customer information</h3> -->
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="email" class="formInput" name="email" id="email_id" placeholder="Customer email ID">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="phone_no" id="" placeholder="Customer phone number">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="address" id="" placeholder="Customer address">
                        </div>
                        <!-- <hr>
                        <h3>Customer reference</h3> -->
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="enquiry_form_number" id="" placeholder="Customer enquiry form number">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="consultant" id="" placeholder="Consultant">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <input type="text"  class="formInput" name="contractor" id="" placeholder="Contractor">
                        </div>
                        <div class="formFields">
                            <div class="helpBtnWrap" style="position: relative;">
                                <a href="" class="helpBtn">?</a>
                                <div class="popper-content hide">popover content goes here.</div>
                            </div>
                            <textarea id="notes" class="formInput" name="notes" rows="4" cols="90" placeholder="Notes"></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="background-color: #169e88">Generate Quotation</button>
                    </form>
                </div>

            </div>
        </div>
        <div class="d-flex cusPagination">
            <div class="">
                <?php $cpId = Request::get('cp_id'); ?>
                <a href="{{URL::to('controlpanel/cart/' . Auth::user()->id ) }}"><img src="{{asset('fassets/images/arrowLefticon.png')}}" /> Back</a>
            </div>
            <!--            <div class="">
                            <button>Next <img src="{{asset('fassets/images/arrowLefticon.png')}}" /></button>
                        </div>-->
        </div>
        <div class="d-flex formPageFooter">
            <div class="left">
                <!--Unit Price: <button class="clcBtn">Calculate</button> <span>420€</span>-->
            </div>
            <div class="right">
                <ul>
                    <!--<li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/generateIcon.png')}}" /></a></li>-->
                    <li><a href="{{URL::to('/')}}" tooltip="Go to Home Page"><img src="{{asset('fassets/images/homeIcon.png')}}" /></a></li>                     
                    <li><a href="{{URL::to('controlpanel/cart/'.Auth::user()->id)}}" tooltip="Cart"><img src="{{asset('fassets/images/addIcon.png')}}" /></a></li>
                    <!--<li><a href="#" tooltip="Generate Quotation"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->

    <!--<li><a href="#" tooltip="Checkout"><img src="{{asset('fassets/images/goIcon.png')}}" /></a></li>-->
                </ul>
            </div>

        </div>
    </div>
</section>
<!-- mid section end -->
<style>
/* A Code: 01-07-2026 Start */
.formWidget .formFields .formArrowIcon {
    position: absolute;
    right: 1.125rem;
    top: 0.5rem;
}
/* A Code: 01-07-2026 End */
</style>

@endsection
