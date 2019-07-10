<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form method="post" :data="form" url="/user_add/store" @success="addSuccess()" @error="error()">
     <fvl-search-select :selected.sync="selectId" label="部署" name="selectId"
      :options="departmentList"
      placeholder="部署を選択すると編集モードになります!"
      :allowEmpty="true"
      :search-keys="['id']"
      option-key="id"
      option-value="name"/>
      <!-- Text input component -->
      <fvl-input :value.sync="form.name" label="部署名" name="name" />
     
    <fvl-submit v-if="selectId=='' || selectId==null ">追加</fvl-submit>
    </fvl-form>
    <span class="padding-set-small margin-set-top-regular" v-if="selectId != ''" v-model="form.selectId">
      <button class="btn btn-warning" @click="edit">編集</button>
      <button class="btn btn-danger" @click="del">削除</button>
    </span>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { FvlForm, FvlInput, FvlSelect, FvlSearchSelect, FvlSubmit } from 'formvuelar'

export default {
  name: "CreateShiftTime",
  components: {
        FvlForm,
        FvlInput,
        FvlSelect,
        FvlSearchSelect,
        FvlSubmit,
        FvlSelect,
        getDo: 1,
    },
  data() {
    return {
      form: {
        name:"",
        id:""
      },
      valuedepartment: '',
      departmentList:[],
      details:[],
      selectId:"",
      oldId:""
    };
  },
  // マウント時
  mounted() {
    console.log("UserAdd Component mounted.");
    this.getDepartmentList();
  },
  watch: {
      selectId: function (val, oldVal) {
        console.log(val+" "+oldVal);
        if(this.selectId != ""){
          this.$axios
            .get("/user_add/get", {
              params: {
                id: this.selectId
              }
            })
            .then(response => {
              this.details = response.data;
              this.form.name = this.details[0].name;
              // hidden
              this.oldCode = this.details[0].id;
              
              console.log("ユーザー詳細情報取得");
            })
            .catch(reason => {
              alert("error");
            });
        }else{
          this.inputClear();
        }
      }
  },
  methods: {
    getDepartmentList(){
      this.$axios
        .get("/get_departments_list")
        .then(response => {
          this.departmentList = response.data;
          console.log("部署リスト取得");
        })
        .catch(reason => {
          alert("error");
        });
    },
    addSuccess(){
      this.$toasted.show("部署を追加しました");
    },
    error(){
      var options = {
          position: "bottom-center",
          duration: 2000,
          fullWidth: false,
          type: "error"
        };
      this.$toasted.show("部署追加に失敗しました",options);
    },
    edit: function (){
      var confirm = window.confirm("編集内容を確定しますか？");
      if(confirm){
        this.$axios
        .post("/user_add/edit", {
            old_code: this.oldCode,
            code: this.form.code,
            name: this.form.name,
          })
          .then(response => {
            var res = response.data;
            if(res.result == 0){
              this.$toasted.show("編集内容を確定しました");
              this.getUserList(1,null);
            }else{
            }
          })
          .catch(reason => {
          });
      }else{
      }
    },
    // 削除
    del: function () {
      var confirm = window.confirm("選択した部署を削除しますか？");
      if(confirm){
        this.$axios
        .post("/user_add/del", {
            user_code: this.userCode
          })
          .then(response => {
            var res = response.data;
            if(res.result == 0){
              this.$toasted.show("選択した部署を削除しました");
              this.inputClear();
              this.getUserList(1,null);
            }else{
            }
          })
          .catch(reason => {
          });
      }else{
      }
    },
    inputClear(){
      this.form.name = "";
      this.form.code = "";
    }
  }
};
</script>
