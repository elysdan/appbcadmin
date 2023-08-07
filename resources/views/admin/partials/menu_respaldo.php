<nav class="main-sidebar bg-default">
    <button class="sidebar-close"><i class="fa fa-times"></i></button>
    <div class="navbar-brand-wrapper d-flex justify-content-start align-items-center">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
            <span class="logo-one"><img src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}"
                                        alt="logo-image"/></span>
            <span class="logo-two"><img src="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"
                                        alt="logo-image"/></span>
        </a>
    </div>
    <div id="main-sidebar">
        <ul class="nav">

            @if (auth()->guard('admin')->user()->role == 0)


                <li class="nav-item {{ sidenav_active('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-th-large text-facebook"></i></span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item {{ sidenav_active('admin.staff*') }}">
                    <a href="{{ route('admin.staff.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-th-large text-facebook"></i></span>
                        <span class="menu-title">Manage Staffs</span>
                    </a>
                </li>


                <li class="nav-item {{ sidenav_active('admin.matching-bonus') }}">
                    <a href="{{ route('admin.matching-bonus') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-money text-facebook"></i></span>
                        <span class="menu-title">Cron Job</span>
                    </a>
                </li>
                   
             

                <li class="nav-item {{ sidenav_active('admin.product.index') }}">
                    <a href="{{ route('admin.product.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-bullhorn text-facebook"></i></span>
                        <span class="menu-title">Contract Settings</span>
                    </a>
                </li>
                <li class="nav-item {{ sidenav_active('admin.network.contract.index') }}">
                    <a href="{{ route('admin.network.contract.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-bullhorn text-facebook"></i></span>
                        <span class="menu-title">Network Contract Settings</span>
                    </a>
                </li>



                <li class="nav-item {{ sidenav_active('admin.pool.interest') }}">
                    <a href="{{ route('admin.pool.interest') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-product-hunt text-facebook"></i></span>
                        <span class="menu-title">Pool Interest</span>
                    </a>
                </li>

                <li class="nav-item {{ sidenav_active('admin.matrix.plan') }}">
                    <a href="{{ route('admin.matrix.plan') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-product-hunt text-facebook"></i></span>
                        <span class="menu-title">Matrix Plans</span>
                    </a>
                </li>


                {{--            <li class="nav-item {{ sidenav_active('admin.plan') }}">--}}
                {{--                <a href="{{ route('admin.plan') }}" class="nav-link">--}}
                {{--                    <span class="menu-icon"><i class="fa fa-list text-facebook"></i></span>--}}
                {{--                    <span class="menu-title">MLM Plan</span>--}}
                {{--                </a>--}}
                {{--            </li>--}}


                <li class="nav-item {{ sidenav_active('admin.compensation.plan') }}">
                    <a href="{{ route('admin.compensation.plan') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-list text-facebook"></i></span>
                        <span class="menu-title">Compensation Plan</span>
                    </a>
                </li>

                <li class="nav-item {{ sidenav_active('admin.ranks.index') }}">
                    <a href="{{ route('admin.ranks.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-list text-facebook"></i></span>
                        <span class="menu-title">Manage Ranks</span>
                    </a>
                     </li>

            @endif

            <li class="nav-item {{ sidenav_active('admin.withdraw*') }}">

                    <a data-default-url="{{ route('admin.withdraw.method.methods') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-bank text-facebook"></i></span>
                        <span class="menu-title">Products</span>
                     
                            <span class="badge bg-blue border-radius-10">{{ NEW }}</span>
                      
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>

                </li>

            <li class="nav-item {{ sidenav_active('admin.users*') }}">
                <a data-default-url="{{ route('admin.users.all') }}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-users text-facebook"></i></span>
                    <span class="menu-title">Manage Users</span>
                    @if($email_unverified_users_count > 0 || $sms_unverified_users_count > 0)
                        <span class="badge bg-orange border-radius-10"><i class="fa px-1 fa-exclamation"></i></span>
                    @endif
                    <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ sidenav_active('admin.users.all') }}">
                        <a class="nav-link" href="{{ route('admin.users.all') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">All Users</span>
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('admin.users.active') }}">
                        <a class="nav-link" href="{{ route('admin.users.active') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Active Users</span>
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('admin.users.banned') }}">
                        <a class="nav-link" href="{{ route('admin.users.banned') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Banned Users</span>
                            @if($banned_users_count) <span
                                class="badge bg-blue border-radius-10">{{ $banned_users_count }}</span> @endif
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('admin.users.emailUnverified') }}">
                        <a class="nav-link" href="{{ route('admin.users.emailUnverified') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Email Unverified</span>
                            @if($email_unverified_users_count) <span
                                class="badge bg-blue border-radius-10">{{ $email_unverified_users_count }}</span> @endif
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('admin.users.smsUnverified') }}">
                        <a class="nav-link" href="{{ route('admin.users.smsUnverified') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">SMS Unverified</span>
                            @if($sms_unverified_users_count) <span
                                class="badge bg-blue border-radius-10">{{ $sms_unverified_users_count }}</span> @endif
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active(['admin.users.login.history','admin.users.login.search']) }}">
                        <a class="nav-link" href="{{ route('admin.users.login.history') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Login History</span>
                        </a>
                    </li>
                    <li class="nav-item {{ sidenav_active('admin.users.email.all') }}">
                        <a class="nav-link" href="{{ route('admin.users.email.all') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Send Email</span>
                        </a>
                    </li>
                </ul>
            </li>

            @if (auth()->guard('admin')->user()->role == 0)

        
                <li class="nav-item {{ sidenav_active('admin.withdraw*') }}">
                    <a data-default-url="{{ route('admin.withdraw.method.methods') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-bank text-facebook"></i></span>
                        <span class="menu-title">Withdraw System</span>
                        @if($pending_withdrawals_count > 0)
                            <span class="badge bg-orange border-radius-10"><i class="fa px-1 fa-exclamation"></i></span>
                        @endif
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">

                        <li class="nav-item {{ sidenav_active('admin.withdraw.request*') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.request.create') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Withdraw create</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.withdraw.method*') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.method.methods') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Withdraw Methods</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.withdraw.pending') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.pending') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Pending Withdrawals</span>
                                @if($pending_withdrawals_count) <span
                                    class="badge bg-blue border-radius-10">{{ $pending_withdrawals_count }}</span> @endif
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.withdraw.approved') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.approved') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Approved Withdrawals</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.withdraw.rejected') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.rejected') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Rejected Withdrawals</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.withdraw.log') }}">
                            <a class="nav-link" href="{{ route('admin.withdraw.log') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">All Withdrawals</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ sidenav_active('admin.deposit*') }}">
                    <a data-default-url="{{ route('admin.deposit.gateway.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-credit-card-alt text-facebook"></i></span>
                        <span class="menu-title">Gateways</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ sidenav_active('admin.deposit.gateway*') }}">
                            <a class="nav-link" href="{{ route('admin.deposit.gateway.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Automatic</span>
                            </a>
                        </li>

                        {{--                    <li class="nav-item {{ sidenav_active('admin.deposit.manual*') }}">--}}
                        {{--                        <a class="nav-link" href="{{ route('admin.deposit.manual.index') }}">--}}
                        {{--                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>--}}
                        {{--                            <span class="menu-title">Manual Methods</span>--}}
                        {{--                        </a>--}}
                        {{--                    </li>--}}

                        {{--                    <li class="nav-item {{ sidenav_active('admin.deposit.pending') }}">--}}
                        {{--                        <a class="nav-link" href="{{ route('admin.deposit.pending') }}">--}}
                        {{--                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>--}}
                        {{--                            <span class="menu-title">Pending Deposits</span>--}}
                        {{--                        </a>--}}
                        {{--                    </li>--}}
                        {{--                    --}}
                        {{--                    <li class="nav-item {{ sidenav_active('admin.deposit.approved') }}">--}}
                        {{--                        <a class="nav-link" href="{{ route('admin.deposit.approved') }}">--}}
                        {{--                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>--}}
                        {{--                            <span class="menu-title">Approved Deposits</span>--}}
                        {{--                        </a>--}}
                        {{--                    </li>--}}

                        {{--                    <li class="nav-item {{ sidenav_active('admin.deposit.rejected') }}">--}}
                        {{--                        <a class="nav-link" href="{{ route('admin.deposit.rejected') }}">--}}
                        {{--                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>--}}
                        {{--                            <span class="menu-title">Rejected Deposits</span>--}}
                        {{--                        </a>--}}
                        {{--                    </li>--}}
                        <li class="nav-item {{ sidenav_active('admin.deposit.list') }}">
                            <a class="nav-link" href="{{ route('admin.deposit.list') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">All Deposits</span>
                            </a>
                        </li>
                    </ul>
                </li>

            @endif

            <li class="nav-item {{ sidenav_active('admin.ticket*') }}">
                <a data-default-url="{{ route('admin.ticket') }}" class="nav-link">
                    <span class="menu-icon"><i class="fa fa-ticket text-facebook"></i></span>
                    <span class="menu-title">Support Ticket</span>
                    @if($pending_ticket_count > 0)
                        <span class="badge bg-orange border-radius-10"><i class="fa px-1 fa-exclamation"></i></span>
                    @endif
                    <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ sidenav_active('admin.ticket') }}">
                        <a class="nav-link" href="{{ route('admin.ticket') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">All Ticket</span>
                        </a>
                    </li>

                    <li class="nav-item {{ sidenav_active('admin.ticket.pending') }}">
                        <a class="nav-link" href="{{ route('admin.ticket.pending') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Pending Ticket</span>
                            @if($pending_ticket_count) <span
                                class="badge bg-blue border-radius-10">{{ $pending_ticket_count }}</span> @endif
                        </a>
                    </li>

                    <li class="nav-item {{ sidenav_active('admin.ticket.closed') }}">
                        <a class="nav-link" href="{{ route('admin.ticket.closed') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Closed Ticket</span>
                        </a>
                    </li>

                    <li class="nav-item {{ sidenav_active('admin.ticket.answered') }}">
                        <a class="nav-link" href="{{ route('admin.ticket.answered') }}">
                            <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                            <span class="menu-title">Answered Ticket</span>
                        </a>
                    </li>

                </ul>
            </li>


            @if (auth()->guard('admin')->user()->role == 0)
                <li class="nav-item {{ sidenav_active('admin.report*') }}">
                    <a data-default-url="{{ route('admin.report.transaction') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-clipboard text-facebook"></i></span>
                        <span class="menu-title">Reports / Logs</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ sidenav_active('admin.report.transaction*') }}">
                            <a class="nav-link" href="{{ route('admin.report.transaction') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Transaction Log</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.report.shares*') }}">
                            <a class="nav-link" href="{{ route('admin.report.shares') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Contracts Sell Log</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.report.pool.interest*') }}">
                            <a class="nav-link" href="{{ route('admin.report.pool.interest') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Pool Interest Log</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.report.interest.log*') }}">
                            <a class="nav-link" href="{{ route('admin.report.interest.log') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Interest Log</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.report.ref_com_log*') }}">
                            <a class="nav-link" href="{{ route('admin.report.ref_com_log') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Referral Commission Log</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.report.binary_com_log*') }}">
                            <a class="nav-link" href="{{ route('admin.report.binary_com_log') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Binary Commission Logs</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.report.residual_com_log*') }}">
                            <a class="nav-link" href="{{ route('admin.report.residual_com_log') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Residual Commission Logs</span>
                            </a>
                        </li>

                    </ul>
                </li>
            @endif
        </ul>

        @if (auth()->guard('admin')->user()->role == 0)
            <hr class="nk-hr"/>
            <h6 class="nav-header text-facebook">Settings</h6>
            <ul class="nav">

                <li class="nav-item {{ sidenav_active('admin.plugin*') }}">
                    <a href="{{ route('admin.plugin.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-cogs text-facebook"></i></span>
                        <span class="menu-title">Plugin & Extensions</span>
                    </a>
                </li>

                <li class="nav-item {{ sidenav_active('admin.frontend*') }}">
                    <a data-default-url="{{ route('admin.frontend.blog.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-diamond text-facebook"></i></span>
                        <span class="menu-title">Frontend Manager</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">

                        <li class="nav-item {{ sidenav_active('admin.frontend.banner*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.banner.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Banner</span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.howWork*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.howWork.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">How it Works</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.frontend.about*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.about.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">About</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.frontend.service*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.service.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Our Features</span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.call-to-action') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.call-to-action') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Counter section</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.frontend.title_subtitle') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.title_subtitle') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Title Subtitle </span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.breadcrumb*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.breadcrumb.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Breadcrumb</span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.social*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.social.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Social Icons</span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.footer*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.footer.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Footer</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.frontend.contact*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.contact.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Contact</span>
                            </a>
                        </li>


                        <li class="nav-item {{ sidenav_active('admin.frontend.seo*') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.seo.edit') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">SEO Manager</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.frontend.terms') }}">
                            <a class="nav-link" href="{{ route('admin.frontend.terms') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Terms And Conditions </span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.setting.load-popup') }}">
                            <a class="nav-link" href="{{ route('admin.setting.load-popup') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Load Pop Up</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ sidenav_active('admin.setting*') }}">
                    <a data-default-url="{{ route('admin.setting.index') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-cog text-facebook"></i></span>
                        <span class="menu-title">General Settings</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ sidenav_active('admin.setting.index') }}">
                            <a class="nav-link" href="{{ route('admin.setting.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Basic</span>
                            </a>
                        </li>

                         <li class="nav-item {{ sidenav_active('admin.setting.smart') }}">
                            <a class="nav-link" href="{{ route('admin.setting.smart') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Smart Contract</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.setting.logo-icon') }}">
                            <a class="nav-link" href="{{ route('admin.setting.logo-icon') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Logo & Icons</span>
                            </a>
                        </li>

                        <li class="nav-item {{ sidenav_active('admin.setting.language*') }}">
                            <a class="nav-link" href="{{ route('admin.setting.language-manage') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Language Manager</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.setting.notice') }}">
                            <a class="nav-link" href="{{ route('admin.setting.notice') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Notice Board</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item {{ sidenav_active('admin.email-template*') }}">
                    <a data-default-url="{{ route('admin.email-template.global') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-envelope-o text-facebook"></i></span>
                        <span class="menu-title">Email Manager</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ sidenav_active('admin.email-template.global') }}">
                            <a class="nav-link" href="{{ route('admin.email-template.global') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Global Template</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active(['admin.email-template.index','admin.email-template.edit']) }}">
                            <a class="nav-link" href="{{ route('admin.email-template.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Email Templates</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active('admin.email-template.setting') }}">
                            <a class="nav-link" href="{{ route('admin.email-template.setting') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Email Configure</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ sidenav_active('admin.sms-template*') }}">
                    <a data-default-url="{{ route('admin.sms-template.global') }}" class="nav-link">
                        <span class="menu-icon"><i class="fa fa-mobile text-facebook"></i></span>
                        <span class="menu-title">SMS Manager</span>
                        <span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ sidenav_active('admin.sms-template.global') }}">
                            <a class="nav-link" href="{{ route('admin.sms-template.global') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">Global Template</span>
                            </a>
                        </li>
                        <li class="nav-item {{ sidenav_active(['admin.sms-template.index','admin.sms-template.edit']) }}">
                            <a class="nav-link" href="{{ route('admin.sms-template.index') }}">
                                <span class="mr-2"><i class="fa fa-angle-right"></i></span>
                                <span class="menu-title">SMS Templates</span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        @endif
    </div>
</nav>
