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
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">期首月</span>
                </div>
                <fvl-search-select
                  :selected.sync="selectId"
                  class="p-0"
                  name="selectId"
                  :options="departmentList"
                  placeholder="部署を選択すると編集モードになります"
                  :allowEmpty="true"
                  :search-keys="['id']"
                  option-key="id"
                  option-value="name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-120" id="basic-addon1">部署名</span>
                </div>
                <fvl-input type="text" class="form-control" :value.sync="form.name" name="name"/>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="submit" class="btn btn-success" v-if="selectId=='' || selectId==null ">追加</button>
                <button type="submit" class="btn btn-success" id="edit" v-if="selectId != ''">編集</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          </fvl-form>
          <!-- .row -->
          <div class="row justify-content-between" v-if="selectId != ''">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button class="btn btn-danger" @click="del">削除</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
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
      valuedepartment: "",
      departmentList: [],
      details: [],
      selectId: "",
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
              id: this.selectId
            }
          })
          .then(response => {
            this.details = response.data;
            this.form.name = this.details[0].name;
            this.form.id = this.details[0].id;
            // hidden
            this.oldId = this.details[0].id;

            console.log("部署名取得");
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
    getDepartmentList() {
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          this.object = { id: "", name: "新規登録" };
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
    // 削除
    del: function() {
      var confirm = window.confirm("選択した部署を削除しますか？");
      if (confirm) {
        this.$axios
          .post("/create_department/del", {
            id: this.selectId
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.$toasted.show("選択した部署を削除しました");
              this.inputClear();
              this.getDepartmentList();
            } else {
            }
          })
          .catch(reason => {});
      } else {
      }
    },
    inputClear() {
      this.form.name = "";
      this.form.id = "";
      this.selectId = "";
    }
  }
};
</script>
<style scoped>
.fvl-search-select{
  height: 36px!important;
  border: 1px solid #ced4da!important;
}
</style>