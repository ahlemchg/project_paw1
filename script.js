$(document).ready(function() {
    let students = [];

    // Charger les étudiants
    function loadStudents() {
        const localStudents = localStorage.getItem('students');
        if (localStudents) {
            students = JSON.parse(localStudents);
            runPageLogic();
        } else {
            fetch('students.json')
                .then(response => response.json())
                .then(data => {
                    students = data;
                    saveStudents(); // Sauvegarder dans localStorage pour la session
                    runPageLogic();
                });
        }
    }

    function saveStudents() {
        localStorage.setItem('students', JSON.stringify(students));
    }

    function runPageLogic() {
        // Logique pour la page de présence
        if (window.location.pathname.includes('attendance.html')) {
            renderTable();
        }

        // Logique pour la page d'ajout
        if (window.location.pathname.includes('add_student.html')) {
            $('#addStudentForm').on('submit', handleAddStudent);
        }

        // Logique pour la page des rapports
        if (window.location.pathname.includes('reports.html')) {
            generateReport();
        }
    }

    loadStudents();

    function renderTable() {
        const tbody = $('#attendanceTable tbody');
        tbody.empty();
        students.forEach((student, index) => {
            let newRow = `<tr data-index="${index}">
                <td>${student.lastName}</td>
                <td>${student.firstName}</td>`;
            for (let i = 0; i < 6; i++) {
                newRow += `<td><input type="checkbox" class="presence" data-session="${i}" ${student.attendance[i] ? 'checked' : ''}></td>
                           <td><input type="checkbox" class="participation" data-session="${i}" ${student.participation[i] ? 'checked' : ''}></td>`;
            }
            newRow += `<td class="absences"></td><td class="participations"></td><td class="message"></td></tr>`;
            tbody.append(newRow);
        });
        updateAllRows();
        bindTableEvents();
    }

    function updateRow(row) {
        const index = $(row).data('index');
        const student = students[index];
        let absenceCount = student.attendance.filter(p => !p).length;
        let participationCount = student.participation.filter(p => p).length;

        $(row).find('.absences').text(absenceCount);
        $(row).find('.participations').text(participationCount);

        $(row).removeClass('green yellow red');
        if (absenceCount < 3) $(row).addClass('green');
        else if (absenceCount <= 4) $(row).addClass('yellow');
        else $(row).addClass('red');

        let message = absenceCount < 3 ? 'Bonne présence' : (absenceCount <= 4 ? 'Attention - présence faible' : 'Exclu - trop d\'absences');
        message += participationCount > 4 ? ' - Excellente participation' : (participationCount < 2 ? ' - Participez plus' : '');
        $(row).find('.message').text(message);
    }

    function updateAllRows() {
        $('#attendanceTable tbody tr').each(function() { updateRow(this); });
    }

    function handleAddStudent(e) {
        e.preventDefault();
        // Validation (simplifiée, peut être étendue)
        const newStudent = {
            id: $('#student_id').val(),
            lastName: $('#last_name').val(),
            firstName: $('#first_name').val(),
            email: $('#email').val(),
            attendance: Array(6).fill(false),
            participation: Array(6).fill(false)
        };
        students.push(newStudent);
        saveStudents();
        $('#addStudentForm')[0].reset();
        $('#confirmationMessage').show().fadeOut(2000, function() {
            window.location.href = 'attendance.html'; // Redirection vers la page de présence
        });
    }

    function generateReport() {
        const totalStudents = students.length;
        let perfectAttendance = 0;
        let totalParticipation = 0;
        let absenceData = [0, 0, 0];

        students.forEach(student => {
            const absences = student.attendance.filter(p => !p).length;
            if (absences === 0) perfectAttendance++;
            totalParticipation += student.participation.filter(p => p).length;
            if (absences < 3) absenceData[0]++;
            else if (absences <= 4) absenceData[1]++;
            else absenceData[2]++;
        });

        $('#totalStudents').text(totalStudents);
        $('#perfectAttendance').text(perfectAttendance);
        $('#totalParticipation').text(totalParticipation);

        const ctx = $('#attendanceChart');
        if (window.myChart instanceof Chart) window.myChart.destroy();
        window.myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Bonne Présence', 'Avertissement', 'Trop d\'absences'],
                datasets: [{ data: absenceData, backgroundColor: ['#d4edda', '#fff3cd', '#f8d7da'] }]
            }
        });
    }

    function bindTableEvents() {
        // Mettre à jour les données lors du changement des cases
        $('.presence, .participation').on('change', function() {
            const index = $(this).closest('tr').data('index');
            const session = $(this).data('session');
            const type = $(this).hasClass('presence') ? 'attendance' : 'participation';
            students[index][type][session] = $(this).is(':checked');
            saveStudents();
            updateRow($(this).closest('tr'));
        });

        // Autres événements (tri, recherche, etc.)
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#attendanceTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#sortAbsences').on('click', () => { 
            students.sort((a, b) => a.attendance.filter(p => !p).length - b.attendance.filter(p => !p).length);
            renderTable(); 
            $('#sortMode').text('Trié par absences (croissant)');
        });

        $('#sortParticipation').on('click', () => { 
            students.sort((a, b) => b.participation.filter(p => p).length - a.participation.filter(p => p).length);
            renderTable(); 
            $('#sortMode').text('Trié par participation (décroissant)');
        });

        $('#highlightExcellent').on('click', () => {
            $('#attendanceTable tbody tr').each(function() {
                if (parseInt($(this).find('.absences').text()) < 3) {
                    $(this).fadeOut(100).fadeIn(100);
                }
            });
        });

        $('#resetColors').on('click', () => updateAllRows());
    }
});