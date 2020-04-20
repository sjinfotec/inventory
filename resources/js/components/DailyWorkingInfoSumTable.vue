<template>
  <div>
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <div class="col-12 p-0">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
              <thead>
                <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'" bgcolor="#e3fbef">
                  <td
                    class="text-center align-middle mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定労働時間 + 残業時間（法定休日労働時間,法定外休日労働時間） + ', '深夜残業','')"
                  >実働時間</td>
                  <td class="text-center align-middle mw-rem-8">所定労働時間</td>
                  <td class="text-center align-middle mw-rem-8">残業時間</td>
                  <td class="text-center align-middle mw-rem-8">深夜残業時間</td>
                  <td class="text-center align-middle mw-rem-8">法定休日労働時間</td>
                  <!-- <td class="text-center align-middle mw-rem-8">法定休日深夜時間</td> -->
                  <td class="text-center align-middle mw-rem-8">法定外休日労働時間</td>
                  <!-- <td class="text-center align-middle mw-rem-8">法定外休日深夜時間</td> -->
                  <td class="text-center align-middle mw-rem-8">深夜労働時間</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle mw-rem-5">出勤者数</td>
                  <td class="text-center align-middle mw-rem-5">外出者数</td>
                  <td class="text-center align-middle mw-rem-5">有給休暇者数</td>
                  <td class="text-center align-middle mw-rem-5">特別休暇者数</td>
                  <td class="text-center align-middle mw-rem-5">早退者数</td>
                  <td class="text-center align-middle mw-rem-5">遅刻者数</td>
                  <td class="text-center align-middle mw-rem-5">欠勤者数</td>
                  <td class="text-center align-middle mw-rem-15">備考</td>
                </tr>
                <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'" bgcolor="#e3fbef">
                  <td
                    class="text-center align-middle mw-rem-5 color-royalblue"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定労働時間 + 残業時間（法定休日労働時間,法定外休日労働時間） + ', '深夜残業','')"
                  >実働時間</td>
                  <td class="text-center align-middle mw-rem-8">所定労働時間</td>
                  <td class="text-center align-middle mw-rem-8">残業時間</td>
                  <td class="text-center align-middle mw-rem-8">深夜残業時間</td>
                  <td class="text-center align-middle mw-rem-8">法定休日労働時間</td>
                  <!-- <td class="text-center align-middle mw-rem-8">法定休日深夜時間</td> -->
                  <td class="text-center align-middle mw-rem-8">法定外休日労働時間</td>
                  <!-- <td class="text-center align-middle mw-rem-8">法定外休日深夜時間</td> -->
                  <td class="text-center align-middle mw-rem-8">深夜労働時間</td>
                  <td class="text-center align-middle mw-rem-8">法定時間</td>
                  <td class="text-center align-middle mw-rem-8">法定外時間</td>
                  <td
                    class="text-center align-middle w-15"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >不就労時間</td>
                  <td class="text-center align-middle mw-rem-5">出勤者数</td>
                  <td class="text-center align-middle mw-rem-5">外出者数</td>
                  <td class="text-center align-middle mw-rem-5">有給休暇者数</td>
                  <td class="text-center align-middle mw-rem-5">特別休暇者数</td>
                  <td class="text-center align-middle mw-rem-5">早退者数</td>
                  <td class="text-center align-middle mw-rem-5">遅刻者数</td>
                  <td class="text-center align-middle mw-rem-5">欠勤者数</td>
                  <td class="text-center align-middle mw-rem-15">備考</td>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td
                    class="text-center align-middle mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定労働時間 + 残業時間（法定休日労働時間,法定外休日労働時間） + ', '深夜残業','')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.legal_working_holiday_hours }}</td>
                  <!-- <td class="text-center align-middle mw-rem-8">{{ calcList.legal_working_holiday_night_overtime_hours }}</td> -->
                  <td class="text-center align-middle mw-rem-8">{{ calcList.out_of_legal_working_holiday_hours }}</td>
                  <!-- <td class="text-center align-middle mw-rem-8">{{ calcList.out_of_legal_working_holiday_night_overtime_hours }}</td> -->
                  <td class="text-center align-middle mw-rem-8">{{ calcList.late_night_working_hours }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_working_status }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_go_out }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_paid_holidays }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_holiday_kubun }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_leave_early }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_late }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_absence }}</td>
                  <td class="text-left align-middlee mw-rem-15"></td>
                </tr>
                <tr
                  v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td
                    class="text-center align-middlee mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('実働時間 = 所定労働時間 + 残業時間（法定休日労働時間,法定外休日労働時間） + ', '深夜残業','')"
                  >{{ calcList.total_working_times }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.regular_working_times }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.legal_working_holiday_hours }}</td>
                  <!-- <td class="text-center align-middle mw-rem-8">{{ calcList.legal_working_holiday_night_overtime_hours }}</td> -->
                  <td class="text-center align-middle mw-rem-8">{{ calcList.out_of_legal_working_holiday_hours }}</td>
                  <!-- <td class="text-center align-middle mw-rem-8">{{ calcList.out_of_legal_working_holiday_night_overtime_hours }}</td> -->
                  <td class="text-center align-middle mw-rem-8">{{ calcList.late_night_working_hours }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.legal_working_times }}</td>
                  <td class="text-center align-middle mw-rem-8">{{ calcList.out_of_legal_working_times }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="edtString"
                    @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','','','')"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_working_status }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_go_out }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_paid_holidays }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_holiday_kubun }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_leave_early }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_late }}</td>
                  <td class="text-center align-middlee mw-rem-5">{{ calcList.total_absence }}</td>
                  <td class="text-left align-middlee mw-rem-15"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row -->
  </div>
</template>
<script>

export default {
  name: "dailyworkinginfotable",
  props: {
    detailOrTotal: {
      type: String,
      default: "detail"
    },
    dateName: {
      type: String,
      default: ""
    },
    calcLists: {
      type: Array
    },
    btnMode: {
      type: String,
      default: ""
    },
    predeterTimeName: {
      type: String,
      default: "残業時間"
    },
    predeterNightTimeName: {
      type: String,
      default: "深夜残業時間"
    },
    predeterTimeSecondName: {
      type: String,
      default: "残業時間"
    },
    predeterNightTimeSecondName: {
      type: String,
      default: "深夜残業"
    },
    // TODO: 本来は .envに記載して取得したい
    apiKey: {
      type: String,
      default: "AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8"
    }
  },
  // マウント時
  mounted() {
    console.log("dailyworkinginfotable  マウント");
  },
  data: function() {
    return {
      edtString: "",
      edtString1: "",
      tipStyle: {
        // 後述のスタイル用オブジェクト
        position: "absolute",
        top: "0px",
        left: "0px"
      },
      dialogVisible: false,
      longitude: "",
      latitude: "",
      record_time: "",
      user_name: "",
      mode_name: ""
    };
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2, value3, value4) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + "\n" + value2;
      }
      if (value3.length > 0) {
        this.edtString = this.edtString + "\n" + value3;
      }
      if (value4.length > 0) {
        this.edtString = this.edtString + "\n" + value4;
      }
    },
    /* １ユーザーに複数レコードが存在する場合
       ２行目以降にはユーザー名を持っていないので、取得する
    */
    getUserNameByIndex(index, name) {
      if (index != 0) {
        for (let i = index; i >= 0; i--) {
          if (this.calcLists[i].user_name != "") {
            return this.calcLists[i].user_name;
          }
        }
      } else {
        return name;
      }
    },
  }
};
</script>
<style lang="scss" scoped>
.svg_img {
  color: #dc143c;
  cursor: pointer;
}

.custom-bg-dark {
  background-color: #606266 !important;
  color: white !important;
}

.text-align-right {
  text-align: right;
}

.text-align-left {
  text-align: left !important;
}

.padding-dis {
  padding: 0.75rem 0rem !important;
}

.color-chartreuse {
  color: chartreuse;
}

table {
   border-collapse: collapse !important;
   border: 1px solid #95c5ed !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}

.mw-rem-8 {
  min-width: 8rem;
}
</style>
