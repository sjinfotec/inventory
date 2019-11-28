<template>
  <div class="card-body mb-3 p-0 border-top">
    <!-- .row -->
    <div class="col-md-3 pt-2 pb-2 w-30 text-center align-middle">
      <col-note
        v-bind:item-name="'各月の残業時間（Ｈ）'"
        v-bind:item-control="'PRIMARY'"
        v-bind:item-note="''"
        data-toggle="tooltip"
        data-placement="top"
      ></col-note>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-light table-striped　border-bottom font-size-sm text-nowrap">
            <thead>
              <tr
              >
                <td class="text-center align-middle">{{ timeItems.type }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_1 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_2 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_3 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_4 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_5 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_6 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_7 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_8 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_9 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_10 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_11 }}</td>
                <td class="text-center align-middle">{{ timeItems.working_date_12 }}</td>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(timevalue,index) in timeItems.timevalues"
              >
                <td class="text-center align-middle">{{ timevalue.type1 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_1 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_2 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_3 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_4 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_5 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_6 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_7 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_8 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_9 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_10 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_11 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_working_times_12 }}</td>
              </tr>
              <tr
                v-for="(timevalue,index) in timeItems.timevalues"
              >
                <td class="text-center align-middle">{{ timevalue.type2 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_1 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_2 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_3 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_4 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_5 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_6 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_7 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_8 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_9 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_10 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_11 }}</td>
                <td class="text-center align-middle">{{ timevalue.total_noholiday_working_times_12 }}</td>
              </tr>
            </tbody>
          </table>
          <!-- <div
            v-for="(timevalue,index) in timeItems.timevalues"
          >
            <div  class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
              <working-chart
                v-on:label-data="'残業時間＋深夜残業時間'"
                v-on:chart-data="timevalue.total_noholiday_working_times_1"
              >
              </working-chart>
            </div>
          </div> -->
        </div>
      </div>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="col-md-3 pt-2 pb-2 w-30 text-center align-middle">
      <col-note
        v-bind:item-name="'警告内容'"
        v-bind:item-control="'SECONDARY'"
        v-bind:item-note="''"
        data-toggle="tooltip"
        data-placement="top"
      ></col-note>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row" v-for="(warningitem,index) in timeItems.warningitems">
      <div class="col-12">
        <div class="table-responsive">
          <table class="table table-light table-striped border-bottom font-size-sm text-nowrap">
            <thead>
              <tr>
                <td class="text-left align-middle w-30">法定内容</td>
                <td class="text-left align-middle w-10">設定上限値</td> 
                <td class="text-center align-middle w-20">警告対象期間</td>
                <td class="text-center align-middle w-20">実残業時間</td>
                <td class="text-center align-middle w-20">警告内容</td>
              </tr>
            </thead>
            <tbody>
              <tr v-if="warningitem.manthly_alert_warning_1_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_1_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_1_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_1_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_1_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_1_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'45時間/月に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_1_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_1_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'45時間/月に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_1_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_1_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_warning_2_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_2_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_2_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_2_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_2_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_2_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'2ヶ月81時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_2_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_2_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'2ヶ月81時間に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_2_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_2_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_warning_3_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_3_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_3_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_3_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_3_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_3_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'3ヶ月120時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_3_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_3_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'3ヶ月120時間に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_3_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_3_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_warning_12_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_12_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_12_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_12_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_12_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_12_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年360時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_12_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_12_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年360時間に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_12_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_12_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.month_alert_45_total_cnt_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.month_alert_45_total_cnt_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.month_alert_45_total_cnt_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.month_alert_45_total_cnt_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.month_alert_45_total_cnt_work_value"
                    v-bind:item-control="warningitem.month_alert_45_total_cnt_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年間6ヶ月に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.month_alert_45_total_cnt_chk_result"
                    v-bind:item-control="warningitem.month_alert_45_total_cnt_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年間6ヶ月に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.month_alert_45_total_cnt_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.month_alert_45_total_cnt_chk_result }}</td> -->
              </tr>
              <tr v-if="warningitem.manthly_alert_warning_100_total_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_100_total_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_100_total_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_100_total_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_100_total_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_100_total_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'月100時間未満に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_100_total_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_100_total_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'月100時間未満に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_100_total_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_100_total_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_ave_2_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_2_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_2_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_ave_2_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_2_work_value"
                    v-bind:item-control="warningitem.manthly_alert_ave_2_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'2ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_2_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_ave_2_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'2ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_2_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_2_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_ave_3_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_3_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_3_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_ave_3_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_3_work_value"
                    v-bind:item-control="warningitem.manthly_alert_ave_3_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'3ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_3_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_ave_3_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'3ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_3_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_3_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_ave_4_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_4_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_4_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_ave_4_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_4_work_value"
                    v-bind:item-control="warningitem.manthly_alert_ave_4_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'4ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_4_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_ave_4_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'4ヶ月平均80時間対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_4_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_4_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_ave_5_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_5_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_5_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_ave_5_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_5_work_value"
                    v-bind:item-control="warningitem.manthly_alert_ave_5_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'5ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_5_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_ave_5_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'5ヶ月平均80時間対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_5_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_5_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_ave_6_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_6_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_ave_6_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_ave_6_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_6_work_value"
                    v-bind:item-control="warningitem.manthly_alert_ave_6_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'6ヶ月平均80時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_ave_6_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_ave_6_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'6ヶ月平均80時間対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_6_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_ave_6_chk_result }}</td>-->
              </tr>
              <tr v-if="warningitem.manthly_alert_warning_720_total_chk_value != 'OK'" class="text-left align-middle">
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_720_total_chk_itm }}</td>
                <td class="text-left align-middle">{{ warningitem.manthly_alert_warning_720_total_chk_setteing }}</td>
                <td class="text-center align-middle">{{ warningitem.manthly_alert_warning_720_total_chk_period }}</td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_720_total_work_value"
                    v-bind:item-control="warningitem.manthly_alert_warning_720_total_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年720時間に対する警告'"
                  ></col-note>
                </td>
                <td class="text-left align-middle">
                  <col-note
                    v-bind:item-name="warningitem.manthly_alert_warning_720_total_chk_result"
                    v-bind:item-control="warningitem.manthly_alert_warning_720_total_chk_value"
                    v-bind:item-note="''"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'年720時間対する警告'"
                  ></col-note>
                </td>
                <!--<td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_720_total_work_value }}</td>
                <td class="text-left align-middle bg-danger">{{ warningitem.manthly_alert_warning_720_total_chk_result }}</td>-->
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- /.row -->
    <!-- .row -->
    <!--<div class="row" v-for="(warningitem,index) in timeItems.warningitems">
      <div v-if="warningitem.manthly_alert_warning_1_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_1_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_1_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_1_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'45時間/月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_2_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_2_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_2_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_2_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'81時間/2か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_3_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_3_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_3_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_3_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'120時間/3か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_12_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_12_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_12_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_12_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'360時間/年に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.month_alert_45_total_cnt_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.month_alert_45_total_cnt_chk_itm"
        v-bind:item-control="warningitem.month_alert_45_total_cnt_chk_value"
        v-bind:item-note="warningitem.month_alert_45_total_cnt_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>残業が45時間超えた月が合計6か月に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_100_total_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_100_total_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_100_total_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_100_total_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>残業が45時間超えた月が最大100時間を超えないに対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_2_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_2_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_2_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_2_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>2ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_3_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_3_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_3_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_3_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>3ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_4_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_4_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_4_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_4_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>4ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_5_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_5_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_5_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_5_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>5ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_ave_6_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_ave_6_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_ave_6_chk_value"
        v-bind:item-note="warningitem.manthly_alert_ave_6_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>6ヵ月平均で80時間以内に対する警告'"
      ></col-note></div>
      <div v-if="warningitem.manthly_alert_warning_720_total_chk_value != 'OK'" class="text-center align-middle w-20"><col-note
        v-bind:item-name="warningitem.manthly_alert_warning_720_total_chk_itm"
        v-bind:item-control="warningitem.manthly_alert_warning_720_total_chk_value"
        v-bind:item-note="warningitem.manthly_alert_warning_720_total_chk_time_array"
        data-toggle="tooltip"
        data-placement="top"
        v-bind:title="'<特別条項>720時間/年に対する警告'"
      ></col-note></div>
    </div> -->
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
