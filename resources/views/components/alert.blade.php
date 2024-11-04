<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>	
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($message = Session::get('msg'))
    <div class="alert alert-{{$message['status'] ? "info":"danger"}} alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message['msg'] }}</strong>
    </div>             
    @endif
</div>