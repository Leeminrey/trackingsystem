@php
    // Set default size if none is provided
    $size = $size ?? '75px'; // Default size is '100px'

    // Adjust size based on predefined values or accept custom pixel sizes
    if($size == 'large') {
        $size = '180px';
    } 
@endphp

<img src="{{ asset('images/QCPL.png') }}" alt="Logo" style="width: {{ $size ?? '100px' }}; height: auto;">
