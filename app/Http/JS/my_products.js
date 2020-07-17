var _iconsvgloading = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <rect x="0" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="10" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="20" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"/></rect></svg>';
var _iconsvgwarning = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="80" height="80"><style type="text/css">* { fill: #ff5d00 }</style><path d="M19.64 16.36L11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg>';
var _counterremoveitem = 0;
var _token = "{{$csrf_token}}";
var _removeitem = "";
var dataTable = '';
var encryption 	= new Encryption();

$(document).ready(function() {
    var initcompleteDT  = false;

    dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
		sorting: [[ 3, "desc" ]],
        ajax: {
			url: "{{$getData}}",
			type: "GET",
			headers: { 'Accept-Dinamic-Key': '{{$key_salt}}' },
			dataSrc: function ( json ) {
                if (json.data.length==0) {
					var code_val = $('.dataTables_filter input').val();
						code_val = code_val.trim();
					if(code_val.length>=13 && !isNaN(code_val) ){
						Swal.fire({
							title: 'Tambahkan Baru',
							text: 'kode ' + code_val + ' tidak ada di database, apakah ingin di tambahkan ke database oleh anda?',
							icon: 'question',
							showCancelButton: true,
							confirmButtonText: 'Ya, tambahkan !'
						}).then((result) => {
							if(result.value){
								app.div.showTable = false,
								app.div.showDetail.display = false,
								app.div.showNewProduct.display = true
							}
						});
					}
				}
                return json.data;
            }
		},
        pagingType: "numbers",
        columns: [
            {   data: 'kode',   name: 'products.barcode'},
            {   data: 'nama',   name: 'products.full_name'},
            {   data: 'categories.name', name: 'categories.name', searchable: false},
            {   data: 'modify', name: 'products.updated_at', searchable: false},
            {   data: null,     name: 'products.id', searchable: false,
                render: function ( data, type, row ) {
                    return  '<button data-bind=\''+JSON.stringify(data)+'\' class="btn btn-outline-primary btn-sm showme"><i class="fa fa-binoculars"></i></button>';
                }
            }
        ],
        initComplete: function() {
            if(!initcompleteDT){
                initcompleteDT  = true;
                $("#dataTable_length").attr("class","dataTables_length mt-3");
                $("#dataTable_filter").attr("class","dataTables_filter mt-3");
                $("#dataTable_filter").append('<button class="btn btn-link"><i class="fa fa-history"></i></button>');
                $("#dataTable_info").parent().attr("class","col-md-2");
                $("#dataTable_paginate").parent().attr("class","col-md-10");
                $("#dataTable_filter>label>input").attr("class","form-control-sm form-control");
                $("#dataTable_length>label>select").attr("class","form-control-sm form-control");
            }
        }
	});
	
	$('#dataTable tbody').on('click', '.showme', function () { 
		app.div.showTable = false,
		app.div.showDetail.display = true,
		app.div.showNewProduct.display = false
		var data = dataTable.row( $(this).parents('tr') ).data();
		var uuid ;
		var code ;
		var name ;
		var snme ;
		var desc ;
		var cate ;
		/*console.log(data);*/
		if (!jQuery.isEmptyObject(data)) {
			uuid = data.uuid.trim();
			code = data.kode.trim();
			name = data.nama.trim();
			snme = data.nm.trim();
			desc = data.desc.trim();
			cate = data.categories.name.trim();
			
		}else{
			data = $(this).data('bind');
			uuid = data.uuid;
			code = data.kode;
			name = data.nama;
			snme = data.nm;
			desc = data.desc;
			cate = data.categories.name;

			uuid = uuid.trim();
			code = code.trim();
			name = name.trim();
			snme = snme.trim();
			desc = desc.trim();
			cate = cate.trim();
		}
		app.div.showDetail.uuid = uuid;
		app.div.showDetail.code = code;
		app.div.showDetail.name = name;
		app.div.showDetail.sname = snme;
		app.div.showDetail.category = cate;
		app.div.showDetail.description = desc;
	});

});

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		div: {
			showTable: true,
			showDetail:{
				display: false,
				svgHTML: '',
				uuid: '',
				code:'',
				name:'',
				sname:'',
				description:'',
				category:'',
				formShow:{
					display: false,
					uuid: '',
					id_unit: '',
					unit: '',
					price: 0,
					discounts:[],
					loading: false
				},
				variations: [],
				priceReferences: [],
				loadingvariations:true
			},
			showNewProduct:{
				display:false,
				uuid: '',
				code:'',
				name:'',
				sname:'',
				description:'',
				category:'',
				loading: false
			}
		},
		categories: {{$categories}},
		units: {{$units}},
		loading: _iconsvgloading
    },
    computed:{
		validationCategory: function (){
			if(this.div.showNewProduct.display){
				var categories	= this.categories;
				var n_arr   	= categories.length;
				var category  	= this.div.showNewProduct.category;
				for(let i = 0; i < n_arr; i++) {
					if(category===categories[i].code){
						return categories[i].code;
					}
				}
				return false;
			}
			return false;
		},
		currentBarcode: function (){
			return this.div.showDetail.code;
		}
    },
    watch: {
        currentBarcode: function (val) {
            if(val!=''){
				this.div.showDetail.svgHTML = '';
				this.div.showDetail.priceReferences = [];
                axios
                .get("{{$getBarcodeImage}}?"+val)
                .then(
                    response => {
                        this.div.showDetail.svgHTML = response.data;
                    }
                ).catch( function (error) {
                    this.div.showDetail.svgHTML = '';
                    console.log(error);
				});

				this.div.showDetail.variations = [];
				this.div.showDetail.loadingvariations = true;

				var uuidHash 	= CryptoJS.SHA256(this.div.showDetail.uuid);
				var _q 	= {uuid:this.div.showDetail.uuid};
					_q 	= JSON.stringify(_q);
					_q 	= encryption.encrypt(_q,"{{$key_salt}}");
					
				axios
                .post("{{$getPriceVariations}}",
					{
						_token: _token,
						_q: _q
					},
					{
						headers: {
							_hash: uuidHash.toString()
						}
					}
				)
                .then(
                    response => {
						if(this.IsJsonString('"'+response.data+'"')){
							this.div.showDetail.variations = response.data;
							this.div.showDetail.loadingvariations = false;
							this.getPriceRef(val);
						}else{
							this.div.showDetail.variations = [];
							this.div.showDetail.loadingvariations = false;
						}
                    }
                ).catch( function (error) {
					this.div.showDetail.variations = [];
					this.div.showDetail.loadingvariations = false;
                    console.log(error);
				});
            }else{
				this.div.showDetail.svgHTML = '';
				this.div.showDetail.variations = [];
				this.div.showDetail.loadingvariations = false;
            }
        }
    },
	methods:
	{
		addNew: function (){
            this.div.showTable = false;
            this.div.showDetail.display = false;
            this.div.showNewProduct.display = true;
            this.div.showNewProduct.uuid = '';
            this.div.showNewProduct.code = '';
			this.div.showNewProduct.name = '';
			this.div.showNewProduct.sname = '';
			this.div.showNewProduct.category = '';
			this.div.showNewProduct.description = '';
			this.div.showNewProduct.loading = false;
		},
		formPrice: function (v=false){
            this.div.showDetail.formShow.display = true;
			this.div.showDetail.formShow.uuid = (v) ? v.uuid:'';
			this.div.showDetail.formShow.id_unit = (v) ? this.getIdUnit(v.unit):'';
			this.div.showDetail.formShow.unit = (v) ? v.unit:'';
			this.div.showDetail.formShow.price = (v) ? v.price:0;
			this.div.showDetail.formShow.discounts = (v) ? v.discounts:[];
		},
		cancelPrice: function (){
			this.div.showDetail.formShow.display = false;
			this.div.showDetail.formShow.uuid = '';
		},
		backToList: function (){
			this.div.showTable = true;
			this.div.showDetail.display = false;
			this.div.showNewProduct.display = false;
			this.div.showNewProduct.uuid = '';
			this.cancelPrice();
		},
		cekNewBarcode: function (){
			var barcode = this.div.showNewProduct.code;
			if(barcode.length!=0){
				Swal.fire({
					html: _iconsvgloading+'<h5 class="mt-4">Sedang Mencari...</h5>',
					showConfirmButton: false,
					allowOutsideClick: false
				});
				var codeHash 	= CryptoJS.SHA256(barcode);
				var _q 	= {code:barcode};
					_q 	= JSON.stringify(_q);
					_q 	= encryption.encrypt(_q,"{{$key_salt}}");
					
				axios
                .post("{{$cekNewBarcode}}",
					{
						_token: _token,
						_q: _q
					},
					{
						headers: {
							_hash: codeHash.toString()
						}
					}
				)
                .then(
                    response => {
						Swal.close();
						var data = response.data;
						var newP = this.div.showNewProduct;
						newP.uuid = (typeof data.uuid !== 'undefined') ?  data.uuid :'';
						newP.name = (typeof data.fname !== 'undefined') ? data.fname : '';
						newP.sname = (typeof data.sname !== 'undefined')? data.sname : '';
						newP.category = (typeof data.cate !== 'undefined') ? data.cate : '';
						newP.description = (typeof data.description !== 'undefined') ? data.description : '';
						if((typeof data.source !== 'undefined') && data.source=='null') {
							Swal.fire(
								'Nama Tidak ditemukan',
								'Nama tidak ditemukan, silakan isi nama produk lengkap sendiri',
								'info'
							)
						}
                    }
                ).catch( function (error) {
                    console.log(error);
				});
			}
			return ;
        },
		saveNewProdact: function (){
			var newP = this.div.showNewProduct;
			if( newP.code.length==0 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Kode Kosong</h4><span class="mt-5">Kode harus di isi</span>'
				});
				return false;
			}else if( newP.code.length<=7 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Kode</h4><span class="mt-5">Kode yang di ketik terlalu pendek,<br/>pakai kode EAN8 atau EAN13</span>'
				});
				return false;
			}else if( newP.code.length>=14 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Kode</h4><span class="mt-5">Kode yang di ketik terlalu panjang,<br/>pakai kode EAN8 atau EAN13</span>'
				});
				return false;
			}
			if( newP.name=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Kosong</h4><span class="mt-5">Nama harus di isi</span>'
				});
				return false;
			}else if( newP.name.length<=4 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Kategori</h4><span class="mt-5">Nama yang di ketik terlalu pendek,<br/>min:5</span>'
				});
				return false;
			}
			if( newP.sname=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Pendek</h4><span class="mt-5">Nama Pendek harus di isi</span>'
				});
				return false;
			}else if( newP.sname.length>=21 ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Pendek</h4><span class="mt-5">Nama yang di ketik terlalu panjang,<br/>max:20 char</span>'
				});
				return false;
			}
			if( !this.validationCategory ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Kategori</h4><span class="mt-5">Status mohon di isi dengan benar</span>'
				});
				return false;
            }
			newP.loading = true;
			return this.sendDataNewProduct();
        },
        sendDataNewProduct: function (){
			var newP = this.div.showNewProduct;
			var uuidHash 	= (newP.uuid!=='') ? CryptoJS.SHA256(newP.uuid) : '';
			var _q 	= {
						code:newP.code,
						name:newP.name,
						snme:newP.sname,
						desc:newP.description,
						cate:this.validationCategory,
						uuid:newP.uuid};
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
				.post("{{$newData}}",
					{
						_token: _token,
						_q: _q
					},
					{
						headers: {
							_hash: uuidHash.toString()
						}
					}
				)
				.then(
					response => {
						var result = response.data;
						if(result.indexOf('*OK*')!='-1'){
							this.div.showTable = false,
							this.div.showDetail.display = true,
							this.div.showNewProduct.display = false
							var res = result.split("*OK*");
							this.div.showDetail.uuid = res[1];
							this.div.showDetail.code = newP.code;
							this.div.showDetail.name = newP.name;
							this.div.showDetail.sname = newP.sname;
							this.div.showDetail.category = newP.category;
							this.div.showDetail.description = newP.description;
							this.div.showDetail.svgHTML = '';
						}else{
							Swal.fire({
								html: _iconsvgwarning+'<h4 class="mt-4">Gagal</h4><span class="mt-5">'+result+'</span>'
							});
						}
						newP.loading = false;
					}
				).catch( function (error) {
					Swal.fire(
						'Failed',
						'Gagal mengirim data',
						'error'
                    )
                    this.loading = false;
					console.log(error);
				});
		},
		savePrice: function (){
			var formPrice = this.div.showDetail.formShow;
			if( formPrice.price<=0 || formPrice.price=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Harga Nol ?</h4><span class="mt-5">Harga tidak boleh nol</span>'
				});
				return false;
			}
			if( formPrice.id_unit=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Unit</h4><span class="mt-5">Nama satuan unit tidak boleh kosong</span>'
				});
				return false;
            }
			formPrice.loading = true;
			return this.sendDataPrice();
        },
        sendDataPrice: function (){
			var detail = this.div.showDetail;
			var formPrice = detail.formShow;
			var uuidHash = (formPrice.uuid!=='') ? CryptoJS.SHA256(formPrice.uuid) : '';
			var _q 	= { unit:formPrice.id_unit,
						price:formPrice.price,
						product:detail.uuid,
						uuid:formPrice.uuid};
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
				.post("{{$saveDataPrice}}",
					{
						_token: _token,
						_q: _q
					},
					{
						headers: {
							_hash: uuidHash.toString()
						}
					}
				)
				.then(
					response => {
						detail.variations = response.data;
						Swal.fire(
							'Berhasil',
							'Data berhasil di ' +((formPrice.uuid=='')?'simpan':'update'),
							'success'
						);
						formPrice.loading = false;
					}
				).catch( function (error) {
					detail.variations = [];
					Swal.fire('Failed','Gagal mengirim data','error')
                    formPrice.loading = false;
					console.log(error);
				});
		},
		enablePrice: function (uuid){
			return this.changeStatusPrice(uuid,1);
		},
		disablePrice: function (uuid){
			return this.changeStatusPrice(uuid,2);
		},
		changeStatusPrice: function (uuid,stt){
			var uuidHash = CryptoJS.SHA256(uuid);
			var _q 	= { stt:stt, uuid:uuid };
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
			.post("{{$changeStatusPrice}}",
				{
					_token: _token,
					_q: _q
				},
				{
					headers: {
						_hash: uuidHash.toString()
					}
				}
			)
			.then(
				response => {
					this.div.showDetail.variations = response.data;
				}
			).catch( function (error) {
				this.div.showDetail.variations = [];
				Swal.fire('Failed','Gagal ganti status data','error')
				console.log(error);
			});
        },
		enableDiscount: function (uuid){
			return this.changeStatusDiscount(uuid,1);
		},
		disableDiscount: function (uuid){
			return this.changeStatusDiscount(uuid,2);
		},
		changeStatusDiscount: function (uuid,stt){
			var uuidHash = CryptoJS.SHA256(uuid);
			var _q 	= { stt:stt, uuid:uuid };
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
			.post("{{$changeStatusDiscount}}",
				{
					_token: _token,
					_q: _q
				},
				{
					headers: {
						_hash: uuidHash.toString()
					}
				}
			)
			.then(
				response => {
					this.div.showDetail.variations = response.data;
					var len = response.data.length;
					var uuidprice = this.div.showDetail.formShow.uuid;
					for (let i = 0; i < len; i++) {
						if(uuidprice==response.data[i].uuid){
							this.div.showDetail.formShow.discounts = [];
							this.div.showDetail.formShow.discounts = response.data[i].discounts;
							break;
						}
					}
				}
			).catch( function (error) {
				this.div.showDetail.variations = [];
				Swal.fire('Failed','Gagal ganti status data','error')
				console.log(error);
			});
        },
		addNewDiscount: function (){
			let today = new Date();
			let dd = today.getDate();
			let mm = today.getMonth()+1; 
			const yyyy = today.getFullYear();
			let yyyx = yyyy+1;

			let hh = today.getHours();
			let ii = today.getMinutes();

			if(ii<10) ii=`0${ii}`;

			if(hh<10) hh=`0${hh}`;

			if(dd<10) dd=`0${dd}`;

			if(mm<10) mm=`0${mm}`;

			today = `${yyyy}-${mm}-${dd} ${hh}:${ii}:00`;
			var next = `${yyyx}-${mm}-${dd} ${hh}:${ii}:00`;
			var addnew = {
				mode : 'NEW',
				uuid : '',
				event_name : 'DISCOUNT',
				value : 1000,
				value_type : 1,
				condition_qty_from : 1,
				condition_qty_to : 1000,
				start_date : today,
				end_date  : next
			};
			this.div.showDetail.formShow.discounts.push(addnew); 
		},
		discountEditMode: function (uuid){
			var discounts = this.div.showDetail.formShow.discounts;
			var strtemp = JSON.stringify(discounts);
			this.div.showDetail.formShow.discounts = [];
			var strreal = JSON.parse(strtemp);

			for (let i = 0; i < strreal.length; i++) {
				var arr = strreal[i];
				if(arr.uuid==uuid){
					arr.mode = 'EDIT';
					break;
				}	
			}
			this.div.showDetail.formShow.discounts = strreal;
		},
		cancelEditDiscount: function (uuid){
			var discounts = this.div.showDetail.formShow.discounts;
			var strtemp = JSON.stringify(discounts);
			this.div.showDetail.formShow.discounts = [];
			var strreal = JSON.parse(strtemp);

			let idx = strreal.findIndex((e) => e.mode == 'NEW');
			if(idx!=-1) strreal.splice(idx, 1);

			for (let i = 0; i < strreal.length; i++) {
				var arr = strreal[i];
				if(arr.uuid==uuid){
					delete arr.mode;
					break;
				}
			}
			this.div.showDetail.formShow.discounts = strreal;
		},
		saveDataDiscount: function (uuid){
			var discounts = this.div.showDetail.formShow.discounts;
			var discTemps = '';
			for (let i = 0; i < discounts.length; i++) {
				var discount = discounts[i];
				if(discount.uuid==uuid){
					discTemps = discount;
					break;
				}	
			}

			if(discTemps!=''){
				var uuidHash = (discTemps.uuid!=='') ? CryptoJS.SHA256(discTemps.uuid) : '';
				var _q 	= { uuid_price:this.div.showDetail.formShow.uuid,
							name:discTemps.event_name,
							value:discTemps.value,
							type:discTemps.value_type,
							from:discTemps.condition_qty_from,
							conto:discTemps.condition_qty_to,
							start:discTemps.start_date,
							end:discTemps.end_date,
							uuid:discTemps.uuid};
					_q 	= JSON.stringify(_q);
					_q 	= encryption.encrypt(_q,"{{$key_salt}}");
				axios
					.post("{{$saveDataDiscount}}",
						{
							_token: _token,
							_q: _q
						},
						{
							headers: {
								_hash: uuidHash.toString()
							}
						}
					)
					.then(
						response => {
							this.div.showDetail.variations = response.data;
							var len = response.data.length;
							var uuidprice = this.div.showDetail.formShow.uuid;
							for (let i = 0; i < len; i++) {
								if(uuidprice==response.data[i].uuid){
									this.div.showDetail.formShow.discounts = [];
									this.div.showDetail.formShow.discounts = response.data[i].discounts;
									break;
								}
							}
						}
					).catch( function (error) {
						this.div.showDetail.variations = [];
						Swal.fire('Failed','Gagal mengirim data','error')
						formPrice.loading = false;
						console.log(error);
					});
			}
		},
		discountHitsModal: function (arrDiscounts=[]){
			var n_arr = arrDiscounts.length;
			if(n_arr>=1){
				var html = ''; 
				for (let i = 0; i < n_arr; i++) {
					var discountr = arrDiscounts[i];
					if(discountr.status==1){
						if(parseFloat(discountr.value)>0){
							if(discountr.value_type==1){
								html += '<span class="badge badge-danger badge-pill">'+
											'-'+ this.numThousans(parseFloat(discountr.value))
										+'</span><br><b>'+discountr.event_name+'</b>';
										if(discountr.condition_qty_from>=2){
											html += '<br>min: '+discountr.condition_qty_from+' | max: '+discountr.condition_qty_to;
										}
								html += '<br>'+this.formatDateIna(discountr.start_date)+' - '+this.formatDateIna(discountr.end_date)+'<br>';
							}else{
								html += '<span class="badge badge-danger badge-pill">'+
											'-'+ parseFloat(discountr.value) + ' %'
										+'</span><br><b>'+discountr.event_name+'</b>';
										if(discountr.condition_qty_from>=2){
											html += '<br>min: '+discountr.condition_qty_from+' | max: '+discountr.condition_qty_to;
										}
								html += '<br>'+this.formatDateIna(discountr.start_date)+' - '+this.formatDateIna(discountr.end_date)+'<br>';
							}
						}
					}
				}
				return html;
			}else{
				return '';
			}
		},
		getPriceRef: function (code){
			this.div.showDetail.priceReferences = [{source:_iconsvgloading,price:''}]
			axios
			.get("{{$getPriceRef}}?"+code)
			.then(
				response => {
					var data = response.data;
					if(data.code==200) this.div.showDetail.priceReferences = data.data.prices;
				}
			).catch( function (error) {
				console.log(error);
			});
		},
		printBarcode: function (){
			Swal.fire({
				html: _iconsvgloading+'<h3 class="mt-4">Processing</h3>',
				showConfirmButton: false,
				allowOutsideClick: false
			});
			axios
			.get("{{$printBarcode}}?code="+this.div.showDetail.code)
			.then(
				response => {
					location.href = "my.bluetoothprint.scheme://http://0.0.0.0:8080/?q="+response.data;
					Swal.close();
				}
			).catch( function (error) {
				Swal.fire('Failed','Gagal ganti status data','error')
				console.log(error);
			});
        },
		getIdUnit: function (code){
			if(this.div.showDetail.formShow.display==true){
				var units = this.units;
				var n_arr = units.length;
				for(let i = 0; i < n_arr; i++) {
					if(code===units[i].code){
						return units[i].id;
					}
				}
			}
			return '';
        },
		formatDateIna: function (date){
			var bln = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']
			var time = date.substr(11,5);
			var date = date.substr(0,10);
			var res = date.split('-');
			var yy = res[0];
			var mm = bln[ parseInt(res[1]) ];
			var dd = res[2];
			return dd + '.' + mm + '.' + yy + ' ' + time;
        },
        numThousans: function (x=0){
			x = x.toString().replace('.', ',');
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		},
		IsJsonString: function (str){
			try {
				JSON.parse(str);
			} catch (e) {
				return false;
			}
			return true;
		}
	}
})