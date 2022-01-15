<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <div id="target" class="transition1 card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table id="table_cnt3" class="table table-striped border-bottom font-size-sm text-nowrap">
                      <tr v-for="(n,index) in form_count " :key="index">
                        <td class="w1 text-center align-middle">{{ form.item_name[index] }}</td>
                        <td class="w2 text-center align-middle">{{ form.item_data[index] }}</td>
                      </tr>
                      <tr>
                        <td class="text-center align-middle">
                          <div id="pview_status" v-if="maketime">時間入力</div>
                          <div id="pview_status" v-else>経過時間</div>
                        </td>
                        <td class="text-center align-middle">
                          <div id="input_cnt1" v-if="maketime">
                          加工時間
                              <span class="input_w1">
                                <input
                                  type="number"
                                  step="1"
                                  class="form-control"
                                  v-model="form.process_time_h"
                                />
                              </span>
                              <span class="input_w2">H</span>
                              <span class="input_w1">
                                <input
                                  type="number"
                                  step="1"
                                  class="form-control"
                                  v-model="form.process_time_m"
                                />
                              </span>
                              <span class="input_w2">M</span>
                          </div>
                          <div v-else>
                            <div v-if="formatTimer">
                              <span>{{ timerStrings[0] }}</span>
                              <span>H</span>
                              <span>{{ timerStrings[1] }}</span>
                              <span>M</span>
                              <span>{{ timerStrings[2] }}</span>
                              <span>S</span>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 START ---------------- -->
            <div id="btn_cnt1" class="print-none" v-if="isbtnctrl == 'top'">
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick1"
                  v-bind:btn-mode="'startwork1'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick2"
                  v-bind:btn-mode="'startwork2'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick3"
                  v-bind:btn-mode="'startwork3'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
            </div>
            <div id="btn_cnt1" class="print-none" v-else-if="isbtnctrl == 'top2'">
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick4"
                  v-bind:btn-mode="'startwork4'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick2"
                  v-bind:btn-mode="'startwork2'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:start-event="startClick3"
                  v-bind:btn-mode="'startwork3'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
            </div>


            <div id="btn_cnt2" class="print-none" v-else-if="isbtnctrl == 'suspen'">
              <div class="btn_col_1">
                <btn-work-time
                  v-on:okclick-event="startClicksus"
                  v-bind:btn-mode="'startworksus'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:okclick-event="startClickmiss"
                  v-bind:btn-mode="'startworkmiss'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:cancelclick-event="startClickcancel"
                  v-bind:btn-mode="'startworkcancel'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
            </div>

            <div id="btn_cnt2" class="print-none" v-else-if="isbtnctrl == 'comple'">
              <div class="btn_col_1">
                <btn-work-time
                  v-on:okclick-event="startClickcomp"
                  v-bind:btn-mode="'startworkcomp'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:okclick-event="startClicknext"
                  v-bind:btn-mode="'startworknext'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <div class="btn_col_1">
                <btn-work-time
                  v-on:cancelclick-event="startClickcancel"
                  v-bind:btn-mode="'startworkcancel'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
            </div>

            <!-- .row -->
            <div id="btn_cnt2" class="print-none" v-else>
              <!-- col -->
              <div class="col-md-6 pb-2">
                <btn-work-time
                  v-on:okclick-event="startClickok"
                  v-bind:btn-mode="'startworkok'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
              <!-- col -->
              <div class="col-md-6 pb-2">
                <btn-work-time
                  v-on:cancelclick-event="startClickcancel"
                  v-bind:btn-mode="'startworkcancel'"
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
// CONST
const C_SUPPLY_DATE_NAME = "納期";
const C_ORDER_NO_NAME = "受注番号";
const C_ROW_SEQ_NAME = "行";
const C_DRAWING_NO_NAME = "図面番号";
const C_ORDER_COUNT_NAME = "個数";
const C_MODEL_NUMBER_NAME = "形式／型番";
const C_PRODUCT_NAME = "品名";
const C_OUTLINE_NAME = "明細摘要";
const C_DEVICE_NAME = "機器名";
const C_USER_NAME = "作業者";
const C_KIND_INI = "1";
const C_KIND_INI_NAME = "作業準備";
const C_KIND_START = "2";
const C_KIND_START_NAME = "作業開始";
const C_KIND_STOP = "3";
const C_KIND_STOP_NAME = "作業中断";
const C_KIND_MSTOP = "4";
const C_KIND_MSTOP_NAME = "ミス中断";
const C_KIND_COMPLETE = "5";
const C_KIND_COMPLETE_NAME = "作業完了";
const C_KIND_NEXT = "6";
const C_KIND_NEXT_NAME = "次工程";
const kindcolorArr = {"作業準備":"#FFF", "作業開始":"#80bb60", "作業中断":"#dd6060", "ミス中断":"#dd6060", "作業完了":"#6cb2eb", "次工程":"#eeaa00" };

export default {
  name: "ProcessInfo",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    order_no: {
      type: String,
      default: ""
    },
    seq: {
      type: Number,
      default: 0
    },
    device: {
      type: String,
      default: ""
    },
    user_code: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      form: {
        order_no: "",
        seq: 0,
        kind: "",
        device_code: "",
        user_code: "",
        row_seq: "",
        item_name: [{}],
        item_data: [{}],
        process_time_h: "",
        process_time_m: "",
        statusText: ""
      },
      isbtnctrl: '',
      kind_index: 0,
      kind_name: "",
      before_kind: "",
      count: 0,
      before_count: 0,
      form_count: 0,
      details: [],
      maketime: false,
      before_kindstatus: "",
      kindstatus: "",
      mode_button: "",
      work_kind: "",
      hour: 0,
      min: 0,
      sec: 0,
      timerOn: false,
      timerObj: null,
      timerStrings: [],

    };
  },
  computed: {
    // タイマー表示
    formatTimer() {
      this.timerStrings = [
        this.hour.toString(),
        this.min.toString(),
        this.sec.toString()
      ].map(function(str) {
        if (str.length < 2) {
          return "0" + str
        } else {
          return str
        }
      })
      if (this.hour > 0 || this.min > 0 || this.sec > 0) {
        return true;
      } else {
        return false;
      }
    }
  },
  // マウント時
  mounted() {
    // console.log('ProcessInfo mounted ');
    this.getItem();
    //this.setKind();
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション
    // ------------------------ イベント処理 ------------------------------------
    // 作業開始処理
    startClick1() {
      this.before_kindstatus = this.kindstatus;
      // console.log('作業開始ボタンbefore_kindstatus = ' + this.kindstatus);
      this.isbtnctrl = false;
      this.form.kind = C_KIND_START;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
    	target.style.background = '#80bb60';
      table_cnt3.style.color = '#FFF';
      this.form.process_time_h = "";
      this.form.process_time_m = "";
    },
    // 作業中断処理
    startClick2() {
      this.before_kindstatus = this.kindstatus;
      this.isbtnctrl = 'suspen';
      this.form.kind = C_KIND_STOP;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
  	  target.style.background = '#dd6060';
      table_cnt3.style.color = '#FFF';
      this.form.process_time_h = "";
      this.form.process_time_m = "";
    },
    // 作業完了処理
    startClick3() {
      this.before_kindstatus = this.kindstatus;
      this.isbtnctrl = 'comple';
      this.form.kind = C_KIND_COMPLETE;
      this.form.process_time_h = this.hour;
      this.form.process_time_m = this.min;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
      this.maketime = true;
  	  target.style.background = '#6cb2eb';
      table_cnt3.style.color = '#FFF';
    },
    // 作業再開処理
    startClick4() {
      this.before_kindstatus = this.kindstatus;
      this.isbtnctrl = false;
      this.form.kind = C_KIND_START;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
    	target.style.background = '#80bb60';
      table_cnt3.style.color = '#FFF';
    },
    // 作業開始OK
    startClickok() {
      this.storeData();
      this.isbtnctrl = 'top';
      this.mode_button = '';
    },
    // 中断処理
    startClicksus() {
      this.form.kind = C_KIND_STOP;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
      this.storeData();
      this.isbtnctrl = 'top2';
      this.mode_button = 'sus';
    },
    // ミス処理
    startClickmiss() {
      this.form.kind = C_KIND_MSTOP;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
      this.storeData();
      this.isbtnctrl = 'top2';
      this.mode_button = 'sus';
    },
    // 完成処理
    startClickcomp() {
      // console.log('startClickcomp in');
      this.form.kind = C_KIND_COMPLETE;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
      this.storeData();
      this.maketime = false;
      this.isbtnctrl = 'top';
      this.mode_button = '';
    },
    // 次工程処理
    startClicknext() {
      this.form.kind = C_KIND_NEXT;
      this.setKind();
      this.form.item_name[this.kind_index] = this.kind_name;
      this.storeData();
      this.maketime = false;
      this.isbtnctrl = 'top';
      this.mode_button = '';
    },
    // 作業キャンセル処理
    startClickcancel() {
      if ( this.mode_button == 'sus' ) {
        this.isbtnctrl = 'top2';
      } else {
        this.isbtnctrl = 'top';
      }
      //this.form.item_name[this.kind_index] = this.kind_name;
      this.form.item_name[this.kind_index] = this.before_kindstatus;
      this.maketime = false;
// console.log( kindcolorArr );
// console.log('before_kindstatus = ' + this.before_kindstatus);
// console.log('kindcolorArr[before_kindstatus] = ' + kindcolorArr[this.before_kindstatus]);
  	  target.style.background = kindcolorArr[this.before_kindstatus];
      if (kindcolorArr[this.before_kindstatus] == '#FFF') {
        table_cnt3.style.color = '#212529';
      }
      this.kindstatus = this.before_kindstatus;
    },
    // 作業タイマー開始処理
    startTimer() {
      let $this = this;
      this.timerObj = setInterval(function() {$this.setTimer()}, 1000)
      this.timerOn = true; //timerがONであることを状態として保持
    },
    // 作業タイマー終了処理
    stopTimer() {
      clearInterval(this.timerObj);
      this.timerOn = false; //timerがOFFであることを状態として保持
    },
    // タイマー設定処理
    setTimer() {
      if (this.sec >= 59) {
        if (this.min >= 59) {
          this.hour++;
          this.min = 0;
          this.sec = 0;
        } else {
          if (this.sec >= 59) {
            this.min++;
            this.sec = 0;
          }
        }
      } else {
        this.sec++;
      }
    },
    // -------------------- サーバー処理 ----------------------------
    // 指示書／管理書取得
    getItem() {
      var arrayParams = { 
        order_no : this.order_no ,
        seq : this.seq,
        device : this.device,
        user_code : this.user_code
        };
      this.postRequest("/process_info/get", arrayParams)
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // 登録処理
    storeData() {
      var arrayParams = { form : this.form };
      // console.log('storeData this.form.kind = ' + this.form.kind);
      // 次工程の場合はじめはcompleteで登録  -- todo
      this.before_kind = "";
      if (this.form.kind == C_KIND_NEXT) {
        // this.form.kind = C_KIND_COMPLETE;
        this.before_kind = C_KIND_NEXT;
      }
      this.postRequest("/process_history/put", arrayParams)
        .then(response => {
          this.putThen(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("作業工程", "登録");
        });
    },
    // -------------------- 共通 ----------------------------
    // 取得正常処理
    getThen(response) {
      // console.log('getThen in');
      var res = response.data;
      this.details = res.details;
      this.count = this.details.length;
      if ( this.details.length > 0) {
        this.form.device_code = this.device;
        this.form.user_code = this.user_code;
        this.form.order_no = this.order_no;
        this.form.seq = this.seq;
        var set_index = 0;
        let $this = this;
        this.details.forEach((detail, i) => {
          $this.form.row_seq = detail.row_seq;
          $this.form.kind = detail.work_kind;
          // console.log('getThen in detail.work_kind = ' + detail.work_kind);
          // console.log('getThen in detail.user_name = ' + detail.user_name);
          // progress_noは廃止する方向
          $this.form.progress_no = null;
          $this.form.item_name[set_index] = C_SUPPLY_DATE_NAME;
          $this.form.item_data[set_index] = detail.supply_date_name;
          // console.log('getThen detail.supply_date_name = ' + $this.form.item_data[set_index]);
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_ORDER_NO_NAME;
          $this.form.item_data[set_index] = detail.order_no;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_ROW_SEQ_NAME;
          $this.form.item_data[set_index] = detail.row_seq;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_DRAWING_NO_NAME;
          $this.form.item_data[set_index] = detail.drawing_no;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_ORDER_COUNT_NAME;
          $this.form.item_data[set_index] = detail.order_count;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_MODEL_NUMBER_NAME;
          $this.form.item_data[set_index] = detail.model_number;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_PRODUCT_NAME;
          $this.form.item_data[set_index] = detail.back_order_product_name;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_OUTLINE_NAME;
          $this.form.item_data[set_index] = detail.outline_name;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_DEVICE_NAME;
          $this.form.item_data[set_index] = detail.device_name;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = C_USER_NAME;
          $this.form.item_data[set_index] = detail.user_name;
          set_index = set_index + 1;
          $this.form.item_name[set_index] = $this.kind_name;
          $this.form.item_data[set_index] = "";
          $this.kind_index = set_index;
          // $this.kindstatus = $this.kind_name;
          $this.work_kind = detail.work_kind;
          // console.log('getThen detail.process_time_h = ' + detail.process_time_h);
          // console.log('getThen detail.process_time_m = ' + detail.process_time_m);
          $this.hour = detail.process_time_h;
          $this.min = detail.process_time_m;
          $this.sec = 0;
        });
        //  作業開始、再開はタイマー起動する
        if (this.form.kind == C_KIND_START) {
          this.startTimer();
        } else {
          this.stopTimer();
        }
        this.form_count = set_index + 1;
        this.setKind();
        if (this.isbtnctrl == '') {
          this.setKindBackColorBottun();
          // this.isbtnctrl = 'top';
        }
        // console.log('getThen in form_count = ' + this.form_count);
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("取得");
        }
      }
    },
    // 更新系正常処理
    putThen(response, eventtext) {
      var messages = [];
      var res = response.data;
      // console.log('putThen res.result = ' + res.result);
      if (res.result) {
        // console.log('putThen this.before_kind = ' + this.before_kind);
        if (this.before_kind == C_KIND_NEXT) {
          // 次工程の場合はcompleteで事前登録しているので2回目nextで登録する  -- todo
          this.before_kind = "";
          this.form.kind = C_KIND_NEXT;
          this.form.process_time_h = "";
          this.form.process_time_m = "";
          // var arrayParams = { form : this.form };
          // this.postRequest("/process_history/put", arrayParams)
          //   .then(response => {
          //     this.putThen(response, "登録");
          //   })
          //   .catch(reason => {
          //     this.serverCatch("作業工程", "登録");
          //   });
        // } else {
        //   messages.push("作業工程を" + eventtext + "しました。");
        //   this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        //   this.getItem();
        }
        messages.push("作業工程を" + eventtext + "しました。");
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        this.getItem();
        //this.isbtnctrl = 'top';

      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("作業工程", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // 作業ステータス設定
    setKind() {
      this.kind_name = "";
      // console.log('setKind this.form.kind = ' + this.form.kind);
      switch (this.form.kind) {
        case C_KIND_START:
          this.kind_name = C_KIND_START_NAME;
          this.kindstatus = this.kind_name;
          this.form.item_name[this.kind_index] = this.kind_name;
          break;
        case C_KIND_STOP:
          this.kind_name = C_KIND_STOP_NAME;
          this.kindstatus = this.kind_name;
          this.form.item_name[this.kind_index] = this.kind_name;
          break;
        case C_KIND_MSTOP:
          this.kind_name = C_KIND_MSTOP_NAME;
          this.kindstatus = this.kind_name;
          this.form.item_name[this.kind_index] = this.kind_name;
          break;
        case C_KIND_COMPLETE:
          this.kind_name = C_KIND_COMPLETE_NAME;
          this.kindstatus = this.kind_name;
          this.form.item_name[this.kind_index] = this.kind_name;
          break;
        case C_KIND_NEXT:
          this.kind_name = C_KIND_NEXT_NAME;
          this.kindstatus = this.kind_name;
      	  target.style.background = '#EEAA00';
          break;
        default:
          this.kind_name = C_KIND_INI_NAME;
          this.kindstatus = this.kind_name;
          this.form.item_name[this.kind_index] = this.kind_name;
      	  // target.style.background = '#FFF';
          break;
      }
      // console.log('setKind out = ' + this.kind_name);
    },
    // 作業ステータス設定
    setKindBackColorBottun(eventtext) {
      // console.log('作業ステータス設定setKindBackColorBottun = ' + this.form.kind);
      // console.log('setKind this.form.kind = ' + this.form.kind);
      switch (this.form.kind) {
        case C_KIND_START:
          this.isbtnctrl = 'top';
          target.style.background = '#80bb60';
          table_cnt3.style.color = '#FFF';
          break;
        case C_KIND_STOP:
          this.isbtnctrl = 'top2';
          target.style.background = '#dd6060';
          table_cnt3.style.color = '#FFF';
          break;
        case C_KIND_MSTOP:
          this.isbtnctrl = 'top2';
      	  target.style.background = '#dd6060';
          break;
        case C_KIND_COMPLETE:
          this.isbtnctrl = 'comple';
          table_cnt3.style.color = '#FFF';
      	  target.style.background = '#6cb2eb';
          break;
        case C_KIND_NEXT:
          this.isbtnctrl = 'top';
          break;
        default:
          this.isbtnctrl = 'top';
      	  //target.style.background = '#FFF';
          break;
      }
    },
  }
};




</script>
<style scoped>
.mw-rem-2 {
    min-width: 2rem;
}
</style>
