@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
                </div>
            </div>
            <div>
                <div id="session-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                
                @if (session('success'))
                <div id="session-message" data-message="{{ session('success') }}"> deleted</div>
                @endif
                {!! $dataTable->table() !!}
            </div>
        </div>
    </section>
@endsection

@section('customjs')
   
    {!! $dataTable->scripts() !!}

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    
        $(document).ready(function() {
            var flashMessage = sessionStorage.getItem('flashMessage');
            if (flashMessage) {
                $('#session-message').text(flashMessage).show();
                sessionStorage.removeItem('flashMessage');
                setTimeout(function() {
                    $('#session-message').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 2000); // 2 seconds delay
            }
        });

        function deleteProduct(id) {
            if (confirm('Are you sure?')) {
                axios.delete('{{ route('products.delete', ':id') }}'.replace(':id', id))
                .then(function(response) {
                    console.log(response.data);
                    if (response.data.status) {
                        sessionStorage.setItem('flashMessage', response.data.message);
                        window.location.href = response.data.redirect;
                    }
                })
                .catch(function(error) {
                    console.error('There was an error!', error);
                });
            }
        }
        
    </script>
@endsection
