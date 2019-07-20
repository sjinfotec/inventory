			    <div class="col-lg-2 col-xs-12 sidebar align-stretch min-height-side-fit">
                    <nav>
                        <!-- block -->
                        <h1 class="font-size-regular">業務メニュー</h1>
                        <ul class="list-unstyled">
                            <li><a href="{{ url('/daily') }}">日次集計</a></li>
                            <li><a href="{{ url('/monthly') }}">月次集計</a></li>
                            <li><a href="{{ url('/setting_shift_time') }}">シフト割り当て</a></li>
                        </ul>
                        <h1 class="font-size-regular">マスタメンテナンス</h1>
                        <ul class="list-unstyled">
                            <li><a href="{{ url('/user_add') }}">ユーザー</a></li>
                            <li><a href="{{ url('/create_department') }}">部署/編集</a></li>
                            <li><a href="{{ url('/create_time_table') }}">タイムテーブル</a></li>
                            <!-- <li><a href="{{ url('/create_shift_time') }}">シフト時間</a></li> -->
                            <li><a href="{{ url('/create_company_information') }}">会社情報</a></li>
                            <li><a href="{{ url('/create_calendar') }}">カレンダー登録</a></li>
                            <li><a href="{{ url('/edit_calendar') }}">カレンダー編集</a></li>
                        </ul>
                        <!-- /block -->
                        @if(Auth::check())
                            @if(Auth::user()->is_admin == 1)
                                <!-- block -->
                                <h1 class="font-size-medium">管理者メニュー</h1>
                                <ul class="list-unstyled">
                                    @can('system-only')
                                    <li><img class="svg-icon-size-regular" src="{{ asset('img/siconGearW.svg') }}"/><a href="{{ url('/shift_change') }}">シフト変更申請</a></li>
                                    <li><img class="svg-icon-size-regular" src="{{ asset('img/siconKeyW.svg') }}"/><a href="{{ url('/room_logs') }}">入室履歴</a></li>
                                    @elsecan('admin-higher')
                                    <li><img class="svg-icon-size-regular" src="{{ asset('img/siconGearW.svg') }}"/><a href="{{ url('/shift_change') }}">シフト変更申請</a></li>
                                    @elsecan('admin-midle')
                                    <li><img class="svg-icon-size-regular" src="{{ asset('img/siconGearW.svg') }}"/><a href="{{ url('/shift_change') }}">シフト変更申請</a></li>
                                    @elsecan('user-higher')
                                    @endcan
                                </ul>
                                <!-- /block -->
                            @endif
                        @endif
                        <!-- block -->
                        <h1 class="font-size-regular">ログインユーザー</h1>
                        <ul class="list-unstyled">
                            @if(Auth::check())
                            <li>{{ Auth::user()->name }}</li>
                            @endif
                        </ul>
                        <!-- /block -->
                    </nav>
                </div>
