<?php
session_start();
require_once "conexion.php";

//verificar que el usuario esta logueado, si no redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
$uid = $_SESSION['id_usuario'];

//parametros de la compra de la entrada
if (!isset($_GET['order'], $_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Parámetros inválidos');
}
$orderId   = $_GET['order'];
$eventoId  = intval($_GET['id']);

//sentencia para buscar la entrada en la base de datos
$stmt = $conexion->prepare("
    SELECT cantidad, fecha_compra 
    FROM compras 
    WHERE order_id = ? 
      AND id_evento = ? 
      AND id_usuario = ?
    LIMIT 1
");
$stmt->execute([$orderId, $eventoId, $uid]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$compra) {
    header('HTTP/1.1 404 Not Found');
    exit('Entrada no encontrada');
}
$qty         = intval($compra['cantidad']);
$fechaCompra = $compra['fecha_compra'];

//recuperacion de los datos del evento
$e = $conexion->prepare("
    SELECT nombre, imagen, precio 
    FROM eventos 
    WHERE id_evento = ?
");
$e->execute([$eventoId]);
$evento = $e->fetch(PDO::FETCH_ASSOC);
if (!$evento) {
    header('HTTP/1.1 404 Not Found');
    exit('Evento no existe');
}

//calculo del subtotal de la entrada
$subtotal = $evento['precio'] * $qty;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrada: <?= htmlspecialchars($evento['nombre']) ?></title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    #ticket { background-color: white; }
  </style>
</head>
<body class="bg-gray-100 py-6 px-4 flex justify-center">

  <div class="w-full max-w-3xl">
    <div id="ticket" class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 print:shadow-none print:rounded-none">
      <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-6 text-center">Entrada General</h1>

      <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1 space-y-4 text-gray-800">
          <h2 class="text-xl sm:text-2xl font-semibold"><?= htmlspecialchars($evento['nombre']) ?></h2>
          <p class="text-sm sm:text-base"><strong>Cantidad:</strong> <?= $qty ?></p>
          <p class="text-sm sm:text-base"><strong>Subtotal:</strong> <?= number_format($subtotal, 2) ?> €</p>
          <p class="text-sm sm:text-base"><strong>Fecha de compra:</strong> <?= htmlspecialchars($fechaCompra) ?></p>
        </div>
        <div class="flex-1 flex items-center justify-center">
          <img src="qr.php?order=<?= urlencode($orderId) ?>&id=<?= $eventoId ?>" alt="Código QR" class="border rounded-lg shadow-md max-w-full h-auto">
        </div>
      </div>
    </div>

    <div class="mt-6 flex justify-center gap-4 print:hidden">
      <a href="panel.php" class="bg-blue-600 text-white px-4 py-2 sm:px-5 sm:py-3 rounded-full hover:bg-blue-700 transition text-sm sm:text-base">Volver a Mi Panel</a>
      <button id="download-pdf" class="bg-green-600 text-white px-4 py-2 sm:px-5 sm:py-3 rounded-full hover:bg-green-700 transition text-sm sm:text-base">Descargar PDF</button>
    </div>
  </div>

  <script>
    window.onload = () => {
      const { jsPDF } = window.jspdf;
      document.getElementById('download-pdf').addEventListener('click', async () => {
        const ticketEl = document.getElementById('ticket');
        const canvas = await html2canvas(ticketEl, { scale: 2, useCORS: true });
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('portrait', 'pt', 'a4');
        const pw = pdf.internal.pageSize.getWidth(),
              ph = pdf.internal.pageSize.getHeight();
        const props  = pdf.getImageProperties(imgData);
        const wScale = pw - 40;
        const hScale = (props.height * wScale) / props.width;
        const yOff   = (ph - hScale) / 2;
        pdf.addImage(imgData, 'PNG', 20, yOff, wScale, hScale);
        pdf.save('entrada_<?= htmlspecialchars($orderId) ?>.pdf');
      });
    };
  </script>

</body>
</html>
