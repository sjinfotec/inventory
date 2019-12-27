<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'部署を設定する'"
            v-bind:header-text2="'部署の登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >部署選択<span class="color-red">[必須]</span></span>
                  </div>
                  <select-departmentlist v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'部署を選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="true"
                    v-bind:date-value="''"
                    v-bind:kill-value="valuekillcheck"
                    v-bind:row-index=0
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="form-check form-check-inline float-right">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="inlineCheckbox1"
                    v-model="valuekillcheck"
                    @change="checkboxChange"
                  >
                  <label class="form-check-label" for="inlineCheckbox1">※部署選択リストに廃止した部署も含める</label>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆部署登録'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesNew.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >部署名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆部署編集'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRowClick">+</button>
              </span>
              {{ this.selectedName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新たに追加することができます</span>
          </div>
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- .panel -->
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
            <div class="col-md pt-3 align-self-stretch">
              <div class="card shadow-pl">
                <!-- panel body -->
                <div class="card-body mb-3 p-0 border-top">
                  <!-- panel contents -->
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table class="table table-striped border-bottom font-size-sm text-nowrap">
                          <thead>
                            <tr>
                              <td class="text-center align-middle w-35 mw-rem-10">No</td>
                              <td class="text-center align-middle w-30">適用開始日</td>
                              <td class="text-center align-middle w-30">廃止開始日</td>
                              <td class="text-center align-middle w-35 mw-rem-10">部署名</td>
                              <td colspan="2" class="text-center align-middle w-35 mw-rem-10">操作</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item,index) in details" v-bind:key="item.id">
                              <td class="row justify-content-between text-center align-middle" v-if="details[index].result == 1">
                                <!-- panel header -->
                                <col-note
                                  v-bind:item-name="'No.' + (index+1) + ' 現在適用中'"
                                  v-bind:item-control="'INFO'"
                                  v-bind:item-note="''"
                                ></col-note>
                                <!-- /.panel header -->
                              </td>
                              <td class="row justify-content-between text-center align-middle" v-else>
                                <col-note
                                  v-bind:item-name="'No.' + (index+1)"
                                  v-bind:item-control="'LIGHT'"
                                  v-bind:item-note="''"
                                ></col-note>
                                <!-- /.panel header -->
                              </td>
                              <td class="text-center align-middle">
                                <div class>
                                  <input
                                    type="date"
                                    class="form-control"
                                    v-model="details[index].apply_term_from"
                                  />
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class>
                                  <input
                                    type="date"
                                    class="form-control"
                                    v-model="details[index].kill_from_date"
                                  />
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="input-group">
                                  <input
                                    type="text"
                                    maxlength="50"
                                    class="form-control"
                                    v-model="details[index].name"
                                  />
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group" v-if="details[index].result != 0 && details[index].id != ''">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="fixclick(index)"
                                  >この内容で更新する</button>
                                </div>
                                <div class="btn-group" v-if="details[index].id == ''">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="addClick(index)"
                                  >この内容で追加する</button>
                                </div>
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group" v-if="details[index].result == 2 && details[index].id != ''">
                                  <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click="delClick(index)"
                                  >削除する</button>
                                </div>
                                <div class="btn-group" v-if="details[index].id == ''">
                                  <button
                                    type="button"
                                    class="btn btn-danger"
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
                  <!-- /.row -->
                  <!-- /.panel contents -->
                </div>
                <!-- /panel body -->
              </div>
            </div>
            <!-- /.panel -->
          </div>
          <!-- /main contentns row -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

export default {
  name: "CreateDepartment",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      form: {
        name: "",
        id: "",
        code: ""
      },
      dbtablename: "",
      listitemname: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      valuedepartment: "",
      valuekillcheck: false,
      departmentList: [],
      selectMode: "",
      details: [],
      applyTerms: [],
      selectedValue: "",
      selectedName: "",
      selectApplyTerm: "",
      count: 0,
      before_count: 0,
      showdepartmentlist: true,
      confirmresult: true,
      oldId: ""
    };
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（新規作成）
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 部署名
      var required = true;
      var equalength = 0;
      var maxlength = 50;
      var itemname = '部署名';
      chkArray = 
        this.checkHeader(this.form.name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        this.messagevalidatesNew = chkArray;
        flag = false;
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function(index) {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 適用開始日
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '適用開始日';
      chkArray = 
        this.checkDetail(this.details[index].apply_term_from, required, equalength, maxlength, itemname, index+1);
      if (chkArray.length > 0) {
        this.messagevalidatesEdt = chkArray;
        flag = false;
      }
      // 部署名
      required = true;
      equalength = 0;
      maxlength = 50;
      itemname = '部署名';
      chkArray =
        this.checkDetail(this.details[index].name, required, equalength, maxlength, itemname, index+1);
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
    
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      // 入力項目の部署クリア
      this.inputClear();
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.selectedName = arrayitem['name'];
        this.getDepartment();
      }
    },
    // 廃止チェックボックスが変更された場合の処理
    checkboxChange: function() {
      this.refreshtDepartmentList();

    },
    // 新規作成ボタンクリック処理
    storeclick() {
      var flag = this.checkFormStore();
      if (flag) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.store();
            }
        });
      }
    },
    // 更新ボタンクリック処理
    fixclick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        if (this.details[index].kill_from_date == "" || this.details[index].kill_from_date == null) {
          messages.push("この内容で更新しますか？");
        } else {
          messages.push("廃止開始日が入力されているため入力日より廃止されます。");
          messages.push("更新してよろしいですか？");
        }
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("更新", index);
            }
        });
      }
    },
    // 追加ボタンクリック処理
    addClick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        if (this.details[index].kill_from_date == "" || this.details[index].kill_from_date == null) {
          messages.push("この内容で追加しますか？");
        } else {
          messages.push("廃止開始日が入力されているため入力日より廃止されます。");
          messages.push("追加してよろしいですか？");
        }
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.FixDetail("追加", index);
            }
        });
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      var messages = [];
      messages.push("この行内容を削除しますか？");
      this.messageswal("確認", messages, "info", true, true, true)
        .then(result  => {
          if (result) {
            this.DelDetail(index);
          }
      });
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      if (this.before_count < this.count) {
        var messages = [];
        messages.push("１度に追加できる情報は１個です。追加してから再実行してください");
        this.messageswal("エラー", messages, "error", true, false, true);
      } else {
        this.object = { id: "", code: this.details[this.details.length-1].code, name: "", apply_term_from: "", kill_from_date: "", result: "" };
        this.details.unshift(this.object);
        this.count = this.details.length
      }
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.messageswal("確認", messages, "info", true, true, true)
          .then(result  => {
            if (result) {
              this.details.splice(index, 1);
              this.count = this.details.length
            }
        });
      } else {
        this.details.splice(index, 1);
        this.count = this.details.length
      }
    },
    
    // -------------------- サーバー処理 ----------------------------
    // 部署取得処理
    getDepartment() {
      this.details = [];
      var messages = [];
      this.postRequest("/create_department/get", { code : this.selectedValue, killvalue : this.valuekillcheck})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 部署登録処理
    store() {
      var messages = [];
      var arrayParams = { code : this.form.code, name : this.form.name };
      this.postRequest("/create_department/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("登録");
        });
    },
    // 部署更新処理（明細）
    FixDetail(kbnname, index) {
      var messages = [];
      var arrayParams = { details : this.details[index] };
      this.postRequest("/create_department/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch(kbnname);
        });
    },
    // 部署削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { id : this.details[index].id };
      this.postRequest("/create_department/del", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          this.serverCatch("削除");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("エラー", res.messagedata, "error", true, false, true);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("部署を" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
        this.refreshtDepartmentList();
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("部署を" + eventtext + "しました");
        this.messageswal(eventtext + "完了", messages, "success", true, false, true);
        this.refreshtDepartmentList();
        this.getDepartment();
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.messageswal("警告", res.messagedata, "warning", true, false, true);
        } else {
          this.serverCatch(eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      messages.push("部署情報" + eventtext + "に失敗しました");
      this.messageswal("エラー", messages, "error", true, false, true);
    },
    inputClear() {
      this.details = [];
      this.form.name = "";
      this.form.id = "";
      this.form.code = "";
      this.selectedValue = "";
      this.selectedName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    checkRowData(index) {
      if (this.details[index].apply_term_from != "" && this.details[index].apply_term_from != null) { return true; }
      if (this.details[index].kill_from_date != "" && this.details[index].kill_from_date != null) { return true; }
      if (this.details[index].name != "" && this.details[index].name != null) { return true; }
      return false;
    },
    refreshtDepartmentList() {
      // 最新リストの表示
      this.showdepartmentlist = false;
      this.$nextTick(() => (this.showdepartmentlist = true));
    }
  }
};
</script>
