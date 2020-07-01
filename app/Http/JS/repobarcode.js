/* script */
var app = new Vue({
	el: '.{{$appid}}',
	data: {
		reporows: 0,
		mydbrows: 0,
		repolimit: 53, /*53753 */
		mytables: [{
					"barcode": "0",
					"full_name": "",
					"short_name": "",
					"created_at": ""
				}],
		loading: false
	},
	methods:
	{
        recursivex: function (){
            this.sleep(2000).then( ()=> {
				if(this.reporows<=this.repolimit){
					this.getRepo();
				}else{
					this.loading = false;
					alert('Selesai')
				}
			})
        },
		getRepo: function (){
			var startx = this.reporows;
			axios
				.get("{{$getval}}?start="+startx)
				.then(
					response => {
						
						this.mytables = [{
											"barcode": "0",
											"full_name": "",
											"short_name": "",
											"created_at": ""
										}];
						this.sleep(200).then( ()=> {
							var result = response.data;
							var nx = this.reporows;
							this.mydbrows = result.data_rows;
							this.mytables = result.data_repo;
							this.loading = true;
							this.reporows = parseInt(nx) + 10;
							
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