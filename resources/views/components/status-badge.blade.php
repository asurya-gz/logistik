@php($status = strtolower($status))
<span class="badge badge-{{ $status }}">{{ ucfirst($status) }}</span>
