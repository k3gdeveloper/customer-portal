<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Link</title>
</head>

<body>
    <form action="{{ route('createLinkForUser', ['userId' => $userId]) }}" method="POST">
        @csrf
        <button type="submit">Criar Link</button>
    </form>

    <!-- Inclua o jQuery se estiver usando AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Configuração do token CSRF para requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</body>

</html>
