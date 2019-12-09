<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">部署を設定する</h1>
            <span class="float-sm-right font-size-sm">部署の登録や変更ができます</span>
          </div>
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
                    >部署<span class="color-red">[必須]</span></span>
                  </div>
                  <select-department v-if="showdepartmentlist"
                    ref="selectdepartment"
                    v-bind:blank-data="false"
                    v-bind:selected-department="selectedValue"
                    v-bind:add-new="true"
                    v-bind:placeholder-data="'部署を選択すると編集モードになります'"
                    v-on:change-event="departmentChanges"
                  ></select-department>
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
            <div class="row justify-content-between" v-if="errors.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
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
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRow">+</button>
              </span>
              {{ this.selectedName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新たに追加することができます</span>
          </div>
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- .panel -->
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
                              <td class="text-center align-middle w-30">有効期間</td>
                              <td class="text-center align-middle w-35 mw-rem-10">部署名</td>
                              <td class="text-center align-middle w-35 mw-rem-10"></td>
                              <td class="text-center align-middle w-35 mw-rem-10">操作</td>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(item,index) in details" v-bind:key="item.id">
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
                                <div class="input-group">
                                  <input
                                    type="text"
                                    maxlength="50"
                                    class="form-control"
                                    v-model="details[index].name"
                                  />
                                </div>
                              </td>
                              <td class="row justify-content-between" v-if="details[index].result == 1">
                                <!-- panel header -->
                                <col-note
                                  v-bind:item-name="'No.' + (index+1) + ' 現在適用中'"
                                  v-bind:item-control="'LIGHT'"
                                  v-bind:item-note="''"
                                ></col-note>
                                <!-- /.panel header -->
                              </td>
                              <td class="row justify-content-between" v-else>
                                <col-note
                                  v-bind:item-name="'No.' + (index+1)"
                                  v-bind:item-control="'LIGHT'"
                                  v-bind:item-note="''"
                                ></col-note>
                                <!-- /.panel header -->
                              </td>
                              <td class="text-center align-middle">
                                <div class="btn-group">
                                  <button
                                    type="button"
                                    class="btn btn-success"
                                    @click="fix_confirm(n)"
                                  >この内容で更新する</button>
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

export default {
  name: "CreateDepartment",
  data() {
    return {
      form: {
        name: "",
        id: "",
        code: ""
      },
      errors: [],
      valuedepartment: "",
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
      oldId: ""
    };
  },
  // マウント時
  mounted() {
    this.$refs.selectdepartment.getDepartmentList(moment(new Date()).format("YYYYMMDD"));
  },
  methods: {
    // バリデーション
    checkFormStore: function() {
      var flag = true;
      this.errors = [];
      if (this.form.name == "") {
        this.errors.push("部署名を入力してください");
        flag = false;
      }
      return flag;
    },
    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.selectedName = arrayitem['name'];
        this.getDepartment();
      }
    },
    getDepartment() {
      this.details = [];
      this.$axios
        .get("/create_department/get", {
          params: {
            code: this.selectedValue
          }
        })
        .then(response => {
          this.details = response.data;
          this.count = this.details.length;
          this.before_count = this.count;
        })
        .catch(reason => {
          this.alert("error", "部署情報取得に失敗しました", "エラー");
        });
    },
    storeclick() {
      var flag = this.checkFormStore();
      if (flag) {
        this.store_confirm();
      }
    },
    store_confirm: function() {
      this.$swal({
        title: "確認",
        text: "この内容で登録しますか？",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(result  => {
        if (result) {
          this.store("store");
        }
      });
    },
    store(kbn) {
      this.$axios
        .post("/create_department/store", {
          kbn: kbn,
          code: this.form.code,
          name: this.form.name
        })
        .then(response => {
          var res = response.data;
          if (res.result) {
            this.alert("success", "部署を登録しました", "登録完了");
            this.refreshtDepartmentList();
            this.form.code = res.code;
          } else {
            if (res.messagedata.length > 0) {
              this.errors = res.messagedata;
            } else {
              this.alert("error", "部署登録に失敗しました", "エラー");
            }
          }
        })
        .catch(reason => {
          this.alert("error", "部署登録に失敗しました", "エラー");
        });
    },
    appendRow: function() {
      if (this.before_count < this.count) {
          this.alert("error", "１度に追加できる情報は１個です。編集確定してから追加してください。", "エラー");
      } else {
        this.object = { id: "", code: "", name: "", apply_term_from: "", result: "" };
        this.details.unshift(this.object);
        this.count = this.details.length
      }
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    addSuccess() {
      this.$toasted.show("登録しました");
      this.getDepartmentList();
    },
    error() {
      var options = {
        position: "bottom-center",
        duration: 2000,
        fullWidth: false,
        type: "error"
      };
      this.$toasted.show("登録に失敗しました", options);
    },
    FixDepartment() {
      this.validate = this.checkForm();
      if (this.validate) {
        this.$axios
          .post("/create_department/fix", {
            details: this.details
          })
          .then(response => {
            var res = response.data;
            this.alert("success", "部署の修正をしました", "修正完了");
          })
          .catch(reason => {
            this.alert("error", "部署の修正に失敗しました", "エラー");
          });
      } else {
        console.log("fix error");
      }
    },
    // 削除
    del: function(id, index) {
      this.details.splice(index, 1);
      this.$axios
        .post("/create_department/del", {
          id: id
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert(
              "success",
              this.form.name + " を削除しました",
              "削除成功"
            );
            // this.inputClear();
            this.getDepartmentList();
          } else {
          }
        })
        .catch(reason => {});
    },
    inputClear() {
      this.details = [];
      this.form.name = "";
      this.form.id = "";
      this.selectedValue = "";
    },
    refreshtDepartmentList() {
      // 最新リストの表示
      this.showdepartmentlist = false;
      this.$nextTick(() => (this.showdepartmentlist = true));
    },
  }
};
</script>
<style scoped>
.fvl-search-select {
  height: 36px !important;
  border: 1px solid #ced4da !important;
}
</style>