<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">№</th>
            <th scope="col">Название проверки</th>
            <th scope="col">Статус</th>
            <th scope="col"></th>
            <th scope="col">Текущее состояние</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $item)
            <tr>
                <td rowspan="2">{{ $item['number'] }}</td>
                <td rowspan="2">{{ $item['name'] }}</td>
                <td rowspan="2" class="{{ $item['status'] ? 'status-ok' : 'status-error' }}">{{ $item['status'] ? 'ОК' : 'Ошибка' }}</td>
                <td>Состояние</td>
                <td >{{ $item['condition'] }}</td>
            </tr>
            <tr>
                <td>Рекомендации</td>
                <td>{{ $item['recommendations'] }}</td>
            </tr>
       @endforeach
    </tbody>
</table>
<div class="row float-right">
    <div class="col-auto">
        <a href="/save/{{$fileName}}" class="btn btn-success">Скачать</a>
        <button type="button" onclick="clearReport()" class="btn btn-danger">Очистить</button>
    </div>
</div>

<style>
    .table{
        background: #fff;
        box-shadow: 0 1px 0 0 rgba(0,0,0,.1), 0 6px 12px 0 rgba(0,0,0,.2);
    }
    .table .status-error{
        background: red;
        color: black;
    }

    .table .status-ok{
        background: green;
        color: black;
    }

</style>