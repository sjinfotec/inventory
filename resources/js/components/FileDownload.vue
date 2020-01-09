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
            <div class="card-header font-size-rg">ダウンロード
            <span class="float-sm-right font-size-sm">該当ダウンロードのリンクをクリックします。</span></div>
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
                        <a v-bind:href="url">打刻インストール（64bitトライアル版）</a>
                      </div>
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

