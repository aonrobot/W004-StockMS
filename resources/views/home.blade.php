@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<div class="container fixed-bottom main" >
        <!-- This is for Sub Menu -->
        <div class="row">
            <div class="col-md-6 m-t-50">
                <h4 class="title">Dashboard </h4>
            </div>
            <div class="col-md-6 m-t-50 text-right">
                <h3>Right</h3>
            </div>

            <div class="col-md-12">
                <div class="panel panel-default card">
                    <div class="panel-body">
                        <h2>Body</h2>
                    </div>
                </div>
            </div>
        </div>

        <footer class="cm-footer">
            <span class="pull-left">This is Footer</span>
        </footer>
</div>

<script>
</script>

@endsection

@section('page_script')

@endsection
