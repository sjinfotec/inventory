<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md-12 pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'タイムテーブルを設定する'"
            v-bind:header-text2="'複数の勤務時間を設定することでシフト編集ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-180"
                      id="basic-addon1"
                    >タイムテーブル選択</span>
                  </div>
                  <select-timetablelist v-if="showtimetablelist"
                    :placeholder-data="placeholderdata"
                    :blank-data="false"
                    v-on:change-event="timetableChanges"
                  >
                  </select-timetablelist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="errorslist.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(error,index) in errorslist" v-bind:key="index">{{ error }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆タイムテーブル名'"
            v-bind:header-text2="'一覧選択などでわかりやすい名称を入力します'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="errors.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-180"
                      id="basic-addon1"
                    >タイムテーブル名<span class="color-red">[必須]</span></span>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    v-model="form.name"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆所定労働時間設定'"
                v-bind:header-text2="'所定労働時間の開始・終了時刻を入力します'"
                v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- 所定労働開始時間 -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'所定労働開始時間'"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularFrom"
                  v-on:change-event="timeChanges($event, '1')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'所定労働終了時間'"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularTo"
                  v-on:change-event="timeChanges($event, '2')">
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆休憩時間設定'"
                v-bind:header-text2="'5パターンまで設定できます'"
                v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- 休憩開始時間 A -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩開始時間 A'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestFrom1"
                  v-on:change-event="timeChanges($event, '3')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 A'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestTo1"
                  v-on:change-event="timeChanges($event, '4')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- 休憩開始時間 B -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩開始時間 B'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestFrom2"
                  v-on:change-event="timeChanges($event, '5')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 B'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestTo2"
                  v-on:change-event="timeChanges($event, '6')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- 休憩開始時間 C -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩開始時間 C'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestFrom3"
                  v-on:change-event="timeChanges($event, '7')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 C'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestTo3"
                  v-on:change-event="timeChanges($event, '8')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- 休憩開始時間 D -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩開始時間 D'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestFrom4"
                  v-on:change-event="timeChanges($event, '9')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 D'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestTo4"
                  v-on:change-event="timeChanges($event, '10')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- 休憩開始時間 E -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩開始時間 E'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestFrom5"
                  v-on:change-event="timeChanges($event, '11')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 E'"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.regularRestTo5"
                  v-on:change-event="timeChanges($event, '12')">
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆深夜残業時間設定'"
                v-bind:header-text2="''"
                v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- 深夜残業開始時間 -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'深夜残業開始時間'"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="form.irregularMidNightFrom"
                  v-on:change-event="timeChanges($event, '13')">
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'深夜残業終了時間'"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="form.irregularMidNightTo"
                  v-on:change-event="timeChanges($event, '14')">
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <btn-work-time
                  v-on:storeclick-event="store_confirm"
                  v-bind:btn-mode="'store'"
                  v-bind:is-push="false">
                </btn-work-time>
                <message-data v-bind:message-datas="messagedatastore" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.panel contents -->
        </div>
      </div>
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='EDT'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRow">+</button>
              </span>
              {{ this.timeTableName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新たに追加することができます</span>
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <div v-for="n in count" v-bind:key="n">
              <!-- .row -->
              <div class="row justify-content-between" v-if="details[(n-1) * 7].result == 1">
                <!-- panel header -->
                <div class="col-md-2 pb-2">
                  <col-note
                    v-bind:item-name="'No.' + n + ' 現在適用中'"
                    v-bind:item-control="'LIGHT'"
                    v-bind:item-note="''"
                  ></col-note>
                </div>
                <!-- /.panel header -->
              </div>
              <div class="row justify-content-between" v-else>
                <!-- panel header -->
                <div class="col-md-2 pb-2">
                  <col-note
                    v-bind:item-name="'No.' + n"
                    v-bind:item-control="'LIGHT'"
                    v-bind:item-note="''"
                  ></col-note>
                </div>
                <!-- /.panel header -->
              </div>
              <!-- /row -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="errorsfix.length">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <ul class="error-red color-red">
                    <li v-for="(error,index) in errorsfix[n]" v-bind:key="index">{{ error }}</li>
                  </ul>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel contents -->
                <!-- panel header -->
                <daily-working-information-panel-header
                  v-bind:header-text1="'◆タイムテーブル名'"
                  v-bind:header-text2="'一覧選択などでわかりやすい名称を入力します'"
                  v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                ></daily-working-information-panel-header>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- .col -->
                <div class="col-md-12 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-180"
                        id="basic-addon1"
                      >タイムテーブル名<span class="color-red">[必須]</span></span>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      v-model="details[(n-1) * 7].name"
                      name="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <daily-working-information-panel-header
                  v-bind:header-text1="'◆適用開始設定'"
                  v-bind:header-text2="'適用開始日付を入力します。入力した日付から有効となります。'"
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
                        for="target_fromdate"
                      >適用開始日付<span class="color-red">[必須]</span></span>
                    </div>
                    <input-datepicker
                      v-bind:default-date="details[(n-1) * 7].apply_term_from"
                      v-on:change-event="applydateDetaileChanges($event, n)"
                      v-on:clear-event="applydateDetaileCleared($event, n)"
                    ></input-datepicker>
                  </div>
                </div>
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <daily-working-information-panel-header
                  v-bind:header-text1="'◆所定労働時間設定'"
                  v-bind:header-text2="'所定労働時間の開始・終了時刻を入力します'"
                  v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                ></daily-working-information-panel-header>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- 所定労働開始時間 -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'所定労働開始時間'"
                    v-bind:item-required="true"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 0])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'所定労働終了時間'"
                    v-bind:item-required="true"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 0])">
                  ></input-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <daily-working-information-panel-header
                  v-bind:header-text1="'◆休憩時間設定'"
                  v-bind:header-text2="'5パターンまで設定できます'"
                  v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                ></daily-working-information-panel-header>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <div class="row justify-content-between">
                <!-- 休憩開始時間 A -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩開始時間 A'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 1].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 1])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩終了時間 A'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 1].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 1])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- 休憩開始時間 B -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩開始時間 B'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 2].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 2])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩終了時間 B'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 2].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 2])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- 休憩開始時間 C -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩開始時間 C'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 3].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 3])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩終了時間 C'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 3].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 3])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- 休憩開始時間 D -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩開始時間 D'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 4].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 4])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩終了時間 D'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 4].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 4])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- 休憩開始時間 E -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩開始時間 E'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 5].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 5])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'休憩終了時間 E'"
                    v-bind:item-required="false"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 5].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 5])">
                  ></input-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- panel header -->
                <daily-working-information-panel-header
                  v-bind:header-text1="'◆深夜残業時間設定'"
                  v-bind:header-text2="''"
                  v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                ></daily-working-information-panel-header>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- 深夜残業開始時間 -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'深夜残業開始時間'"
                    v-bind:item-required="true"
                    v-bind:place-holder="'※開始時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 6].from_time"
                    v-on:change-event="timeDetailChanges1($event, [n, 6])">
                  ></input-time>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <input-time
                    v-bind:item-title="'深夜残業終了時間'"
                    v-bind:item-required="true"
                    v-bind:place-holder="'※終了時刻を半角で入力します'"
                    v-bind:value-data="details[(n-1) * 7 + 6].to_time"
                    v-on:change-event="timeDetailChanges2($event, [n, 6])">
                  ></input-time>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="col-md-12 pb-2 text-align-right">
                <div class="btn-group">
                  <button
                    type="button"
                    class="btn btn-success"
                    @click="fix_confirm(n)"
                  >この内容で更新する</button>
                </div>
                <!-- <div class="btn-group" v-if="form.no != 1">
                  <button
                    type="button"
                    class="btn btn-danger"
                    @click="delClick(n)"
                  >削除する</button>
                </div> -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="col-md-12 pb-2">
                <message-data v-bind:message-datas="messagedatafix" v-bind:message-class="'warning'"></message-data>
              </div>
              <!-- /.row -->
            </div>
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- /main contentns row -->
    <!-- modal modalは未使用 20191126-->
    <modal name="add-time-table" :width="800" :height="800" :pivotY="0.4" v-model="selectMode">
      <div class="card">
        <div class="card-header">新しい有効期間で追加する</div>
        <div class="card-body">
          <!-- .row -->
          <div class="row justify-content-between" v-if="errors.length">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <ul class="error-red color-red">
                <li v-for="(error,index) in errors" v-bind:key="index">{{ error }}</li>
              </ul>
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
                    class="input-group-text font-size-sm line-height-xs label-width-180"
                    id="basic-addon1"
                  >タイムテーブル名</span>
                </div>
                <input type="text" class="form-control" v-model="add.name" name="name" />
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
                  >有効期間</span>
                </div>
                <input type="date" class="form-control" v-model="add.apply_term_from" name="term" />
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
                  >所定労働開始時間</span>
                </div>
                <input type="time" class="form-control" v-model="add.regularFrom" name="syoteifrom" />
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
                  >所定労働終了時間</span>
                </div>
                <input type="time" class="form-control" v-model="add.regularTo" name="syoteito" />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header col-12 bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">休憩時間設定</h1>
              <span class="float-sm-right font-size-sm">5パターンまで設定できます</span>
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
                  >休憩開始時間 A</span>
                </div>
                <input
                  class="form-control"
                  type="time"
                  v-model="add.regularRestFrom1"
                  name="syoteifrom"
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
                  >休憩終了時間 A</span>
                </div>
                <input
                  class="form-control"
                  type="time"
                  v-model="add.regularRestTo1"
                  name="syoteito"
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
                  >休憩開始時間 B</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestFrom2"
                  name="syoteifrom"
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
                  >休憩終了時間 B</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestTo2"
                  name="syoteito"
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
                  >休憩開始時間 C</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestFrom3"
                  name="syoteifrom"
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
                  >休憩終了時間 C</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestTo3"
                  name="syoteito"
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
                  >休憩開始時間 D</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestFrom4"
                  name="syoteifrom"
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
                  >休憩終了時間 D</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestTo4"
                  name="syoteito"
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
                  >休憩開始時間 E</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestFrom5"
                  name="syoteifrom"
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
                  >休憩終了時間 E</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.regularRestTo5"
                  name="syoteito"
                />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- panel header -->
            <div class="card-header bg-transparent pb-2 border-0">
              <h1 class="float-sm-left font-size-rg">深夜残業時間設定</h1>
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
                  >深夜残業開始時間</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.irregularMidNightFrom"
                  name="syoteifrom"
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
                  >深夜残業終了時間</span>
                </div>
                <input
                  type="time"
                  class="form-control"
                  v-model="add.irregularMidNightTo"
                  name="syoteito"
                />
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button
                  type="button"
                  class="btn btn-primary btn-lg font-size-rg w-100"
                  v-on:click="addTime()"
                >追加する</button>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button
                  type="button"
                  class="btn btn-warning btn-lg font-size-rg w-100"
                  v-on:click="hide"
                >キャンセル</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
      </div>
    </modal>
    <!-- /modal -->
  </div>
</template>
<script>
import toasted from "vue-toasted";
import moment from "moment";

export default {
  name: "CreateTimeTable",
  data() {
    return {
      form: {
        name: "",
        apply_term_from: "",
        id: "",
        no: "",
        regularFrom: "",
        regularTo: "",
        regularRestFrom1: "",
        regularRestTo1: "",
        regularRestFrom2: "",
        regularRestTo2: "",
        regularRestFrom3: "",
        regularRestTo3: "",
        regularRestFrom4: "",
        regularRestTo4: "",
        regularRestFrom5: "",
        regularRestTo5: "",
        irregularMidNightFrom: "",
        irregularMidNightTo: ""
      },
      add: {
        name: "",
        apply_term_from: "",
        id: "",
        no: "",
        regularFrom: "",
        regularTo: "",
        regularRestFrom1: "",
        regularRestTo1: "",
        regularRestFrom2: "",
        regularRestTo2: "",
        regularRestFrom3: "",
        regularRestTo3: "",
        regularRestFrom4: "",
        regularRestTo4: "",
        regularRestFrom5: "",
        regularRestTo5: "",
        irregularMidNightFrom: "",
        irregularMidNightTo: ""
      },
      showtimetablelist: true,
      placeholderdata: "タイムテーブルを選択すると編集モードになります",
      timeTableName: "",
      timeTableList: [],
      details: [],
      selectMode: "",
      errorslist: [],
      errors: [],
      errorsfix: [],
      count: 0,
      before_count: 0,
      closingYm: "",
      closingYmd: "",
      oldId: "",
      errcnt: 0,
      messagedatastore: [],
      messagedatafix: []
    };
  },
  methods: {
    // 新規作成時
    checkForm: function() {
      var flag = true;
      this.errors = [];
      this.messagedatastore = [];
      this.errcnt = 0;
      if (this.form.name == "" || this.form.name == null) {
        flag = false;
        this.errcnt++;
        this.errors.push("タイムテーブル名を入力してください");
      }
      if (this.form.name.length > 191) {
        flag = false;
        this.errcnt++;
        this.errors.push("タイムテーブル名称の文字数は 191 文字内で入力してください");
      }
      if (this.form.regularFrom == "" || this.form.regularFrom == null) {
        flag = false;
        this.errcnt++;
        this.errors.push("所定労働開始時間を入力してください");
      }
      if (this.form.regularTo == "" || this.form.regularTo == null) {
        flag = false;
        this.errcnt++;
        this.errors.push("所定労働終了時間を入力してください");
      }
      if (this.form.regularRestFrom1 != "" && this.form.regularRestFrom1 != null) {
        if (this.form.regularRestTo1 == "" || this.form.regularRestTo1 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩終了時間 Aを入力してください");
        }
      }
      if (this.form.regularRestTo1 != "" && this.form.regularRestTo1 != null) {
        if (this.form.regularRestFrom1 == "" || this.form.regularRestFrom1 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩開始時間 Aを入力してください");
        }
      }
      if (this.form.regularRestFrom2 != "" && this.form.regularRestFrom2 != null) {
        if (this.form.regularRestTo2 == "" || this.form.regularRestTo2 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩終了時間 Bを入力してください");
        }
      }
      if (this.form.regularRestTo2 != "" && this.form.regularRestTo2 != null) {
        if (this.form.regularRestFrom2 == "" || this.form.regularRestFrom2 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩開始時間 Bを入力してください");
        }
      }
      if (this.form.regularRestFrom3 != "" && this.form.regularRestFrom3 != null) {
        if (this.form.regularRestTo3 == "" || this.form.regularRestTo3 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩終了時間 Cを入力してください");
        }
      }
      if (this.form.regularRestTo3 != "" && this.form.regularRestTo3 != null) {
        if (this.form.regularRestFrom3 == "" || this.form.regularRestFrom3 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩開始時間 Cを入力してください");
        }
      }
      if (this.form.regularRestFrom4 != "" && this.form.regularRestFrom4 != null) {
        if (this.form.regularRestTo4 == "" || this.form.regularRestTo4 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩終了時間 Dを入力してください");
        }
      }
      if (this.form.regularRestTo4 != "" && this.form.regularRestTo4 != null) {
        if (this.form.regularRestFrom4 == "" || this.form.regularRestFrom4 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩開始時間 Dを入力してください");
        }
      }
      if (this.form.regularRestFrom5 != "" && this.form.regularRestFrom5 != null) {
        if (this.form.regularRestTo5 == "" || this.form.regularRestTo5 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩終了時間 Eを入力してください");
        }
      }
      if (this.form.regularRestTo5 != "" && this.form.regularRestTo5 != null) {
        if (this.form.regularRestFrom5 == "" || this.form.regularRestFrom5 == null) {
          flag = false;
          this.errcnt++;
          this.errors.push("休憩開始時間 Eを入力してください");
        }
      }
      if (this.form.irregularMidNightFrom == "" || this.form.irregularMidNightFrom == null) {
        flag = false;
        this.errcnt++;
        this.errors.push("深夜残業開始時間を入力してください");
      }
      if (this.form.irregularMidNightTo == "" || this.form.irregularMidNightTo == null) {
        flag = false;
        this.errcnt++;
        this.errors.push("深夜残業終了時間を入力してください");
      }

      if (!flag) {
        this.messagedatastore.push(this.errcnt + "項目にエラーがあります。");
      }

      return flag;
    },
    // 修正時
    checkFormfix: function(n) {
      var flag = true;
      var cnt = 0;
      this.errorsfix = [];
      this.errorsfix[n] = [];
      this.messagedatafix = [];
      this.errcnt = 0;

      if (this.details[(n-1) * 7].name == "" || this.details[(n-1) * 7].name == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "タイムテーブル名を入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7].name.length > 191) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "タイムテーブル名称の文字数は 191 文字内で入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7].apply_term_from == "" || this.details[(n-1) * 7].apply_term_from == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "適用開始日付を入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7].from_time == "" || this.details[(n-1) * 7].from_time == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "所定労働開始時間を入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7].to_time == "" || this.details[(n-1) * 7].to_time == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "所定労働終了時間を入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7 + 1].from_time != "" && this.details[(n-1) * 7 + 1].from_time != null) {
        if (this.details[(n-1) * 7 + 1].to_time == "" || this.details[(n-1) * 7 + 1].to_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩終了時間 Aを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 1].to_time != "" && this.details[(n-1) * 7 + 1].to_time != null) {
        if (this.details[(n-1) * 7 + 1].from_time == "" || this.details[(n-1) * 7 + 1].from_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩開始時間 Aを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 2].from_time != "" && this.details[(n-1) * 7 + 2].from_time != null) {
        if (this.details[(n-1) * 7 + 2].to_time == "" || this.details[(n-1) * 7 + 2].to_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩終了時間 Bを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 2].to_time != "" && this.details[(n-1) * 7 + 2].to_time != null) {
        if (this.details[(n-1) * 7 + 2].from_time == "" || this.details[(n-1) * 7 + 2].from_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩開始時間 Bを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 3].from_time != "" && this.details[(n-1) * 7 + 3].from_time != null) {
        if (this.details[(n-1) * 7 + 3].to_time == "" || this.details[(n-1) * 7 + 3].to_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩終了時間 Cを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 3].to_time != "" && this.details[(n-1) * 7 + 3].to_time != null) {
        if (this.details[(n-1) * 7 + 3].from_time == "" || this.details[(n-1) * 7 + 3].from_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩開始時間 Cを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 4].from_time != "" && this.details[(n-1) * 7 + 4].from_time != null) {
        if (this.details[(n-1) * 7 + 4].to_time == "" || this.details[(n-1) * 7 + 4].to_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩終了時間 Dを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 4].to_time != "" && this.details[(n-1) * 7 + 4].to_time != null) {
        if (this.details[(n-1) * 7 + 4].from_time == "" || this.details[(n-1) * 7 + 4].from_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩開始時間 Dを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 5].from_time != "" && this.details[(n-1) * 7 + 5].from_time != null) {
        if (this.details[(n-1) * 7 + 5].to_time == "" || this.details[(n-1) * 7 + 5].to_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩終了時間 Eを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 5].to_time != "" && this.details[(n-1) * 7 + 5].to_time != null) {
        if (this.details[(n-1) * 7 + 5].from_time == "" || this.details[(n-1) * 7 + 5].from_time == null) {
          flag = false;
          this.errcnt++;
          this.errorsfix[n][cnt] = "休憩開始時間 Eを入力してください";
          cnt++;
        }
      }
      if (this.details[(n-1) * 7 + 6].from_time == "" || this.details[(n-1) * 7 + 6].from_time == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "深夜残業開始時間を入力してください";
        cnt++;
      }
      if (this.details[(n-1) * 7 + 6].to_time == "" || this.details[(n-1) * 7 + 6].to_time == null) {
        flag = false;
        this.errcnt++;
        this.errorsfix[n][cnt] = "深夜残業終了時間を入力してください";
        cnt++;
      }

      if (!flag) {
        this.messagedatafix.push(this.errcnt + "項目にエラーがあります。");
      }

      return flag;
    },
    // タイムテーブル選択が変更された場合の処理
    timetableChanges: function(value, name) {
      this.inputClear();
      this.timeTableName = name;
      this.placeholderdata = name;
      this.details = [];
      this.form.no = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.getDetail();
      }
    },
    // 時刻が変更された場合の処理
    timeChanges: function(value, type) {
      if (type == '1') {
        this.form.regularFrom = value;
      } else if (type == '2') {
        this.form.regularTo = value;
      } else if (type == '3') {
        this.form.regularRestFrom1 = value;
      } else if (type == '4') {
        this.form.regularRestTo1 = value;
      } else if (type == '5') {
        this.form.regularRestFrom2 = value;
      } else if (type == '6') {
        this.form.regularRestTo2 = value;
      } else if (type == '7') {
        this.form.regularRestFrom3 = value;
      } else if (type == '8') {
        this.form.regularRestTo3 = value;
      } else if (type == '9') {
        this.form.regularRestFrom4 = value;
      } else if (type == '10') {
        this.form.regularRestTo4 = value;
      } else if (type == '11') {
        this.form.regularRestFrom5 = value;
      } else if (type == '12') {
        this.form.regularRestTo5 = value;
      } else if (type == '13') {
        this.form.irregularMidNightFrom = value;
      } else if (type == '14') {
        this.form.irregularMidNightTo = value;
      }
    },
    // 有効期間が変更された場合の処理
    applydateChanges: function(value) {
      console.log('applydateChanges value = ' + value);
      this.form.apply_term_from = value;
    },
    // 有効期間が変更された場合の処理（明細）
    applydateDetaileChanges: function(value, typeRow) {
      console.log('applydateDetaileChanges value = ' + value);
      this.details[(typeRow-1) * 7].apply_term_from = value;
    },
    // 有効期間がクリアされた場合の処理（明細）
    applydateDetaileCleared: function(value, typeRow) {
      console.log('applydateDetaileCleared value = ' + value);
      this.details[(typeRow-1) * 7].apply_term_from = value;
    },
    
    // 開始時刻が変更された場合の処理
    timeDetailChanges1: function(value, arrayNum) {
      this.details[(arrayNum[0]-1) * 7 + arrayNum[1]].from_time = value;
    },
    // 終了時刻が変更された場合の処理
    timeDetailChanges2: function(value, arrayNum) {
      this.details[(arrayNum[0]-1) * 7 + arrayNum[1]].to_time = value;
    },
    getDetail() {
      this.$axios
        .get("/create_time_table/get", {
          params: {
            no: this.form.no
          }
        })
        .then(response => {
          var res = response.data;
          if (res.result) {
            this.count = res.details.length / 7; // １データにつき７レコードある
            this.before_count = this.count;
            this.details = res.details
          } else {
            if (res.messagedata.length > 0) {
              this.errorslist = res.messagedata;
            } else {
              this.alert("error", "タイムテーブル選択リスト取得処理でエラーが発生しました", "エラー");
            }
          }
        })
        .catch(reason => {
          alert("タイムテーブル選択リスト取得処理でエラーが発生しました");
        });
    },
    /*delTime() {
      this.getDetail();
      console.log("del");
    },
    show: function() {
      this.add.no = this.selectMode;
      this.$modal.show("add-time-table");
    },
    hide: function() {
      this.$modal.hide("add-time-table");
    }, */
    // 削除するボタン押下splice
    delClick(value_n) {
      this.validate = this.checkRowInput(value_n);
      if (this.validate) {
      }
    },
    store_confirm: function() {
      var flag = this.checkForm();
      if (flag) {
        this.$swal({
          title: "確認",
          text: "この内容で登録しますか？",
          icon: "info",
          buttons: true,
          dangerMode: true
        }).then(result  => {
          if (result) {
            this.store("store");
          }
        });
      }
    },
    fix_confirm: function(n) {
      var flag = this.checkFormfix(n);
      if (flag) {
        this.closingYm = moment(new Date()).subtract(1, 'M').format('YYYYMM');
        this.$axios
          .get("/get_closing_day", {
            params: {
              target_date: this.closingYm
            }
          })
          .then(response => {
            var res = response.data;
            this.closingYmd =String(this.closingYm) + String(res);
            this.dt = moment(this.details[(n-1) * 7].apply_term_from).format('YYYYMMDD');
            if (this.closingYmd >= this.dt) {
              this.fix_warning_confirm(n);
            } else {
              this.fix_normal_confirm(n);
            }
          })
          .catch(reason => {
            this.alert("error", "締日取得エラー。集計方法基本設定の締日設定を確認してください。", "エラー");
          });
      }
    },
    fix_normal_confirm: function(n) {
      this.$swal({
        title: "確認",
        text: "この内容で編集を確定しますか？",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(result  => {
        if (result) {
          if (this.details[(n-1) * 7].id == "" || this.details[(n-1) * 7].id == null) {
            // 追加
            this.detailesTofrm(n);
            this.store("fix");
          } else {
            this.fix(n);
          }
        }
      });
    },
    fix_warning_confirm: function(n) {
      this.$swal({
        title: "確認",
        text: "適用開始日が前月の締日" + moment(this.closingYmd).format('YYYY年MM月DD日') + "以前ですが" + "\n" + "前月締日以前のデータは自動集計されないため、" + "\n" + "月次集計の一括集計を行う必要があります。" + "\n" + "この内容で編集を確定しますか？",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(result  => {
        if (result) {
          if (this.details[(n-1) * 7].id == "" || this.details[(n-1) * 7].id == null) {
            // 追加
            this.detailesTofrm(n);
            this.store("fix");
          } else {
            this.fix(n);
          }
        }
      });
    },
    store(kbn) {
      this.$axios
        .post("/create_time_table/store", {
          kbn: kbn,
          no: this.form.no,
          name: this.form.name,
          details: this.form
        })
        .then(response => {
          var res = response.data;
          if (res.result) {
            this.alert("success", "タイムテーブルを登録しました", "登録完了");
            this.inputAddClear();
            this.refreshtTimeTableList();
            this.form.no = res.no;
          } else {
            if (res.messagedata.length > 0) {
              this.errors = res.messagedata;
            } else {
              this.alert("error", "タイムテーブル登録に失敗しました", "エラー");
            }
          }
        })
        .catch(reason => {
          this.alert("error", "タイムテーブル登録に失敗しました", "エラー");
        });
    },
    fix: function(n) {
      this.$axios
        .post("/create_time_table/fix", {
          no: n,
          details: this.details
        })
        .then(response => {
          var res = response.data;
          if (res.result) {
            this.alert("success", "タイムテーブルを修正しました", "修正完了");
            this.getDetail();
            this.refreshtTimeTableList();
          } else {
            if (res.messagedata.length > 0) {
              this.errors = res.messagedata;
            } else {
              this.alert("error", "タイムテーブル修正に失敗しました", "エラー");
            }
          }

        })
        .catch(reason => {
          this.alert("error", "タイムテーブルの修正に失敗しました", "エラー");
        });
    },
    addTime() {
      this.$axios
        .post("/create_time_table/add", {
          details: this.add
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", "新規有効期間で追加しました", "追加完了");
            //this.hide();
            this.inputAddClear();
            this.refreshtTimeTableList();
            //this.getDetail();
          } else {
          }
        })
        .catch(reason => {
          this.alert("error", "追加に失敗しました", "エラー");
        });
    },
    alert: function(state, message, title) {
      this.$swal(title, message, state);
    },
    alertDelConf: function(state, date) {
      this.$swal({
        title: "確認",
        text: "削除してもよろしいですか？",
        icon: state,
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.del(date);
        } else {
        }
      });
    },
    refreshtTimeTableList() {
      // 最新リストの表示
      this.showtimetablelist = false;
      this.$nextTick(() => (this.showtimetablelist = true));
    },
    addSuccess() {
      this.alert("success", "登録しました", "登録成功");
      this.selectMode = this.form.no;
      this.refreshtTimeTableList();
      this.getDetail();
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    // 削除
    del: function(date) {
      this.$axios
        .post("/create_time_table/del", {
          no: this.selectMode,
          apply_term_from: date
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert(
              "success",
              "選択したタイムテーブルを削除しました",
              "削除"
            );
            this.getDetail();
            this.refreshtTimeTableList();
            this.selectMode = "";
          } else {
          }
        })
        .catch(reason => {
          this.alert("error", "削除でエラーが発生しました", "エラー");
        });
    },
    appendRow: function() {
      if (this.before_count < this.count) {
          this.alert("error", "１度に追加できる情報は１個です。編集確定してから追加してください。", "エラー");
      } else {
        for( var i=0;i<7;i++ ) {
          this.details.unshift({
            id: "",
            no: this.form.no,
            name: "",
            working_time_kubun: "",
            apply_term_from: moment(new Date()).format('YYYY-MM-DD'),
            from_time: "",
            to_time: "",
            result: "",
            created_user: "",
            updated_user: ""
          });
        }
        this.count = this.details.length / 7; // １データにつき７レコードある
      }
    },
    // 行入力チェック
    checkRowInput: function(value_n) {
      var flag = false;
      if (this.details[(n-1) * 7].name == "" &&
        this.details[(n-1) * 7].apply_term_from == "" &&
        this.details[(n-1) * 7].from_time == "" &&
        this.details[(n-1) * 7].to_time == "" &&
        this.details[(n-1) * 7 + 1].from_time == "" &&
        this.details[(n-1) * 7 + 1].to_time == "" &&
        this.details[(n-1) * 7 + 2].from_time == "" &&
        this.details[(n-1) * 7 + 2].to_time == "" &&
        this.details[(n-1) * 7 + 3].from_time == "" &&
        this.details[(n-1) * 7 + 3].to_time == "" &&
        this.details[(n-1) * 7 + 4].from_time == "" &&
        this.details[(n-1) * 7 + 4].to_time == "" &&
        this.details[(n-1) * 7 + 5].from_time == "" &&
        this.details[(n-1) * 7 + 5].to_time == "" &&
        this.details[(n-1) * 7 + 6].from_time == "" &&
        this.details[(n-1) * 7 + 6].to_time == "") {
        flag = false;
      } else {
        flag = true;
      }
      return flag;
    },
    inputClear() {
      this.form.name = "";
      this.form.apply_term_from = "";
      this.form.id = "";
      this.form.no = "";
      this.form.regularFrom = "";
      this.form.regularTo = "";
      this.form.regularRestFrom1 = "";
      this.form.regularRestTo1 = "";
      this.form.regularRestFrom2 = "";
      this.form.regularRestTo2 = "";
      this.form.regularRestFrom3 = "";
      this.form.regularRestTo3 = "";
      this.form.regularRestFrom4 = "";
      this.form.regularRestTo4 = "";
      this.form.regularRestFrom5 = "";
      this.form.regularRestTo5 = "";
      this.form.irregularMidNightFrom = "";
      this.form.irregularMidNightTo = "";
      this.errorslist = [];
      this.errors = [];
      this.errorsfix = [];
      this.messagedatastore = [];
      this.messagedatafix = [];
    },
    inputAddClear() {
      this.add.name = "";
      this.add.apply_term_from = "";
      this.add.id = "";
      this.add.no = "";
      this.add.regularFrom = "";
      this.add.regularTo = "";
      this.add.regularRestFrom1 = "";
      this.add.regularRestTo1 = "";
      this.add.regularRestFrom2 = "";
      this.add.regularRestTo2 = "";
      this.add.regularRestFrom3 = "";
      this.add.regularRestTo3 = "";
      this.add.regularRestFrom4 = "";
      this.add.regularRestTo4 = "";
      this.add.regularRestFrom5 = "";
      this.add.regularRestTo5 = "";
      this.add.irregularMidNightFrom = "";
      this.add.irregularMidNightTo = "";
    },
    detailesTofrm(n) {
      this.form.name = this.details[(n-1) * 7].name;
      this.form.apply_term_from = this.details[(n-1) * 7].apply_term_from;
      this.form.id = "";
      this.form.no = this.details[(n-1) * 7].no;
      this.form.regularFrom = this.details[(n-1) * 7].from_time;
      this.form.regularTo = this.details[(n-1) * 7].to_time;
      this.form.regularRestFrom1 = this.details[(n-1) * 7 + 1].from_time;
      this.form.regularRestTo1 = this.details[(n-1) * 7 + 1].to_time;
      this.form.regularRestFrom2 = this.details[(n-1) * 7 + 2].from_time;
      this.form.regularRestTo2 = this.details[(n-1) * 7 + 2].to_time;
      this.form.regularRestFrom3 = this.details[(n-1) * 7 + 3].from_time;
      this.form.regularRestTo3 = this.details[(n-1) * 7 + 3].to_time;
      this.form.regularRestFrom4 = this.details[(n-1) * 7 + 4].from_time;
      this.form.regularRestTo4 = this.details[(n-1) * 7 + 4].to_time;
      this.form.regularRestFrom5 = this.details[(n-1) * 7 + 5].from_time;
      this.form.regularRestTo5 = this.details[(n-1) * 7 + 5].to_time;
      this.form.irregularMidNightFrom = this.details[(n-1) * 7 + 6].from_time;
      this.form.irregularMidNightTo = this.details[(n-1) * 7 + 6].to_time;
    }
  }
};
</script>
<style scoped>
.padding-top-l {
  padding-top: 50px;
}

.padding-left-l {
  padding-left: 25px;
}

.padding-top-m {
  padding-top: 20px;
}

.text-align-right {
  text-align: right;
}
</style>
