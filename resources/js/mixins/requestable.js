export const requestable =  {
  data() {
    return {
      messagedata: ""
    };
  },
  methods:{
    // -------------------------- public メソッド ------------------------
    // post
    postRequest: function(url, arrayparams) {
      let self = this;
      // 連想配列はObject.keys(arrayparams).length
      var len = Object.keys(arrayparams).length;
      if (len != 0) {
        return new Promise( function (resolve, reject) {
          self.$axios
          .post(url, {
            keyparams: arrayparams
          })
          .then(response  => {
            resolve(response);
          })
          .catch(reason => {
            reject(reason);
          });
        });
      } else {
        return new Promise( function (resolve, reject) {
          self.$axios
          .post(url)
          .then(response  => {
            resolve(response);
          })
          .catch(reason => {
            reject(reason);
          });
        });
      }
    }
    // -------------------------- private メソッド ------------------------
  }
}
