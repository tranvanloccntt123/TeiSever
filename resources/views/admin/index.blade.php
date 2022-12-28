<x-admin.layout>
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">admin /</span> <a href="{{route('m.'.$title)}}">{{$title}}</a>
    </h4>
    @foreach ($layouts as $layout)
        @include('admin.layouts.'.$layout)
    @endforeach
    <script>
        let active = "<?php echo $title ?>";
        document.getElementById(`menu-${active}`).className += " active";
    </script>
</x-admin.layout>