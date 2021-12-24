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
                            @if($menu_selections[Config::get('const.MENUITEM.process_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>{{ $menu_selections[Config::get('const.MENUITEM.process_block') - 1]->item_kanji_name }}</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseAggregate">
                                    @if($menu_selections[Config::get('const.MENUITEM.edit_work_order') - 1]->is_select)
                                        @cannot('system-only')
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_work_order') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.edit_work_order') - 1]->item_kanji_name }}</a></li>
                                        @endcannot
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.upload_backorder') - 1]->is_select)
                                        @cannot('system-only')
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/store_backorder') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.upload_backorder') - 1]->item_kanji_name }}</a></li>
                                        @endcannot
                                    @endif
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.progress_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>{{ $menu_selections[Config::get('const.MENUITEM.progress_block') - 1]->item_kanji_name }}</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseAggregate" role="button" aria-expanded="true" aria-controls="collapseAggregate"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseAggregate">
                                    @if($menu_selections[Config::get('const.MENUITEM.check_progress') - 1]->is_select)
                                        @cannot('system-only')
                                            <li><a class="px-3 py-1 text-white d-block" href="{{ url('/process_view') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-notifications-none-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.check_progress') - 1]->item_kanji_name }}</a></li>
                                        @endcannot
                                    @endif
                                </ul>
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.setting_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>{{ $menu_selections[Config::get('const.MENUITEM.setting_block') - 1]->item_kanji_name }}</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseSetting" role="button" aria-expanded="true" aria-controls="collapseSetting"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                @can('admin-higher')
                                <ul class="collapse show list-unstyled" id="collapseSetting">
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_product') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_company_information') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-business-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_product') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_pcustomer') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_department') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-group-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_pcustomer') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_device') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_device') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-group-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_device') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_user') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/edit_user') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-laptop-chromebook-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_user') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_office') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_time_table') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-restore-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_office') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_department') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_department') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-assignment-ind-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_department') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_company') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/create_company_information') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-flag-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_company') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.setting_employment') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/setting_employment') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-flag-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.setting_employment') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                </ul>
                                @endcan
                            @endif
                            @if($menu_selections[Config::get('const.MENUITEM.operation_block') - 1]->is_select)
                                <h3 class="side-head p-3 font-size-rg">
                                    <span>{{ $menu_selections[Config::get('const.MENUITEM.operation_block') - 1]->item_kanji_name }}</span>
                                    <a class="float-right mb-1 font-size-sm line-height-xs btn btn-secondary btn-sm" data-toggle="collapse" href="#collapseLogout" role="button" aria-expanded="true" aria-controls="collapseLogout"><img class="icon-size-xs" src="{{ asset('images/round-expand-less-w.svg') }}" alt=""></a>
                                </h3>
                                <ul class="collapse show list-unstyled" id="collapseLogout">
                                    @if($menu_selections[Config::get('const.MENUITEM.change_pass') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/user_pass') }}"><img class="icon-size-sm mr-3" src="{{ asset('images/round-assignment-ind-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.change_pass') - 1]->item_kanji_name }}</a></li>
                                    @endif
                                    @if($menu_selections[Config::get('const.MENUITEM.download_document') - 1]->is_select)
                                        <li><a class="px-3 py-1 text-white d-block" href="{{ url('/file_download') }}"><img class="icon-size-sm mr-3" src="{{ asset('/images/round-get-app-w.svg') }}" alt="">{{ $menu_selections[Config::get('const.MENUITEM.download_document') - 1]->item_kanji_name }}</a></li>
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
