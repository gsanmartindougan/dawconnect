<div class="col-12 card">
    <div class="col-12 card-body">
        <div class="table-responsive">
            <table id="avisos_table" class="table table-striped col-12 w-100 data_table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results['avisos'] as $aviso)
                        <tr>
                            <td><a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                                href="{{ route('avisos.show', $aviso->id) }}">{{ $aviso->title }}</a></td>
                            <td>{{ ($aviso->created_at)->format('d-m-Y H:i') }}</td>
                            <td class="text-end">
                                @if ($aviso->mod_id == auth()->user()->id)
                                    <a href="{{ route('avisos.edit', $aviso->id) }}"
                                        class="h-6 w-6 text-red-600"><x-antdesign-edit-o /></a>
                                    <a class="h-6 w-6 text-red-600 text-danger" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal{{ $aviso->id }}">
                                        <x-antdesign-delete-o /></a>
                                    @include('pages.avisos.modal.borrar')
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

