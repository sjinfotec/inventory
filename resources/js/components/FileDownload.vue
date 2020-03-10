<template>
  <div>
    <!-- main contentns row -->
    <div class="row justify-content-between">
      <!-- .panel -->
      <div class="col-md pt-3">
        <div class="card shadow-pl">
          <!-- panel header -->
          <daily-working-information-panel-header
            v-bind:header-text1="'ダウンロード'"
            v-bind:header-text2="'該当ダウンロードのリンクをクリックします'"
          ></daily-working-information-panel-header>
          <!-- /.panel header -->
          <div class="card-body pt-2">
            <!-- panel contents -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆ＯｎｅＤａｗｎインストールファイルダウンロード'"
                v-bind:header-text2="'とくに指定ない限り最新版をダウンロードしてください'"
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
                  <a href="/file_download?filekbn=1">・カード打刻インストール（64bitトライアル版 最新版）</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=2">・イベントログ取得インストール（64bitトライアル版 最新版） -- ファイル名：pceventlog.zip</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆遠隔リモートインストールファイルダウンロード'"
                v-bind:header-text2="'とくに指定ない限り最新版をダウンロードしてください'"
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
                  <a href="/file_download?filekbn=3">・遠隔リモートインストール（最新版） -- ファイル名：TeamViewer_Setup.exe</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- panel header -->
              <daily-working-information-panel-header
                v-bind:header-text1="'◆説明書ダウンロード'"
                v-bind:header-text2="'とくに指定ない限り最新版をダウンロードしてください'"
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
                  <a href="/file_download?filekbn=21">・操作マニュアル（導入編）（PDF 最新版） -- ファイル名：Installmanual.pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=22">・操作マニュアル（導入編）（EXCEL 最新版） -- ファイル名：Installmanual.xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=23">・操作マニュアル（打刻編）（PDF 最新版） -- ファイル名：Operationmanual(card_reader).pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=24">・操作マニュアル（打刻編）（EXCEL 最新版） -- ファイル名：Operationmanual(card_reader).xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=25">・操作マニュアル（設定編）（PDF 最新版） -- ファイル名：Operationmanual(setting).pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=26">・操作マニュアル（設定編）（EXCEL 最新版） -- ファイル名：Operationmanual(setting).xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=27">・操作マニュアル（弥生給与ソフト連携）（PDF 最新版） -- ファイル名：Operationmanual(yayoi).pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=28">・操作マニュアル（弥生給与ソフト連携）（EXCEL 最新版） -- ファイル名：Operationmanual(yayoi).xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=29">・操作マニュアル（イベントログ取得インストール）（PDF 最新版） -- ファイル名：pceventlogmanual.pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=30">・操作マニュアル（イベントログ取得インストール）（EXCEL 最新版） -- ファイル名：pceventlogmanual.xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=31">・操作マニュアル（遠隔リモートインストール）（PDF 最新版） -- ファイル名：Operationmanual(TeamViewer).pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=32">・操作マニュアル（遠隔リモートインストール）（EXCEL 最新版） -- ファイル名：Operationmanual(TeamViewer).xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
    </div>
    <!-- /.panel -->
  </div>
  <!-- main contentns row -->
</template>
<script>

export default {
  name: "FileDownload",
  methods: {
    // -------------------- サーバー処理 ----------------------------
    // -------------------- 共通 ----------------------------
  }
};
</script>

