@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                {{--                 <div class="card">
                    <div class="card-header">{{ __('Produtos') }}</div>
                    <div class="card-body">z
                        <!-- Produtos como botões -->
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-block mb-3">Monitor de incidentes</a>
                        <a href="{{ url('/produto1') }}" class="btn btn-primary btn-block mb-3">Produto 1</a>
                        <a href="{{ url('/produto2') }}" class="btn btn-primary btn-block mb-3">Produto 2</a>
                        <!-- Adicione mais produtos conforme necessário -->
                    </div>
                </div>
            </div> --}}
                <!-- Main Content -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">{{ __('Home') }}</div>
                        <div class="card-body">
                            <!-- Espaço reservado para futuras edições -->
                            <p>Bem-vindo à área de edição.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
