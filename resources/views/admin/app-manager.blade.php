<x-admin.application-layout>
    @foreach ($layouts as $layout)
        @include('admin.layouts.'.$layout)
    @endforeach
</x-admin.application-layout>