var par = inc;
const url = '{{ route('kamus.get_list') }}';
let ll = '';
fetch(url)
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        let authors = data;
        console.log(data);
        data.map((value, respon) => {
            ll += "<option value='" + value.idnya + "'>" + value.nama_kpi + "</option>";
        });
    });

console.log(ll);