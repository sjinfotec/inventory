<template>
  <!-- col -->
  <div class="col-sm-6 col-md-3 col-lg-2 pb-2 align-self-stretch">
    <div class="card text-white bg-warning border-0">
      <div class="card-body px-3 py-2">
        <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-w.svg" alt=""></span>
        <h1 class="font-size-sm m-0 mb-1">{{ this.itemName }}</h1>
        <p class="font-size-lg m-0">{{ this.itemValue }}</p>
      </div>
    </div>
  </div>
  <!-- /.col -->
</template>
<script>

export default {
  name: "colnotemploymentworking",
  props: {
    itemName: {
        type: String,
        default: null
    },
    itemValue: {
        type: String,
        default: null
    }
  }
};
</script>
