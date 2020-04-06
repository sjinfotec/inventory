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
            <div class="row justify-content-between"
              v-if="this.distributionmode !== this.distributionmarutaka_value">
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
                v-bind:header-text1="'◆ツールファイルダウンロード'"
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
                  <a href="/file_download?filekbn=4">・ユーザー一括登録テンプレートファイル（最新版） -- ファイル名：users_template_org.xlsx</a>
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
            <div class="row justify-content-between"
              v-if="this.login_user_role === this.login_adminuser_role && this.distributionmode === this.distributionmarutaka_value">
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=11">・管理者画面接続説明書（EXCEL 最新版） -- ファイル名：csd1000l_admin.xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between"
              v-if="this.login_user_role === this.login_adminuser_role && this.distributionmode === this.distributionmarutaka_value">
            >
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=12">・管理者画面接続説明書（PDF 最新版） -- ファイル名：csd1000l_admin.pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between"
              v-if="this.editionmode === this.editionclient_value || this.editionmode === this.editionssjjoo_value"
            >
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=13">・クライアント版サーバー構築手順書（WORD 最新版） -- ファイル名：Operationmanual(clientServer).docx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between"
              v-if="this.editionmode === this.editionclient_value || this.editionmode === this.editionssjjoo_value"
            >
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=14">・クライアント版サーバー構築手順書（PDF 最新版） -- ファイル名：Operationmanual(clientServer).pdf</a>
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
                  <a href="/file_download?filekbn=21">・操作マニュアル（導入編）（EXCEL 最新版） -- ファイル名：Installmanual.xlsx</a>
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
                  <a href="/file_download?filekbn=22">・操作マニュアル（導入編）（PDF 最新版） -- ファイル名：Installmanual.pdf</a>
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
                  <a href="/file_download?filekbn=23">・操作マニュアル（打刻編）（EXCEL 最新版） -- ファイル名：Operationmanual(card_reader).xlsx</a>
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
                  <a href="/file_download?filekbn=24">・操作マニュアル（打刻編）（PDF 最新版） -- ファイル名：Operationmanual(card_reader).pdf</a>
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
                  <a href="/file_download?filekbn=25">・操作マニュアル（設定編）（EXCEL 最新版） -- ファイル名：Operationmanual(setting).xlsx</a>
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
                  <a href="/file_download?filekbn=26">・操作マニュアル（設定編）（PDF 最新版） -- ファイル名：Operationmanual(setting).pdf</a>
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
                  <a href="/file_download?filekbn=27">・操作マニュアル（弥生給与ソフト連携）（EXCEL 最新版） -- ファイル名：Operationmanual(yayoi).xlsx</a>
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
                  <a href="/file_download?filekbn=28">・操作マニュアル（弥生給与ソフト連携）（PDF 最新版） -- ファイル名：Operationmanual(yayoi).pdf</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between"
              v-if="this.distributionmode !== this.distributionmarutaka_value"
            >
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=29">・操作マニュアル（イベントログ取得インストール）（EXCEL 最新版） -- ファイル名：pceventlogmanual.xlsx</a>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- .row -->
            <div class="row justify-content-between"
              v-if="this.distributionmode !== this.distributionmarutaka_value"
            >
              <!-- .col -->
              <div class="col-md-12 pb-2">
                <div class="input-group">
                  <a href="/file_download?filekbn=30">・操作マニュアル（イベントログ取得インストール）（PDF 最新版） -- ファイル名：pceventlogmanual.pdf</a>
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
                  <a href="/file_download?filekbn=31">・操作マニュアル（遠隔リモートインストール）（EXCEL 最新版） -- ファイル名：Operationmanual(TeamViewer).xlsx</a>
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
                  <a href="/file_download?filekbn=32">・操作マニュアル（遠隔リモートインストール）（PDF 最新版） -- ファイル名：Operationmanual(TeamViewer).pdf</a>
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
  props: {
    authusers: {
        type: Array,
        default: []
    },
    generaluser: {
        type: Number,
        default: 0
    },
    generalapproveruser: {
        type: Number,
        default: 0
    },
    adminuser: {
        type: Number,
        default: 0
    },
    distribution: {
        type: Number,
        default: 0
    },
    distribution43z: {
        type: Number,
        default: 0
    },
    distributionssjjoo: {
        type: Number,
        default: 0
    },
    distributionmarutaka: {
        type: Number,
        default: 0
    },
    edition: {
        type: Number,
        default: 0
    },
    editiondemo: {
        type: Number,
        default: 0
    },
    editiontrial: {
        type: Number,
        default: 0
    },
    editioncroud: {
        type: Number,
        default: 0
    },
    editionssjjoo: {
        type: Number,
        default: 0
    },
    editionclient: {
        type: Number,
        default: 0
    }
  },
  data() {
    return {
      login_user_code: 0,
      login_user_role: 0,
      login_generaluser_role: 0,
      login_generalapproveruser_role: 0,
      login_adminuser_role: 0,
      distributionmode : 0,
      distribution43z_value : 0,
      distributionssjjoo_value : 0,
      distributionmarutaka_value : 0,
      editionmode : 0,
      editiondemo_value : 0,
      editiontrial_value : 0,
      editioncroud_value : 0,
      editionssjjoo_value : 0,
      editionclient_value : 0
    };
  },
  // マウント時
  mounted() {
    this.login_user_code = this.authusers['code'];
    this.login_user_role = this.authusers['role'];
    this.login_generaluser_role = this.generaluser;
    this.login_generalapproveruser_role = this.generalapproveruser;
    this.login_adminuser_role = this.adminuser;
    this.distributionmode = this.distribution;
    this.distribution43z_value = this.distribution43z;
    this.distributionssjjoo_value = this.distributionssjjoo;
    this.distributionmarutaka_value = this.distributionmarutaka;
    this.editionmode = this.edition;
    this.editiondemo_value = this.editiondemo;
    this.editiontrial_value = this.editiontrial;
    this.editioncroud_value = this.editioncroud;
    this.editionssjjoo_value = this.editionssjjoo;
    this.editionclient_value = this.editionclient;
  },
  methods: {
    // -------------------- サーバー処理 ----------------------------
    // -------------------- 共通 ----------------------------
  }
};
</script>

