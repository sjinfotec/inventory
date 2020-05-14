<template>
  <div>
    <el-dialog
      custom-class="custom-bg-dark"
      v-bind:title="dateName + ' 打刻時の位置情報'"
      :visible.sync="dialogVisible"
      width="60%"
      center="true"
      v-if="dialogVisible"
    >
      <!-- <div class="col-xs-12 padding-dis-left"> -->
      <!-- <div>{{ user_name }} さん {{mode_name}} : {{ record_time }}</div> -->
      <div class="card bg-dark text-white">
        <!-- <div class="card-header">打刻時の位置情報</div> -->
        <div class="card-body">
          <h5 class="card-title">{{ user_name }} さん</h5>
          <h5 class="card-subtitle mb-2 color-chartreuse">{{ record_time }} {{mode_name}}</h5>
          <div style="width: 100%; overflow: hidden; width:100%;">
            <iframe
              v-bind:src="'https://www.google.com/maps/embed/v1/place?q='+longitude+','+latitude+'&key='+apiKey"
              width="100%"
              height="600"
              frameborder="0"
              style="border:0; margin-top: -150px;"
            ></iframe>
            <div class="card-footer text-align-right padding-dis">
              <span class="float-left">※機種の設定により位置情報が多少ずれる事があります。</span>
              <el-button type="danger" @click="dialogVisible = false">閉じる</el-button>
            </div>
          </div>
          <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
        </div>
      </div>

      <!-- <div class="dialog-footer text-align-right"> -->
      <!-- <span slot="footer" class="dialog-footer"> -->
      <!-- <el-button type="danger" @click="dialogVisible = false">閉じる</el-button> -->
      <!-- <el-button type="success" @click="dialogVisible = false">承認</el-button> -->
      <!-- </span> -->
      <!-- </div> -->
      <!-- </div> -->
    </el-dialog>
  </div>
</template>
<script>

export default {
  name: "showmapdialog",
  props: {
    dateName: {
      type: String,
      default: ""
    },
    dialogVisible: {
      type: Boolean,
      default: false
    },
    longitude: {
      type: String,
      default: ""
    },
    latitude: {
      type: String,
      default: ""
    },
    record_time: {
      type: String,
      default: ""
    },
    user_name: {
      type: String,
      default: ""
    },
    mode_name: {
      type: String,
      default: ""
    },
    // TODO: 本来は .envに記載して取得したい
    apiKey: {
      type: String,
      default: "AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8"
    }
  },
  created() {
    // scriptjs(
    //       "https://maps.googleapis.com/maps/api/js?key=AIzaSyDmNKensj6Y3qEY9t0v1kbQqUxdOrhq3X8&callback=initMap",
    //       "loadGoogleMap"
    //     );
    // scriptjs.ready("loadGoogleMap", this.loadMap);
  }
};
</script>
