<x-admin.layout>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Admin</a></li>
          <li class="breadcrumb-item"><a href="{{route('m.'.$title)}}">{{$title}}</a></li>
          @if ($subTitle != "")
            <li class="breadcrumb-item"><a href="#">{{$subTitle}}</a></li>
          @endif
          
        </ol>
      </nav>
    @foreach ($layouts as $layout)
        @include('admin.layouts.'.$layout)
    @endforeach
</x-admin.layout>