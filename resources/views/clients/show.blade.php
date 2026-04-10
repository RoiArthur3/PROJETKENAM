@extends('layouts.app')
@section('title', 'Client - '.config('app.name','KENAM SERVICES'))
@section('content')
<div class="container-fluid"><h1 class="mb-4">Client #{{ $id ?? '' }}</h1></div>
@endsection
