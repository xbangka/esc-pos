var _token = "{{$csrf_token}}";
var _iconsvgwarning = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="80" height="80"><style type="text/css">* { fill: #ff5d00 }</style><path d="M19.64 16.36L11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg>';

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		email: '',
		password: '',
		loading: false
	},
	methods:
	{
		login: function (str){
			if( !this.validemail(this.email) ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Email</h4><span class="mt-5">Ketik email dengan format yang benar</span>'
				});
				return false;
			}
			if( !this.validdomainemail(this.email) ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Email</h4><span class="mt-5">Domain email tidak direkomendasikan</span>'
				});
				return false;
			}
			if( this.password.length==0 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Password Kosong</h4><span class="mt-5">Password harus di isi</span>'
				});
				return false;
			}else if( this.password.length<=4 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Password</h4><span class="mt-5">Password yang di ketik terlalu pendek</span>'
				});
				return false;
			}
			this.loading = true;
			return this.senddata();
		},
		validemail: function (email){
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if (email.match(mailformat))
				return true;
			return false;
		},
		validdomainemail: function (email){
			var dx = email.split("@");
			var domain = dx[dx.length - 1];
			var domainallow = {{$email_allow}};
			
			if(domainallow.indexOf(domain) !== -1){
				return true;
			}
			return false;
		},
		senddata: function (){
			let encryption 	= new Encryption();
			var passHash 	= CryptoJS.SHA256(this.password);
			var _q 	= {email:this.email,password:this.password};
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
				.post("{{$signin}}", {
					_token: _token,
					_hash: passHash.toString(),
					_q: _q
				})
				.then(
					response => {
						var result = response.data;
						if(result.indexOf('*OK*')!='-1'){
							Swal.fire(
								'Berhasil',
								'Anda dipersilakan masuk ke dashboard',
								'success'
							)
							window.location.href = "{{$dashboard}}";
						}else{
							Swal.fire({
								html: _iconsvgwarning+'<h4 class="mt-4">Gagal</h4><span class="mt-5">'+result+'</span>'
							});
						}
						this.loading = false;
					}
				).catch( function (error) {
					Swal.fire(
						'Failed',
						'Gagal mengirim data',
						'error'
					)
					console.log(error);
				});
		}
	}
})