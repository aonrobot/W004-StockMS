@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container main" >
    <h2>Adjust Stock</h2>
    <div class="col-md-12" style="margin-bottom: 30px;">
    </div>
    
    <div class="col-md-12">
    </div>
</div>

<script>
    $(document).ready(function(){
    })
    
</script>

@endsection

