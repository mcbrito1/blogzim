<?php
    require_once("include/mysql_functions.php");

    $post = [];

    $conn = db_connect('mysql', 'root', 'root', 'blogzim');
    db_create_entry_table();

    if(isset($_POST['action']) && $_POST['action'] == 'update') {

        db_update('entry', [
            'title' => $_POST['title'],
            'text' => $_POST['text'],
            'author' => 'Marcelo'
        ], 'id=?', 'i', [
            1
        ]);

        header("Location: default.php");
        exit();
    }

    if(isset($_GET['postId']) && is_numeric($_GET['postId'])) {
        $postId = $_GET['postId'];
        $post = db_select("SELECT * FROM entry WHERE id = " . $postId);

        if ($post) {
            $post = $post[0];
        } else {
            // Se a postagem não existir, redireciona para a página principal
            header("Location: default.php");
            exit();
        }
    }

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu blogzim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
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
                        echo str_replace('[paramPostId]', $post['id'], $dropdown_adm);
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
    <button type="button" class="btn btn-outline-dark mt-5" data-bs-toggle="modal" data-bs-target="#postagemModal">Fazer postagem</button>

    <div id="postagemModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo ((isset($_GET['postId'])) ? 'Modificar ' : 'Cadastrar ') ?>Postagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form" method="post">
                    <div class="mb-3">
                        <input type="hidden" name="action" value="<?php echo (isset($_GET['postId']) ? 'update':'create') ?>">
                        <input type="hidden" name="id" value="<?php echo (isset($_GET['postId']) ? $_GET['postId']:'') ?>" />
                        <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título da postagem" value="<?php echo isset($_GET['postId']) ? $post['title'] : ''; ?>" />
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" id="text" name="text" rows="3" placeholder="Digite o texto da postagem"><?php echo isset($_GET['postId']) ? $post['text'] : ''; ?></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="javascript: document.querySelector('#form').submit();"><?php echo ((isset($_GET['postId'])) ? 'Modificar ' : 'Cadastrar ') ?>postagem</button>
            </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var postId = new URLSearchParams(window.location.search).get('postId');
            if (postId) {
                var modal = new bootstrap.Modal(document.getElementById('postagemModal'));
                modal.show();
            }
        });
    </script>
</body>
</html>