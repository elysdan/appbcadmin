@extends('admin.layouts.master')

@push('script')

@endpush

@section('content')

<div class="row">

<div class="col-lg-12" style='margin:20px; margin-bottom:10px;'>
    
    <div class="card" style='margin-top:5px;'>

        <div class="table-responsive table-responsive-xl">
            <table class="table align-items-center">
                <thead>
                            <tr>
                                <th scope="col" class='text-center'>Id pago</th>
                                <th scope="col" class='text-center'>created_at</th>
                                <th scope="col" class='text-center'>Hash</th>
                            </tr>
                </thead>
                <tbody>
                    @forelse( $pago as $pagos )
                            <tr>
                                <td class='text-center'><a href="#">{{$pagos->id }}</a></td>
                                <td class='text-center'>{{ show_datetime($pagos->created_at) }}</td>
                                <td class='text-center'><a href='https://polygonscan.com/tx/{{$pagos->id_tx}}' target='_blank'> {{$pagos->id_tx}}</a></td> 
                            </tr>
                    @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                            </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            <nav aria-label="...">
                {{ $pago->appends($_GET)->links() }}
            </nav>
        </div>
        
    </div>
</div>
</div>

@endsection