<template>
  <div v-if="this.missingmiddletime !== null || this.displaynone">
    <!-- col -->
    <div class="col-6 pb-2 clearfix align-self-stretch float-left">
      <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-warning rounded"><img class="icon-size-lg" src="/images/round-skip_previous-w.svg" alt=""></span>
      <h1 class="font-size-sm m-0 mb-1">{{ this.missingmiddlename }}</h1>
      <p class="font-size-lg m-0">{{ this.missingmiddletime }}</p>
    </div>
    <!-- /.col -->
    <!-- col -->
    <div class="col-6 pb-2 clearfix align-self-stretch float-left">
      <span class="float-left mr-2 py-2 px-2 font-size-lg text-white bg-warning rounded"><img class="icon-size-lg" src="/images/round-skip-next-w.svg" alt=""></span>
      <h1 class="font-size-sm m-0 mb-1">戻り時間</h1>
      <p class="font-size-lg m-0">{{ this.missingmiddlereturntime }}</p>
    </div>
    <!-- /.col -->
  </div>
</template>
<script>

export default {
  name: "ColMissingMiddle",
  props: {
    missingmiddlename: {
        type: String,
        default: null
    },
    missingmiddletime: {
        type: String,
        default: null
    },
    missingmiddlereturntime: {
        type: String,
        default: null
    },
    displaynone: {
        type: Boolean,
        default: true
    }
  },
  // マウント時
  mounted() {
    console.log("ColMissingMiddle mounted"+this.missingmiddlename);
  }
};
</script>
