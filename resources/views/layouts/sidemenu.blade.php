                    <!-- offcanvas-left -->
                    <div class="col-xl-2 px-0 offcanvas-collapse offcanvas-collapse-from-left side-base">
                        <nav class="text-white">
                            <h3 class="side-head p-3 font-size-rg">アカウント情報</h3>
                            <div class="float-left mx-2">
                                <img class="p-2 icon-size-user rounded-circle" src="{{ asset('images/round-person-w.svg') }}" alt="">
                            </div>
                            <ul class="float-left list-unstyled">
                                <li class="pr-3 py-1 text-white font-size-sm d-block">情報処理課</li>
                                <li class="pr-3 py-1 text-white font-size-sm d-block">
                                @if(Auth::check())
                                    {{ Auth::user()->name }}
                                @endif
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                            <h3 class="side-head p-3 font-size-rg">
                                <span>集計</span>
                                <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                            </h3>
                            <ul class="collapse show list-unstyled" id="collapseAggregate">
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/daily') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">日次集計</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/monthly') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">月次集計</a></li>
                            </ul>
                            <h3 class="side-head p-3 font-size-rg">
                                <span>編集</span>
                                <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseEdit" role="button" aria-expanded="true" aria-controls="collapseEdit"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                            </h3>
                            <ul class="collapse show list-unstyled" id="collapseEdit">
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_calendar') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-outlined-flag-w.svg') }}" alt="">カレンダー編集</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_shift_time') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-access-time-w.svg') }}" alt="">シフト編集</a></li>
                            </ul>
                            <h3 class="side-head p-3 font-size-rg">
                                <span>設定</span>
                                <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseSetting" role="button" aria-expanded="true" aria-controls="collapseSetting"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                            </h3>
                            <ul class="collapse show list-unstyled" id="collapseSetting">
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_company_information') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-business-w.svg') }}" alt="">会社設定</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_department') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-group-w.svg') }}" alt="">組織設定</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_time_table') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-restore-w.svg') }}" alt="">勤務時間設定</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_calc') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-laptop-chromebook-w.svg') }}" alt="">時間計算設定</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_calendar') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-flag-w.svg') }}" alt="">カレンダー設定</a></li>
                                <li><a class="px-3 py-1 text-white d-block" href="{{ url('/user_add') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-assignment-ind-w.svg') }}" alt="">ユーザ情報設定</a></li>
                            </ul>
                        </nav>
                    </div>
                    <!-- /offcanvas-left -->
