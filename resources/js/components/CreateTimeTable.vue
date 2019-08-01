<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header clearfix bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">タイムテーブルを設定する</h1>
          <span class="float-sm-right font-size-sm">複数の勤務時間を設定することでシフト登録ができます</span>
        </div>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <fvl-form
            method="post"
            :data="form"
            url="/create_time_table/store"
            @success="addSuccess()"
            @error="error()"
          >
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-12 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">タイムテーブル</span>
                </div>
                <fvl-search-select
                  :selected.sync="selectId"
                  class="p-0"
                  name="selectId"
                  :options="timeTableList"
                  placeholder="タイムテーブルを選択すると編集モードになります"
                  :allowEmpty="true"
                  :search-keys="['no']"
                  option-key="no"
                  option-value="name"
                />
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">タイムテーブルNO</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.no" label="タイムテーブルNo" name="no">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2" v-if="selectId != ''">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">タイムテーブルNO</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.no" label="タイムテーブルNo (編集不可)" name="no" readonly="true">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">タイムテーブル名</span>
                </div>
                <input type="text" class="form-control" :value.sync="form.name" label="タイムテーブル名称" name="name">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">所定労働開始時間</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.regularFrom" label="所定労働時間" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">所定労働終了時間</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.regularTo" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header col-12 bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">休憩時間設定</h1>
              <span class="float-sm-right font-size-sm">2パターンまで設定できます</span>
            </div>
            <!-- /.panel header -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">休憩開始時間 A</span>
                </div>
                <input class="form-control" type="time" :value.sync="form.regularRestFrom1" label="所定労働時間内休憩　２パターンまで" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">休憩終了時間 A</span>
                </div>
                <input class="form-control" :value.sync="form.regularRestTo1" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">休憩開始時間 B</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.regularRestFrom2" label="　" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">休憩終了時間 B</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.regularRestTo2" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header col-12 bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">残業時間設定</h1>
              <span class="float-sm-right font-size-sm">3パターンまで設定できます</span>
            </div>
            <!-- /.panel header -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業開始時間 A</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularFrom1" label="所定外労働時間　３パターンまで" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業終了時間 A</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularTo1" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業開始時間 B</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularFrom2" label="　" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業終了時間 B</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularTo2" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業開始時間 C</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularFrom3" label="　" name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">残業終了時間 C</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularTo3" label=" " name="syoteito">
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">深夜残業時間設定</h1>
            </div>
            <!-- /.panel header -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">深夜残業開始時間</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularMidNightFrom"　label="所定外深夜労働時間"　name="syoteifrom">
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-150" id="basic-addon1">深夜残業終了時間</span>
                </div>
                <input type="time" class="form-control" :value.sync="form.irregularMidNightTo"　label=" "　name="syoteito">
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
                <button class="btn btn-danger" @click="alertDelConf('info')">削除</button>
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
  name: "CreateTimeTable",
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
        id: "",
        no: "",
        name: "",
        regularFrom: "",
        regularTo: "",
        regularRestFrom1: "",
        regularRestTo1: "",
        regularRestFrom2: "",
        regularRestTo2: "",
        irregularFrom1: "",
        irregularTo1: "",
        irregularFrom2: "",
        irregularTo2: "",
        irregularFrom3: "",
        irregularTo3: "",
        irregularMidNightFrom: "",
        irregularMidNightTo: ""
      },
      timeTableList: [],
      details: [],
      selectId: "",
      oldId: ""
    };
  },
  // マウント時
  mounted() {
    this.getTimeTableList();
  },
  // セレクトボックス変更時
  watch: {
    selectId: function(val, oldVal) {
      // console.log(val + " " + oldVal);
      if (this.selectId != "") {
        this.getDetail();
      } else {
        this.inputClear();
      }
    }
  },
  methods: {
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertDelConf: function(state) {
      this.$swal({
        title: "確認",
        text: "削除してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.del();
        } else {
        }
      });
    },
    getDetail() {
      this.$axios
        .get("/create_time_table/get", {
          params: {
            no: this.selectId
          }
        })
        .then(response => {
          this.details = response.data;
          this.form.id = this.details[0].id;
          this.form.no = this.details[0].no;
          this.form.name = this.details[0].name;
          this.form.regularFrom = this.details[0].from_time;
          this.form.regularTo = this.details[0].to_time;

          this.form.regularRestFrom1 = this.details[1].from_time;
          this.form.regularRestTo1 = this.details[1].to_time;
          this.form.regularRestFrom2 = this.details[2].from_time;
          this.form.regularRestTo2 = this.details[2].to_time;

          this.form.irregularFrom1 = this.details[3].from_time;
          this.form.irregularTo1 = this.details[3].to_time;
          this.form.irregularFrom2 = this.details[4].from_time;
          this.form.irregularTo2 = this.details[4].to_time;
          this.form.irregularFrom3 = this.details[5].from_time;
          this.form.irregularTo3 = this.details[5].to_time;

          this.form.irregularMidNightFrom = this.details[6].from_time;
          this.form.irregularMidNightTo = this.details[6].to_time;

          // hidden
          this.oldId = this.details[0].id;
        })
        .catch(reason => {
          alert("詳細取得でエラーが発生しました");
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
          alert("リスト取得エラー");
        });
    },
    addSuccess() {
      this.alert("success", "登録しました", "登録成功");
      this.selectId = this.form.no;
      this.getTimeTableList();
      this.getDetail();
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    // 削除
    del: function() {
      this.$axios
        .post("/create_time_table/del", {
          no: this.selectId
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert(
              "success",
              "選択したタイムテーブルを削除しました",
              "削除"
            );
            this.inputClear();
            this.getTimeTableList();
          } else {
          }
        })
        .catch(reason => {
          this.alert("error", "削除でエラーが発生しました", "エラー");
        });
    },
    inputClear() {
      this.form.name = "";
      this.form.id = "";
      this.form.no = "";
      this.selectId = "";
      this.form.regularFrom = "";
      this.form.regularTo = "";

      this.form.regularRestFrom1 = "";
      this.form.regularRestTo1 = "";
      this.form.regularRestFrom2 = "";
      this.form.regularRestTo2 = "";

      this.form.irregularFrom1 = "";
      this.form.irregularTo1 = "";
      this.form.irregularFrom2 = "";
      this.form.irregularTo2 = "";
      this.form.irregularFrom3 = "";
      this.form.irregularTo3 = "";

      this.form.irregularMidNightFrom = "";
      this.form.irregularMidNightTo = "";
    }
  }
};
</script>
<style scoped>
.padding-top-l {
  padding-top: 50px;
}

.padding-left-l {
  padding-left: 25px;
}

.padding-top-m {
  padding-top: 20px;
}
</style>
