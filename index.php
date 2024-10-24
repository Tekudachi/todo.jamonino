<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List actualizable mediante botón</title>
    <!-- Bootstrap CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">
        <!-- Título -->
        <h1 class="text-center">Todo list</h1>

        <!-- Formulario para agregar tareas -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="content">Nueva tarea</label>
                    <input type="text" id="content" class="form-control" placeholder="Ingresa una tarea">
                </div>
                <button id="guardar" class="btn btn-primary btn-block">Guardar</button>
            </div>
        </div>

        <!-- Listado de tareas -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <h2 class="text-center" id="lista">TO DO</h2>
                <ul id="todo-list" class="list-group">
                    <?php
                    require "DB.php";
                    require "todo.php";

                    try {
                        $db = new DB;
                        $todo_list = Todo::DB_selectAll($db->connection);
                        foreach ($todo_list as $row) {
                            echo "<li class='list-group-item'>" . htmlspecialchars($row->getContent()) . "</li>";
                        }
                    } catch (PDOException $e) {
                        print "Error!: " . $e->getMessage() . "<br/>";
                        die();
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('guardar').addEventListener('click', function () {
            const content = document.getElementById('content').value;

            if (!content) {
                alert('Por favor, introduce una tarea.');
                return;
            }

            const url = 'http://www.vh5.lan/controller.php';  // Asegúrate de que la URL esté correcta y que el servidor PHP esté configurado
            const postData = {
                content: content
            };

            // Llamada POST usando fetch
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const lista = document.getElementById('todo-list');
                    lista.innerHTML = '';  // Limpiar la lista antes de agregar nuevos elementos

                    data.forEach(item => {
                        var li = document.createElement("li");
                        li.classList.add('list-group-item');
                        li.appendChild(document.createTextNode(item.content));  // Solo mostrar el contenido
                        lista.appendChild(li);
                    });

                    // Limpiar el campo de texto
                    document.getElementById('content').value = '';
                } else {
                    console.error('No se recibieron datos');
                }
            })
            .catch(error => console.error('Error en la solicitud POST:', error));
        });
    </script>

</body>
</html>

