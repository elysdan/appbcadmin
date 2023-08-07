@extends('admin.layouts.master')

@section('content')


<div class="page-header page-header-light shadow">
				
			<table class='table'>
				  <thead>
					<tr>
							<th>Semana</th>
							<th style='text-align:right'>Depositos</th>
							<th style='text-align:right'>Comisiones</th>
							<th style='text-align:right'>Retiros</th>
							<th style='text-align:right'>Re-inversión</th>
							<th style='text-align:right'>Participaciones</th>
							<th style='text-align:right'>Membresías</th>
					<tr>
				   <thead>
				   <tbody>
					@foreach($tabla as $data)
							<tr>	
								<td>{{$data->fecha}}</td>
								<td style='text-align:right'>{{@round($data->depositos,2)}} USD</td>
								<td style='text-align:right'>{{@round($data->comisiones,2)}} USD</td>
								<td style='text-align:right'>{{@round($data->retiros  * -1,2)}} USD</td>
								<td style='text-align:right'>{{@round($data->reinversion ,2)}} USD</td>
								<td style='text-align:right'>{{@round($data->participaciones ,2)}} USD</td>
								<td style='text-align:right'>{{@round($data->membresias ,2)}} USD</td>
							</tr>
					@endforeach
				   </tbody>
			</table>

					
</div>
   
@endsection
