<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div  v-if="this.displayphase === ''" class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'申請する申請書を選択する'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div v-if="this.get_Identification" class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >申請書類<span class="color-red">[必須]</span></label>
                  </div>
                  <select-generallist
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'申請書類を選択してください'"
                    v-bind:selected-value="selectedDoccodeValue"
                    v-bind:add-new="false"
                    v-bind:get-do="'1'"
                    v-bind:date-value="''"
                    v-bind:kill-value="false"
                    v-bind:row-index="'0'"
                    v-bind:identification-id="identification_id"
                    v-on:change-event="doccodeChange"
                  ></select-generallist>
                </div>
                <message-data v-bind:message-datas="messagedatadoccode" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:makedemandclick-event="makedemandclick"
                  v-bind:btn-mode="'makedemand'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /.row -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- list contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div v-if="this.displayphase === ''" class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'申請一覧表示'"
            v-bind:header-text2="'直近１０件の申請書を表示。修正またはコピーで作成できます。'"
          ></daily-working-information-panel-header>
          <div class="col-md-6 pb-2">
            <message-data v-bind:message-datas="messagedatascopy" v-bind:message-class="'warning'"></message-data>
          </div>
          <!-- /.panel header -->
          <!-- panel body -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-5">選択</td>
                          <td class="text-center align-middle w-15">申請日</td>
                          <td class="text-center align-middle w-15">申請番号</td>
                          <td class="text-center align-middle w-15">申請書類</td>
                          <td class="text-center align-middle w-15">ステータス</td>
                          <td class="text-center align-middle w-15">申請理由</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(item,index) in array_demand" v-bind:key="item.id">
                          <td class="text-center align-middle">
                            <div class="input-group">
                              <input name="'radio' + index" type="radio" class="form-control" v-on:change="radiochange(index)"/>
                            </div>
                          </td>
                          <td class="text-center align-middle">{{item.demand_date_name}}</td>
                          <td class="text-center align-middle">{{item.no}}</td>
                          <td class="text-center align-middle">{{item.doc_code_name}}</td>
                          <td class="text-center align-middle">{{item.status_name}}</td>
                          <td class="text-center align-middle">{{item.demand_reason}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <div class="card-body mb-3 py-0 pt-2 border-top print-none">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:editdemandclick-event="editdemandclick"
                  v-bind:btn-mode="'editdemand'"
                  v-bind:is-push="iseditdemandpush">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:editcopyclick-event="editcopyclick"
                  v-bind:btn-mode="'editcopy'"
                  v-bind:is-push="iseditcopypush">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /list contentns row -->
    <!-- main contentns row -->
    <div v-if="this.displayphase != ''" class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3 align-self-stretch">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="valueselecteddocname"
            v-bind:header-text2="'下記の通り、' + valueselecteddocname + '致します。'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="col-md-6 pb-2">
            <message-data-server v-bind:message-datas="messagedatasserver" v-bind:message-class="'warning'"></message-data-server>
          </div>
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputDemanddate"
                    >申請番号</label>
                  </div>
                  <label class="form-control" >{{ edit.demandno }}</label>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 7 || selectedDoccodeValue === 8 || selectedDoccodeValue === 9" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputDemanddate"
                    >申請日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.demanddate" type="date" class="form-control" id="inputDemanddate">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditdemanddate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <div v-else class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputDemanddate"
                    >申請日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.demanddate" type="date" class="form-control" id="inputDemanddate">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditdemanddate" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputOvertimedate"
                    >残業日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputOvertimedate">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 2" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputHolidaydate"
                    >取得日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputHolidaydate">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 3" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodfrom"
                    >振替休暇日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodto"
                    >休日出勤日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodto" type="date" class="form-control" id="inputGetperiodto">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodto" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 4" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodfrom"
                    >休日出勤日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodto"
                    >代休日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodto" type="date" class="form-control" id="inputGetperiodto">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodto" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 6" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodfrom"
                    >取得期間開始<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodto"
                    >取得期間終了<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodto" type="date" class="form-control" id="inputGetperiodto">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodto" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 7 || selectedDoccodeValue === 8 || selectedDoccodeValue === 9" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGetperiodfrom"
                    >取得日<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 10" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-130"
                      for="inputGetperiodfrom"
                    >欠勤取得期間開始<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodfrom" type="date" class="form-control" id="inputGetperiodfrom">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodfrom" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-130"
                      for="inputGetperiodto"
                    >欠勤取得期間終了<span class="color-red">[必須]</span></label>
                  </div>
                  <input v-model="edit.getperiodto" type="date" class="form-control" id="inputGetperiodto">
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditgetperiodto" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <!-- /.panel contents -->
            <div v-if="selectedDoccodeValue === 1" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="appendRow">+</button>
                      </span>
                      取得者及び予定時間入力
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで申請情報を追加できます</span>
                  </div>
                  <div class="col-md-6 pb-2">
                    <message-data v-bind:message-datas="messagedatadetail" v-bind:message-class="'warning'"></message-data>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top" v-if="demandDetails.length">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">氏名<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">作業項目<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">残業時間開始<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">残業時間終了<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">予定時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-165">申請理由<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in demandDetails" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="demandDetails[index].user_code"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="ulist in userList"
                                        :value="ulist.code"
                                        v-bind:key="ulist.code"
                                      >{{ ulist.name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].working_item"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_from_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_to_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="text"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].scheduled_time"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].demand_reason"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <rowbtn-work-time
                                  v-on:rowdelclick-event="alertDelConf('info',item.id,index)"
                                  v-bind:btn-mode="'rowdel'"
                                  v-bind:is-push="false">
                                </rowbtn-work-time>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
            <div v-if="selectedDoccodeValue === 2" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="appendRow">+</button>
                      </span>
                      取得者及び予定時間入力
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで申請情報を追加できます</span>
                  </div>
                  <div class="col-md-6 pb-2">
                    <message-data v-bind:message-datas="messagedatadetail" v-bind:message-class="'warning'"></message-data>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top" v-if="demandDetails.length">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">氏名<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">作業項目<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">時間開始<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">時間終了<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">出勤時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-165">申請理由<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in demandDetails" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="demandDetails[index].user_code"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="ulist in userList"
                                        :value="ulist.code"
                                        v-bind:key="ulist.code"
                                      >{{ ulist.name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].working_item"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_from_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_to_name"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="text"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].scheduled_time"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].demand_reason"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <rowbtn-work-time
                                  v-on:rowdelclick-event="alertDelConf('info',item.id,index)"
                                  v-bind:btn-mode="'rowdel'"
                                  v-bind:is-push="false">
                                </rowbtn-work-time>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <div v-if="selectedDoccodeValue === 5" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="appendRow">+</button>
                      </span>
                      担当者及び時間変更入力
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで申請情報を追加できます</span>
                  </div>
                  <div class="col-md-6 pb-2">
                    <message-data v-bind:message-datas="messagedatadetail" v-bind:message-class="'warning'"></message-data>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top" v-if="demandDetails.length">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-15">氏名<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">期間開始<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">期間終了<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">始業時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">終業時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-165">申請理由<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in demandDetails" v-bind:key="item.id">
                                <td class="text-center align-middle">
                                  <div class="input-group">
                                    <select
                                      class="custom-select"
                                      v-model="demandDetails[index].user_code"
                                    >
                                      <option value></option>
                                      <option
                                        v-for="ulist in userList"
                                        :value="ulist.code"
                                        v-bind:key="ulist.code"
                                      >{{ ulist.name }}</option>
                                    </select>
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="date"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].date_from"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="date"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].date_to"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_from"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_to"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].demand_reason"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <rowbtn-work-time
                                  v-on:rowdelclick-event="alertDelConf('info',item.id,index)"
                                  v-bind:btn-mode="'rowdel'"
                                  v-bind:is-push="false">
                                </rowbtn-work-time>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue === 7 || selectedDoccodeValue === 8 || selectedDoccodeValue === 9" class="row justify-content-between">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" @click="appendRow">+</button>
                      </span>
                      取得期間入力
                    </h1>
                    <span class="float-sm-right font-size-sm">「＋」アイコンをクリックすることで申請情報を追加できます</span>
                  </div>
                  <div class="col-md-6 pb-2">
                    <message-data v-bind:message-datas="messagedatadetail" v-bind:message-class="'warning'"></message-data>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <!-- .row -->
                  <!-- /.row -->
                  <div class="card-body mb-3 p-0 border-top" v-if="demandDetails.length">
                    <!-- panel contents -->
                    <!-- .row -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-striped border-bottom font-size-sm text-nowrap">
                            <thead>
                              <tr>
                                <td class="text-center align-middle w-10">取得開始時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-10">取得終了時間<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-165">申請理由<span class="color-red">[必須]</span></td>
                                <td class="text-center align-middle w-15">操作</td>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item,index) in demandDetails" v-bind:key="item.id">
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_from"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <input
                                      type="time"
                                      class="font-size-sm form-control"
                                      v-model="demandDetails[index].time_to"
                                    />
                                  </div>
                                </td>
                                <td class="text-center align-middle"
                                  data-placement="top"
                                >
                                  <div class>
                                    <textarea
                                      v-model="demandDetails[index].demand_reason"
                                      class="font-size-sm form-control"
                                      rows="3"
                                       id="inputSummary">
                                    </textarea>
                                  </div>
                                </td>
                                <rowbtn-work-time
                                  v-on:rowdelclick-event="alertDelConf('info',item.id,index)"
                                  v-bind:btn-mode="'rowdel'"
                                  v-bind:is-push="false">
                                </rowbtn-work-time>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div v-if="selectedDoccodeValue !== 1" class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div :class="errorClassObject('summary')" class="input-group">
                  <div class="input-group-prepend">
                    <label for="inputDemandreason" class="control-label">申請理由<span class="color-red">[必須]</span></label>
                  </div>
                </div>
                <div>
                  <textarea v-model="edit.demandreason" class="form-control" rows="3" id="inputDemandreason" placeholder="申請理由"></textarea>
                </div>
              </div>
              <div class="col-md-6 pb-2">
                <message-data v-bind:message-datas="messageeditdemandreason" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >承認者</label>
                  </div>
                  <select-comfirm
                    v-bind:blank-data="true"
                    v-bind:get-do="1"
                    v-bind:or-final="'0'"
                    v-bind:main-orsub="'1'"
                    v-bind:selected-confirm="valueselectedconfirm"
                    v-bind:placeholder-data="'承認者を選択してください'"
                    v-on:change-event="confirmChange"
                  ></select-comfirm>
                </div>
                <message-data v-bind:message-datas="messagedataconfirm" v-bind:message-class="'messageconfirmkbn'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-120"
                      for="inputGroupSelect01"
                    >最終承認者</label>
                  </div>
                  <select-comfirm
                    v-bind:blank-data="true"
                    v-bind:get-do="1"
                    v-bind:or-final="'99'"
                    v-bind:main-orsub="'1'"
                    v-bind:selected-confirm="valueselectedconfirmfinal"
                    v-bind:placeholder-data="'最終承認者を選択してください'"
                    v-on:change-event="confirmfinalChange"
                  ></select-comfirm>
                </div>
                <message-data v-bind:message-datas="messagedataconfirmfinal" v-bind:message-class="'messageconfirmkbn'"></message-data>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
          <div class="card-body mb-3 py-0 pt-4 border-top print-none">
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:dodemandclick-event="dodemandclick"
                    v-bind:btn-mode="'dodemand'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:dischargeclick-event="dischargeclick"
                    v-on:is-push="isdischargepush"
                    v-bind:btn-mode="'discharge'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                    v-on:backclick-event="backclick"
                    v-bind:btn-mode="'back'">
                </btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <div>
              <button id='mail' style="display:none">メール送信</button>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';


const CONST_C025 = 'C025';
const CONST_C026 = 'C026';
const CONST_C032 = 'C032';
const CONST_C025_GENERALUSER_PHYSICAL_NAME= "general_user";
const CONST_C025_ADMINUSER_PHYSICAL_NAME= "admin_user";
const CONST_C025_APPROVERUSER_PHYSICAL_NAME= "general_approver__user";
const CONST_C025_HIGHUSER_PHYSICAL_NAME= "high_user";
const CONST_MENU_EDITUSER_PHYSICAL_NAME= "edit_worktime_user";
const CONST_MENU_EDITUSER_CON_PHYSICAL_NAME= "edit_worktime_user_conditional";
const dateRE   = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/
const timeRE   = /^[0-9]{2}:[0-9]{2}$/
const timecountRE   = /^([1-9][0-9]{0,2}|0)(\.[0-9]{1,2})?$/

export default {
  name: "MakeDemand",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    authusers: {
        type: Array,
        default: []
    },
    const_generaldatas: {
        type: Array,
        default: []
    }
  },
  data() {
    return {
      getDo: 0,
      selectedDoccodeValue: 0,
      fromdate: "",
      valueselecteddocname: "",
      killValue: false,
      validate: false,
      displayphase: "",
      isdischargepush: true,
      iseditcopypush: true,
      iseditdemandpush: true,
      resresults: [],
      array_demandresult: [],
      array_demand: [],
      array_demanddeatail: [],
      confirmlist: [],
      department_name : "",
      user_name : "",
      toaddress : "",
      array_ccaddress: [],
      edit: {
        demandno : "",
        demanddate  : "",
        getperiodfrom   : "",
        getperiodto   : "",
        demandreason   : "",
        confirm : "",
        confirmfinal : ""
      },
      edit_demanddeatail: [],
      maxstrLength: 191,
      maxdemandreasonLength: 256,
      valueselectedconfirm: "",
      valueselectedconfirmfinal: "",
      messagedatadoccode: [],
      messagedatasserver: [],
      messageeditdemanddate: [],
      messageeditgetperiodfrom: [],
      messageeditgetperiodto: [],
      messageeditdemandreason: [],
      messagedataconfirm: [],
      messagedataconfirmfinal: [],
      messagedatadetail: [],
      messagedatascopy: [],
      messageshowsearch: false,
      messageconfirmkbn: "warning",
      selecttedrowindex: -1,
      demandDetails: [],
      userroleList: [],
      userrole: "",
      identification_id: '',
      generalList_m: [],
      userList: [],
      general_C025_data: [],
      general_C026_data: [],
      general_C032_data: [],
      isUserblank: true,
      login_user_code: "",
      login_user_role: "",
      login_user_department_code: "",
      generaluserrole: "",
      adminuserrole: ""
    };
  },
  computed: {
    get_C025: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C025) {
          $this.general_C025_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.general_C025_data;
    },
    get_C026: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C026) {
          $this.general_C026_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.general_C025_data;
    },
    get_C032: function() {
      let $this = this;
      var i = 0;
      this.const_generaldatas.forEach( function( item ) {
        if (item.identification_id == CONST_C032) {
          $this.general_C032_data.push($this.const_generaldatas[i]);
        }
        i++;
      });    
      return this.general_C025_data;
    },
    get_AdminUserRole: function() {
      if (this.adminuserrole == null || this.adminuserrole == "") {
        if (this.general_C025_data.length == 0) {
          let $this = this;
          this.get_C025.forEach( function( item ) {
            if (item.physical_name == CONST_C025_ADMINUSER_PHYSICAL_NAME) {
              $this.adminuserrole = item.code;
            }
          });    
        } else {
          let $this = this;
          this.general_C025_data.forEach( function( item ) {
            if (item.physical_name == CONST_C025_ADMINUSER_PHYSICAL_NAME) {
              $this.adminuserrole = item.code;
            }
          });    
        }
      }
      return this.adminuserrole;
    },
    get_LoginUserCode: function() {
      this.login_user_code = this.authusers['code'];
      return this.login_user_code;
    },
    get_LoginUserRole: function() {
      this.login_user_role = this.authusers['role'];
      return this.login_user_role;
    },
    get_Identification: function() {
      if (this.get_LoginUserRole == null || this.get_LoginUserRole == "") {
        this.get_LoginUserRole;
      }
      if (this.general_C025_data.length == 0) {
        let $this = this;
        this.get_C025.forEach( function( item ) {
          if (item.physical_name == CONST_C025_GENERALUSER_PHYSICAL_NAME) {
            $this.generaluserrole = item.code;
          }
        });    
      } else {
        let $this = this;
        this.general_C025_data.forEach( function( item ) {
          if (item.physical_name == CONST_C025_GENERALUSER_PHYSICAL_NAME) {
            $this.generaluserrole = item.code;
          }
        });    
      }
      if (this.get_LoginUserRole == this.generaluserrole) {
        this.identification_id = CONST_C032;
      } else {
        this.identification_id = CONST_C026;
      }
      return this.identification_id;
    },
    validation() {
      const edit = this.edit
      return {
        demanddate: (!!edit.demanddate && dateRE.test(edit.demanddate)),
        getperiodfrom: (!!edit.getperiodfrom && dateRE.test(edit.getperiodfrom))
      }
    },
    isValid() {
      const validation = this.validation
      return Object
        .keys(validation)
        .every(function (key) {
          return validation[key]
       })
    },
  },
  // マウント時
  mounted() {
    // this.getUserDepartmentRole();
    //this.getUserRole();
  },
  methods: {
    // バリデーション
    checkForm: function(e) {
      this.validate = true;
      this.messagedataClear();
      if (!this.selectedDoccodeValue) {
        this.messagedatadoccode.push("申請書類は必ず選択してください。");
        this.validate = false;
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    checkFormDetail: function(e) {
      this.messageconfirmkbn = "warning";
      this.validate = true;
      this.messagedataClear();
      if (!this.edit.demanddate) {
        this.messageeditdemanddate.push("申請日は必ず入力してください。");
        this.validate = false;
      } else {
        if (!dateRE.test(this.edit.demanddate)) {
          this.messageeditdemanddate.push("申請日に正しい日付を入力してください。");
          this.validate = false;
        }
      }
      if (this.selectedDoccodeValue == 1) {
        if (!this.edit.getperiodfrom) {
          this.messageeditgetperiodfrom.push("残業日は必ず入力してください。");
          this.validate = false;
        } else {
          if (!dateRE.test(this.edit.getperiodfrom)) {
            this.messageeditgetperiodfrom.push("残業日に正しい日付を入力してください。");
            this.validate = false;
          }
        }
        if (this.demandDetails.length == 0) {
          this.messagedatadetail.push("予定時間が入力されていません。");
          this.validate = false;
        }
        for (var i = 0; i < this.demandDetails.length; i++) {
          if (!this.isRowInput(i)) {
            this.messagedatadetail.push(i+1 + "行めの入力がありません。");
            this.validate = false;
          } else {
            if (this.demandDetails[i].user_code == "") {
              this.messagedatadetail.push(i+1 + "行めの氏名に入力がありません。");
              this.validate = false;
            }
            if (this.demandDetails[i].working_item == "") {
              this.messagedatadetail.push(i+1 + "行めの作業項目に入力がありません。");
              this.validate = false;
            } else {
              if (this.demandDetails[i].working_item.length > this.maxstrLength) {
                this.messagedatadetail.push(i+1 + "行めの作業項目は" + this.maxstrLength + "文字以内で入力してください。");
                this.validate = false;
              }
            }
            if (this.demandDetails[i].time_from_name == "") {
              this.messagedatadetail.push(i+1 + "行めの残業時間開始に入力がありません。");
              this.validate = false;
            } else {
              if (!timeRE.test(this.demandDetails[i].time_from_name)) {
                this.messagedatadetail.push(i+1 + "行めの残業時間開始に正しい時刻を入力してください。");
                this.validate = false;
              }
            }
            if (this.demandDetails[i].time_to_name == "") {
              this.messagedatadetail.push(i+1 + "行めの残業時間終了に入力がありません。");
              this.validate = false;
            } else {
              if (!timeRE.test(this.demandDetails[i].time_to_name)) {
                this.messagedatadetail.push(i+1 + "行めの残業時間終了に正しい時刻を入力してください。");
                this.validate = false;
              }
            }
            if (this.demandDetails[i].scheduled_time == "") {
              this.messagedatadetail.push(i+1 + "行めの予定時間に入力がありません。");
              this.validate = false;
            } else {
              if (!timecountRE.test(this.demandDetails[i].scheduled_time)) {
                this.messagedatadetail.push(i+1 + "行めの予定時間は整数部3桁小数部2桁以内で入力してください。");
                this.validate = false;
              }
            }
            if (this.demandDetails[i].demand_reason == "") {
              this.messagedatadetail.push(i+1 + "行めの申請理由に入力がありません。");
              this.validate = false;
            } else {
              if (this.demandDetails[i].demand_reason.length > this.maxdemandreasonLength) {
                this.messagedatadetail.push(i+1 + "行めの申請理由は" + this.maxdemandreasonLength + "文字以内で入力してください。");
                this.validate = false;
              }
            }
          }
        }
      }
      if (!this.valueselectedconfirm && !this.valueselectedconfirmfinal) {
        this.messagedataconfirm.push("承認者または最終承認者は必ず入力してください。");
        this.messagedataconfirmfinal.push("承認者または最終承認者は必ず入力してください。");
      }

      if (this.validate) {
        return this.validate;
      }

      e.preventDefault();
    },
    // 申請書類選択が変更された場合の処理
    doccodeChange: function(value, arrayData) {
      this.array_demandresult = [];
      this.array_demand = [];
      this.array_demanddeatail = [];
      this.demandDetails = [];
      this.selectedDoccodeValue = value;
      this.valueselecteddocname = arrayData['name'];
      this.getDo = 1;
      // 申請一覧取得
      if (this.selectedDoccodeValue) {
        this.getDemandList();
      }
    },
    // 承認者選択が変更された場合の処理
    confirmChange: function(value) {
      this.valueselectedconfirm = value;
    },
    // 最終承認者選択が変更された場合の処理
    confirmfinalChange: function(value, name) {
      this.valueselectedconfirmfinal = value;
    },
    // 新規作成ボタンがクリックされた場合の処理
    makedemandclick: function(e) {
      this.messagedataClear();
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.demanditemClear();
        this.displayphase = "make";
        this.isdischargepush = false;
      }
    },
    // 編集ボタンがクリックされた場合の処理
    editdemandclick: function(e) {
      this.messagedataClear();
      this.validate = this.checkForm(e);
      if (this.validate) {
        this.displayphase = "edit";
        this.isdischargepush = true;
      }
    },
    // 複写作成ボタンがクリックされた場合の処理
    editcopyclick: function(e) {
      this.messagedataClear();
      if (this.selecttedrowindex < 0) {
          this.messagedatascopy.push("複写する行が選択されていません。");
      } else {
        this.validate = this.checkForm(e);
        if (this.validate) {
          this.edit.demandno = "";
          this.edit.demanddate = "";
          this.edit.getperiodfrom = "";
          this.edit.getperiodto = "";
          this.displayphase = "copy";
          this.isdischargepush = false;
        }
      }
    },
    // 申請ボタンがクリックされた場合の処理
    dodemandclick: function(e) {
      this.checkFormDetail(e);
      if (this.validate) {
        this.alertstoreConf("info");
      }
    },
    // 取り下げボタンがクリックされた場合の処理
    dischargeclick: function(e) {
      if (this.validate) {
        this.alertdischargeConf("info");
      }
    },
    // 戻るボタンがクリックされた場合の処理
    backclick: function(e) {
      this.displayphase = "";
      this.demanditemClear();
      var arrayData = {'rowindex' : 0, 'name' : this.valueselecteddocname};
      this.doccodeChange(this.selectedDoccodeValue, arrayData);
    },
    // ラジオボタンがクリックされた場合の処理
    radiochange: function(index) {
      // editセット
      this.selecttedrowindex = index;
      this.edit.demandno = this.array_demandresult[index].array_demand[0].no;
      this.edit.demandno = this.array_demandresult[index].array_demand[0].no;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].demand_date).format("YYYY-MM-DD");
      this.edit.demanddate = this.dateFormat;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].date_from).format("YYYY-MM-DD");
      this.edit.getperiodfrom = this.dateFormat;
      this.dateFormat = moment(this.array_demandresult[index].array_demand[0].date_to).format("YYYY-MM-DD");
      this.edit.getperiodto = this.dateFormat;
      this.edit.demandreason = this.array_demandresult[index].array_demand[0].demand_reason;
      this.edit.confirm = this.array_demandresult[index].array_demand[0].nmail_user_code;
      this.valueselectedconfirm = this.edit.confirm;
      this.edit.confirmfinal = "";
      // 明細
      for ( var i=0; i<this.array_demandresult[index].array_demandDeatail.length; i++ ) {
        this.demandDetails.push({
          id: this.array_demandresult[index].array_demandDeatail[i].detail_row_no,
          department_code: this.array_demandresult[index].array_demandDeatail[i].detail_department_code,
          user_code: this.array_demandresult[index].array_demandDeatail[i].detail_user_code,
          working_item: this.array_demandresult[index].array_demandDeatail[i].detail_working_item,
          time_from_name: this.array_demandresult[index].array_demandDeatail[i].detail_time_from,
          time_to_name: this.array_demandresult[index].array_demandDeatail[i].detail_time_to,
          scheduled_time: this.array_demandresult[index].array_demandDeatail[i].detail_scheduled_time,
          demand_reason: this.array_demandresult[index].array_demandDeatail[i].detail_demand_reason
        });
      }
    },
    // ログインユーザーの権限を取得
    // getUserDepartmentRole: function() {
    //   this.$axios
    //     .get("/get_login_user_department", {
    //     })
    //     .then(response => {
    //       this.userroleList = response.data;
    //       for ( var i=0; i<this.userroleList.length; i++ ) {
    //           this.login_user_department_code = this.userroleList[i]["department_code"];
    //           this.userrole = this.userroleList[i]["role"];
    //       }
    //       this.getUserList('', this.login_user_department_code);
    //     })
    //     .catch(reason => {
    //       alert("ログインユーザー権限取得エラー");
    //     });
    // },
    // 氏名選択リスト取得処理
    getUserList(targetdate){
      if (targetdate == '') {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (this.login_user_department_code == "") { this.login_user_department_code = null; }
      this.postRequest("/get_user_list",
        { targetdate: targetdate,
          killvalue: this.killValue,
          getDo : this.getDo,
          departmentcode : this.login_user_department_code,
          employmentcode : null
        })
        .then(response  => {
          this.getThenuser(response);
          if (this.login_user_department_code == null) { this.login_user_department_code = ""; }
          // 固有処理 END
        })
        .catch(reason => {
          this.serverCatch("氏名", "取得");
          if (this.login_user_department_code == null) { this.login_user_department_code = ""; }
        });
    },
    getGeneralList(value) {
      this.$axios
        .get("/get_general_list", {
          params: {
            identificationid: value
          }
        })
        .then(response => {
          this.generalList_m = response.data;
        })
        .catch(reason => {
          this.alert("error", "勤怠権限リスト取得に失敗しました", "エラー");
        });
    },
    // 申請一覧取得
    getDemandList() {
      this.$axios
        .get("/demand/list_demand", {
          params: {
            doccode: this.selectedDoccodeValue,
            usercode: "",
            getdo: 1
          }
        })
        .then(response => {
          this.resresults = response.data;
          if (this.resresults.array_demandresult != null) {
            this.array_demandresult = this.resresults.array_demandresult;
          }
          if (this.resresults.array_demand != null) {
            this.array_demand = this.resresults.array_demand;
          }
          if (this.resresults.array_demanddetail != null) {
            this.array_demanddetail = this.resresults.array_demanddetail;
          }
          if (Object.keys(this.array_demand).length > 0) {
            this.iseditcopypush = false;
            this.iseditdemandpush = false;
          } else {
            this.iseditcopypush = true;
            this.iseditdemandpush = true;
          }
          if (this.resresults.messagedata != null) {
            this.messagedatasserver = this.resresults.messagedata;
          }
        })
        .catch(reason => {
          this.alert("error", "申請一覧取得に失敗しました", "エラー");
        });
    },
    errorClassObject(key) {
      return {
        'has-error': (this.validation[key] == false)
      }
    },
    appendRow: function() {
      this.demandDetails.push({
        id: "",
        department_code: "",
        user_code: "",
        working_item: "",
        time_from_name: "",
        time_to_name: "",
        scheduled_time: "",
        demand_reason: ""
      });
    },
    alertstoreConf: function(state) {
      this.$swal({
        title: "確認",
        text: "この内容で申請しますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.store("store");
        }
      });
    },
    alertdischargeConf: function(state) {
      this.$swal({
        title: "確認",
        text: "申請を取り下げしますか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.store("discharge");
        }
      });
    },
    alertDelConf: function(state, id, index) {
      if (this.isRowInput(index)) {
        this.$swal({
          title: "確認",
          text: "削除しますか？",
          icon: state,
          buttons: true,
          dangerMode: true
        }).then(willDelete => {
          if (willDelete) {
            this.demandDetails.splice(index, 1);
          }
        });
      } else {
        this.demandDetails.splice(index, 1);
      }
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    // 申請処理
    store: function(kbn) {
      this.edit.confirm = this.valueselectedconfirm;
      this.edit.confirmfinal = this.valueselectedconfirmfinal;
      this.$axios
        .post("/demand/make_demand", {
          doccode: this.selectedDoccodeValue,
          demandedit: this.edit,
          demanddetail: this.demandDetails,
          kbn: kbn
        })
        .then(response => {
          this.resresults = response.data;
          this.store_result = this.resresults.result;
          this.msg = "";
          if (kbn == "store") {
            this.msg = "申請";
          } else if(kbn == "discharge") {
            this.msg = "申請取り下げ";
          }
          if (this.store_result == true) {
            this.department_name = this.resresults.department_name;
            this.user_name = this.resresults.user_name;
            this.toaddress = this.resresults.toaddress;
            this.array_ccaddress = this.resresults.ccaddress;
            this.alert("success", this.msg + "しました", "申請完了");
            this.isdischargepush = true;
            this.edit.demandno = this.resresults.demandno;
            this.demandMailSend();
          } else {
            this.edit.demandno = "";
            this.alert("error", this.msg + "に失敗しました", "エラー");
          }
          if (this.resresults.messagedata != null) {
            this.messagedatasserver = this.resresults.messagedata;
          }
          this.$forceUpdate();
        })
        .catch(reason => {
          this.alert("error", "申請に失敗しました", "エラー");
        });
    },
    // ------------------------ サーバー処理 ----------------------------
    // ログインユーザーの権限を取得
    // getUserRole: function() {
    //   var arrayParams = [];
    //   this.postRequest("/get_login_user_role", arrayParams)
    //     .then(response  => {
    //       this.getThenrole(response);
    //     })
    //     .catch(reason => {
    //       this.serverCatch("ユーザー権限", "取得");
    //     });
    // },

    // ----------------- 共通メソッド ----------------------------------
    // 取得正常処理（ユーザー権限）
    getThenuser(response) {
      var res = response.data;
      if (res.result) {
        this.userrole = res.role;
        if (this.userrole == "1") {
          this.identification_id = "C032";
        } else {
          this.identification_id = "C026";
        }
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("ユーザー権限", "取得");
        }
      }
    },
    // 取得正常処理（ユーザー権限）
    getThenrole(response) {
      var res = response.data;
      if (res.result) {
        this.userrole = res.role;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("ユーザー権限", "取得");
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    // クリアメソッド
    itemClear: function() {
    },
    // 申請項目クリアメソッド
    demanditemClear: function() {
      this.demandDetails = [];
      this.edit.demandno = "";
      this.edit.demanddate = "";
      this.edit.getperiodfrom = "";
      this.edit.getperiodto = "";
      this.edit.demandreason = "";
      this.edit.confirm = "";
      this.edit.confirmfinal = "";
    },
    // メッセージ領域クリアメソッド
    messagedataClear: function() {
      this.messageeditdemanddate = [];
      this.messageeditgetperiodfrom = [];
      this.messageeditgetperiodto = [];
      this.messageeditdemandreason = [];
      this.messagedatadetail = [];
      this.messagedataconfirm = [];
      this.messagedataconfirmfinal = [];
      this.messagedatasserver = [];
      this.messagedatadoccode = [];
      this.messagedatascopy = [];
    },
    // 申請明細で入力されているか
    isRowInput: function(index) {
      if (this.demandDetails[index].user_code != "" ||
        this.demandDetails[index].working_item != "" ||
        this.demandDetails[index].time_from_name != "" ||
        this.demandDetails[index].time_to_name != "" ||
        this.demandDetails[index].scheduled_time != "" ||
        this.demandDetails[index].demand_reason != "") {
        return true;
      }
      return false;
    },
    // 申請メール
    demandMailSend: function(index) {
      var address, ccAddress, subject, body, hiddenData;
      var sendmail = document.getElementById('mail');
      subject = '件名：';
      if (this.selectedDoccodeValue == 1){
        subject += '残業申請';
      } else if (this.selectedDoccodeValue == 2){
        subject += '休日出勤申請';
      } else if (this.selectedDoccodeValue == 3){
        subject += '休日振替申請';
      } else if (this.selectedDoccodeValue == 4){
        subject += '代休申請';
      } else if (this.selectedDoccodeValue == 5){
        subject += 'シフト変更申請';
      } else if (this.selectedDoccodeValue == 6){
        subject += '有給休暇申請';
      } else if (this.selectedDoccodeValue == 7){
        subject += '遅刻申請';
      } else if (this.selectedDoccodeValue == 8){
        subject += '早退申請';
      } else if (this.selectedDoccodeValue == 9){
        subject += '外出申請書';
      } else if (this.selectedDoccodeValue == 10){
        subject += '欠勤申請';
      }
      subject += '承認依頼';
      body = '以下の申請の承認を依頼します。';
      body += '%0D%0A' + '%0D%0A' + '申請番号：' + this.edit.demandno;
      this.dateFormat = moment(this.edit.demanddate).format("YYYY年MM月DD日");
      body += '%0D%0A' + '申請日：' + this.dateFormat;
      body += '%0D%0A' + '申請者：（部署）' + this.department_name + '（氏名）' + this.user_name;
      console.log("demandMailSend 3");
      address = this.toaddress;
      ccAddress = "";
      console.log("this.array_ccaddress = " + this.array_ccaddress.length);
      for ( var i=0; i<this.array_ccaddress.length; i++ ) {
        console.log("this.array_ccaddress = " + this.array_ccaddress[i]);
        if (ccAddress.length == 0) {
          ccAddress += this.array_ccaddress[i];
        } else {
          ccAddress += "," + this.array_ccaddress[i];
        }
      }
      console.log("demandMailSend 4");

      location.href = 'mailto:' + address + '?cc=' + ccAddress + '&subject=' + subject + '&body=' + body;
      console.log("demandMailSend end");
    }
  }
};
</script>

