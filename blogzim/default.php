<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu blogzim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <?php
        require_once("include/mysql_functions.php");

        db_create_entry_table();

        if(isset($_POST['action']) && $_POST['action'] == 'create') {

            $data_insert = [
                'title' => $_POST['title'],
                'text' => $_POST['text'],
                'author' => 'Marcelo'
            ];

            // Insere a nova postagem no banco de dados
            db_insert('entry', $data_insert);
        }

        $dropdown_adm = file_get_contents("include/dropdown_adm.php");

        $conn = db_connect('mysql', 'root', 'root', 'blogzim');
    ?>
</head>
<body>
    <h1 class="text-center">Meu blogzim</h1>
    <p class="text-center">Bem-vindo ao meu blogzim! Aqui você encontrará uma variedade de artigos interessantes.</p>
    <h2 class="text-center">Postagens</h2>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                $posts = db_select('select * from entry order by id desc');

                if ($posts) {
                    foreach ($posts as $post) {
                ?>
                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                        <p class="card-text"><?php echo $post['text']; ?></p>
                        <?php
                        echo $dropdown_adm;
                        ?>
                    </div>
                </div>
                <?php }} else { ?>
                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="card-title text-center"><?php echo "Nenhuma postagem ainda... :´-(" ?></h5>
                    </div>
                </div>
                <?php } ?>
    </div>
    <button type="button" class="btn btn-outline-dark mt-5" data-bs-toggle="modal" data-bs-target="#exampleModal">Fazer postagem</button>

    <div id="exampleModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form" method="post">
                    <div class="mb-3">
                        <input type="hidden" name="action" value="create">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título da postagem">
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" id="text" name="text" rows="3" placeholder="Digite o texto da postagem"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="javascript: document.querySelector('#form').submit();">Salvar postagem</button>
            </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>