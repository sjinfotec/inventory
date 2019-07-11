<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form
      method="post"
      :data="form"
      url="/create_department/store"
      @success="addSuccess()"
      @error="error()"
    >
      <fvl-search-select
        :selected.sync="selectId"
        label="部署"
        name="selectId"
        :options="departmentList"
        placeholder="部署を選択すると編集モードになります!"
        :allowEmpty="true"
        :search-keys="['id']"
        option-key="id"
        option-value="name"
      />
      <!-- Text input component -->
      <fvl-input :value.sync="form.name" label="部署名" name="name" />

      <fvl-submit v-if="selectId=='' || selectId==null ">追加</fvl-submit>
      <fvl-submit id="edit" v-if="selectId != ''">編集</fvl-submit>
    </fvl-form>
    <span class="padding-set-small margin-set-top-regular" v-if="selectId != ''">
      <button class="btn btn-danger" @click="del">削除</button>
    </span>
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
  name: "CreateShiftTime",
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
    console.log("UserAdd Component mounted.");
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
