<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload de fichier</title>
</head>
<body>
    <form action="{{ route("post","upload")  }}" enctype="multipart/form-data" method="post">
        <p>{{ $error }}</p>
        <input type="file" name="fichier">
        <input type="hidden" name="csrf" value="{{ generateCsrf()->getToken() }}">
        <button>Upload</button>
    </form>
</body>
</html>