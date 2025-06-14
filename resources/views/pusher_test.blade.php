



<!DOCTYPE html>
<head>
  <title>Low Stock Medicines</title>
  <!-- <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
  Pusher.logToConsole = true;

  var pusher = new Pusher('2506583fbf75cc088002', {
    cluster: 'ap2'
  });

  var channel = pusher.subscribe('low-stock');
  channel.bind('low-stock-event', function(data) {
      let medicine = data.medicine;
      let table = document.getElementById("lowStockTable").getElementsByTagName('tbody')[0];
      
      // التحقق إذا كانت الكمية أقل من الحد الأدنى
      if (medicine.quantity <= medicine.alert_quantity) {
          // البحث عن صف الدواء الحالي
          let existingRow = document.getElementById(row-${medicine.id});
          
          if (!existingRow) {
              let row = table.insertRow();
              row.setAttribute("id", row-${medicine.id});
              row.innerHTML = `<td>${medicine.medicine_name}</td><td>${medicine.quantity}</td><td>${medicine.alert_quantity}</td>`;
          } else {
              existingRow.cells[1].innerText = medicine.quantity; // تحديث الكمية الجديدة
          }
      } else {
          // إذا زادت الكمية، احذف الدواء من الجدول
          let rowToRemove = document.getElementById(row-${medicine.id});
          if (rowToRemove) {
              rowToRemove.remove();
          }
      }
  });
</script>
</head>
<body>
  <h1>Low Stock Medicines</h1>
  <table id="lowStockTable">
    <thead>
      <tr>
        <th>اسم الدواء</th>
        <th>الكمية الحالية</th>
        <th>الحد الأدنى</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</body>