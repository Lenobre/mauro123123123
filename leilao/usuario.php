<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
</head>
<body>
    <h1>Cadastro de Usuário</h1>
    <form id="registerForm">
        <label for="name">Nome</label>
        <input type="text" id="name" name="name" required>
        <br>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>
        <br>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>
        <br>

        <button type="submit">Cadastrar</button>
    </form>

    <div id="message"></div>

    <script>
        const form = document.getElementById('registerForm');
        const messageDiv = document.getElementById('message');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(form);

            fetch('createUser.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.innerHTML = `<p>${data.Message}</p>`;
            })
            .catch(error => {
                messageDiv.innerHTML = `<p>Erro ao realizar o cadastro.</p>`;
            });
        });
    </script>
</body>
</html>
