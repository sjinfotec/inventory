<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div  v-if="this.displayphase === ''" class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'承認する申請書を選択する'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-100"
                      for="inputGroupSelect01"
                    >申請状況指定</label>
                  </div>
                  <select-generallist
                    v-bind:identification-id="'C031'"
                    v-bind:placeholder-data="'申請状況を選択してください'"
                    v-bind:blank-data="true"
                    v-bind:selected-value="valueselectedsituation"
                    v-on:change-event="situationChange"
                  ></select-generallist>
                </div>
                <message-data v-bind:message-datas="messagedatadoccode" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >申請書類</label>
                  </div>
                  <select-generallist
                    v-bind:identification-id="'C026'"
                    v-bind:placeholder-data="'申請書類を選択してください'"
                    v-bind:blank-data="true"
                    v-bind:selected-value="valueselecteddoccode"
                    v-on:change-event="doccodeChange"
                  ></select-generallist>
                </div>
                <message-data v-bind:message-datas="messagedatadoccode" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:searchclick-event="searchclick"
                  v-bind:btn-mode="'search'"
                  v-bind:is-push="issearchbutton">
                </btn-work-time>
                <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /.row -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- list contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div v-if="this.displayphase === ''" class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'申請一覧表示'"
            v-bind:header-text2="'承認する申請を選択します。申請日の古い順で表示しています。'"
          ></daily-working-information-panel-header>
          <div class="col-md-6 pb-2">
            <message-data v-bind:message-datas="messagedatascopy" v-bind:message-class="'warning'"></message-data>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
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
                          <td class="text-center align-middle w-15">申請者</td>
                          <td class="text-center align-middle w-15">申請部署</td>
                          <td class="text-center align-middle w-15">申請日</td>
                          <td class="text-center align-middle w-15">申請番号</td>
                          <td class="text-center align-middle w-15">申請書類</td>
                          <td class="text-center align-middle w-15">ステータス</td>
                          <td class="text-center align-middle w-15">申請理由</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in array_demand" v-bind:key="item.id">
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input name="'radio' + index" type="radio" class="form-control" v-on:change="radiochange(index)"/>
                            </div>
                          </td>
                          <td class="text-center align-middle">{{item.demand_user_name}}</td>
                          <td class="text-center align-middle">{{item.demand_department_name}}</td>
                          <td class="text-center align-middle">{{item.demand_date_name}}</td>
                          <td class="text-center align-middle">{{item.no}}</td>
                          <td class="text-center align-middle">{{item.doc_code_name}}</td>
                          <td class="text-center align-middle">{{item.status_name}}</td>
                          <td class="text-center align-middle">{{item.demand_reason}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <div class="card-body mb-3 py-0 pt-2 border-top print-none">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:checkdemandclick-event="checkdemandclick"
                  v-bind:btn-mode="'checkdemand'"
                  v-bind:is-push="iseditdemandpush">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /list contentns row -->
    <!-- main contentns row -->
    <div v-if="this.displayphase !== ''" class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="valueselecteddocname"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="col-md-6 pb-2">
            <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
          </div>
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputDemanddate"
                    >申請番号</label>
                  </div>
                  <label class="form-control" >{{ edit.demandno }}</label>
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
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputDemanddate"
                    >申請日</label>
                  </div>
                  <input v-model="edit.demanddate" type="date" readonly="readonly" class="form-control" id="inputDemanddate">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valuedisplayddoccode === '1'" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputOvertimedate"
                    >残業日</label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" readonly="readonly" class="form-control" id="inputOvertimedate">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valuedisplayddoccode !== 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodfrom"
                    >取得期間開始</label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" readonly="readonly" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodto"
                    >取得期間終了</label>
                  </div>
                  <input v-model="edit.getperiodto" type="date" readonly="readonly" class="form-control" id="inputGetperiodto">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <!-- /.panel contents -->
            <div v-if="valuedisplayddoccode === 1" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg">+</button>
                      </span>
                      予定時間入力
                    </h1>
                    <span class="float-sm-right font-size-sm"></span>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top" v-if="demandDetails.length">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">作業項目</td>
                                <td class="text-center align-middle w-10">残業時間開始</td>
                                <td class="text-center align-middle w-10">残業時間終了</td>
                                <td class="text-center align-middle w-10">予定時間</td>
                                <td class="text-center align-middle w-165">申請理由</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in demandDetails" v-bind:key="item.id">
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="item.working_item"
                                      readonly="readonly"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      readonly="readonly"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_from_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      readonly="readonly"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_to_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="text"
                                      readonly="readonly"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].scheduled_time"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      readonly="readonly"
                                      v-model="demandDetails[index].demand_reason"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
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
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valuedisplayddoccode !== 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div :class="errorClassObject('summary')" class="input-group">
                  <div class="input-group-prepend">
                    <label for="inputDemandreason" class="control-label">申請理由</label>
                  </div>
                </div>
                <div>
                  <textarea readonly="readonly" v-model="edit.demandreason" class="form-control" rows="3" id="inputDemandreason" placeholder="申請理由"></textarea>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div :class="errorClassObject('summary')" class="input-group">
                  <div class="input-group-prepend">
                    <label for="inputSendbackreason" class="control-label">差し戻し理由</label>
                  </div>
                </div>
                <div>
                  <textarea v-model="edit.sendbackreason" class="form-control" rows="3" id="inputSendbackreason" placeholder="差し戻し理由"></textarea>
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditsendbackreason" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valueseq !== '99'" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >承認者</label>
                  </div>
                  <select-comfirm
                    v-bind:blank-data="true"
                    v-bind:get-do="1"
                    v-bind:or-final="'0'"
                    v-bind:main-orsub="'1'"
                    v-bind:selected-confirm="valueselectedconfirm"
                    v-bind:placeholder-data="'承認者を選択してください'"
                    v-on:change-event="confirmChange"
                  ></select-comfirm>
                </div>
                <message-data v-bind:message-datas="messagedataconfirm" v-bind:message-class="'messageconfirmkbn'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >最終承認者</label>
                  </div>
                  <select-comfirm
                    v-bind:blank-data="true"
                    v-bind:get-do="1"
                    v-bind:or-final="'99'"
                    v-bind:main-orsub="'1'"
                    v-bind:selected-confirm="valueselectedconfirmfinal"
                    v-bind:placeholder-data="'最終承認者を選択してください'"
                    v-on:change-event="confirmfinalChange"
                  ></select-comfirm>
                </div>
                <message-data v-bind:message-datas="messagedataconfirmfinal" v-bind:message-class="'messageconfirmkbn'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:doapprovalclick-event="doapprovalclick"
                    v-bind:btn-mode="'doapproval'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:sendbackclick-event="sendbackclick"
                    v-bind:btn-mode="'sendback'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:backclick-event="backclick"
                    v-bind:btn-mode="'back'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <div>
              <button id='mail' style="display:none">メール送信</button>
            </div>
          </div>
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";

const dateRE   = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/
const timeRE   = /^[0-9]{2}:[0-9]{2}$/
const timecountRE   = /^([1-9][0-9]{0,2}|0)(\.[0-9]{1,2})?$/

export default {
  name: "MakeDemand",
  data() {
    return {
      getdo: 0,
      valueselectedsituation: 0,
      valueselecteddoccode: 0,
      valuedisplayddoccode: 0,
      servervalueselectedsituation: "",
      servervalueselecteddoccode: "",
      servervaluedisplayddoccode: "",
      fromdate: "",
      valueselecteddocname: "",
      valueseq: "",
      validate: false,
      displayphase: "",
      issendbackpush: true,
      iseditcopypush: true,
      iseditdemandpush: true,
      resresults: [],
      array_demandresult: [],
      array_demand: [],
      array_demanddeatail: [],
      confirmlist: [],
      edit: {
        demandno : "",
        demand_now : "",
        demanddate  : "",
        getperiodfrom   : "",
        getperiodto   : "",
        demandreason   : "",
        sendbackreason   : "",
        confirm : "",
        confirmfinal : ""
      },
      demandDetails: [],
      valueselectedconfirm: "",
      valueselectedconfirmfinal: "",
      messagedatadoccode: [],
      messagedatasserver: [],
      messageeditdemanddate: [],
      messageeditgetperiodfrom: [],
      messageeditgetperiodto: [],
      messageeditsendbackreason: [],
      messagedataconfirm: [],
      messagedataconfirmfinal: [],
      messagedatadetail: [],
      messagedatascopy: [],
      messageshowsearch: false,
      messageconfirmkbn: "warning",
      selecttedrowindex: -1,
      issearchbutton: false
    };
  },
  // マウント時
  mounted() {
    console.log("MakeDemand Component mounted.");
    this.valueselectedsituation = 1;
    this.valueselecteddoccode = 0;
    this.valuedisplayddoccode = 0;
    this.getDemandList();
  },
  computed: {
    validation() {
      const edit = this.edit
      return {
        demanddate: (!!edit.demanddate && dateRE.test(edit.demanddate)),
        getperiodfrom: (!!edit.getperiodfrom && dateRE.test(edit.getperiodfrom))
      }
    },
    isValid() {
      const validation = this.validation
      return Object
        .keys(validation)
        .every(function (key) {
          return validation[key]
       })
     }
  },
  methods: {
    // バリデーション
    checkForm: function(e) {
      this.validate = true;
      this.messagedataClear();
      if (this.array_demand.length > 0 && this.selecttedrowindex == -1) {
        this.messagedatascopy.push("承認する申請を選択してください。");
        this.validate = false;
      }
      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    checkFormDetail: function(e) {
      this.messageconfirmkbn = "warning";
      this.validate = true;
      this.messagedataClear();

      if (this.valueseq != '99') {
        if (!this.valueselectedconfirm && !this.valueselectedconfirmfinal) {
          this.messagedataconfirm.push("承認者または最終承認者は必ず入力してください。");
          this.messagedataconfirmfinal.push("承認者または最終承認者は必ず入力してください。");
          this.validate = false;
        }
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    checkFormSendback: function(e) {
      this.messageconfirmkbn = "warning";
      this.validate = true;
      this.messagedataClear();

      if (!this.edit.sendbackreason) {
        this.messageeditsendbackreason.push("差し戻し理由は必ず入力してください。");
        this.validate = false;
      }
      if (this.valueseq != '99') {
        if (!this.valueselectedconfirm && !this.valueselectedconfirmfinal) {
          this.messagedataconfirm.push("承認者または最終承認者は必ず入力してください。");
          this.messagedataconfirmfinal.push("承認者または最終承認者は必ず入力してください。");
          this.validate = false;
        }
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 承認状況選択が変更された場合の処理
    situationChange: function(value) {
      this.valueselectedsituation = value;
    },
    // 申請書類選択が変更された場合の処理
    doccodeChange: function(value) {
      this.valueselecteddoccode = value;
      this.valuedisplayddoccode = value;
    },
    // 承認者選択が変更された場合の処理
    confirmChange: function(value) {
      this.valueselectedconfirm = value;
    },
    // 最終承認者選択が変更された場合の処理
    confirmfinalChange: function(value) {
      this.valueselectedconfirmfinal = value;
    },
    // 表示ボタンがクリックされた場合の処理
    searchclick: function(e) {
      this.messagedataClear();
      this.getDemandList();
    },
    // 申請確認ボタンがクリックされた場合の処理
    checkdemandclick: function(e) {
      this.messagedataClear();
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.displayphase = "check";
        this.issendbackpush = true;
      }
    },
    // 承認ボタンがクリックされた場合の処理
    doapprovalclick: function(e) {
      this.checkFormDetail(e);
      if (this.validate) {
        this.alertstoreConf("info");
      }
    },
    //差し戻しボタンがクリックされた場合の処理
    sendbackclick: function(e) {
      this.checkFormSendback(e);
      if (this.validate) {
        this.alertsendbackConf("info");
      }
    },
    // 戻るボタンがクリックされた場合の処理
    backclick: function(e) {
      this.displayphase = "";
      this.valuedisplayddoccode = 0;
      this.demanditemClear();
      if (this.valueselecteddoccode.length > 0) {
        this.doccodeChange(this.valueselecteddoccode);
      }
    },
    // ラジオボタンがクリックされた場合の処理
    radiochange: function(index) {
      this.valueselecteddocname = this.array_demandresult[index].array_demand[0].doc_code_name;
      // editセット
      this.selecttedrowindex = index;
      this.edit.demandno = this.array_demandresult[index].array_demand[0].no;
      this.edit.demand_now = this.array_demandresult[index].array_demand[0].demand_now;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].demand_date).format("YYYY-MM-DD");
      this.edit.demanddate = this.dateFormat;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].date_from).format("YYYY-MM-DD");
      this.edit.getperiodfrom = this.dateFormat;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].date_to).format("YYYY-MM-DD");
      this.edit.getperiodto = this.dateFormat;
      this.edit.demandreason = this.array_demandresult[index].array_demand[0].demand_reason;
      this.edit.confirm = this.array_demandresult[index].array_demand[0].nmail_user_code;
      this.valueselectedconfirm = "";
      this.edit.confirmfinal = "";
      // 明細
      for ( var i=0; i<this.array_demandresult[index].array_demandDeatail.length; i++ ) {
        this.demandDetails.push({
          id: this.array_demandresult[index].array_demandDeatail[i].detail_row_no,
          working_item: this.array_demandresult[index].array_demandDeatail[i].detail_working_item,
          time_from_name: this.array_demandresult[index].array_demandDeatail[i].detail_time_from,
          time_to_name: this.array_demandresult[index].array_demandDeatail[i].detail_time_to,
          scheduled_time: this.array_demandresult[index].array_demandDeatail[i].detail_scheduled_time,
          demand_reason: this.array_demandresult[index].array_demandDeatail[i].detail_demand_reason
        });
      }
      this.valuedisplayddoccode = this.array_demandresult[index].array_demand[0].doc_code;
      this.valueseq = this.array_demandresult[index].array_demand[0].seq;
    },
    // 申請一覧取得
    getDemandList() {
      this.array_demandresult = [];
      this.array_demand = [];
      this.array_demanddetail = [];
      this.messageshowsearch = true;
      this.issearchbutton = true;
      if (this.valueselectedsituation == 0) {
        this.servervalueselectedsituation = "";
      } else {
        this.servervalueselectedsituation = this.valueselectedsituation;
      }
      if (this.valueselecteddoccode == 0) {
        this.servervalueselecteddoccode = "";
      } else {
        this.servervalueselecteddoccode = this.valueselecteddoccode;
      }
      this.$axios
        .get("/approval/list_approval", {
          params: {
            situation: this.servervalueselectedsituation,
            doccode: this.servervalueselecteddoccode,
            usercode: "",
            getdo: 1
          }
        })
        .then(response => {
          this.resresults = response.data;
          if (this.resresults.array_demandresult != null) {
            this.array_demandresult = this.resresults.array_demandresult;
          }
          if (this.resresults.array_demand != null) {
            this.array_demand = this.resresults.array_demand;
          }
          if (this.resresults.array_demanddetail != null) {
            this.array_demanddetail = this.resresults.array_demanddetail;
          }
          if (Object.keys(this.array_demand).length > 0) {
            this.iseditcopypush = false;
            this.iseditdemandpush = false;
          } else {
            this.iseditcopypush = true;
            this.iseditdemandpush = true;
          }
          if (this.resresults.messagedata != null) {
            this.messagedatasserver = this.resresults.messagedata;
          }
          this.messageshowsearch = false;
          this.issearchbutton = false;
        })
        .catch(reason => {
          this.alert("error", "申請一覧取得に失敗しました", "エラー");
          this.issearchbutton = false;
          this.messageshowsearch = false;
        });
    },
    errorClassObject(key) {
      return {
        'has-error': (this.validation[key] == false)
      }
    },
    alertstoreConf: function(state) {
      this.$swal({
        title: "確認",
        text: "この内容で承認しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.store("store");
        }
      });
    },
    alertsendbackConf: function(state) {
      this.$swal({
        title: "確認",
        text: "申請を差し戻ししますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.store("sendback");
        }
      });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    // 承認処理
    store: function(kbn) {
      this.edit.confirm = this.valueselectedconfirm;
      this.edit.confirmfinal = this.valueselectedconfirmfinal;
      if (this.valuedisplayddoccode == 0) {
        this.servervaluedisplayddoccode = "";
      } else {
        this.servervaluedisplayddoccode = this.valuedisplayddoccode;
      }
      this.$axios
        .post("/approval/make_approval", {
          doccode: this.servervaluedisplayddoccode,
          demandedit: this.edit,
          kbn: kbn
        })
        .then(response => {
          this.resresults = response.data;
          this.store_result = this.resresults.result;
          this.msg = '';
          if (kbn == "store") {
            this.msg = "承認";
          } else if(kbn == "sendback") {
            this.msg = "申請差し戻し";
          }
          if (this.store_result == true) {
            this.department_name = this.resresults.department_name;
            this.user_name = this.resresults.user_name;
            this.toaddress = this.resresults.toaddress;
            this.array_ccaddress = this.resresults.ccaddress;
            this.alert("success", this.msg + "しました", "承認完了");
            this.isdischargepush = true;
            // 申請者あて
            this.approvalMailSend(kbn);
            console.log('end');
          } else {
            this.alert("error", this.msg + "に失敗しました", "エラー");
          }
          if (this.resresults.messagedata != null) {
            this.messagedatasserver = this.resresults.messagedata;
          }
          this.$forceUpdate();
        })
        .catch(reason => {
          this.alert("error", "承認に失敗しました", "エラー");
        });
    },
    // クリアメソッド
    itemClear: function() {
    },
    // 申請項目クリアメソッド
    demanditemClear: function() {
      this.selecttedrowindex = -1;
      this.demandDetails = [];
      this.edit.demandno = "";
      this.edit.demand_now = "";
      this.edit.demanddate = "";
      this.edit.getperiodfrom = "";
      this.edit.getperiodto = "";
      this.edit.demandreason = "";
      this.edit.sendbackreason = "";
      this.edit.checkreason = "";
      this.edit.confirm = "";
      this.edit.confirmfinal = "";
    },
    // メッセージ領域クリアメソッド
    messagedataClear: function() {
      this.messageeditdemanddate = [];
      this.messageeditgetperiodfrom = [];
      this.messageeditgetperiodto = [];
      this.messageeditsendbackreason = [];
      this.messagedatadetail = [];
      this.messagedataconfirm = [];
      this.messagedataconfirmfinal = [];
      this.messagedatasserver = [];
      this.messagedatadoccode = [];
      this.messagedatascopy = [];
    },
    approvalMailSend: function(kbn) {
      var address, ccAddress, subject, body, hiddenData;
      var sendmail = document.getElementById('mail');
      subject = '件名：';
      if (this.valuedisplayddoccode == 1){
        subject += '残業申請';
      } else if (this.valuedisplayddoccode == 2){
        subject += '休日出勤申請';
      } else if (this.valuedisplayddoccode == 3){
        subject += '休日振替申請';
      } else if (this.valuedisplayddoccode == 4){
        subject += '代休申請';
      } else if (this.valuedisplayddoccode == 5){
        subject += 'シフト変更申請';
      } else if (this.valuedisplayddoccode == 6){
        subject += '有給休暇申請';
      } else if (this.valuedisplayddoccode == 7){
        subject += '遅刻申請';
      } else if (this.valuedisplayddoccode == 8){
        subject += '早退申請';
      } else if (this.valuedisplayddoccode == 9){
        subject += '外出申請書';
      } else if (this.valuedisplayddoccode == 10){
        subject += '欠勤申請';
      }
      subject += '承認';
      body = '';
      if (kbn == "store") {
        body += "依頼のあった以下の申請を承認しました。";
        body += '%0D%0A' + "次の承認者に承認を依頼します。";
      } else if(kbn == "sendback") {
        body += "依頼のあった以下の申請を差し戻ししました。";
      }
      body += '%0D%0A' + '%0D%0A' + '申請番号：' + this.edit.demandno;
      this.dateFormat = moment(this.edit.demanddate).format("YYYY年MM月DD日");
      body += '%0D%0A' + '申請日：' + this.dateFormat;
      body += '%0D%0A' + '承認日：' + this.dateFormat;
      body += '%0D%0A' + '次承認者：（部署）' + this.department_name + '（氏名）' + this.user_name;
      address = this.toaddress;
      ccAddress = "";
      for ( var i=0; i<this.array_ccaddress.length; i++ ) {
        if (ccAddress.length == 0) {
          ccAddress += this.array_ccaddress[i];
        } else {
          ccAddress += "," + this.array_ccaddress[i];
        }
      }

      location.href = 'mailto:' + address + '?cc=' + ccAddress + '&subject=' + subject + '&body=' + body;
    }
  }
};
</script>

