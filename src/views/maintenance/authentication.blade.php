<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Maintenance</title>
</head>
<body class="bg-dark text-light">
    <p class="display-4 text-center mt-5 mb-5 m-auto">Le site est actuellement en maintenance, veuillez vous authentifier pour y accéder</p>

    <form class="m-auto w-75" action="{{ $secretLink }}" method="post">
        <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="hidden" name="csrf" value="{{ generateCsrf()->getToken() }}">
            <input type="password" class="form-control" id="password" aria-describedby="password" placeholder="Entrez votre mot de passe" required name="password">
        </div>

        <button type="submit" class="btn btn-primary mb-2 mt-5 m-auto d-block">Accéder au site</button>
    </form>
</body>
</html>