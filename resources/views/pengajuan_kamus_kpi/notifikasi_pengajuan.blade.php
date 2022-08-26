@if ($data->count() > 0)


    @foreach ($data as $datas)
        <a href="#" class="navi-item notifikasi_ll" onClick="javascript:detail_notif({{ $datas->idnya }})">
            <div class="navi-link">
                <div class="navi-icon mr-2">
                    <i class="flaticon2-line-chart text-success"></i>
                </div>
                <div class="navi-text">
                    <div class="font-weight-bold">
                        {{ $datas->nama_kpi }}
                    </div>
                    <div class="text-muted">{{ $datas->date_crated }}</div>
                </div>
            </div>
        </a>
    @endforeach
@else
    <div class="alert alert-danger">Data kosong</div>
@endif
