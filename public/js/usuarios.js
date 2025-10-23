
$(document).ready(function () {

    // Inicializa DataTable
    var table = $('#users-table').DataTable({
        ajax: {
            url: '/usuarios/listar',
            type: 'GET',
            dataSrc: function (json) {
                return json;
            }
        },
        columns: [
            { data: 'id' },
            { data: 'nome' },
            { data: 'email' },
            {
            data: 'colors',
                render: function (colors) {
                    if (!colors || colors.length === 0) return '-';
                    return colors.map(c => c.name).join(', ');
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                className: 'text-center',
                render: function (data) {
                    return `
                        <button class="btn btn-primary btn-sm btn-edit" data-id="${data.id}">Editar</button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${data.id}">Excluir</button>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });

    // Inicializa Selectize
    var selectColors = $('#colors').selectize({
        plugins: ['remove_button'],
        placeholder: 'Selecione uma ou mais cores'
    })[0].selectize;

    // Variáveis para controlar se é edição
    var editMode    = false;
    var editUserId  = null;

    // Abrir modal para adicionar usuário
    $('#btnAddUser').on('click', function () {
        editMode = false;
        editUserId = null;
        $('#formAddUser')[0].reset();
        selectColors.clear();
        $('#addUserLabel').text('Adicionar Usuário');
        $('#btnSaveUser').text('Salvar');
        $('#addUserModal').modal('show');
    });

    // Abrir modal para editar usuário
    $('#users-table').on('click', '.btn-edit', function () {
        editMode    = true;
        editUserId  = $(this).data('id');

        $.ajax({
            url: `/usuarios/${editUserId}/editar`,
            type: 'GET',
            success: function (response) {
                $('#nome').val(response.nome);
                $('#email').val(response.email);

                // Preenche cores selecionadas
                selectColors.clear();
                var colorIds = response.colors.map(c => c.id);
                selectColors.setValue(colorIds);

                $('#addUserLabel').text('Editar Usuário');
                $('#btnSaveUser').text('Atualizar');
                $('#addUserModal').modal('show');
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Não foi possível carregar os dados do usuário.'
                });
            }
        });
    });

    // Salvar ou atualizar usuário
    $('#btnSaveUser').on('click', function () {
        var formData = {
            nome:   $('#nome').val(),
            email:  $('#email').val(),
            colors: $('#colors').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        var url = editMode ? `/usuarios/${editUserId}/atualizar` : '/usuarios/salvar';
        var type = 'POST';

        $.ajax({
            url: url,
            type: type,
            data: formData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: editMode ? 'Usuário atualizado com sucesso!' : 'Usuário cadastrado com sucesso!',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        $('#addUserModal').modal('hide');
                        table.ajax.reload();
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let msg = Object.values(errors).map(e => e.join('<br>')).join('<br>');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção!',
                        html: msg,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao salvar usuário!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    console.error(xhr.responseText);
                }
            }
        });
    });

    // Excluir usuário
    $('#users-table').on('click', '.btn-delete', function () {
        var userId = $(this).data('id');
        Swal.fire({
            icon: 'warning',
            title: 'Tem certeza?',
            text: 'Esta ação não poderá ser desfeita!',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!'

        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/usuarios/${userId}/excluir`,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: 'Usuário excluído com sucesso.',
                            confirmButtonColor: '#28a745'
                        });
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível excluir o usuário.'
                        });
                    }
                });
            }
        });
    });

});
