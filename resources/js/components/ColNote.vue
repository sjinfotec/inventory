<template>
  <!-- col -->
  <div class="col-12 pb-2 align-self-stretch">
    <div v-if="this.itemControl === 'WA'">
      <div class="card text-black border border-warning bg-warning">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'NG'">
      <div class="card text-black border border-danger bg-danger">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-error-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'PRIMARY'">
      <div class="card text-secondary border border-secondary bg-primary">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'SUCCESS'">
      <div class="card text-secondary border border-secondary bg-success">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'SECONDARY'">
      <div class="card text-white border border-secondary bg-secondary">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/images/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'INFO'">
      <div class="card text-white border border-info bg-info">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/imagesround-info-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'LIGHT'">
      <div class="card text-light border border-black bg-light">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'DARK'">
      <div class="card text-dark border border-dark bg-dark">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
    <div v-if="this.itemControl === 'WHITE'">
      <div class="card text-black border border-black bg-white">
        <div class="card-body px-3 py-2">
          <span class="d-md-none float-left"><img class="icon-size-ml mr-2" src="/round-watch-later-b.svg" alt=""></span>
          <h1 class="font-size-sm m-0">{{ this.itemName }}</h1>
          <p v-for="value in itemNote" class="font-size-sm my-2">{{ value }}</p>
        </div>
      </div>
    </div>
  </div>
  <!-- /.col -->
</template>
<script>

export default {
  name: "colnote",
  props: {
    itemName: {
        type: String,
        default: null
    },
    itemControl: {
        type: String,
        default: null
    },
    itemNote: {
        type: Array
    }
  },
  data() {
    return {
      itemValue:''
    };
  }
};
</script>
