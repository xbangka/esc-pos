;/**/
var _token = "{{$csrf_token}}";
var _removeitem = "";
var _counterremoveitem = 0;
var _daysname = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
var _monthsname = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
var _iconsvgloading = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <rect x="0" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="10" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="20" y="0" width="4" height="10" fill="#333"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"/></rect></svg>';
var _iconsvgwarning = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="80" height="80"><style type="text/css">* { fill: #ff5d00 }</style><path d="M19.64 16.36L11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg>';
var encryption 	= new Encryption();

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		uniqid: '',
		tempuniqid: '~',
		detailtransaksi: [],
		responses: [{name:'',code:'',category:'',discountHits:[]}],
        UPC: '',
        cash: 0,
        datetimestuk: '',
        timenow: '',
        pagePayData: false,
		readyscan: true
	},
	computed:{
		subtotal: function (){
			var n_arr = this.detailtransaksi.length;
			var subtotal = 0;
			for (let i = 0; i < n_arr; i++) {
				if(this.UPC!='~'){
					const trx = this.detailtransaksi[i];
					subtotal = subtotal + (trx.price * trx.qty);
				}
			}
			return subtotal;
		},
		discount: function (){
			var trx = this.detailtransaksi;
			var n_arr = trx.length;
			if(n_arr>=1 && this.UPC!='~'){
				var discount_total 	= 0; 
				for (let i = 0; i < n_arr; i++) {
					var price 		= trx[i].price;
					var qty 		= trx[i].qty;
					var arrDiscounts= trx[i].discount;
					var n_arr_disc 	= arrDiscounts.length;
					for (let n = 0; n < n_arr_disc; n++) {
						var discountx = arrDiscounts[n];
						if(parseFloat(discountx.value)>0){
							if(discountx.value_type==1 && qty>=discountx.condition_qty_from && qty<=discountx.condition_qty_to){
								discount_total = discount_total + parseFloat(discountx.value);

							}else if(discountx.value_type==2 && qty>=discountx.condition_qty_from && qty<=discountx.condition_qty_to){
								var pricex = parseFloat(price) * parseInt(qty);
								var discx = (pricex / 100) * parseFloat(discountx.value);
								discount_total = discount_total + discx;
							}
						}
					}
				}
				return discount_total;
				
			}else{
				return 0;
			}
		},
		totaltrx: function (){
			return this.subtotal - this.discount;
		},
		changedue: function (){
			return this.cash - this.totaltrx;
		},
		changedue_composition: function (){
			if(this.changedue>0){
				var changedue = this.changedue;
				var nominal = [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100];
				var dividen = 0;
				var html = '';
				var color = [];
					color[100000] = ' text-danger';
					color[50000] = ' text-primary';
					color[20000] = ' text-success';
					color[10000] = ' text-danger';
					color[5000] = ' text-warning';
					color[2000] = '';
					color[1000] = '';
					color[500] = '';
					color[200] = '';
					color[100] = '';
				var i_count = 0;
				for (let n = 0; n < 10; n++) {
					dividen = nominal[n];
					i_count = 0;
					for (let i = 1; i <= 10; i++) {
						if(changedue>=dividen){
							changedue = changedue - dividen;
						}else{
							break;
						}
						i_count = i;
					}
					if(i_count>0){
						html += '<p class="mb-0'+color[dividen]+'">'+this.numThousans(dividen)+' x '+i_count+'</p>';
					}
				}
				return html;
			}
			return '';
		},
		beatheart: {
			get: function (){
				return this.datetimestuk;
			},
			set: function (uuid){
				this.datetimestuk = uuid;
			}
		},
		thisBrowserHash: function (){
			return this.hashCode( navigator.product + ' ' + navigator.appVersion + ' ' + navigator.platform + ' ' + navigator.language + ' {{$code}}');
		}
	},
	watch: {
		
	},
    mounted()
    {
		this.uniqid = Math.random().toString(36).substring(2,10);
		this.setlisten();
		setInterval(function(){app.updateTime();},1000);
    },
	methods:
	{
        setlisten(){
            
            document.addEventListener("keydown", function(e) {
                var code = app.UPC;
                if(e.keyCode===13){
                    app.btnCariProduk(code);
                    app.UPC = '';
                }else{
                    const textInput = e.key || String.fromCharCode(e.keyCode);
                    const targetName = e.target.localName;
                    if (textInput && textInput.length === 1 && targetName !== 'input'){
                        app.UPC = code + textInput;
                    }
                }
            });
        
		},
		btnCekProduct: function (){
			var barcode = this.UPC;
			this.btnCariProduk(barcode);
		},
		btnCariProduk: function (barcode){
			if(barcode=='') return false;
			
			var barcode	    = barcode.trim();
			var data 		= localStorage.getItem(this.thisBrowserHash+barcode) || false;

			if(data){
				var code_toko = "{{$code}}";
				var nx = code_toko.length;
				data = encryption.decrypt(data, code_toko);
				data = data.substr(nx);
				data = data.slice(0,-nx);
				data = JSON.parse( data.trim() );
				this.fetchDataProduk(data,'local');
				this.UPC = '';
			}else if(navigator.onLine){
				barcode	    	= encryption.encrypt(barcode, "{{$key_salt}}");
				var code 		= encryption.encrypt("{{$code}}", "{{$key_salt}}");
				var keyHash 	= CryptoJS.SHA256('{{$key_salt}}');
				
				Swal.fire({
					html: _iconsvgloading+'<h3 class="mt-4">Mencari</h3>',
					showConfirmButton: false,
					allowOutsideClick: false
				});
	
				axios
				.post("{{$get_products}}", {
					_token: _token,
					_UPC: barcode,
					_toko: code
				},
				{
					headers: {
						Hash: keyHash.toString()
					}
				})
				.then(
					response => {
						var result = response.data;

						if(result.code==200){
							this.UPC = '';
							this.fetchDataProduk(result.data,'external');
						}else{
							Swal.fire("Tidak Ditemukan",result.message,'error');
						}
					}
				).catch( function (error) {
					Swal.fire(
						'Kesalahan',
						'Gagal mencari data',
						'error'
					);
					console.log(error);
				});
			}else{
				Swal.fire({
					html: _iconsvgwarning+'<h2 class="mt-4">Offline</h2><span class="mt-5">Tidak dapat mencari harga produk, Internet network sedang Offline ?</span>'
				});
			}
		},
		fetchDataProduk: function (data,source){
			var n_data = data.length;
			if(n_data==0){
				Swal.fire({
					html: _iconsvgwarning+'<h2 class="mt-4">Setting</h2><span class="mt-5">Barcode harga belum di setting</span>'
				});
			}else if(n_data==1){
				var product_price = data[0];
				if(product_price.discount.length==0){
					var found = this.detailtransaksi.some( el=>el.uuid===product_price.uuid );
					if(found){
						var objIndex = this.detailtransaksi.findIndex((obj => obj.uuid===product_price.uuid));
						Vue.set(this.detailtransaksi[objIndex],'bg','blink-once');
						this.sleep(500).then( ()=> {
							this.detailtransaksi[objIndex].qty = this.detailtransaksi[objIndex].qty + 1;
							delete this.detailtransaksi[objIndex].bg;
						});
					}else{
						product_price.qty = 1;
						product_price.bg = 'blink-once';
						this.detailtransaksi.push(product_price);
						var objIndex = this.detailtransaksi.findIndex((obj => obj.uuid===product_price.uuid));
						this.sleep(500).then( ()=> {
							delete this.detailtransaksi[objIndex].bg;
						});
					}
				}else{
					this.responses = data;
					$('#showmodal').click();
				}
				Swal.close();
			}else{
				this.responses = data;
				$('#showmodal').click();
				Swal.close();
			}

			if(source==='external' && n_data>=1){
				
				var barcode 	= data[0].code;
				var code_toko 	= "{{$code}}";
				var dataEncript = JSON.stringify(data);
					dataEncript = code_toko + dataEncript + code_toko;
					dataEncript = encryption.encrypt(dataEncript, code_toko);
				localStorage.setItem(this.thisBrowserHash+barcode,dataEncript);
			}
		},
		btnvarianitem: function (uuid){
			var arr = this.responses;
			var n_arr = arr.length;
			for (let i = 0; i < n_arr; i++) {
				var product_price = arr[i];
				if(product_price.uuid===uuid){
					let found = this.detailtransaksi.some( el=>el.uuid===uuid );
					if(found){
						let objIndex = this.detailtransaksi.findIndex((obj => obj.uuid===uuid));
						this.detailtransaksi[objIndex].qty = this.detailtransaksi[objIndex].qty + 1;
						Vue.set(this.detailtransaksi[objIndex], 'bg', 'blink-once');
						this.sleep(500).then( ()=> {
							delete this.detailtransaksi[objIndex].bg;
						});
					}else{
						product_price.qty = 1;
						product_price.bg = 'blink-once';
						this.detailtransaksi.push(product_price);
						var objIndex = this.detailtransaksi.findIndex((obj => obj.uuid===product_price.uuid));
						this.sleep(500).then( ()=> {
							delete this.detailtransaksi[objIndex].bg;
						});
					}
					break;
				}
			}
		},
		btnremoveitem: function (uuid){
			var fi = '_'+uuid;
			if(_removeitem!=fi){
				_removeitem = fi;
				_counterremoveitem = 1;
			}else{
				_counterremoveitem++;
				if(_counterremoveitem>=3){
					_removeitem = '';
					_counterremoveitem = 0;
					Swal.fire({
						title: 'Konfirmasi hapus',
						text: 'Yakin ingin dihapus ?',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Ya, hapus !'
					}).then((result) => {
						if(result.value){
							this.detailtransaksi = this.detailtransaksi.filter(function (obj) {
								return obj.uuid !== uuid;
							});
						}
					});
					
				}
			}
		},
		sleep: function (ms){
            return new Promise(resolve => setTimeout(resolve, ms));
        },
		discountHits: function (price, qty, arrDiscounts=[]){
			var n_arr = arrDiscounts.length;
			if(n_arr>=1){
				var html = ''; 
				for (let i = 0; i < n_arr; i++) {
					var discounty = arrDiscounts[i];
					if(parseFloat(discounty.value)>0){
						if(discounty.value_type==1 && qty>=discounty.condition_qty_from && qty<=discounty.condition_qty_to){
							html += '<span class="badge badge-danger badge-pill" title="'+discounty.event_name+'&#13;min : '+discounty.condition_qty_from+'">'+
										'-'+ this.numThousans(parseFloat(discounty.value))
									+'</span>';
						}else if(discounty.value_type==2 && qty>=discounty.condition_qty_from && qty<=discounty.condition_qty_to){
							var pricex = parseFloat(price) * parseInt(qty);
							var discx = (pricex / 100) * parseFloat(discounty.value);
							html += '<span class="badge badge-danger badge-pill">'+
										'-'+ this.numThousans(discx)
									+'</span>';
						}
					}
				}
				return html;
			}else{
				return '';
			}
        },
		discountHitsModal: function (arrDiscounts=[]){
			var n_arr = arrDiscounts.length;
			if(n_arr>=1){
				var html = ''; 
				for (let i = 0; i < n_arr; i++) {
					var discountr = arrDiscounts[i];
					if(parseFloat(discountr.value)>0){
						if(discountr.value_type==1){
							html += '<span class="badge badge-danger badge-pill">'+
										'-'+ this.numThousans(parseFloat(discountr.value))
									+'</span><br><b>'+discountr.event_name+'</b>';
									if(discountr.condition_qty_from>=2){
										html += '<br>min: '+discountr.condition_qty_from+'<br>max: '+discountr.condition_qty_to;
									}
						}else if(discountr.value_type==2){
							html += '<span class="badge badge-danger badge-pill">'+
										'-'+ parseFloat(discountr.value) + ' %'
									+'</span><br><b>'+discountr.event_name+'</b>';
									if(discountr.condition_qty_from>=2){
										html += '<br>min: '+discountr.condition_qty_from+'<br>max: '+discountr.condition_qty_to;
									}
						}
					}
				}
				return html;
			}else{
				return '';
			}
        },
        numThousans: function (x=0){
			x = x.toString().replace('.', ',');
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },
        btnkeypadnum: function (x){
			
			let today = new Date();
			let hh = today.getHours();
			let ii = today.getMinutes();
			let dd = today.getDate();
			let mm = today.getMonth()+1;
			let yy = today.getFullYear().toString().substr(2,2);
			this.datetimestuk = dd+'.'+mm+'.'+yy+'-'+hh+':'+ii;
			
			var cash = this.cash + '';
			
			if(x==='<'){
				cash = cash.substr(0,cash.length-1);
			}else if(x==='p'){
				cash = this.totaltrx;
			}else if(x==='10K'){
				cash = 10000;
			}else if(x==='20K'){
				cash = 20000;
			}else if(x==='50K'){
				cash = 50000;
			}else if(x==='100K'){
				cash = 100000;
			}else{
				cash = cash + '' + x;
			}

			cash = (cash=='') ? 0 : cash;
            return this.cash = parseInt(cash);
        },
        btnqtycontrol: function (uuid,x){
			var arr = this.detailtransaksi;
			var n_arr = arr.length;
			for (let i = 0; i < n_arr; i++) {
				var product_price = arr[i];
				if(product_price.uuid===uuid){
					if(x==='-' && product_price.qty!=1){
						var n = product_price.qty - 1 ;
						this.beatheart = product_price.uuid;
						Vue.set(app.detailtransaksi[i], 'qty', n);
					}else if(x==='+'){
						var n = product_price.qty + 1 ;
						this.beatheart = product_price.uuid;
						Vue.set(app.detailtransaksi[i], 'qty', n);
					}
					break;
				}
			}
			this.UPC = '.';
			this.sleep(10).then( ()=> {
				this.UPC = '';
			});
        },
        btnreset: function (){
			this.detailtransaksi 	= [];
			this.responses 			= [{name:'',code:'',category:'',discountHits:[]}];
			this.cash 				= 0;
			this.uniqid 			= Math.random().toString(36).substring(2,10);
			document.getElementById("txtcode").focus();
        },
        hashCode: function (str){
			var hash = 0;
			if(str.length==0){
				return hash;
			}
			for (let i = 0; i < str.length; i++) {
				var char = str.charCodeAt(i);
				hash = ((hash<<5)-hash)+char;
				hash = hash & hash;
			}
			hash = Math.abs(hash);
			return hash.toString();
        },
		pagePay: function (param){
			return this.pagePayData=param;
		},
		saveAndPrint: function (){
			this.sendData();
		},
		onlySave: function (){
			this.sendData(1);
		},
		onlyPrint: function (){
			this.sendData(2);
		},
		sendData: function (action=0){
			if(this.tempuniqid==this.uniqid){
				Swal.fire({
					html: _iconsvgwarning+'<h2 class="mt-4">Sudah</h2><span class="mt-5">Sudah dilakukan transaksi, klik reset untuk memulai transaksi lainnya</span>'
				});
				return false;
			}
			if(this.changedue<0){
				Swal.fire({
					html: _iconsvgwarning+'<h2 class="mt-4">Kembalian Minus</h2><span class="mt-5">Tidak dapat menlanjutkan transaksi, jika angka kembalian minus</span>'
				});
				return false;
			}
			if(!navigator.onLine){
				Swal.fire({
					html: _iconsvgwarning+'<h2 class="mt-4">Offline</h2><span class="mt-5">Tidak dapat menlanjutkan transaksi, Internet network sedang Offline ?</span>'
				});
				return false;
			}
			Swal.fire({
				html: _iconsvgloading+'<h3 class="mt-4">Processing</h3>',
				showConfirmButton: false,
				allowOutsideClick: false
			});
			var trx = {
						items : this.detailtransaksi,
						total : this.totaltrx,
						cash : this.cash,
						changedue : this.changedue,
						toko: "{{$code}}",
						action: action,
						trxuniqid: this.uniqid
					};
				trx = JSON.stringify(trx);
				trx = encryption.encrypt(trx, "{{$key_salt}}");

			this.frequentlyBarcodeHits(this.detailtransaksi);

			axios
			.post("{{$send_transaction}}", {
				_token: _token,
				_trx: trx
			})
			.then(
				response => {
					var result = response.data;
					if(action!=1){
						Swal.close();
						location.href = "my.bluetoothprint.scheme://http://0.0.0.0:8080/?q="+result;
					}
				}
			).catch( function (error) {
				Swal.fire(
					'Kesalahan',
					'Gagal mencari data',
					'error'
				);
				console.log(error);
			});
		},
		frequentlyBarcodeHits: function(trx){
			var nx = trx.length;
			var frequentlyname = 'frequently' + this.thisBrowserHash;

			var datafrequently = localStorage.getItem(frequentlyname) || false;
			if(datafrequently){
				datafrequently = JSON.parse( datafrequently.trim() );
			}else{
				datafrequently = [];
			}

			var found = false;
			var barcode;
			var barcodeyxz = '';
			var d 	= 	new Date();
			var dt 	= 	this.zeroPadding(d.getFullYear(),4) + '-' +
						this.zeroPadding(d.getMonth()+1) + '-' +
						this.zeroPadding(d.getDate()) + ' ' +
						this.zeroPadding(d.getHours()) + ':' +
						this.zeroPadding(d.getMinutes()) + ':' +
						this.zeroPadding(d.getSeconds());
			if(nx>=1){
				for (let i = 0; i < nx; i++) {
					barcode = trx[i].code;
					if(barcode!=='' && barcodeyxz!==barcode){
						barcodeyxz 	= barcode;
						found		= datafrequently.some(el => el.code===barcode);
						if(found){
							let objIndex = datafrequently.findIndex((obj => obj.code===barcode));
							datafrequently[objIndex].hits = datafrequently[objIndex].hits + 1;
						}else{
							datafrequently.push( {code:barcode, hits:1, date:dt} );
						}
					}
				}
				localStorage.removeItem(frequentlyname);
				datafrequently.sort( this.compareValues('hits','desc') );
				datafrequently = JSON.stringify(datafrequently);
				localStorage.setItem(frequentlyname,datafrequently);
			}
		},
		compareValues: function (key,order='asc'){
            return function innerSort(a,b){
				if(!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
					return 0;
				}
				const varA = (typeof a[key] === 'string') ? a[key].toUpperCase() : a[key];
				const varB = (typeof b[key] === 'string') ? b[key].toUpperCase() : b[key];
				let comparison = 0;
				if(varA > varB){
					comparison = 1;
				}else if(varA < varB){
					comparison = -1;
				}
				return (
					(order==='desc') ? (comparison * -1) : comparison
				);
			}
		},
		updateTime: function (){
			var d = new Date();
			this.timenow 	= 	_daysname[d.getDay()] + ', ' +
								d.getDate() + ' ' +
								_monthsname[d.getMonth()] + ' ' +
								this.zeroPadding(d.getFullYear(),4) + ' ' +
								this.zeroPadding(d.getHours()) + ':' +
								this.zeroPadding(d.getMinutes()) + ':' +
								this.zeroPadding(d.getSeconds());
			
			this.datetimestuk = this.zeroPadding(d.getDate()) + '.' +
								this.zeroPadding(d.getMonth()+1) + '.' +
								this.zeroPadding(d.getFullYear()) + '-' +
								this.zeroPadding(d.getHours()) + ':' +
								this.zeroPadding(d.getMinutes());
		},
		zeroPadding: function (num=0, digit=2){
			var zero ;
			for (let i = 0; i < digit; i++) zero += '0';
			return (zero + num).slice(-digit);
		},
		btnUpdateDataLocal: function (){
			var frequentlyname = 'frequently' + this.thisBrowserHash;

			var datafrequently = localStorage.getItem(frequentlyname) || false;
			if(datafrequently){
				datafrequently = JSON.parse( datafrequently.trim() );
			}else{
				datafrequently = [];
			}

			var nx = datafrequently.length;

			if(nx!=0){
				var fi = '_update_data';
				if(_removeitem!=fi){
					_removeitem = fi;
					_counterremoveitem = 1;
				}else{
					_counterremoveitem++;
					if(_counterremoveitem>=3){
						_removeitem = '';
						_counterremoveitem = 0;
						Swal.fire({
							title: 'Update',
							text: 'mengupdate data lokal, yakin ingin menlanjutkan ?',
							icon: 'question',
							showCancelButton: true,
							confirmButtonText: 'Ya, Update'
						}).then((result) => {
							if(result.value){
								Swal.fire({
									html: _iconsvgloading+'<h3 class="mt-4">Sedang Update</h3>',
									showConfirmButton: false,
									allowOutsideClick: false
								});
								var databarcode = [];
								var code;
								var date;
								for (let i = 0; i < nx; i++) {
									code = datafrequently[i].code;
									date = datafrequently[i].date;
									databarcode.push(code+','+date.slice(0,-3));
								}
								
								code = encryption.encrypt("{{$code}}", "{{$key_salt}}");
								var keyHash 	= CryptoJS.SHA256('{{$key_salt}}');

								axios
								.post("{{$update_data_local}}", {
									_token: _token,
									_data: databarcode,
									_toko: code
								},
								{
									headers: {
										Hash: keyHash.toString()
									}
								})
								.then(
									response => {
										var result = response.data;

										if(result.code==200){
											var data 		= result.data;
											var code_toko 	= "{{$code}}";
											var objkey 		= Object.keys(data);
											var n 			= objkey.length;

											var frequentlyname = 'frequently' + this.thisBrowserHash;

											var datafrequently = localStorage.getItem(frequentlyname) || false;
											if(datafrequently){
												datafrequently = JSON.parse( datafrequently.trim() );
											}else{
												datafrequently = [];
											}

											var found = false;
											var barcode;
											var barcodeyxz = '';
											var d 	= 	new Date();
											var dt 	= 	this.zeroPadding(d.getFullYear(),4) + '-' +
														this.zeroPadding(d.getMonth()+1) + '-' +
														this.zeroPadding(d.getDate()) + ' ' +
														this.zeroPadding(d.getHours()) + ':' +
														this.zeroPadding(d.getMinutes()) + ':' +
														this.zeroPadding(d.getSeconds());
											for (let i = 0; i < n; i++) {
												var barcode = objkey[i];
												var evalobj = eval('data.' + barcode);
													barcode = barcode.substr(1);
												var dataEncript = JSON.stringify(evalobj);
													dataEncript = code_toko + dataEncript + code_toko;
													dataEncript = encryption.encrypt(dataEncript, code_toko);
												localStorage.setItem(this.thisBrowserHash+barcode,dataEncript);

												if(barcodeyxz!==barcode){
													barcodeyxz 	= barcode;
													found		= datafrequently.some(el => el.code===barcode);
													if(found){
														let objIndex = datafrequently.findIndex((obj => obj.code===barcode));
														datafrequently[objIndex].date = dt;
													}
												}
											}
											localStorage.removeItem(frequentlyname);
											datafrequently.sort( this.compareValues('hits','desc') );
											datafrequently = JSON.stringify(datafrequently);
											localStorage.setItem(frequentlyname,datafrequently);
											Swal.fire('Success','Data berhasil di Update','success');
										}else{
											Swal.fire("Kesalahan",result.message,'error');
										}
									}
								).catch( function (error) {
									Swal.fire(
										'Kesalahan',
										'Gagal meminta data',
										'error'
									);
									console.log(error);
								});
							}
						});
					}
				}
			}else{
				Swal.fire('Maaf','Data Local Kosong','info');
			}
		},
		htmlCountDataLocal: function (){
			var frequentlyname = 'frequently' + this.thisBrowserHash;

			var datafrequently = localStorage.getItem(frequentlyname) || false;
			if(datafrequently){
				datafrequently = JSON.parse( datafrequently.trim() );
			}else{
				datafrequently = [];
			}

			return datafrequently.length;
        },
		btnLogout: function (){
			var fi = '_logout';
			if(_removeitem!=fi){
				_removeitem = fi;
				_counterremoveitem = 1;
			}else{
				_counterremoveitem++;
				if(_counterremoveitem>=3){
					_removeitem = '';
					_counterremoveitem = 0;
					Swal.fire({
						title: 'Logout',
						text: 'Yakin ingin logout app kasir ?',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Ya, logout !'
					}).then((result) => {
						if(result.value){
							window.location.href = "logout";
						}
					});
				}
			}
        }
	}
});

function showdropdown() {
	document.getElementById("myDropdown").classList.toggle("show-dropdown");
}

window.onclick = function(event) {
	if (!event.target.matches('.dropbtn')) {
		var dropdowns = document.getElementsByClassName("dropdown-content");
		var i;
		for (i = 0; i < dropdowns.length; i++) {
			var openDropdown = dropdowns[i];
			if (openDropdown.classList.contains('show-dropdown')) {
				openDropdown.classList.remove('show-dropdown');
			}
		}
	}
}