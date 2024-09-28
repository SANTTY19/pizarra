<?php
// Incluir la conexión a la base de datos
include './includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $abonado = $_POST['abonado'];
    $nombre = $_POST['nombre'];
    $contribuyente = $_POST['contribuyente'];
    $e_mail = $_POST['e_mail'];
    $telefono1 = $_POST['telefono1'];
    $direccion = $_POST['direccion'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    $colaborador = $_POST['colaborador'];
    $estado = $_POST['estado'];

    // Preparar la consulta SQL
    $sql = "INSERT INTO eventos (abonado, nombre, contribuyente, e_mail, telefono1, direccion, titulo, fecha, descripcion, colaborador, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la sentencia
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $abonado, $nombre, $contribuyente, $e_mail, $telefono1, $direccion, $titulo, $fecha, $descripcion, $colaborador, $estado);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo "Evento guardado correctamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeamSELCA - Calendario Colaborativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">TeamSELCA</div>
            <div class="user-profile">
                <span>Bienvenido, Usuario</span>
                <img src="./assets/img/logo-favicon.png" alt="Avatar">
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="sidebar">
            <h2>Panel de Control</h2>
            <div class="mini-calendar-container">
                <div id="month-year" class="month-year-display"></div>
                <div id="mini-calendar"></div>
                
            </div>
            
            <br>            
            <button class="btn-add-event" id="add-event-btn">Añadir Evento</button>
            <button class="btn-add-member" id="add-member-btn">Añadir Miembro</button>
            <div class="team-list">
                <h3>Miembros del equipo</h3>
                <ul id="team-members">
                    <li><span>Juan Pérez</span></li>
                    <li><span>María González</span></li>
                    <li><span>Ana López</span></li>
                </ul>
            </div>
        </div>

        <div class="calendar-container">
            <div class="calendar-header">
                <button class="prev-month">Anterior</button>
                <h2 id="current-month"></h2>
                <button class="next-month">Siguiente</button>
            </div>

            <div class="calendar">
                <div class="day-names">
                    <div>Domingo</div>
                    <div>Lunes</div>
                    <div>Martes</div>
                    <div>Miércoles</div>
                    <div>Jueves</div>
                    <div>Viernes</div>
                    <div>Sábado</div>
                </div>
                <div id="calendar-days" class="days-grid"></div>
            </div>
        </div>
    </div>

<!-- Modal para añadir evento -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Añadir Evento</h2>
        <br>
         <!-- Botón para mostrar la tabla de clientes -->
         <button type="button" id="show-client-table" class="btn-load">Seleccionar Cliente</button>
        <br>
        <form id="add-event-form" method="POST" action="guardar_evento.php">
    <!-- Información del cliente autocompletada -->
    <div id="client-info" class="client-info">
        <div class="input-container">
            <label for="event-abonado">Número de Abonado</label>
            <input type="text" id="event-abonado" name="abonado" placeholder="Ingrese número de abonado" required>
        </div>
        <div class="input-container">
            <label for="client-name">Nombre del Cliente</label>
            <input type="text" id="client-name" name="nombre" disabled>
        </div>
        <div class="input-container">
            <label for="client-id">Contribuyente</label>
            <input type="text" id="client-id" name="contribuyente" disabled>
        </div>
        <div class="input-container">
            <label for="client-email">Correo Electrónico</label>
            <input type="email" id="client-email" name="e_mail" disabled>
        </div>
        <div class="input-container">
            <label for="client-phone">Teléfono</label>
            <input type="text" id="client-phone" name="telefono1" disabled>
        </div>
        <div class="input-container">
            <label for="client-address">Dirección</label>
            <textarea id="client-address" name="direccion" rows="2" disabled></textarea>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div id="client-table-container" class="client-table-container">
        <br>
        <input type="text" id="client-search" placeholder="Buscar cliente..." onkeyup="filterClients()">
        <table id="client-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Contribuyente</th>
                    <th>Correo Electrónico</th>
                    <th>Teléfono</th>
                </tr>
            </thead>
            <tbody id="client-table-body">
                <!-- Los clientes se llenarán aquí dinámicamente -->
            </tbody>
        </table>
    </div>

    <!-- Otros campos del evento -->
    <div class="input-container">
        <label for="event-title">Título del Evento</label>
        <input type="text" id="event-title" name="titulo" placeholder="Ingrese el título del evento" required>
    </div>
    <div class="input-container">
        <label for="event-date">Fecha</label>
        <input type="date" id="event-date" name="fecha" required>
    </div>
    <div class="input-container">
        <label for="event-description">Descripción</label>
        <textarea id="event-description" name="descripcion" rows="4" placeholder="Descripción del evento"></textarea>
    </div>
    <div class="input-container">
        <label for="event-collaborator">Asignar a Colaborador</label>
        <select id="event-collaborator" name="colaborador" required>
            <option value="">Seleccione un colaborador</option>
            <option value="Juan Pérez">Juan Pérez</option>
            <option value="María González">María González</option>
            <option value="Ana López">Ana López</option>
        </select>
    </div>
    <div class="input-container">
        <label for="event-status">Estado</label>
        <select id="event-status" name="estado" required>
            <option value="">Seleccione un estado</option>
            <option value="urgente">Urgente</option>
            <option value="medio">Medio</option>
            <option value="basico">Básico</option>
        </select>
    </div>
    <button type="submit" class="btn-submit">Guardar Evento</button>
</form>

    </div>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        max-height: 80vh;
        overflow-y: auto;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover {
        color: black;
        cursor: pointer;
    }

    /* Input Fields */
    .input-container {
        margin-bottom: 15px;
    }

    .input-container label {
        display: block;
        font-weight: bold;
    }

    .input-container input,
    .input-container textarea,
    .input-container select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .input-container input:focus,
    .input-container textarea:focus,
    .input-container select:focus {
        border-color: #001744;
    }

    /* Buttons */
    .btn-load, .btn-submit {
        background-color: #001744;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-load:hover, .btn-submit:hover {
        background-color: #0042cc;
    }

    .client-table-container {
        display: none;
    }

    .client-info {
        display: none;
    }

   /* Tabla de clientes */
#client-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

#client-table th, #client-table td {
    padding: 12px 15px;
    text-align: left;
}

#client-table th {
    background-color: #001744;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
}

#client-table td {
    background-color: #f9f9f9;
}

#client-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

#client-table tr:hover {
    background-color: #e0e0e0;
    cursor: pointer;
}

/* Barra de búsqueda */
#client-search {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 16px;
    background-image: url('https://cdn-icons-png.flaticon.com/512/54/54481.png');
    background-position: 10px center;
    background-repeat: no-repeat;
    background-size: 20px;
    padding-left: 40px; /* Para dejar espacio para el ícono */
}

#client-search:focus {
    border-color: #001744;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 23, 68, 0.4);
}

</style>

<script>
    document.getElementById('show-client-table').addEventListener('click', function() {
        const clientTableContainer = document.getElementById('client-table-container');
        clientTableContainer.style.display = clientTableContainer.style.display === 'none' ? 'block' : 'none';
        loadClients();
    });

    // Modificamos el código de la función que carga los datos del cliente al hacer clic en la tabla
    function loadClients() {
        fetch(`http://192.168.12.3:83/api/public/api/clientes/all`)
            .then(response => response.json())
            .then(data => {
                const clientTableBody = document.getElementById('client-table-body');
                clientTableBody.innerHTML = '';
                data.forEach(cliente => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${cliente.nombre}</td>
                        <td>${cliente.contribuyente}</td>
                        <td>${cliente.e_mail}</td>
                        <td>${cliente.telefono1}</td>
                    `;
                    row.addEventListener('click', () => {
                        document.getElementById('client-name').value = cliente.nombre;
                        document.getElementById('client-id').value = cliente.contribuyente;
                        document.getElementById('client-email').value = cliente.e_mail;
                        document.getElementById('client-phone').value = cliente.telefono1;
                        document.getElementById('client-address').value = cliente.direccion;
                        document.getElementById('event-abonado').value = cliente.cliente; // Aquí se autocompleta el número de abonado
                        document.getElementById('client-info').style.display = 'block';
                        document.getElementById('client-table-container').style.display = 'none';
                    });
                    clientTableBody.appendChild(row);
                });
            });
    }

    // Asignar la fecha seleccionada del calendario al campo de fecha en el modal
    function openModal(selectedDate) {
        // Abre el modal
        document.getElementById("modal").style.display = "block";
        
        // Asigna la fecha seleccionada al campo de fecha
        document.getElementById("event-date").value = selectedDate;
    }

    // Escuchar clics en las celdas del calendario
    document.querySelectorAll(".calendar-cell").forEach(cell => {
        cell.addEventListener("click", function() {
            const selectedDate = this.getAttribute("data-date"); // Captura la fecha seleccionada
            openModal(selectedDate); // Abre el modal con la fecha preseleccionada
        });
    });


    function filterClients() {
        const searchValue = document.getElementById('client-search').value.toLowerCase();
        const clientRows = document.querySelectorAll('#client-table-body tr');
        clientRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchValue)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }
    
</script>

    <!-- Modal para añadir miembro -->
    <div id="addMemberModal" class="modal">
        <div class="modal-content">
            <span class="close-member-modal">&times;</span>
            <h2>Añadir Miembro</h2>
            <form id="addMemberForm">
                <div class="input-container">
                    <label for="member-name">Nombre del Miembro:</label>
                    <input type="text" id="member-name" name="member-name" required>
                </div>
                <div class="input-container">
                    <label for="member-role">Rol:</label>
                    <select id="member-role" name="member-role" required>
                        <option value="tecnico">Técnico</option>
                        <option value="administrativo">Administrativo</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Añadir Miembro</button>
            </form>
        </div>
    </div>

    <script>
        const calendarDays = document.getElementById("calendar-days");
        const currentMonth = document.getElementById("current-month");
        
        let events = [];
        let selectedDate = new Date();
        let month = selectedDate.getMonth();
        let year = selectedDate.getFullYear();
        
        function displayCalendar() {
    calendarDays.innerHTML = '';
    
    // Actualiza el mes mostrado
    currentMonth.textContent = `${new Date(year, month).toLocaleString('default', { month: 'long' })} ${year}`;
    
    const firstDay = new Date(year, month, 1);
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const startDay = firstDay.getDay();

    for (let i = 0; i < startDay; i++) {
        const emptyDiv = document.createElement("div");
        calendarDays.appendChild(emptyDiv);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement("div");
        dayDiv.className = "day";

        if (day === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear()) {
            dayDiv.classList.add("today");
        }

        const eventForDay = events.find(event => 
            event.date.getDate() === day && event.date.getMonth() === month && event.date.getFullYear() === year
        );

        if (eventForDay) {
            dayDiv.classList.add("event");
            dayDiv.style.backgroundColor = getStatusColor(eventForDay.status);
            dayDiv.innerHTML = `
                <span class="status-icon"> ${getStatusIcon(eventForDay.status)}  ${day} </span> <br> ${eventForDay.title}  <br>
                <span>${eventForDay.collaborator} </span>
            `;
        } else {
            dayDiv.textContent = day;
        }

        calendarDays.appendChild(dayDiv);
    }
}

        
        function getStatusIcon(status) {
            switch (status) {
                case 'urgente':
                    return '<i class="fas fa-exclamation-triangle"></i>';
                case 'medio':
                    return '<i class="fas fa-exclamation-circle"></i>';
                case 'basico':
                    return '<i class="fas fa-check-circle"></i>';
                default:
                    return '';
            }
        }
        
        function getStatusColor(status) {
            switch (status) {
                case 'urgente':
                    return 'red';
                case 'medio':
                    return 'yellow';
                case 'basico':
                    return 'green';
                default:
                    return '';
            }
        }
        
        displayCalendar();
        
        document.querySelector(".prev-month").addEventListener("click", () => {
            month--;
            if (month < 0) {
                month = 11;
                year--;
            }
            displayCalendar();
        });
        
        document.querySelector(".next-month").addEventListener("click", () => {
            month++;
            if (month > 11) {
                month = 0;
                year++;
            }
            displayCalendar();
        });
        
        // Funcionalidad para los modales
        const modal = document.getElementById("modal");
        const addEventBtn = document.getElementById("add-event-btn");
        const closeModal = document.querySelector(".close");
        
        const addMemberModal = document.getElementById("addMemberModal");
        const addMemberBtn = document.getElementById("add-member-btn");
        const closeMemberModal = document.querySelector(".close-member-modal");

        // Mostrar modal de añadir evento
        addEventBtn.onclick = () => {
            modal.style.display = "block";
        };

        // Cerrar modal de añadir evento
        closeModal.onclick = () => {
            modal.style.display = "none";
        };

        // Mostrar modal de añadir miembro
        addMemberBtn.onclick = () => {
            addMemberModal.style.display = "block";
        };

        // Cerrar modal de añadir miembro
        closeMemberModal.onclick = () => {
            addMemberModal.style.display = "none";
        };

        // Cerrar modales al hacer clic fuera de ellos
        window.onclick = (event) => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == addMemberModal) {
                addMemberModal.style.display = "none";
            }
        };

        // Agregar evento al enviar el formulario
        document.getElementById("add-event-form").onsubmit = function(event) {
            event.preventDefault();
            const title = document.getElementById("event-title").value;
            const date = new Date(document.getElementById("event-date").value);
            const description = document.getElementById("event-description").value;
            const collaborator = document.getElementById("event-collaborator").value;
            const status = document.getElementById("event-status").value;

            events.push({ title, date, description, collaborator, status });
            displayCalendar();
            modal.style.display = "none";
            this.reset(); // Resetear el formulario
        };

        // Agregar miembro al enviar el formulario
        document.getElementById("addMemberForm").onsubmit = function(event) {
            event.preventDefault();
            const memberName = document.getElementById("member-name").value;
            const memberRole = document.getElementById("member-role").value;

            const memberList = document.getElementById("team-members");
            const newMember = document.createElement("li");
            newMember.innerHTML = `<span>${memberName} - ${memberRole}</span>`;
            memberList.appendChild(newMember);
            addMemberModal.style.display = "none";
            this.reset(); // Resetear el formulario
        };

        const miniCalendar = document.getElementById("mini-calendar");
const monthYearDisplay = document.getElementById("month-year");
let miniMonth = new Date().getMonth();
let miniYear = new Date().getFullYear();

function displayMiniCalendar() {
    miniCalendar.innerHTML = '';
    monthYearDisplay.textContent = `${getMonthName(miniMonth)} ${miniYear}`;
    
    const firstDay = new Date(miniYear, miniMonth, 1);
    const daysInMonth = new Date(miniYear, miniMonth + 1, 0).getDate();
    const startDay = firstDay.getDay();

    // Celdas vacías antes del primer día del mes
    for (let i = 0; i < startDay; i++) {
        const emptyDiv = document.createElement("div");
        miniCalendar.appendChild(emptyDiv);
    }

    // Crear los días del mes
    for (let day = 1; day <= daysInMonth; day++) {
        const dayDiv = document.createElement("div");
        dayDiv.className = "mini-day";
        dayDiv.textContent = day;

        // Resaltar el día actual
        if (day === new Date().getDate() && miniMonth === new Date().getMonth() && miniYear === new Date().getFullYear()) {
            dayDiv.classList.add("today");
        }

        miniCalendar.appendChild(dayDiv);
    }
}

// Función para obtener el nombre del mes
function getMonthName(month) {
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return monthNames[month];
}

// Navegar entre meses
function changeMonth(direction) {
    miniMonth += direction;
    if (miniMonth < 0) {
        miniMonth = 11;
        miniYear--;
    } else if (miniMonth > 11) {
        miniMonth = 0;
        miniYear++;
    }
    displayMiniCalendar();
}

// Inicializar el mini calendario
displayMiniCalendar();

// Agregar eventos para navegación
document.querySelector(".prev-month").addEventListener("click", () => changeMonth(-1));
document.querySelector(".next-month").addEventListener("click", () => changeMonth(1));
    </script>
</body>
</html>
