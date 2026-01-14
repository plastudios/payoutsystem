@extends('layouts.app')

@section('title', 'Webhook Logs')

@section('content')
<div class="container">
    <h2 class="mb-4">Your Webhook Logs</h2>

    <table class="table table-bordered" id="logTable">
        <thead>
            <tr>
                <th>Batch ID</th>
                <th>Status Code</th>
                <th>Request</th>
                <th>Response</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->batch_id }}</td>
                <td>
                    <span class="badge bg-{{ $log->status_code == 200 ? 'success' : 'danger' }}">
                        {{ $log->status_code ?? 'N/A' }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#logModal"
                            data-type="request"
                            data-content="{{ htmlentities($log->request_payload) }}">
                        View
                    </button>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#logModal"
                            data-type="response"
                            data-content="{{ htmlentities($log->response_payload) }}">
                        View
                    </button>
                </td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logModalLabel">Webhook Log</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" id="logContent" rows="20" readonly></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-sm btn-secondary" onclick="copyLog()">Copy to Clipboard</button>
        <button class="btn btn-sm btn-outline-dark" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    function copyLog() {
        const textarea = document.getElementById("logContent");
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        document.execCommand("copy");
    }

    const logModal = document.getElementById('logModal');
    logModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const content = button.getAttribute('data-content');
        const type = button.getAttribute('data-type');
        const modalTitle = logModal.querySelector('.modal-title');
        const textarea = logModal.querySelector('#logContent');

        modalTitle.textContent = `Webhook ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        textarea.value = decodeHTMLEntities(content);
    });

    function decodeHTMLEntities(text) {
        const textarea = document.createElement('textarea');
        textarea.innerHTML = text;
        return textarea.value;
    }

    $(document).ready(function () {
        $('#logTable').DataTable({
            pageLength: 25,
            order: [[4, 'desc']],
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    });
</script>
@endsection
