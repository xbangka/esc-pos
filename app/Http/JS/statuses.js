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
            {   data: 'modul',  name: 'module'},
            {   data: 'nama',   name: 'name'},
            {   data: 'key',    name: 'foreign_key', searchable: false},
            {   data: null,     name: 'bgcolor', searchable: false,
                render: function ( data, type, row ) {
                    return '<div class="badge badge-link shadow" style="border:1px solid #ddd;background:'+data.bg+'">&nbsp;</div> '+data.bg;
                }
            },
            {   data: null,     name: 'fontcolor', searchable: false,
                render: function ( data, type, row ) {
                    return '<div class="badge badge-link shadow" style="border:1px solid #ddd;background:'+data.font+'">&nbsp;</div> '+data.font;
                }
            },
            {   data: 'modify', name: 'updated_at', searchable: false},
            {   data: null,     name: 'id', searchable: false,
                render: function ( data, type, row ) {
                    var action_delete  = "deleteit('"+data.uuid+"','"+data.modul+"','"+data.nama+"')";
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
		var modul ;
		var name ;
		var key ;
		var bg ;
		var font ;
		/*console.log(data);*/
		if (!jQuery.isEmptyObject(data)) {
			uuid = data.uuid.trim();
			modul = data.modul.trim();
			name = data.nama.trim();
			key = data.key.trim();
			bg = data.bg.trim();
			font = data.font.trim();
			
		}else{
			data = $(this).data('bind');
			uuid = data.uuid;
			modul = data.modul;
			name = data.nama;
			key = data.key;
			bg = data.bg;
			font = data.font;

			uuid = uuid.trim();
			code = code.trim();
			name = name.trim();
			key  = key.trim();
			bg   = bg.trim();
			font = font.trim();
		}
		app.uuidedit = uuid;
		app.modul = modul;
		app.name = name;
		app.key = key;
		app.bg = bg;
		app.font = font;
		app.labl = 'EDIT';
	});
});


function deleteit(uuid,modul,nama){
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
				text: 'Modul '+ modul+ ', ' +nama+', yakin Anda ingin menghapusnya ?',
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
		modul : '',
		name : '',
		key : '',
		bg : '',
		font : '',
		labl: '',
		loading: false
	},
	methods:
	{
		addNew: function (){
            this.btnadd = false;
            this.showform = true;
            this.uuidedit = '';
            this.modul = '';
            this.name = '';
            this.key = '';
            this.bg = '';
            this.font = '';
            this.labl = 'BARU';
		},
		cancel: function (){
            this.btnadd = true;
            this.showform = false;
		},
		save: function (){
			if( this.modul=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Kode Kosong</h4><span class="mt-5">Kode harus di isi</span>'
				});
				return false;
			}
			if( this.name=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Nama Kosong</h4><span class="mt-5">Nama harus di isi</span>'
				});
				return false;
			}
			if( this.key=='' ){
				Swal.fire({
					html: _iconsvgwarning+'<h4 class="mt-4">Key Kosong</h4><span class="mt-5">Key harus di isi</span>'
				});
				return false;
			}
			this.loading = true;
			return this.senddata();
        },
        senddata: function (){
			let encryption 	= new Encryption();
			var uuidHash 	= (this.uuidedit!=='') ? CryptoJS.SHA256(this.uuidedit) : '';
			var _q 	=   {
                            modul:this.modul,
                            name:this.name,
                            key:this.key,
                            bg:this.bg,
                            font:this.font,
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
        }
	}
})