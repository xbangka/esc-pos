/* script */
var app = new Vue({
	el: '.{{$appid}}',
	data: {
        resultresponse: 0,
		loading: false
	},
	methods:
	{
        recursivex: function (){
            if(this.resultresponse=='finish'){
                this.loading = false;
                alert('Selesai')
            }else{
                this.getRepo();
            }
        },
		getRepo: function (){
            let idx = this.resultresponse;
			axios
				.get("{{$getsvg}}?start="+idx)
				.then(
					response => {
						this.resultresponse = '';
						this.sleep(1000).then( () => {
                            this.resultresponse = response.data;
                            this.loading = true;
							this.recursivex();
						})
					}
				).catch( function (error) {
					this.loading = false;
					alert('error');
					console.log(error);
				});
		},
		sleep: function (ms){
            return new Promise(resolve => setTimeout(resolve, ms));
        },
	}
})