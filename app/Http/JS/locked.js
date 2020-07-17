;/**/
var _token = "{{$csrf_token}}";
var _iconsvgloading = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <rect x="0" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="10" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="20" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"/></rect></svg>';

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
						Swal.fire({
							html: _iconsvgloading+'<h3 class="mt-4">Pemeriksaan</h3>',
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