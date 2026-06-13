<header class="headerWidget" id="headerWidget">
    <div class="container">
        <div class="d-flex headerWidgetBody">
            <div class="logo"><a href="{{route('main.home')}}">
			<img src="{{asset('fassets/images/logo.png')}}" alt="wilo Logo"></a>
			</div>
            @if(!Auth::user())
            <div class="loginBtn">
                <a href="{{route('loginform')}}"><span>Login</span></a>
            </div>
            @else
            <div class="userDropdown">
                <button class="cusDropdown" id="" onclick="mobileMenuOpen()"><img src="{{asset('fassets/images/userDropdownIcon.png')}}" /> {{Auth::user()->name }}</button>
                <ul id="cusDropdownList" class="dropdown-content">
                    <!--<li><a href="">Profile</a></li>-->
                    <li><a href="{{URL::to('controlpanel/quotations/user-list')}}">User List</a></li>
                    @if (auth()->user()->isAdmin())
                    <li><a href="{{URL::to('admin/dashboard')}}">Admin Dashboard</a></li>

                    @endif
					
        <li><a href="{{URL::to('fire-fighting-documents')}}">Fire Fighting Documnet</a></li>
       
                    <li><a href="{{URL::to('/controlpanel/documents')}}">Documents</a></li>
                    <li><a href="{{URL::to('/controlpanel/manuals')}}">Manual</a></li>

                    <li><a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                            <i class="cil-account-logout c-sidebar-nav-icon nav-icon"></i>
                            {{ trans('global.logout') }}
                        </a>
                        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form></li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</header>
