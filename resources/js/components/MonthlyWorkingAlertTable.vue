<template>
  <div v-if="timeValues.length">
    <div class="card-body mb-3 p-0 border-top">
      <!-- panel contents -->
      <!-- .row -->
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <div class="col-12 p-0">
              <table class="table table-striped border-bottom font-size-sm text-nowrap">
                <thead>
                  <tr
                    v-for="(timeItem,index) in timeItems"
                    bgcolor="#add8e6"
                  >
                    <td class="text-center align-middle w-15">{{ timeItem.department_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.employment_status_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.user_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_1 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_2 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_3 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_4 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_5 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_6 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_7 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_8 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_9 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_10 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_11 }}</td>
                    <td class="text-center align-middle w-15">{{ timeItem.working_date_12 }}</td>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(timeValue,index) in timeValues"
                  >
                    <td class="text-center align-middle w-15">{{ timeValue.department_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.employment_status_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.user_name }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_1 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_2 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_3 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_4 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_5 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_6 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_7 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_8 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_9 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_10 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_11 }}</td>
                    <td class="text-center align-middle w-15">{{ timeValue.total_working_times_12 }}</td>
                  </tr>
                </tbody>
              </table>
              <div v-for="(warningitem,index) in warningItems">
                <col-note
                  v-bind:item-name="warningitem.total_working_times_1"
                  v-bind:time-values="warningitem.manthly_alert_warning_1_chk"
                ></col-note>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
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
    },
    warningItems: {
      type: Array
    }
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
