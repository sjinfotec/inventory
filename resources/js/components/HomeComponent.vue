<template>
  <div class="col-xl-12">
    <!-- main contentns row -->
    <div class="d-flex flex-row flex-wrap align-content-between">
      <div class="p-4">
        <a class href="/monthly">
          <img width="120" height="120" class src="/images/icon01.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/daily">
          <img width="120" height="120" class src="/images/icon02.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/daily_alert">
          <img width="120" height="120" class src="/images/icon03.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/monthly_alert">
          <img width="120" height="120" class src="/images/icon04.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/demand">
          <img width="120" height="120" class src="/images/icon05.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/approval">
          <img width="120" height="120" class src="/images/icon06.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/confirm">
          <img width="120" height="120" class src="/images/icon07.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/setting_shift_time">
          <img width="120" height="120" class src="/images/icon08.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/edit_work_times">
          <img width="120" height="120" class src="/images/icon09.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/edit_attendancelog">
          <img width="120" height="120" class src="/images/icon10.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/user_pass">
          <img width="120" height="120" class src="/images/icon11.svg" @click="test()" alt />
        </a>
      </div>
      <div class="p-4">
        <a class href="/file_download">
          <img width="120" height="120" class src="/images/icon12.svg" @click="test()" alt />
        </a>
      </div>
    </div>
    <div class="d-flex flex-row justify-content">
      <div class="card flex-fill">
        <div class="card-header bg-color">
          <!-- <img class="icon-size-sm svg_img orange600" src="/images/info-32.png" alt />打刻エラー -->
          <i class="fa fa-exclamation-triangle my-red fa-lg fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bold">対応が必要となる項目</span>
        </div>
        <div class="card-body">
          <h5 class="card-title">【打刻エラー】</h5>
          <p class="card-text">情報処理課 武田大蔵 さん 退勤エラー</p>
          <p class="card-text">営業部 田口覚 さん 退勤状態から公用外出を開始</p>
        </div>
      </div>
      <div class="card flex-fill margin-left-small">
        <div class="card-header bg-color">
          <i class="fa fa-bullhorn fa-lg my-orange fa-fw" aria-hidden="true"></i>
          <span class="font-weight-bolder">お知らせ</span>
        </div>
        <div class="card-body">
          <div v-if="informations" v-for="(item,index) in informations">
            <span v-html="item.content"></span>
          </div>
          <div class="float-right">
            <button class="btn btn-primary" @click="makeInformation()">作成</button>
          </div>
        </div>
      </div>
    </div>
    <el-dialog custom-class v-bind:title="'三条印刷からのお知らせ'" :visible.sync="dialogVisible" width="80%">
      <div class="card">
        <div class="card-header">
          <i class="fa fa-edit my-orange fa-lg fa-fw" aria-hidden="true"></i>お知らせ入力
        </div>
        <div class="card-body">
          <textarea
            id="textContents"
            type="text"
            name="content"
            v-model="content"
            cols="50"
            rows="10"
            wrap="hard"
            placeholder="html形式でお知らせ内容を入力してください"
            class="form-control"
          ></textarea>
          <div>
            <table class="table">
              <thead>
                <tr>
                  <th>内容</th>
                  <th>日時</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in informations">
                  <td v-html="row.content"></td>
                  <td>{{row.created_at}}</td>
                  <td>
                    <el-button type="danger" @click="delPostInformations(row.id)">削除</el-button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer text-align-right padding-dis">
            <el-button type="danger" @click="dialogVisible = false">閉じる</el-button>
            <!-- 下記ボタンは三条印刷ユーザーのみ表示 -->
            <el-button type="primary" @click="postInformation()">作成する</el-button>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import toasted from "vue-toasted";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";

export default {
  name: "Home",
  mixins: [dialogable, checkable, requestable],
  data() {
    return {
      details: [],
      informations: [],
      content: "",
      dialogVisible: false
    };
  },
  // マウント時
  mounted() {
    this.getPostInformations();
  },
  methods: {
    // -------------------- 共通 ----------------------------
    // お知らせ取得
    getPostInformations() {
      this.$axios
        .get("/get_post_informations")
        .then(response => {
          this.informations = response.data;
          console.log("お知らせ取得");
        })
        .catch(reason => {
          console.log(reason);
          alert("error");
        });
    },
    // お知らせ登録
    postInformation() {
      axios
        .post("/insert_post_informations", {
          content: this.content
        })
        .then(res => {
          this.dialogVisible = false;
          this.$toasted.show("お知らせ登録しました。");
          this.getPostInformations();
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.dialogVisible = false;
        });
    },
    // お知らせ削除
    delPostInformations(id) {
      axios
        .post("/del_post_informations", {
          id: id
        })
        .then(res => {
          this.$toasted.show("削除しました。");
          this.getPostInformations();
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.dialogVisible = false;
        });
    },
    makeInformation() {
      this.dialogVisible = true;
    },
    hide: function() {
      this.$modal.hide("hello-world");
    },
    show: function() {
      this.$modal.show("hello-world");
    }
  }
};
</script>
<style scoped>
.font-color-black {
  color: black;
}
.margin-top-regular {
  margin-top: 30px;
}
.margin-left-small {
  margin-left: 15px;
}
.my-red {
  color: red;
}
.my-skyblue {
  color: skyblue;
}
.my-orange {
  color: #fecb81;
}
.bg-color {
  background-color: floralwhite;
}
.padding-dis {
  padding: 0.75rem 0rem !important;
}
</style>
