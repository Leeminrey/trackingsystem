@extends('layouts.app')

@section('content')


@include('components.breadcrumb')

@if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="container">
    @csrf
    <div class="form-group">
        <label for="locator_no">Locator No.:</label>
        <input type="text" class="form-control" name="locator_no" id="locator_no" 
            value="{{ old('locator_no') }}" required 
            placeholder="Locator Number" 
            oninput="this.value = this.value.toUpperCase();">

        @error('locator_no')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

        
    <div class="form-group">
        <label for="subject">Subject:</label>
        <input type="text" class="form-control" name="subject" placeholder="Subject" required>
    </div>
    
    <div class="form-group">
        <label for="received_from">Received from:</label>
        <input type="text" class="form-control" name="received_from" placeholder="Received from" required>
    </div>

    <div class="form-group">
        <label for="date_received">Date Received:</label>
        <input type="date" class="form-control" name="date_received" required>
    </div>

    <div class="form-group">
        <label for="details">Details:</label>
        <textarea class="form-control" name="details" placeholder="Details"></textarea>
    </div>

    <div class="form-group">
        <label for="file">Upload File:</label>
        <input type="file" name="file[]" id="file" multiple required>
    </div>

   
    <button type="submit" class="btn btn-primary" style="width: 170px;">Upload Document</button>
</form>



@endsection