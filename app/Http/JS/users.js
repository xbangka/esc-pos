var _iconsvgloading = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve"> <rect x="0" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="10" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite" /> </rect> <rect x="20" y="0" width="4" height="10" fill="#3f6ad8"> <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"/></rect></svg>';
var _iconsvgwarning = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="80" height="80"><style type="text/css">* { fill: #ff5d00 }</style><path d="M19.64 16.36L11.53 2.3A1.85 1.85 0 0 0 10 1.21 1.85 1.85 0 0 0 8.48 2.3L.36 16.36C-.48 17.81.21 19 1.88 19h16.24c1.67 0 2.36-1.19 1.52-2.64zM11 16H9v-2h2zm0-4H9V6h2z"/></svg>';
var _counterremoveitem = 0;
var _token = "{{$csrf_token}}";
var _removeitem = "";
var dataTable = '';

$(document).ready(function() {
	var initcompleteDT  = false;
	

    dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        sorting: [[ 5, "desc" ]],
        ajax: {
			url: "{{$getData}}",
			type: "GET",
			headers: { 'Accept-Dinamic-Key': '{{$key_salt}}' },
		},
        pagingType: "numbers",
        columns: [
            {   data: 'nama',       name: 'users.firstname'},
            {   data: 'alias',      name: 'users.nameshow'},
            {   data: 'user',       name: 'users.username'},
            {   data: 'stores.name',name: 'stores.name'},
            {   data: null,         name: 'statuses.name',
                render: function ( data, type, row ) {
                    return  '<div class="badge badge-light" style="background:'+data.statuses.bgcolor+';color:'+data.statuses.fontcolor+'">'+data.statuses.name+'</span>';
                }
            },
            {   data: 'modify', name: 'users.updated_at', searchable: false},
            {   data: null,     name: 'users.lastname',
                render: function ( data, type, row ) {
                    var action_delete  = "deleteit('"+data.uuid+"','"+data.nama+"')";
                    return  '<button data-bind=\''+JSON.stringify(data)+'\' class="btn btn-outline-primary btn-sm edit"><i class="fa fa-paint-brush"></i></button> '+
                            '<button onclick="'+action_delete+'" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button>';
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
	
	$('#dataTable tbody').on('click', '.edit', function () { 
		app.addNew();
		var data = dataTable.row( $(this).parents('tr') ).data();
		var uuid ;
		var title ;
		var fname ;
        var lname ;
        var alias ;
        var user ;
        var email ;
        var phone ;
        var status ;
        var store ;
        
		if (!jQuery.isEmptyObject(data)) {
			uuid = data.uuid.trim();
			title = data.title.trim();
			fname = data.fname.trim();
			lname = data.lname.trim();
			alias = data.alias.trim();
			user = data.user.trim();
			email = data.mail.trim();
			phone = data.telp.trim();
			status = data.statuses.name.trim();
			store = data.stores.uuid.trim();
			
		}else{
			data = $(this).data('bind');
			uuid = data.uuid;
			title = data.title;
			fname = data.fname;
			lname = data.lname;
			alias = data.alias;
			user = data.user;
			email = data.mail;
            phone = data.telp;
            status = data.statuses.name;
			store = data.stores.uuid;

			uuid = uuid.trim();
			title = title.trim();
			fname = fname.trim();
			lname = lname.trim();
			alias = alias.trim();
			user = user.trim();
			email = email.trim();
            phone = phone.trim();
            status = status.trim();
			store = store.trim();
		}
		app.uuidedit = uuid;
		app.title = title;
		app.fname = fname;
		app.lname = lname;
		app.alias = alias;
		app.username = user;
		app.email = email;
        app.phone = phone;
        app.status = status;
		app.store = store;
		app.labl = 'EDIT';
	});
});


function deleteit(uuid,nama){
	var fi = 'delete'+uuid;
	if(_removeitem!=fi){
		_removeitem = fi;
		_counterremoveitem = 1;
	}else{
		_counterremoveitem++;
		if(_counterremoveitem>=3){
			_removeitem = '';
			_counterremoveitem = 0;
			Swal.fire({
				title: 'Konfirmasi Penghapusan',
				text: nama+', yakin Anda ingin menghapusnya ?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: 'HAPUS',
				cancelButtonText: 'BATAL'
			}).then((result) => {
				if(result.value){
					Swal.fire({
						html: _iconsvgloading+'<h5 class="mt-4">Memproses penghapusan...</h5>',
						showConfirmButton: false,
						allowOutsideClick: false
					});
					app.deleteit(uuid);
				}
			});
		}
	}
}

var app = new Vue({
	el: '.{{$appid}}',
	data: {
		btnadd: true,
		showform: false,
		uuidedit: '',
		title: '',
		fname: '',
		lname: '',
		alias: '',
		username: '',
		email: '',
		phone: '',
        store: '',
        labl: '',
		loading: false,
        stores:{{$stores}},
        status: '',
        statuses:{{$statuses}}
    },
    computed:{
		validationStatus: function (){
			if(this.labl!=''){
				var statuses= this.statuses;
				var n_arr   = statuses.length;
				var status  = document.getElementById('r1').value;
				for(let i = 0; i < n_arr; i++) {
					if(status===statuses[i].name){
						return statuses[i].foreign_key;
					}
				}
				return false;
			}
			return false;
        }
    },
	methods:
	{
		addNew: function (){
            this.btnadd = false;
            this.showform = true;
            this.uuidedit = '';
            this.title = '0';
            this.fname = '';
            this.lname = '';
            this.alias = '';
            this.username = '';
            this.email = '';
            this.phone = '';
            this.store = '';
            this.status = '';
            this.labl = 'BARU';
		},
		cancel: function (){
            this.btnadd = true;
            this.showform = false;
		},
		save: function (){
			if( this.fname=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Depan Kosong</h4><span class="mt-5">Nama Depan harus di isi</span>'
				});
				return false;
			}
			if( this.lname=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Belakang Kosong</h4><span class="mt-5">Nama Belakang harus di isi</span>'
				});
				return false;
			}
			if( this.alias=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Alias Kosong</h4><span class="mt-5">Nama Panggilan harus di isi<br>max: 6 char</span>'
				});
				return false;
			}
			if( this.username=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Username Kosong</h4><span class="mt-5">Username harus di isi</span>'
				});
				return false;
			}
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
            if( this.phone!='' && !this.phone.match(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im) ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nomor Telpon</h4><span class="mt-5">Nomor telpon tidak benar</span>'
				});
				return false;
            }
            if( !this.validationStatus ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Status</h4><span class="mt-5">Status mohon di isi dengan benar</span>'
				});
				return false;
            }
			this.loading = true;
			return this.senddata();
        },
        senddata: function (){
			let encryption 	= new Encryption();
			var uuidHash 	= (this.uuidedit!=='') ? CryptoJS.SHA256(this.uuidedit) : '';
			var _q 	= {
                        title:this.title,
                        fname:this.fname,
                        lname:this.lname,
                        alias:this.alias,
                        username:this.username,
                        email:this.email,
                        phone:this.phone,
                        store:this.store,
                        stts:this.validationStatus,
                        uuid:this.uuidedit
                    };
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
				.post("{{$insertupdatedelete}}",
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
							Swal.fire(
								'Berhasil',
								'Data berhasil di ' +((app.uuidedit=='')?'simpan':'update'),
								'success'
                            )
                            dataTable.ajax.reload( null, false );
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
                    this.loading = false;
					console.log(error);
				});
		},
		deleteit: function (uuid){
			let encryption 	= new Encryption();
			var uuidHash 	= CryptoJS.SHA256(uuid);
			var _q 	= {uuid:uuid};
				_q 	= JSON.stringify(_q);
				_q 	= encryption.encrypt(_q,"{{$key_salt}}");
			axios
				.post("{{$insertupdatedelete}}",
					{
						_token: _token,
						_delete: true,
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
							Swal.fire(
								'Berhasil',
								'Data berhasil di hapus',
								'success'
                            );
                            dataTable.ajax.reload( null, false );
						}else{
							Swal.fire({
								html: _iconsvgwarning+'<h4 class="mt-4">Gagal</h4><span class="mt-5">'+result+'</span>'
							});
						}
					}
				).catch( function (error) {
					Swal.fire(
						'Failed',
						'Gagal mengirim data',
						'error'
                    )
					console.log(error);
				});
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
	}
})