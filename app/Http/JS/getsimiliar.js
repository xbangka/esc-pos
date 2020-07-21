var _iconsvgloading = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <rect x="0" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="10" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="20" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"/></rect></svg>';
var _iconsvgwarning = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="80" height="80"><style type="text/css">* { fill: #ff5d00 }</style><path d="M19.64 16.36L11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg>';
var _token = "{{$csrf_token}}";

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		myword: '',
		mytables: []
	},
	methods:
	{
        btnCariProduct: function (){
            var myword = this.myword;
            if(myword=='') return false;
            axios
				.get("{{$get_handayani}}?q="+myword)
				.then(
					response => {
						if(response.status==200){
							var result      = response.data;
							if(result!='' && result.length!=0 ){
								this.mytables 	= result;
							}else{
								alert('tidak ada');
							}
						}else{
							console.log(response.statusText);
							this.mytables 	= [];
						}
					}
				).catch( function (error) {
					alert('1 '+error);
					console.log(error);
                });
        },
		btnSave: function (data){
            data.loading = true;
			axios
            .post("{{$save_product}}",
            {
                _token: _token,
                _code: data.code,
                _name: data.name
            })
            .then(
                response => {
                    data.loading = false;
                    var result = response.data;
                    if(result=='OK'){
                        var n = this.mytables.length;
                        for (let i = 0; i < n; i++) {
                            if(this.mytables[i].code==data.code){
                                this.mytables[i].exist = true;
                                break;
                            }
                        }
                    }
                }
            ).catch( function (error) {
                alert('2 '+error);
                console.log(error);
            });
		}
	}
})