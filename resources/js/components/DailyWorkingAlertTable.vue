<template>
  <div v-if="alertLists.length">
    <table class="table table-striped table-bordered">
      <tr bgcolor="#add8e6">
        <th>日付</th>
        <th>雇用形態</th>
        <th>部署</th>
        <th>氏名</th>
        <th>前回打刻時刻</th>
        <th>前回打刻モード</th>
        <th>打刻時刻</th>
        <th>打刻モード</th>
        <th
          data-toggle="tooltip"
          data-placement="top"
          v-bind:title="'出退勤間インターバル時間警告の法定警告'"
        >法定警告内容</th>
        <th
          data-toggle="tooltip"
          data-placement="top"
          v-bind:title="'カード打刻内容の確認'"
        >打刻内容確認</th>
      </tr>
      <tr
        v-for="(alertList,index) in alertLists"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td class="text-align-center">{{ alertList.record_date_name }}</td>
        <td class="text-align-center">{{ alertList.employment_status_name }}</td>
        <td class="text-align-center">{{ alertList.department_name }}</td>
        <td class="text-align-center">{{ alertList.user_name }}</td>
        <td class="text-align-center">{{ alertList.before_record_time }}</td>
        <td class="text-align-center">{{ alertList.before_mode_name }}</td>
        <td class="text-align-center">{{ alertList.current_record_time }}</td>
        <td class="text-align-center">{{ alertList.current_mode_name }}</td>
        <td class="text-align-center"
          data-toggle="tooltip"
          data-placement="top"
          v-if="alertList.interval_alaert === 1"
          v-bind:title="'退勤してから出勤までの時間が規定時間以内であること'"
        >出退勤間インターバル時間警告</td>
        <td class="text-align-center"
          data-toggle="tooltip"
          data-placement="top"
          v-if="alertList.interval_alaert !== 1"
          v-bind:title="'退勤してから出勤までの時間が規定時間以内であること'"
        ></td>
        <td class="text-align-center"
          data-toggle="tooltip"
          data-placement="top"
          v-if="alertList.hit_alert === 1"
          v-bind:title="'カード打刻内容の確認'"
        >打刻忘れまたはミス</td>
        <td class="text-align-center"
          data-toggle="tooltip"
          data-placement="top"
          v-if="alertList.holiday_alert === 1"
          v-bind:title="'カード打刻内容の確認'"
        >出勤日に打刻データがない</td>
      </tr>
    </table>
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
