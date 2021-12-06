<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== アップロード部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3 print-none">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'以下のアップロードアイコンで受注残を登録します。'"
            v-bind:header-text2="'セキュリティ上ファイル選択操作が必要となります。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- ファイル選択 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-3 pb-2">
                <button type="button" class="btn btn-lg font-size-rg box" @click="upclick">
                  <img class="icon-size-user mr-2 pb-1" src="/images/upload-icon-1.svg" alt />受注残登録
                </button>
                <input
                  type="file"
                  style="display: none;"
                  ref="uplog"
                  @change="onFileChange"
                  accept=".xlsx"
                />
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ファイル選択 END ---------------- -->
          </div>
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messageStore.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li
                    v-for="(messagestore,index) in messageStore"
                    v-bind:key="index"
                  >{{ messagestore }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== アップロード部 END ========================== -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import moment from "moment";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

export default {
  name: "monthlyworkingtime",
  mixins: [dialogable, checkable, requestable],
  props: {
    authusers: {
      type: Array,
      default: []
    }
  },
  data: function() {
    return {
      messageStore: [],
      eventlogs: [],
      login_user_code: "",
      login_user_role: 1
    };
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers["code"];
    this.login_user_role = this.authusers["role"];
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // ------------------------ イベント処理 ------------------------------------

    // アップロードボタンがクリックされた場合の処理
    upclick: function(e) {
      this.$refs.uplog.click(); // 同じファイルだとイベントが走らない
    },
    // ファイル選択が変更された場合の処理
    onFileChange: function(e) {
      // 受注残ファイルアップロード storage/private
      this.fileUpload(e);
    },
    // ファイルアップロード
    fileUpload(e) {
      const formData = new FormData();
      var file_data = e.target.files[0];
      console.log("fileUpload file_data = " + file_data.name);
      formData.append("file", file_data);
      axios
        .post("/api/backorderUpload", formData)
        .then(response => {
          this.fileStore(file_data.name);
        })
        .catch(reason => {
          this.serverCatch("受注残", "アップロード");
        });
    },
    // ----------------- privateメソッド ----------------------------------

    // ------------------------ サーバー処理 ----------------------------
    // ログ登録処理
    fileStore(filename) {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          var arrayParams = {
            user_code: this.login_user_code,
            file_name: filename
          };
          this.postRequest("/store_backorder/store", arrayParams)
            .then(response => {
              this.$swal.close();
              this.putThenStore(response, "登録");
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("受注残", "登録");
            });
        }
      });
    },

    // ------------------------ 共通処理 ----------------------------
    // 更新系正常処理（明細）
    putThenStore(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("受注残を" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("受注残", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    }
  }
};
</script>
<style scoped>
.box {
  padding: 0.5em 1em;
  margin: 2em 0;
  border-bottom: solid 6px #3f87ce;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25);
  border-radius: 9px;
  font-weight: bold;
}
</style>
