<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'顧客情報を設定する'"
            v-bind:header-text2="'顧客情報の登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div id="input-area_3" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >営業所</span>
                  </div>
                  <select-officelist
                    ref="selectofficelist"
                    v-if="showofficelist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'営業所を選択してください'"
                    v-bind:selected-value="selectedOfficeValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:row-index="0"
                    v-on:change-event="officeChanges"
                  ></select-officelist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >客先</label>
                  </div>
                  <select-customerlist
                    ref="selectcustomerlist"
                    v-if="showoCustomerlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'客先を選択すると編集モードになります'"
                    v-bind:selected-value="selectedCustomerValue"
                    v-bind:add-new="true"
                    v-bind:row-index="0"
                    v-bind:get-do="getDo"
                    v-bind:date-value="get_NewDateYMD"
                    v-bind:office-value="selectedOfficeValue"
                    v-on:change-event="customerChanges"
                  ></select-customerlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
          </div>
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- ----------- ボタン部 END ---------------- -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
    </div>
    <div class="row justify-content-between">
      <!-- ========================== 新規部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode==='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆ユーザー情報設定'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 新規入力部 START ---------------- -->
          <!-- panel contents -->
          <div id="input-area_3" class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesNew.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li
                    v-for="(messagevalidate,index) in messagevalidatesNew"
                    v-bind:key="index"
                  >{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->


            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      営業所
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <select-officelist
                    v-if="showaddofficelist"
                    ref="addselectofficelist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'営業所を選択してください'"
                    v-bind:selected-value="form.office_code"
                    v-on:change-event="addofficeChanges"
                  ></select-officelist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->



            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      客先・顧客
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'ユーザーの氏名を191文字以内で入力します'"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->












            <!-- ----------- 項目部 END ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div id="btn_cnt6" class="row justify-content-between">
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
            <!-- ----------- ボタン部 END ---------------- -->
          </div>
          <!-- /.panel contents -->
          <!-- ----------- 新規入力部 END ---------------- -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 新規部 END ========================== -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl" v-if="details.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆　' + searchedUserName"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button
                  class="btn btn-success btn-lg font-size-rg"
                  v-on:click="appendRowClick"
                >＋新規履歴追加</button>
              </span>
            </h1>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 END ---------------- -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li
                    v-for="(messagevalidate,index) in messagevalidatesEdt"
                    v-bind:key="index"
                  >{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- panel contents -->
            <div v-for="(item,index) in details" v-bind:key="item.id">
              <div class="card shadow-pl">
                <div class="card-body pt-3">
                  <!-- item.result  1: 現在適用中 -->
                  <!-- item.result  2: 未来適用予定または -->
                  <!-- 現在適用中 ----------------------------------------------------------------->
                  <div v-if="item.result != ''">
                    <!-- .row -->
                    <div class="row justify-content-between" v-if="item.result == 1">
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1) + ' 現在適用中'"
                          v-bind:item-control="'INFO'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <div class="row justify-content-between" v-else>
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1)"
                          v-bind:item-control="'LIGHT'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <!-- /.row -->



                    <!-- .row -->
                    <div id="input-area_3" class="row justify-content-between">



                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              営業所
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select class="custom-select" v-model="item.office_code">
                            <option value></option>
                            <option
                              v-for="dlist in officeList"
                              :value="dlist.code"
                              v-bind:key="dlist.code"
                            >{{ dlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->


                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              客先
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.name"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'客先を191文字以内で入力します'"
                            name="name"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /row -->
                    <!-- .row -->
                    <!-- ----------- ボタン部 START ---------------- -->
                    <div id="btn_cnt6" class="row justify-content-between">
                      <div class="col-md-12 pb-2">
                        <div class="btn-group float-left">
                          <button
                            v-if="item.result != 0 && item.id != ''"
                            type="button"
                            class="btn btn-success"
                            @click="fixclick(index)"
                          >この内容で更新する</button>
                          <button
                            v-if="item.id == ''"
                            type="button"
                            class="btn btn-success"
                            @click="addClick(index)"
                          >この内容で追加する</button>
                          <button
                            v-if="item.result != 0 && item.id != ''"
                            type="button"
                            class="btn btn-danger"
                            @click="delClick(index)"
                          >この内容を削除する</button>
                        </div>
                      </div>
                    </div>
                    <!-- /.row -->
                    <!-- ----------- ボタン部 END ---------------- -->
                  </div>
                  <!-- 現在適用中より過去 ----------------------------------------------------------------->
                  <div v-else>
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1)"
                          v-bind:item-control="'LIGHT'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <!-- /row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- panel contents -->
                      <!-- panel header -->
                      <daily-working-information-panel-header
                        v-bind:header-text1="'◆ユーザー'"
                        v-bind:header-text2="''"
                        v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                      ></daily-working-information-panel-header>
                      <!-- /.panel header -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >適用開始日</span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.apply_term_from"
                            name="applytermfrom"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >退職日</span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.kill_from_date"
                            name="killfromdate"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >氏名</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.name"
                            name="name"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >フリガナ</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="30"
                            v-model="item.kana"
                            name="kana"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >雇用形態</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.employment_status">
                            <option value></option>
                            <option
                              v-for="elist in get_C001"
                              :value="elist.code"
                              v-bind:key="elist.code"
                            >{{ elist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >役職</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.official_position"
                            name="officialposition"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >所属部署</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.department_code">
                            <option value></option>
                            <option
                              v-for="dlist in departmentList"
                              :value="dlist.code"
                              v-bind:key="dlist.code"
                            >{{ dlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >メールアドレス</span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.email"
                            name="email"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >ログインID</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            v-model="item.code"
                            name="code"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >モバイル用アドレス</span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.mobile_email"
                            name="mobile_email"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >パスワード</span>
                          </div>
                          <input
                            type="password"
                            class="form-control"
                            v-model="item.password"
                            name="password"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >勤怠管理</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.management">
                            <option value></option>
                            <option
                              v-for="mlist in get_C017"
                              :value="mlist.code"
                              v-bind:key="mlist.code"
                            >{{ mlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >タイムテーブル</span>
                          </div>
                          <select
                            disabled
                            class="custom-select"
                            v-model="item.working_timetable_no"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'「勤務時間設定」で登録したタイムテーブルのリストから選択します。'"
                          >
                            <option value></option>
                            <option
                              v-for="tlist in timetableList"
                              :value="tlist.no"
                              v-bind:key="tlist.no"
                            >{{ tlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >権限</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.role">
                            <option value></option>
                            <option
                              v-for="rlist in get_C025"
                              :value="rlist.code"
                              v-bind:key="rlist.code"
                            >{{ rlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /row -->
                  </div>
                </div>
              </div>
              <!-- /panel contents -->
            </div>
            <!-- ----------- 項目部 END ---------------- -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
        </div>
        <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
      </div>
    </div>
    <!-- /.panel -->
    <el-dialog
      v-bind:title="' モバイル打刻用URL送信'"
      :visible.sync="dialogVisible"
      width="50%"
      center="true"
    >
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">送信するモバイルのメールアドレスを入力してください</h5>
          <h5 class="card-subtitle mb-2">{{ form.code }}</h5>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text font-size-sm line-height-xs label-width-150">email</span>
            </div>
            <input
              type="email"
              class="form-control"
              v-model="input_mobile_address"
              maxlength="191"
              name="input_mobile_address"
            />
          </div>
          <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        <div class="card-footer">
          <button class="btn btn-warning" @click="sendUrl()">送信する</button>
        </div>
      </div>
    </el-dialog>
  </div>
  <!-- /main contentns row -->
</template>
<script>
import moment from "moment";
import encoding from "encoding-japanese";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

// CONST
const CONST_C001 = 'C001';
const CONST_C017 = 'C017';
const CONST_C025 = 'C025';
const CONST_C037 = 'C037';
const CONST_C041 = 'C041';
const CONST_TIMETABLE_EQUALITY = 0;       // タイムテーブル設定方法一律C041 index
const CONST_TIMETABLE_WEEK = 1;           // タイムテーブル設定方法WEEKC041 index
const CONST_USER_DOWNLOAD = '4';          // ユーザー情報ダウンロードのC037 code
const CONST_KBNNAME_ADD = "追加"
const CONST_KBNNAME_UPD = "更新"
const CONST_KBNNAME_DEL = "削除"
const CONST_KBNNAME_REL = "解除"
const CONST_KBNNAME_REG = "登録"
const CONST_KBNNAME_UPL = "アップロード登録"
const CONST_KBNNAME_CON = "確認"

export default {
  name: "EditUser",
  mixins: [dialogable, checkable, requestable],
  props: {
    authusers: {
      type: Array,
      default: []
    },
    isexistdownload: {
      type: String,
      default: ""
    },
    settingcompanies: {
      type: String,
      default: ""
    },
    settingdepartments: {
      type: String,
      default: ""
    },
    settingsettings: {
      type: String,
      default: ""
    },
    settingworkingtimetables: {
      type: String,
      default: ""
    },
    settingcalendarsettinginformations: {
      type: String,
      default: ""
    },
    settingusers: {
      type: String,
      default: ""
    },
    const_generaldatas: {
        type: Array,
        default: []
    }
  },
  computed: {
    get_C001: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C001) {
          $this.const_C001_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C001_data;
    },
    get_C017: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C017) {
          $this.const_C017_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C017_data;
    },
    get_C025: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C025) {
          $this.const_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C025_data;
    },
    get_C037: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C037) {
          $this.const_C037_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.const_C037_data;
    },
    get_C037TARGET: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C037) {
          if (item.code == CONST_USER_DOWNLOAD) {
            $this.const_C037_data_target.push($this.const_generaldatas[i]);
          }
        }
        i++;
      });    
      return this.const_C037_data_target;
    },
    get_C041: function() {
      let $this = this;
      var i = 0;
      var array_set = [{}];
      var check_value = false;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C041) {
          array_set = {
            key: $this.const_generaldatas[i]['code'],
            value: $this.const_generaldatas[i]['code'],
            label: $this.const_generaldatas[i]['code_name'],
            checked: check_value
          };
          $this.const_C041_data.push(array_set);
          // check_value = false;
        }
        i++;
      });    
      return this.const_C041_data;
    },
    get_NewDateY_M_D: function() {
      return moment(new Date()).format("YYYY-MM-DD");
    },
    get_NewDateYMD: function() {
      return moment(new Date()).format("YYYYMMDD");
    },
    get_FormApplyNewDate: function() {
      if (this.form.apply_term_from == "") {
        this.form.apply_term_from = this.get_NewDateY_M_D;
      }
      return this.form.apply_term_from;
    }
  },
  data() {
    return {
      const_C001_data: [],
      const_C017_data: [],
      const_C025_data: [],
      const_C037_data: [],
      const_C037_data_target: [],
      const_C041_data: [],
      timetable_check: {
        chkptn : 1
      },
      isTabledisabled : [false, true],
      formweekdays: [
        '月曜日',
        '火曜日',
        '水曜日',
        '木曜日',
        '金曜日',
        '土曜日',
        '日曜日'
      ],
      timetable: {
        timeptn : 1,
        timeptn_timetable : "",
        timeptn_timetable_w : ["","","","","","",""]
      },
      showofficelist: true,
      showoCustomerlist: true,
      selectedOfficeValue: "",
      selectedCustomerValue: "",
      selectedCustomerName: "",

      valueDepartmentkillcheck: false,
      showdepartmentlist: true,
      showadddepartmentlist: true,
      selectedUserValue: "",
      selectedUserName: "",
      
      valueUserkillcheck: false,
      isUsermanagement: true,
      showuserlist: true,
      selectedEmploymentValue: "",
      valueTimeTablekillcheck: false,
      selectMode: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      messagevalidatestimetable: [],

      searchedCustomerValue: "",
      searchedCustomerName: "",
      searchedOfficeValue: "",


      searchedUserValue: "",
      searchedUserName: "",
      searchedDepartmentValue: "",
      showrelease: true,
      details: [],
      before_details: [],
      usersups: [],
      form: {
        id: "",
        office_code: "",
        code: "",
        name: "",
      },
      count: 0,
      before_count: 0,
      getDo: 1,
      departmentList: [],
      timetableList: [],
      cardId: "",
      mobile_address: "",
      dialogVisible: false,
      latest_user_code: "",
      messageshowsearch: false,
      input_mobile_address: "",
      iscsvbutton: false,
      valuefromdate: "",
      valuetodate: "",
      DatePickerFormat: "yyyy年MM月dd日",
      infoMsgcnt: 0
    };
  },
  // マウント時
  mounted() {
    //this.getDepartmentList("");
    this.getCustomerList("");
    //this.getTimeTableList("");
  },
  created() {
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 顧客
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = "顧客名";
      chkArray = this.checkHeader(
        this.form.name,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 顧客CODE
      required = true;
      equalength = 0;
      maxlength = 10;
      itemname = "ログインＩＤ";
      chkArray = this.checkHeader(
        this.form.code,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function(index) {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;

      // 顧客名
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = "顧客名";
      chkArray = this.checkDetail(
        this.details[index].name,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }

      // 営業所コード
      required = true;
      equalength = 0;
      maxlength = 16;
      itemname = "営業所コード";
      chkArray = this.checkDetail(
        this.details[index].office_code,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }






      // ログインＩＤ
      required = true;
      equalength = 0;
      maxlength = 10;
      itemname = "ログインＩＤ";
      chkArray = this.checkDetail(
        this.details[index].code,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }

      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
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
      this.form.code = value;
      this.selectedCustomerValue = value;
      this.selectedCustomerName = arrayitem["name"];
      console.log('customerChanges_selectedCustomerValue = ' + this.selectedCustomerValue);
      this.searchclick();

    },
    // 客先選択が変更された場合の処理
    backordercustomernameChanges: function(event) {
      this.form.order_no = event.target.value;
      this.value_order_no = event.target.value;
    },








    // 新規作成部署選択が変更された場合の処理
    adddepartmentChanges: function(value, arrayitem) {
      this.form.department_code = value;
    },
    // 新規作成雇用形態選択が変更された場合の処理
    addemploymentChanges: function(value, arrayitem) {
      this.form.employment_status = value;
    },
    // 新規作成ログインＩＤが変更された場合の処理
    addcodeChange: function() {
      this.form.password = this.form.code;
    },
    // 新規作成勤怠管理が変更された場合の処理
    adddisplayChange: function(value, arrayitem) {
      this.form.management = value;
    },
    // 新規作成タイムテーブルが変更された場合の処理
    addtimetableChanges: function(value, arrayitem) {
      this.form.working_timetable_no = value;
    },
    // 新規作成権限が変更された場合の処理
    addroleChange: function(value, arrayitem) {
      this.form.role = value;
    },
    // 選択が変更された場合の処理
    timetablechkptnChanges: function(value, index) {
      if (index == 0) {
        this.isTabledisabled[0] = false;
        this.isTabledisabled[1] = true;
      } else {
        this.isTabledisabled[0] = true;
        this.isTabledisabled[1] = false;
      }
    },
    // 表示するボタンクリック処理
    searchclick: function() {
      // 入力項目のクリア
      this.selectMode = "";
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];



//      this.searchedUserValue = this.selectedCustomerValue;
//      this.searchedUserName = this.selectedCustomerName;
      this.searchedCustomerValue = this.selectedCustomerValue;
      this.searchedCustomerName = this.selectedCustomerName;

//      this.searchedDepartmentValue = this.selectedOfficeValue;
      this.searchedOfficeValue = this.selectedOfficeValue;

        console.log('selectedCustomerValue = ' + this.selectedCustomerValue);


      if (this.selectedCustomerValue == "" || this.selectedCustomerValue == null) {
        this.selectMode = 'NEW';
        this.newItemClear();
        this.form.office_code = this.searchedOfficeValue;
        console.log('selectmode = ' + this.selectMode);
        console.log('office_code = ' + this.searchedOfficeValue);
        this.refresOfficeList();
      } else {
        this.selectMode = 'EDT';
        this.getItem();
      }
    },
    // 開始日付が変更された場合の処理
    fromdateChanges: function(value) {
      this.valuefromdate = value;
    },
    // 開始日付がクリアされた場合の処理
    fromdateCleared: function() {
      this.valuefromdate = "";
    },
    // 終了日付が変更された場合の処理
    fromtoChanges: function(value, arrayitem) {
      this.valuetodate = value;
    },
    // 終了日付がクリアされた場合の処理
    fromtoCleared: function() {
      this.valuetodate = "";
    },
    
    // CSVから作成するボタンクリック処理
    usersuploadclick: function() {
      this.selectMode = 'UPL';
    },
    // アップロードボタンがクリックされた場合の処理
    upclick: function(e) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var messages = [];
      messages.push("登録しているユーザー情報をクリアしてから登録します。");
      messages.push("既存データは消去されますが、よろしいですか？");
      this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
        result => {
          if (result) {
            var obj = document.getElementById("f1");
            obj.value = "";
            this.$refs.uplog.click(); // 同じファイルだとイベントが走らない
          }
        }
      );
    },
    // ファイル選択が変更された場合の処理
    onFileChange: function(e) {
      var isResult = this.handleFileSelect(e);
      if (!isResult) {
        var messages = [];
        messages.push("アップロードするファイルの内容が誤っています。");
        messages.push("確認してください。");
        this.htmlMessageSwal("エラー", messages, "error", true, false);
      }
    },
    // 新規作成ボタンクリック処理
    storeclick() {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var flag = this.checkFormStore();
      this.form.role = 0;
      if (flag) {
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
          this.messagevalidatesNew,
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
    // 更新ボタンクリック処理
    fixclick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var flag = this.checkFormFix(index);
      var msgCnt = 0;
      if (flag) {
        var messages = [];
        var item_name = this.jdgSearchItemInput();
        if (item_name != null) {
          msgCnt += 1;
          messages.push(this.toWide(String(msgCnt)) + ". " + item_name + "が変更されていますが、");
          messages.push("変更前の条件で更新します。");
        }
        if (messages.length == 0) {
          messages.push("この内容で更新しますか？");
        } else {
          messages.push("更新してよろしいですか？");
        }
        this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
          result => {
            if (result) {
              this.FixDetail(CONST_KBNNAME_UPD, index);
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagevalidatesEdt,
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
    // 追加ボタンクリック処理
    addClick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var msgCnt = 0;
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        if (this.details.length > 1) {
          if (this.details[index].code != this.before_details[this.before_count - 1].code) {
            msgCnt += 1;
            messages.push(this.toWide(String(msgCnt)) + ". " + "ログインIDが変更されていますが、");
            messages.push("以前のログインIDと別ユーザーで集計されます。");
          }
        }
        if (
          this.details[index].kill_from_date != "" &&
          this.details[index].kill_from_date != null
        ) {
          msgCnt += 1;
          messages.push(
            this.toWide(String(msgCnt)) + ". " + "退職日が入力されているため入力日より退職扱いとなります。"
          );
        }
        if (messages.length == 0) {
          messages.push("この内容で追加しますか？");
        } else {
          messages.push("追加してよろしいですか？");
        }
        this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
          result => {
            if (result) {
              this.FixDetail(CONST_KBNNAME_ADD, index);
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagevalidatesEdt,
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
    // 削除ボタンクリック処理
    delClick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var messages = [];
      var item_name = this.jdgSearchItemInput();
      if (item_name != null) {
        messages.push(item_name + "が変更されていますが、");
        messages.push("変更前の条件で削除します。");
      }
      messages.push("この内容を削除しますか？");
      this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
        result => {
          if (result) {
            this.DelDetail(index);
          }
        }
      );
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      if (this.before_count < this.count) {
        var messages = [];
        messages.push(
          "１度に追加できる情報は１個です。追加してから再実行してください"
        );
        this.htmlMessageSwal("エラー", messages, "error", true, false);
      } else {
        var add_apply_term_from = this.details[0].apply_term_from;
        var add_kill_from_date = this.details[0].kill_from_date;
        var add_card_idm = this.details[0].card_idm;
        var add_code = this.details[0].code;
        var add_department_code = this.details[0].department_code;
        var add_employment_status = this.details[0].employment_status;
        var add_name = this.details[0].name;
        var add_kana = this.details[0].kana;
        var add_short_name = this.details[0].short_name;
        var add_official_position = this.details[0].official_position;
        var add_working_timetable_no = this.details[0].working_timetable_no;
        var add_email = this.details[0].email;
        var add_mobile_email = this.details[0].mobile_email;
        var add_management = this.details[0].management;
        var add_role = this.details[0].role;
        var add_password = this.details[0].password;
        this.object = {
          id: "",
          code: add_code,
          department_code: add_department_code,
          employment_status: add_employment_status,
          name: add_name,
          kana: add_kana,
          short_name: add_short_name,
          official_position: add_official_position,
          working_timetable_no: add_working_timetable_no,
          email: add_email,
          mobile_email: add_mobile_email,
          password: add_password,
          management: add_management,
          role: add_role,
          card_idm: add_card_idm,
          apply_term_from: "",
          kill_from_date: add_kill_from_date,
          result: "2"
        };
        this.details.unshift(this.object);
        this.count = this.details.length;
      }
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("履歴追加取り消ししてよろしいですか？");
        this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
          result => {
            if (result) {
              this.details.splice(index, 1);
              this.count = this.details.length;
            }
          }
        );
      } else {
        this.details.splice(index, 1);
        this.count = this.details.length;
      }
    },
    // ICカード情報削除ボタンクリック処理
    releaseclick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messagevalidatestimetable = [];
      var messages = [];
      var item_name = this.jdgSearchItemInput();
      if (item_name != null) {
        messages.push(item_name + "が変更されていますが、");
        messages.push("変更前の条件で解除します。");
      }
      messages.push("カード情報の紐づけを解除しますか？");
      this.htmlMessageSwal(CONST_KBNNAME_CON, messages, "info", true, true).then(
        result => {
          if (result) {
            this.ReleaseCard(CONST_KBNNAME_REL, index);
          }
        }
      );
    },
    // -------------------- サーバー処理 ----------------------------
    // 顧客取得処理
    getItem() {
      var arrayParams = {
        //code: this.searchedUserValue,
        code: this.searchedCustomerValue,
      };
      this.postRequest("/edit_customer/get", arrayParams)
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("顧客", "取得getitem");
        });
    },
    // 顧客登録処理
    storeData() {
      console.log('storeData password = ' + this.form.password);
      var arrayParams = { details: this.form };
      this.postRequest("/edit_customer/store", arrayParams)
        .then(response => {
          this.putThenHead(response, CONST_KBNNAME_REG);
        })
        .catch(reason => {
          this.serverCatch("顧客", CONST_KBNNAME_REG);
        });
    },
    // 顧客更新処理（明細）
    FixDetail(kbnname, index) {
      var arrayParams = [];
      if (kbnname == CONST_KBNNAME_ADD) {
        arrayParams = { details: this.details[index], before_details: [] };
      } else {
        arrayParams = { details: this.details[index], before_details: this.before_details[index] };
      }
      this.postRequest("/edit_customer/fix", arrayParams)
        .then(response => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("顧客", CONST_KBNNAME_REG);
        });
    },
    // 顧客削除処理（明細）
    DelDetail(index) {
      var arrayParams = { details: this.details[index] };
      this.postRequest("/edit_customer/del", arrayParams)
        .then(response => {
          this.putThenDetail(response, CONST_KBNNAME_DEL);
        })
        .catch(reason => {
          this.serverCatch("顧客", CONST_KBNNAME_DEL);
        });
    },



    // 客先選択リスト取得処理
    getCustomerList(targetdate) {
      if (targetdate == "") {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      var arrayParams = {
        targetdate: targetdate,
      };
      this.postRequest("/get_customers_list", arrayParams)
        .then(response => {
          this.getThencustomer(response);
        })
        .catch(reason => {
          this.serverCatch("営業所", "取得");
        });
    },






    // モバイル端末へ打刻URL送信
    sendUrl() {
      const url = "/api/mail/inquiry";
      const self = this;
      this.messageshowsearch = true;
      this.details.forEach(element => {
        if (element.result == 1) {
          this.latest_user_code = element.code;
          this.mobile_address = element.mobile_email;
        }
      });
      //axiosでPOST送信
      axios
        .post(url, {
          email: this.mobile_address,
          login_id: this.latest_user_code
        })
        .then(res => {
          console.log(res);
          if (res.data.result) {
            //メール送信完了画面に遷移する
            var messages = [];
            messages.push(
              "※メールが届かない場合は、お手数ですがご使用いただくモバイル端末のブラウザにて「https://onedawnm.onedawn.net」へ手動で接続して下さい。"
            );
            this.htmlMessageSwal("送信完了", messages, "success", true, false);
          } else {
            self.errors = res.data.errors;
          }
          this.messageshowsearch = false;
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.messageshowsearch = false;
        });
    },
    // アップロード登録処理
    usersUpload() {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          var arrayParams = { usersups: this.usersups };
          this.postRequest("/edit_user/up", arrayParams)
            .then(response => {
              this.$swal.close();
              this.putThenUp(response, CONST_KBNNAME_UPL);
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("ユーザ", CONST_KBNNAME_UPL);
            });
        }
      });
    },

    // ----------------- privateメソッド ----------------------------------
    // イベントログファイル操作
    handleFileSelect: function(e) {
      var file_data = e.target.files[0];
      // 読み込み
      var reader = new FileReader();
      // 読み込んだファイルの中身を取得する
      reader.readAsBinaryString(file_data);
      let $this = this;
      //ファイルの中身を取得後に処理を行う
      reader.addEventListener("load", function() {
        var result = reader.result;
        const sjisArray = [];
        for (let i = 0; i < result.length; i += 1) {
          sjisArray.push(result.charCodeAt(i));
        }
        // 変換処理の実施
        const uniArray = encoding.convert(sjisArray, "unicode", "sjis");
        var result_enc = encoding.codeToString(uniArray);
        var array_linetext = result_enc.split("\r\n");
        var user_office_code = "";
        var user_code = "";
        var user_name = "";
        var linetext = "";
        var array_object = [];
        // 1行目はヘッダ
        for (var i = 1; i < array_linetext.length; i++) {
          linetext = array_linetext[i].split(",");
          // TODO:linetext.length=1はSKIPとするが(EOF)EOF以外のデータの場合でもSKIPとなる
          if (linetext.length == 3) {
            array_object.push({
              user_code: linetext[0].trim(),
              user_office_code: linetext[1].trim(),
              user_name: linetext[2].trim(),
            });
          } else if (linetext.length != 1) {
            return false;
          }
        }
        $this.usersups = array_object;
        $this.usersUpload();
        return true;
      });
    },
    // -------------------- 共通 ----------------------------
    // 客先選択コンポーネント取得メソッド
    getCustomerSelected: function(value) {
      this.$refs.selectcustomerlist.getList(value);
    },

    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // managementcode=99 → すべて
      this.$refs.selectuserlist.getList(
        "",
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue,
        99
      );
    },
    // 取得正常処理（ユーザーリスト）
    // getThenuser(response) {
    //   this.details = [];
    //   this.before_details = [];
    //   var res = response.data;
    //   if (res.result) {
    //     this.details = res.details;
    //     this.count = this.details.length;
    //     this.before_details = res.details;
    //     this.before_count = this.count;
    //   } else {
    //     if (res.messagedata.length > 0) {
    //       this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
    //     } else {
    //       this.serverCatch("氏名", "取得");
    //     }
    //   }
    // },
    // 取得正常処理（ユーザー）
    getThen(response) {
      this.details = [];
      this.count = 0;
      this.before_details = [];
      this.before_count = 0;
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_details = JSON.parse(JSON.stringify(this.details));
        this.before_count = this.before_details.length;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("getThenエラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },


    // 取得正常処理（客先選択リスト）
    getThencustomer(response) {
      var res = response.data;
      if (res.result) {
        this.customerList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("営業所", "取得");
        }
      }
    },






    // 取得正常処理（明細部署選択リスト）
    getThendepartment(response) {
      var res = response.data;
      if (res.result) {
        this.departmentList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("部署", "取得");
        }
      }
    },
    // 取得正常処理（明細タイムテーブル対象選択リスト）
    getThentimetable(response) {
      var res = response.data;
      if (res.result) {
        this.timetableList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細タイムテーブル", "取得");
        }
      }
    },
    // 更新系正常処理
    timputThenTimetable(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("タイムテーブルを" + eventtext + "しました。");
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        this.refreshUserList();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("タイムテーブル", eventtext);
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("ユーザーを" + eventtext + "しました。");
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        this.refreshUserList();
        //this.getNotSetting();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザー", eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーを" + eventtext + "しました");
        this.refreshUserList();
        this.getItem();
        //this.getNotSetting();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザ", eventtext);
        }
      }
    },
    // カード解除正常処理（明細）
    putThenCard(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーとカードの紐づけを解除しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザ", eventtext);
        }
      }
    },
    // 更新系正常処理（アップロード）
    putThenUp(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーを" + eventtext + "しました");
        //this.getNotSetting();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザー", eventtext);
        }
      }
    },
    
    // 設定要否取得処理
    getNotSetting() {
      if (this.infoMsgcnt > 1) { return; }
      if (this.settingcalendarsettinginformations == 0) {
        this.getThenCalendarSettingInfos();
      } else if (this.isexistdownload == 0) {
        this.getThenDownload();
      }
      this.infoMsgcnt++;
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("異常処理エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.before_details = [];
      this.form.name = "";
      this.form.id = "";
      this.form.code = "";
      this.selectedUserValue = "";
      this.searchedUserValue = "";
      this.selectedUserName = "";
      this.searchedUserName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    // 新規作成用
    newItemClear() {
      this.form.id = "";
      this.form.office_code = "";
      this.form.code = "";
      this.form.name = "";
    },
    checkRowData(index) {
      if (
        this.details[index].office_code != "" &&
        this.details[index].office_code != null
      ) {
        return true;
      }
      if (this.details[index].code != "" && this.details[index].code != null) {
        return true;
      }
      if (this.details[index].name != "" && this.details[index].name != null) {
        return true;
      }
      return false;
    },
    // 最新リストの表示（営業所）
    refresOfficeList() {
      this.showofficelist = false;
      this.$nextTick(() => (this.showofficelist = true));
    },
    // 最新リストの表示（顧客）
    refresCustomerlist() {
      this.showoCustomerlist = false;
      this.$nextTick(() => (this.showoCustomerlist = true));
    },

    // 最新リストの表示（明細部署）
    refreshaddDepartmentList() {
      this.showadddepartmentlist = false;
      this.$nextTick(() => (this.showadddepartmentlist = true));
    },
    // 最新リストの表示（明細部署）
    refreshreleaseCardbottun() {
      this.showrelease = false;
      this.$nextTick(() => (this.showrelease = true));
    },
    // 検索項目入力変更判定
    jdgSearchItemInput: function() {
      if (this.searchedOfficeValue != this.selectedOfficeValue) {
        return "営業所";
      }
      if (this.searchedCustomerValue != this.selectedCustomerValue) {
        return "顧客";
      }
      return null;
    },
    // 半角to全角（英数字）
    toWide: function(str) {
      return str.replace(/[A-Za-z0-9]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0) + 0xFEE0);
          });
    }
  }
};
</script>
<style scoped>

.table th, .table td {
    padding: 0rem !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-6 {
  min-width: 3rem;
}
</style>
