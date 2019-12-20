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
            v-bind:header-text1="'ＸＸＸを設定する'"
            v-bind:header-text2="'ＸＸＸの登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
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
                    >ＸＸＸＸ<span class="color-red">[必須]</span></span>
                  </div>
                  <select-XXXXXXXXX v-if="showlist"
                    ref="selectXXXXXXXXX"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'ＸＸＸを選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="true"departmentChanges
                    v-bind:date-value="''"
                    v-bind:kill-value="valuekillcheck"
                    v-bind:row-index=0
                    v-on:change-event="itemChanges"
                  ></select-XXXXXXXXX>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
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
                  <label class="form-check-label" for="inlineCheckbox1">※ＸＸＸＸ選択リストに廃止したＸＸＸＸも含める</label>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
      <!-- ========================== 新規部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆ＸＸＸＸ登録'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 新規入力部 START ---------------- -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
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
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >ＸＸＸＸ名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.XXXX"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'ＸＸＸＸ'"
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
            <div class="row justify-content-between">
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
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆ＸＸＸＸ編集'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRowClick">+</button>
              </span>
              {{ this.selectedName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新規に追加することができます</span>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 END ---------------- -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
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
            <!-- ----------- 項目部 START ---------------- -->
            <!-- panel contents -->
            <div v-for="index in count" v-bind:key="index">
              <!-- 現在適用中 ----------------------------------------------------------------->
              <div v-if="details[(index-1) * 7].result != ''">
                <!-- .row -->
                <div class="row justify-content-between" v-if="details[(index-1) * 7].result == 1">
                  <!-- panel header -->
                  <div class="col-md-2 pb-2">
                    <col-note
                      v-bind:item-name="'No.' + index + ' 現在適用中'"
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
                      v-bind:item-name="'No.' + index"
                      v-bind:item-control="'LIGHT'"
                      v-bind:item-note="''"
                    ></col-note>
                  </div>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
              <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel contents -->
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆ＸＸＸＸＸ'"
                    v-bind:header-text2="'ＸＸＸＸＸを入力します'"
                    v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- .col -->
                  <div class="col-md-12 pb-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span
                          class="input-group-text font-size-sm line-height-xs label-width-180"
                          id="basic-addon1"
                        >ＸＸＸＸＸ<span class="color-red">[必須]</span></span>
                      </div>
                      <input
                        type="text"
                        class="form-control"
                        v-model="details[(index-1) * 7].XXXXXX"
                        data-toggle="tooltip"
                        data-placement="top"
                        v-bind:title="'ＸＸＸＸＸを入力します'"
                        name="name"
                      />
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /row -->
                <!-- .row -->
                <!-- ----------- ボタン部 START ---------------- -->
                <div class="row justify-content-between">
                  <div class="col-md-12 pb-2">
                    <div class="btn-group float-left">
                      <button v-if="details[(index-1) * 7].result != 0 && details[(index-1) * 7].id != ''"
                        type="button"
                        class="btn btn-success"
                        @click="fixclick(index)"
                      >この内容で更新する</button>
                      <button v-if="details[(index-1) * 7].id == ''"
                        type="button"
                        class="btn btn-success"
                        @click="addClick(index)"
                      >この内容で追加する</button>
                      <button v-if="details[(index-1) * 7].result == 2 && details[(index-1) * 7].id != ''"
                        type="button"
                        class="btn btn-danger"
                        @click="delClick(index)"
                      >この内容を削除する</button>
                      <button v-if="details[(index-1) * 7].id == ''"
                        type="button"
                        class="btn btn-danger"
                        @click="rowDelClick(index)"
                      >行削除</button>
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
                      v-bind:item-name="'No.' + index"
                      v-bind:item-control="'LIGHT'"
                      v-bind:item-note="''"
                    ></col-note>
                  </div>
                  <!-- /.panel header -->
                </div>
                <!-- /row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆ＸＸＸＸＸ'"
                    v-bind:header-text2="''"
                    v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- .col -->
                  <div class="col-md-12 pb-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span
                          class="input-group-text font-size-sm line-height-xs label-width-180"
                          id="basic-addon1"
                        >ＸＸＸＸＸ</span>
                      </div>
                      <input
                        type="text"
                        class="form-control"
                        v-model="details[(index-1) * 7].XXXXXX"
                        name="name"
                        disabled
                      />
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
            <!-- /panel contents -->
            <!-- ----------- 項目部 END ---------------- -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
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
  name: "CreateXXXXXXXX",
  mixins: [ dialogable, checkable, requestable ],
  data() {
    return {
      showlist: true,
      selectedValue: "",
      valuekillcheck: false,
      selectMode: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      selectedName: "",
      details: [],
      form: {
        XXXX: "",
        XXXX: "",
        XXXX: ""
      },
      count: 0,
      before_count: 0
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
      // 適用開始日
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '適用開始日';
      chkArray = 
        this.checkDetail(this.details[index].apply_term_from, required, equalength, maxlength, itemname, index+1);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
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
    itemChanges: function(value, arrayitem) {
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
        this.getItem();
      }
    },
    // 廃止チェックボックスが変更された場合の処理
    checkboxChange: function() {
      this.refreshItemList();

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
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesNew, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
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
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
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
      // 項目数が多い場合以下コメントアウト
      /* } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        }); */
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      var messages = [];
      messages.push("この内容を削除しますか？");
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
        this.object = { id: "", code: this.details[this.details.length-1].code, name: "", apply_term_from: "", kill_from_date: "", result: "2" };
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
    getItem() {
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
        this.refreshItemList();
        this.form.code = res.code;
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
        this.refreshItemList();
        this.getItem();
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
      var messages = [];
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
    },
    refreshItemList() {
      // 最新リストの表示
      this.showlist = false;
      this.$nextTick(() => (this.showlist = true));
    }
  }
};
</script>
