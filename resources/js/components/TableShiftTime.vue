<template>
  <div>
    <!-- ----------- テーブル部 START ---------------- -->
    <!-- main contentns row -->
    <div class="card-body pt-2">
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <!-- <table class="table"> -->
              <thead>
                <tr>
                  <td v-for="(csvitem1,Index1) in get_TITLE1"
                    class="text-center align-middle mw-rem-10">{{ csvitem1 }}</td>
                  <td class="text-center align-middle mw-rem-2-6" v-if="isEdtbutton">操作</td>
                  <td v-for="(item,rowIndex) in detailDates" v-bind:key="item.date"
                    class="text-center align-middle mw-rem-10">{{ item['date_name'] }}</td>
                  <td v-for="(csvitem2,Index2) in get_TITLE2"
                    class="text-center align-middle mw-rem-5">{{ csvitem2 }}</td>
                </tr>
              </thead>
              <tbody>
                <template v-for="(item1,rowIndex1) in details">
                <!-- <tr v-for="(item1,rowIndex1) in details" v-bind:key="item1['user_code']"> -->
                  <tr>
                    <td rowspan="2" class="text-left align-middle mw-rem-10" v-if="item1['set_department_name'] === 1">{{ item1['department_name'] }}</td>
                    <td rowspan="2" class="text-left align-middle mw-rem-10" v-if="item1['set_employment_name'] === 1">{{ item1['employment_name'] }}</td>
                    <td rowspan="2" class="text-left align-middle mw-rem-10" v-if="item1['set_user_name'] === 1">{{ item1['user_name'] }}</td>
                    <td class="text-center align-middle mw-rem-2-6" style="text-align:center" v-if="isEdtbutton">
                      <input type="image" src="images/btn01.svg" v-on:click="detailEdtClick(rowIndex1)" alt />
                    </td>
                    <td
                      v-for="(item2,rowIndex2) in item1['array_user_date_data']" v-bind:key="item2['date']"
                      class="text-center align-middle mw-rem-10">
                      <div v-if="item2['business_kubun'] === 1 && item2['holiday_kubun'] === 0">{{ item2['working_timetable_name'] }}</div>
                      <div v-else-if="item2['business_kubun'] === 1 && item2['holiday_kubun'] !== 0">{{ item2['holiday_kubun_name'] }}</div>
                      <div v-else>{{ item2['business_kubun_name'] }}</div>
                    </td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_regular_day_cnt'] === 1 && item1['regular_day_cnt'] > 0">{{ item1['regular_day_cnt'] }}日</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_regular_day_cnt'] === 1 && item1['regular_day_cnt'] === 0"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_night_day_cnt'] === 1 && item1['night_day_cnt'] > 0">{{ item1['night_day_cnt'] }}日</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_night_day_cnt'] === 1 && item1['night_day_cnt'] === 0"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_weekly_dayoff_cnt'] === 1 && item1['weekly_dayoff_cnt'] > 0">{{ item1['weekly_dayoff_cnt'] }}日</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_weekly_dayoff_cnt'] === 1 && item1['weekly_dayoff_cnt'] === 0"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_paid_holiday_cnt'] === 1 && item1['paid_holiday_cnt'] > 0">{{ item1['paid_holiday_cnt'] }}日</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_paid_holiday_cnt'] === 1 && item1['paid_holiday_cnt'] === 0"></td>
                  </tr>
                  <tr bgcolor="#EEFFFF">
                    <td class="text-center align-middle mw-rem-2-6 form-control-sm-2">実績</td>
                    <td
                      v-for="(item3,rowIndex3) in item1['array_user_date_data']" v-bind:key="item3['date']"
                      class="text-center align-middle mw-rem-10">
                      <div v-if="item3['total_working_times'] === ''"></div>
                      <div v-else>{{ item3['total_working_times'] }}</div>
                    </td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_regular_day_times'] === 1 && item1['regular_day_times'] !== ''">{{ item1['regular_day_times'] }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_regular_day_times'] === 1 && item1['regular_day_times'] === ''"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_night_day_times'] === 1 && item1['night_day_times'] !== ''">{{ item1['night_day_times'] }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_night_day_times'] === 1 && item1['night_day_times'] === ''"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_weekly_dayoff_times'] === 1 && item1['weekly_dayoff_times'] !== ''">{{ item1['weekly_dayoff_times'] }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_weekly_dayoff_times'] === 1 && item1['weekly_dayoff_times'] === ''"></td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_paid_holiday_day_times'] === 1 && item1['paid_holiday_day_times'] !== ''">{{ item1['paid_holiday_day_times'] }}</td>
                    <td class="text-center align-middle mw-rem-5"
                      v-if="item1['set_paid_holiday_day_times'] === 1 && item1['paid_holiday_day_times'] === ''"></td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.row -->  
    </div>
    <!-- ----------- テーブル部 END ---------------- -->
  </div>
</template>
<script>

export default {
  name: "TableshiftTime",
  props: {
    detailDates: {
      type: Array,
      default: []
    },
    details: {
      type: Array,
      default: []
    },
    detailCsvitems: {
      type: Array,
      default: []
    },
    isEdtbutton: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    get_TITLE1: function() {
      this.detailCsvitems1 = [];
      let $this = this;
      var setf = true;
      this.detailCsvitems.forEach( function( item ) {
        if (item.item_name == 'scheduled_results') {
          setf = false;
          return;
        }
        if (setf) {
          if (item.is_select == 1) {
            if (item.tem_code != 99) {
              $this.detailCsvitems1.push(item.item_out_name);
            }
          }
        }
      });

      return this.detailCsvitems1;
    },
    get_TITLE2: function() {
      this.detailCsvitems2 = [];
      let $this = this;
      var setf = false;
      this.detailCsvitems.forEach( function( item ) {
        if (item.item_name == 'day31') {
          setf = true;
          return;
        }
        if (setf) {
          if (item.is_select == 1) {
            if (item.tem_code != 99) {
              $this.detailCsvitems2.push(item.item_out_name);
            }
          }
        }
      });

      return this.detailCsvitems2;
    }
  },
  data: function() {
    return {
      detailCsvitems1: [],
      detailCsvitems2: []
    };
  },
  methods: {
    // ------------------------ イベント処理 ------------------------------------
    
    // 明細編集ボタンクリックされた場合の処理
    detailEdtClick: function(index) {
      var arrayData = {'rowIndex' : index};
      this.$emit('detaileditclick-event',event, arrayData);
    }
  }
};
</script>
<style scoped>

thead, tbody {
  display: block !important;
}

tbody {
  overflow-x: hidden !important;
  overflow-y: scroll !important;
  height: 360px !important;
}

.table th, .table td {
    padding: 0rem !important;
    border-style: solid dashed !important;
    border-width: 1px !important;
    border-color: #95c5ed #dee2e6 !important;
}

.mw-rem-2-6 {
  min-width: 2.6rem;
}
.form-control-sm-2 {
  height: calc(1.3em + 0.5rem + 2px);
  padding: 0.25rem 0.5rem;
  font-size: 0.7875rem;
  line-height: 1.5;
  border-radius: 0.2rem;
}

input {
    vertical-align : middle;
}
</style>
