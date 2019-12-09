<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <!-- /.panel header -->
          <div class="card">
            <div class="card-header font-size-rg">パスワード変更入力
            <span class="float-sm-right font-size-sm">新しいパスワードを入力します。</span></div>
              <div class="card-body">
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
                  <!-- col -->
                  <div class="col-md-12 pb-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span
                          class="input-group-text font-size-sm line-height-sm label-width-230"
                        >新しいパスワード<span class="color-red">[必須]</span></span>
                      </div>
                      <input
                        class="form-control"
                        v-model="enterPass"
                        maxlength="12"
                        type="text"
                        title="半角英数字12文字以内"
                        pattern="^[a-zA-Z0-9]{6,12}$"
                      />
                    </div>
                  </div>
                  <!-- /.col -->
                  <!-- col -->
                  <div class="col-md-12 pb-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span
                          class="input-group-text font-size-sm line-height-sm label-width-230"
                        >新しいパスワード（再入力）<span class="color-red">[必須]</span></span>
                      </div>
                      <input
                        class="form-control"
                        v-model="reEnterPass"
                        maxlength="12"
                        type="password"
                        title="半角英数字12文字以内"
                        pattern="^[a-zA-Z0-9]{6,12}$"
                      />
                    </div>
                    <p class="rf-mini rf-mt-5">※確認のため再入力してください。</p>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- col -->
                  <div class="col-md-12 pb-2">
                    <div class="btn-group d-flex">
                      <button
                        type="button"
                        class="btn btn-primary btn-lg font-size-rg w-100"
                        v-on:click="alertPassConf('warning')"
                      >パスワードを変更</button>
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- main contentns row -->
</template>
<script>
import toasted from "vue-toasted";

export default {
  name: "UserPass",
  data() {
    return {
      enterPass: "",
      reEnterPass: "",
      validate: false,
      errors: []
    };
  },
  // マウント時
  mounted() {
    console.log("UserPass Component mounted.");
  },
  methods: {
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertPassConf: function(state) {
      this.$swal({
        title: "確認",
        text: "パスワードを変更しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.passChange();
        } else {
        }
      });
    },
    // バリデーション
    checkFormPass: function() {
      var flag = false;
      this.errors = [];

      if (this.reEnterPass && this.enterPass) {
        if (this.reEnterPass != this.enterPass) {
          flag = false;
          this.errors.push("入力したパスワードが一致していません");
        } else {
          flag = true;
        }
        return flag;
      } else {
        if (!this.enterPass) {
          flag = false;
          this.errors.push("新しいパスワードを入力してください");
        }
        if (!this.reEnterPass) {
          flag = false;
          this.errors.push("新しいパスワード（再入力）を入力してください");
        }
        return flag;
      }
    },
    passChange: function() {
      this.validate = this.checkFormPass();
      if (this.validate) {
        this.$axios
          .post("/user_pass/passchange", {
            password: this.enterPass
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.alert("success", "パスワードを変更しました", "変更完了");
            } else {
            }
          })
          .catch(reason => {
            this.alert("error", "パスワード変更に失敗しました", "エラー");
          });
      } else {
      }
    },
    inputPassClear() {
      this.enterPass = "";
      this.reEnterPass = "";
    }
  }
};
</script>

