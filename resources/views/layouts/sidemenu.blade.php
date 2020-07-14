                    @if(Auth::check())
                    <!-- offcanvas-left -->
                    <div class="col-xl-2 px-0 offcanvas-collapse offcanvas-collapse-from-left side-base print-none">
                        <nav class="text-white">
                            <h3 class="side-head p-3 font-size-rg">アカウント情報</h3>
                            <div class="float-left mx-2">
                                <img class="p-2 icon-size-user rounded-circle" src="{{ asset('images/round-person-w.svg') }}" alt="">
                            </div>
                            <ul class="float-left list-unstyled">
                                <department-set></department-set>
                                <li class="pr-3 py-1 text-white font-size-sm d-block">
                                    {{ Auth::user()->name }}
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                            @if($menu_selections[Config::get('const.MENUITEM.calc_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>集計</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseAggregate">
                                    @if($menu_selections[Config::get('const.MENUITEM.daily') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/daily') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">日次集計</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.monthly') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/monthly') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">月次集計</a></li>
                                    @endif
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.alert_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>警告通知</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseAggregate">
                                    @if($menu_selections[Config::get('const.MENUITEM.daily_alert') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/daily_alert') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">日次警告通知</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.monthly_alert') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/monthly_alert') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">月次警告通知</a></li>
                                    @endif
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.attendancelog_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>勤怠ログ</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseEdit" role="button" aria-expanded="true" aria-controls="collapseEdit"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseEdit">
                                    @if($menu_selections[Config::get('const.MENUITEM.store_attendancelog') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/store_attendancelog') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">勤怠ログ登録</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.edit_attendancelog') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_attendancelog') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">勤怠ログ編集</a></li>
                                    @endif
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.edit_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>編集</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseEdit" role="button" aria-expanded="true" aria-controls="collapseEdit"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseEdit">
                                    <!-- <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_calendar') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-outlined-flag-w.svg') }}" alt="">カレンダー編集</a></li> -->
                                    @can('admin-higher')
                                        @if($menu_selections[Config::get('const.MENUITEM.create_shift_time') - 1]->is_select)
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_shift_time') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-access-time-w.svg') }}" alt="">シフト編集</a></li>
                                        @endif
                                        @if($menu_selections[Config::get('const.MENUITEM.edit_work_times') - 1]->is_select)
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_work_times') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-access-time-w.svg') }}" alt="">勤怠編集</a></li>
                                        @endif
                                    @endcan
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.demand_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>申請機能</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseAggregate">
                                    @if($menu_selections[Config::get('const.MENUITEM.demand') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/demand') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">各種申請作成</a></li>
                                    @endif
                                    @can('admin-midle')
                                        @if($menu_selections[Config::get('const.MENUITEM.approval') - 1]->is_select)
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/approval') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">各種申請承認</a></li>
                                        @endif
                                        @can('admin-higher')
                                            @if($menu_selections[Config::get('const.MENUITEM.confirm') - 1]->is_select)
                                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/approvalroot') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">承認者ルート設定</a></li>
                                            @endif
                                        @endcan
                                    @endcan
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.setting_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>設定</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseSetting" role="button" aria-expanded="true" aria-controls="collapseSetting"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                @can('admin-higher')
                                <ul class="collapse show list-unstyled" id="collapseSetting">
                                    @if($menu_selections[Config::get('const.MENUITEM.create_company_information') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_company_information') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-business-w.svg') }}" alt="">会社設定</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.create_department') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_department') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-group-w.svg') }}" alt="">組織設定</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_calc') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_calc') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-laptop-chromebook-w.svg') }}" alt="">労働時間基本設定</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.create_time_table') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_time_table') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-restore-w.svg') }}" alt="">勤務帯時間設定</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_calendar') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_calendar') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-flag-w.svg') }}" alt="">カレンダー設定</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.edit_user') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_user') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-assignment-ind-w.svg') }}" alt="">ユーザ情報設定</a></li>
                                    @endif
                                </ul>
                                @endcan
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.operation_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>操作</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseLogout" role="button" aria-expanded="true" aria-controls="collapseLogout"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseLogout">
                                    @if($menu_selections[Config::get('const.MENUITEM.user_pass') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/user_pass') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-assignment-ind-w.svg') }}" alt="">パスワード変更</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.file_download') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/file_download') }}"><img class="icon-size-sm mr-3" src="{{ asset('/images/round-get-app-w.svg') }}" alt="">ダウンロード</a></li>
                                    @endif
                                    <li><a class="px-3 py-1 text-white d-block" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><img class="icon-size-sm mr-3" src="{{ asset('images/round-lock-w.svg') }}" alt="">ログアウト</a></li>
                                </ul>
                            @endif
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </nav>
                    </div>
                    <!-- /offcanvas-left -->
                    @endif
