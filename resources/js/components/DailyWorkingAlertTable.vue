<template>
  <div v-if="alertLists.length">
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <thead>
              <tr>
                <td class="text-center align-middle w-15">日付</td>
                <td class="text-center align-middle w-15">雇用形態</td>
                <td class="text-center align-middle w-15">部署</td>
                <td class="text-center align-middle w-15">氏名</td>
                <td class="text-center align-middle w-15">前回打刻時刻</td>
                <td class="text-center align-middle w-15">前回打刻モード</td>
                <td class="text-center align-middle w-15">打刻時刻</td>
                <td class="text-center align-middle w-15">打刻モード</td>
                <td
                  class="text-center align-middle w-15"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="'出退勤間インターバル時間警告の法定警告'"
                >法定警告内容</td>
                <td
                  class="text-center align-middle w-15"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-bind:title="'カード打刻内容の確認'"
                >打刻内容確認</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(alertList,index) in alertLists"
                class="card-body mb-3 py-0 pt-4 border-top"
              >
                <td class="text-center align-middle">{{ alertList.record_date_name }}</td>
                <td class="text-center align-middle">{{ alertList.employment_status_name }}</td>
                <td class="text-center align-middle">{{ alertList.department_name }}</td>
                <td class="text-center align-middle">{{ alertList.user_name }}</td>
                <td class="text-center align-middle">{{ alertList.before_record_time }}</td>
                <td class="text-center align-middle">{{ alertList.before_mode_name }}</td>
                <td class="text-center align-middle">{{ alertList.current_record_time }}</td>
                <td class="text-center align-middle">{{ alertList.current_mode_name }}</td>
                <td
                  class="text-center align-middle"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-if="alertList.interval_alaert === 1"
                  v-bind:title="'退勤してから出勤までの時間が規定時間以内であること'"
                >出退勤間インターバル時間警告</td>
                <td
                  class="text-center align-middle"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-if="alertList.interval_alaert !== 1"
                  v-bind:title="'退勤してから出勤までの時間が規定時間以内であること'"
                ></td>
                <td
                  class="text-center align-middle"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-if="alertList.hit_alert === 1"
                  v-bind:title="'カード打刻内容の確認'"
                >打刻ミス</td>
                <td
                  class="text-center align-middle"
                  data-toggle="tooltip"
                  data-placement="top"
                  v-if="alertList.holiday_alert === 1"
                  v-bind:title="'出勤日にカード打刻していない'"
                >打刻忘れ（出勤日）</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

export default {
  name: "dailyworkingalerttable",
  props: {
    alertLists: {
      type: Array
    }
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
