<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <div class="card-header clearfix bg-transparent pb-0 border-0">
            <h1 class="float-sm-left font-size-rg">タイムテーブルを設定する</h1>
            <span class="float-sm-right font-size-sm">複数の勤務時間を設定することでシフト登録ができます</span>
          </div>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <fvl-form
              method="post"
              :data="form"
              url="/create_time_table/store"
              @success="addSuccess()"
              @error="error()"
            >
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- .col -->
                <div class="col-12 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >タイムテーブル</span>
                    </div>
                    <fvl-search-select
                      :selected.sync="selectId"
                      class="p-0"
                      name="selectId"
                      :options="timeTableList"
                      placeholder="タイムテーブルを選択すると編集モードになります"
                      :allowEmpty="true"
                      :search-keys="['no']"
                      option-key="no"
                      option-value="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >タイムテーブルNO</span>
                    </div>
                    <fvl-input
                      type="text"
                      class="form-control p-0"
                      :value.sync="form.no"
                      name="no"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId != ''">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >タイムテーブルNO</span>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      :value.sync="selectId"
                      name="no"
                      readonly="true"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >タイムテーブル名</span>
                    </div>
                    <fvl-input
                      type="text"
                      class="form-control p-0"
                      :value.sync="form.name"
                      name="name"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >所定労働開始時間</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularFrom"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >所定労働終了時間</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularTo"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="selectId=='' || selectId==null ">
                <!-- panel header -->
                <div class="card-header col-12 bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">休憩時間設定</h1>
                  <span class="float-sm-right font-size-sm">5パターンまで設定できます</span>
                </div>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="selectId=='' || selectId==null ">
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩開始時間 A</span>
                    </div>
                    <fvl-input
                      class="form-control p-0"
                      type="time"
                      :value.sync="form.regularRestFrom1"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩終了時間 A</span>
                    </div>
                    <fvl-input
                      class="form-control p-0"
                      type="time"
                      :value.sync="form.regularRestTo1"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩開始時間 B</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestFrom2"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩終了時間 B</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestTo2"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩開始時間 C</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestFrom3"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩終了時間 C</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestTo3"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩開始時間 D</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestFrom4"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩終了時間 D</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestTo4"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩開始時間 E</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestFrom5"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >休憩終了時間 E</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.regularRestTo5"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="selectId=='' || selectId==null ">
                <!-- panel header -->
                <div class="card-header bg-transparent pb-2 border-0">
                  <h1 class="float-sm-left font-size-rg">深夜残業時間設定</h1>
                </div>
                <!-- /.panel header -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between" v-if="selectId=='' || selectId==null ">
                <!-- .col -->
                <div class="col-md-6 pb-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >深夜残業開始時間</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.irregularMidNightFrom"
                      name="syoteifrom"
                    />
                  </div>
                </div>
                <!-- /.col -->
                <!-- .col -->
                <div class="col-md-6 pb-2" v-if="selectId=='' || selectId==null ">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text font-size-sm line-height-xs label-width-150"
                        id="basic-addon1"
                      >深夜残業終了時間</span>
                    </div>
                    <fvl-input
                      type="time"
                      class="form-control p-0"
                      :value.sync="form.irregularMidNightTo"
                      name="syoteito"
                    />
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <!-- .row -->
              <div class="row justify-content-between">
                <!-- col -->
                <div class="col-md-12 pb-2">
                  <div class="btn-group d-flex">
                    <button
                      type="submit"
                      class="btn btn-success"
                      v-if="selectId=='' || selectId==null "
                    >追加する</button>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </fvl-form>
            <!-- /.panel contents -->
            <!-- main contentns row -->
            <div class="row justify-content-between" v-if="details.length ">
              <!-- .panel -->
              <div class="col-md pt-3 align-self-stretch">
                <div class="card shadow-pl">
                  <!-- panel header -->
                  <div class="card-header bg-transparent pt-3 border-0">
                    <h1 class="float-sm-left font-size-rg">
                      <span>
                        <button class="btn btn-success btn-lg font-size-rg" v-on:click="show">+</button>
                      </span>
                      タイムテーブル一覧
                    </h1>
                    <span class="float-sm-right font-size-sm">登録済みのタイムテーブルを編集できます</span>
                  </div>
                  <!-- /.panel header -->
                  <!-- panel body -->
                  <div class="card-body mb-3 p-0 border-top">
                    <!-- panel contents -->
                    <div v-for="n in count" v-bind:key="n">
                      <!-- .row -->
                      <div class="row justify-content-between">
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >タイムテーブル名</span>
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
                        <!-- .col -->
                        <div class="col-md-6 pb-2">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >有効期間</span>
                            </div>
                            <input
                              type="date"
                              class="form-control"
                              v-model="details[(n-1) * 7].apply_term_from"
                              name="term"
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
                              >所定労働開始時間</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >所定労働終了時間</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩開始時間 A</span>
                            </div>
                            <input
                              class="form-control"
                              type="time"
                              v-model="details[(n-1) * 7 + 1].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩終了時間 A</span>
                            </div>
                            <input
                              class="form-control"
                              type="time"
                              v-model="details[(n-1) * 7 + 1].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩開始時間 B</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 2].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩終了時間 B</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 2].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩開始時間 C</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 3].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩終了時間 C</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 3].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩開始時間 D</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 4].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩終了時間 D</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 4].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩開始時間 E</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 5].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >休憩終了時間 E</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 5].to_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >深夜残業開始時間</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 6].from_time"
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
                                class="input-group-text font-size-sm line-height-xs label-width-150"
                                id="basic-addon1"
                              >深夜残業終了時間</span>
                            </div>
                            <input
                              type="time"
                              class="form-control"
                              v-model="details[(n-1) * 7 + 6].to_time"
                              name="syoteito"
                            />
                          </div>
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->
                      <div class="col-md-12 pb-2 text-align-right">
                        <div class="btn-group">
                          <button
                            type="button"
                            class="btn btn-danger"
                            @click="alertDelConf('info',details[(n-1) * 7 + 1].apply_term_from)"
                          >削除する</button>
                        </div>
                      </div>
                    </div>
                    <!-- .row -->
                    <div class="row justify-content-between">
                      <!-- col -->
                      <div class="col-md-12 pb-2">
                        <div class="btn-group d-flex">
                          <button type="button" class="btn btn-success" @click="fix()">修正する</button>
                        </div>
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
            <!-- /main contentns row -->
          </div>
        </div>
      </div>
      <!-- /.panel -->
    </div>
    <!-- /main contentns row -->
    <!-- modal -->
    <modal name="add-time-table" :width="800" :height="800" :pivotY="0.4" v-model="selectId">
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
                    class="input-group-text font-size-sm line-height-xs label-width-150"
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
import {
  FvlForm,
  FvlInput,
  FvlSelect,
  FvlSearchSelect,
  FvlSubmit
} from "formvuelar";

export default {
  name: "CreateTimeTable",
  components: {
    FvlForm,
    FvlInput,
    FvlSelect,
    FvlSearchSelect,
    FvlSubmit,
    FvlSelect,
    getDo: 1
  },
  data() {
    return {
      form: {
        id: "",
        no: "",
        name: "",
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
        irregularFrom1: "",
        irregularTo1: "",
        irregularFrom2: "",
        irregularTo2: "",
        irregularFrom3: "",
        irregularTo3: "",
        irregularMidNightFrom: "",
        irregularMidNightTo: ""
      },
      add: {
        apply_term_from: "",
        no: "",
        name: "",
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
        irregularFrom1: "",
        irregularTo1: "",
        irregularFrom2: "",
        irregularTo2: "",
        irregularFrom3: "",
        irregularTo3: "",
        irregularMidNightFrom: "",
        irregularMidNightTo: ""
      },
      timeTableList: [],
      details: [],
      selectId: "",
      errors: [],
      count: 0,
      oldId: ""
    };
  },
  // マウント時
  mounted() {
    this.getTimeTableList();
  },
  // セレクトボックス変更時
  watch: {
    selectId: function(val, oldVal) {
      if (this.selectId != "") {
        this.getDetail();
      } else {
        this.details = [];
        this.inputClear();
      }
    }
  },
  methods: {
    delTime() {
      this.getDetail();
      console.log("del");
    },
    show: function() {
      this.add.no = this.selectId;
      this.$modal.show("add-time-table");
    },
    hide: function() {
      this.$modal.hide("add-time-table");
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
            this.hide();
            this.inputAddClear();
            this.getTimeTableList();
            this.getDetail();
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
    getDetail() {
      this.$axios
        .get("/create_time_table/get", {
          params: {
            no: this.selectId
          }
        })
        .then(response => {
          this.details = response.data;
          this.count = this.details.length / 7; // １データにつき７レコードある
        })
        .catch(reason => {
          alert("詳細取得でエラーが発生しました");
        });
    },
    getTimeTableList() {
      this.$axios
        .get("/get_time_table_list")
        .then(response => {
          this.timeTableList = response.data;
          this.object = { apply_term_from: "", name: "新規登録", no: "" };
          this.timeTableList.unshift(this.object);

          console.log("タイムテーブルリスト取得");
        })
        .catch(reason => {
          alert("リスト取得エラー");
        });
    },
    addSuccess() {
      this.alert("success", "登録しました", "登録成功");
      this.selectId = this.form.no;
      this.getTimeTableList();
      this.getDetail();
    },
    error() {
      this.alert("error", "登録に失敗しました", "エラー");
    },
    fix: function() {
      this.$axios
        .post("/create_time_table/fix", {
          details: this.details
        })
        .then(response => {
          var res = response.data;
          if (res.result == 0) {
            this.alert("success", "タイムテーブルを修正しました", "修正完了");
            this.getDetail();
            this.getTimeTableList();
          } else {
          }
        })
        .catch(reason => {
          this.alert("error", "タイムテーブルの修正に失敗しました", "エラー");
        });
    },
    // 削除
    del: function(date) {
      this.$axios
        .post("/create_time_table/del", {
          no: this.selectId,
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
            this.getTimeTableList();
            this.selectId = "";
          } else {
          }
        })
        .catch(reason => {
          this.alert("error", "削除でエラーが発生しました", "エラー");
        });
    },
    inputClear() {
      this.form.name = "";
      this.form.id = "";
      this.form.no = "";
      this.selectId = "";
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
    },
    inputAddClear() {
      this.add.name = "";
      this.add.apply_term_from = "";
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
