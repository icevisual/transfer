<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
        <meta charset="utf-8">
        <script src="{{asset('js/jquery-1.9.1.min.js') }}"></script>
        <style>
        .thumb{
            display: inline-block;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            background: #ccc;
            margin: 5px;
        }
        .thumb img{
            width:100px;
        }
        .thumb p{
            margin:0;
        }
        .remove-X{
            width:100px;
        }
        </style>
        <script>
        $(function(){
            $('.remove-X').click(function(){
                var _container = $(this).parent().parent();
                var pc_id = _container.data('id');
                $.ajax({
                    'url' : '/removeUnwanted',
                    'dataType' : 'json',
                    'data' : {
                        'pc_id' : pc_id
                    },
                    'success' : function(d){
                        if(d.status == 200){
                            if(d.data.pc_id){
                                _container.data('id',d.data.pc_id);
                                _container.find('img').eq(0).attr('src',d.data.thumb)
                            }else{
                                alert('nothing');
                            }
                            
//                             window.location.reload();
                        }
                    }
                });
            });
        })
        </script>
    </head>
    <body>
        	@if ($info['total']>0)
							<div class="box-body box-page">
								<div class="col-sm-7">
									<div class="dataTables_info" id="example2_info" role="status"
										aria-live="polite">
										共 <b>{{$info['total']}}</b> 条记录 当前第 <b>{{$info['current_page']}}</b>
										页/共 <b>{{$info['last_page']}}</b> 页
									</div>
								</div>
								<div class="col-sm-6 pull-right">
									<div class="dataTables_paginate paging_simple_numbers"
										id="example2_paginate">
											{!!$info['render']!!}
										</div>
								</div>
							</div>
							@endif
    
        @foreach($info['list'] as $v)
         <div class="thumb" data-id="{{$v['pc_id']}}">
            <img src="{{$v['thumb']}}">
            <div>
            <span>{{$v['cn_name']}}</span> 
            @if($v['probably_name'])
            | <span>{{$v['probably_name']}}</span>
            @endif
            <p>h = {{$v['height']}} , w = {{$v['width']}}</p>
            <button class="remove-X">X</button>
            </div>
        </div>
        @endforeach
    </body>
</html>
