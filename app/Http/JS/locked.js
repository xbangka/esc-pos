;/**/
var _token = "{{$csrf_token}}";

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		keyas: ''
	},
	methods:
	{
		btnClick: function (num){
            let keyas = this.keyas;
            var n = keyas.length;
            if(num=='c'){
                this.keyas = '';

            }else if(num=='<'){
                n = n - 1;
                this.keyas = keyas.substr(0,n)

            }else{
                this.keyas = keyas + '' + num;
                n = this.keyas.length;
                if(n>=5){
                    this.sleep(50).then( ()=> {
                        Swal.fire({
                            title: 'Mengirim',
                            imageUrl: '{{$loadingthree}}',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        this.senddata();
                    })
                }
            }
		},
		placeholder: function (){
            let keyas = this.keyas;
            var txt = '';
            for (let i = 0; i < 5; i++) {
                if(i<keyas.length){
                    txt += '<i class="fa fa-circle"></i> ';
                }else{
                    txt += '<i class="fa fa-minus"></i> ';
                }
            }
			return txt.trim();
        },
        sleep: function (ms){
            return new Promise(resolve => setTimeout(resolve, ms));
        },
		senddata: function (){
			let encryption 	= new Encryption();
			var keyas 		= encryption.encrypt(this.keyas, "{{$key_salt}}");
			var code 		= encryption.encrypt("{{$code}}", "{{$key_salt}}");

			axios
				.post("{{$checking_keyas}}", {
					_token: _token,
					keyas: keyas,
					toko: code
				})
				.then(
					response => {
						var result = response.data;
						if(result.indexOf('*reload*')!='-1'){
							location.reload();
						}else{
                            Swal.fire(result,'','error');
                            this.keyas = '';
						}
					}
				).catch( function (error) {
					Swal.fire(
						'Failed',
						'Gagal mengirim data',
						'error'
					);
					console.log(error);
				});
		}
	}
})