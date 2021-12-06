<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆加工指示書入力'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_01 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      for="target_fromdate"
                    >
                      納期日付
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuedate"
                    v-bind:date-format="DatePickerFormat"
                    v-bind:place-holder="'納期日付を選択してください'"
                    v-on:change-event="supplydateChanges"
                    v-on:clear-event="supplydateCleared"
                  ></input-datepicker>
                </div>
                <message-data
                  v-bind:message-datas="messagedatasupplydate"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <!-- 
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆客先指定</h1>
                <span class="float-sm-right font-size-sm">客先をを指定します</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_02 pb-2 print-none">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <label
                      class="input-area-text"
                      for="target_customer"
                    >営業所</label>
                  </div>
                  <select-officelist
                    ref="selectofficelist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'営業所を選択してください'"
                    v-bind:selected-value="selectedOfficeValue"
                    v-bind:add-new="false"
                    v-bind:row-index="0"
                    v-on:change-event="officeChanges"
                  ></select-officelist>
                </div>
                <message-data
                  v-bind:message-datas="messagedataoffice"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_03 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <label
                      class="input-area-text"
                      for="target_customer"
                    >客先</label>
                  </div>
                  <select-customerlist
                    ref="selectcustomerlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'客先を選択してください'"
                    v-bind:selected-value="selectedCustomerValue"
                    v-bind:add-new="false"
                    v-bind:office-code="selectedOfficeValue"
                    v-bind:row-index="0"
                    v-on:change-event="customerChanges"
                  ></select-customerlist>
                </div>
                <message-data
                  v-bind:message-datas="messagedatacustomer"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_04 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >受注番号
                    <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="受注番号"
                      class="form-control"
                      @change="ordernoChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataorderno" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_05 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >行</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="行"
                      class="form-control"
                      @change="seqChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatavalueseq" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_06 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >図面番号</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="図面番号"
                      class="form-control"
                      @change="drawingnoChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatadrawingno" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_07 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >個数</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="個数"
                      class="form-control"
                      @change="ordercountChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataordercount" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <!-- 
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆受注番号指定</h1>
                <span class="float-sm-right font-size-sm">受注番号を指定します</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <!-- 
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆品名指定</h1>
                <span class="float-sm-right font-size-sm">品名を指定します</span>
              </div>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_08 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >型式／型番</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="型式／型番"
                      class="form-control"
                      @change="modelnumberChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatamodelnumber" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_09 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >品名</span>
                  </div>
                  <div class="form-control p-0">
                    <select-productlist
                      ref="selectofficelist"
                      v-bind:blank-data="true"
                      v-bind:placeholder-data="'品名を選択してください'"
                      v-bind:selected-value="selectedProductsNameValue"
                      v-bind:add-new="false"
                      v-bind:row-index="0"
                      v-on:change-event="productsnameChanges"
                    ></select-productlist>
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataproductsname" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_10 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >単価</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="単価"
                      max="999999999"
                      min="0"
                      step="1"
                      class="form-control"
                      @change="unitpriceChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataunitprice" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_11 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >明細摘要</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="明細摘要"
                      class="form-control"
                      @change="outlinenameChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataoutlinename" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_12 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >材質・寸法</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="材質・寸法"
                      class="form-control"
                      @change="backorderqualitychanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatabackorderqualityname" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_13 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >材料費</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="材料費"
                      max="999999999"
                      min="0"
                      step="1"
                      class="form-control"
                      @change="materialcostChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedatamaterialcost" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_15 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >素材納入元</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="素材納入元"
                      class="form-control"
                      @change="backorderqualitychanges"
                    />
                  </div>
                </div>
                <message-data
                  v-bind:message-datas="messagedatamaterialcustomer"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_16 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >熱処理</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="熱処理"
                      class="form-control"
                      @change="heatprocesschanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataheatprocess" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_17 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >熱処理費</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="熱処理費"
                      max="999999999"
                      min="0"
                      step="1"
                      class="form-control"
                      @change="heatcostChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataheatcost" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_19 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <label
                      class="input-area-text"
                      for="target_customer"
                    >外注先</label>
                  </div>
                  <select-customerlist
                    ref="selectoutsourcingcustomerlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'外注先を選択してください'"
                    v-bind:selected-value="selectedOutsourcingCustomerValue"
                    v-bind:add-new="false"
                    v-bind:office-code="selectedOutsourcingOfficeValue"
                    v-bind:row-index="0"
                    v-on:change-event="outsourcingcustomerChanges"
                  ></select-customerlist>
                </div>
                <message-data
                  v-bind:message-datas="messagedataoutsourcingcustomer"
                  v-bind:message-class="'warning'"
                ></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_20 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >外注費</span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="number"
                      title="外注費"
                      max="999999999"
                      min="0"
                      step="1"
                      class="form-control"
                      @change="outsourcingcostChanges"
                    />
                  </div>
                </div>
                <message-data v-bind:message-datas="messagedataoutsourcingcost" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0">
                <h1 class="float-sm-left font-size-rg">◆工程管理書入力</h1>
                <span class="float-sm-right font-size-sm"></span>
              </div>
              <!-- /.panel header -->
              <message-data v-bind:message-datas="messagedataclosing" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedataupTime" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedatatimeunit" v-bind:message-class="'warning'"></message-data>
              <message-data v-bind:message-datas="messagedatatimeround" v-bind:message-class="'warning'"></message-data>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row">
              <div class="col-12" id="position_base1">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table id="table_cnt1" class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="td-first text-center align-middle w1">工程No.</td>
                          <td class="text-center align-middle w2">
                            <div v-if="product_processes_maxindex > 1">
                              <btn-work-time
                                v-on:changepattern-event="changepatternclick"
                                v-bind:btn-mode="'changepattern'"
                                v-bind:is-push="false"
                              ></btn-work-time>
                            </div>
                          <span>使用機種</span></td>
                          <td class="text-center align-middle w3">機器名</td>
                          <td class="text-center align-middle w4">加工者</td>
                          <td  colspan="4" class="text-center align-middle ">加工時間</td>
                          <td class="text-center align-middle print-none">完了日</td>
                          <!--<td class="text-center align-middle w-5">QRコード</td>-->
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(n,index) in 12" :key="index">
                          <td class="td-first text-center align-middle">{{form.progress_no[index]}}</td>
                          <td class="text-center align-middle">{{form.progress_name[index]}}</td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="form.device_code[index]"
                              @change="devicecodeChanges(form.device_code[index1], index1)">
                              <option value></option>
                              <option
                                v-for="tlist in progress_details_deviceList"
                                :value="tlist.code"
                                v-bind:key="tlist.code"
                              >{{ tlist.name }}</option>
                            </select>
                          </td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="form.process_user_code[index]"
                              @change="usercodeChanges(form.process_user_code[index], index1)">
                              <option value></option>
                              <option
                                v-for="tlist in progress_details_userList"
                                :value="tlist.code"
                                v-bind:key="tlist.code"
                              >{{ tlist.name }}</option>
                            </select>
                          </td>
                          <td class="text-center align-middle ws1">
                            <div class>
                              <input
                                type="number"
                                step="1"
                                class="form-control"
                                v-model="form.process_time_h[index]"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1">H</td>
                          <td class="td-first text-center align-middle ws1">
                            <div class>
                              <input
                                type="number"
                                step="1"
                                class="form-control"
                                v-model="form.process_time_m[index]"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1">M</td>
                          <td class="text-center align-middle w6 print-none">
                            <div class>
                              <input
                                type="date"
                                class="form-control"
                                v-model="form.complete_date[index]"
                              />
                            </div>
                          </td>
                          <!--
                          <td class="text-center align-middle">
                            <vue-qrcode v-if="form.qrText[index]" :value="form.qrText[index]" :options="qroption" tag="img"></vue-qrcode>
                          </td>
                          -->
                        </tr>
                        <tr>
                          <td colspan="4" class="td-first text-right align-middle pad2 f_style_1">実績合計</td>
                          <td class="text-center align-middle ws1 f_style_1">
                            <div class>
                              <input
                                type="number"
                                step="1"
                                class="form-control"
                                v-model="form.process_time_h[index]"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1 f_style_1">H</td>
                          <td class="td-first text-center align-middle ws1 f_style_1">
                            <div class>
                              <input
                                type="number"
                                step="1"
                                class="form-control"
                                v-model="form.process_time_m[index]"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1 f_style_1">M</td>
                          <td class="text-center align-middle w6 f_style_1 print-none">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
<div class="cnt_view_qr">
  <div class="cnt_view_position"><img src="/images/test_qr.png"></div>
</div><!--end view QR code-->
              </div>
            </div>
            <!-- /.row -->


            <!-- .row -->
            <div class="row">
              <div class="col-12">
              <table id="table_cnt2">
                <tr>
                  <td rowspan="4" class="wrmode">加工時間合計</td>
                  <td class="frame_wh1">
                    <div class="flex1">
                      <div class="cnt2_name">name01</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name02</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name03</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name04</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td class="border_off"><div class="flex2"><p class="str03">前回実績<br>月日＆時間</p></div></td>
                          <td class="text-center align-middle ws1 border_off_r">
                          12
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">月</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          31
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">日</td>
                          <td class="text-center align-middle ws1 border_off_r">
                          23
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">H</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          59
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">M</td>
                </tr>

                <tr>
                  <td class="frame_wh1">
                    <div class="flex1">
                      <div class="cnt2_name">name05</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name06</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name07</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name08</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td class="border_off"></td>
                  <td colspan="4" class="textalign1 border_off">目標加工時間</td>
                          <td class="text-center align-middle ws1 border_off_r">
                          23
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">H</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          59
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">M</td>
                </tr>

                <tr>
                  <td class="frame_wh1">
                    <div class="flex1">
                      <div class="cnt2_name">name09</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name10</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name11</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name12</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td class="border_off"></td>
                  <td colspan="4" class="textalign1 border_off">目標金額</td>
                  <td colspan="4" class="textalign1">99999999</td>
                </tr>

                <tr>
                  <td class="frame_wh1">
                    <div class="flex1">
                      <div class="cnt2_name">name13</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name14</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name15</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td>
                    <div class="flex1">
                      <div class="cnt2_name">name16</div>
                      <div class="cnt2_hm"><span class="str01">99 H</span><span class="str01">59 M</span></div>
                    </div>
                  </td>
                  <td></td>
                  <td colspan="4" class="textalign1 border_off">時間単価</td>
                  <td colspan="4" class="textalign1">10000</td>
                </tr>




              </table>
              </div>
            </div>
            <!-- /.row -->


            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between print-none">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';
import VueQrcode from "@chenfengyuan/vue-qrcode";
// CONST
const CONST_C009 = "C009";
const CONST_C010 = "C010";
const CONST_INDEXORHOME_MENU = 1;
const CONST_INDEXORHOME_HOME = 2;
const CONST_PROGRESSNO_1 = "材料入荷（チェック）";
const CONST_PROGRESSNO_12 = "検査・完了";
const CONST_KBNNAME_CON = "確認"
const CONST_KBNNAME_ADD = "追加"
const CONST_KBNNAME_UPD = "更新"
const CONST_KBNNAME_DEL = "削除"
const CONST_KBNNAME_REL = "解除"
const CONST_KBNNAME_REG = "登録"

export default {
  name: "EditWorkOrder",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
      type: Array,
      default: []
    },
    const_generaldatas: {
        type: Array,
        default: []
    },
    indexorhome: {
        type: Number,
        default: 0
    }
  },
  components: {
    VueQrcode
  },
  data() {
    return {
      valuedate: "",
      defaultDate: new Date(),
      DatePickerFormat: "yyyy年MM月dd日",
      selectedOfficeValue: "",
      selectedCustomerValue: "",
      selectedProductsNameValue: "",
      selectedMaterialOfficeValue: "",
      selectedMaterialCustomerValue: "",
      selectedOutsourcingOfficeValue: "",
      selectedOutsourcingCustomerValue: "",
      value_order_no: "",
      value_drawing_no: "",
      value_order_count: "",
      value_seq: "",
      value_model_number: "",
      value_unit_price: 0,
      value_material_cost: 0,
      value_outline_name: "",
      value_back_order_quality_name: "",
      value_heat_process: "",
      value_heat_cost: 0,
      value_outsourcing_cost: 0,
      getDo: 1,
      messagedatasupplydate: [],
      messagedataoffice: [],
      messagedatacustomer: [],
      messagedataorderno: [],
      messagedatavalueseq: [],
      messagedatadrawingno: [],
      messagedataordercount: [],
      messagedatamodelnumber: [],
      messagedataproductsname: [],
      messagedataunitprice: [],
      messagedataoutlinename: [],
      messagedatabackorderqualityname: [],
      messagedataomaterialffice: [],
      messagedatamaterialcustomer: [],
      messagedatamaterialcost: [],
      messagedataheatprocess: [],
      messagedataheatcost: [],
      messagedataooutsourcingffice: [],
      messagedataoutsourcingcustomer: [],
      messagedataoutsourcingcost: [],
      messagedatastore: [],
      details: [],
      detailslength: 0,
      detailsOrg: [],
      detailsOrglength: 0,
      progress_details_deviceList: [{}],
      progress_details_userList: [{}],
      index_or_home: "",
      product_processes_index: 0,
      product_processes_maxindex: 1,
      form: {
        order_no: "",
        seq: 0,
        row_seq: "",
        drawing_no: "",
        order_date: "",
        supply_date: "",
        office_code: "",
        customer_code: "",
        back_order_customer_name: "",
        order_count: "",
        model_number: "",
        product_code: "",
        processes_code: "",
        back_order_product_name: "",
        unit_price: "",
        outline_name: "",
        back_order_quality_name: "",
        material_cost: "",
        material_office_code: "",
        material_customer_code: "",
        heat_process: "",
        heat_cost: "",
        outsourcing_office_code: "",
        outsourcing_customer_code: "",
        outsourcing_cost: "",
        progress_no: [{}],
        progress_name: [{}],
        product_processes_code: [{}],
        product_processes_detail_no: [{}],
        device_code: [{}],
        process_department_code: [{}],
        process_user_code: [{}],
        process_history_no: [{}],
        process_time_h: [{}],
        process_time_m: [{}],
        setup_history_no: [{}],
        setup_time_h: [{}],
        setup_time_m: [{}],
        complete_date: [{}],
        qrText: [{}],
        total_process_time_h: [{}],
        total_process_time_m: [{}],
        total_setup_time_h: [{}],
        total_setup_time_m: [{}]
      },
      product_resresults: [],
      qroption: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 5,
        scale: 2,
        width: 50,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },


      year: "",
      bMonth: "",
      valueymd: "",
      valueoneMonthTotal: "",
      valuetwoMonthTotal: "",
      valuethreeMonthTotal: "",
      before_valueoneMonthTotal: "",
      before_value_order_no: "",
      limit_valueoneMonthTotal: 100,
      valuecalcauto: "",
      edtString: "",
      days_max: [],
      baseYear: "",
      hidden: "",
      testList: [],
      messagedatayear: [],
      messagedatabiginningMonth: [],
      messagedataoneMonthTotal: [],
      messagedatatwoMonthTotal: [],
      messagedatathreeMonthTotal: [],
      messagedatayearTotal: [],
      messagedataspyearTotal: [],
      messagedataspave_2_6: [],
      messagedataspcount: [],
      messagedataspinterval: [],
      messagedatavaluecalcauto: [],
      messagedataclosing: [],
      messagedataupTime: [],
      messagedatatimeunit: [],
      messagedatatimeround: [],
      const_C009_data: [],
      const_C010_data: [],
      storeisPush: false
    };
  },
  computed: {
    get_c009: function() {
      if (this.const_C009_data.length == 0) {
        var i = 0;
        let $this = this;
        this.const_generaldatas.forEach(function(item) {
          if (item.identification_id == CONST_C009) {
            $this.const_C009_data.push($this.const_generaldatas[i]);
          }
          i++;
        });
      }
      return this.const_C009_data;
    },
    get_c010: function() {
      if (this.const_C010_data.length == 0) {
        var i = 0;
        let $this = this;
        this.const_generaldatas.forEach(function(item) {
          if (item.identification_id == CONST_C010) {
            $this.const_C010_data.push($this.const_generaldatas[i]);
          }
          i++;
        });
      }
      return this.const_C010_data;
    },
    // 月の月末リスト作成
    get_Maxdays: function() {
      if (this.days_max.length == 0) {
        for (let index = 0; index < 12; index++) {
          var month = index + 1;
          this.days_max[index] = new Date(this.form.year, month, 0).getDate();
        }
      }
      return this.days_max;
    },
    // 月の月末取得
    get_Days: function() {
      if (this.days_max.length == 0) {
        this.get_Maxdays;
      }
      let self = this;
      return function (index) {
        return self.days_max[index];
      }
    }
  },
  // マウント時
  mounted() {
    this.valuedate = this.defaultDate;
    var date = new Date();
    this.valueymd = moment(this.defaultDate).format("YYYY");
    this.year = moment(this.valueymd).format("YYYY");
    this.inputClear();
    this.messageClear();
    // this.baseYear = date.getFullYear();
    this.index_or_home = this.indexorhome;
    // 1:index 2:homeindex
    if (this.index_or_home == CONST_INDEXORHOME_HOME) {
      this.itemClear();
      this.getItem(false);
    }
    this.getDeviceList();
    this.getUserList(this.valuedate);
    this.form.supply_date = this.valuedate;
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    checkForm: function() {
      var flag = true;
      this.messageClear();

      if (this.form.supply_date == "" || this.form.supply_date == null) {
        this.messagedatasupplydate.push("納期日付は必ず入力してください");
      }
      if (this.messagedatasupplydate.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedatasupplydate;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedatasupplydate);
        }
      }
      if (this.messagedatastore.length > 0) {
        flag  = false;
      }

      console.log('checkForm this.form.order_no = ' + this.form.order_no)
      if (this.form.order_no == "" || this.form.order_no == null) {
        this.messagedataorderno.push("受注番号は必ず入力してください");
      }
      if (this.messagedataorderno.length > 0) {
        if (this.messagedatastore.length == 0) {
          this.messagedatastore = this.messagedataorderno;
        } else {
          this.messagedatastore = this.messagedatastore.concat(this.messagedataorderno);
        }
      }
      if (this.messagedatastore.length > 0) {
        flag  = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    // 指定日付が変更された場合の処理
    supplydateChanges: function(value) {
      moment.locale("ja");
      this.stringtext = "";
      this.form.supply_date = value;
      if (this.form.supply_date == null || this.form.supply_date == "") {
        this.stringtext = "";
      } else {
        this.datejaFormat = moment(this.form.supply_date).format(
          "YYYY年MM月DD日 (ddd)"
        );
      }
    },
    // 指定日付がクリアされた場合の処理
    supplydateCleared: function() {
      this.valuedate = "";
      this.form.supply_date = "";
      this.stringtext = "";
    },
    // 営業所選択が変更された場合の処理
    officeChanges: function(value, arrayitem) {
      this.form.office_code = value;
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getCustomerSelected(this.form.office_code);
    },
    // 客先選択が変更された場合の処理
    customerChanges: function(value, arrayitem) {
      this.form.customer_code = value;
    },

    // 受注番号が変更された場合の処理
    ordernoChanges: function(event) {
      this.form.order_no = event.target.value;
      this.setQrText();

    },
    // 行が変更された場合の処理
    seqChanges: function(event) {
      this.form.row_seq = event.target.value;
      this.setQrText();
    },
    // 図面番号が変更された場合の処理
    drawingnoChanges: function(event) {
      this.form.drawing_no = event.target.value;
      this.setQrText();
    },
    // 個数が変更された場合の処理
    ordercountChanges: function(event) {
      this.form.order_count = event.target.value;
    },
    // 型式／型番が変更された場合の処理
    modelnumberChanges: function(event) {
      this.form.model_number = event.target.value;
    },
    // 品名が変更された場合の処理
    productsnameChanges: function(value, arrayitem) {
      this.form.product_code = event.target.value;
      // 工程データ読み込み工程管理書入力の2-11に設定する
      this.getProductProcess(this.form.product_code);
    },
    // 単価が変更された場合の処理
    unitpriceChanges: function(event) {
      this.form.unit_price = event.target.value;
    },
    // 明細摘要が変更された場合の処理
    outlinenameChanges: function(event) {
      this.form.outline_name = event.target.value;
    },
    // 材質・寸法が変更された場合の処理
    backorderqualitychanges: function(event) {
      this.form.back_order_quality_name = event.target.value;
    },
    // 材料費が変更された場合の処理
    materialcostChanges: function(event) {
      this.form.material_cost = event.target.value;
    },
    // 素材納入営業所が変更された場合の処理
    materialofficeChanges: function(value, arrayitem) {
      this.form.material_Office_code = value;
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getMaterialCustomerSelected(this.form.material_Office_code);
    },
    // 素材納入元が変更された場合の処理
    materialcustomerChanges: function(value, arrayitem) {
      this.form.material_customer_code = value;
    },
    // 熱処理が変更された場合の処理
    heatprocesschanges: function(event) {
      this.form.heat_process = event.target.value;
    },
    // 熱処理費が変更された場合の処理
    heatcostChanges: function(event) {
      this.form.heat_cost = event.target.value;
    },
    // 外注先営業所が変更された場合の処理
    outsourcingofficecodeChanges: function(value, arrayitem) {
      this.form.outsourcing_Office_code = value;
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getOutsourcingCustomerSelected(this.this.form.outsourcing_Office_code);
    },
    // 外注先が変更された場合の処理
    outsourcingcustomerChanges: function(value, arrayitem) {
      this.form.outsourcing_customer_code = value;
    },
    // 外注費が変更された場合の処理
    outsourcingcostChanges: function(event) {
      this.form.outsourcing_cost = event.target.value;
    },
    // 機器名が変更された場合の処理
    devicecodeChanges: function(value , index) {
    },
    // 加工者が変更された場合の処理
    usercodeChanges: function(value , index) {
    },
    // 加工時間が変更された場合の処理
    processtimeChanges: function(index) {
      //
    },
    // 段取り時間が変更された場合の処理
    setuptimeChanges: function(index) {
      //
    },
    // 完了日が変更された場合の処理
    completeChanges: function(index) {
      //
    },
    // パターン変更の場合の処理
    changepatternclick: function() {
      if (this.product_processes_index < this.product_processes_maxindex - 1) {
        this.product_processes_index +=1;
      } else {
        this.product_processes_index = 0;
      }
      this.setProductProcessTable();
      //
    },
    // 工程データ登録処理
    storeclick() {
      if (this.checkForm()) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
          result => {
            if (result) {
              this.storeData();
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagedatastore,
          "error",
          true,
          false,
          true
        ).then(result => {
          if (result) {
          }
        });
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // 機器リスト取得処理
    getDeviceList(){
      this.postRequest("/get_device_list", { code: null })
        .then(response  => {
          this.getThenDeviceList(response);
        })
        .catch(reason => {
          this.serverCatch("機器リスト");
        });
    },
    // 社員リスト取得処理
    getUserList(targetdate){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      this.login_user_department_code = null;
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: null,
          getDo : 1,
          departmentcode : null,
          employmentcode : null
        })
        .then(response  => {
          this.getThenuser(response);
        })
        .catch(reason => {
          this.serverCatch("社員リスト", "取得");
        });
    },
    // 工程データ取得処理
    getProductProcess(value){
      if (value != '') {
        this.login_user_department_code = null;
        this.postRequest("/get_product_processes",
          { 
            product_code: value
          })
          .then(response  => {
            this.getThenprocess(response);
          })
          .catch(reason => {
            this.serverCatch("工程データ", "取得");
          });
      }
    },
    
    // 労働時間基本設定取得処理
    getItem() {
      this.inputClear();
      this.messageClear();
      this.form.year = this.year;
      var arrayParams = { year : this.year };
      this.postRequest("/setting_calc/get", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 登録処理
    storeData() {
      this.form.seq = 0;
      this.form.supply_date = moment(this.valuedate).format("YYYYMMDD")
      var arrayParams = { form : this.form };
      this.postRequest("/edit_work_order/put_process", arrayParams)
        .then(response => {
          this.putThenprogress(response, CONST_KBNNAME_REG);
        })
        .catch(reason => {
          this.serverCatch("加工指示書／工程管理書", CONST_KBNNAME_REG);
        });
    },
    // -------------------- 共通 ----------------------------
    // 客先選択コンポーネント取得メソッド
    getCustomerSelected: function(value) {
      this.$refs.selectcustomerlist.getList(value);
    },
    // 素材納入元選択コンポーネント取得メソッド
    getMaterialCustomerSelected: function(value) {
      this.$refs.selectmaterialcustomerlist.getList(value);
    },
    // 外注先選択コンポーネント取得メソッド
    getOutsourcingCustomerSelected: function(value) {
      this.$refs.selectoutsourcingcustomerlist.getList(value);
    },
    // 取得正常処理
    getThenDeviceList(response) {
      var res = response.data;
      if (res.result) {
        this.progress_details_deviceList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 取得正常処理
    getThenuser(response) {
      var res = response.data;
      console.log('getThenuser = res.result' + res.result);
      if (res.result) {
        console.log('getThenuser = res.details' + res.details.length);
        this.progress_details_userList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 取得正常処理
    getThenprocess(response) {
      var res = response.data;
      if (res.result) {
        if (res.details != null) {
          this.product_resresults = res.details;
          this.product_processes_index = 0;
          this.setProductProcessTable();
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 取得正常処理
    getThen(response) {
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.year = this.details[0].fiscal_year;
          this.form.biginningMonth = this.details[0].beginning_month;
          this.form.sp_count = this.details[0].count_sp;
          if (this.details[0].calc_auto_time != null) {
            this.form.calc_auto_time = this.details[0].calc_auto_time.toString();
            this.valuecalcauto = this.form.calc_auto_time;
          }
          if (this.details[0].max_1month_total != null) {
            this.form.oneMonthTotal = this.details[0].max_1month_total.toString();
            this.valueoneMonthTotal = this.details[0].max_1month_total.toString();
            this.limit_valueoneMonthTotal = this.details[0].max_1month_total;
          }
          if (this.details[0].max_2month_total != null) {
            this.form.twoMonthTotal = this.details[0].max_2month_total.toString();
            this.valuetwoMonthTotal = this.details[0].max_2month_total.toString();
          }
          if (this.details[0].max_3month_total != null) {
            this.form.threeMonthTotal = this.details[0].max_3month_total.toString();
            this.valuethreeMonthTotal = this.details[0].max_3month_total.toString();
          }
          if (this.details[0].max_12month_total != null) {
            this.form.yearTotal = this.details[0].max_12month_total.toString();
          }
          if (this.details[0].max_1month_total_sp != null) {
            this.form.sp_oneMonthTotal = this.details[0].max_1month_total_sp.toString();
            this.value_order_no = this.details[0].max_1month_total_sp.toString();
            this.limit_valueoneMonthTotal = this.details[0].max_1month_total_sp;
          }
          if (this.details[0].ave_2_6_time_sp != null) {
            this.form.sp_ave_2_6 = this.details[0].ave_2_6_time_sp.toString();
          }
          if (this.details[0].max_12month_total_sp != null) {
            this.form.value_drawing_no = this.details[0].max_12month_total_sp.toString();
          }
          if (this.details[0].interval != null) {
            this.form.sp_interval = this.details[0].interval.toString();
          }
          this.details.forEach((detail, i) => {
            if (detail.closing != null) {
              this.form.closingDate[i] = detail.closing.toString();
            } else {
              this.form.closingDate[i] = "";
            }
            if (detail.uplimit_time != null) {
              this.form.upTime[i] = detail.uplimit_time.toString();
            } else {
              this.form.upTime[i] = "";
            }
            if (detail.time_unit != null) {
              this.form.timeunit[i] = detail.time_unit.toString();
            } else {
              this.form.timeunit[i] = "";
            }
            if (detail.time_rounding != null) {
              this.form.timeround[i] = detail.time_rounding.toString();
            } else {
              this.form.timeround[i] = "";
            }
            this.bMonth = detail.beginning_month;
          });
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThenprogress(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("加工指示書／工程管理書を" + eventtext + "しました。");
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("加工指示書／工程管理書", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // 項目クリア
    inputClear() {
      this.details = [];
      this.count = 0;
      this.before_count = 0;

      this.form.supply_date = "";
      this.form.office_code = "";
      this.form.customer_code = "";
      this.form.order_no = "";
      this.form.row_seq = "";
      this.form.drawing_no = "";
      this.form.order_count = "";
      this.form.model_number = "";
      this.form.product_code = "";
      this.form.processes_code = "";
      this.form.unit_price = "";
      this.form.outline_name = "";
      this.form.back_order_quality_name = "";
      this.form.material_cost = "";
      this.form.material_Office_code = "";
      this.form.material_customer_code = "";
      this.form.heat_process = "";
      this.form.heat_cost = "";
      this.form.outsourcing_Office_code = "";
      this.form.outsourcing_customer_code = "";
      this.form.outsourcing_cost = "";

      this.form.progress_no[0] = 1;
      this.form.progress_name[0] = CONST_PROGRESSNO_1;
      this.form.product_processes_code[0] = '00';
      this.form.product_processes_detail_no[0] = 0;
      this.form.device_code[0] = "";
      this.form.process_department_code[0] = "";
      this.form.process_history_no[0] = "";
      this.form.setup_history_no[0] = "";
      this.form.process_user_code[0] = "";
      this.form.process_time_h[0] = "";
      this.form.process_time_m[0] = "";
      this.form.setup_time_h[0] = "";
      this.form.setup_time_m[0] = "";
      this.form.complete_date[0] = "";
      this.form.qrText[0] = null;
      this.form.progress_no[11] = 12;
      this.form.progress_name[11] = CONST_PROGRESSNO_12;
      this.form.product_processes_code[11] = '99';
      this.form.product_processes_detail_no[11] = 0;
      this.form.device_code[11] = "";
      this.form.process_department_code[11] = "";
      this.form.process_history_no[11] = "";
      this.form.setup_history_no[11] = "";
      this.form.process_user_code[11] = "";
      this.form.process_time_h[11] = "";
      this.form.process_time_m[11] = "";
      this.form.setup_time_h[11] = "";
      this.form.setup_time_m[11] = "";
      this.form.complete_date[11] = "";
      this.form.qrText[11] = null;
      for (let index = 1; index < 11; index++) {
        this.form.progress_no[index] = index + 1;
        this.form.progress_name[index] = "";
        this.form.product_processes_code[index] = "";
        this.form.product_processes_detail_no[index] = 0;
        this.form.device_code[index] = "";
        this.form.process_department_code[index] = "";
        this.form.process_history_no[index] = "";
        this.form.setup_history_no[index] = "";
        this.form.process_user_code[index] = "";
        this.form.process_time_h[index] = "";
        this.form.process_time_m[index] = "";
        this.form.setup_time_h[index] = "";
        this.form.setup_time_m[index] = "";
        this.form.complete_date[index] = "";
        this.form.qrText[index] = null;
      }
    },
    // メッセージ項目クリア
    messageClear() {
      this.messagedatasupplydate = [];
      this.messagedataoffice = [];
      this.messagedatacustomer = [];
      this.messagedataorderno = [];
      this.messagedatavalueseq = [];
      this.messagedatadrawingno = [];
      this.messagedataordercount = [];
      this.messagedatamodelnumber = [];
      this.messagedataproductsname = [];
      this.messagedataunitprice = [];
      this.messagedataoutlinename = [];
      this.messagedatabackorderqualityname = [];
      this.messagedataomaterialffice = [];
      this.messagedatamaterialcustomer = [];
      this.messagedatamaterialcost = [];
      this.messagedataheatprocess = [];
      this.messagedataheatcost = [];
      this.messagedataooutsourcingffice = [];
      this.messagedataoutsourcingcustomer = [];
      this.messagedataoutsourcingcost = [];
      this.messagedatastore = [];
    },
    // tooltips文字列生成
    edttooltips: function(value1, value2) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + '\n' + value2;
      }
    },
    // QRセット
    setQrText() {
      for (let index = 0; index < 12; index++) {
        this.form.qrText[index] = this.form.order_no + this.form.row_seq + this.form.drawing_no + ('00' + (index+1)).slice(-2);
      }
      this.$forceUpdate();
    },
    // 使用機種セット
    setProductProcessTable() {
      for (let index = 1; index < 11; index++) {
        this.form.progress_no[index] = index + 1;
        this.form.progress_name[index] = "";
        this.form.product_processes_code[index] = "";
        this.form.product_processes_detail_no[index] = 0;
        this.form.device_code[index] = "";
        this.form.process_user_code[index] = "";
        this.form.process_time_h[index] = "";
        this.form.process_time_m[index] = "";
        this.form.setup_time_h[index] = "";
        this.form.setup_time_m[index] = "";
        this.form.complete_date[index] = "";
      }
      let $this = this;
      var i = 0;
      this.product_processes_maxindex = this.product_resresults.length;

      this.product_resresults.forEach( function( items ) {
        if(i == $this.product_processes_index){
          $this.form.processes_code = items.processes_code;
          var array_detailes = items.array_products_details;
          for (let index = 0; index < array_detailes.length; index++) {
            $this.form.progress_name[index + 1] = array_detailes[index].detail_name;
            $this.form.product_processes_code[index + 1] = array_detailes[index].product_processes_code;
            $this.form.product_processes_detail_no[index + 1] = array_detailes[index].product_processes_detail_no;
          }
        }
        i = i + 1;
      });
      this.$forceUpdate();
    }
  }
};
</script>
<style scoped>
.mw-rem-2 {
    min-width: 2rem;
}
</style>
