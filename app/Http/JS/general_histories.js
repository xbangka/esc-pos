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
        sorting: [[ 6, "desc" ]],
        ajax: {
			url: "{{$getData}}",
			type: "GET",
			headers: { 'Accept-Dinamic-Key': '{{$key_salt}}' },
		},
        pagingType: "numbers",
        columns: [
            {   data: 'user',       name: 'users.firstname'},
            {   data: 'username',   name: 'users.username'},
            {   data: 'module',     name: 'module_name'},
            {   data: 'column',     name: 'column'},
            {   data: 'old',        name: 'old_value'},
            {   data: 'new',        name: 'new_value'},
            {   data: 'created',    name: 'created_at', searchable: false}
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
		var code ;
		var name ;
		if (!jQuery.isEmptyObject(data)) {
			uuid = data.uuid.trim();
			code = data.kode.trim();
			name = data.nama.trim();
			
		}else{
			data = $(this).data('bind');
			uuid = data.uuid;
			code = data.kode;
			name = data.nama;

			uuid = uuid.trim();
			code = code.trim();
			name = name.trim();
		}
		app.uuidedit = uuid;
		app.code = code;
		app.name = name;
		app.labl = 'EDIT';
	});
});


var app = new Vue({
	el: '.{{$appid}}',
	data: {
		btnadd: true,
		showform: false,
		uuidedit: '',
		code: '',
		name: '',
		labl: '',
		loading: false
	},
	methods:
	{
		addNew: function (){
            this.btnadd = false;
            this.showform = true;
            this.uuidedit = '';
            this.code = '';
            this.name = '';
            this.labl = 'BARU';
		}
	}
})