<?php

namespace App\Http\Core;

class Constants
{
    const errorFilePresence = [
        'number' => '1',
        'name' => 'Проверка наличия файла robots.txt',
        'status' => false,
        'condition' => 'Файл robots.txt отсутствует',
        'recommendations' => 'Программист: Создать файл robots.txt и разместить его на сайте.',
    ];

    const okFilePresence = [
        'number' => '1',
        'name' => 'Проверка наличия файла robots.txt',
        'status' => true,
        'condition' => 'Файл robots.txt присутствует',
        'recommendations' => 'Доработки не требуются',
    ];

    const errorIsDirectiveHost = [
        'number' => '6',
        'name' => 'Проверка указания директивы Host',
        'status' => false,
        'condition' => 'В файле robots.txt не указана директива Host',
        'recommendations' => 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.',
    ];

    const okIsDirectiveHost = [
        'number' => '6',
        'name' => 'Проверка указания директивы Host',
        'status' => true,
        'condition' => 'Директива Host указана',
        'recommendations' => 'Доработки не требуются',
    ];

    const errorCountDirectiveHost = [
        'number' => '8',
        'name' => 'Проверка количества директив Host, прописанных в файле',
        'status' => false,
        'condition' => 'В файле прописано несколько директив Host',
        'recommendations' => 'Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта',
    ];

    const okCountDirectiveHost = [
        'number' => '8',
        'name' => 'Проверка количества директив Host, прописанных в файле',
        'status' => true,
        'condition' => 'В файле прописана 1 директива Host',
        'recommendations' => 'Доработки не требуются',
    ];


    const errorSizeFile = [
        'number' => '10',
        'name' => 'Проверка размера файла robots.txt',
        'status' => false,
        'condition' => 'Размера файла robots.txt составляет ',
        'condition_end' => ' Кб, что превышает допустимую норму',
        'recommendations' => 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб',
    ];

    const okSizeFile = [
        'number' => '10',
        'name' => 'Проверка размера файла robots.txt',
        'status' => true,
        'condition' => 'Размер файла robots.txt составляет ',
        'condition_end' => ' Кб, что находится в пределах допустимой нормы',
        'recommendations' => 'Доработки не требуются',
    ];

    const errorIsDirectiveSitemap = [
        'number' => '11',
        'name' => 'Проверка указания директивы Sitemap',
        'status' => false,
        'condition' => 'В файле robots.txt не указана директива Sitemap',
        'recommendations' => 'Программист: Добавить в файл robots.txt директиву Sitemap',
    ];

    const okIsDirectiveSitemap = [
        'number' => '11',
        'name' => 'Проверка указания директивы Sitemap',
        'status' => true,
        'condition' => 'Директива Sitemap указана',
        'recommendations' => 'Доработки не требуются',
    ];

    const errorCodeAnswer = [
        'number' => '12',
        'name' => 'Проверка кода ответа сервера для файла robots.txt',
        'status' => false,
        'condition' => 'При обращении к файлу robots.txt сервер возвращает код ответа ',
        'recommendations' => 'Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200',
    ];

    const okCodeAnswer = [
        'number' => '12',
        'name' => 'Проверка кода ответа сервера для файла robots.txt',
        'status' => true,
        'condition' => 'Файл robots.txt отдаёт код ответа сервера 200',
        'recommendations' => 'Доработки не требуются',
    ];

    public static function errorCodeAnswer($code)
    {
        $errorCodeAnswer = self::errorCodeAnswer;
        $errorCodeAnswer['condition'] = $errorCodeAnswer['condition'] . $code;

        return $errorCodeAnswer;
    }

    public static function errorSizeFile($fileSize)
    {
        $errorSizeFile = self::errorSizeFile;
        $errorSizeFile['condition'] = $errorSizeFile['condition'] . round($fileSize/1024, 2) . $errorSizeFile['condition_end'];

        return $errorSizeFile;
    }

    public static function okSizeFile($fileSize)
    {
        $okSizeFile = self::okSizeFile;
        $okSizeFile['condition'] = $okSizeFile['condition'] . round($fileSize/1024, 2) . $okSizeFile['condition_end'];

        return $okSizeFile;
    }
}