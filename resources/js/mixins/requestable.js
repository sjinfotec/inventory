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
    }
    // -------------------------- private メソッド ------------------------
  }
}
