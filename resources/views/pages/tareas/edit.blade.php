<div class="modal fade" id="edit_tarea" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header  text-center">
                <h3 class="modal-title">Editar Tarea</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body col-md-12">
                <form id="tareaForm">
                    <label for="titulo" class="fs-5"></label>
                    <input type="text" id="titulo" name="titulo" class="form-control mb-2"
                        value="{{ $tarea->title }}" required>
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mt-2">Editar</button>
                        <a class="btn btn-secondary mt-2" data-bs-dismiss="modal"
                            aria-label="Close">Cerrar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('custom-scripts')
    <script>
        document.getElementById('tareaForm').addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.showLoading()
            let formData = new FormData(this);
            //console.log(formData)
            axios.post('{{ route('tareas.update') }}', formData)
                .then(function(response) {
                    //console.log(response.data.postUrl)
                    //let postUrl = response.data.postUrl;
                    let mensaje = response.data.mensaje;
                    localStorage.setItem('mensaje', mensaje);
                    window.location.reload();
                })
                .catch(function(error) {
                    console.error(error);
                    console.error(response.data.postUrl);
                });
        });
    </script>
@endpush
