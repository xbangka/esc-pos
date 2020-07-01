/* script */
var app = new Vue({
	el: '.{{$appid}}',
	data: {
		resultresponse: '',
		loading: false
	},
	methods:
	{
        recursivex: function (){
            this.sleep(100).then( ()=> {
				if(this.resultresponse=='finish'){
                    this.loading = false;
					alert('Selesai')
				}else{
					this.getRepo();
				}
			})
        },
		getRepo: function (){
			axios
				.get("{{$getuuid}}")
				.then(
					response => {
						this.resultresponse = '';
						this.sleep(100).then( ()=> {
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