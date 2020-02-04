<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'各種申請の承認者ルート設定'"
            v-bind:header-text2="'承認者と最終承認者を登録します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
        </div>
        <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
      </div>
      <!-- .panel -->
    </div>
    <!-- ----------- 承認ルート一覧 START ---------------- -->
    <div class="row justify-content-between" v-if="selectmode == 'LST'">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- ----------- パネルヘッダ部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" @click="appendRoot">+</button>
              </span>
              承認ルート一覧表示
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新規追加、行選択で編集できます</span>
          </div>
          <!-- /.panel header -->
          <!-- ----------- パネルヘッダ部 END ---------------- -->
          <!-- ----------- パネルコンテンツ部 START ---------------- -->
          <div class="card-body mb-3 pt-3 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-5">選択</td>
                          <td class="text-center align-middle w-15">承認ルート番号</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item,index) in root_user_confirm"  v-bind:key="item.confirm_no"
                        >
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input name="'radio' + index" type="radio" class="form-control" v-on:change="radiochange(index)"/>
                            </div>
                          </td>
                          <td class="text-center align-middle w-15">{{ item.confirm_no }}</td>
                          <td v-for="(itemseq,indexseq) in item.seqs">
                              <tr
                                v-for="(itemms,indexms) in itemseq.main_subs"
                              >
                                <td
                                  class="text-center align-middle w-15"
                                  v-if= "itemseq.seq != 99">第{{ itemseq.seq }}承認（{{ itemms.main_sub_name }}）{{ itemms.user_name }}</td>
                                <td
                                  class="text-center align-middle w-15"
                                  v-if= "itemseq.seq == 99">最終承認（{{ itemms.main_sub_name }}）{{ itemms.user_name }}</td>
                              </tr>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- /.row -->
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- ----------- パネルコンテンツ部 END ---------------- -->
        </div>
      </div>
    </div>
    <!-- ----------- 承認ルート一覧 END ---------------- -->
    <!-- ----------- 承認者情報 START ---------------- -->
    <div class="row justify-content-between" v-if="selectmode === 'EDT'">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- ----------- メインパネルヘッダ部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-outline-secondary btn-lg font-size-rg" @click="closeRoot">-</button>
              </span>
              承認ルート設定
            </h1>
            <span class="float-sm-right font-size-sm">「－」アイコンをクリックすると登録済みの承認ルート一覧を表示できます</span>
          </div>
          <!-- /.panel header -->
          <!-- ----------- メインパネルヘッダ部 END ---------------- -->
          <!-- ----------- メッセージ部 START ---------------- -->
          <!-- .row -->
          <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <ul class="error-red color-red">
                <li v-for="(messagevalidate,index) in messagevalidatesEdt" v-bind:key="index">{{ messagevalidate }}</li>
              </ul>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- ----------- メッセージ部 END ---------------- -->
          <!-- ----------- 適用部署部 START ---------------- -->
          <!-- .row -->
          <div class="card-header bg-transparent pt-3 border-0">
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-320"
                      id="basic-addon1"
                    >この承認ルートを適用する部署（省略は全部署）</span>
                  </div>
                  <select
                    class="custom-select"
                    v-model="applydepartmentCode"
                    v-on:change="applydepartmentChange"
                  >
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
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- ----------- 適用部署部 END ---------------- -->
          <!-- ----------- 承認者情報 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" @click="append">+</button>
              </span>
              承認者情報
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで承認者情報を追加できます</span>
          </div>
          <daily-working-information-panel-header
            v-bind:header-text1="'承認順番は行順です。また、承認者情報は省略可能です。'"
            v-bind:header-text2="'正副セットで同じ順番とします。ただし副は省略可能。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- パネルヘッダ部 END ---------------- -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel body -->
              <div class="card-body mb-3 py-0 pt-4 border-top print-none">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table class="table table-striped border-bottom font-size-sm text-nowrap">
                        <thead>
                          <tr>
                            <td class="text-center align-middle w-15 mw-rem-10"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                            >正副区分<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-25 mw-rem-10">承認者部署<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-35 mw-rem-10">承認者<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-35 mw-rem-10" colspan="3">操作</td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item,index) in confirms" v-bind:key="item.id">
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                            >
                              <div class="input-group">
                                <select
                                  class="custom-select"
                                  v-model="confirms[index].main_sub"
                                  v-on:change="mainsubChange"
                                >
                                  <option value></option>
                                  <option
                                    v-for="glist in generalList"
                                    :value="glist.code"
                                    v-bind:key="glist.code"
                                  >{{ glist.code_name }}</option>
                                </select>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <div class="input-group">
                                <select-departmentlist-detail
                                  ref="selectdepartmentlist"
                                  v-bind:department-list="departmentList"
                                  v-bind:blank-data="true"
                                  v-bind:placeholder-data="'部署を選択してください'"
                                  v-bind:selected-value="confirms[index].user_department_code"
                                  v-bind:add-new="false"
                                  v-bind:date-value="''"
                                  v-bind:kill-value="valueDepartmentkillcheck"
                                  v-bind:row-index="index"
                                  v-on:change-event="departmentChangeslUser"
                                ></select-departmentlist-detail>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <div class="input-group">
                                <select-userlist v-if="showuserlist"
                                  ref="selectuserlist"
                                  v-bind:blank-data="true"
                                  v-bind:placeholder-data="'氏名を選択してください'"
                                  v-bind:selected-value="confirms[index].user_code"
                                  v-bind:add-new="false"
                                  v-bind:get-do="getDo"
                                  v-bind:date-value="applytermdate"
                                  v-bind:kill-value="valueUserkillcheck"
                                  v-bind:row-index="index"
                                  v-bind:department-value="confirms[index].user_department_code"
                                  v-bind:employment-value="''"
                                  v-bind:management-value="'99'"
                                  v-bind:array-role="roles"
                                  v-on:change-event="userChanges"
                                ></select-userlist>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行を上に追加します。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-success btn-lg font-size-rg"
                                  @click="rowInsClick(index)"
                                >行挿入</button>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行を下に追加します。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-success btn-lg font-size-rg"
                                  @click="rowAddClick(index)"
                                >行追加</button>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行削除ではまだデータは削除されません。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-danger btn-lg font-size-rg"
                                  @click="rowDelClick(index)"
                                >行削除</button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- ----------- 承認者情報 END ---------------- -->
          <!-- ----------- 最終承認者情報 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" @click="appendFinal">+</button>
              </span>
              最終承認者情報
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで承認者情報を追加できます</span>
          </div>
          <daily-working-information-panel-header
            v-bind:header-text1="'承認順番は行順です。また、最終承認者情報は必須入力です。'"
            v-bind:header-text2="'正副セットで同じ順番とします。ただし副は省略可能。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel body -->
              <div class="card-body mb-3 py-0 pt-4 border-top print-none">
                <!-- panel contents -->
                <!-- .row -->
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table class="table table-striped border-bottom font-size-sm text-nowrap">
                        <thead>
                          <tr>
                            <td class="text-center align-middle w-15 mw-rem-10"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                            >正副区分<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-25 mw-rem-10">承認者部署<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-35 mw-rem-10">承認者<span class="color-red">[必須]</span></td>
                            <td class="text-center align-middle w-35 mw-rem-10" colspan="3">操作</td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item,index) in final_confirms" v-bind:key="item.id">
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'正の承認者と直前の正の代理承認者となる副の承認者の区別を選択します。'"
                            >
                              <div class="input-group">
                                <select
                                  class="custom-select"
                                  v-model="final_confirms[index].main_sub"
                                  v-on:change="mainsubFinalChange"
                                >
                                  <option value></option>
                                  <option
                                    v-for="glist in generalList"
                                    :value="glist.code"
                                    v-bind:key="glist.code"
                                  >{{ glist.code_name }}</option>
                                </select>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <div class="input-group">
                                <select-departmentlist-detail
                                  ref="selectdepartmentlist"
                                  v-bind:department-list="departmentList"
                                  v-bind:blank-data="true"
                                  v-bind:placeholder-data="'部署を選択してください'"
                                  v-bind:selected-value="final_confirms[index].user_department_code"
                                  v-bind:add-new="false"
                                  v-bind:date-value="''"
                                  v-bind:kill-value="valueDepartmentkillcheck"
                                  v-bind:row-index="index"
                                  v-on:change-event="departmentChangesFinalUser"
                                ></select-departmentlist-detail>
                              </div>
                            </td>
                            <td class="text-center align-middle">
                              <div class="input-group">
                                <select-userlist v-if="showuserlist2"
                                  ref="selectuserlist2"
                                  v-bind:blank-data="true"
                                  v-bind:placeholder-data="'氏名を選択してください'"
                                  v-bind:selected-value="final_confirms[index].user_code"
                                  v-bind:add-new="false"
                                  v-bind:get-do="getDo"
                                  v-bind:date-value="applytermdate"
                                  v-bind:kill-value="valueUserkillcheck"
                                  v-bind:row-index="index"
                                  v-bind:department-value="final_confirms[index].user_department_code"
                                  v-bind:employment-value="''"
                                  v-bind:management-value="'99'"
                                  v-bind:array-role="roles"
                                  v-on:change-event="userFinalChanges"
                                ></select-userlist>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行を上に追加します。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-success btn-lg font-size-rg"
                                  @click="rowInsFinalClick(index)"
                                >行挿入</button>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行を下に追加します。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-success btn-lg font-size-rg"
                                  @click="rowAddFinalClick(index)"
                                >行追加</button>
                              </div>
                            </td>
                            <td class="text-center align-middle"
                              data-toggle="tooltip"
                              data-placement="top"
                              v-bind:title="'行削除ではまだデータは削除されません。'"
                            >
                              <div class="btn-group">
                                <button
                                  type="button"
                                  class="btn btn-danger btn-lg font-size-rg"
                                  @click="rowDelFinalClick(index)"
                                >行削除</button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- /.row -->
              </div>
              <!-- /.panel contents -->
            </div>
          </div>
          <!-- /panel body -->
          <!-- ----------- 最終承認者情報 END ---------------- -->
        </div>
      </div>
    </div>
    <!-- ----------- ボタン部 START ---------------- -->
    <div class="row justify-content-between" v-if="selectmode === 'EDT'">
      <!-- .row -->
      <!-- col -->
      <div class="col-md-12 pb-2">
        <btn-work-time v-bind:btn-mode="'store'" v-on:storeclick-event="storeclick"></btn-work-time>
      </div>
      <!-- /.col -->
      <!-- /.row -->
      <!-- .row -->
      <!-- col -->
      <div class="col-md-12 pb-2" v-if="confirm_no != 0">
        <btn-work-time v-bind:btn-mode="'delete'" v-on:deleteclick-event="deleteclick"></btn-work-time>
      </div>
      <!-- /.col -->
      <!-- /.row -->
    </div>
  </div>
  <!-- main contentns row -->
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "SettingRoot",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      valueDepartmentkillcheck : false,
      showdepartmentlist: true,
      departmentList: [],
      valueUserkillcheck : false,
      showuserlist: true,
      showuserlist2: true,
      userList: [],
      root_user_confirm_title: [],
      root_user_confirm: [],
      confirms: [],
      final_confirms: [],
      confirm_no: 0,
      server_confirms: [],
      server_final_confirms: [],
      generalList: [],
      userCode: "",
      applydepartmentCode: "",
      validate: false,
      selectmode: "",
      show_result: false,
      messageshowsearch: true,
      messagedatasserver: [],
      messagevalidatesEdt: [],
      applytermdate : '',
      fromdate : '',
      issearchbutton: false,
      getDo : 0,
      roles: [],
      finalerrors: []
    };
  },
  created() {
    this.roles = [];
    this.roles.push(5);
    this.roles.push(9);
  },
  // マウント時
  mounted() {
    this.getDo = 1;
    this.getDepartmentList('');
    // this.getUserList('');
    this.getGeneralList("C027");
    this.getConfirm();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkForm: function() {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '';

      //承認者
      for(var $i=0;$i<this.confirms.length;$i++) {
        // 正副
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '承認者情報・正副';
        chkArray = 
          this.checkDetailtext(this.confirms[$i].main_sub, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        } else {
          if ($i == 0) {
            // 最初が正であること
            if (this.confirms[$i].main_sub != 1) {
              chkArray.push("1行目めの承認者情報・正副は正を入力してください");
              this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
            }
          }
        }
        // 承認者部署
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '承認者情報・承認者部署';
        chkArray = 
          this.checkDetailtext(this.confirms[$i].user_department_code, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        }
        // 承認者
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '承認者情報・承認者';
        chkArray = 
          this.checkDetailtext(this.confirms[$i].user_code, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        }
      }

      //最終承認者
      if (this.final_confirms.length == 0) {
        chkArray.push("最終承認者は必ず入力してください");
        this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
      }
      for(var $i=0;$i<this.final_confirms.length;$i++) {
        // 正副
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '最終承認者情報・正副';
        chkArray = 
          this.checkDetailtext(this.final_confirms[$i].main_sub, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        } else {
          if ($i == 0) {
            // 最初が正であること
            if (this.final_confirms[$i].main_sub != 1) {
              chkArray.push("1行目めの最終承認者情報・正副は正を入力してください");
              this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
            }
          }
        }
        // 承認者部署
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '最終承認者情報・承認者部署';
        chkArray = 
          this.checkDetailtext(this.final_confirms[$i].user_department_code, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        }
        // 承認者
        required = true;
        equalength = 0;
        maxlength = 0;
        itemname = '最終承認者情報・承認者';
        chkArray = 
          this.checkDetailtext(this.final_confirms[$i].user_code, required, equalength, maxlength, itemname, ($i+1) + '行め');
        if (chkArray.length > 0) {
          if (this.messagevalidatesEdt.length == 0) {
            this.messagevalidatesEdt = chkArray;
          } else {
            this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
          }
        }
      }

      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------
    
    // 適用部署選択が変更された場合の処理
    applydepartmentChange: function() {
      // 承認者情報の適用部署に設定
      // 承認者情報
      for ( var i=0; i<this.confirms.length; i++ ) {
        this.confirms[i].confirm_department_code = this.applydepartmentCode;
      }
      // 最終承認者情報
      for ( var i=0; i<this.final_confirms.length; i++ ) {
        this.final_confirms[i].confirm_department_code = this.applydepartmentCode;
      }
    },
    // 承認者正副選択が変更された場合の処理
    mainsubChange: function() {
    },
    // 承認ユーザー部署選択が変更された場合の処理
    departmentChangeslUser: function(value, arrayitem) {
      console.log('value' + value);
      console.log('rowindex' + arrayitem['rowindex']);
      this.confirms[arrayitem['rowindex']].user_department_code = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected(value, arrayitem['rowindex']);
    },
    // 承認者部署選択が変更された場合の処理
    departmentChangesConfirm: function(value, arrayitem) {
      this.confirms[arrayitem['rowindex']].confirm_department_code = value;
    },
    // 承認者ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      this.confirms[arrayitem['rowindex']].user_code = value;
    },
    // 最終承認者正副選択が変更された場合の処理
    mainsubFinalChange: function() {
    },
    // 最終承認ユーザー部署選択が変更された場合の処理
    departmentChangesFinalUser: function(value, arrayitem) {
      this.final_confirms[arrayitem['rowindex']].user_department_code = value;
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelectedFinal(value, arrayitem['rowindex']);
    },
    // 最終承認者部署選択が変更された場合の処理
    departmentChangesFinalConfirm: function(value, arrayitem) {
      this.final_confirms[arrayitem['rowindex']].confirm_department_code = value;
    },
    // 最終承認者ユーザー選択が変更された場合の処理
    userFinalChanges: function(value, arrayitem) {
      this.final_confirms[arrayitem['rowindex']].user_code = value;
    },
    // 承認ルート追加の処理
    appendRoot: function() {
      this.applydepartmentCode = "";
      this.confirm_no = 0;
      this.confirms = [];
      this.final_confirms = [];
      this.append();
      this.appendFinal();
      this.selectmode = "EDT";
    },
    // 承認者追加の処理
    append: function() {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.confirms.push(appendobject);
    },
    // 最終承認者追加の処理
    appendFinal: function() {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.final_confirms.push(appendobject);
    },
    // 承認ルート閉じるの処理
    closeRoot: function() {
      this.selectmode = "LST";
      this.confirms = [];
      this.final_confirms = [];
    },
    // 承認者行挿入ボタンクリック処理
    rowInsClick: function(index) {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.confirms.splice(index, 0, appendobject);
    },
    // 承認者行追加ボタンクリック処理
    rowAddClick: function(index) {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.confirms.splice(index+1, 0, appendobject);
    },
    // 承認者行削除ボタンクリック処理
    rowDelClick: function(index) {
      this.messagevalidatesEdt = [];
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.confirms.splice(index, 1);
            }
        });
      } else {
        this.confirms.splice(index, 1);
      }
    },
    // 最終承認者行挿入ボタンクリック処理
    rowInsFinalClick: function(index) {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.final_confirms.splice(index, 0, appendobject);
    },
    // 最終承認者行追加ボタンクリック処理
    rowAddFinalClick: function(index) {
      if (this.applydepartmentCode == null) { this.applydepartmentCode = "";}
      var appendobject = {
        id: "",
        confirm_no: this.confirm_no,
        seq: "",
        main_sub: "",
        confirm_department_code: this.applydepartmentCode,
        user_department_code: "",
        user_code: "",
        main_sub_name: "",
        confirm_department_name: "",
        user_department_name: "",
        user_name: "",
        created_user: "",
        updated_user: "",
        created_at: "",
        updated_at: ""
      };
      this.final_confirms.splice(index+1, 0, appendobject);
    },
    // 最終承認者行削除ボタンクリック処理
    rowDelFinalClick: function(index) {
      this.messagevalidatesEdt = [];
      if (this.checkRowDataFinal(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.final_confirms.splice(index, 1);
            }
        });
      } else {
        this.final_confirms.splice(index, 1);
      }
    },
    // ラジオボタンがクリックされた場合の処理
    radiochange: function(index) {
      this.roles = [];
      this.roles.push(5);
      this.roles.push(9);
      console.log('setting_root radiochange' + this.roles[0]);
      console.log('setting_root radiochange' + this.roles[1]);
      // 承認者情報
      this.confirm_no = this.root_user_confirm[index].confirm_no;
      this.applydepartmentCode = "";
      this.confirms = [];
      for ( var i=0; i<this.server_confirms.length; i++ ) {
        if (this.server_confirms[i].confirm_no == this.confirm_no) {
          if (this.applydepartmentCode == "") {
            this.applydepartmentCode = this.server_confirms[i].confirm_department_code;
          }
          this.confirms.push({
            id: this.server_confirms[i].id,
            confirm_no: this.server_confirms[i].confirm_no,
            seq: this.server_confirms[i].seq,
            main_sub: this.server_confirms[i].main_sub,
            confirm_department_code: this.server_confirms[i].confirm_department_code,
            user_department_code: this.server_confirms[i].user_department_code,
            user_code: this.server_confirms[i].user_code,
            main_sub_name: this.server_confirms[i].main_sub_name,
            confirm_department_name: this.server_confirms[i].confirm_department_name,
            user_department_name: this.server_confirms[i].user_department_name,
            user_name: this.server_confirms[i].user_name,
            created_user: this.server_confirms[i].created_user,
            userupdated_user_code: this.server_confirms[i].updated_user,
            created_at: this.server_confirms[i].created_at,
            updated_at: this.server_confirms[i].updated_at
          });
        } else {
          if(this.server_confirms[i].confirm_no > this.confirm_no) {
            i=999;
          }
        }
      }
      // 最終承認者情報
      this.final_confirms = [];
      for ( var i=0; i<this.server_final_confirms.length; i++ ) {
        if (this.server_final_confirms[i].confirm_no == this.confirm_no) {
          if (this.applydepartmentCode == "") {
            this.applydepartmentCode = this.server_final_confirms[i].confirm_department_code;
          }
          this.final_confirms.push({
            id: this.server_final_confirms[i].id,
            confirm_no: this.server_final_confirms[i].confirm_no,
            seq: this.server_final_confirms[i].seq,
            main_sub: this.server_final_confirms[i].main_sub,
            confirm_department_code: this.server_final_confirms[i].confirm_department_code,
            user_department_code: this.server_final_confirms[i].user_department_code,
            user_code: this.server_final_confirms[i].user_code,
            main_sub_name: this.server_final_confirms[i].main_sub_name,
            confirm_department_name: this.server_final_confirms[i].confirm_department_name,
            user_department_name: this.server_final_confirms[i].user_department_name,
            user_name: this.server_final_confirms[i].user_name,
            created_user: this.server_final_confirms[i].created_user,
            userupdated_user_code: this.server_final_confirms[i].updated_user,
            created_at: this.server_final_confirms[i].created_at,
            updated_at: this.server_final_confirms[i].updated_at
          });
        } else {
          if(this.server_confirms[i].confirm_no > this.confirm_no) {
            i=999;
          }
        }
      }
      this.selectmode = "EDT";
    },
    // 登録ボタンクリック処理
    storeclick() {
      this.messagevalidatesEdt = [];
      var flag = this.checkForm();
      if (flag) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.store();
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 削除ボタンクリック処理
    deleteclick() {
      var messages = [];
      messages.push("この内容を削除しますか？");
      this.messageswal("確認", messages, "info", true, true, true)
        .then(result  => {
          if (result) {
            this.delete();
          }
      });
    },
    // -------------------- サーバー処理 ----------------------------
    // 部署選択リスト取得処理
    getDepartmentList(targetdate){
      if (targetdate == '') {
          targetdate = moment(new Date()).format("YYYYMMDD");
      }
      var arrayParams = { targetdate : targetdate, killvalue: this.valueDepartmentkillcheck };
      this.postRequest("/get_departments_list", arrayParams)
        .then(response  => {
          this.getThendepartment(response);
        })
        .catch(reason => {
          this.serverCatch("部署", "取得");
        });
    },
    // ユーザーリスト取得処理
    getUserList(targetdate){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (this.selectedDepartmentValue == "") { this.selectedDepartmentValue = null; }
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: this.killValue,
          getDo : this.getDo,
          departmentcode : this.selectedDepartmentValue,
          employmentcode : null,
          managementcode : 99,
          roles : this.roles
        })
        .then(response  => {
          this.getThenuser(response);
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
          // 固有処理 END
        })
        .catch(reason => {
          this.serverCatch("ユーザー", "取得");
          if (this.selectedDepartmentValue == null) { this.selectedDepartmentValue = ""; }
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid : value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response  => {
          if (value == "C027") {
            this.getThenmainsub(response, "正副");
          }
        })
        .catch(reason => {
          if (value == "C027") {
            this.serverCatch("正副", "取得");
          }
        });
    },
    // 承認者取得処理
    getConfirm(){
      this.itemClear();
      this.postRequest("/confirm/gettable",
        { confirm_no : null,
          confirm_department_code: null,
          user_department_code: null,
          user_code: null,
          seq: null,
          main_sub: null })
        .then(response  => {
          this.getThenconfirm(response);
        })
        .catch(reason => {
          this.serverCatch("承認者", "取得");
        });
    },
    // 登録
    store: function() {
      var arrayParams = { confirmno : this.confirm_no, confirms : this.confirms, final_confirms :  this.final_confirms};
      this.postRequest("/confirm/store", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("承認情報", "登録");
        });
    },
    // 削除
    delete: function() {
      var arrayParams = { confirmno : this.confirm_no};
      this.postRequest("/confirm/del", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          this.serverCatch("承認情報", "登録");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理（明細部署選択リスト）
    getThendepartment(response) {
      var res = response.data;
      if (res.result) {
        this.departmentList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("部署", "取得");
        }
      }
    },
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function(value, index) {
      // managementcode=99 → すべて
      this.$refs.selectuserlist[index].getList(
        '',
        this.valueUserkillcheck,
        this.getDo,
        value,
        null,
        99,
        this.roles
      );
      this.refreshuserlist();
    },
    // 最終承認者ユーザー選択コンポーネント取得メソッド
    getUserSelectedFinal: function(value, index) {
      // managementcode=99 → すべて
      this.$refs.selectuserlist2[index].getList(
        '',
        this.valueUserkillcheck,
        this.getDo,
        value,
        null,
        99,
        this.roles
      );
      this.refreshuserlist2();
    },
    // 取得正常処理（ユーザーリスト）
    getThenuser(response) {
      var res = response.data;
      if (res.result) {
        this.userList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("ユーザー", "取得");
        }
      }
    },
    // 取得正常処理（明細正副選択リスト）
    getThenmainsub(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("明細正副", "取得");
        }
      }
    },
    // 取得正常処理（承認者）
    getThenconfirm(response) {
      var res = response.data;
      this.selectmode = "EDT";
      if (res.result) {
        this.server_confirms = res.confirms;
        if (this.server_confirms.length == 0) {
          var messagedatas = [];
          messagedatas.push("承認ルートはまだ設定されていません。\nプラスアイコンで登録してください。" );
          this.messageswal("確認", messagedatas, "info", true, false, false);
          this.messagedatasserver = messagedatas;
        } else {
          this.selectmode = "LST";
        }
        this.server_final_confirms = res.final_confirms;
        // this.root_user_confirm_title = res.root_user_confirm_title;
        this.root_user_confirm = res.root_user_confirm;
        this.show_result = true;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("承認者", "取得");
        }
      }
      this.messageshowsearch = false;
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("承認情報" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
        this.selectmode = "LST";
        this.getConfirm();
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch("承認情報", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
      this.messageshowsearch = false;
    },
    // 承認者行入力チェック
    checkRowData(index) {
      if (this.confirms[index].main_sub != "" && this.confirms[index].main_sub != null) { return true; }
      if (this.confirms[index].user_department_code != "" && this.confirms[index].user_department_code != null) { return true; }
      if (this.confirms[index].user_code != "" && this.confirms[index].user_code != null) { return true; }
      // if (this.confirms[index].confirm_department_code != "" && this.confirms[index].confirm_department_code != null) { return true; }
      return false;
    },
    // 最終承認者行入力チェック
    checkRowDataFinal(index) {
      if (this.final_confirms[index].main_sub != "" && this.final_confirms[index].main_sub != null) { return true; }
      if (this.final_confirms[index].user_department_code != "" && this.final_confirms[index].user_department_code != null) { return true; }
      if (this.final_confirms[index].user_code != "" && this.final_confirms[index].user_code != null) { return true; }
      // if (this.final_confirms[index].confirm_department_code != "" && this.final_confirms[index].confirm_department_code != null) { return true; }
      return false;
    },
    itemClear() {
      this.applydepartmentCode = "";
      this.confirms = [];
      this.final_confirms = [];
      this.root_user_confirm_title = [];
      this.root_user_confirm = [];
      this.server_confirms = [];
      this.server_final_confirms = [];
    },
    // 最新リストの表示（明細部署）
    refreshuserlist() {
      this.showuserlist = false;
      this.$nextTick(() => (this.showuserlist = true));
    },
    // 最新リストの表示（明細部署）
    refreshuserlist2() {
      this.showuserlist2 = false;
      this.$nextTick(() => (this.showuserlist2 = true));
    }
  }
};
</script>

