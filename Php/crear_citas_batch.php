<?php
/**
 * crear_citas_batch.php
 *
 * Genera citas de 1 hora para todos los doctores en un período dado.
 * Cada doctor tiene un descanso de almuerzo intercalado (alternando 12pm y 2pm).
 *
 * Uso:
 *   php crear_citas_batch.php                          # dry-run, 2 meses por defecto
 *   php crear_citas_batch.php --run                     # ejecuta realmente, 2 meses
 *   php crear_citas_batch.php --run --from 2026-05-01  # desde fecha específica
 *   php crear_citas_batch.php --run --from 2026-05-01 --to 2026-06-30
 *
 * 
 */

require_once __DIR__ . '/coneccion.php';

// ── Argumentos de línea de comandos ─────────────────────────────────────────────
$dryRun   = !in_array('--run', $argv, true);
$fechaIni = null;
$fechaFin = null;

for ($j = 1; $j < count($argv); $j++) {
    if ($argv[$j] === '--from' && isset($argv[$j + 1])) {
        $fechaIni = DateTime::createFromFormat('Y-m-d', $argv[++$j]);
        if (!$fechaIni) {
            echo "Fecha --from inválida. Formato esperado: YYYY-MM-DD\n";
            exit(1);
        }
    }
    if ($argv[$j] === '--to' && isset($argv[$j + 1])) {
        $fechaFin = DateTime::createFromFormat('Y-m-d', $argv[++$j]);
        if (!$fechaFin) {
            echo "Fecha --to inválida. Formato esperado: YYYY-MM-DD\n";
            exit(1);
        }
    }
}

if ($fechaIni !== null && $fechaFin !== null && $fechaIni > $fechaFin) {
    echo "La fecha --from no puede ser posterior a --to.\n";
    exit(1);
}

$fechaInicio = $fechaIni ?? new DateTime('today');
$fechaFin    = $fechaFin ?? new DateTime('+59 days');

echo "=== Generador de citas batch ===\n";
echo $dryRun
    ? "[MODO PRUEBA — ninguna cita será insertada. Usa --run para ejecutar.]\n\n"
    : "[MODO EJECUCIÓN — las citas se insertarán en la base de datos.]\n\n";
echo "Período : {$fechaInicio->format('Y-m-d')} → {$fechaFin->format('Y-m-d')}\n\n";

// ── Obtener todos los doctores ─────────────────────────────────────────────────
$resDoctores = $conn->query(
    "SELECT cont, nameUser, secondNameUser FROM usuario WHERE rolUser = 'doctor'"
);

if ($resDoctores->num_rows === 0) {
    echo "No se encontraron doctores en la base de datos.\n";
    exit;
}

$doctores = $resDoctores->fetch_all(MYSQLI_ASSOC);

// ── Configuración ───────────────────────────────────────────────────────────────
$lugarDefault = 'Consultorio principal';

// Slots de 1 hora: 6am–5pm (último slot 4pm–5pm = hora 16 a 17)
// Se generan 11 slots: 06:00, 07:00, …, 16:00
$slotsDia = [];
for ($h = 6; $h <= 16; $h++) {
    $slotsDia[] = [
        sprintf('%02d:00:00', $h),
        sprintf('%02d:00:00', $h + 1),
    ];
}

// ── Prepared statements ─────────────────────────────────────────────────────────
$stmtInsert = $conn->prepare(
    "INSERT IGNORE INTO citas
        (fechaCita, horaInicioCita, horaFinalCita, lugarCita, motivoCita, estadoCita, contDoctor, contPaciente)
     VALUES (?, ?, ?, ?, ?, 'disponible', ?, NULL)"
);

$stmtCheck = $conn->prepare(
    "SELECT contCita FROM citas
     WHERE contDoctor = ? AND fechaCita = ? AND horaInicioCita = ?
       AND estadoCita = 'disponible'"
);

// ── Contadores ────────────────────────────────────────────────────────────────
$totalInsertadas = 0;
$totalOmitidas   = 0;
$doctoresCount   = count($doctores);

foreach ($doctores as $i => $doctor) {
    $contDoctor   = (int) $doctor['cont'];
    $nombreDoctor = $doctor['nameUser'] . ' ' . $doctor['secondNameUser'];

    // Doctores con índice par descansan a las 12pm (slot 12–13 omitido)
    // Doctores con índice impar descansan a las 2pm (slot 13–14 omitido)
    $descanso12pm = ($i % 2 === 0);

    $citasDelDoctor = 0;
    $omitidasDoctor = 0;

    $fecha = clone $fechaInicio;
    while ($fecha <= $fechaFin) {
        $diaSemana = (int) $fecha->format('N'); // 1=Lunes … 7=Domingo

        // Solo Lunes–Sábado
        if ($diaSemana <= 6) {
            $fechaStr = $fecha->format('Y-m-d');

            foreach ($slotsDia as $slot) {
                [$horaInicio, $horaFinSlot] = $slot;
                $horaInt = (int) substr($horaInicio, 0, 2);

                // Omitir slot de almuerzo según tipo de doctor
                if ($descanso12pm && $horaInt === 12) continue; // 12–13pm
                if (!$descanso12pm && $horaInt === 13) continue; // 1–2pm

                // ¿Ya existe una cita disponible en este slot?
                $stmtCheck->bind_param('iss', $contDoctor, $fechaStr, $horaInicio);
                $stmtCheck->execute();
                $stmtCheck->store_result();
                $yaExiste = $stmtCheck->num_rows > 0;
                $stmtCheck->reset();

                if ($yaExiste) {
                    $omitidasDoctor++;
                    continue;
                }

                $citasDelDoctor++;

                if (!$dryRun) {
                    $motivo = 'Bloque automático — consultar disponibilidad';
                    $stmtInsert->bind_param(
                        'sssssi',
                        $fechaStr, $horaInicio, $horaFinSlot,
                        $lugarDefault, $motivo, $contDoctor
                    );
                    $stmtInsert->execute();
                }
            }
        }

        $fecha->modify('+1 day');
    }

    $totalInsertadas += $citasDelDoctor;
    $totalOmitidas   += $omitidasDoctor;

    $tag = $yaExiste > 0 ? " ({$omitidasDoctor} omitidas)" : '';
    echo sprintf(
        "  [%d/%d] Dr. %s — %d citas%s\n",
        $i + 1, $doctoresCount, $nombreDoctor, $citasDelDoctor, $tag
    );
}

echo "\n";
echo "========================================\n";
echo "  Resumen\n";
echo "========================================\n";
echo "  Doctores procesados : {$doctoresCount}\n";
echo "  Citas insertadas    : {$totalInsertadas}\n";
echo "  Citas omitidas      : {$totalOmitidas} (ya existían)\n";
echo "  Período             : {$fechaInicio->format('Y-m-d')} → {$fechaFin->format('Y-m-d')}\n";
echo "========================================\n";

$stmtInsert->close();
$stmtCheck->close();
$conn->close();

if ($dryRun) {
    echo "\nPara ejecutar realmente, agrega --run:\n";
    echo "  php crear_citas_batch.php --run\n";
    echo "\nOpciones de fecha:\n";
    echo "  --from YYYY-MM-DD   Fecha de inicio (por defecto: hoy)\n";
    echo "  --to   YYYY-MM-DD   Fecha de fin (por defecto: +59 días)\n";
}