<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu blogzim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <?php
        $dropdown_adm = file_get_contents("include/dropdown_adm.php");
    ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1>Meu blogzim</h1>
                <p>Bem-vindo ao meu blogzim! Aqui você encontrará uma variedade de artigos interessantes.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Últimos Posts</h2>
                <ul class="list-group">
                    <li class="list-group-item">
                        <h3>Post 1</h3>
                        <p>Este é o conteúdo do primeiro post. Aqui você pode escrever sobre qualquer coisa interessante.</p>
                        <?php
                        echo $dropdown_adm;
                        ?>
                    </li>
                    <li class="list-group-item">
                        <h3>Post 2</h3>
                        <p>Este é o conteúdo do segundo post. Continue escrevendo sobre tópicos que você acha que seus leitores vão gostar.</p>
                    </li>
                    <li class="list-group-item">
                        <h3>Post 3</h3>
                        <p>Este é o conteúdo do terceiro post. Não se esqueça de manter seus posts atualizados e interessantes!</p>
                    </li>
                </ul>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>