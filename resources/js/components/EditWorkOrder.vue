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
              <div class="col-cnt_22 pb-2 print_view">
                <div id="input-area_1">
                  <h2>加工指示書／工程管理書</h2>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-cnt_21 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      id="basic-addon1"
                    >保存シート
                    </span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="保存シート"
                      class="inlineblock form-control storage_sheet_input"
                      :value="value_storage_sheet"
                      @change="storagesheetChanges"
                    />
                    <p class="inlineblock storage_sheet_text">で渡し済</p>
                  </div>
                </div>
                <span class="print-none"><message-data v-bind:message-datas="messagedataorderno" v-bind:message-class="'warning'"></message-data></span>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-cnt_01 pb-2">
                <div id="input-area_1">
                  <div class="input-area-prepend">
                    <span
                      class="input-area-text"
                      for="target_fromdate"
                    >
                      納期日付
                      <span class="color-red print-none">[必須]</span>
                    </span>
                  </div>
                  <input-datepicker
                    v-bind:default-date="valuesupplydate"
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
                    v-if="showofficelist"
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
                  <div v-if="selectedOfficeValue">
                    <select-customerlist
                      ref="selectcustomerlist"
                      v-if="showoCustomerlist"
                      v-bind:blank-data="true"
                      v-bind:placeholder-data="'客先を選択してください'"
                      v-bind:selected-value="selectedCustomerValue"
                      v-bind:add-new="false"
                      v-bind:office-code="selectedOfficeValue"
                      v-bind:row-index="0"
                      v-on:change-event="customerChanges"
                    ></select-customerlist>
                  </div>
                  <div v-else class="form-control p-0">
                    <input
                      type="text"
                      title="客先"
                      class="form-control"
                      :value="value_back_order_customer_name"
                      @change="backordercustomernameChanges"
                    />
                  </div>
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
                    <span class="color-red print-none">[必須]</span>
                    </span>
                  </div>
                  <div class="form-control p-0">
                    <input
                      type="text"
                      title="受注番号"
                      class="form-control"
                      :value="value_order_no"
                      @change="ordernoChanges"
                    />
                  </div>
                </div>
                <span class="print-none"><message-data v-bind:message-datas="messagedataorderno" v-bind:message-class="'warning'"></message-data></span>
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
                      :value="value_row_seq"
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
                      :value="value_drawing_no"
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
                      :value="value_order_count"
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
                      :value="value_model_number"
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
                    <input
                      type="text"
                      title="品名"
                      class="form-control"
                      v-model="selectedProductsValue"
                      @change="productsnameChanges"
                    />
<!--
                    <select-productlist
                      ref="selectproductlist"
                      v-if="showoProductlist"
                      v-bind:blank-data="true"
                      v-bind:placeholder-data="'品名を選択してください'"
                      v-bind:selected-value="selectedProductsValue"
                      v-bind:add-new="false"
                      v-bind:row-index="0"
                      v-on:change-event="productsnameChanges"
                    ></select-productlist>
-->
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
                  <div class="form-control fontsize0  p-0" v-if="unitpriceediting">
                    <p class="inlineblock tankaatto">@</p>
                    <input
                      ref="target"
                      type="number"
                      title="単価"
                      min="0"
                      class="form-control inputnum_r inlineblock tankainput"
                      :value="value_unit_price"
                      @change="unitpriceChanges"
                      @focusin="unitpriceFocusin"
                      @focusout="unitpriceFocusout"

                    />
                  </div>
                  <div class="form-control fontsize0 p-0" v-else>
                    <p class="inlineblock tankaatto">@</p>
                    <input
                      type="text"
                      title="単価"
                      class="form-control inputnum_r inlineblock tankainput"
                      :value ="value_unit_price | localeNum"
                      @change="unitpriceChanges"
                      @focusin="unitpriceFocusin"
                      @focusout="unitpriceFocusout"

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
                      :value="value_outline_name"
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
                      :value="value_back_order_quality_name"
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
                  <div class="form-control p-0" v-if="materialcostediting">
                    <input
                      ref="target"
                      type="number"
                      title="材料費"
                      min="0"
                      class="form-control inputnum_r"
                      :value="value_material_cost"
                      @change="materialcostChanges"
                      @focusin="materialcostFocusin"
                      @focusout="materialcostFocusout"

                    />
                  </div>
                  <div class="form-control p-0" v-else>
                    <input
                      type="text"
                      title="材料費"
                      class="form-control inputnum_r"
                      :value ="value_material_cost | localeNum"
                      @change="materialcostChanges"
                      @focusin="materialcostFocusin"
                      @focusout="materialcostFocusout"

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
                      :value="value_material_customer_name"
                      @change="materialcustomerChanges"
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
                      :value="value_heat_process"
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
                  <div class="form-control p-0" v-if="heatcostediting">
                    <input
                      ref="target"
                      type="number"
                      title="熱処理費"
                      min="0"
                      class="form-control inputnum_r"
                      :value="value_heat_cost"
                      @change="heatcostChanges"
                      @focusin="heatcostFocusin"
                      @focusout="heatcostFocusout"

                    />
                  </div>
                  <div class="form-control p-0" v-else>
                    <input
                      type="text"
                      title="熱処理費"
                      class="form-control inputnum_r"
                      :value ="value_heat_cost | localeNum"
                      @change="heatcostChanges"
                      @focusin="heatcostFocusin"
                      @focusout="heatcostFocusout"

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
                  <select-outsoucingcustomerlist
                    ref="selectoutsourcingcustomerlist"
                    v-if="showoOutsourcingcustomerlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'外注先を選択してください'"
                    v-bind:selected-value="selectedOutsourcingCustomerValue"
                    v-bind:add-new="false"
                    v-bind:row-index="0"
                    v-on:change-event="outsourcingcustomerChanges"
                  ></select-outsoucingcustomerlist>
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
                  <div class="form-control p-0" v-if="outsourcingcostediting">
                    <input
                      ref="target"
                      type="number"
                      title="外注費"
                      min="0"
                      class="form-control inputnum_r"
                      :value="value_outsourcing_cost"
                      @change="outsourcingcostChanges"
                      @focusin="outsourcingcostFocusin"
                      @focusout="outsourcingcostFocusout"

                    />
                  </div>
                  <div class="form-control p-0" v-else>
                    <input
                      type="text"
                      title="外注費"
                      class="form-control inputnum_r"
                      :value ="value_outsourcing_cost | localeNum"
                      @change="outsourcingcostChanges"
                      @focusin="outsourcingcostFocusin"
                      @focusout="outsourcingcostFocusout"

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




          <div class="card-body mb-3 p-0 border-top print-none" v-if="isQr">
            <!-- panel contents -->
            <!-- .row -->
            <div id="btn_cnt4">
              <!-- .col -->
              <div class="btn_col_1">
                <div class="input-group btn_sty_1">
                  <a @click="no_qrcodeClick" class="btn btn-primary">印刷</a>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="btn_col_1">
                <div class="input-group btn_sty_1">
                  <a @click="qrcodeClick" class="btn btn-primary">QRコード付き印刷</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>


 



          <div class="card-body mb-3 p-0 border-top" >
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between px-3">
              <!-- panel header -->
              <div class="card-header col-12 bg-transparent pb-2 border-0 print-none">
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
                          <td class="text-center align-middle w2">使用機種</td>
                          <td class="text-center align-middle w3">機器名</td>
                          <td class="text-center align-middle w4">加工者</td>
                          <td  colspan="4" class="text-center align-middle ">加工時間</td>
                          <td class="text-center align-middle ">完了日</td>
                          <!--<td class="text-center align-middle w-5">QRコード</td>-->
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(n,index) in 12" :key="index">
                          <td class="td-first text-center align-middle">{{form.progress_no[index]}}</td>
                          <td class="text-center align-middle">{{form.progress_name[index]}}</td>
                          <td class="text-center align-middle">
                            <select class="form-control" v-model="form.device_code[index]"
                              @change="devicecodeChanges(form.device_code[index], index)">
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
                              @change="usercodeChanges(form.process_user_code[index], index)">
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
                                min="0"
                                class="form-control inputnum_r"
                                v-model="form.process_time_h[index]"
                                @change="processtimeHChanges()"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1">H</td>
                          <td class="td-first text-center align-middle ws1">
                            <div class>
                              <input
                                type="number"
                                step="1"
                                min="0"
                                class="form-control inputnum_r"
                                v-model="form.process_time_m[index]"
                                @change="processtimeMChanges()"
                              />
                            </div>
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1">M</td>
                          <td class="text-center align-middle w6 ">
                            <div class>
                              <input
                                type="date"
                                class="form-control"
                                v-model="form.complete_date[index]"
                              />
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="4" class="td-first text-right align-middle pad2 f_style_1 jtime str_style1">実績合計</td>
                          <td class="text-center align-middle ws1 f_style_1 str_style1">
                            {{form.result_process_time_h}}
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1 f_style_1 str_style1">H</td>
                          <td class="td-first text-center align-middle ws1 f_style_1 str_style1">
                            {{form.result_process_time_m}}
                          </td>
                          <td class="td-first text-left align-middle ws2 pad1 f_style_1 str_style1">M</td>
                          <td class="text-center align-middle w6 f_style_1 ">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- 印刷用QRコード-->
                <div id="view_off" >
                  <div id="print_view_qr" class="cnt_view_qr">
                    <div class="cnt_view_position" v-if="qrText">
                      <vue-qrcode
                        :value="qrText" :options="qroption1" tag="img">
                      </vue-qrcode>
                      <p><span>[{{ form.order_no }}][{{ form.row_seq }}][{{ form.drawing_no }}]{{qrText}}</span></p>
                    </div>
                  </div><!--end view QR code v-if="isprint_qrText"-->
                </div>
                <!--/印刷用QRコード-->
              </div>
            </div>
            <!-- /.row -->


            <!-- .row -->
            <div class="row">
              <div class="col-12">
              <table id="table_cnt2">
                <tr>
                  <td rowspan="4" class="wrmode">加<br>工<br>時<br>間<br>合<br>計</td>
                  <td class="frame_wh1" v-for="(n,index1) in 4" :key="index1">
                    <div class="flex1" v-if="form.total.process_total_user_name_1[index1]">
                      <div class="cnt2_name">{{ form.total.process_total_user_name_1[index1] }}</div>
                      <div class="cnt2_hm" v-if="form.total.process_result_process_time_h_1[index1]">
                        <span class="str01">{{ form.total.process_result_process_time_h_1[index1] }}H</span>
                        <span class="str01">{{ form.total.process_result_process_time_m_1[index1] }}M</span></div>
                    </div>
                    <div class="flex1" v-else>
                      <div class="cnt2_name"></div>
                      <div class="cnt2_hm">
                        <span class="str01"></span>
                        <span class="str01"></span></div>
                    </div>
                  </td>
                  <td class="border_off"><div class="flex2"><p class="str03">前回実績<br>月日＆時間</p></div></td>
                  <td class="frame_wh2"></td>
                  <td class="frame_wh2"></td>
                  <!--
                          <td class="text-center align-middle ws1 border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">月</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">日</td>
                          <td class="text-center align-middle ws1 border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">H</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">M</td>
                  -->
                </tr>

                <tr>
                  <td class="frame_wh1" v-for="(n,index2) in 4" :key="index2">
                    <div class="flex1" v-if="form.total.process_total_user_name_2[index2]">
                      <div class="cnt2_name">{{ form.total.process_total_user_name_2[index2] }}</div>
                      <div class="cnt2_hm" v-if="form.total.process_result_process_time_h_2[index2]">
                        <span class="str01">{{ form.total.process_result_process_time_h_2[index2] }}H</span>
                        <span class="str01">{{ form.total.process_result_process_time_m_2[index2] }}M</span></div>
                    </div>
                    <div class="flex1" v-else>
                      <div class="cnt2_name"></div>
                      <div class="cnt2_hm">
                        <span class="str01"></span>
                        <span class="str01"></span></div>
                    </div>
                  </td>
                  <td class="border_off"></td>
                  <td class="textalign1 border_off">目標加工時間</td>
                  <td></td>
                  <!--
                          <td class="text-center align-middle ws1 border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l border_off_r">H</td>
                          <td class="text-center align-middle ws1 border_off_l border_off_r">
                          </td>
                          <td class="text-left align-middle ws2 pad1 border_off_l">M</td>
                  -->
                </tr>

                <tr>
                  <td class="frame_wh1" v-for="(n,index3) in 4" :key="index3">
                    <div class="flex1" v-if="form.total.process_total_user_name_3[index3]">
                      <div class="cnt2_name">{{ form.total.process_total_user_name_3[index3] }}</div>
                      <div class="cnt2_hm" v-if="form.total.process_result_process_time_h_3[index3]">
                        <span class="str01">{{ form.total.process_result_process_time_h_3[index3] }}H</span>
                        <span class="str01">{{ form.total.process_result_process_time_m_3[index3] }}M</span></div>
                    </div>
                    <div class="flex1" v-else>
                      <div class="cnt2_name"></div>
                      <div class="cnt2_hm">
                        <span class="str01"></span>
                        <span class="str01"></span></div>
                    </div>
                  </td>
                  <td class="border_off"></td>
                  <td class="textalign1 border_off">目標金額</td>
                  <td class="textalign1"></td>
                </tr>

                <tr>
                  <td class="frame_wh1" v-for="(n,index4) in 4" :key="index4">
                    <div class="flex1" v-if="form.total.process_total_user_name_4[index4]">
                      <div class="cnt2_name">{{ form.total.process_total_user_name_4[index4] }}</div>
                      <div class="cnt2_hm" v-if="form.total.process_result_process_time_h_4[index4]">
                        <span class="str01">{{ form.total.process_result_process_time_h_4[index4] }}H</span>
                        <span class="str01">{{ form.total.process_result_process_time_m_4[index4] }}M</span></div>
                    </div>
                    <div class="flex1" v-else>
                      <div class="cnt2_name"></div>
                      <div class="cnt2_hm">
                        <span class="str01"></span>
                        <span class="str01"></span></div>
                    </div>
                  </td>
                  <td class="frame_wh1"></td>
                  <td class="textalign1 border_off">時間単価</td>
                  <td class="textalign1"></td>
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
    order_no: {
      type: String,
      default: ""
    },
    row_seq: {
      type: String,
      default: ""
    },
    seq: {
      type: Number,
      default: 0
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
      valuesupplydate: "",
      defaultDate: new Date(),
      DatePickerFormat: "yyyy年MM月dd日",
      selectedOfficeValue: "",
      selectedCustomerValue: "",
      selectedProductsValue: "",
      selectedMaterialOfficeValue: "",
      selectedMaterialCustomerValue: "",
      selectedOutsourcingOfficeValue: "",
      selectedOutsourcingCustomerValue: "",
      value_storage_sheet: "",
      value_order_no: "",
      value_drawing_no: "",
      value_order_count: "",
      value_row_seq: "",
      value_model_number: "",
      value_unit_price: "",
      value_material_cost: "",
      value_material_customer_name: "",
      value_outline_name: "",
      value_back_order_quality_name: "",
      value_heat_process: "",
      value_heat_cost: "",
      value_outsourcing_cost: "",
      value_seq: 0,
      value_back_order_customer_name: "",
      value_order_date: "",
      value_processes_code: "",
      value_back_order_product_name: "",
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
      unitpriceediting: false,
      materialcostediting: false,
      heatcostediting: false,
      outsourcingcostediting: false,
      form: {
        storage_sheet: "",
        order_no: "",
        seq: 0,
        row_seq: "",
        drawing_no: "",
        order_date: "",
        supply_date: "",
        office_code: "",
        order_kingaku: "",
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
        m_office_code: "",
        material: "",
        material_customer_code: "",
        material_customer_name: "",
        heat_process: "",
        heat_cost: "",
        o_office_code: "",
        outsourcing_customer_code: "",
        outsourcing_cost: "",
        progress_no: [{}],
        progress_name: [{}],
        product_processes_code: [{}],
        product_processes_detail_no: [{}],
        device_code: [{}],
        process_department_code: [{}],
        process_user_code: [{}],
        process_user_name: [{}],
        process_history_no: [{}],
        process_time_h: [{}],
        process_time_m: [{}],
        setup_history_no: [{}],
        setup_time_h: [{}],
        setup_time_m: [{}],
        complete_date: [{}],
        qrText: [{}],
        result_process_time_h: 0,
        result_process_time_m: 0,
        result_setup_time_h: 0,
        result_setup_time_m: 0,
        total: {
          process_total_cnt: 0,
          process_total_user_code_1: [{}],
          process_total_user_name_1: [{}],
          process_result_process_time_h_1: [{}],
          process_result_process_time_m_1: [{}],
          process_total_user_code_2: [{}],
          process_total_user_name_2: [{}],
          process_result_process_time_h_2: [{}],
          process_result_process_time_m_2: [{}],
          process_total_user_code_3: [{}],
          process_total_user_name_3: [{}],
          process_result_process_time_h_3: [{}],
          process_result_process_time_m_3: [{}],
          process_total_user_code_4: [{}],
          process_total_user_name_4: [{}],
          process_result_process_time_h_4: [{}],
          process_result_process_time_m_4: [{}],
        }
      },
      qrText:"",
      product_resresults: [],
      qroption1: {
        errorCorrectionLevel: "M",
        maskPattern: 0,
        margin: 10,
        scale: 2,
        width: 300,
        color: {
          dark: "#000000FF",
          light: "#FFFFFFFF"
        }
      },
      isQr:true,
      showofficelist: true,
      showoCustomerlist: true,
      showoProductlist: true,
      showoMaterialofficelist: true,
      showoMaterialcustomerlist: true,
      showoOutsourcingofficelist: true,
      showoOutsourcingcustomerlist: true,
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
      storeisPush: false,
      isprint_qrText: false
    };
  },
  filters: {
    localeNum: function(val) {
      return Number(val).toLocaleString('ja-JP');
    }
  },
  computed: {
    unitpricetype() {
      if (this.unitpriceediting) {
        return "number";
      } else {
        return "text";
      }
    },
    materialcosttype() {
      if (this.materialcostediting) {
        return "number";
      } else {
        return "text";
      }
    },
    heatcosttype() {
      if (this.heatcostediting) {
        return "number";
      } else {
        return "text";
      }
    },
    outsourcingcosttype() {
      if (this.outsourcingcostediting) {
        return "number";
      } else {
        return "text";
      }
    },
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
    this.valuesupplydate = this.defaultDate;
    var date = new Date();
    this.valueymd = moment(this.defaultDate).format("YYYY");
    this.year = moment(this.valueymd).format("YYYY");
    this.inputClear();
    this.messageClear();
    // this.baseYear = date.getFullYear();
    this.index_or_home = this.indexorhome;
    // 1:index 2:homeindex
    if (this.index_or_home == CONST_INDEXORHOME_HOME) {
      this.getItem();
    }
    this.getDeviceList();
    this.getUserList(this.valuesupplydate);
    this.form.supply_date = this.valuesupplydate;
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
    // 保存シートが変更された場合の処理
    storagesheetChanges: function(event) {
      this.form.storage_sheet = event.target.value;
      this.value_storage_sheet = event.target.value;
    },
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
      this.valuesupplydate = "";
      this.form.supply_date = "";
      this.stringtext = "";
    },
    // 営業所選択が変更された場合の処理
    officeChanges: function(value, arrayitem) {
      this.form.office_code = value;
      this.selectedOfficeValue = value;
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getCustomerSelected(this.form.office_code);
    },
    // 客先選択が変更された場合の処理
    customerChanges: function(value, arrayitem) {
      this.form.customer_code = value;
    },
    // 客先選択が変更された場合の処理
    backordercustomernameChanges: function(event) {
      this.form.order_no = event.target.value;
      this.value_order_no = event.target.value;
    },


    // 受注番号が変更された場合の処理
    ordernoChanges: function(event) {
      this.form.order_no = event.target.value;
      this.value_order_no = event.target.value;
      this.setQrText();

    },
    // 行が変更された場合の処理
    seqChanges: function(event) {
      this.form.row_seq = event.target.value;
      this.value_row_seq = event.target.value;
      this.setQrText();
    },
    // 図面番号が変更された場合の処理
    drawingnoChanges: function(event) {
      this.form.drawing_no = event.target.value;
      this.value_drawing_no = event.target.value;
      this.setQrText();
    },
    // 個数が変更された場合の処理
    ordercountChanges: function(event) {
      this.form.order_count = event.target.value;
      this.value_order_count = event.target.value;
    },
    // 型式／型番が変更された場合の処理
    modelnumberChanges: function(event) {
      this.form.model_number = event.target.value;
      this.value_model_number= event.target.value;
    },
    // 品名が変更された場合の処理
    productsnameChanges: function(event) {
      this.form.back_order_product_name = event.target.value;
      this.selectedProductsValue = event.target.value;
      // 工程データ読み込み工程管理書入力の2-11に設定する
      // this.getProductProcess(this.form.product_code);
    },
    // 単価が変更された場合の処理
    unitpriceChanges: function(event) {
      this.form.unit_price = event.target.value;
      this.value_unit_price = event.target.value;
    },
    // 単価がfocusinの処理
    unitpriceFocusin(event) {
      this.unitpriceediting = true;
    },
    // 単価がfocusoutの処理
    unitpriceFocusout(event) {
      this.unitpriceediting = false;
    },
    // 明細摘要が変更された場合の処理
    outlinenameChanges: function(event) {
      this.form.outline_name = event.target.value;
      this.value_outline_name = event.target.value;
    },
    // 材質・寸法が変更された場合の処理
    backorderqualitychanges: function(event) {
      this.form.back_order_quality_name = event.target.value;
      this.value_back_order_quality_name = event.target.value;
    },
    // 材料費が変更された場合の処理
    materialcostChanges: function(event) {
      this.form.material_cost = event.target.value;
      this.value_material_cost = event.target.value;
    },
    // 材料費がfocusinの処理
    materialcostFocusin(event) {
      this.materialcostediting = true;
    },
    // 材料費がfocusoutの処理
    materialcostFocusout(event) {
      this.materialcostediting = false;
    },
    // 素材納入営業所が変更された場合の処理
    materialofficeChanges: function(value, arrayitem) {
      this.form.m_office_code = value;
      console.log('materialofficeChanges this.form.m_office_code = ' + this.form.m_office_code);
      // 客先選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getMaterialCustomerSelected(this.form.m_office_code);
    },
    // 素材納入元が変更された場合の処理
    materialcustomerChanges: function(event) {
      this.form.material_customer_name = event.target.value;
      this.value_material_customer_name = event.target.value;
    },
    // 熱処理が変更された場合の処理
    heatprocesschanges: function(event) {
      this.form.heat_process = event.target.value;
      this.value_heat_process = event.target.value;
    },
    // 熱処理費が変更された場合の処理
    heatcostChanges: function(event) {
      this.form.heat_cost = event.target.value;
      this.value_heat_cost = event.target.value;
    },
    // 熱処理費がfocusinの処理
    heatcostFocusin(event) {
      this.heatcostediting = true;
    },
    // 熱処理費がfocusoutの処理
    heatcostFocusout(event) {
      this.heatcostediting = false;
    },
    // 外注先が変更された場合の処理
    outsourcingcustomerChanges: function(value, arrayitem) {
      this.form.outsourcing_customer_code = value;
      selectedOutsourcingCustomerValue = value;
    },
    // 外注費が変更された場合の処理
    outsourcingcostChanges: function(event) {
      this.form.outsourcing_cost = event.target.value;
      this.value_outsourcing_cost = event.target.value;
    },
    // 外注費がfocusinの処理
    outsourcingcostFocusin(event) {
      this.outsourcingcostediting = true;
    },
    // 外注費がfocusoutの処理
    outsourcingcostFocusout(event) {
      this.outsourcingcostediting = false;
    },
    // 機器名が変更された場合の処理
    devicecodeChanges: function(value , index) {
    },
    // 加工者が変更された場合の処理
    usercodeChanges: function(value , index) {
      console.log('usercodeChanges = value = ' + value);
      this.form.process_user_name[index] = value;
    },
    // 加工時間が変更された場合の処理
    processtimeHChanges: function() {
      this.calcTimes();
    },
    // 加工時間が変更された場合の処理
    processtimeMChanges: function() {
      this.calcTimes();
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
    
    // QRコード作成ボタンクリック処理
    qrcodeClick() {
      if (this.checkForm()) {
        this.isQr = true;
        this.setQrText();
        this.$forceUpdate();
        print_view_qr.style.display = 'block';
        //setTimeout(this.qr_print, 1000);
        this.qr_print();
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
    no_qrcodeClick() {
    if (this.print_view_rq !== 'none') {
      print_view_qr.style.display = 'none';
      }
      //this.isprint_qrText = false;
      this.qr_print();
    },
    qr_print() {
      window.print();
    },
    backClick() {
      this.isQr = true;
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
    
    // 指示書／管理書取得
    getItem() {
      this.inputClear();
      this.messageClear();
      var arrayParams = { 
        target_from_date : null ,
        target_to_date : null ,
        office_code : null ,
        customer_code : null ,
        order_no : this.order_no ,
        row_seq : this.row_seq,
        seq : this.seq
        };
      this.postRequest("/get_product_chart", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 登録処理
    storeData() {
      this.form.supply_date = moment(this.valuesupplydate).format("YYYYMMDD");
      console.log('storeData this.form.processes_code = ' + this.form.processes_code);
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
    // 営業所選択コンポーネント取得メソッド
    getOfficeSelected: function(value) {
      this.$refs.selectofficelist.getList(value);
    },
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
      if (res.result) {
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
          console.log('getThenprocess this.form.processes_code = ' + this.form.processes_code);
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
        this.users_details = res.users_details;
        this.count = this.details.length;
        this.before_count = this.count;
        if ( this.details.length > 0) {
          this.form.supply_date = this.details[0]['supply_date'];
          this.form.office_code = this.details[0]['office_code'];
          this.form.customer_code = this.details[0]['customer_code'];
          this.form.back_order_product_name = this.details[0]['back_order_product_name'];
          this.form.order_no = this.details[0]['order_no'];
          this.form.seq = this.details[0]['seq'];
          this.form.order_date = this.details[0]['order_date'];
          this.form.order_kingaku = this.details[0]['order_kingaku'];
          this.form.processes_code = this.details[0]['processes_code'];
          this.form.back_order_customer_name = this.details[0]['back_order_customer_name'];
          this.form.drawing_no = this.details[0]['drawing_no'];
          this.form.order_count = this.details[0]['order_count'];
          this.form.row_seq = this.details[0]['row_seq'];
          this.form.model_number = this.details[0]['model_number'];
          this.form.unit_price = this.details[0]['unit_price'];
          this.form.m_office_code = this.details[0]['material_office_code'];
          this.form.material_cost = this.details[0]['material_cost'];
          this.form.material_customer_code = this.details[0]['material_customer_code'];
          this.form.material_customer_name = this.details[0]['material_customer_name'];
          this.form.outline_name = this.details[0]['outline_name'];
          this.form.back_order_quality_name = this.details[0]['back_order_quality_name'];
          this.form.heat_process = this.details[0]['heat_process'];
          this.form.heat_cost = this.details[0]['heat_cost'];
          this.form.o_office_code = this.details[0]['outsourcing_office_code'];
          this.form.outsourcing_cost = this.details[0]['outsourcing_cost'];
          this.form.outsourcing_customer_code = this.details[0]['outsourcing_customer_code'];

          this.result_process_time_h = 0;
          this.result_process_time_m = 0;
          this.details.forEach((detail, i) => {
            if (detail.progress_no != null) {
              this.form.progress_no[i] = detail.progress_no.toString();
            } else {
              this.form.progress_no[i] = "";
            }
            if (detail.progress_name != null) {
              this.form.progress_name[i] = detail.progress_name;
            } else {
              this.form.progress_name[i] = "";
            }
            if (detail.product_processes_code != null) {
              this.form.product_processes_code[i] = detail.product_processes_code;
            } else {
              this.form.product_processes_code[i] = "";
            }
            if (detail.product_processes_detail_no != null) {
              this.form.product_processes_detail_no[i] = detail.product_processes_detail_no.toString();
            } else {
              this.form.product_processes_detail_no[i] = "";
            }
            if (detail.device_code != null) {
              this.form.device_code[i] = detail.device_code;
            } else {
              this.form.device_code[i] = "";
            }
            if (detail.process_department_code != null) {
              this.form.process_department_code[i] = detail.process_department_code;
            } else {
              this.form.process_department_code[i] = "";
            }
            if (detail.users_code != null) {
              this.form.process_user_code[i] = detail.users_code;
            } else {
              this.form.process_user_code[i] = "";
            }
            if (detail.user_name != null) {
              this.form.process_user_name[i] = detail.user_name;
            } else {
              this.form.process_user_name[i] = "";
            }
            
            if (detail.process_history_no != null) {
              this.form.process_history_no[i] = detail.process_history_no;
            } else {
              this.form.process_history_no[i] = "";
            }
            if (detail.process_time_h != null) {
              this.form.process_time_h[i] = detail.process_time_h;
            } else {
              this.form.process_time_h[i] = "";
            }
            if (detail.process_time_m != null) {
              this.form.process_time_m[i] = detail.process_time_m;
            } else {
              this.form.process_time_m[i] = "";
            }
            if (detail.setup_history_no != null) {
              this.form.setup_history_no[i] = detail.setup_history_no.toString();
            } else {
              this.form.setup_history_no[i] = "";
            }
            if (detail.setup_time_h != null) {
              this.form.setup_time_h[i] = detail.setup_time_h.toString();
            } else {
              this.form.setup_time_h[i] = "";
            }
            if (detail.setup_time_m != null) {
              this.form.setup_time_m[i] = detail.setup_time_m.toString();
            } else {
              this.form.setup_time_m[i] = "";
            }
            if (detail.complete_date != null) {
              this.form.complete_date[i] = moment(detail.complete_date).format("YYYY-MM-DD");;
            } else {
              this.form.complete_date[i] = "";
            }
            if (detail.qrText != null) {
              this.form.qrText[i] = detail.qrText;
            } else {
              this.form.qrText[i] = "";
            }
          });
        }
        if ( this.users_details.length > 0) {
          this.users_details.forEach((detail, index) => {
            if (index < 4) {
              this.form.total.process_total_user_code_1[index] = detail.code;
              this.form.total.process_total_user_name_1[index] = detail.name;
            } else {
              if (index < 8) {
                this.form.total.process_total_user_code_2[index - 4] = detail.code;
                this.form.total.process_total_user_name_2[index - 4] = detail.name;
              } else {
                if (index < 12) {
                  this.form.total.process_total_user_code_3[index - 8] = detail.code;
                  this.form.total.process_total_user_name_3[index - 8] = detail.name;
                } else {
                  this.form.total.process_total_user_code_4[index - 12] = detail.code;
                  this.form.total.process_total_user_name_4[index - 12] = detail.name;
                }
              }
            }
          });
        }
        this.calcTimes();
        this.setValue();
        this.refresOfficeList();
        this.refresCustomerlist();
        this.refresProductlist();
        this.refresMaterialofficelist();
        this.refresMaterialcustomerlist();
        this.refresOutsourcingofficelist();
        this.refresOutsourcingcustomerlist();
        this.setQrText();
        this.$forceUpdate();
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
        this.getItem();
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
    // value値設定
    setValue() {
      this.valuesupplydate = moment(this.form.supply_date).format("YYYY-MM-DD");
      this.selectedOfficeValue = this.form.office_code;
      this.selectedCustomerValue = this.form.customer_code;
      this.selectedProductsValue = this.form.back_order_product_name; // 受注残品名に変更
      this.selectedMaterialOfficeValue = this.form.m_office_code;
      this.selectedMaterialCustomerValue = this.form.material_customer_code;
      this.value_material_customer_name = this.form.material_customer_name;
      this.selectedOutsourcingOfficeValue = this.form.o_office_code;
      this.selectedOutsourcingCustomerValue = this.form.outsourcing_customer_code;
      this.value_order_no = this.form.order_no;
      this.value_seq = this.form.seq;
      this.value_back_order_customer_name = this.form.back_order_customer_name;
      this.value_order_date = this.form.order_date;
      this.value_processes_code = this.form.processes_code;
      this.value_back_order_product_name = this.form.back_order_product_name;
      this.value_drawing_no = this.form.drawing_no;
      this.value_order_count = this.form.order_count;
      this.value_row_seq = this.form.row_seq;
      this.value_model_number = this.form.model_number;
      this.value_unit_price = this.form.unit_price;
      this.value_material_cost = this.form.material_cost;
      this.value_outline_name = this.form.outline_name;
      this.value_back_order_quality_name = this.form.back_order_quality_name;
      this.value_heat_process = this.form.heat_process;
      this.value_heat_cost = this.form.heat_cost;
      this.value_outsourcing_cost = this.form.outsourcing_cost;
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
      this.form.seq = 0;
      this.form.back_order_customer_name = "";
      this.form.back_order_product_name = "";
      this.form.order_date = "";
      this.form.order_kingaku = "";
      this.form.drawing_no = "";
      this.form.order_count = "";
      this.form.model_number = "";
      this.form.product_code = "";
      this.form.processes_code = "";
      this.form.unit_price = "";
      this.form.outline_name = "";
      this.form.back_order_quality_name = "";
      this.form.material_cost = "";
      this.form.m_office_code = "";
      this.form.material_customer_code = "";
      this.form.material_customer_name = "";
      this.form.heat_process = "";
      this.form.heat_cost = "";
      this.form.o_office_code = "";
      this.form.outsourcing_customer_code = "";
      this.form.outsourcing_cost = "";
      for (let index = 0; index < 12; index++) {
        this.form.progress_no[index] = index + 1;
        this.form.progress_name[index] = "";
        this.form.product_processes_code[index] = "";
        this.form.product_processes_detail_no[index] = 0;
        this.form.device_code[index] = "";
        this.form.process_user_code[index] = "";
        this.form.process_user_name[index] = "";
        this.form.process_department_code[index] = "";
        this.form.process_history_no[index] = "";
        this.form.setup_history_no[index] = "";
        this.form.process_time_h[index] = "";
        this.form.process_time_m[index] = "";
        this.form.setup_time_h[index] = "";
        this.form.setup_time_m[index] = "";
        this.form.complete_date[index] = "";
        this.form.qrText[index] = null;
      }

      this.form.total.process_total_cnt = 0;
      for (let index = 0; index < 4; index++) {
        this.form.total.process_total_user_code_1[index] = "";
        this.form.total.process_total_user_name_1[index] = "";
        this.form.total.process_result_process_time_h_1[index] = 0;
        this.form.total.process_result_process_time_m_1[index] = 0;
        this.form.total.process_total_user_code_2[index] = "";
        this.form.total.process_total_user_name_2[index] = "";
        this.form.total.process_result_process_time_h_2[index] = 0;
        this.form.total.process_result_process_time_m_2[index] = 0;
        this.form.total.process_total_user_code_3[index] = "";
        this.form.total.process_total_user_name_3[index] = "";
        this.form.total.process_result_process_time_h_3[index] = 0;
        this.form.total.process_result_process_time_m_3[index] = 0;
        this.form.total.process_total_user_code_4[index] = "";
        this.form.total.process_total_user_name_4[index] = "";
        this.form.total.process_result_process_time_h_4[index] = 0;
        this.form.total.process_result_process_time_m_4[index] = 0;
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
      if (this.form.seq == 0) {
        this.qrText = 'A' + "order_no='" + this.form.order_no + "'&seq='1'";
      } else {
        this.qrText = 'A' + "order_no='" + this.form.order_no + "'&seq='" + this.form.seq + "'";
      }
      console.log('setQrText this.qrText = ' + this.form.order_no);
      console.log('setQrText this.qrText = ' + this.qrText);
      // for (let index = 0; index < 12; index++) {
        // this.form.qrText[index] = this.form.order_no + this.form.row_seq + this.form.drawing_no + ('00' + (index+1)).slice(-2);
      // }
      this.$forceUpdate();
    },
    // 実績値合計時間算出
    calcTimes() {
      var temp_calc_h = 0;
      this.form.result_process_time_h = 0;
      this.form.result_process_time_m = 0;
      for (let index = 0; index < 12; index++) {
        if (this.form.process_time_h[index] != "" && this.form.process_time_h[index] != null) {
          this.form.result_process_time_h += Number(this.form.process_time_h[index]);
        }
        if (this.form.process_time_m[index] != "" && this.form.process_time_m[index] != null) {
          this.form.result_process_time_m += Number(this.form.process_time_m[index]);
        }
      }
      if (this.form.result_process_time_m > 59) {
        temp_calc_h = Math.floor(this.form.result_process_time_m / 60);
        this.form.result_process_time_h += temp_calc_h;
        this.form.result_process_time_m = this.form.result_process_time_m - (temp_calc_h * 60);
      }
      this.form.total.process_total_cnt = 0;
      var isSet = false;
      for (let index1 = 0; index1 < 4; index1++) {
        this.form.total.process_result_process_time_h_1[index1] = 0;
        this.form.total.process_result_process_time_m_1[index1] = 0;
        this.form.total.process_result_process_time_h_2[index1] = 0;
        this.form.total.process_result_process_time_m_2[index1] = 0;
        this.form.total.process_result_process_time_h_3[index1] = 0;
        this.form.total.process_result_process_time_m_3[index1] = 0;
        this.form.total.process_result_process_time_h_4[index1] = 0;
        this.form.total.process_result_process_time_m_4[index1] = 0;
      }
      for (let index1 = 0; index1 < 12; index1++) {
        isSet = false;
        if (this.form.process_user_name[index1] != "") {
          console.log('calcTimes process_user_name ' + this.form.process_user_name[index1]);
          for (let index2 = 0; index2 < 4; index2++) {
            if (!isSet) {
              console.log('calcTimes this.form.total.process_total_user_name_1[index2] ' + this.form.total.process_total_user_name_1[index2]);
              if (this.form.total.process_total_user_name_1[index2] != "") {
                if (this.form.total.process_total_user_name_1[index2] == this.form.process_user_name[index1]) {
                  this.form.total.process_result_process_time_h_1[index2] += Number(this.form.process_time_h[index1]);
                  this.form.total.process_result_process_time_m_1[index2] += Number(this.form.process_time_m[index1]);
                  isSet = true;
                }
              }
            }
            if (!isSet) {
              console.log('calcTimes this.form.total.process_total_user_name_2[index2] ' + this.form.total.process_total_user_name_2[index2]);
              if (this.form.total.process_total_user_name_2[index2] != "") {
                if (this.form.total.process_total_user_name_2[index2] == this.form.process_user_name[index1]) {
                  this.form.total.process_result_process_time_h_2[index2] += Number(this.form.process_time_h[index1]);
                  this.form.total.process_result_process_time_m_2[index2] += Number(this.form.process_time_m[index1]);
                  isSet = true;
                }
              }
            }
            if (!isSet) {
              if (this.form.total.process_total_user_name_3[index2] != "") {
                if (this.form.total.process_total_user_name_3[index2] == this.form.process_user_name[index1]) {
                  this.form.total.process_result_process_time_h_3[index2] += Number(this.form.process_time_h[index1]);
                  this.form.total.process_result_process_time_m_3[index2] += Number(this.form.process_time_m[index1]);
                  isSet = true;
                }
              }
            }
            if (!isSet) {
              if (this.form.total.process_total_user_name_4[index2] != "") {
                if (this.form.total.process_total_user_name_4[index2] == this.form.process_user_name[index1]) {
                  this.form.total.process_result_process_time_h_4[index2] += Number(this.form.process_time_h[index1]);
                  this.form.total.process_result_process_time_m_4[index2] += Number(this.form.process_time_m[index1]);
                  isSet = true;
                }
              }
            }
          }
          if (!isSet) {
            console.log('calcTimes this.form.total.process_total_cnt ' + this.form.total.process_total_cnt);
            if (this.form.total.process_total_cnt < 4) {
              if (this.form.process_user_name[index1] != null && this.form.process_user_name[index1] != "") {
                this.form.total.process_total_user_name_1[this.form.total.process_total_cnt] = this.form.process_user_name[index1];
                this.form.total.process_result_process_time_h_1[this.form.total.process_total_cnt] += Number(this.form.process_time_h[index1]);
                this.form.total.process_result_process_time_m_1[this.form.total.process_total_cnt] += Number(this.form.process_time_m[index1]);
              }
              this.form.total.process_total_cnt +=1;
            } else {
              if (this.form.total.process_total_cnt < 8) {
                console.log('calcTimes this.form.process_user_name[index1] ' + this.form.process_user_name[index1]);
                if (this.form.process_user_name[index1] != null && this.form.process_user_name[index1] != "") {
                  this.form.total.process_total_user_name_2[this.form.total.process_total_cnt - 4] = this.form.process_user_name[index1];
                  this.form.total.process_result_process_time_h_2[this.form.total.process_total_cnt - 4] += Number(this.form.process_time_h[index1]);
                  this.form.total.process_result_process_time_m_2[this.form.total.process_total_cnt - 4] += Number(this.form.process_time_m[index1]);
                  console.log('calcTimes this.form.total.process_total_user_name_2[this.form.total.process_total_cnt - 4] ' + this.form.total.process_total_user_name_2[this.form.total.process_total_cnt - 4]);
                }
                this.form.total.process_total_cnt +=1;
              } else {
                if (this.form.total.process_total_cnt < 12) {
                  if (this.form.process_user_name[index1] != null && this.form.process_user_name[index1] != "") {
                    this.form.total.process_total_user_name_3[this.form.total.process_total_cnt - 8] = this.form.process_user_name[index1];
                    this.form.total.process_result_process_time_h_3[this.form.total.process_total_cnt - 8] += Number(this.form.process_time_h[index1]);
                    this.form.total.process_result_process_time_m_3[this.form.total.process_total_cnt - 8] += Number(this.form.process_time_m[index1]);
                  }
                  this.form.total.process_total_cnt +=1;
                } else {
                  if (this.form.total.process_total_cnt < 16) {
                    if (this.form.process_user_name[index1] != null && this.form.process_user_name[index1] != "") {
                      this.form.total.process_total_user_name_4[this.form.total.process_total_cnt - 12] = this.form.process_user_name[index1];
                      this.form.total.process_result_process_time_h_4[this.form.total.process_total_cnt - 12] += Number(this.form.process_time_h[index1]);
                      this.form.total.process_result_process_time_m_4[this.form.total.process_total_cnt - 12] += Number(this.form.process_time_m[index1]);
                    }
                    this.form.total.process_total_cnt +=1;
                  }
                }
              }
            }
          }
        }
      }
      for (let index1 = 0; index1 < 4; index1++) {
        if (this.form.total.process_result_process_time_m_1[index1] > 59) {
          temp_calc_h = Math.floor(this.form.total.process_result_process_time_m_1[index1] / 60);
          this.form.total.process_result_process_time_h_1[index1] += temp_calc_h;
          this.form.total.process_result_process_time_m_1[index1] = this.form.total.process_result_process_time_m_1[index1] - (temp_calc_h * 60);
        }
        if (this.form.total.process_result_process_time_m_2[index1] > 59) {
          temp_calc_h = Math.floor(this.form.total.process_result_process_time_m_2[index1] / 60);
          this.form.total.process_result_process_time_h_2[index1] += temp_calc_h;
          this.form.total.process_result_process_time_m_2[index1] = this.form.total.process_result_process_time_m_2[index1] - (temp_calc_h * 60);
        }
        if (this.form.total.process_result_process_time_m_3[index1] > 59) {
          temp_calc_h = Math.floor(this.form.total.process_result_process_time_m_3[index1] / 60);
          this.form.total.process_result_process_time_h_3[index1] += temp_calc_h;
          this.form.total.process_result_process_time_m_3[index1] = this.form.total.process_result_process_time_m_3[index1] - (temp_calc_h * 60);
        }
        if (this.form.total.process_result_process_time_m_4[index1] > 59) {
          temp_calc_h = Math.floor(this.form.total.process_result_process_time_m_4[index1] / 60);
          this.form.total.process_result_process_time_h_4[index1] += temp_calc_h;
          this.form.total.process_result_process_time_m_4[index1] = this.form.total.process_result_process_time_m_4[index1] - (temp_calc_h * 60);
        }
      }
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
      console.log('setProductProcessTable this.form.processes_code = ' + this.form.processes_code);
      this.$forceUpdate();
    },
    // 最新リストの表示
    refresOfficeList() {
      this.showofficelist = false;
      this.$nextTick(() => (this.showofficelist = true));
    },
    refresCustomerlist() {
      this.showoCustomerlist = false;
      this.$nextTick(() => (this.showoCustomerlist = true));
    },
    refresProductlist() {
      this.showoProductlist = false;
      this.$nextTick(() => (this.showoProductlist = true));
    },
    refresMaterialofficelist() {
      this.showoMaterialofficelist = false;
      this.$nextTick(() => (this.showoMaterialofficelist = true));
    },
    refresMaterialcustomerlist() {
      this.showoMaterialcustomerlist = false;
      this.$nextTick(() => (this.showoMaterialcustomerlist = true));
    },
    refresOutsourcingofficelist() {
      this.showoOutsourcingofficelist = false;
      this.$nextTick(() => (this.showoOutsourcingofficelist = true));
    },
    refresOutsourcingcustomerlist() {
      this.showoOutsourcingcustomerlist = false;
      this.$nextTick(() => (this.showoOutsourcingcustomerlist = true));
    },
  }
}

</script>
<style scoped>
.mw-rem-2 {
    min-width: 2rem;
}
</style>
