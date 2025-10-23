@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lista de Usuários</h2>
        <a class="btn btn-success" id="btnAddUser">Adicionar Usuário</a>
    </div>

    <table class="table table-striped table-hover mt-3" id="users-table">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Cores</th>
                <th style="text-align: center;">Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    @include('modals.add_user', ['colors' => $colors])

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
    <script src="{{ asset('js/usuarios.js') }}"></script>
@endpush
