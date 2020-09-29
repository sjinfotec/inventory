<template>
  <div>
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <div class="col-12 p-0">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
              <thead>
                <tr v-if="detailOrTotal === 'detail'" bgcolor="#e3f0fb">
                  <td class="text-center align-middle mw-rem-6">部署</td>
                  <td class="text-center align-middle mw-rem-7">雇用形態</td>
                  <td class="text-center align-middle mw-rem-7">氏名</td>
                  <td class="text-center align-middle mw-rem-2-5">出勤</td>
                  <td class="text-center align-middle mw-rem-2-5">退勤</td>
                  <td class="text-center align-middle mw-rem-2-5">公外</td>
                  <td class="text-center align-middle mw-rem-2-5">公外戻</td>
                  <td class="text-center align-middle mw-rem-2-5">私外</td>
                  <td class="text-center align-middle mw-rem-2-5">私外戻</td>
                  <!-- <td class="text-center align-middle mw-rem-5">勤務状態</td>
                  <td class="text-center align-middle mw-rem-10">勤務帯</td> -->
                  <td
                    class="text-center align-middle mw-rem-2-5 color-royalblue"
                  >実働</td>
                  <!--  <td class="text-center align-middle css-fukidashi"
                    @mouseover="edttooltips('実働時間 = 所定 + ',predeterTimeName,predeterNightTimeName,'')">
                    <span class="text">実働時間</span>
                    <span class="fukidashi">{{ edtString }}</span>
                  </td>-->
                  <td class="text-center align-middle mw-rem-2-5">所定</td>
                  <!-- <td class="text-center align-middle mw-rem-5">{{ predeterTimeSecondName }}</td>
                  <td class="text-center align-middle mw-rem-5">{{ predeterNightTimeSecondName }}</td>-->
                  <td class="text-center align-middle mw-rem-4">残業時間</td>
                  <td class="text-center align-middle mw-rem-4">深夜残業</td>
                  <td class="text-center align-middle mw-rem-5">法定休日</td>
                  <!-- <td class="text-center align-middle mw-rem-5">法休深夜</td> -->
                  <td class="text-center align-middle mw-rem-5">法定外休日</td>
                  <!-- <td class="text-center align-middle mw-rem-5">法外休深夜</td> -->
                  <td class="text-center align-middle mw-rem-3">深夜労働</td>
                  <td
                    class="text-center align-middle mw-rem-5 color-royalblue"
                  >不就労時間</td>
                  <td class="text-center align-middle mw-rem-15">備考</td>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="detailOrTotal === 'detail'"
                  v-for="(calcList,index) in calcLists"
                >
                  <td class="text-left align-middle mw-rem-6">{{ calcList.department_name }}</td>
                  <td class="text-left align-middle mw-rem-7">{{ calcList.employment_status_name }}</td>
                  <td class="text-left align-middle mw-rem-7">{{ calcList.user_name }}</td>
                  <!-- 出勤 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.attendance_time }}
                  </td>
                  <!-- /出勤 -->
                  <!-- 退勤 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.leaving_time }}
                  </td>
                  <!-- /退勤 -->
                  <!-- 公用外出　開始 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.public_going_out_time }}
                  </td>
                  <!-- /公用外出　終了 -->
                  <!-- 公用外出戻り　開始 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.public_going_out_return_time }}
                  </td>
                  <!-- /公用外出戻り　終了 -->
                  <!-- 私用外出　開始 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.missing_middle_time }}
                  </td>
                  <!-- /私用外出　終了 -->
                  <!-- 私用外出戻り　開始 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-if="calcList.holiday_description === '1日集計対象休暇'"
                  >
                  </td>
                  <td
                    class="text-center align-middle mw-rem-2-5"
                    v-else
                  >
                    {{ calcList.missing_middle_return_time }}
                  </td>
                  <!-- /私用外出戻り　終了 -->
                  <!-- 実働 -->
                  <td
                    class="text-center align-middle mw-rem-2-5"
                  >{{ calcList.total_working_times }}</td>
                  <!-- /実働時間 -->
                  <!-- 所定労働時間 -->
                  <td class="text-center align-middle mw-rem-2-5">{{ calcList.regular_working_times }}</td>
                  <!-- /所定労働時間 -->
                  <!-- 時間外労働時間 -->
                  <td
                    class="text-center align-middle mw-rem-4"
                    v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                  >{{ calcList.off_hours_working_hours }}</td>
                  <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                  <!-- /時間外労働時間 -->
                  <!-- 深夜残業時間 -->
                  <td
                    class="text-center align-middle mw-rem-4"
                    v-if="calcList.business_kubun !== '2' && calcList.business_kubun !== '3'"
                  >{{ calcList.late_night_overtime_hours }}</td>
                  <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                  <!-- /深夜残業時間 -->
                  <!-- 法定休日労働時間 -->
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-if="calcList.business_kubun === '2'"
                  >{{ calcList.legal_working_holiday_hours }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else-if="calcList.business_kubun === ''"
                  ></td>
                  <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                  <!-- /法定休日労働時間 -->
                  <!-- 法定休日深夜残業時間 -->
                  <!-- <td
                    class="text-center align-middle mw-rem-5"
                    v-if="calcList.business_kubun === '2'"
                  >{{ calcList.legal_working_holiday_night_overtime_hours }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else-if="calcList.business_kubun === ''"
                  ></td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else
                  >00:00</td>-->
                  <!-- /法定休日深夜残業時間 -->
                  <!-- 法定外（所定休日）休日労働時間 -->
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-if="calcList.business_kubun === '3'"
                  >{{ calcList.out_of_legal_working_holiday_hours }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else-if="calcList.business_kubun === ''"
                  ></td>
                  <td class="text-center align-middle mw-rem-5" v-else>00:00</td>
                  <!-- /法定外（所定休日）休日労働時間
                  <!-- 法定外（所定休日）休日深夜残業時間-->
                  <!-- <td
                    class="text-center align-middle mw-rem-5"
                    v-if="calcList.business_kubun === '3'"
                  >{{ calcList.out_of_legal_working_holiday_night_overtime_hours }}</td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else-if="calcList.business_kubun === ''"
                  ></td>
                  <td
                    class="text-center align-middle mw-rem-5"
                    v-else
                  >00:00</td>-->
                  <!-- /法定外（所定休日）休日深夜残業時間 -->
                  <!-- 深夜労働時間 -->
                  <td
                    class="text-center align-middle mw-rem-3"
                  >{{ calcList.late_night_working_hours }}</td>
                  <!-- /深夜労働時間 -->
                  <!-- 未就労労働時間 -->
                  <td
                    class="text-center align-middle mw-rem-5"
                  >{{ calcList.not_employment_working_hours }}</td>
                  <!-- /未就労労働時間 -->
                  <!-- 備考 -->
                  <td
                    class="text-left align-middle mw-rem-15"
                  >{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                  <!-- /備考 -->
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
  name: "dailyworkinginfotableprint",
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
    // predeterTimeName: {
    //   type: String,
    //   default: "残業時間"
    // },
    // predeterNightTimeName: {
    //   type: String,
    //   default: "深夜残業時間"
    // },
    // predeterTimeSecondName: {
    //   type: String,
    //   default: "残業時間"
    // },
    // predeterNightTimeSecondName: {
    //   type: String,
    //   default: "深夜残業"
    // },
    loginUser: {
      type: String,
      default: ""
    },
    loginRole: {
      type: String,
      default: ""
    },
    accountData: {
      type: String,
      default: ""
    },
    menuData: {
      type: Array,
      default: []
    },
    classText: {
      type: String,
      default: 'text-left text-align-left mw-rem-4'
    }
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
      mode_name: "",
      menu_data: []
    };
  },
  methods: {
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

.table th,
.table td {
  padding: 0rem !important;
  border-style: solid dashed !important;
  border-width: 1px !important;
  border-color: #95c5ed #dee2e6 !important;
}

table {
  border-collapse: collapse !important;
  border: 1px solid #95c5ed !important;
}

.mw-rem-2-5 {
  min-width: 2.5rem;
}

.mw-rem-3 {
  min-width: 3rem;
}

.mw-rem-4 {
  min-width: 4rem;
}

.mw-rem-5 {
  min-width: 5rem;
}

.mw-rem-6 {
  min-width: 6rem;
}

.mw-rem-7 {
  min-width: 7rem;
}

.mw-rem-8 {
  min-width: 8rem;
}

.screen-off {
	display: none;
}


</style>
