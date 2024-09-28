<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - Planeador de Eventos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex; /* Flexbox para el diseño */
        }
        #sidebar {
            width: 250px; /* Ancho del sidebar */
            background-color: #001744; /* Color de fondo del sidebar */
            color: white;
            padding: 20px;
            height: 100vh; /* Altura completa de la ventana */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        #sidebar h2 {
            margin: 0 0 20px;
            font-weight: 700;
        }
        #sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 0;
            transition: background 0.3s;
        }
        #sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        #main-content {
            flex: 1; /* Toma el espacio restante */
            padding: 20px;
        }
        header {
            background-color: #001744; /* Color del encabezado */
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        #calendar {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #nav {
            margin-bottom: 20px;
        }
        button {
            background: #001744;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            margin-right: 10px; /* Espacio entre botones */
        }
        button:hover {
            background: #0056b3;
        }
        #month-view, #week-view, #day-view {
            display: none;
            width: 100%;
        }
        #month-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        #day-view, #week-view {
            display: grid;
            grid-template-rows: auto repeat(24, 1fr);
            gap: 5px;
            width: 100%;
        }
        #week-view {
            grid-template-columns: repeat(7, 1fr);
        }
        .day, .hour {
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            position: relative;
            transition: transform 0.2s;
        }
        .day:hover, .hour:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .event {
            background: #cfe3ff;
            border-left: 5px solid #007BFF;
        }
        #event-modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .modal-content h2 {
            margin: 0 0 15px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input:focus, textarea:focus {
            border-color: #007BFF;
            outline: none;
        }
        #close-modal {
            background: #dc3545;
        }
        #close-modal:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    
    <div id="sidebar">
        <h2>Opciones</h2>
        <a href="#" id="add-member-btn">Agregar Miembros</a>
        <a href="#" id="view-members-btn">Ver Miembros</a>
    </div>

    <div id="main-content">
        <header>
            <h1>Calendario - Planeador de Eventos</h1>
        </header>
        <div id="nav">
            <button id="month-btn">Mes</button>
            <button id="week-btn">Semana</button>
            <button id="day-btn">Día</button>
            <button id="add-event-btn">Agregar Evento</button> <!-- Nuevo botón para agregar eventos -->
        </div>
        
        <div id="calendar">
            <div id="month-view"></div>
            <div id="week-view"></div>
            <div id="day-view"></div>
        </div>

        <!-- Modal para agregar eventos -->
        <div id="event-modal">
            <div class="modal-content">
                <h2>Añadir Evento</h2>
                <form id="event-form">
                    <label for="event-title">Título:</label>
                    <input type="text" id="event-title" required>
                    <label for="event-description">Descripción:</label>
                    <textarea id="event-description" required></textarea>
                    <input type="hidden" id="event-date">
                    <input type="hidden" id="event-time">
                    <button type="submit">Guardar Evento</button>
                    <button type="button" id="close-modal">Cerrar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tu código JavaScript aquí
        const dayView = document.getElementById("day-view");
        const weekView = document.getElementById("week-view");
        const monthView = document.getElementById("month-view");
        const eventModal = document.getElementById("event-modal");
        const eventForm = document.getElementById("event-form");
        const eventTitle = document.getElementById("event-title");
        const eventDescription = document.getElementById("event-description");
        const eventDate = document.getElementById("event-date");
        const eventTime = document.getElementById("event-time");
        const closeModal = document.getElementById("close-modal");
        const addEventBtn = document.getElementById("add-event-btn");

        let events = [];
        let selectedDate = new Date();

        function displayDay() {
            dayView.innerHTML = '';
            const currentDateStr = selectedDate.toLocaleDateString();

            const dayHeader = document.createElement("div");
            dayHeader.textContent = `Fecha: ${currentDateStr}`;
            dayHeader.style.gridRow = "1";
            dayHeader.style.gridColumn = "1 / -1"; 
            dayHeader.style.fontSize = "24px";
            dayHeader.style.textAlign = "center";
            dayHeader.style.fontWeight = "bold";
            dayView.appendChild(dayHeader);

            for (let hour = 0; hour < 24; hour++) {
                const hourDiv = document.createElement("div");
                hourDiv.className = "hour";
                hourDiv.textContent = `${hour}:00`;

                const dateStr = `${selectedDate.getFullYear()}-${selectedDate.getMonth() + 1}-${selectedDate.getDate()}`;
                const timeStr = `${hour}:00`;

                events.forEach(event => {
                    if (event.date === dateStr && event.time === timeStr) {
                        hourDiv.classList.add("event");
                        hourDiv.title = `${event.title}: ${event.description}`;
                    }
                });

                hourDiv.addEventListener('click', () => {
                    eventDate.value = dateStr; 
                    eventTime.value = timeStr; 
                    eventModal.style.display = "flex"; 
                });

                dayView.appendChild(hourDiv);
            }
        }

        function displayWeek() {
            weekView.innerHTML = '';
            const startOfWeek = new Date(selectedDate);
            startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());

            const weekHeader = document.createElement("div");
            weekHeader.textContent = `Semana del ${startOfWeek.toLocaleDateString()} al ${new Date(startOfWeek).setDate(startOfWeek.getDate() + 6)}`;
            weekHeader.style.gridRow = "1";
            weekHeader.style.gridColumn = "1 / -1"; 
            weekHeader.style.fontSize = "24px";
            weekHeader.style.textAlign = "center";
            weekHeader.style.fontWeight = "bold";
            weekView.appendChild(weekHeader);

            for (let day = 0; day < 7; day++) {
                const currentDay = new Date(startOfWeek);
                currentDay.setDate(currentDay.getDate() + day);

                const dayDiv = document.createElement("div");
                dayDiv.className = "day";
                dayDiv.textContent = currentDay.toLocaleDateString();
                
                const dateStr = `${currentDay.getFullYear()}-${currentDay.getMonth() + 1}-${currentDay.getDate()}`;

                for (let hour = 0; hour < 24; hour++) {
                    const hourDiv = document.createElement("div");
                    hourDiv.className = "hour";
                    hourDiv.textContent = `${hour}:00`;

                    const timeStr = `${hour}:00`;

                    events.forEach(event => {
                        if (event.date === dateStr && event.time === timeStr) {
                            hourDiv.classList.add("event");
                            hourDiv.title = `${event.title}: ${event.description}`;
                        }
                    });

                    hourDiv.addEventListener('click', () => {
                        eventDate.value = dateStr; 
                        eventTime.value = timeStr; 
                        eventModal.style.display = "flex"; 
                    });

                    dayDiv.appendChild(hourDiv);
                }

                weekView.appendChild(dayDiv);
            }
        }

        function displayMonth() {
            monthView.innerHTML = '';
            const currentMonth = selectedDate.getMonth();
            const currentYear = selectedDate.getFullYear();
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            
            const monthHeader = document.createElement("div");
            monthHeader.textContent = `Mes: ${firstDay.toLocaleString('default', { month: 'long' })} ${currentYear}`;
            monthHeader.style.gridRow = "1";
            monthHeader.style.gridColumn = "1 / -1"; 
            monthHeader.style.fontSize = "24px";
            monthHeader.style.textAlign = "center";
            monthHeader.style.fontWeight = "bold";
            monthView.appendChild(monthHeader);

            for (let day = 1; day <= lastDay.getDate(); day++) {
                const dayDiv = document.createElement("div");
                dayDiv.className = "day";
                dayDiv.textContent = day;

                const dateStr = `${currentYear}-${currentMonth + 1}-${day}`;
                
                events.forEach(event => {
                    if (event.date === dateStr) {
                        dayDiv.classList.add("event");
                        dayDiv.title = `${event.title}: ${event.description}`;
                    }
                });

                dayDiv.addEventListener('click', () => {
                    eventDate.value = dateStr; 
                    eventModal.style.display = "flex"; 
                });

                monthView.appendChild(dayDiv);
            }
        }

        eventForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const newEvent = {
                title: eventTitle.value,
                description: eventDescription.value,
                date: eventDate.value,
                time: eventTime.value
            };
            events.push(newEvent);
            eventModal.style.display = "none"; 
            eventTitle.value = '';
            eventDescription.value = '';
            displayDay(); // Refresca la vista del día
            displayWeek(); // Refresca la vista de la semana
            displayMonth(); // Refresca la vista del mes
        });

        closeModal.addEventListener('click', () => {
            eventModal.style.display = "none"; 
        });

        addEventBtn.addEventListener('click', () => {
            eventModal.style.display = "flex"; 
        });

        document.getElementById("day-btn").addEventListener('click', () => {
            monthView.style.display = 'none';
            weekView.style.display = 'none';
            dayView.style.display = 'flex';
            displayDay();
        });

        document.getElementById("week-btn").addEventListener('click', () => {
            monthView.style.display = 'none';
            weekView.style.display = 'flex';
            dayView.style.display = 'none';
            displayWeek();
        });

        document.getElementById("month-btn").addEventListener('click', () => {
            weekView.style.display = 'none';
            dayView.style.display = 'none';
            monthView.style.display = 'grid';
            displayMonth();
        });

        // Inicializa mostrando el mes actual
        displayMonth();
    </script>
</body>
</html>
