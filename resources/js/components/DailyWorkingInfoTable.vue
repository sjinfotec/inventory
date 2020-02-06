<template>
  <div v-if="calcLists.length">
    <div class="card-body mb-3 p-0 border-top">
      <!-- panel contents -->
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <div class="col-12 p-0">
              <table class="table table-striped border-bottom font-size-sm text-nowrap">
                <thead>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'">
                    <td class="text-center align-middle w-15">部署</td>
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15">氏名</td>
                    <td class="text-center align-middle w-15">出勤</td>
                    <td class="text-center align-middle w-15">退勤</td>
                    <td class="text-center align-middle w-15">公用外出</td>
                    <td class="text-center align-middle w-15">公用外出戻り</td>
                    <td class="text-center align-middle w-15">私用外出</td>
                    <td class="text-center align-middle w-15">私用外出戻り</td>
                    <td class="text-center align-middle w-15">勤務状態</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString1"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <!--  <td class="text-center align-middle css-fukidashi"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')">
                      <span class="text">実働時間</span>
                      <span class="fukidashi">{{ edtString }}</span>
                    </td> -->
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15">部署</td>
                    <td class="text-center align-middle w-15">雇用形態</td>
                    <td class="text-center align-middle w-15">氏名</td>
                    <td class="text-center align-middle w-15">出勤</td>
                    <td class="text-center align-middle w-15">退勤</td>
                    <td class="text-center align-middle w-15">公用外出</td>
                    <td class="text-center align-middle w-15">公用外出戻り</td>
                    <td class="text-center align-middle w-15">私用外出</td>
                    <td class="text-center align-middle w-15">私用外出戻り</td>
                    <td class="text-center align-middle w-15">勤務状態</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString1"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'">
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @click="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤者数</td>
                    <td class="text-center align-middle w-15">外出者数</td>
                    <td class="text-center align-middle w-15">有給休暇者数</td>
                    <td class="text-center align-middle w-15">特別休暇者数</td>
                    <td class="text-center align-middle w-15">早退者数</td>
                    <td class="text-center align-middle w-15">遅刻者数</td>
                    <td class="text-center align-middle w-15">欠勤者数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'">
                    <td class="text-center align-middle w-15 color-royalblue"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >実働時間</td>
                    <td class="text-center align-middle w-15">所定時間</td>
                    <td class="text-center align-middle w-15">所定外時間</td>
                    <td class="text-center align-middle w-15">{{ predeterTimeName }}</td>
                    <td class="text-center align-middle w-15">{{ predeterNightTimeName }}</td>
                    <td class="text-center align-middle w-15">法定時間</td>
                    <td class="text-center align-middle w-15">法定外時間</td>
                    <td class="text-center align-middle w-15"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >不就労時間</td>
                    <td class="text-center align-middle w-15">出勤者数</td>
                    <td class="text-center align-middle w-15">外出者数</td>
                    <td class="text-center align-middle w-15">有給休暇者数</td>
                    <td class="text-center align-middle w-15">特別休暇者数</td>
                    <td class="text-center align-middle w-15">早退者数</td>
                    <td class="text-center align-middle w-15">遅刻者数</td>
                    <td class="text-center align-middle w-15">欠勤者数</td>
                    <td class="text-center align-middle w-35 mw-rem-20">備考</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.department_name }}</td>
                    <td class="text-center align-middle">{{ calcList.employment_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.user_name }}</td>
                    <!-- <td class="text-center align-middle" v-if="calcList.x_attendance_time_positions"><button class="btn btn-success">{{ calcList.attendance_time }}</button></td> -->
                    <!-- 出勤 -->
                    <td class="text-center align-middle" v-if="calcList.x_attendance_time_positions">
                      {{ calcList.attendance_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.attendance_time,calcList.user_name,calcList.x_attendance_time_positions,calcList.y_attendance_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.attendance_time }}</td>
                    <!-- 退勤 -->
                    <td class="text-center align-middle" v-if="calcList.x_leaving_time_positions">
                      {{ calcList.leaving_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.leaving_time,calcList.user_name,calcList.x_leaving_time_positions,calcList.y_leaving_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.leaving_time }}</td>
                    <!-- 私用外出　開始 -->
                    <td class="text-center align-middle" v-if="calcList.x_public_going_out_time_positions">
                      {{ calcList.public_going_out_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.public_going_out_time,calcList.user_name,calcList.x_public_going_out_time_positions,calcList.y_public_going_out_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.public_going_out_time }}</td>
                    <!-- 私用外出　終了 -->
                    <td class="text-center align-middle" v-if="calcList.x_public_going_out_return_time_positions">
                      {{ calcList.public_going_out_return_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.public_going_out_return_time,calcList.user_name,calcList.x_public_going_out_return_time_positions,calcList.y_public_going_out_return_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.public_going_out_return_time }}</td>
                    <!-- 公用外出　開始 -->
                    <td class="text-center align-middle" v-if="calcList.x_missing_middle_time_positions">
                      {{ calcList.missing_middle_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.missing_middle_time,calcList.user_name,calcList.x_missing_middle_time_positions,calcList.y_missing_middle_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.missing_middle_time }}</td>
                    <!-- 公用外出　終了 -->
                    <td class="text-center align-middle" v-if="calcList.x_missing_middle_return_time_positions">
                      {{ calcList.missing_middle_return_time }} <img class="icon-size-sm svg_img orange600" src="/images/room-24px.svg" @click="showMap(calcList.missing_middle_return_time,calcList.user_name,calcList.x_missing_middle_return_time_positions,calcList.y_missing_middle_return_time_positions)" alt /></td>
                    <td class="text-center align-middle" v-else >{{ calcList.missing_middle_return_time }}</td>
                
                    <!-- <td class="text-center align-middle">{{ calcList.leaving_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_return_time }}</td> -->
                    <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-left align-middle">{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'detail' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle">{{ calcList.department_name }}</td>
                    <td class="text-center align-middle">{{ calcList.employment_status_name }}</td>
                    <td class="text-center align-middle">{{ calcList.user_name }}</td>
                    <td class="text-center align-middle">{{ calcList.attendance_time }}</td>
                    <td class="text-center align-middle">{{ calcList.leaving_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_time }}</td>
                    <td class="text-center align-middle">{{ calcList.public_going_out_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_time }}</td>
                    <td class="text-center align-middle">{{ calcList.missing_middle_return_time }}</td>
                    <td class="text-center align-middle">{{ calcList.working_status_name }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-left align-middle">{{ calcList.remark_holiday_name }} {{ calcList.remark_check_result }} {{ calcList.remark_check_max_times }} {{ calcList.remark_check_interval }}</td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='basicswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                    <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                    <td class="text-center align-middle">{{ calcList.total_late }}</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                    <td class="text-left align-middle"> </td>
                  </tr>
                  <tr v-if="detailOrTotal === 'total' && btnMode ==='detailswitch'"
                    v-for="(calcList,index) in calcLists"
                  >
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('実働時間 = 所定時間 + ',predeterTimeName,predeterNightTimeName,'')"
                    >{{ calcList.total_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_regular_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.off_hours_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.late_night_overtime_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.legal_working_times }}</td>
                    <td class="text-center align-middle">{{ calcList.out_of_legal_working_times }}</td>
                    <td class="text-center align-middle"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="edtString"
                      @mouseover="edttooltips('欠勤・遅刻・早退・私用外出で働かなかった（不就労）時間。給与控除対象','')"
                    >{{ calcList.not_employment_working_hours }}</td>
                    <td class="text-center align-middle">{{ calcList.total_working_status }}</td>
                    <td class="text-center align-middle">{{ calcList.total_go_out }}</td>
                    <td class="text-center align-middle">{{ calcList.total_paid_holidays }}</td>
                    <td class="text-center align-middle">{{ calcList.total_holiday_kubun }}</td>
                    <td class="text-center align-middle">{{ calcList.total_leave_early }}</td>
                    <td class="text-center align-middle">{{ calcList.total_late }}</td>
                    <td class="text-center align-middle">{{ calcList.total_absence }}</td>
                    <td class="text-left align-middle"> </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
      <el-dialog
          title="位置情報"
          :visible.sync="dialogVisible"
          width="80%"
        >
          <div class="col-xs-12 padding-dis-left margin-bottom-regular">
            <div>
              {{ user_name }} さんの　打刻時間 : {{ record_time }}
            </div>
        <div style="width: 100%; overflow: hidden; height:600px; width:1000px;">
          <iframe v-bind:src="'https://www.google.com/maps/embed/v1/place?q='+longitude+','+latitude+'&key='+apiKey" width="100%" height="600" frameborder="0" style="border:0; margin-top: -150px;">
          </iframe>
        </div>
        <div>
          <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">閉じる</el-button>
            <!-- <el-button type="success" @click="dialogVisible = false">承認</el-button> -->
          </span>
          </div>
          </div>
        </el-dialog>
    </div>
  </div>
</template>
<script>
export default {
  name: "dailyworkinginfotable",
  props: {
    detailOrTotal: {
      type: String,
      default: 'detail'
    },
    calcLists: {
      type: Array
    },
    btnMode: {
      type: String,
      default: ''
    },
    predeterTimeName: {
      type: String,
      default: '残業時間'
    },
    predeterNightTimeName: {
      type: String,
      default: '深夜残業時間'
    },
    // TODO: 本来は .envに記載して取得したい
    apiKey: {
      type: String,
      default: 'AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8'
    }
  },
  // マウント時
  mounted() {
    console.log("dailyworkinginfotable  マウント");
  },
  created(){
    // scriptjs(
    //       "https://maps.googleapis.com/maps/api/js?key=AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8&callback=initMap",
    //       "loadGoogleMap"
    //     );
    // scriptjs.ready("loadGoogleMap", this.loadMap);
  },
  data: function() {
    return {
      edtString: "",
      edtString1: "",
      tipStyle: {  // 後述のスタイル用オブジェクト
          position: 'absolute',
          top: '0px',
          left: '0px',
      },
      dialogVisible: false,
      longitude:"",
      latitude:"",
      record_time:"",
      user_name:""
    };
  },
  computed: {
    latiTude: {
        get: function() {
            return this.latitude;
        },
        set: function(newValue) {
            this.latitude = newValue;
        }
    },
    longiTude: {
        get: function() {
            return this.longitude;
        },
        set: function(newValue) {
            this.longitude = newValue;
        }
    }
  },
  methods: {
    // tooltips
    edttooltips: function(value1, value2) {
      if (value1.length > 0) {
        this.edtString = value1;
      }
      if (value2.length > 0) {
        this.edtString = this.edtString + '\n' + value2;
      }
    },
    // マップ表示
    showMap: function(time,name,x,y){
      console.log(x)
      console.log(y)
      console.log(time)
      console.log(name)
      this.latitude = x;
      this.longitude = y;
      this.user_name = name;
      this.record_time = time;
      this.dialogVisible = true;
    // Let's draw the map
      // var map = new google.maps.Map(document.getElementById("map_canvas1"), mapOptions);
      // var myMarker = new google.maps.Marker({
      //     // マーカーを置く緯度経度
      //     position: new google.maps.LatLng(43.0751744,141.385728),
      //     map: map
      //   });
    }
  }
};
</script>
<style scoped>
.svg_img {
  color: #dc143c;
  cursor: pointer;
}
</style>
