                    <!-- offcanvas-left -->
                <div id="cnt_menu">
                    <div class="offcanvas_left side-base print-none">
                        <nav class="">
                        <div id="menu_li">
                            <!--<h3 class="side-head p-3 font-size-rg">資材在庫管理システム</h3>-->
                                <ul>
                                        <li class="gc2"><a class="" href="{{ url('/material_management') }}"><!--<img class="iconsize_sm" src="{{ asset('images/round-add-circle-w.svg') }}" alt="">-->資材－在庫</a></li>
                                        <li class="gc3"><a class="" href="{{ url('/mmstock') }}">棚卸</a></li>
                                        <li class="gc4"><a class="" href="{{ url('/mmdust') }}">抹消</a></li>
                                        <!--
                                        <li><a class="" href="{{ url('/') }}"><img class="iconsize_sm" src="{{ asset('images/round-business-w.svg') }}" alt=""><span>検索</span></a></li>
                                        -->
                                        <!--
                                        <li><a class="" href="{{ url('/file_download') }}"><img class="iconsize_sm" src="{{ asset('/images/round-get-app-w.svg') }}" alt="">ダウンロード</a></li>
                                        <li><a class="" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><img class="iconsize_sm" src="{{ asset('images/round-lock-w.svg') }}" alt="">ログアウト</a></li>
                                        -->
                                </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            </form>
                        </div>
                        </nav>
                    </div>
                </div>
                    <!-- /offcanvas-left -->
