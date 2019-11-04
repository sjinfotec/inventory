<template>
  <div class="card-body mb-3 p-0 border-top">
    <!-- panel contents -->
    <!-- .row -->
    <div class="row" v-for="(warningitem,index) in timeItems.warningitems">
      <div v-if="warningitem.manthly_alert_warning_1_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_1_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_1_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_1_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'45時間/月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_2_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_2_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_2_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_2_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'81時間/2か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_3_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_3_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_3_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_3_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'120時間/3か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_12_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_12_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_12_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_12_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'360時間/年に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.month_alert_45_total_cnt_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.month_alert_45_total_cnt_chk_itm"
        v-bind:item-control="warningitem.month_alert_45_total_cnt_chk_value"
        v-bind:item-note="warningitem.month_alert_45_total_cnt_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>残業が45時間超えた月が合計6か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_100_total_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_100_total_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_100_total_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_100_total_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>残業が45時間超えた月が最大100時間を超えないに対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_2_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_2_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_2_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_2_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>2ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_3_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_3_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_3_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_3_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>3ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_4_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_4_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_4_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_4_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>4ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_5_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_5_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_5_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_5_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>5ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_6_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_6_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_6_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_6_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>6ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_720_total_chk_value != 'OK'" class="text-center align-middle w-15"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_720_total_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_720_total_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_720_total_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>720時間/年に対する警告'"
      ></col-note></div>
    </div>
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-striped border-bottom font-size-sm text-nowrap">
            <thead>
              <tr
                bgcolor="#add8e6"
              >
                <td class="text-center align-middle w-15">{{ timeItems.employment_status_name }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_1 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_2 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_3 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_4 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_5 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_6 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_7 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_8 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_9 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_10 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_11 }}</td>
                <td class="text-center align-middle w-15">{{ timeItems.working_date_12 }}</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(timevalue,index) in timeItems.timevalues"
              >
                <td class="text-center align-middle w-15">{{ timevalue.employment_status_name }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_1 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_2 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_3 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_4 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_5 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_6 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_7 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_8 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_9 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_10 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_11 }}</td>
                <td class="text-center align-middle w-15">{{ timevalue.total_working_times_12 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- /.row -->
  </div>
</template>
<script>

export default {
  name: "monthlyworkingalerttable",
  props: {
    timeItems: {
      type: Array
    }
  },
  // マウント時
  mounted() {
    console.log(this.name + "マウント");
  }
};
</script>
