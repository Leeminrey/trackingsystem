@extends('layouts.app')

@section('content')

<div class="edit-container">
    <h2>Edit Document</h2>
    <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="locator_no">Locator No:</label>
        <input type="text" class="form-control" id="locator_no" name="locator_no" value="{{ $document->locator_no }}" required>
    </div>

    <div class="form-group">
        <label for="subject">Subject:</label>
        <input type="text" class="form-control" id="subject" name="subject" value="{{ $document->subject }}" required>
    </div>

    <div class="form-group">
        <label for="subject">Received From:</label>
        <input type="text" class="form-control" id="received_from" name="received_from" value="{{ $document->received_from }}" required>
    </div>

    <div class="form-group">
        <label for="date_received">Date Received:</label>
        <input type="date" class="form-control"  id="date_received" name="date_received" value="{{ $document->date_received }}" required>
    </div>

    <div class="form-group">
        <label for="details">Details:</label>
        <textarea id="details" class="form-control"  name="details" rows="4">{{ $document->details }}</textarea>
    </div>

    <div class="form-group">
        <label>Current File:</label>
        <p><a href="{{ route('documents.view', $document->hashed_file_name) }}" target="_blank">{{ $document->original_file_name }}</a></p>
    </div>

    <div class="form-group">
        <label for="file">Upload a new file (optional):</label>
        <input type="file" id="file" name="file">
    </div>

    <div class="form-group">
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('dashboard') }}" class="btn-cancel">Cancel</a>
    </div>
</form>


</div>
@endsection
