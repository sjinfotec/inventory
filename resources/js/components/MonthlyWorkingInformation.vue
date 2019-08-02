<template>
<div>
  <!-- main contentns row -->
  <div class="row justify-content-between print-none">
    <!-- .panel -->
    <div class="col-md pt-3">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pb-0 border-0">
          <h1 class="float-sm-left font-size-rg">年月を指定して集計を表示する</h1>
          <span class="float-sm-right font-size-sm">雇用形態や所属部署でフィルタリングして表示できます</span>
        </div>
        <!-- /.panel header -->
        <div class="card-body pt-2">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row justify-content-between">
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">指定年</span>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text font-size-sm line-height-xs label-width-90" id="basic-addon1">指定月</span>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">表示区分</label>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">雇用形態</label>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">所属部署</label>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <!-- /.col -->
            <!-- .col -->
            <div class="col-md-6 pb-2">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="input-group-text font-size-sm line-height-xs label-width-90" for="inputGroupSelect01">氏　　名</label>
                </div>
                <select class="custom-select">
                  <option selected></option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
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
                <button type="button" class="btn btn-primary btn-lg font-size-rg w-100">この条件で表示する</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
  <!-- main contentns row -->
  <div class="row justify-content-between">
    <!-- .panel -->
    <div class="col-md pt-3 align-self-stretch">
      <div class="card shadow-pl">
        <!-- panel header -->
        <div class="card-header bg-transparent pt-3 border-0 print-none">
          <h1 class="float-md-left font-size-rg">月次集計 2019年07月 〆日から集計</h1>
          <span class="float-md-right font-size-sm">虫眼鏡アイコンをクリックするとタイムカードが表示されます</span>
        </div>
        <!-- /.panel header -->
        <!-- panel body -->
        <div class="card-body mb-3 py-0 pt-4 border-top print-none">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <div class="btn-group d-flex">
                <button type="button" class="btn btn-success btn-lg font-size-rg w-100"><img class="icon-size-sm mr-2 pb-1" src="/images/round-get-app-w.svg" alt="">集計結果をCSVファイルに出力する</button>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
        <!-- panel body -->
        <div class="card-body mb-3 py-0 pt-4 border-top print-only print-space">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row">
            <!-- col -->
            <div class="col-md-12 pb-2">
              <h1 class="float-md-left font-size-rg">三条印刷株式会社</h1>
              <span class="float-md-right font-size-sm">2019年07月</span>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
        <!-- panel body -->
        <div class="card-body mb-3 py-0 pt-4 border-top print-space">
          <!-- panel contents -->
          <!-- .row -->
          <div class="row">
            <!-- col -->
            <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
              <a class="float-left mr-2 px-2 py-2 font-size-rg btn btn-primary btn-lg print-none" data-toggle="collapse" href="#collapseUser1" role="button" aria-expanded="true" aria-controls="collapseUser1"><img class="icon-size-rg" src="/images/round-search-w.svg" alt=""></a>
              <h1 class="font-size-sm m-0 mb-1">氏名</h1>
              <p class="font-size-rg font-weight-bold m-0">三条 丈太郎</p>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-6 col-lg-6 pb-2 align-self-stretch">
              <h1 class="font-size-sm m-0 mb-1 text-sm-right">所属部署</h1>
              <p class="font-size-rg m-0 text-sm-right">情報処理課</p>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- .row -->
          <div class="row mt-2 print-none">
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <h1 class="font-size-sm m-0 mb-1">雇用形態</h1>
                  <p class="font-size-rg m-0">正社員</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <h1 class="font-size-sm m-0 mb-1">勤務時間</h1>
                  <p class="font-size-lg m-0">08:13</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-access-time-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">所定労働時間</h1>
                  <p class="font-size-lg m-0">08:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-access-time-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">所定外労働時間</h1>
                  <p class="font-size-lg m-0">08:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">残業時間</h1>
                  <p class="font-size-lg m-0">00:13</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">深夜残業時間</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">法定労働時間</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">法定外労働時間</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">未就労時間</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">時間外労働</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">遅刻日数</h1>
                  <p class="font-size-lg m-0">00日</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">早退日数</h1>
                  <p class="font-size-lg m-0">00日</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">休暇日数</h1>
                  <p class="font-size-lg m-0">00日</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">有給休暇日数</h1>
                  <p class="font-size-lg m-0">00日</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">時間外労働3ヵ月</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">時間外労働6ヵ月</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">時間外労働1年</h1>
                  <p class="font-size-lg m-0">00:00</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <!-- col -->
            <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
              <div class="card text-secondary border border-secondary">
                <div class="card-body px-3 py-2">
                  <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
                  <h1 class="font-size-sm m-0 mb-1">6ヵ月連続45時間</h1>
                  <p class="font-size-lg m-0">超過</p>
                </div>
              </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <!-- /.panel contents -->
        </div>
        <!-- /panel body -->
        <!-- collapse -->
        <div class="collapse page-break-after" id="collapseUser1">
          <!-- panel body -->
          <div class="card-body mb-3 p-0 border-top">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <div class="col-12 p-0">
                    <table class="table table-striped border-bottom font-size-sm text-nowrap">
                      <thead>
                        <tr>
                          <td class="text-center align-middle w-20">日付</td>
                          <td class="text-center align-middle w-20">出勤時間</td>
                          <td class="text-center align-middle w-20">退勤時間</td>
                          <td class="text-center align-middle mw-rem-20">備考</td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="text-center align-middle">2019年07月01日（日）</td>
                          <td class="text-center align-middle">08:30</td>
                          <td class="text-center align-middle">18:00</td>
                          <td class="text-center align-middle"></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
            <!-- /.panel contents -->
          </div>
          <!-- /panel body -->
        </div>
        <!-- /collapse -->
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- /main contentns row -->
</div>
</template>
<script>
export default {
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
