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
            v-bind:header-text1="'タイムテーブルを設定する'"
            v-bind:header-text2="'タイムテーブルの登録や変更ができます'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- ----------- 選択リスト START ---------------- -->
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span
                      class="input-group-text font-size-sm line-height-xs label-width-180"
                      id="basic-addon1"
                    >タイムテーブル選択<span class="color-red">[必須]</span></span>
                  </div>
                  <select-timetablelist v-if="showlist"
                    ref="selecttimetablelist"
                    v-bind:blank-data="false"
                    v-bind:placeholder-data="'タイムテーブルを選択すると編集モードになります'"
                    v-bind:setting-value="selectedValue"
                    v-bind:add-new="true"
                    v-bind:date-value="''"
                    v-bind:kill-value="valuekillcheck"
                    v-bind:row-index=0
                    v-bind:set-shift="true"
                    v-on:change-event="itemChanges"
                  ></select-timetablelist>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- 選択リスト END ---------------- -->
          </div>
          <!-- panel contents -->
        </div>
      </div>
      <!-- /.panel -->
      <!-- ========================== 検索部 END ========================== -->
      <!-- ========================== 新規部 START ========================== -->
      <!-- .panel -->
      <div class="col-md-12 pt-3" v-if="selectMode=='NEW'">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆タイムテーブル登録'"
            v-bind:header-text2="'タイムテーブル名は一覧選択などでわかりやすい名称を入力します'"
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
                  <li v-for="(messagevalidate,index) in messagevalidatesNew" v-bind:key="index">{{ messagevalidate }}</li>
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
                    data-toggle="tooltip"
                    data-placement="top"
                    v-bind:title="'一覧選択などでわかりやすい名称を入力します'"
                    name="name"
                  />
                </div>
              </div>
              <!-- /.col -->
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
            <!-- 所定労働時間 -->
            <!-- .row -->
            <div
              v-for="(item, index) in get_RegularTime" v-bind:key="index"
              class="row justify-content-between"
            >
              <!-- .col -->
              <div class="col-md-6 pb-2" v-if="index === 0">
                <input-time
                  v-bind:item-title="'所定労働開始時間 '+(index+1)"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="regularTime[index]['fromTime']"
                  v-on:change-event="timeChanges($event, '1', index)"
                ></input-time>
              </div>
              <div class="col-md-6 pb-2" v-else>
                <input-time
                  v-bind:item-title="'所定労働開始時間 '+(index+1)"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="regularTime[index]['fromTime']"
                  v-on:change-event="timeChanges($event, '1', index)"
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2" v-if="index === 0">
                <input-time
                  v-bind:item-title="'所定労働終了時間 '+(index+1)"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="regularTime[index]['toTime']"
                  v-on:change-event="timeChanges($event, '2', index)"
                ></input-time>
              </div>
              <div class="col-md-6 pb-2" v-else>
                <input-time
                  v-bind:item-title="'所定労働終了時間 '+(index+1)"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="regularTime[index]['toTime']"
                  v-on:change-event="timeChanges($event, '2', index)"
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.所定労働時間 -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆休憩時間設定'"
                v-bind:header-text2="'労働時間に集計しない時間帯を設定します'"
                v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- 休憩時間 -->
            <!-- .row -->
            <div
              class="row justify-content-between"
              v-for="(item,index) in get_RegularRestTime" v-bind:key="index"
            >
              <!-- .col -->
              <div
                class="col-md-6 pb-2"
              >
                <input-time
                  v-bind:item-title="'休憩開始時間 '+(index+1)"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="regularRestTime[index]['fromTime']"
                  v-on:change-event="timeChanges($event, '3', index)"
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'休憩終了時間 '+(index+1)"
                  v-bind:item-required="false"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="regularRestTime[index]['toTime']"
                  v-on:change-event="timeChanges($event, '4', index)"
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.休憩時間 -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆深夜労働時間設定'"
                v-bind:header-text2="''"
                v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
              ></daily-working-information-panel-header>
              <!-- /.panel header -->
            </div>
            <!-- /.row -->
            <!-- 深夜労働時間 -->
            <!-- .row -->
            <div
              class="row justify-content-between"
              v-for="(item,index) in get_MidNightTime" v-bind:key="index"
            >
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'深夜労働開始時間 '+(index+1)"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※開始時刻を半角で入力します'"
                  v-bind:value-data="midnightTime[index]['fromTime']"
                  v-on:change-event="timeChanges($event, '5', index)"
                ></input-time>
              </div>
              <!-- /.col -->
              <!-- .col -->
              <div class="col-md-6 pb-2">
                <input-time
                  v-bind:item-title="'深夜労働終了時間 '+(index+1)"
                  v-bind:item-required="true"
                  v-bind:place-holder="'※終了時刻を半角で入力します'"
                  v-bind:value-data="midnightTime[index]['toTime']"
                  v-on:change-event="timeChanges($event, '6', index)"
                ></input-time>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- /.深夜労働時間 -->
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
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'◆タイムテーブル編集'"
            v-bind:header-text2="''"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 START ---------------- -->
          <!-- panel header -->
          <div class="card-header bg-transparent pt-3 border-0">
            <h1 class="float-sm-left font-size-rg">
              <span>
                <button class="btn btn-success btn-lg font-size-rg" v-on:click="appendRowClick">+</button>
              </span>
              {{ this.selectedName }}
            </h1>
            <span class="float-sm-right font-size-sm">「＋」アイコンで新規に追加することができます</span>
          </div>
          <!-- /.panel header -->
          <!-- ----------- 「＋」アイコン部 END ---------------- -->
          <!-- ----------- 編集入力部 START ---------------- -->
          <!-- main contentns row -->
          <div class="card-body pt-2" v-if="details.length">
            <!-- panel contents -->
            <!-- ----------- メッセージ部 START ---------------- -->
            <!-- .row -->
            <div class="row justify-content-between" v-if="messagevalidatesEdt.length">
              <!-- col -->
              <div class="col-md-12 pb-2">
                <ul class="error-red color-red">
                  <li v-for="(messagevalidate,index) in messagevalidatesEdt" v-bind:key="index">{{ messagevalidate }}</li>
                </ul>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- ----------- メッセージ部 END ---------------- -->
            <!-- ----------- 項目部 START ---------------- -->
            <!-- panel contents -->
            <div v-for="index in count" v-bind:key="index">
              <!-- 現在適用中 ----------------------------------------------------------------->
              <div v-if="details[(index-1) * 7].result != ''">
                <!-- .row -->
                <div class="row justify-content-between" v-if="details[(index-1) * 7].result == '1'">
                  <!-- panel header -->
                  <div class="col-md-2 pb-2">
                    <col-note
                      v-bind:item-name="'No.' + index + ' 現在適用中'"
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
                      v-bind:item-name="'No.' + index"
                      v-bind:item-control="'LIGHT'"
                      v-bind:item-note="''"
                    ></col-note>
                  </div>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
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
                        v-model="details[(index-1) * 7].name"
                        name="name"
                      />
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
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
                        v-bind:default-date="details[(index-1) * 7].apply_term_from"
                        v-on:change-event="applydateDetaileChanges($event, index)"
                        v-on:clear-event="applydateDetaileCleared($event, index)"
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
                <!-- 所定労働時間 -->
                <!-- .row -->
                <div
                  v-for="(item, index) in get_RegularTime" v-bind:key="index"
                  class="row justify-content-between"
                >
                    <input-time
                      v-bind:item-title="'所定労働開始時間'"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※開始時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 0])"
                    ></input-time>
                  <!-- .col -->
                  <div class="col-md-6 pb-2" v-if="index === 0">
                    <input-time
                      v-bind:item-title="'所定労働開始時間 '+(index+1)"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※開始時刻を半角で入力します'"
                      v-bind:value-data="regularTime[index]['fromTime']"
                      v-on:change-event="timeChanges($event, '1', index)"
                    ></input-time>
                  </div>
                  <div class="col-md-6 pb-2" v-else>
                    <input-time
                      v-bind:item-title="'所定労働開始時間 '+(index+1)"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※開始時刻を半角で入力します'"
                      v-bind:value-data="regularTime[index]['fromTime']"
                      v-on:change-event="timeChanges($event, '1', index)"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2" v-if="index === 0">
                    <input-time
                      v-bind:item-title="'所定労働終了時間 '+(index+1)"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="regularTime[index]['toTime']"
                      v-on:change-event="timeChanges($event, '2', index)"
                    ></input-time>
                  </div>
                  <div class="col-md-6 pb-2" v-else>
                    <input-time
                      v-bind:item-title="'所定労働終了時間 '+(index+1)"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="regularTime[index]['toTime']"
                      v-on:change-event="timeChanges($event, '2', index)"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- /.所定労働時間 -->

                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- 所定労働開始時間 -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'所定労働開始時間'"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※開始時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 0])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'所定労働終了時間'"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 0])"
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
                      v-bind:value-data="details[(index-1) * 7 + 1].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 1])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'休憩終了時間 A'"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 1].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 1])"
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
                      v-bind:value-data="details[(index-1) * 7 + 2].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 2])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'休憩終了時間 B'"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 2].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 2])"
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
                      v-bind:value-data="details[(index-1) * 7 + 3].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 3])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'休憩終了時間 C'"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 3].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 3])"
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
                      v-bind:value-data="details[(index-1) * 7 + 4].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 4])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'休憩終了時間 D'"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 4].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 4])"
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
                      v-bind:value-data="details[(index-1) * 7 + 5].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 5])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'休憩終了時間 E'"
                      v-bind:item-required="false"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 5].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 5])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆深夜労働時間設定'"
                    v-bind:header-text2="''"
                    v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- 深夜労働開始時間 -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'深夜労働開始時間'"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※開始時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 6].from_time"
                      v-on:change-event="timeDetailChanges1($event, [index, 6])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time
                      v-bind:item-title="'深夜労働終了時間'"
                      v-bind:item-required="true"
                      v-bind:place-holder="'※終了時刻を半角で入力します'"
                      v-bind:value-data="details[(index-1) * 7 + 6].to_time"
                      v-on:change-event="timeDetailChanges2($event, [index, 6])"
                    ></input-time>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <!-- ----------- ボタン部 START ---------------- -->
                <div class="row justify-content-between">
                  <div class="col-md-12 pb-2">
                    <div class="btn-group float-left">
                      <button v-if="details[(index-1) * 7].result != '0' && details[(index-1) * 7].id != ''"
                        type="button"
                        class="btn btn-success"
                        @click="fixclick(index)"
                      >この内容で更新する</button>
                      <button v-if="details[(index-1) * 7].id == ''"
                        type="button"
                        class="btn btn-success"
                        @click="addClick(index)"
                      >この内容で追加する</button>
                      <button v-if="details[(index-1) * 7].result == '2' && details[(index-1) * 7].id != ''"
                        type="button"
                        class="btn btn-danger"
                        @click="delClick(index)"
                      >この内容を削除する</button>
                      <button v-if="details[(index-1) * 7].id == ''"
                        type="button"
                        class="btn btn-danger"
                        @click="rowDelClick(index)"
                      >行削除</button>
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
                      v-bind:item-name="'No.' + index"
                      v-bind:item-control="'LIGHT'"
                      v-bind:item-note="''"
                    ></col-note>
                  </div>
                  <!-- /.panel header -->
                </div>
                <!-- /row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆タイムテーブル名'"
                    v-bind:header-text2="''"
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
                        >タイムテーブル名</span>
                      </div>
                      <input
                        type="text"
                        class="form-control"
                        v-model="details[(index-1) * 7].name"
                        name="name"
                        disabled
                      />
                    </div>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆適用開始設定'"
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
                          for="target_fromdate"
                        >適用開始日付</span>
                      </div>
                      <input-datepicker-disabled
                        v-bind:default-date="details[(index-1) * 7].apply_term_from"
                      ></input-datepicker-disabled>
                    </div>
                  </div>
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- 所定労働開始時間 -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'所定労働開始時間'"
                      v-bind:value-data="details[(index-1) * 7].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'所定労働終了時間'"
                      v-bind:value-data="details[(index-1) * 7].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆休憩時間設定'"
                    v-bind:header-text2="''"
                    v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <div class="row justify-content-between">
                  <!-- 休憩開始時間 A -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩開始時間 A'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 1].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩終了時間 A'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 1].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- 休憩開始時間 B -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩開始時間 B'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 2].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩終了時間 B'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 2].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- 休憩開始時間 C -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩開始時間 C'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 3].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩終了時間 C'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 3].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- 休憩開始時間 D -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩開始時間 D'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 4].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩終了時間 D'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 4].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- 休憩開始時間 E -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩開始時間 E'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 5].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'休憩終了時間 E'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 5].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- panel header -->
                  <daily-working-information-panel-header
                    v-bind:header-text1="'◆深夜労働時間設定'"
                    v-bind:header-text2="''"
                    v-bind:class-text="'card-header col-12 bg-transparent pb-2 border-0'"
                  ></daily-working-information-panel-header>
                  <!-- /.panel header -->
                </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row justify-content-between">
                  <!-- 深夜労働開始時間 -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'深夜労働開始時間'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 6].from_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                  <!-- .col -->
                  <div class="col-md-6 pb-2">
                    <input-time-disabled
                      v-bind:item-title="'深夜労働終了時間'"
                      v-bind:place-holder="''"
                      v-bind:value-data="details[(index-1) * 7 + 6].to_time"
                    ></input-time-disabled>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
            <!-- /.panel contents -->
            <!-- ----------- 項目部 END ---------------- -->
          </div>
          <!-- /.main contentns row -->
          <!-- ----------- 編集入力部 END ---------------- -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
    <!-- /main contentns row -->
  </div>
</template>
<script>
import moment from "moment";
import {dialogable} from '../mixins/dialogable.js';
import {checkable} from '../mixins/checkable.js';
import {requestable} from '../mixins/requestable.js';

// CONST
const CONST_ATTENDANCE_COUNT_CODE = '1';

export default {
  name: "CreateTimeTable",
  mixins: [ dialogable, checkable, requestable ],
  props: {
    feature_item_selections: {
        type: Array,
        default: []
    }
  },
  data() {
    return {
      showlist: true,
      selectedValue: "",
      valuekillcheck: false,
      selectMode: "",
      messagevalidatesNew: [],
      messagevalidatesEdt: [],
      selectedName: "",
      closingYmd: "",
      details: [],
      form: {
        name: "",
        apply_term_from: "",
        id: "",
        no: "",
        regular: [],
        regularRest: [],
        midnight: []
      },
      regularTime: [],
      regularRestTime: [],
      midnightTime: [],
      count: 0,
      before_count: 0,
      regularTime_count: 0,
      regularRestTime_count: 5,
      midnightTime_count: 1
    };
  },
  computed: {
    get_RegularTime: function() {
      var array_set = [{}];
      this.regularTime = [];
      let $this = this;
      this.feature_item_selections.forEach( function( item ) {
        if (item.item_code == CONST_ATTENDANCE_COUNT_CODE) {
          $this.regularTime_count = Number(item.value_select);
          for(var i=0;i<$this.regularTime_count;i++) {
              array_set = {
                fromTime: "",
                toTime: ""
              };
              $this.regularTime.push(array_set);
          }
          return $this.regularTime;
        }
      });

      return this.regularTime;
    },
    get_RegularRestTime: function() {
      var array_set = [{}];
      this.regularRestTime = [];
      for(var i=0;i<this.regularRestTime_count;i++) {
          array_set = {
            fromTime: "",
            toTime: ""
          };
          this.regularRestTime.push(array_set);
      }
      return this.regularRestTime;
    },
    get_MidNightTime: function() {
      var array_set = [{}];
      this.midnightTime = [];
      for(var i=0;i<this.midnightTime_count;i++) {
          array_set = {
            fromTime: "",
            toTime: ""
          };
          this.midnightTime.push(array_set);
      }
      return this.midnightTime;
    }
  },
  methods: {
    // ------------------------ バリデーション ------------------------------------
    // バリデーション（新規作成）
    checkFormStore: function() {
      this.messagevalidatesNew = [];
      var chkArray = [];
      var flag = true;
      // タイムテーブル名
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = 'タイムテーブル名';
      chkArray = 
        this.checkHeader(this.form.name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesNew.length == 0) {
          this.messagevalidatesNew = chkArray;
        } else {
          this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
        }
      }

      // 所定労働時間
      for (let index = 0; index < this.regularTime.length; index++) {
        if (index == 0) {
          required = true;
        } else {
          required = false;
        }
        equalength = 0;
        maxlength = 0;
        itemname = '所定労働開始時間';
        chkArray = 
          this.checkDetailtext(this.regularTime[index]['fromTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
        }
        if (index == 0) {
          required = true;
        } else {
          required = false;
        }
        equalength = 0;
        maxlength = 0;
        itemname = '所定労働終了時間';
        chkArray = 
          this.checkDetailtext(this.regularTime[index]['toTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
        }
      }

      // 休憩時間
      for (let index = 0; index < this.regularRestTime.length; index++) {
        required = false;
        equalength = 0;
        maxlength = 0;
        itemname = '休憩開始時間';
        chkArray = 
          this.checkDetailtext(this.regularRestTime[index]['fromTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
        }
        required = false;
        equalength = 0;
        maxlength = 0;
        itemname = '休憩終了時間';
        chkArray = 
          this.checkDetailtext(this.regularRestTime[index]['toTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
        }
      }
      // 深夜労働開始時間
      for (let index = 0; index < this.midnightTime.length; index++) {
        if (index == 0) {
          required = true;
        } else {
          required = false;
        }
        equalength = 0;
        maxlength = 0;
        itemname = '深夜労働開始時間';
        chkArray = 
          this.checkDetailtext(this.midnightTime[index]['fromTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
        }
        if (index == 0) {
          required = true;
        } else {
          required = false;
        }
        equalength = 0;
        maxlength = 0;
        itemname = '深夜労働終了時間';
        chkArray = 
          this.checkDetailtext(this.midnightTime[index]['toTime'], required, equalength, maxlength, index+1, itemname);
        if (chkArray.length > 0) {
          if (this.messagevalidatesNew.length == 0) {
            this.messagevalidatesNew = chkArray;
          } else {
            this.messagevalidatesNew = this.messagevalidatesNew.concat(chkArray);
          }
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
      // タイムテーブル名
      var required = true;
      var equalength = 0;
      var maxlength = 191;
      var itemname = 'タイムテーブル名';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7].name, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 適用開始日付
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '適用開始日付';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7].apply_term_from, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      } else {
        // 適用中と比較
        var chkdt = moment(this.details[(index-1) * 7].apply_term_from).format('YYYYMMDD');
        var chksourcedt = "";
        if (this.details[(index-1) * 7].result == '2') {
          for (var i=index;i<=this.before_count;i++) {
            chksourcedt = moment(this.details[(i) * 7].apply_term_from).format('YYYYMMDD');
            if (chkdt <= chksourcedt) {
              this.messagevalidatesEdt.push("現在適用中の適用開始日付以前の日付は登録できません");
            }
          }
        }
      }
      // 所定労働開始時間
      var required = true;
      var equalength = 0;
      var maxlength = 0;
      var itemname = '所定労働開始時間';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7].from_time, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 所定労働終了時間
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '所定労働終了時間';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7].to_time, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 休憩終了時間A
      if (this.details[(index-1) * 7 + 1].from_time != "" && this.details[(index-1) * 7 + 1].from_time != null) {
        if (this.details[(index-1) * 7 + 1].to_time == "" || this.details[(index-1) * 7 + 1].to_time == null) {
          this.messagevalidatesEdt.push("休憩終了時間 Aを入力してください");
        }
      }
      if (this.details[(index-1) * 7 + 1].to_time != "" && this.details[(index-1) * 7 + 1].to_time != null) {
        if (this.details[(index-1) * 7 + 1].from_time == "" || this.details[(index-1) * 7 + 1].from_time == null) {
          this.messagevalidatesEdt.push("休憩開始時間 Aを入力してください");
        }
      }
      // 休憩終了時間B
      if (this.details[(index-1) * 7 + 2].from_time != "" && this.details[(index-1) * 7 + 2].from_time != null) {
        if (this.details[(index-1) * 7 + 2].to_time == "" || this.details[(index-1) * 7 + 2].to_time == null) {
          this.messagevalidatesEdt.push("休憩終了時間 Bを入力してください");
        }
      }
      if (this.details[(index-1) * 7 + 2].to_time != "" && this.details[(index-1) * 7 + 2].to_time != null) {
        if (this.details[(index-1) * 7 + 2].from_time == "" || this.details[(index-1) * 7 + 2].from_time == null) {
          this.messagevalidatesEdt.push("休憩開始時間 Bを入力してください");
        }
      }
      // 休憩終了時間C
      if (this.details[(index-1) * 7 + 3].from_time != "" && this.details[(index-1) * 7 + 3].from_time != null) {
        if (this.details[(index-1) * 7 + 3].to_time == "" || this.details[(index-1) * 7 + 3].to_time == null) {
          this.messagevalidatesEdt.push("休憩終了時間 Cを入力してください");
        }
      }
      if (this.details[(index-1) * 7 + 3].to_time != "" && this.details[(index-1) * 7 + 3].to_time != null) {
        if (this.details[(index-1) * 7 + 3].from_time == "" || this.details[(index-1) * 7 + 3].from_time == null) {
          this.messagevalidatesEdt.push("休憩開始時間 Cを入力してください");
        }
      }
      // 休憩終了時間D
      if (this.details[(index-1) * 7 + 4].from_time != "" && this.details[(index-1) * 7 + 4].from_time != null) {
        if (this.details[(index-1) * 7 + 4].to_time == "" || this.details[(index-1) * 7 + 4].to_time == null) {
          this.messagevalidatesEdt.push("休憩終了時間 Dを入力してください");
        }
      }
      if (this.details[(index-1) * 7 + 4].to_time != "" && this.details[(index-1) * 7 + 4].to_time != null) {
        if (this.details[(index-1) * 7 + 4].from_time == "" || this.details[(index-1) * 7 + 4].from_time == null) {
          this.messagevalidatesEdt.push("休憩開始時間 Dを入力してください");
        }
      }
      // 休憩終了時間E
      if (this.details[(index-1) * 7 + 5].from_time != "" && this.details[(index-1) * 7 + 5].from_time != null) {
        if (this.details[(index-1) * 7 + 5].to_time == "" || this.details[(index-1) * 7 + 5].to_time == null) {
          this.messagevalidatesEdt.push("休憩終了時間 Eを入力してください");
        }
      }
      if (this.details[(index-1) * 7 + 5].to_time != "" && this.details[(index-1) * 7 + 5].to_time != null) {
        if (this.details[(index-1) * 7 + 5].from_time == "" || this.details[(index-1) * 7 + 5].from_time == null) {
          this.messagevalidatesEdt.push("休憩開始時間 Eを入力してください");
        }
      }
      // 深夜労働開始時間
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '深夜労働開始時間';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7 + 6].from_time, required, equalength, maxlength, itemname);
      if (chkArray.length > 0) {
        if (this.messagevalidatesEdt.length == 0) {
          this.messagevalidatesEdt = chkArray;
        } else {
          this.messagevalidatesEdt = this.messagevalidatesEdt.concat(chkArray);
        }
      }
      // 深夜労働終了時間
      required = true;
      equalength = 0;
      maxlength = 0;
      itemname = '深夜労働終了時間';
      chkArray = 
        this.checkHeader(this.details[(index-1) * 7 + 6].to_time, required, equalength, maxlength, itemname);
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
    
    // タイムテーブル選択が変更された場合の処理
    itemChanges: function(value, arrayitem) {
      // 入力項目の部署クリア
      this.inputClear();
      this.messagevalidatesNew = [];
      this.messagevalidatesEdt = [];
      this.selectedValue = value;
      if (value == "" || value == null) {
        this.selectMode = 'NEW';
      } else {
        this.selectMode = 'EDT';
        this.selectedName = arrayitem['name'];
        this.getItem();
      }
    },
    // 廃止チェックボックスが変更された場合の処理
    /*checkboxChange: function() {
      this.refreshItemList();

    }, */
    // 時刻が変更された場合の処理
    timeChanges: function(value, type, index) {
      if (type == '1') {
        this.regularTime[index]['fromTime'] = value;
      } else if (type == '2') {
        this.regularTime[index]['toTime'] = value;
      } else if (type == '3') {
        this.regularRestTime[index]['fromTime'] = value;
      } else if (type == '4') {
        this.regularRestTime[index]['toTime'] = value;
      } else if (type == '5') {
        this.midnightTime[index]['fromTime'] = value;
      } else if (type == '6') {
        this.midnightTime[index]['toTime'] = value;
      }
    },
    // 有効期間が変更された場合の処理
    applydateChanges: function(value) {
      this.form.apply_term_from = value;
    },
    // 有効期間が変更された場合の処理（明細）
    applydateDetaileChanges: function(value, typeRow) {
      this.details[(typeRow-1) * 7].apply_term_from = value;
    },
    // 有効期間がクリアされた場合の処理（明細）
    applydateDetaileCleared: function(value, typeRow) {
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
    // 新規作成ボタンクリック処理
    storeclick() {
      var flag = this.checkFormStore();
      if (flag) {
        var messages = [];
        messages.push("この内容で登録しますか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              this.store();
            }
        });
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesNew, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 更新ボタンクリック処理
    fixclick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        this.getclosingday(index, "更新");
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 追加ボタンクリック処理
    addClick(index) {
      var flag = this.checkFormFix(index);
      if (flag) {
        this.getclosingday(index, "追加");
      // 項目数が多い場合以下コメントアウト
      } else {
        this.countswal("エラー", this.messagevalidatesEdt, "error", true, false, true)
          .then(result  => {
            if (result) {
            }
        });
      }
    },
    // 削除ボタンクリック処理
    delClick(index) {
      var messages = [];
      messages.push("この内容を削除しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.DelDetail(index);
          }
      });
    },
    // プラス追加ボタンクリック処理
    appendRowClick: function() {
      if (this.before_count < this.count) {
        var messages = [];
        messages.push("１度に追加できる情報は１個です。追加してから再実行してください");
        this.htmlMessageSwal("エラー", messages, "error", true, false);
      } else {
        var no = this.details[0].no;
        var name = this.details[0].name;
        var j=6;
        for( var i=0;i<7;i++ ) {
          this.details.unshift({
            id: "",
            no: no,
            name: name,
            working_time_kubun: i+1,
            apply_term_from: "",
            from_time: this.details[j].from_time,
            to_time: this.details[j].to_time,
            result: 2,
            created_user: "",
            updated_user: ""
          });
        }
        this.count = this.details.length / 7; // １データにつき７レコードある
      }
    },
    // 行削除ボタンクリック処理
    rowDelClick: function(index) {
      if (this.checkRowData(index)) {
        var messages = [];
        messages.push("行削除してよろしいですか？");
        this.htmlMessageSwal("確認", messages, "info", true, true)
          .then(result  => {
            if (result) {
              for( var i=((index-1) * 7);i<7;i++ ) {
                this.details.splice(0, 1);
              }
              this.count = this.details.length / 7;
            }
        });
      } else {
        for( var i=((index-1) * 7);i<7;i++ ) {
          this.details.splice(0, 1);
          //console.log(" I = " + i + " " + JSON.stringify(this.details,null,'\t'));
        }
        this.count = this.details.length / 7;
      }
    },
    
    // -------------------- サーバー処理 ----------------------------
    // タイムテーブル取得処理
    getItem() {
      this.details = [];
      this.postRequest("/create_time_table/get", { no : this.selectedValue, killvalue : this.valuekillcheck})
        .then(response  => {
          this.getThen(response);
        })
        .catch(reason => {
          this.serverCatch("取得");
        });
    },
    // タイムテーブル登録処理
    store() {
      var messages = [];
      this.form.regularTime = this.regularTime;
      this.form.regularRestTime = this.regularRestTime;
      this.form.midnightTime = this.midnightTime;
      var arrayParams = { no : this.form.no, name : this.form.name, details : this.form };
      this.postRequest("/create_time_table/store", arrayParams)
        .then(response  => {
          this.putThenHead(response, "登録");
        })
        .catch(reason => {
          messages.push("タイムテーブル登録に失敗しました");
          this.htmlMessageSwal("エラー", messages, "error", true, false);
        });
    },
    // タイムテーブル更新処理（明細）
    FixDetail(index, eventtext) {
      var messages = [];
      var arrayParams = { index : index, details : this.details };
      this.postRequest("/create_time_table/fix", arrayParams)
        .then(response  => {
          this.putThenDetail(response, eventtext);
        })
        .catch(reason => {
          messages.push("タイムテーブル" + eventtext + "に失敗しました");
          this.htmlMessageSwal("エラー", messages, "error", true, false);
        });
    },
    // タイムテーブル削除処理（明細）
    DelDetail(index) {
      var messages = [];
      var arrayParams = { index : index, details : this.details };
      this.postRequest("/create_time_table/del", arrayParams)
        .then(response  => {
          this.putThenDetail(response, "削除");
        })
        .catch(reason => {
          messages.push("タイムテーブル削除に失敗しました");
          this.htmlMessageSwal("エラー", messages, "error", true, false);
        });
    },
    // 前月締日取得処理
    getclosingday: function(index, eventtext) {
      var messages = [];
      // 前月
      this.closingYm = moment(new Date()).subtract(1, 'M').format('YYYYMM');
      var arrayParams = { target_date : this.closingYm };
      this.postRequest("/get_closing_day", arrayParams)
        .then(response  => {
          var res = response.data;
          this.closingYmd =String(this.closingYm) + String(res.closing);
          this.dt = moment(this.details[(index-1) * 7].apply_term_from).format('YYYYMMDD');
          if (this.closingYmd >= this.dt) {
            this.fix_warning_confirm(index, eventtext);
          } else {
            this.fix_normal_confirm(index, eventtext);
          }
        })
        .catch(reason => {
          messages.push("締日取得エラー。集計方法基本設定の締日設定を確認してください。");
          this.htmlMessageSwal("エラー", messages, "error", true, false);
        });
    },
    // -------------------- 共通 ----------------------------
    // 締日チェックOKの場合
    fix_normal_confirm: function(index, eventtext) {
      var messages = [];
      messages.push("この内容で" + eventtext + "しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.FixDetail(index, eventtext);
          }
      });
    },
    // 締日チェックNGの場合
    fix_warning_confirm: function(index, eventtext) {
      var messages = [];
      messages.push("適用開始日が前月の締日" + moment(this.closingYmd).format('YYYY年MM月DD日') + "以前ですが");
      messages.push("前月締日以前のデータは自動集計されないため、");
      messages.push("月次集計の一括集計を行う必要があります。");
      messages.push("この内容で編集を確定しますか？");
      this.htmlMessageSwal("確認", messages, "info", true, true)
        .then(result  => {
          if (result) {
            this.FixDetail(index, eventtext);
          }
      });
    },
    // 取得正常処理
    getThen(response) {
      this.details = [];
      var res = response.data;
      if (res.result) {
        this.details = res.details;
        this.count = this.details.length / 7;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("エラー", res.messagedata, "error", true, false);
        } else {
          serverCatch("取得")
        }
      }
    },
    // 更新系正常処理
    putThenHead(response, eventtext) {
      var res = response.data;
      if (res.result) {
        this.$toasted.show("タイムテーブルを" + eventtext + "しました");
        this.refreshItemList();
        this.form.no = res.no;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          serverCatch(eventtext)
        }
      }
    },
    // 更新系正常処理（明細）
    putThenDetail(response, eventtext) {
      var res = response.data;
      if (res.result) {
        this.$toasted.show("タイムテーブルを" + eventtext + "しました");
        this.refreshItemList();
        this.getItem();
        this.count = this.details.length / 7;
        this.before_count = this.count;
      } else {
        if (res.messagedata.length > 0) {
          this.htmlMessageSwal("警告", res.messagedata, "warning", true, false);
        } else {
          serverCatch(eventtext)
        }
      }
    },
    // 異常処理
    serverCatch(eventtext) {
      var messages = [];
      messages.push("タイムテーブル情報" + eventtext + "に失敗しました");
      this.htmlMessageSwal("エラー", messages, "error", true, false);
    },
    inputClear() {
      this.details = [];
      this.selectedValue = "";
      this.selectedName = "";
      this.selectMode = "";
      this.count = 0;
      this.before_count = 0;
      var array_set = [{}];
      this.regularTime = [];
      for(var i=0;i<this.regularTime_count;i++) {
          array_set = {
            fromTime: "",
            toTime: ""
          };
          this.regularTime.push(array_set);
      }
      array_set = [{}];
      this.regularRestTime = [];
      for(var i=0;i<this.regularRestTime_count;i++) {
          array_set = {
            fromTime: "",
            toTime: ""
          };
          this.regularRestTime.push(array_set);
      }
      array_set = [{}];
      this.midnightTime = [];
      for(var i=0;i<this.midnightTime_count;i++) {
          array_set = {
            fromTime: "",
            toTime: ""
          };
          this.midnightTime.push(array_set);
      }
      this.form.name = "";
      this.form.apply_term_from = "";
      this.form.id = "";
      this.form.no = "";
      this.form.regular = this.regularTime;
      this.form.regularRest = this.regularRestTime;
      this.form.midnight = this.midnightTime;
    },
    checkRowData(index) {
      if (this.details[(index-1) * 7].name != "" && this.details[(index-1) * 7].name != null) { return true; }
      if (this.details[(index-1) * 7].apply_term_from != "" && this.details[(index-1) * 7].apply_term_from != null) { return true; }
      if (this.details[(index-1) * 7].from_time != "" && this.details[(index-1) * 7].from_time != null) { return true; }
      if (this.details[(index-1) * 7].to_time != "" && this.details[(index-1) * 7].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 1].from_time != "" && this.details[(index-1) * 7 + 1].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 1].to_time != "" && this.details[(index-1) * 7 + 1].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 2].from_time != "" && this.details[(index-1) * 7 + 2].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 2].to_time != "" && this.details[(index-1) * 7 + 3].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 3].from_time != "" && this.details[(index-1) * 7 + 3].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 3].to_time != "" && this.details[(index-1) * 7 + 3].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 4].from_time != "" && this.details[(index-1) * 7 + 4].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 4].to_time != "" && this.details[(index-1) * 7 + 4].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 5].from_time != "" && this.details[(index-1) * 7 + 5].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 5].to_time != "" && this.details[(index-1) * 7 + 5].to_time != null) { return true; }
      if (this.details[(index-1) * 7 + 6].from_time != "" && this.details[(index-1) * 7 + 6].from_time != null) { return true; }
      if (this.details[(index-1) * 7 + 6].to_time != "" && this.details[(index-1) * 7 + 6].to_time != null) { return true; }
      return false;
    },
    refreshItemList() {
      // 最新リストの表示
      this.showlist = false;
      this.$nextTick(() => (this.showlist = true));
    }
  }
};
</script>
