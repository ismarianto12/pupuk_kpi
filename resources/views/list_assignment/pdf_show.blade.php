<title>Detail Assigment KPI Per Unit Kerja</title>

<style>
    #customers {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }



    #customers td,
    #customers th {
        border: 0.1px dotted #000;
        padding: 2px;


    }

    #customers tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #customers tr:hover {
        background-color: #ddd;
    }

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    }

    .text-center {
        text-align: center;
    }
</style>

<div style="float:left">
    <img src="{{ asset('assets/img/logo.png') }}" height="80px" widht="80px" />
</div>

<div style="margin-left:10px">


    <center>
        <h3>PT PUPUK INDONESIA PERSERO</h3>
        <h4>DETAIL ASSIGMENT KPI <i>(Key Performance Indicator)</i>
        </h4>
    </center>
</div>



<table id="customers">
    <thead style="height:20px">
        <tr>
            <th rowspan="2" class="text-center"><strong>
                    <center>KPI</center>
                </strong></th>
            <th rowspan="2" class="text-center"><strong>Satuan</strong></th>
            <th rowspan="2" class="text-center"><strong>Target</strong></th>
            <th rowspan="2" class="text-center"><strong>Polaritas</strong></th>
            <th colspan="3" class="text-center">
                <b>Bobot</b>
            </th>
        </tr>
        <tr>
            <th>Sub</th>
            <th>KPI</th>
            <th>Total</th>
        </tr>

    </thead>

    @php
        $j = 1;
    @endphp
    @foreach ($render as $as => $renders)
        @php
            $tmprospektif_id = $renders['tmprospektif_id']['val'] ?? $renders['tmprospektif_id']['val'];
            $tmprospektif_sub_id = $renders['tmprospektif_sub_id']['val'] ? $renders['tmprospektif_sub_id']['val'] : null;
        @endphp

        <tbody id="table_body{{ $j }}">


            @if ($renders['parent']['val'] == false)
                <tr id="tr_child_{{ $j }}_{{ $j }}">
                @else
                <tr>
            @endif

            @if ($renders['parent']['val'])
                <td colspan="8" style="background:#ddd">
                    <div style="margin-left:0px">
                        @php echo $renders['nama_kpi']['val'] @endphp</div>
                </td>
            @else
                <td>
                    <div style="margin-left:20px"> @php echo $renders['nama_kpi']['val'] @endphp</div>
                </td>
                <td>{{ $renders['nama_satuan']['val'] }}</td>
                <td>{{ $renders['target']['val'] }}</td>
                <td>{{ $renders['nama_polaritas']['val'] }}</td>
                <td>{{ $renders['sub']['val'] }}</td>
                <td>{{ $renders['kpi']['val'] }}</td>
                <td colspan="2">{{ $renders['total']['val'] }}</td>
            @endif
            </tr>

        </tbody>
        @php
            $j++;
        @endphp
    @endforeach


</table>

<div style="float:right">
    <br />
    PT Pupuk Indonesia Persero , Jakarta {{ date('Y') }}
    <br /><br /><br />
    Kinerja Korporat



</div>
