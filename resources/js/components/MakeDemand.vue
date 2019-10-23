<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div  v-if="this.displayphase === ''" class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'申請する申請書を選択する'"
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
                      class="input-group-text font-size-sm line-height-xs label-width-90"
                      for="inputGroupSelect01"
                    >申請書類<span class="color-red">[*]</span></label>
                  </div>
                  <general-list
                    v-bind:identification-id="'C026'"
                    v-bind:placeholder-data="'申請書類を選択してください'"
                    v-bind:blank-data="true"
                    v-on:change-event="doccodeChange"
                  ></general-list>
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
                  v-on:makedemandclick-event="makedemandclick"
                  v-bind:btn-mode="'makedemand'">
                </btn-work-time>
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
            v-bind:header-text2="'直近１０件の申請書を表示。コピーで作成できます。'"
          ></daily-working-information-panel-header>
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
                          <td class="text-center align-middle w-15">申請日</td>
                          <td class="text-center align-middle w-15">申請番号</td>
                          <td class="text-center align-middle w-15">申請書類</td>
                          <td class="text-center align-middle w-15">ステータス</td>
                          <td class="text-center align-middle w-15">申請理由</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in details" v-bind:key="item.id">
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input type="radio" class="form-control" v-model="picked" />
                            </div>
                          </td>
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
                  v-on:editcopyclick-event="editcopyclick"
                  v-bind:btn-mode="'editcopy'"
                  v-bind:is-push="iseditcopypush">
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
                  v-on:editdemandclick-event="editdemandclick"
                  v-bind:btn-mode="'editdemand'"
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
    <div v-if="this.displayphase != ''" class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-body mb-3 py-0 pt-4 border-top">
            <legend>{{ valueselecteddocname }}</legend>
          </div>
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
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputRelease"
                    >申請日<span class="color-red">[*]</span></label>
                  </div>
                  <input v-model="edit.release" type="date" class="form-control" id="inputRelease">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valueselecteddoccode === 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputRelease"
                    >残業日<span class="color-red">[*]</span></label>
                  </div>
                  <input v-model="edit.release" type="date" class="form-control" id="inputRelease">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="valueselecteddoccode === 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputRelease"
                    >残業時間開始<span class="color-red">[*]</span></label>
                  </div>
                  <input v-model="edit.release" type="time" class="form-control" id="inputRelease">
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputRelease"
                    >残業時間終了<span class="color-red">[*]</span></label>
                  </div>
                  <input v-model="edit.release" type="time" class="form-control" id="inputRelease">
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <!-- /.panel contents -->
            <div v-if="valueselecteddoccode === 1" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="append">+</button>
                      </span>
                      予定時間入力
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで申請情報を追加できます</span>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">作業項目<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-10">残業時間開始<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-10">残業時間終了<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-10">予定時間<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-165">申請理由<span class="color-red">[*]</span></td>
                                <td class="text-center align-middle w-15">操作</td>
                              </tr>
                            </thead>
                            <tbody>
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
            <div v-if="valueselecteddoccode !== 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div :class="errorClassObject('summary')" class="input-group">
                  <div class="input-group-prepend">
                    <label for="inputSummary" class="control-label">申請理由<span class="color-red">[*]</span></label>
                  </div>
                </div>
                <div>
                  <textarea v-model="edit.summary" class="form-control" rows="3" id="inputSummary" placeholder="申請理由"></textarea>
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
                      for="inputGroupSelect01"
                    >承認者<span class="color-red">[*]</span></label>
                  </div>
                  <general-list
                    v-bind:identification-id="'C026'"
                    v-bind:placeholder-data="'承認者を選択してください'"
                    v-bind:blank-data="true"
                    v-on:change-event="doccodeChange"
                  ></general-list>
                </div>
                <message-data v-bind:message-datas="messagedatadoccode" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >最終承認者<span class="color-red">[*]</span></label>
                  </div>
                  <general-list
                    v-bind:identification-id="'C026'"
                    v-bind:placeholder-data="'最終承認者を選択してください'"
                    v-bind:blank-data="true"
                    v-on:change-event="doccodeChange"
                  ></general-list>
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
          </div>
          <!-- /.panel contents -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:dodemandclick-event="dodemandclick"
                    v-bind:btn-mode="'dodemand'">
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
                    v-on:dischargeclick-event="dischargeclick"
                    v-on:is-push="isdischargepush"
                    v-bind:btn-mode="'discharge'">
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
const isbn10RE = /^[0-9]{9}[0-9X]$/
const dateRE   = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/

export default {
  name: "MakeDemand",
  data() {
    return {
      getdo: 0,
      valueselecteddoccode: "",
      fromdate: "",
      valueselecteddocname: "",
      validate: false,
      messagedatadoccode: [],
      messagedatasserver: [],
      messageshowsearch: false,
      displayphase: "",
      isdischargepush: true,
      iseditcopypush: true,
      iseditdemandpush: true,
      resresults: [],
      details: [],
      edit: {
        title  : "",
        summary: "",
        isbn   : "",
        release: ""
      },
      maxLength: 10,
      employStatusList: [],
      timeTableList: [],
      userDetails: [],
      generalList_m: [],
      generalList_r: [],
      userCode: "",
      departmentCode: "",
      errors: [],
      oldCode: "",
      cardId: "",
      oldPass: ""
    };
  },
  // マウント時
  mounted() {
    console.log("MakeDemand Component mounted.");
  },
  computed: {
    validation() {
      const edit = this.edit
      return {
        title  : (!!edit.title && edit.title.length <= this.maxLength),
        summary: (!!edit.summary),
        isbn   : (!!edit.isbn    && isbn10RE.test(edit.isbn)),
        release: (!!edit.release && dateRE.test(edit.release))
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
      this.messagedatasserver = [];
      this.messagedatadoccode = [];
      if (!this.valueselecteddoccode) {
        this.messagedatadoccode.push("申請書類は必ず選択してください。");
        this.validate = false;
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 申請書類選択が変更された場合の処理
    doccodeChange: function(value, name) {
      this.valueselecteddoccode = value;
      this.valueselecteddocname = name;
      this.getDo = 1;
      // 申請一覧取得
      if (this.valueselecteddoccode) {
        this.getDemandList();
      } else {
        this.details = [];
      }
    },
    // 新規作成ボタンがクリックされた場合の処理
    makedemandclick: function(e) {
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.displayphase = "make";
      }
    },
    // 編集ボタンがクリックされた場合の処理
    editdemandclick: function(e) {
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.displayphase = "edit";
      }
    },
    // 複写作成ボタンがクリックされた場合の処理
    editcopyclick: function(e) {
    },
    // 申請ボタンがクリックされた場合の処理
    dodemandclick: function(e) {
    },
    // 取り下げボタンがクリックされた場合の処理
    dischargeclick: function(e) {
      this.displayphase = "";
    },
    // 戻るボタンがクリックされた場合の処理
    backclick: function(e) {
      this.displayphase = "";
    },
    // 申請一覧取得
    getDemandList() {
      this.$axios
        .get("/demand/list_demand", {
          params: {
            doccode: this.valueselecteddoccode,
            usercode: "",
            getdo: 1
          }
        })
        .then(response => {
          this.resresults = response.data;
          if (this.resresults.demands != null) {
            this.details = this.resresults.demands;
            if (Object.keys(this.details).length > 0) {
              this.iseditcopypush = false;
              this.iseditdemandpush = false;
            } else {
              this.iseditcopypush = true;
              this.iseditdemandpush = true;
            }
          }
          if (this.resresults.messagedata != null) {
            this.messagedatasserver = this.resresults.messagedata;
          }
        })
        .catch(reason => {
          this.alert("error", "申請一覧取得に失敗しました", "エラー");
        });
    },
    errorClassObject(key) {
      return {
        'has-error': (this.validation[key] == false)
      }
    },

    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertDelConf: function(state, id, index) {
      if (id >= 0) {
        this.$swal({
          title: "確認",
          text: "削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.del(id, index);
          } else {
          }
        });
      } else {
        this.userDetails.splice(index, 1);
      }
    },
    ReleaseCardInfo: function(state) {
      this.$swal({
        title: "ユーザーに紐づいているICカードを解除します",
        text: "解除しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.$axios
            .post("/user_add/release_card_info", {
              card_idm: this.cardId
            })
            .then(response => {
              var res = response.data;
              if (res.result == 0) {
                this.alert("success", "カードを解除しました", "解除完了");
                this.cardId = "";
                // this.get;
              } else {
              }
            })
            .catch(reason => {});
        } else {
        }
      });
    },
    // 削除
    del: function(id, index) {
      this.userDetails.splice(index, 1);
      this.$axios
        .post("/user_add/del", {
          id: id
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", " 行削除しました", "削除成功");
            // this.getDepartmentList();
          } else {
          }
        })
        .catch(reason => {});
    },
    getEmploymentStatusList() {
      this.$axios
        .get("/get_employment_status_list")
        .then(response => {
          this.employStatusList = response.data;
          console.log("雇用形態リスト取得");
        })
        .catch(reason => {
          this.alert("error", "雇用形態リスト取得に失敗しました", "エラー");
        });
    },
    getTimeTableList() {
      this.$axios
        .get("/get_time_table_list")
        .then(response => {
          this.timeTableList = response.data;
          console.log("タイムテーブルリスト取得");
        })
        .catch(reason => {
          this.alert("error", "タイムテーブルリスト取得に失敗しました", "エラー");
        });
    },
    getGeneralList(value) {
      this.$axios
        .get("/get_general_list", {
          params: {
            identificationid: value
          }
        })
        .then(response => {
          if (value == "C017") {
            this.generalList_m = response.data;
          }
          if (value == "C025") {
            this.generalList_r = response.data;
          }
        })
        .catch(reason => {
          this.alert("error", "勤怠権限リスト取得に失敗しました", "エラー");
        });
    },
    addSuccess() {
      this.getUserList(1, null);
      this.$toasted.show("登録しました");
      this.getUserList(1, null);
      this.userCode = "";
    },
    getUserList(getdovalue, value) {
      console.log("getdovalue = " + getdovalue);
      this.$axios
        .get("/get_user_list", {
          params: {
            getdo: getdovalue,
            code: value
          }
        })
        .then(response => {
          this.userList = response.data;
          this.object = { code: "", name: "新規登録" };
          this.userList.unshift(this.object);
          console.log("ユーザーリスト取得");
        })
        .catch(reason => {
          alert("ユーザーリスト取得エラー");
        });
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    // クリアメソッド
    itemClear: function() {
    }
  }
};
</script>

