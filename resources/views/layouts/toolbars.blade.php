<div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-2">
            <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">
                @php echo $par @endphp</h5>
            <span class="pulse-ring"></span>


            @if ($ajax_button == '')
                {{-- <a href="#" class="create btn btn-light-warning font-weight-bolder btn-sm">
                    {{ $label ? $label : 'Add New' }}</a> --}}
            @else
                <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200">
                </div>
                <a href="{{ $ajax_button }}"
                    class="btn btn-light-warning font-weight-bolder btn-sm">{{ $label ? $label : 'Add New' }}</a>
            @endif
        </div>

        <!--end::Toolbar-->
    </div>
</div>
