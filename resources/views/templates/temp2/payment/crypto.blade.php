@extends(activeTemplate().'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Preview</div>
                <div class="card-body text-center">
                    <h3 class="text-color"> @lang('PLEASE SEND EXACTLY') <span style="color: green"> {{ $data->amount }}</span> {{$data->currency}}</h3>
                    <h5>@lang('TO') <span style="color: green"> {{ $data->sendto }}</span></h5>
                    <img src="{{$data->img}}" alt="">
                    <h4 class="text-color bold">@lang('SCAN TO SEND')</h4>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')

@stop
