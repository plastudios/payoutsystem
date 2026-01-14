@extends('layouts.app')

@section('title', 'FI List')

@section('content')
    <h2>Financial Institution List</h2>
    <a href="/fi/fetch" class="btn btn-success mb-3">Fetch Latest FI List</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="fiTable" class="table table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Code</th>
                <th>Short Code</th>
                <th>Status</th>
                <th>Card Routing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fiList as $fi)
                <tr>
                    <td>{{ $fi->fiType }}</td>
                    <td>{{ $fi->fiName }}</td>
                    <td>{{ $fi->fiCode }}</td>
                    <td>{{ $fi->fiShortCod }}</td>
                    <td>{{ $fi->fiStatus }}</td>
                    <td>{{ $fi->cardRoutingNo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#fiTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
});
</script>
@endsection
