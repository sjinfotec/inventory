<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- ========================== 検索部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'ユーザーを設定する'"
            v-bind:header-text2="'ユーザーの登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >所属部署</span>
                  </div>
                  <select-departmentlist
                    v-if="showdepartmentlist"
                    ref="selectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'部署を選択してください'"
                    v-bind:selected-department="selectedDepartmentValue"
                    v-bind:add-new="false"
                    v-bind:date-value="''"
                    v-bind:kill-value="valueDepartmentkillcheck"
                    v-bind:row-index="0"
                    v-on:change-event="departmentChanges"
                  ></select-departmentlist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <label
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="target_users"
                    >氏 名</label>
                  </div>
                  <select-userlist
                    v-if="showuserlist"
                    ref="selectuserlist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'氏名を選択すると編集モードになります'"
                    v-bind:selected-value="selectedUserValue"
                    v-bind:add-new="true"
                    v-bind:get-do="getDo"
                    v-bind:date-value="applytermdate"
                    v-bind:kill-value="valueUserkillcheck"
                    v-bind:row-index="0"
                    v-bind:department-value="selectedDepartmentValue"
                    v-bind:employment-value="''"
                    v-bind:management-value="'99'"
                    v-on:change-event="userChanges"
                  ></select-userlist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="form-check form-check-inline float-right">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="inlineCheckbox1"
                    v-model="valueDepartmentkillcheck"
                    @change="checkboxChangeDepartment"
                  />
                  <label class="form-check-label" for="inlineCheckbox1">※所属部署選択リストに廃止した部署も含める</label>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="form-check form-check-inline float-right">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="inlineCheckbox2"
                    v-model="valueUserkillcheck"
                    @change="checkboxChangeUser"
                  />
                  <label class="form-check-label" for="inlineCheckbox1">※氏名選択リストに退職した氏名も含める</label>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択ボタン類 START ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:usersupload-event="usersuploadclick"
                  v-bind:btn-mode="'usersupload'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-csv-download
                  v-bind:btn-mode="'csvusers'"
                  v-bind:csv-data="calcresults"
                  v-bind:is-csvbutton="iscsvbutton"
                  v-bind:csv-date="datejaFormat"
                ></btn-csv-download>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
    </div>
    <div class="row justify-content-between">
      <!-- ========================== アップロード部 START ========================== -->
      <!-- .panel -->
      <div class="col-md pt-3 print-none" v-if="selectMode=='UPUSERS'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'以下のアップロードアイコンでユーザーを登録します。'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- ファイル選択 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-3 pb-2">
                <button type="button" class="btn btn-lg font-size-rg" @click="upclick">
                  <img class="icon-size-user mr-2 pb-1" src="/images/upload-icon-1.svg" alt />ユーザー登録
                </button>
                <input
                  type="file"
                  id="f1"
                  style="display: none;"
                  ref="uplog"
                  @change="onFileChange"
                  accept=".csv"
                />
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ファイル選択 END ---------------- -->
          </div>
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messageUp.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messageup,index) in messageUp" v-bind:key="index">{{ messageup }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== アップロード部 END ========================== -->
      <!-- /.panel -->
      <!-- ========================== 新規部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆ユーザー情報設定'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 新規入力部 START ---------------- -->
          <!-- panel contents -->
          <div class="card-body pt-2">
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesNew.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li
                    v-for="(messagevalidate,index) in messagevalidatesNew"
                    v-bind:key="index"
                  >{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      氏名
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'ユーザーの氏名を191文字以内で入力します'"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >フリガナ</span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.kana"
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'ユーザーのフリガナを30文字桁以内で入力します'"
                    name="kana"
                  />
                </div>
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
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      雇用形態
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <select-employmentstatuslist
                    ref="addselectemploymentstatuslist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'雇用形態を選択してください'"
                    v-bind:selected-value="form.status"
                    v-on:change-event="addemploymentChanges"
                  ></select-employmentstatuslist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >役職</span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.official_position"
                    name="official_position"
                  />
                </div>
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
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      所属部署
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <select-departmentlist
                    v-if="showadddepartmentlist"
                    ref="addselectdepartmentlist"
                    v-bind:blank-data="true"
                    v-bind:placeholder-data="'所属部署を選択してください'"
                    v-bind:selected-value="form.department_code"
                    v-on:change-event="adddepartmentChanges"
                  ></select-departmentlist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      メールアドレス
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.email"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                    name="email"
                  />
                </div>
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
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="'登録は管理者が（半角英数字4-10文字）で決定入力します。'"
                    >
                      ログインID
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.code"
                    name="code"
                    title="登録は管理者が（半角英数字4-10文字）で決定入力します。"
                    pattern="^[a-zA-Z0-9]{4,10}$"
                    @change="addcodeChange"
                  />
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                    >
                      モバイル用アドレス
                      <!-- <span class="color-red">[必須]</span> -->
                    </span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.mobile_email"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                    name="mobile_email"
                  />
                </div>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="inputGroupSelect01"
                    >
                      勤怠管理
                      <span class="color-red">[必須]</span>
                    </label>
                  </div>
                  <select-generallist
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'勤怠管理の有無を選択してください'"
                    v-bind:selected-value="form.management"
                    v-bind:identification-id="'C017'"
                    v-on:change-event="adddisplayChange"
                  ></select-generallist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="'「勤務時間設定」で登録したタイムテーブルのリストから選択します。'"
                    >
                      タイムテーブル
                      <span class="color-red">[必須]</span>
                    </span>
                  </div>
                  <select-timetablelist
                    :blank-data="true"
                    :selected-value="form.working_timetable_no"
                    v-on:change-event="addtimetableChanges"
                  ></select-timetablelist>
                </div>
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
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      for="inputGroupSelect02"
                    >
                      権限
                      <span class="color-red">[必須]</span>
                    </label>
                  </div>
                  <select-generallist
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'勤怠管理の権限を選択してください'"
                    v-bind:selected-value="form.role"
                    v-bind:identification-id="'C025'"
                    v-on:change-event="addroleChange"
                  ></select-generallist>
                </div>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-150"
                      id="basic-addon1"
                      data-toggle="tooltip"
                      data-placement="top"
                      v-bind:title="'新規登録時はパスワードはログインIDとなります。'"
                    >パスワード</span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.password"
                    name="password"
                    title="新規登録時はパスワードはログインIDとなります。"
                    disabled
                  />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 項目部 END ---------------- -->
            <!-- ----------- ボタン部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="storeclick"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="false"
                ></btn-work-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- ボタン部 END ---------------- -->
          </div>
          <!-- /.panel contents -->
          <!-- ----------- 新規入力部 END ---------------- -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 新規部 END ========================== -->
      <!-- ========================== 編集部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl" v-if="details.length">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆　' + selectedUserName"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button
                  class="btn btn-success btn-lg font-size-rg"
                  v-on:click="appendRowClick"
                >＋新規履歴追加</button>
              </span>
            </h1>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 END ---------------- -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li
                    v-for="(messagevalidate,index) in messagevalidatesEdt"
                    v-bind:key="index"
                  >{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- panel contents -->
            <div v-for="(item,index) in details" v-bind:key="item.id">
              <div class="card shadow-pl">
                <div class="card-body pt-3">
                  <!-- item.result  1: 現在適用中 -->
                  <!-- item.result  2: 未来適用予定または -->
                  <!-- 現在適用中 ----------------------------------------------------------------->
                  <div v-if="item.result != ''">
                    <!-- .row -->
                    <div class="row justify-content-between" v-if="item.result == 1">
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1) + ' 現在適用中'"
                          v-bind:item-control="'INFO'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <div class="row justify-content-between" v-else>
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1)"
                          v-bind:item-control="'LIGHT'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              適用開始日
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.apply_term_from"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'この情報を適用する開始日を入力します'"
                            name="applytermfrom"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >退職日</span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.kill_from_date"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'退職する日を入力します'"
                            name="killfromdate"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              氏名
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.name"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'氏名を191文字以内で入力します'"
                            name="name"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >フリガナ</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="30"
                            v-model="item.kana"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'フリガナを30文字以内で入力します'"
                            name="kana"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              雇用形態
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select class="custom-select" v-model="item.employment_status">
                            <option value></option>
                            <option
                              v-for="elist in employStatusList"
                              :value="elist.code"
                              v-bind:key="elist.code"
                            >{{ elist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >役職</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.official_position"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'役職を191文字以内で入力します'"
                            name="officialposition"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              所属部署
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select class="custom-select" v-model="item.department_code">
                            <option value></option>
                            <option
                              v-for="dlist in departmentList"
                              :value="dlist.code"
                              v-bind:key="dlist.code"
                            >{{ dlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              メールアドレス
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.email"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'メールアドレスを191文字以内で入力します'"
                            name="email"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              ログインID
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <input type="text" class="form-control" v-model="item.code" name="code" />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >モバイル用アドレス</span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.mobile_email"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'モバイル用メールアドレスを191文字以内で入力します'"
                            name="mobile_email"
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >パスワード</span>
                          </div>
                          <input
                            type="password"
                            class="form-control"
                            v-model="item.password"
                            title="パスワードの変更はできません。"
                            name="password"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              勤怠管理
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select class="custom-select" v-model="item.management">
                            <option value></option>
                            <option
                              v-for="mlist in generalList_m"
                              :value="mlist.code"
                              v-bind:key="mlist.code"
                            >{{ mlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              タイムテーブル
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select
                            class="custom-select"
                            v-model="item.working_timetable_no"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'「勤務時間設定」で登録したタイムテーブルのリストから選択します。'"
                          >
                            <option value></option>
                            <option
                              v-for="tlist in timetableList"
                              :value="tlist.no"
                              v-bind:key="tlist.no"
                            >{{ tlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >
                              権限
                              <span class="color-red">[必須]</span>
                            </span>
                          </div>
                          <select class="custom-select" v-model="item.role">
                            <option value></option>
                            <option
                              v-for="rlist in generalList_r"
                              :value="rlist.code"
                              v-bind:key="rlist.code"
                            >{{ rlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /row -->
                    <!-- .row -->
                    <!-- ----------- ボタン部 START ---------------- -->
                    <div class="row justify-content-between">
                      <div class="col-md-12 pb-2">
                        <div class="btn-group float-left">
                          <button
                            v-if="item.result != 0 && item.id != ''"
                            type="button"
                            class="btn btn-success"
                            @click="fixclick(index)"
                          >この内容で更新する</button>
                          <button
                            v-if="item.id == ''"
                            type="button"
                            class="btn btn-success"
                            @click="addClick(index)"
                          >この内容で追加する</button>
                          <button
                            v-if="item.result != 0 && item.id != ''"
                            type="button"
                            class="btn btn-danger"
                            @click="delClick(index)"
                          >この内容を削除する</button>
                          <button
                            v-if="item.result != 0 && item.id != ''"
                            type="button"
                            class="btn btn-warning"
                            @click="sendUrl()"
                          >モバイル打刻URLを送信する</button>
                          <button
                            v-if="item.id == ''"
                            type="button"
                            class="btn btn-danger"
                            @click="rowDelClick(index)"
                          >履歴追加取り消し</button>
                          <button
                            v-if="item.result != 0 && item.id != '' && item.card_idm != '' && showrelease"
                            type="button"
                            class="btn btn-danger"
                            @click="releaseclick(index)"
                          >カード情報を解除する</button>
                        </div>
                      </div>
                    </div>
                    <!-- /.row -->
                    <!-- ----------- ボタン部 END ---------------- -->
                  </div>
                  <!-- 現在適用中より過去 ----------------------------------------------------------------->
                  <div v-else>
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- panel header -->
                      <div class="col-md-2 pb-2">
                        <col-note
                          v-bind:item-name="'No.' + (index+1)"
                          v-bind:item-control="'LIGHT'"
                          v-bind:item-note="''"
                        ></col-note>
                      </div>
                      <!-- /.panel header -->
                    </div>
                    <!-- /row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- panel contents -->
                      <!-- panel header -->
                      <daily-working-information-panel-header
                        v-bind:header-text1="'◆ユーザー'"
                        v-bind:header-text2="''"
                        v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                      ></daily-working-information-panel-header>
                      <!-- /.panel header -->
                    </div>
                    <!-- /.row -->
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >適用開始日</span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.apply_term_from"
                            name="applytermfrom"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >退職日</span>
                          </div>
                          <input
                            type="date"
                            class="form-control"
                            v-model="item.kill_from_date"
                            name="killfromdate"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >氏名</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.name"
                            name="name"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >フリガナ</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="30"
                            v-model="item.kana"
                            name="kana"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >雇用形態</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.employment_status">
                            <option value></option>
                            <option
                              v-for="elist in employStatusList"
                              :value="elist.code"
                              v-bind:key="elist.code"
                            >{{ elist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >役職</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            maxlength="191"
                            v-model="item.official_position"
                            name="officialposition"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >所属部署</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.department_code">
                            <option value></option>
                            <option
                              v-for="dlist in departmentList"
                              :value="dlist.code"
                              v-bind:key="dlist.code"
                            >{{ dlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >メールアドレス</span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.email"
                            name="email"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >ログインID</span>
                          </div>
                          <input
                            type="text"
                            class="form-control"
                            v-model="item.code"
                            name="code"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >モバイル用アドレス</span>
                          </div>
                          <input
                            type="email"
                            maxlength="191"
                            class="form-control"
                            v-model="item.mobile_email"
                            name="mobile_email"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >パスワード</span>
                          </div>
                          <input
                            type="password"
                            class="form-control"
                            v-model="item.password"
                            name="password"
                            disabled
                          />
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >勤怠管理</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.management">
                            <option value></option>
                            <option
                              v-for="mlist in generalList_m"
                              :value="mlist.code"
                              v-bind:key="mlist.code"
                            >{{ mlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >タイムテーブル</span>
                          </div>
                          <select
                            disabled
                            class="custom-select"
                            v-model="item.working_timetable_no"
                            data-toggle="tooltip"
                            data-placement="top"
                            v-bind:title="'「勤務時間設定」で登録したタイムテーブルのリストから選択します。'"
                          >
                            <option value></option>
                            <option
                              v-for="tlist in timetableList"
                              :value="tlist.no"
                              v-bind:key="tlist.no"
                            >{{ tlist.name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                      <!-- .col -->
                      <div class="col-md-6 pb-2">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span
                              class="input-group-text font-size-sm line-height-xs label-width-180"
                              id="basic-addon1"
                            >権限</span>
                          </div>
                          <select disabled class="custom-select" v-model="item.role">
                            <option value></option>
                            <option
                              v-for="rlist in generalList_r"
                              :value="rlist.code"
                              v-bind:key="rlist.code"
                            >{{ rlist.code_name }}</option>
                          </select>
                        </div>
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /row -->
                  </div>
                </div>
              </div>
              <!-- /panel contents -->
            </div>
            <!-- ----------- 項目部 END ---------------- -->
          </div>
          <!-- /main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
        </div>
        <message-waiting v-bind:is-message-show="messageshowsearch"></message-waiting>
      </div>
    </div>
    <!-- /.panel -->
    <el-dialog
      v-bind:title="' モバイル打刻用URL送信'"
      :visible.sync="dialogVisible"
      width="50%"
      center="true"
    >
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">送信するモバイルのメールアドレスを入力してください</h5>
          <h5 class="card-subtitle mb-2">{{ form.code }}</h5>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text font-size-sm line-height-xs label-width-150">email</span>
            </div>
            <input
              type="email"
              class="form-control"
              v-model="input_mobile_address"
              maxlength="191"
              name="input_mobile_address"
            />
          </div>
          <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
        <div class="card-footer">
          <button class="btn btn-warning" @click="sendUrl()">送信する</button>
        </div>
      </div>
    </el-dialog>
  </div>
  <!-- /main contentns row -->
</template>
<script>
import moment from "moment";
import encoding from "encoding-japanese";
import { dialogable } from "../mixins/dialogable.js";
import { checkable } from "../mixins/checkable.js";
import { requestable } from "../mixins/requestable.js";
export default {
  name: "EditUser",
  mixins: [dialogable, checkable, requestable],
  props: {
    authusers: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      selectedDepartmentValue: "",
      valueDepartmentkillcheck: false,
      showdepartmentlist: true,
      showadddepartmentlist: true,
      selectedUserValue: "",
      valueUserkillcheck: false,
      isUsermanagement: true,
      showuserlist: true,
      selectedEmploymentValue: "",
      valueTimeTablekillcheck: false,
      selectMode: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      messageUp: [],
      selectedUserName: "",
      showrelease: true,
      details: [],
      usersups: [],
      form: {
        id: "",
        apply_term_from: "",
        code: "",
        department_code: "",
        employment_status: "",
        name: "",
        kana: "",
        kill_from_date: "",
        official_position: "",
        working_timetable_no: "",
        email: "",
        mobile_email: "",
        password: "",
        management: "",
        role: ""
      },
      count: 0,
      before_count: 0,
      getDo: 1,
      departmentList: [],
      applytermdate: moment(new Date()).format("YYYYMMDD"),
      timetableList: [],
      generalList_m: [],
      generalList_r: [],
      cardId: "",
      mobile_address: "",
      dialogVisible: false,
      latest_user_code: "",
      messageshowsearch: false,
      login_user_code: "",
      login_user_role: ""
    };
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers["code"];
    this.login_user_role = this.authusers["role"];
    this.details = [];
    this.getDepartmentList("");
    this.getGeneralList("C001");
    this.getTimeTableList("");
    this.getGeneralList("C017");
    this.getGeneralList("C025");
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // 氏名
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = "氏名";
      chkArray = this.checkHeader(
        this.form.name,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // フリガナ
      required = false;
      equalength = 0;
      maxlength = 30;
      itemname = "フリガナ";
      chkArray = this.checkHeader(
        this.form.kana,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 雇用形態
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "雇用形態";
      chkArray = this.checkHeader(
        this.form.employment_status,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 役職
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = "役職";
      chkArray = this.checkHeader(
        this.form.official_position,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 所属部署
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "所属部署";
      chkArray = this.checkHeader(
        this.form.department_code,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // email
      required = true;
      equalength = 0;
      maxlength = 191;
      itemname = "メールアドレス";
      chkArray = this.checkHeader(
        this.form.email,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // mobile_email
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = "モバイル用アドレス";
      chkArray = this.checkHeader(
        this.form.mobile_email,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // ユーザーCODE
      required = true;
      equalength = 0;
      maxlength = 10;
      itemname = "ログインＩＤ";
      chkArray = this.checkHeader(
        this.form.code,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 勤怠管理対象
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "勤怠管理対象";
      chkArray = this.checkHeader(
        this.form.management,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // タイムテーブル
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "タイムテーブル";
      chkArray = this.checkHeader(
        this.form.working_timetable_no,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      // 権限
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "権限";
      chkArray = this.checkHeader(
        this.form.role,
        required,
        equalength,
        maxlength,
        itemname
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }
      if (this.messagevalidatesNew.length > 0) {
        flag = false;
      }
      return flag;
    },
    // バリデーション（更新）
    checkFormFix: function(index) {
      this.messagevalidatesEdt = [];
      var chkArray = [];
      var flag = true;
      // 適用開始日
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = "適用開始日";
      chkArray = this.checkDetail(
        this.details[index].apply_term_from,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      } else {
        // 適用中と比較
        var chkdt = moment(this.details[index].apply_term_from).format(
          "YYYYMMDD"
        );
        var chksourcedt = "";
        if (this.details[index].result == "2") {
          for (var i = index + 1; i < this.count; i++) {
            chksourcedt = moment(this.details[i].apply_term_from).format(
              "YYYYMMDD"
            );
            if (chkdt <= chksourcedt) {
              this.messagevalidatesEdt.push(
                "現在適用中の適用開始日付以前の日付は登録できません"
              );
            }
          }
        }
      }
      // 退職開始日
      required = false;
      equalength = 0;
      maxlength = 0;
      itemname = "退職開始日";
      chkArray = this.checkDetail(
        this.details[index].kill_from_date,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // ログインＩＤ
      required = true;
      equalength = 0;
      maxlength = 10;
      itemname = "ログインＩＤ";
      chkArray = this.checkDetail(
        this.details[index].code,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 部署コード
      required = true;
      equalength = 0;
      maxlength = 8;
      itemname = "部署コード";
      chkArray = this.checkDetail(
        this.details[index].department_code,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 雇用形態
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "雇用形態";
      chkArray = this.checkDetail(
        this.details[index].employment_status,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 氏名
      required = true;
      equalength = 0;
      maxlength = 191;
      itemname = "氏名";
      chkArray = this.checkDetail(
        this.details[index].name,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // フリガナ
      required = false;
      equalength = 0;
      maxlength = 30;
      itemname = "フリガナ";
      chkArray = this.checkDetail(
        this.details[index].kana,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 役職
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = "役職";
      chkArray = this.checkDetail(
        this.details[index].official_position,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // メールアドレス
      required = true;
      equalength = 0;
      maxlength = 191;
      itemname = "メールアドレス";
      chkArray = this.checkDetail(
        this.details[index].email,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // モバイル用アドレス
      required = false;
      equalength = 0;
      maxlength = 191;
      itemname = "モバイル用アドレス";
      chkArray = this.checkDetail(
        this.details[index].mobile_email,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // パスワード
      required = true;
      equalength = 0;
      maxlength = 191;
      itemname = "パスワード";
      chkArray = this.checkDetail(
        this.details[index].password,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 勤怠管理対象
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "勤怠管理対象";
      chkArray = this.checkDetail(
        this.details[index].management,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // タイムテーブルNo
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "タイムテーブルNo";
      chkArray = this.checkDetail(
        this.details[index].working_timetable_no,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 権限
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = "権限";
      chkArray = this.checkDetail(
        this.details[index].role,
        required,
        equalength,
        maxlength,
        itemname,
        index + 1
      );
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      if (this.messagevalidatesEdt.length > 0) {
        flag = false;
      }
      return flag;
    },
    // ------------------------ イベント処理 ------------------------------------

    // 部署選択が変更された場合の処理
    departmentChanges: function(value, arrayitem) {
      this.selectedDepartmentValue = value;
      this.inputClear();
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
      this.selectMode = "";
    },
    // 廃止チェックボックスが変更された場合の処理
    checkboxChangeDepartment: function() {
      this.refreshDepartmentList();
      this.inputClear();
      this.selectedDepartmentValue = "";
      // ユーザー選択コンポーネントの取得メソッドを実行
      this.getDo = 1;
      this.getUserSelected();
      this.selectMode = "";
    },
    // ユーザー選択が変更された場合の処理
    userChanges: function(value, arrayitem) {
      // 入力項目の部署クリア
      this.selectMode = "";
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      this.selectedUserValue = value;
      if (value == "" || value == null) {
        this.selectMode = "NEW";
        this.newItemClear();
        this.form.department_code = this.selectedDepartmentValue;
        this.refreshaddDepartmentList();
      } else {
        this.selectMode = "EDT";
        this.selectedUserName = arrayitem["name"];
        this.getItem();
      }
    },
    // 廃止チェックボックスが変更された場合の処理
    checkboxChangeUser: function() {
      this.inputClear();
      this.refreshUserList();
    },
    // 新規作成部署選択が変更された場合の処理
    adddepartmentChanges: function(value, arrayitem) {
      this.form.department_code = value;
    },
    // 新規作成雇用形態選択が変更された場合の処理
    addemploymentChanges: function(value, arrayitem) {
      this.form.employment_status = value;
    },
    // 新規作成ログインＩＤが変更された場合の処理
    addcodeChange: function() {
      this.form.password = this.form.code;
    },
    // 新規作成勤怠管理が変更された場合の処理
    adddisplayChange: function(value, arrayitem) {
      this.form.management = value;
    },
    // 新規作成タイムテーブルが変更された場合の処理
    addtimetableChanges: function(value, arrayitem) {
      this.form.working_timetable_no = value;
    },
    // 新規作成権限が変更された場合の処理
    addroleChange: function(value, arrayitem) {
      this.form.role = value;
    },
    // CSVボタンクリック処理
    usersuploadclick: function() {
      this.selectMode = "UPUSERS";
    },
    // アップロードボタンがクリックされた場合の処理
    upclick: function(e) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var messages = [];
      messages.push("登録しているユーザー情報をクリアしてから登録します。");
      messages.push("既存データは消去されますが、よろしいですか？");
      this.htmlMessageSwal("確認", messages, "info", true, true).then(
        result => {
          if (result) {
            var obj = document.getElementById("f1");
            obj.value = "";
            this.$refs.uplog.click(); // 同じファイルだとイベントが走らない
          }
        }
      );
    },
    // ファイル選択が変更された場合の処理
    onFileChange: function(e) {
      var isResult = this.handleFileSelect(e);
      if (!isResult) {
        var messages = [];
        messages.push("アップロードするファイルの内容が誤っています。");
        messages.push("確認してください。");
        this.htmlMessageSwal("エラー", messages, "error", true, false);
      }
    },
    // 新規作成ボタンクリック処理
    storeclick() {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var flag = this.checkFormStore();
      if (flag) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.htmlMessageSwal("確認", messages, "info", true, true).then(
          result => {
            if (result) {
              this.store();
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagevalidatesNew,
          "error",
          true,
          false,
          true
        ).then(result => {
          if (result) {
          }
        });
      }
    },
    // 更新ボタンクリック処理
    fixclick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        if (
          this.details[index].kill_from_date == "" ||
          this.details[index].kill_from_date == null
        ) {
          messages.push("この内容で更新しますか？");
        } else {
          messages.push(
            "退職日が入力されているため入力日より退職扱いとなります。"
          );
          messages.push("更新してよろしいですか？");
        }
        this.htmlMessageSwal("確認", messages, "info", true, true).then(
          result => {
            if (result) {
              this.FixDetail("更新", index);
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagevalidatesEdt,
          "error",
          true,
          false,
          true
        ).then(result => {
          if (result) {
          }
        });
      }
    },
    // 追加ボタンクリック処理
    addClick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var flag = this.checkFormFix(index);
      if (flag) {
        var messages = [];
        if (
          this.details[index].kill_from_date == "" ||
          this.details[index].kill_from_date == null
        ) {
          messages.push("この内容で追加しますか？");
        } else {
          messages.push(
            "退職日が入力されているため入力日より退職扱いとなります。"
          );
          messages.push("追加してよろしいですか？");
        }
        this.htmlMessageSwal("確認", messages, "info", true, true).then(
          result => {
            if (result) {
              this.FixDetail("追加", index);
            }
          }
        );
        // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal(
          "エラー",
          this.messagevalidatesEdt,
          "error",
          true,
          false,
          true
        ).then(result => {
          if (result) {
          }
        });
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var messages = [];
      messages.push("この内容を削除しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true).then(
        result => {
          if (result) {
            this.DelDetail(index);
          }
        }
      );
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      if (this.before_count < this.count) {
        var messages = [];
        messages.push(
          "１度に追加できる情報は１個です。追加してから再実行してください"
        );
        this.htmlMessageSwal("エラー", messages, "error", true, false);
      } else {
        var add_apply_term_from = this.details[0].apply_term_from;
        var add_kill_from_date = this.details[0].kill_from_date;
        var add_card_idm = this.details[0].card_idm;
        var add_code = this.details[0].code;
        var add_department_code = this.details[0].department_code;
        var add_employment_status = this.details[0].employment_status;
        var add_name = this.details[0].name;
        var add_kana = this.details[0].kana;
        var add_official_position = this.details[0].official_position;
        var add_working_timetable_no = this.details[0].working_timetable_no;
        var add_email = this.details[0].email;
        var add_management = this.details[0].management;
        var add_role = this.details[0].role;
        var add_password = this.details[0].password;
        this.object = {
          id: "",
          code: add_code,
          department_code: add_department_code,
          employment_status: add_employment_status,
          name: add_name,
          kana: add_kana,
          official_position: add_official_position,
          working_timetable_no: add_working_timetable_no,
          email: add_email,
          password: add_password,
          management: add_management,
          role: add_role,
          card_idm: add_card_idm,
          apply_term_from: "",
          kill_from_date: add_kill_from_date,
          result: "2"
        };
        this.details.unshift(this.object);
        this.count = this.details.length;
      }
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("履歴追加取り消ししてよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true).then(
          result => {
            if (result) {
              this.details.splice(index, 1);
              this.count = this.details.length;
            }
          }
        );
      } else {
        this.details.splice(index, 1);
        this.count = this.details.length;
      }
    },
    // ICカード情報削除ボタンクリック処理
    releaseclick(index) {
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.messageUp = [];
      var messages = [];
      messages.push("カード情報の紐づけを解除しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true).then(
        result => {
          if (result) {
            this.ReleaseCard("解除", index);
          }
        }
      );
    },
    // -------------------- サーバー処理 ----------------------------
    // 氏名取得処理
    getItem() {
      var arrayParams = {
        code: this.selectedUserValue,
        killvalue: this.isUsermanagement
      };
      this.postRequest("/edit_user/get", arrayParams)
        .then(response => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("ユーザ", "取得");
        });
    },
    // 氏名登録処理
    store() {
      var arrayParams = { details: this.form };
      this.postRequest("/edit_user/store", arrayParams)
        .then(response => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          this.serverCatch("ユーザ", "登録");
        });
    },
    // 氏名更新処理（明細）
    FixDetail(kbnname, index) {
      var arrayParams = { details: this.details[index] };
      this.postRequest("/edit_user/fix", arrayParams)
        .then(response => {
          this.putThenDetail(response, kbnname);
        })
        .catch(reason => {
          this.serverCatch("ユーザ", "登録");
        });
    },
    // 氏名削除処理（明細）
    DelDetail(index) {
      var arrayParams = { id: this.details[index].id };
      this.postRequest("/edit_user/del", arrayParams)
        .then(response => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          this.serverCatch("ユーザ", "削除");
        });
    },
    // カード解除
    ReleaseCard(kbnname, index) {
      var arrayParams = { card_idm: this.details[index].card_idm };
      this.postRequest("/edit_user/release_card_info", arrayParams)
        .then(response => {
          this.putThenCard(response, kbnname);
          this.details[index].card_idm = "";
          this.refreshreleaseCardbottun();
        })
        .catch(reason => {
          this.serverCatch("カード", kbnname);
        });
    },
    // 部署選択リスト取得処理
    getDepartmentList(targetdate) {
      if (targetdate == "") {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      var arrayParams = {
        targetdate: targetdate,
        killvalue: this.valueDepartmentkillcheck
      };
      this.postRequest("/get_departments_list", arrayParams)
        .then(response => {
          this.getThendepartment(response);
        })
        .catch(reason => {
          this.serverCatch("部署", "取得");
        });
    },
    // タイムテーブル選択リスト取得処理
    getTimeTableList(targetdate) {
      if (targetdate == "") {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      this.postRequest("/get_time_table_list", {
        targetdate: targetdate,
        killvalue: this.valueTimeTablekillcheck
      })
        .then(response => {
          this.getThentimetable(response);
        })
        .catch(reason => {
          this.serverCatch("タイムテーブル", "取得");
        });
    },
    // 氏名選択リスト取得処理
    getUserList(targetdate) {
      if (targetdate == "") {
        targetdate = moment(new Date()).format("YYYYMMDD");
      }
      if (this.selectedDepartmentValue == "") {
        this.selectedDepartmentValue = null;
      }
      if (this.selectedEmploymentValue == "") {
        this.selectedEmploymentValue = null;
      }
      this.postRequest("/get_user_list", {
        targetdate: targetdate,
        killvalue: this.killValue,
        getDo: this.getDo,
        departmentcode: this.selectedDepartmentValue,
        employmentcode: this.selectedEmploymentValue,
        managementcode: "ALL"
      })
        .then(response => {
          this.getThenuser(response);
          if (this.selectedDepartmentValue == null) {
            this.selectedDepartmentValue = "";
          }
          if (this.selectedEmploymentValue == null) {
            this.selectedEmploymentValue = "";
          }
          // 固有処理 END
        })
        .catch(reason => {
          this.serverCatch("氏名", "取得");
          if (this.selectedDepartmentValue == null) {
            this.selectedDepartmentValue = "";
          }
          if (this.selectedEmploymentValue == null) {
            this.selectedEmploymentValue = "";
          }
        });
    },
    // コード選択リスト取得処理
    getGeneralList(value) {
      var arrayParams = { identificationid: value };
      this.postRequest("/get_general_list", arrayParams)
        .then(response => {
          if (value == "C001") {
            this.getThenemployment(response, "雇用形態");
          }
          if (value == "C017") {
            this.getThenmanagement(response, "勤怠管理");
          }
          if (value == "C025") {
            this.getThenrole(response, "権限");
          }
        })
        .catch(reason => {
          if (value == "C001") {
            this.serverCatch("雇用形態", "取得");
          }
          if (value == "C017") {
            this.serverCatch("勤怠管理", "取得");
          }
          if (value == "C025") {
            this.serverCatch("権限", "取得");
          }
        });
    },
    // モバイル端末へ打刻URL送信
    sendUrl() {
      const url = "/api/mail/inquiry";
      const self = this;
      this.messageshowsearch = true;
      this.details.forEach(element => {
        if (element.result == 1) {
          this.latest_user_code = element.code;
          this.mobile_address = element.mobile_email;
        }
      });
      //axiosでPOST送信
      axios
        .post(url, {
          email: this.mobile_address,
          login_id: this.latest_user_code
        })
        .then(res => {
          console.log(res);
          if (res.data.result) {
            //メール送信完了画面に遷移する
            var messages = [];
            messages.push(
              "※メールが届かない場合は、お手数ですがご使用いただくモバイル端末のブラウザにて「https://onedawnm.onedawn.net」へ手動で接続して下さい。"
            );
            this.htmlMessageSwal("送信完了", messages, "success", true, false);
          } else {
            self.errors = res.data.errors;
          }
          this.messageshowsearch = false;
        })
        .catch(err => {
          //例外処理を行う
          console.log(err);
          this.messageshowsearch = false;
        });
    },
    // アップロード登録処理
    usersUpload() {
      // 処理中メッセージ表示
      this.$swal({
        title: "処　理　中...",
        html: "",
        allowOutsideClick: false, //枠外をクリックしても画面を閉じない
        showConfirmButton: false,
        showCancelButton: true,
        onBeforeOpen: () => {
          this.$swal.showLoading();
          var arrayParams = { usersups: this.usersups };
          this.postRequest("/edit_user/up", arrayParams)
            .then(response => {
              this.$swal.close();
              this.putThenUp(response, "アップロード登録");
            })
            .catch(reason => {
              this.$swal.close();
              this.serverCatch("ユーザ", "アップロード登録");
            });
        }
      });
    },

    // ----------------- privateメソッド ----------------------------------
    // イベントログファイル操作
    handleFileSelect: function(e) {
      var file_data = e.target.files[0];
      // 読み込み
      var reader = new FileReader();
      // 読み込んだファイルの中身を取得する
      reader.readAsBinaryString(file_data);
      let $this = this;
      //ファイルの中身を取得後に処理を行う
      reader.addEventListener("load", function() {
        var result = reader.result;
        const sjisArray = [];
        for (let i = 0; i < result.length; i += 1) {
          sjisArray.push(result.charCodeAt(i));
        }
        // 変換処理の実施
        const uniArray = encoding.convert(sjisArray, "unicode", "sjis");
        var result_enc = encoding.codeToString(uniArray);
        var array_linetext = result_enc.split("\r\n");
        var user_code = "";
        var user_department_name = "";
        var user_employment_name = "";
        var user_name = "";
        var user_kana = "";
        var user_official_position = "";
        var user_kill_from_date = "";
        var user_working_timetable_name = "";
        var user_email = "";
        var user_mobile_email = "";
        var user_management = "";
        var user_role = "";
        var linetext = "";
        var array_object = [];
        // 1行目はヘッダ
        for (var i = 1; i < array_linetext.length; i++) {
          linetext = array_linetext[i].split(",");
          // TODO:linetext.length=1はSKIPとするが(EOF)EOF以外のデータの場合でもSKIPとなる
          if (linetext.length == 12) {
            array_object.push({
              user_code: linetext[0].trim(),
              user_department_name: linetext[1].trim(),
              user_employment_name: linetext[2].trim(),
              user_name: linetext[3].trim(),
              user_kana: linetext[4].trim(),
              user_official_position: linetext[5].trim(),
              user_kill_from_date: linetext[6].trim(),
              user_working_timetable_name: linetext[7].trim(),
              user_email: linetext[8].trim(),
              user_mobile_email: linetext[9].trim(),
              user_management: linetext[10].trim(),
              user_role: linetext[11].trim()
            });
          } else if (linetext.length != 1) {
            return false;
          }
        }
        $this.usersups = array_object;
        $this.usersUpload();
        return true;
      });
    },
    // -------------------- 共通 ----------------------------
    // ユーザー選択コンポーネント取得メソッド
    getUserSelected: function() {
      // managementcode=99 → すべて
      this.$refs.selectuserlist.getList(
        "",
        this.valueUserkillcheck,
        this.getDo,
        this.selectedDepartmentValue,
        this.selectedEmploymentValue,
        99
      );
    },
    // 取得正常処理（ユーザーリスト）
    getThenuser(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 取得正常処理（ユーザー）
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("氏名", "取得");
        }
      }
    },
    // 取得正常処理（明細部署選択リスト）
    getThendepartment(response) {
      var res = response.data;
      if (res.result) {
        this.departmentList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("部署", "取得");
        }
      }
    },
    // 取得正常処理（明細雇用形態選択リスト）
    getThenemployment(response) {
      var res = response.data;
      if (res.result) {
        this.employStatusList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("雇用形態", "取得");
        }
      }
    },
    // 取得正常処理（明細タイムテーブル対象選択リスト）
    getThentimetable(response) {
      var res = response.data;
      if (res.result) {
        this.timetableList = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細タイムテーブル", "取得");
        }
      }
    },
    // 取得正常処理（明細勤怠管理対象選択リスト）
    getThenmanagement(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_m = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細勤怠管理", "取得");
        }
      }
    },
    // 取得正常処理（明細コード権限選択リスト）
    getThenrole(response, value) {
      var res = response.data;
      if (res.result) {
        this.generalList_r = res.details;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          this.serverCatch("明細権限", "取得");
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        messages.push("ユーザーを" + eventtext + "しました。");
        messages.push(
          "個人のカレンダー設定する場合はカレンダー設定処理をしてください。"
        );
        this.htmlMessageSwal(eventtext + "完了", messages, "info", true, false);
        this.refreshUserList();
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザー", eventtext);
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーを" + eventtext + "しました");
        this.refreshUserList();
        this.getItem();
        this.count = this.details.length;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザ", eventtext);
        }
      }
    },
    // カード解除正常処理（明細）
    putThenCard(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーとカードの紐づけを解除しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザ", eventtext);
        }
      }
    },
    // 更新系正常処理（アップロード）
    putThenUp(response, eventtext) {
      var messages = [];
      var res = response.data;
      if (res.result) {
        this.$toasted.show("ユーザーを" + eventtext + "しました");
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          this.serverCatch("ユーザー", eventtext);
        }
      }
    },
    // 異常処理
    serverCatch(kbn, eventtext) {
      var messages = [];
      messages.push(kbn + "情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.form.name = "";
      this.form.id = "";
      this.form.code = "";
      this.selectedUserValue = "";
      this.selectedUserName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
    },
    // 新規作成用
    newItemClear() {
      this.form.id = "";
      this.form.code = "";
      this.form.name = "";
      this.form.kana = "";
      this.form.kill_from_date = "";
      this.form.official_position = "";
      this.form.email = "";
      this.form.mobile_email = "";
      this.form.password = "";
    },
    checkRowData(index) {
      if (
        this.details[index].department_code != "" &&
        this.details[index].department_code != null
      ) {
        return true;
      }
      if (
        this.details[index].employment_status != "" &&
        this.details[index].employment_status != null
      ) {
        return true;
      }
      if (this.details[index].name != "" && this.details[index].name != null) {
        return true;
      }
      if (this.details[index].kana != "" && this.details[index].kana != null) {
        return true;
      }
      if (
        this.details[index].official_position != "" &&
        this.details[index].official_position != null
      ) {
        return true;
      }
      if (
        this.details[index].working_timetable_no != "" &&
        this.details[index].working_timetable_no != null
      ) {
        return true;
      }
      if (
        this.details[index].email != "" &&
        this.details[index].email != null
      ) {
        return true;
      }
      if (
        this.details[index].password != "" &&
        this.details[index].password != null
      ) {
        return true;
      }
      if (
        this.details[index].management != "" &&
        this.details[index].management != null
      ) {
        return true;
      }
      if (this.details[index].role != "" && this.details[index].role != null) {
        return true;
      }
      if (
        this.details[index].kill_from_date != "" &&
        this.details[index].kill_from_date != null
      ) {
        return true;
      }
      return false;
    },
    // 最新リストの表示（部署）
    refreshDepartmentList() {
      this.showdepartmentlist = false;
      this.$nextTick(() => (this.showdepartmentlist = true));
    },
    // 最新リストの表示（氏名）
    refreshUserList() {
      this.showuserlist = false;
      this.$nextTick(() => (this.showuserlist = true));
    },
    // 最新リストの表示（明細部署）
    refreshaddDepartmentList() {
      this.showadddepartmentlist = false;
      this.$nextTick(() => (this.showadddepartmentlist = true));
    },
    // 最新リストの表示（明細部署）
    refreshreleaseCardbottun() {
      this.showrelease = false;
      this.$nextTick(() => (this.showrelease = true));
    }
  }
};
</script>