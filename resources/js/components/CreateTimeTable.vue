<template>
  <!-- panel body -->
  <div class="panel-body">
    <!-- form wrapper -->
    <fvl-form
      method="post"
      :data="form"
      url="/create_time_table/store"
      @success="addSuccess()"
      @error="error()"
    >
      <fvl-search-select
        :selected.sync="selectId"
        label="タイムテーブル"
        name="selectId"
        :options="timeTableList"
        placeholder="タイムテーブルを選択すると編集モードになります"
        :allowEmpty="true"
        :search-keys="['no']"
        option-key="no"
        option-value="name"
      />
      <!-- Text input component -->
      <fvl-input :value.sync="form.no" label="タイムテーブルNo" name="no" />
      <fvl-input :value.sync="form.name" label="タイムテーブル名称" name="name" />
      <!-- 所定労働時間 -->
      <div class="row">
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.regularFrom" label="所定労働時間" name="syoteifrom" />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.regularTo" label=" " name="syoteito" />
          </div>
        </div>
      </div>
      <!-- /所定労働時間 -->
      <!-- 所定労働時間休憩 -->
      <div class="row">
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input
              type="time"
              :value.sync="form.regularRestFrom1"
              label="所定労働時間休憩　２パターンまで"
              name="syoteifrom"
            />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.regularRestTo1" label=" " name="syoteito" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.regularRestFrom2" label="　" name="syoteifrom" />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.regularRestTo2" label=" " name="syoteito" />
          </div>
        </div>
      </div>
      <!-- /所定労働時間休憩 -->
      <!-- 所定外労働時間 -->
      <div class="row">
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input
              type="time"
              :value.sync="form.irregularFrom1"
              label="所定外労働時間　３パターンまで"
              name="syoteifrom"
            />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.irregularTo1" label=" " name="syoteito" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.irregularFrom2" label="　" name="syoteifrom" />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.irregularTo2" label=" " name="syoteito" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.irregularFrom3" label="　" name="syoteifrom" />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input type="time" :value.sync="form.irregularTo3" label=" " name="syoteito" />
          </div>
        </div>
      </div>
      <!-- /所定外労働時間 -->
      <!-- 所定外深夜労働時間 -->
      <div class="row">
        <div class="col-sm-4">
          <div class="inner-box">
            <fvl-input
              type="time"
              :value.sync="form.irregularMidNightFrom"
              label="所定外深夜労働時間"
              name="syoteifrom"
            />
          </div>
        </div>
        <div class="col-sm-1 padding-top-l padding-left-l">
          <div class="inner-box">
            <label>～</label>
          </div>
        </div>
        <div class="col-sm-4 padding-top-m">
          <div class="inner-box">
            <fvl-input
              type="time"
              :value.sync="form.irregularMidNightTo"
              label=" "
              name="syoteito"
            />
          </div>
        </div>
      </div>
      <!-- /所定外深夜労働時間 -->
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
    getDetail() {
      this.$axios
        .get("/create_time_table/get", {
          params: {
            no: this.selectId
          }
        })
        .then(response => {
          this.details = response.data;
          console.log(this.details);
          this.form.id = this.details[0].id;
          // console.log(this.details[0].no);
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
          alert("error");
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
      this.$toasted.show("登録しました");
      this.getTimeTableList();
      this.inputClear();
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
          .post("/create_time_table/del", {
            no: this.selectId
          })
          .then(response => {
            var res = response.data;
            if (res.result == 0) {
              this.$toasted.show("選択した部署を削除しました");
              this.inputClear();
              this.getTimeTableList();
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
