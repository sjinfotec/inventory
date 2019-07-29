<template>
  <div v-if="this.attendancetime !== null || this.displaynone">
    <!-- col -->
    <div class="col-6 col-lg-2 pb-2 align-self-stretch">
      <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-success rounded"><img class="icon-size-lg" src="/images/round-play-circle-filled-w.svg" alt=""></span>
      <h1 class="font-size-sm m-0 mb-1">出勤時間</h1>
      <p class="font-size-lg m-0">{{ this.attendancetime }}</p>
    </div>
    <!-- /.col -->
    <!-- col -->
    <div class="col-6 col-lg-2 pb-2 align-self-stretch">
      <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-success rounded"><img class="icon-size-lg" src="/images/round-pause-circle-filled-w.svg" alt=""></span>
      <h1 class="font-size-sm m-0 mb-1">退勤時間</h1>
      <p class="font-size-lg m-0">{{ this.leavingtime }}</p>
    </div>
    <!-- /.col -->
  </div>
</template>
<script>

export default {
  name: "ColAttendance",
  props: {
    attendancetime: {
        type: String,
        default: null
    },
    leavingtime: {
        type: String,
        default: null
    },
    displaynone: {
        type: Boolean,
        default: true
    }
  }
};
</script>
