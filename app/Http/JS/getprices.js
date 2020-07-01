/* script */
var app = new Vue({
	el: '.{{$appid}}',
	data: {
		upc: 0,
		price: 0,
		mytables: [],
		loading: false
	},
	methods:
	{
        recursivex: function (){
			this.loading    = true;
            axios
				.get("{{$get_upc}}")
				.then(
					response => {
						if(response.status==200){
							var result      = response.data;
							if(result!='' && result.length!=0 ){
								this.mytables 	= result;
								this.getRepo();
							}else{
								this.loading    = false;
								alert('Selesai');
							}
						}else{
							console.log(response.statusText);
							this.sleep(2000).then( ()=> {
								this.mytables 	= [];
								this.getRepo();
							})
						}
					}
				).catch( function (error) {
					this.loading    = false;
					alert('1 '+error);
					console.log(error);
                });
        },
		getRepo: function (){
			var upc = this.upc;
			var mytables  = this.mytables;
			let nx  = mytables.length;
			var counter = 0;
			for (let i = 0; i < nx; i++) {
				axios
					.get("{{$get_prices}}?EAN13="+mytables[i].barcode)
					.then(
						response => {
							if(response.status==200){
								var result = response.data;
								for (var i = 0; i < nx; i++){
									if (mytables[i].barcode == result.barcode){
										mytables[i].price = result.price;
										break;
									}
								}
								counter++;
								if(counter>=nx){
									this.recursivex();
								}
							}else{
								console.log(response.statusText);
							}
						}
					).catch( function (error) {
						counter++;
						if(counter>=nx){
							app.recursivex();
						}
						alert('2 '+error);
						console.log(error);
					});
			}
		},
		sleep: function (ms){
            return new Promise(resolve => setTimeout(resolve, ms));
        },
		clearTable: function (){
            this.mytables = [];
        }
	}
})