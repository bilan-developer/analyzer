@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="mx-auto m-5" style="width: 700px;">
            <h2>Проверить сайт</h2>
            <form id="form" class="form-horizontal" role="form">
                <div class="form-group">
                    <div class="input-group mb-1">
                        <input type="text" class="form-control" id="url" name="url" placeholder="Сайт" aria-label="Сайт" onclick="$(this).removeClass('is-invalid')">
                        <div class="input-group-append">
                            <button class="btn btn-primary" onclick="sendForm()" type="button">Проверить</button>
                        </div>
                        <div class="invalid-feedback" id="url_error">
                            Please choose a username.
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="report">

        </div>
    </div>
@endsection
