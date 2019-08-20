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
          <div class="card-body pt-2" v-if="errors.length">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <ul class="error-red">
                  <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <fvl-form
              method="post"
              :data="form"
              url="/create_department/store"
              @success="addSuccess()"
              @error="error()"
            >
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >部署</span>
                    </div>
                    <fvl-search-select
                      :selected.sync="selectId"
                      class="p-0"
                      name="selectId"
                      :options="departmentList"
                      placeholder="部署を選択すると編集モードになります"
                      :allowEmpty="true"
                      :search-keys="['code']"
                      option-key="code"
                      option-value="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group" v-if="selectId=='' || selectId==null ">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-120"
                        id="basic-addon1"
                      >部署名</span>
                    </div>
                    <fvl-input
                      type="text"
                      class="form-control"
                      :value.sync="form.name"
                      name="name"
                    />
                  </div>
                </div>
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <div class="btn-group d-flex">
                    <button
                      type="submit"
                      class="btn btn-success"
                      v-if="selectId=='' || selectId==null "
                    >追加する</button>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </fvl-form>
            <!-- /.panel contents -->
            <!-- main contentns row -->
            <div class="row justify-content-between" v-if="details.length ">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="append">+</button>
                      </span>
                      部署一覧
                    </h1>
                    <span class="float-sm-right font-size-sm">登録済みの部署を編集できます</span>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <div class="card-body mb-3 p-0 border-top">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-30">有効期間</td>
                                <td class="text-center align-middle w-35 mw-rem-10">部署名</td>
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
                                <td class="text-center align-middle">
                                  <div class="btn-group">
                                    <button
                                      type="button"
                                      class="btn btn-danger btn-lg font-size-rg"
                                      @click="alertDelConf('info',item.id,index)"
                                    >削除</button>
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
              <!-- col -->
              <div class="col-md-12 pb-2">
                <div class="btn-group d-flex">
                  <button
                    class="btn btn-success"
                    id="edit"
                    @click="FixDepartment()"
                    v-if="selectId != ''"
                  >修正する</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /main contentns row -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import {
  FvlForm,
  FvlInput,
  FvlSelect,
  FvlSearchSelect,
  FvlSubmit
} from "formvuelar";

export default {
  name: "CreateDepartment",
  components: {
    FvlForm,
    FvlInput,
    FvlSelect,
    FvlSearchSelect,
    FvlSubmit,
    FvlSelect,
    getDo: 1
  },
  data() {
    return {
      form: {
        name: "",
        id: ""
      },
      errors: [],
      valuedepartment: "",
      departmentList: [],
      details: [],
      applyTerms: [],
      selectId: "",
      selectApplyTerm: "",
      oldId: ""
    };
  },
  // マウント時
  mounted() {
    this.getDepartmentList();
  },
  watch: {
    selectId: function(val, oldVal) {
      console.log(val + " " + oldVal);
      if (this.selectId != "") {
        this.$axios
          .get("/create_department/get", {
            params: {
              code: this.selectId
            }
          })
          .then(response => {
            this.details = response.data;
            // this.details.push({ apply_term_from: "2019-01-05" });
          })
          .catch(reason => {
            alert("error");
          });
      } else {
        this.inputClear();
      }
    }
  },
  methods: {
    append: function() {
      this.details.push({ apply_term_from: "", code: this.selectId, name: "" });
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
        this.details.splice(index, 1);
      }
    },
    // バリデーション
    checkForm: function() {
      var flag = false;
      this.errors = [];
      this.details.forEach(element => {
        flag = true;
        if (element.apply_term_from == "") {
          this.errors.push("有効期間を入力してください");
          flag = false;
        }
        if (element.name == "") {
          this.errors.push("部署名を入力してください");
          flag = false;
        }
      });
      return flag;
    },
    getDepartmentList() {
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          this.object = { code: "", name: "新規登録" };
          this.departmentList.unshift(this.object);
          console.log("部署リスト取得");
        })
        .catch(reason => {});
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
      this.selectId = "";
    }
  }
};
</script>
<style scoped>
.fvl-search-select {
  height: 36px !important;
  border: 1px solid #ced4da !important;
}
</style>