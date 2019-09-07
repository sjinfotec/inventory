<template>
  <div v-if="timeValues.length">
    <table class="table table-striped table-bordered">
      <tr
        v-for="(timeItem,index) in timeItems"
        class="card-body mb-3 py-0 pt-4 border-top"
        bgcolor="#add8e6"
      >
        <th width="150">{{ timeItem.department_name }}</th>
        <th width="150">{{ timeItem.employment_status_name }}</th>
        <th width="150">{{ timeItem.user_name }}</th>
        <th width="150">{{ timeItem.working_date_1 }}</th>
        <th width="150">{{ timeItem.working_date_2 }}</th>
        <th width="150">{{ timeItem.working_date_3 }}</th>
        <th width="150">{{ timeItem.working_date_4 }}</th>
        <th width="150">{{ timeItem.working_date_5 }}</th>
        <th width="150">{{ timeItem.working_date_6 }}</th>
        <th width="150">{{ timeItem.working_date_7 }}</th>
        <th width="150">{{ timeItem.working_date_8 }}</th>
        <th width="150">{{ timeItem.working_date_9 }}</th>
        <th width="150">{{ timeItem.working_date_10 }}</th>
        <th width="150">{{ timeItem.working_date_11 }}</th>
        <th width="150">{{ timeItem.working_date_12 }}</th>
      </tr>
      <tr
        v-for="(timeValue,index) in timeValues"
        class="card-body mb-3 py-0 pt-4 border-top"
      >
        <td width="150" class="text-align-center">{{ timeValue.department_name }}</td>
        <td width="150" class="text-align-center">{{ timeValue.employment_status_name }}</td>
        <td width="150" class="text-align-center">{{ timeValue.user_name }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_1 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_2 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_3 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_4 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_5 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_6 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_7 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_8 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_9 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_10 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_11 }}</td>
        <td width="150" class="text-align-center">{{ timeValue.total_working_times_12 }}</td>
      </tr>
    </table>
  </div>
</template>
<script>

export default {
  name: "dailyworkingalerttable",
  props: {
    timeItems: {
      type: Array
    },
    timeValues: {
      type: Array
    }
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
